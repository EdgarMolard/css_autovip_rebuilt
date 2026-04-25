<?php
	session_start();
	
	define("CHECK_IN", "1");
	include_once("configuration.php");
	
	if (!empty($_GET['lang'])){
		$lang=$_GET['lang'];
	}
	else {
		$lang = 'fr';
	}
	
	// Détruire la session
	session_destroy();
	
	// Redirection vers la page d'accueil
	header('Location: index.php?lang=' . $lang);
	exit;
?>
