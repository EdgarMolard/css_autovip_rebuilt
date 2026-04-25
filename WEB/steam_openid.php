<?php
/**
 * Steam OpenID Authentication Class
 * Handles Steam authentication via OpenID 2.0
 */

class SteamOpenID {
    const STEAM_API_URL = 'https://steamcommunity.com/openid/login';
    const STEAM_VERIFY_URL = 'https://steamcommunity.com/openid/login';
    
    private $return_url;
    
    public function __construct($return_url) {
        $this->return_url = $return_url;
    }
    
    /**
     * Get the login URL for Steam
     */
    public function getLoginURL() {
        $params = array(
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $this->return_url,
            'openid.realm' => $this->getRealm(),
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.ax.mode' => 'fetch_request',
            'openid.ax.type.email' => 'http://axschema.org/contact/email',
            'openid.ax.required' => 'email'
        );
        
        return self::STEAM_API_URL . '?' . http_build_query($params);
    }
    
    /**
     * Get the realm (domain)
     */
    private function getRealm() {
        return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    }
    
    /**
     * Validate the OpenID response from Steam
     */
    public function validate() {
        if (!isset($_GET['openid_ns']) || !isset($_GET['openid_mode'])) {
            return false;
        }
        
        if ($_GET['openid_mode'] == 'error') {
            return false;
        }
        
        if ($_GET['openid_mode'] != 'id_res') {
            return false;
        }
        
        $params = array(
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'check_auth'
        );
        
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'openid_') === 0) {
                $params[str_replace('openid_', 'openid.', $key)] = $value;
            }
        }
        
        $response = $this->makeRequest(self::STEAM_VERIFY_URL, $params);
        
        if (strpos($response, 'is_valid:true') !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get Steam ID from OpenID response
     */
    public function getSteamID() {
        if (!isset($_GET['openid_identity'])) {
            return false;
        }
        
        $matches = array();
        if (preg_match('/\/id\/([0-9]+)\/?$/', $_GET['openid_identity'], $matches)) {
            $steam64 = $matches[1];
            return $this->convertSteamID64to32($steam64);
        }
        
        return false;
    }
    
    /**
     * Convert 64-bit Steam ID to 32-bit format (STEAM_X:Y:Z)
     */
    private function convertSteamID64to32($steam64) {
        $z = bcmod($steam64, 2);
        $y = bcdiv(bcsub($steam64, 76561197960265728 + $z, 0), 2, 0);
        $x = 0;
        
        if (bcmod(bcadd($y, $z, 0), 2) == 0) {
            $x = 0;
        } else {
            $x = 1;
        }
        
        $y = bcdiv($y, 2, 0);
        
        return "STEAM_" . $x . ":" . bcmod($y, 2) . ":" . bcdiv($y, 2, 0);
    }
    
    /**
     * Get Steam profile information
     */
    public function getSteamInfo($steam64) {
        $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=" . STEAM_API_KEY . "&steamids=" . $steam64;
        
        if (STEAM_API_KEY == '' || STEAM_API_KEY == '0') {
            return false;
        }
        
        $response = @file_get_contents($url);
        
        if ($response === false) {
            return false;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['response']['players'][0])) {
            return false;
        }
        
        return $data['response']['players'][0];
    }
    
    /**
     * Make HTTP request
     */
    private function makeRequest($url, $params = array()) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params),
                'timeout' => 5
            )
        ));
        
        return @file_get_contents($url, false, $context);
    }
}
?>
