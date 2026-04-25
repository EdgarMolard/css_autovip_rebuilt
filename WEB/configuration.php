<?php
	// Load environment variables from .env file
	require_once(__DIR__ . '/EnvLoader.php');
	EnvLoader::load();
	
	// Ne pas toucher à ça, la configuration se trouve plus bas :-)
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	
	if (!empty($_GET['lang'])){
		$lang=$_GET['lang'];
	}
	else {
		$lang = substr($HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'],0,2);
		if (empty($_GET['lang'])){
			$lang='fr';
		}
	}
	
	/* ######################################
	   #		  [Configuration]			#
	   ###################################### */
	 
	/*
		Apparence du site
	*/
	
	define("TITLE", EnvLoader::get("TITLE", "AutoVip")); // Titre de la page
	define("LOGO_TITLE", EnvLoader::get("LOGO_TITLE", "AutoVip")); // Logo de la page
	
	define("URL_FORUM", EnvLoader::get("URL_FORUM", "0")); // Lien de votre forum, laissez 0 si aucun
	
	define("URL_SITE", EnvLoader::get("URL_SITE", "http://localhost")); // Lien du site
	define("NOM_SITE", EnvLoader::get("NOM_SITE", "AutoVip")); // Nom du site
	define("NOM_TEAM", EnvLoader::get("NOM_TEAM", "AutoVip")); // Nom de votre team
	
	define("URL_SOURCEBANS", EnvLoader::get("URL_SOURCEBANS", "0")); // Lien de votre sourcebans, laissez 0 si aucun
	define("URL_TS3", EnvLoader::get("URL_TS3", "0")); // Lien de votre Ts, laissez 0 si aucun
	define("URL_DISCORD", EnvLoader::get("URL_DISCORD", "0")); // Lien de votre Discord, laissez 0 si aucun
	define("MAIL_CONTACT", EnvLoader::get("MAIL_CONTACT", "0")); // Lien de contact, laissez 0 si vide
	define("GROUPE_STEAM", EnvLoader::get("GROUPE_STEAM", "0")); // Lien du groupe steam, laissez 0 si vide
	
	define("NEWS_A_AFFICHER", 5); //Nombre de news à afficher sur la page d'accueil
	define("LIMITE_RESULTATS_RECHERCHE", 50); // Nombre de resultats maximum pour les formulaire etc

	
	/*
		Configuration des mails
	*/
	
	define("MAIL_AUTEUR", EnvLoader::get("MAIL_AUTEUR", "AutoVip")); // Nom de l'auteur des mails
	define("MAIL_REPLY", EnvLoader::get("MAIL_REPLY", "noreply@example.com")); // Expediteur des mails
	
	define("MAIL_INSCRIPTION", (int)EnvLoader::get("MAIL_INSCRIPTION", "1")); // Envoyer un mail lors de l'inscription / 1 = Activé, 0 = Désactivé
	
	define("MAIL_INSCRIPTION_TITLE", EnvLoader::get("MAIL_INSCRIPTION_TITLE", "Bienvenue sur notre AutoVIP")); // titre du mail
	define("MAIL_INSCRIPTION_CONTENT","

		<strong>Bienvenue sur notre site {PSEUDO}!</strong>
		<p>Grâce à ce site, vous pouvez devenir vip sur tous nos serveurs!
		Vous pouvez recharger votre compte par PayPal ou StarPass.
		</p>
		
		<u>Rappel des informations utiles</u>:
		</span>
		
		URL de notre site : {SITE_URL}
		Votre identifiant : <strong><span style=\"color: #3399FF;\">{IDENTIFIANT}</span></strong>
		Votre mot de passe : <strong><span style=\"color: #3399FF;\">{MOT_DE_PASSE}</span></strong>
		Steam ID : <strong><span style=\"color: #3399FF;\">{STEAM_ID}</span></strong>
		
		<strong><span style=\"color: #FF3300;\">INFORMATION : Pour des mesures de sécurité, votre mot de passe est crypté dans notre base de données !</span></strong>
		
		################################
		Adresse de notre Forum : {FORUM_URL}
		Lien de notre Sourcebans : {SOURCEBANS_URL}
		Lien de notre groupe Steam: {GROUPE_STEAM}
		################################
		:: {NOM_TEAM} <{CONTACT_RESPONSABLE}>
		</span>
	
	"); // Contenu du mail d'inscription
	/* Variables disponibles : {SITE} {SITE_URL} {PSEUDO} {IDENTIFIANT} {MOT_DE_PASSE} {NOM_TEAM} {FORUM_URL} {SOURCEBANS_URL} {GROUPE_STEAM} {CONTACT_RESPONSABLE} */

	/*
		Configuration Steam OpenID
	*/
	
	define("STEAM_API_KEY", EnvLoader::get("STEAM_API_KEY", "")); // Clé API Steam (obtenir sur https://steamcommunity.com/dev/apikey) - Laisser vide pour désactiver
	define("STEAM_OPENID_ENABLED", (int)EnvLoader::get("STEAM_OPENID_ENABLED", "1")); // Activer l'authentification Steam OpenID (1 = Activé, 0 = Désactivé)
	
	/*
		Configuration de la base de données MySQL
	*/
	
	
	define("SQL_HOST", EnvLoader::get("SQL_HOST", 'localhost')); // host de la bdd
	define("SQL_USER", EnvLoader::get("SQL_USER", 'root')); // identifiant de la bdd
	define("SQL_BDD", EnvLoader::get("SQL_BDD", 'autovip')); // bdd utilisée 
	define("SQL_PASSWORD", EnvLoader::get("SQL_PASSWORD", '')); // pass de la bdd
	define("SB_PREFIX", EnvLoader::get("SB_PREFIX", 'sb')); // prefix des tables de sourcebans
	define("SQL_PREFIX", EnvLoader::get("SQL_PREFIX", 'af')); // Prefix utilisée pour les tables
	define("ADMIN_PREFIX_SB", EnvLoader::get("ADMIN_PREFIX_SB", '[VIP]')); // Prefix des admins sur le sourcebans
	define("ADMIN_FIN_PSEUDO_SB", EnvLoader::get("ADMIN_FIN_PSEUDO_SB", '')); // Pareil que au dessus, mais à la fin du name
	
	
	/*
		Module de limitation de durée de ban
	*/
	
	
	define("ALLOW_REGISTER", (int)EnvLoader::get("ALLOW_REGISTER", "1")); //Accepter les nouvelles inscriptions: 1 : Oui (default), 0 = Bloquer les nouvelles inscriptions
	define("MAX_REGISTER_IP", (int)EnvLoader::get("MAX_REGISTER_IP", "2")); //Inscription par IP maximum autorisés (2 inscriptionS par IP)
	define("DELAY_RECOVERY_PASSE", (int)EnvLoader::get("DELAY_RECOVERY_PASSE", "300")); //Nombre de password recovery autorisé (en secondes) , 0 si vous ne souhaitez pas cette protection

	/*
			Prix globaux
	*/
	
	
	define("TOKEN_GEO", (int)EnvLoader::get("TOKEN_GEO", "0")); // Nombre de token à payer pour être géo (geo = access sur tous les serveurs VIP + ADMIN) (1 mois) (laissé 0 si vous ne souhaitez pas d'offres géo)

	
	/*
		Configuration des droits admins
	*/
	
	define("ROOT_SITE", EnvLoader::get("ROOT_SITE", "STEAM_1:1:0")); // Steam id de la personne qui aura tous les droits sur le site vip (vous)
	
	//Pensez également à vous attribuer le niveau 5  sur le site 
	
	// les levels vont de 1 à 5 !
	
	if(strcmp($lang, "fr") == 0) {
		define("LEVEL_NAME_1", "Administrateur"); // Nom donné aux admins de niveau 1 (vide si ce rang n existe pas)
	}
	else {
		define("LEVEL_NAME_1", "Administrator"); // Nom donné aux admins de niveau 1 (vide si ce rang n existe pas)
	}
	if(strcmp($lang, "fr") == 0) {
		define("LEVEL_NAME_2", "Formateur"); // Nom donné aux admins de niveau 2 (vide si ce rang n existe pas)
	}
	else {
		define("LEVEL_NAME_2", "Formative"); // Nom donné aux admins de niveau 2 (vide si ce rang n existe pas)
	}
	if(strcmp($lang, "fr") == 0) {
		define("LEVEL_NAME_3", "Responsable"); // Nom donné aux admins de niveau 3 (vide si ce rang n existe pas)
	}
	else {
		define("LEVEL_NAME_3", "Responsible"); // Nom donné aux admins de niveau 3 (vide si ce rang n existe pas)
	}
	if(strcmp($lang, "fr") == 0) {
		define("LEVEL_NAME_4", "Trésorier"); // Nom donné aux admins de niveau 4 (vide si ce rang n existe pas)
	}
	else {
		define("LEVEL_NAME_4", "Treasurer"); // Nom donné aux admins de niveau 4 (vide si ce rang n existe pas)
	}
	if(strcmp($lang, "fr") == 0) {
		define("LEVEL_NAME_5", "Vice-Président"); // Nom donné aux admins de niveau 5 (vide si ce rang n existe pas)
	}
	else {
		define("LEVEL_NAME_5", "Vice-President"); // Nom donné aux admins de niveau 4 (vide si ce rang n existe pas)
	}
	if(strcmp($lang, "fr") == 0) {
		define("LEVEL_NAME_ROOT", "Président"); // Nom donné aux admins de niveau ROOT (vous) 
	}
	else {
		define("LEVEL_NAME_ROOT", "President"); // Nom donné aux admins de niveau ROOT (vous)
	}
	
	define("LVL_NEWS", (int)EnvLoader::get("LVL_NEWS", "3")); // Level d'un admin (site vip) pour acceder aux news (ajout/modification/suppresion)
	define("LVL_GESTION_ADMIN", (int)EnvLoader::get("LVL_GESTION_ADMIN", "3")); // Level d'un admin (site vip) pour accéder à la gestion des admins (suspension/desuspendre)
	define("LVL_GESTION_TOKEN", (int)EnvLoader::get("LVL_GESTION_TOKEN", "3")); // Level d'un admin (site vip) pour accéder à la gestion des tokens (en ajouter, retirer)
	define("LVL_GESTION_LOGS", (int)EnvLoader::get("LVL_GESTION_LOGS", "4")); // Level d'un admin (site vip) pour accéder aux logs (créditations, inscription, etc)
	define("LVL_GESTION_SERVEURS", (int)EnvLoader::get("LVL_GESTION_SERVEURS", "5")); // Level d'un admin (site vip) pour accéder à la gestion des serveurs (en ajouter, retirer)
	define("LVL_GESTION_SITEADMINS", (int)EnvLoader::get("LVL_GESTION_SITEADMINS", "4")); // Level d'un admin (site vip) pour ajouter/modifier des admins sur le site
	/*
		Configuration de paiement
	*/
	
	/* Allopass */
	
	define("ALLOPASS_PAIEMENT", (int)EnvLoader::get("ALLOPASS_PAIEMENT", "0"));	// Activer le paiement par Allopass (0 si désactiver)
	define("ALLOPASS_IDS", EnvLoader::get("ALLOPASS_IDS", '0')); // Informations IDS allopass
	define("ALLOPASS_IDD", EnvLoader::get("ALLOPASS_IDD", '0')); // Informations IDD allopass
	define("ALLOPASS_AUTHID", EnvLoader::get("ALLOPASS_AUTHID", "")); // Information complet de votre document pour le sécuriser
	define("ALLOPASS_CODETEST", EnvLoader::get("ALLOPASS_CODETEST", "")); //Permet de ne pas bloquer le code de test si vous avez des test à faire =P
	
	/* StarPass */
	
	define("STARPASS_PAIEMENT", (int)EnvLoader::get("STARPASS_PAIEMENT", "0"));	// Activer le paiement par StarPass (0 si désactiver)
	define("STARPASS_IDP", EnvLoader::get("STARPASS_IDP", '0')); // Informations IDP StarPass
	define("STARPASS_IDD", EnvLoader::get("STARPASS_IDD", '0')); // Informations IDD StarPass
	define("STARPASS_CODETEST", EnvLoader::get("STARPASS_CODETEST", "")); //Permet de ne pas bloquer le code de test si vous avez des test à faire =P
	
	/* PayPal */
	define("PAYPAL_PAIEMENT", (int)EnvLoader::get("PAYPAL_PAIEMENT", "0"));	// Activer le paiement par PayPal (0 si désactiver)
	define("PAYPAL_MAIL", EnvLoader::get("PAYPAL_MAIL", "paypal@example.com")); // Mail qui recevra le paiement
	define("PAYPAL_CURRENCY", EnvLoader::get("PAYPAL_CURRENCY", "EUR")); //Devise (EUR / USD / etc)
	
	if(strcmp($lang, "fr") == 0) {
		define("PAYPAL_NOM_PRODUIT_1", "Créditation 1 token"); // Nom du produit
	}
	else {
		define("PAYPAL_NOM_PRODUIT_1", "Crediting 1 token"); // Nom du produit
	}
	define("PAYPAL_PRIX_1", EnvLoader::get("PAYPAL_PRIX_1", "1.25")); // Prix en euro (ou devise souhaitee) (0 si vous ne souhaitez pas faire apparaitre cette offre)
	define("PAYPAL_TOKEN_1", (int)EnvLoader::get("PAYPAL_TOKEN_1", "1")); // Nombre de token ajouté pour cette offre
	
	if(strcmp($lang, "fr") == 0) {
		define("PAYPAL_NOM_PRODUIT_2", "Créditation 3 tokens"); // Nom du produit
	}
	else {
		define("PAYPAL_NOM_PRODUIT_2", "Crediting 3 tokens"); // Nom du produit
	}
	define("PAYPAL_PRIX_2", EnvLoader::get("PAYPAL_PRIX_2", "3.50")); // Prix en euro (ou devise souhaitee) (0 si vous ne souhaitez pas faire apparaitre cette offre)
	define("PAYPAL_TOKEN_2", (int)EnvLoader::get("PAYPAL_TOKEN_2", "3")); // Nombre de token ajouté pour cette offre
	
	if(strcmp($lang, "fr") == 0) {
		define("PAYPAL_NOM_PRODUIT_3", "Créditation 6 tokens"); // Nom du produit
	}
	else {
		define("PAYPAL_NOM_PRODUIT_3", "Crediting 6 tokens"); // Nom du produit
	}
	define("PAYPAL_PRIX_3", EnvLoader::get("PAYPAL_PRIX_3", "6.00")); // Prix en euro (ou devise souhaitee) (0 si vous ne souhaitez pas faire apparaitre cette offre)
	define("PAYPAL_TOKEN_3", (int)EnvLoader::get("PAYPAL_TOKEN_3", "6")); // Nombre de token ajouté pour cette offre
	
	if(strcmp($lang, "fr") == 0) {
		define("PAYPAL_NOM_PRODUIT_4", "Créditation 12 tokens"); // Nom du produit
	}
	else {
		define("PAYPAL_NOM_PRODUIT_4", "Crediting 12 tokens"); // Nom du produit
	}
	define("PAYPAL_PRIX_4", EnvLoader::get("PAYPAL_PRIX_4", "10.00")); // Prix en euro (ou devise souhaitee) (0 si vous ne souhaitez pas faire apparaitre cette offre)
	define("PAYPAL_TOKEN_4", (int)EnvLoader::get("PAYPAL_TOKEN_4", "12")); // Nombre de token ajouté pour cette offre

	if(strcmp($lang, "fr") == 0) {
		define("PAYPAL_NOM_PRODUIT_5", "Accès VIP à vie"); // Nom du produit
	}
	else {
		define("PAYPAL_NOM_PRODUIT_5", "VIP access for life"); // Nom du produit
	}
	define("PAYPAL_PRIX_5", EnvLoader::get("PAYPAL_PRIX_5", "30.00")); // Prix en euro (ou devise souhaitee) (0 si vous ne souhaitez pas faire apparaitre cette offre)
	define("PAYPAL_TOKEN_5", (int)EnvLoader::get("PAYPAL_TOKEN_5", "999")); // Nombre de token ajouté pour cette offre
	
	define("PAYPAL_RETURN_PAGE1", URL_SITE . "/index.php?p=token&c=bon_paypal&lang=$lang");
	define("PAYPAL_RETURN_PAGE2", URL_SITE . "/index.php?p=token&c=bon_paypal2&lang=$lang");
	define("PAYPAL_RETURN_PAGE3", URL_SITE . "/index.php?p=token&c=bon_paypal3&lang=$lang");
	define("PAYPAL_RETURN_PAGE4", URL_SITE . "/index.php?p=token&c=bon_paypal4&lang=$lang");
	define("PAYPAL_RETURN_PAGE5", URL_SITE . "/index.php?p=token&c=bon_paypal5&lang=$lang");
	define("PAYPAL_CANCEL_PAGE", URL_SITE . "/index.php?p=token&lang=$lang");
	define("PAYPAL_URL_NOTIFICATION", URL_SITE . "/pages/paiements/paypal_notification.php");
	
	/* Rentabiliweb - Monnaie virtuelle (Mini Tokens) */
	
	define("RENTABILIWEB_PAIEMENT", (int)EnvLoader::get("RENTABILIWEB_PAIEMENT", "0"));	// Activer le paiement par Rentabiliweb (0 si désactiver)
	define("RENTABILIWEB_HASH", EnvLoader::get("RENTABILIWEB_HASH", "")); // Définir le hash de la page callback
	define("RENTABILIWEB_DOCID", (int)EnvLoader::get("RENTABILIWEB_DOCID", "0")); // DocID rentabiliweb 
	define("RENTABILIWEB_SITEID", (int)EnvLoader::get("RENTABILIWEB_SITEID", "0")); // SiteID
	define("MINITOKEN_CONVERSION", (int)EnvLoader::get("MINITOKEN_CONVERSION", "110")); // Nombre de minitoken pour obtenir 1 token
?>