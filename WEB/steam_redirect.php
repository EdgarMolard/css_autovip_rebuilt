<?php
	session_start();
	
	ini_set('display_errors', 0); 
	error_reporting(E_ERROR);
	
	define("CHECK_IN", "1");
	
	include_once("configuration.php");
	include_once("fonctions.php");
	include_once("steam_openid.php");

	if (!empty($_GET['lang'])){
		$lang=$_GET['lang'];
	}
	else {
		$lang = substr($HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'],0,2);
		if (empty($_GET['lang'])){
			$lang='fr';
		}
	}
	
	// Initialiser Steam OpenID et rediriger directement vers Steam
	$steam_openid = new SteamOpenID(URL_SITE . '/steam_callback.php?lang=' . $lang);
	$steam_login_url = $steam_openid->getLoginURL();
	
	// Redirection directe vers Steam
	header('Location: ' . $steam_login_url);
	exit;
?>
