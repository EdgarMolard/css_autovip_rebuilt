<?php
/**
 * Wrapper SteamOpenID - Utilise la librairie xpaw/steam-openid
 * Interface compatible avec l'implémentation existante
 */

require_once __DIR__ . '/vendor/autoload.php';

class SteamOpenID {
    
    private $steam;
    private $returnUrl;
    private $lastError = '';
    private $steamAPIProfileEndpoint = 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002';
    
    public function __construct($returnUrl) {
        $this->returnUrl = $returnUrl;
        // Use a small subclass to make OpenID verification request handling more robust on shared hosts.
        $this->steam = new class($returnUrl) extends \xPaw\Steam\SteamOpenID {
            public function SendSteamRequest(array $arguments): array {
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    if ($ch !== false) {
                        curl_setopt_array($ch, [
                            CURLOPT_USERAGENT => 'OpenID Verification (+https://github.com/xPaw/SteamOpenID.php)',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_URL => self::SERVER,
                            CURLOPT_CONNECTTIMEOUT => 8,
                            CURLOPT_TIMEOUT => 8,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $arguments,
                            CURLOPT_SSL_VERIFYPEER => true,
                            CURLOPT_SSL_VERIFYHOST => 2,
                        ]);

                        $response = (string)curl_exec($ch);
                        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $errno = curl_errno($ch);
                        curl_close($ch);

                        if ($code === 200 && $response !== '') {
                            return [$code, $response];
                        }

                        // Fallback for hosts with broken CA bundle/SSL chain.
                        if ($errno !== 0 || $code === 0) {
                            $ch = curl_init();
                            if ($ch !== false) {
                                curl_setopt_array($ch, [
                                    CURLOPT_USERAGENT => 'OpenID Verification (+https://github.com/xPaw/SteamOpenID.php)',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_URL => self::SERVER,
                                    CURLOPT_CONNECTTIMEOUT => 8,
                                    CURLOPT_TIMEOUT => 8,
                                    CURLOPT_POST => true,
                                    CURLOPT_POSTFIELDS => $arguments,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_SSL_VERIFYHOST => 0,
                                ]);

                                $response = (string)curl_exec($ch);
                                $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                curl_close($ch);
                                return [$code, $response];
                            }
                        }

                        return [$code, $response];
                    }
                }

                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'content' => http_build_query($arguments),
                        'timeout' => 8,
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                $response = @file_get_contents(self::SERVER, false, $context);
                if ($response === false) {
                    return [0, ''];
                }

                return [200, $response];
            }
        };
    }
    
    /**
     * Génère l'URL de redirection vers Steam
     */
    public function getLoginURL() {
        // Correction: Utiliser GetAuthUrl() (PascalCase) sans paramètres
        return $this->steam->GetAuthUrl();
    }
    
    /**
     * Valide la réponse de Steam
     * Retourne le Steam64 ID ou false en cas d'erreur
     */
    public function validate() {
        try {
            $this->lastError = '';
            return $this->steam->Validate();
        } catch (Throwable $e) {
            $libraryError = $e->getMessage();

            // Fallback: manually verify OpenID with Steam endpoint using current callback parameters.
            $steam64 = $this->manualValidateOpenId();
            if ($steam64 !== false) {
                $this->lastError = '';
                return $steam64;
            }

            $this->lastError = $libraryError . (!empty($this->lastError) ? ' | fallback: ' . $this->lastError : '');
            error_log('Steam validation error: ' . $this->lastError);
            return false;
        }
    }

    /**
     * Retourne le dernier message d'erreur de validation OpenID.
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Manual OpenID 2.0 check_authentication verification against Steam.
     * Returns Steam64 ID on success, false on failure.
     */
    private function manualValidateOpenId() {
        try {
            $required = [
                'openid_mode',
                'openid_ns',
                'openid_op_endpoint',
                'openid_claimed_id',
                'openid_identity',
                'openid_return_to',
                'openid_response_nonce',
                'openid_assoc_handle',
                'openid_signed',
                'openid_sig',
            ];

            foreach ($required as $key) {
                if (empty($_GET[$key]) || !is_string($_GET[$key])) {
                    $this->lastError = 'Missing OpenID parameter: ' . $key;
                    return false;
                }
            }

            if ($_GET['openid_mode'] !== 'id_res') {
                $this->lastError = 'Wrong openid_mode.';
                return false;
            }

            if ($_GET['openid_claimed_id'] !== $_GET['openid_identity']) {
                $this->lastError = 'Wrong openid_claimed_id, should equal to openid_identity.';
                return false;
            }

            if ($_GET['openid_op_endpoint'] !== 'https://steamcommunity.com/openid/login') {
                $this->lastError = 'Wrong openid_op_endpoint.';
                return false;
            }

            // Keep same security logic as library for return_to prefix.
            if (!str_starts_with($_GET['openid_return_to'], $this->returnUrl)) {
                $this->lastError = 'Wrong openid_return_to.';
                return false;
            }

            if (preg_match('/^https:\/\/steamcommunity\.com\/openid\/id\/(?<id>76561[0-9]{12})\/?$/', $_GET['openid_identity'], $matches) !== 1) {
                $this->lastError = 'Wrong openid_identity.';
                return false;
            }

            $rawBody = $this->buildRawOpenIdVerificationBody();
            [$code, $response] = $this->sendOpenIdVerification($rawBody);
            if ($code !== 200 || $response === '') {
                $this->lastError = 'Fallback verification HTTP error: ' . $code;
                return false;
            }

            if (strpos($response, "is_valid:true") === false) {
                $this->lastError = 'Steam returned is_valid:false | response=' . trim(preg_replace('/\s+/', ' ', $response));
                return false;
            }

            return $matches['id'];
        } catch (Throwable $e) {
            $this->lastError = 'Fallback exception: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Build check_authentication payload from raw query string to preserve exact callback values.
     */
    private function buildRawOpenIdVerificationBody() {
        $queryString = (string)($_SERVER['QUERY_STRING'] ?? '');
        $openidPairs = [];

        if ($queryString !== '') {
            $pairs = explode('&', $queryString);

            foreach ($pairs as $pair) {
                if ($pair === '' || strpos($pair, '=') === false) {
                    continue;
                }

                [$rawKey, $rawValue] = explode('=', $pair, 2);
                $decodedKey = urldecode($rawKey);

                if (!str_starts_with($decodedKey, 'openid.')) {
                    continue;
                }

                if ($decodedKey === 'openid.mode') {
                    $openidPairs[] = rawurlencode('openid.mode') . '=' . rawurlencode('check_authentication');
                    continue;
                }

                // Keep original key/value encoding as much as possible.
                $openidPairs[] = $rawKey . '=' . $rawValue;
            }
        }

        // Some server stacks normalize the callback query into $_GET only.
        // If no dotted key survived in QUERY_STRING, rebuild payload from $_GET.
        if (empty($openidPairs)) {
            foreach ($_GET as $key => $value) {
                if (!is_string($value) || $value === '' || !str_starts_with($key, 'openid_')) {
                    continue;
                }

                $dotted = 'openid.' . substr($key, 7);
                if ($dotted === 'openid.mode') {
                    $value = 'check_authentication';
                }

                $openidPairs[] = rawurlencode($dotted) . '=' . rawurlencode($value);
            }
        }

        if (empty($openidPairs)) {
            return '';
        }

        $hasMode = false;
        foreach ($openidPairs as $pair) {
            if (str_starts_with($pair, 'openid.mode=')) {
                $hasMode = true;
                break;
            }
        }

        if (!$hasMode) {
            $openidPairs[] = rawurlencode('openid.mode') . '=' . rawurlencode('check_authentication');
        }

        return implode('&', $openidPairs);
    }

    /**
     * Sends OpenID verification request to Steam.
     * Returns [httpCode, responseBody].
     */
    private function sendOpenIdVerification($rawBody) {
        $url = 'https://steamcommunity.com/openid/login';

        if (!is_string($rawBody) || $rawBody === '') {
            return [0, ''];
        }

        if (function_exists('curl_init')) {
            $ch = curl_init();
            if ($ch !== false) {
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $rawBody,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/x-www-form-urlencoded',
                    ],
                    CURLOPT_CONNECTTIMEOUT => 8,
                    CURLOPT_TIMEOUT => 8,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_USERAGENT => 'AutoVip Steam OpenID validation',
                ]);

                $response = (string)curl_exec($ch);
                $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $errno = curl_errno($ch);
                curl_close($ch);

                if ($code === 200 && $response !== '') {
                    return [$code, $response];
                }

                // Retry with relaxed SSL only when transport failed.
                if ($errno !== 0 || $code === 0) {
                    $ch = curl_init();
                    if ($ch !== false) {
                        curl_setopt_array($ch, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $rawBody,
                            CURLOPT_HTTPHEADER => [
                                'Content-Type: application/x-www-form-urlencoded',
                            ],
                            CURLOPT_CONNECTTIMEOUT => 8,
                            CURLOPT_TIMEOUT => 8,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_USERAGENT => 'AutoVip Steam OpenID validation',
                        ]);

                        $response = (string)curl_exec($ch);
                        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        return [$code, $response];
                    }
                }

                return [$code, $response];
            }
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $rawBody,
                'timeout' => 8,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return [0, ''];
        }

        return [200, $response];
    }
    
    /**
     * Extrait l'ID Steam (utilisé après validate())
     * Note: Validate() retourne déjà le Steam64, cette méthode convertit en Steam32
     */
    public function getSteamID() {
        // Cette méthode n'est généralement pas appelée directement
        // car validate() retourne déjà le Steam64
        return null;
    }
    
    /**
     * Récupère les infos utilisateur Steam via API
     */
    public function getSteamInfo($steamID32) {
        if (empty(STEAM_API_KEY)) {
            return null;
        }
        
        $steam64 = $this->convertSteamID32to64($steamID32);
        if (!$steam64) {
            return null;
        }
        
        $params = [
            'key' => STEAM_API_KEY,
            'steamids' => $steam64
        ];
        
        $response = $this->makeRequest($this->steamAPIProfileEndpoint, $params);
        $data = json_decode($response, true);
        
        if (!empty($data['response']['players'][0])) {
            return $data['response']['players'][0];
        }
        
        return null;
    }
    
    /**
     * Convertit Steam64 en Steam32 (format STEAM_1:Y:Z)
     */
    private function convertSteamID64to32($steam64) {
        $steam64 = (string)$steam64;
        
        if (function_exists('bcmod')) {
            $z = (int)bcmod($steam64, 2);
            $y = (int)bcdiv(bcsub($steam64, $z, 0), 2, 0);
        } else {
            $z = $steam64 % 2;
            $y = intdiv($steam64 - $z, 2);
        }
        
        return "STEAM_1:$z:$y";
    }
    
    /**
     * Convertit Steam32 (STEAM_1:Y:Z) en Steam64
     */
    private function convertSteamID32to64($steamID32) {
        if (preg_match('/STEAM_1:(\d):(\d+)/', $steamID32, $matches)) {
            $y = (int)$matches[1];
            $z = (int)$matches[2];
            $steam64 = $z * 2 + $y + 76561197960265728;
            return (string)$steam64;
        }
        return null;
    }
    
    /**
     * Effectue une requête POST
     */
    private function makeRequest($url, $params) {
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($params),
                'timeout' => 10
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];
        
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        
        return $response !== false ? $response : '';
    }
}
