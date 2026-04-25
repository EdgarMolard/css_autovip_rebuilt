<?php
/**
 * Callback Steam OpenID - Utilise xpaw/steam-openid
 * Reçoit la réponse de Steam, valide l'authentification
 */

session_start();

define("CHECK_IN", "1");

try {
    include_once("configuration.php");
    include_once("fonctions.php");
    include_once("steam_openid.php");
} catch (Exception $e) {
    http_response_code(500);
    die("Configuration error: " . $e->getMessage());
}

// Logs
function log_steam_error($message, $context = []) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $log_message .= " | Context: " . json_encode($context);
    }
    $log_message .= "\n";
    
    $log_files = [
        dirname(__FILE__) . '/logs/steam_errors.log',
        dirname(__DIR__) . '/logs/steam_errors.log'
    ];
    
    foreach ($log_files as $log_file) {
        if (is_writable(dirname($log_file))) {
            @file_put_contents($log_file, $log_message, FILE_APPEND);
            break;
        }
    }
}

/**
 * Convertit Steam64 en Steam32 (format STEAM_1:Y:Z)
 */
function convertSteamID64to32($steam64) {
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

try {
    // Vérifier que nous avons une réponse OpenID
    if (!isset($_GET['openid_ns'])) {
        throw new Exception('No OpenID response from Steam');
    }
    
    // Créer instance SteamOpenID et valider
    $steam_openid = new SteamOpenID(URL_SITE . '/steam_callback.php?lang=' . ($_GET['lang'] ?? 'fr'));
    
    // Valider la réponse - retourne le Steam64 ID ou false
    $steam64 = $steam_openid->validate();
    if (!$steam64) {
        $reason = $steam_openid->getLastError();
        throw new Exception('Steam validation failed' . (!empty($reason) ? ': ' . $reason : ''));
    }
    
    // Convertir Steam64 en Steam32 pour la base de données
    $steam_id = convertSteamID64to32($steam64);
    if (!$steam_id) {
        throw new Exception('Could not convert Steam ID');
    }
    
    // Variables par défaut
    $username = 'Steam_' . substr($steam_id, -8);
    $email = 'steam_' . time() . '@steam.local';

    // Connexion DB: le projet utilise global $conn (pas $con)
    $db = $GLOBALS['conn'] ?? null;
    if (!($db instanceof mysqli)) {
        throw new Exception('Database connection not initialized (expected $conn).');
    }
    
    // Récupérer info utilisateur depuis API Steam (optionnel)
    $steam_info = $steam_openid->getSteamInfo($steam_id);
    if ($steam_info) {
        if (isset($steam_info['personaname'])) {
            $username = $steam_info['personaname'];
        }
        if (isset($steam_info['profileurl'])) {
            $email = $steam_info['profileurl'];
        }
    }
    
    // Vérifier ou créer l'utilisateur en base de données
    $query = "SELECT * FROM " . SQL_PREFIX . "_users WHERE steam_id = '" . mysqli_real_escape_string($db, $steam_id) . "'";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Utilisateur existant - mettre à jour
        $user = mysqli_fetch_assoc($result);
        $update = "UPDATE " . SQL_PREFIX . "_users SET 
                   lastseen = NOW(),
                   lastseen_ip = '" . mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR']) . "'
                   WHERE steam_id = '" . mysqli_real_escape_string($db, $steam_id) . "'";
        mysqli_query($db, $update);
        
        $user_id = $user['id'];
        $username = $user['username'] ?? $username;
    } else {
        // Nouvel utilisateur - créer
        $insert = "INSERT INTO " . SQL_PREFIX . "_users 
                  (steam_id, username, mail, date_register, ip_register, lastseen, lastseen_ip)
                  VALUES 
                  ('" . mysqli_real_escape_string($db, $steam_id) . "', 
                   '" . mysqli_real_escape_string($db, $username) . "', 
                   '" . mysqli_real_escape_string($db, $email) . "',
                   NOW(), 
                   '" . mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR']) . "', 
                   NOW(), 
                   '" . mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR']) . "')";
        
        if (!mysqli_query($db, $insert)) {
            throw new Exception('Database insert failed: ' . mysqli_error($db));
        }
        
        $user_id = mysqli_insert_id($db);
    }
    
    // Définir la session
    $_SESSION['af_id'] = $user_id;
    $_SESSION['af_steam_id'] = $steam_id;
    $_SESSION['af_pseudo'] = $username;
    $_SESSION['af_token'] = bin2hex(random_bytes(16));
    $_SESSION['af_date_register'] = date('Y-m-d H:i:s');
    $_SESSION['af_ip_register'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['af_lastseen'] = date('Y-m-d H:i:s');
    $_SESSION['af_lastseen_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['af_ip_client'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['af_admin_level'] = 0;
    
    // Log succès
    log_steam_error('✓ Authentification réussie', [
        'steam_id' => $steam_id,
        'user_id' => $user_id,
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    
    // Redirection
    header('Location: index.php?p=compte');
    exit;
    
} catch (Exception $e) {
    // Log erreur
    log_steam_error('✗ Erreur authentification: ' . $e->getMessage(), [
        'ip' => $_SERVER['REMOTE_ADDR'],
        'get_data' => $_GET
    ]);
    
    // Redirection vers erreur
    header('Location: login.php?error=1');
    exit;
}
