<?php
	session_start();
	
	ini_set('display_errors', 1); 
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
		if (empty($lang) || empty($_GET['lang'])){
			$lang='fr';
		}
	}
	
	// Vérifier si l'utilisateur est déjà connecté
	if ($_SESSION['af_id'] && $_SESSION['af_ip_client'])
		header('Location: index.php?lang=' . $lang);
	
	// Initialiser Steam OpenID
	$steam_openid = new SteamOpenID(URL_SITE . '/steam_callback.php?lang=' . $lang);
	
	// Vérifier si c'est une réponse de Steam
	if (isset($_GET['openid_ns'])) {
		
		// Valider la réponse
		if ($steam_openid->validate()) {
			
			// Récupérer le Steam ID
			$steam_id_64 = str_replace('https://steamcommunity.com/openid/id/', '', $_GET['openid_identity']);
			$steam_id = $steam_openid->getSteamID();
			
			if ($steam_id && is_numeric($steam_id_64)) {
				
				// Vérifier si l'utilisateur existe
				$result = mysqli_query($conn, "
					SELECT *
					FROM `".SQL_PREFIX."_users`
					WHERE steam_id='".$steam_id."'
					LIMIT 0,1
				");
				
				if (mysqli_num_rows($result) == 1) {
					// Utilisateur existe, le connecter
					$resultat = mysqli_fetch_array($result);

					$_SESSION['af_id'] = $resultat['id'];
					$_SESSION['af_steam_id'] = $resultat['steam_id'];
					$_SESSION['af_pseudo'] = $resultat['username'];
					$_SESSION['af_token'] = GetInfo($resultat['id'], 'token');
					$_SESSION['af_date_register'] = $resultat['date_register'];
					$_SESSION['af_ip_register'] = $resultat['ip_register'];
					$_SESSION['af_lastseen'] = $resultat['lastseen'];
					$_SESSION['af_lastseen_ip'] = $resultat['lastseen_ip'];
					$_SESSION['af_ip_client'] = $_SERVER['REMOTE_ADDR'];
					$_SESSION['af_admin_level'] = $resultat['admin_level'];
					
					// Mettre à jour les données de connexion
					$requete_sql = "UPDATE `".SQL_PREFIX."_users` SET `lastseen` = '".time()."', `lastseen_ip` = '".$_SERVER['REMOTE_ADDR']."' WHERE `id` =".$resultat['id'].";";
					mysqli_query($conn, $requete_sql);
					
					// Vérifier si c'est le compte root
					if (ROOT_SITE == $resultat['steam_id'])
						$_SESSION['af_admin_level'] = 10;
					
					// Redirection
					header('Location: index.php?p=compte&lang=' . $lang);
					exit;
					
				} else {
					// Nouvel utilisateur, créer le compte
					
					// Récupérer les infos de Steam
					$steam_info = $steam_openid->getSteamInfo($steam_id_64);
					
					// Générer un pseudo depuis le nom Steam
					$pseudo = isset($steam_info['personaname']) ? $steam_info['personaname'] : 'SteamUser';
					$pseudo = preg_replace("/[^A-Za-z0-9 ]/", "", $pseudo);
					$pseudo = preg_replace("/  /", " ", $pseudo);
					$pseudo = preg_replace("/  /", " ", $pseudo);
					
					// S'assurer que le pseudo n'est pas vide et n'existe pas
					if (empty($pseudo)) {
						$pseudo = 'SteamUser_' . substr($steam_id_64, -5);
					}
					
					// Vérifier que le pseudo n'existe pas
					$counter = 1;
					$original_pseudo = $pseudo;
					while (true) {
						$check = mysqli_query($conn, "SELECT id FROM `".SQL_PREFIX."_users` WHERE `username`='".$pseudo."' LIMIT 1");
						if (mysqli_num_rows($check) == 0) {
							break;
						}
						$pseudo = $original_pseudo . "_" . $counter;
						$counter++;
					}
					
					// Récupérer l'email depuis Steam (si disponible)
					$adresse_email = isset($steam_info['profileurl']) ? 'steam_' . $steam_id_64 . '@steam.local' : 'steam_' . $steam_id_64 . '@steam.local';
					
					// Générer un mot de passe aléatoire
					$mot_de_passe = bin2hex(random_bytes(16));
					
					// Insérer le nouvel utilisateur
					$steam_id_normalized = str_replace("STEAM_0", "STEAM_1", $steam_id);
					$requete_sql = "
						INSERT INTO `".SQL_PREFIX."_users` 
						(`id`, `username`, `mail`, `password`, `date_register`, `ip_register`, `lastseen`, `lastseen_ip`, `token`, `mini_token`, `is_suspended`, `admin_level`, `steam_id`, `suspend_reason`, `suspend_admin`, `suspend_time`, `recovery_date`, `recovery_code`)
						VALUES (NULL , '".mysqli_real_escape_string($conn, $pseudo)."', '".mysqli_real_escape_string($conn, $adresse_email)."', '".md5($mot_de_passe)."', '".time()."', '".$_SERVER["REMOTE_ADDR"]."', '".time()."', '".$_SERVER["REMOTE_ADDR"]."', '0', '0', '0', '0', '".mysqli_real_escape_string($conn, $steam_id_normalized)."', '', '', '0', '0', '');
					";
					
					if (mysqli_query($conn, $requete_sql)) {
						
						// Récupérer l'ID de l'utilisateur nouvellement créé
						$result = mysqli_query($conn, "SELECT id FROM `".SQL_PREFIX."_users` WHERE `steam_id`='".$steam_id_normalized."' LIMIT 1");
						$new_user = mysqli_fetch_array($result);
						$user_id = $new_user['id'];
						
						// Insérer dans les logs
						mysqli_query($conn, "
							INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`ip`)
							VALUES (NULL , '".time()."', 'Inscription', '".$user_id."', '".$_SERVER['REMOTE_ADDR']."');
						");
						
						// Envoyer un mail de bienvenue
						if (MAIL_INSCRIPTION) {
							$mail_content = MAIL_INSCRIPTION_CONTENT;
							$mail_content = str_replace("{PSEUDO}", $pseudo, $mail_content);
							$mail_content = str_replace("{SITE_URL}", URL_SITE, $mail_content);
							$mail_content = str_replace("{IDENTIFIANT}", $pseudo, $mail_content);
							$mail_content = str_replace("{MOT_DE_PASSE}", "Connecté via Steam", $mail_content);
							$mail_content = str_replace("{STEAM_ID}", $steam_id_normalized, $mail_content);
							$mail_content = str_replace("{FORUM_URL}", URL_FORUM, $mail_content);
							$mail_content = str_replace("{SOURCEBANS_URL}", URL_SOURCEBANS, $mail_content);
							$mail_content = str_replace("{GROUPE_STEAM}", GROUPE_STEAM, $mail_content);
							$mail_content = str_replace("{NOM_TEAM}", NOM_TEAM, $mail_content);
							$mail_content = str_replace("{CONTACT_RESPONSABLE}", MAIL_CONTACT, $mail_content);
							
							$headers = "MIME-Version: 1.0\r\n";
							$headers .= "Content-type: text/html; charset=UTF-8\r\n";
							$headers .= "From: " . MAIL_AUTEUR . " <" . MAIL_REPLY . ">\r\n";
							
							mail($adresse_email, MAIL_INSCRIPTION_TITLE, $mail_content, $headers);
						}
						
						// Connecter l'utilisateur
						$_SESSION['af_id'] = $user_id;
						$_SESSION['af_steam_id'] = $steam_id_normalized;
						$_SESSION['af_pseudo'] = $pseudo;
						$_SESSION['af_token'] = 0;
						$_SESSION['af_date_register'] = time();
						$_SESSION['af_ip_register'] = $_SERVER["REMOTE_ADDR"];
						$_SESSION['af_lastseen'] = time();
						$_SESSION['af_lastseen_ip'] = $_SERVER["REMOTE_ADDR"];
						$_SESSION['af_ip_client'] = $_SERVER['REMOTE_ADDR'];
						$_SESSION['af_admin_level'] = 0;
						
						// Vérifier si c'est le compte root
						if (ROOT_SITE == $steam_id_normalized)
							$_SESSION['af_admin_level'] = 10;
						
						// Redirection vers la page d'accueil
						header('Location: index.php?p=compte&lang=' . $lang);
						exit;
						
					} else {
						// Erreur lors de l'insertion
						if($lang == 'fr') {
							$error_msg = "Une erreur s'est produite lors de la création de votre compte.";
						} else {
							$error_msg = "An error occurred while creating your account.";
						}
					}
				}
			}
		}
		
		// Erreur de validation
		if($lang == 'fr') {
			$error_msg = "Erreur lors de la validation avec Steam.";
		} else {
			$error_msg = "Error validating with Steam.";
		}
	}
	
	// Redirection vers login avec erreur
	if (isset($error_msg)) {
		header('Location: login.php?error=1&lang=' . $lang);
		exit;
	}
	
	// Redirection par défaut vers login
	header('Location: login.php?lang=' . $lang);
	exit;
	
	mysqli_close($conn);
?>
