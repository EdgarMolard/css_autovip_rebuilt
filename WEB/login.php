<?php
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ERROR);
	
	define("CHECK_IN", "1");
	
	include_once("configuration.php");
	include_once("fonctions.php");
	include_once("steam_openid.php");

	if ($_SESSION['af_id'] && $_SESSION['af_ip_client'])
		echo '<meta http-equiv="refresh" content="0; URL=index.php">';
	
	if (!empty($_GET['lang'])){
		$lang=$_GET['lang'];
	}
	else {
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
		if (empty($_GET['lang'])){
			$lang='fr';
		}
	}
	
	// Initialiser Steam OpenID
	$steam_openid = new SteamOpenID(URL_SITE . '/steam_callback.php?lang=' . $lang);
	$steam_login_url = $steam_openid->getLoginURL();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title> <?php echo TITLE; ?> </title>
<link rel="Shortcut Icon" href="img/favicon.ico"/>
<link href="css/reset.css" media="screen" rel="stylesheet" type="text/css" />
<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="css/ie.css" media="screen" rel="stylesheet" type="text/css">
<![endif]-->
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery-custom.js"></script>

</head>
<body>
<div class="shadow-login"></div><!-- end div .shadow-login -->
<!-- BEGIN LOGIN -->
<div id="login">
	<a href="?lang=fr"><img src="./img/icon/flag_fr-FR.png" /></a>
	<a href="?lang=en"><img src="./img/icon/flag_en-GB.png" /></a>
    <p class="logo"><a>Identification</a></p>
    <div class="box-out">
    	<div class="box-in">
		<?php
			// Afficher les messages d'erreur
			if (isset($_GET['error']))
			{
				$error_msg = isset($_SESSION['steam_error']) ? $_SESSION['steam_error'] : 'An error occurred during Steam login';
				
				if($lang == 'fr') {
					echo '
						<div class="notification error">
							<div class="messages">' . htmlspecialchars($error_msg) . ' <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
				} else {
					echo '
						<div class="notification error">
							<div class="messages">' . htmlspecialchars($error_msg) . ' <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
				}
				
				// Effacer le message d'erreur après l'affichage
				unset($_SESSION['steam_error']);
			}
			
			// Afficher le bouton de connexion Steam
			if($lang == 'fr') {
				echo '
					<div class="notification info">
						<div class="messages">Connectez-vous avec votre compte Steam pour accéder à votre compte VIP <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
				echo '
					<div style="text-align: center; padding: 20px;">
						<a href="' . htmlspecialchars($steam_login_url) . '">
							<img src="https://community.cloudflare.steamstatic.com/public/images/steamworks_docs/en/signin_buttons/sits_01.png" alt="Se connecter via Steam" />
						</a>
					</div>
				';
				echo "<center><p><a href='index.php?lang=$lang'>Retour au site</a></p></center>";
			} else {
				echo '
					<div class="notification info">
						<div class="messages">Log in with your Steam account to access your VIP account <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
				echo '
					<div style="text-align: center; padding: 20px;">
						<a href="' . htmlspecialchars($steam_login_url) . '">
							<img src="https://community.cloudflare.steamstatic.com/public/images/steamworks_docs/en/signin_buttons/sits_01.png" alt="Sign in via Steam" />
						</a>
					</div>
				';
				echo "<center><p><a href='index.php?lang=$lang'>Back to website</a></p></center>";
			}
		?>
		</div><!-- end div .box-in -->
    </div><!-- end div .box-out -->
</div><!-- end div #login -->
<!-- END LOGIN -->
</body>
</html>

<?php mysqli_close($conn); ?>
