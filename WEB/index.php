<?php
	session_start();
	
	ini_set('display_errors', 0); 
	if (strlen($_SESSION['af_ip_client'] ?? '') > 6)
		if (($_SESSION['af_ip_client'] ?? '') != $_SERVER['REMOTE_ADDR'])
			session_destroy();
	
	error_reporting(E_ERROR);
	
	define("CHECK_IN", "1");

	include_once("configuration.php");
	
	include_once("fonctions.php");
	
	$url = trim($url, '/');
	echo substr($url, strrpos($url, '/')+1);
	
	if (!empty($_GET['lang'])){
		$lang=$_GET['lang'];
	}
	else {
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
		if (empty($_GET['lang'])){
			$lang='fr';
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<title> <?php echo TITLE; ?> </title>
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE"/>
	<link rel="Shortcut Icon" href="img/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="./css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="./css/style.css"/>
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="js/jquery-tipsy.js"></script>
	<script type="text/javascript" src="js/jquery-custom.js"></script>
	<script type="text/javascript" src="js/blazy.js"></script>
</head>

<body>
<div class="shadow"></div>
<div id="header">
	<p class="logo"><a><?php echo LOGO_TITLE; ?></a></p>
	<a href="?lang=fr"><img src="./img/icon/flag_fr-FR.png" /></a>
	<a href="?lang=en"><img src="./img/icon/flag_en-GB.png" /></a>

	<?php
	
	if (isset($_SESSION['af_pseudo']))
	{
		if (ROOT_SITE != $_SESSION['af_steam_id'])	$_SESSION['af_admin_level'] = GetInfo($_SESSION['af_id'], 'admin_level');
		
		if (RENTABILIWEB_PAIEMENT)		$mini_token = " - <b>".GetInfo($_SESSION['af_id'], 'mini_token')."</b> MiniTokens";
		
		$client_token = GetInfo($_SESSION['af_id'], 'token');

		if ($client_token > 1) $text_token = "Tokens";
		else if ($client_token <= 1) $text_token = "Token";
		
		if(strcmp($lang, "fr") == 0) { 
			echo '<p class="user"><span>Bienvenue, '.htmlentities($_SESSION['af_pseudo'], ENT_QUOTES, "UTF-8").' [ <b>'.$client_token.' '.$text_token.'</b>';
		}
		else {
			echo '<p class="user"><span>Welcome, '.htmlentities($_SESSION['af_pseudo'], ENT_QUOTES, "UTF-8").' [ <b>'.$client_token.' '.$text_token.'</b>';
		}
		if (RENTABILIWEB_PAIEMENT) echo'<b>'.$mini_token.' </b>';
		if(strcmp($lang, "fr") == 0) { 
			echo "]</span> <a href='index.php?p=compte&lang=$lang' class='tooltip' title='Voir votre compte'>Votre compte</a> - <a href='index.php?p=logout&lang=$lang' class='tooltip' title='Vous d&eacute;connecter'>Sortir</a></p>";
		}
		else {
			echo "]</span> <a href='index.php?p=compte&lang=$lang' class='tooltip' title='See ur account'>See ur account</a> - <a href='index.php?p=logout&lang=$lang' class='tooltip' title='Log out'>Log Out</a></p>";
		}
	}
	else
		if(strcmp($lang, "fr") == 0) { 
			echo "<p class='user'><span>Bienvenue, visiteur</span> <a href='steam_redirect.php?lang=$lang' class='tooltip' title='Identifiez vous'>Se connecter</a></p>";
		}
		else {
			echo "<p class='user'><span>Welcome, guest</span> <a href='steam_redirect.php?lang=$lang' class='tooltip' title='Log In'>Log In</a></p>";
		}
	?>
</div>

<?php 
	include_once('./pages/menu_gauche.php');
?>

<div id="content">
	<?php include_once('./pages/selection_page.php'); ?>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>