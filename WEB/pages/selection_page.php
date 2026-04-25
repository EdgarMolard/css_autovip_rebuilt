<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
		
		
	$page = "./pages/accueil.php"; // page par defaut
	
	
	if (isset($_POST['register']) OR isset($_POST['register2'])) 	$page = './pages/inscription/formulaire.php';				 // Redirection pour faire fonctionner le formulaire d'inscription
	if (isset($_POST['server_add'])) 								$page = './pages/admin/server_add.php';						 // Redirection pour faire fonctionner le formulaire d'ajout de serveur
	
	if (isset($_POST['news_add'])) 									$page = './pages/admin/news_add.php';							 // Redirection pour faire fonctionner le formulaire d'ajout de news
	if (isset($_POST['news_edit']))									$page = './pages/admin/news_edit.php'; 							// Redirection pour faire fonctionner le formulaire d'edit de news
	
	if (isset($_POST['client_search'])) 							$page = './pages/admin/client_list.php'; 						// Redirection pour faire fonctionner le formulaire de recherche de compte
	if (isset($_POST['client_edit'])) 								$page = './pages/admin/client_edit.php'; 						// Redirection pour faire fonctionner le formulaire de d'edition de compte
	if (isset($_POST['client_suspend'])) 							$page = './pages/admin/client_suspend.php'; 					// Redirection pour faire fonctionner le formulaire de changement de pw

	if (isset($_POST['new_pw'])) 									$page = './pages/compte/main.php';							 // Redirection pour faire fonctionner le formulaire de suspension de compte
	
	if (isset($_POST['mini_token']))								$page = './pages/paiements/mini_token.php'; 					// Bouton mini tokenz
	
	
	 /*-- 			Switch des pages 			 --*/
	 
	if (isset($_GET['p']))
	{
		$p = $_GET['p'];
		switch ($_GET['p']) { 
			 /*####################
				Partie guest		 
			 #####################*/
				case 'register': 		$page = './pages/inscription/formulaire.php';				break; // Inscription
				case 'staff': 			$page = './pages/guest/staff.php';							break; // Page du staff
				case 'charte': 			$page = './pages/guest/charte.php';							break; // Page de la charte
				case 'aide':			$page = './pages/guest/aide.php';							break; // Page d'aide
				case 'avantages': 		$page = './pages/guest/avantages.php';						break; // Page des avantages
				case 'top': 			$page = './pages/guest/topconnected.php';					break; // Page des Top connected
			 /*####################
			 Partie Administration			 
			 #####################*/
				case 'server_gestion': 		$page = './pages/admin/server_gestion.php';				break;  // Gestion des serveurs
				case 'server_add': 			$page = './pages/admin/server_add.php';				   	break; 	// Ajout de serveur
				
				case 'liste_vip': 			$page = './pages/admin/liste_vip.php';					break;  // Liste des VIP
				
				case 'news_gestion': 		$page = './pages/admin/news_gestion.php';				 break;	// Gestion des news
				case 'news_add': 			$page = './pages/admin/news_add.php';					 break; // Ajout d'une news
				case 'news_edit': 			$page = './pages/admin/news_edit.php';					 break; // Editer une news
				
				case 'client_list': 		$page = './pages/admin/client_list.php';				 break; // Gestion des clients
				case 'client_edit': 		$page = './pages/admin/client_edit.php';				 break; // Editer un client
				case 'client_suspend': 		$page = './pages/admin/client_suspend.php';				 break; // Suspendre un client
				
				case 'historique_admin': 	$page = './pages/admin/historique_admin.php';			 break; // Historique admin
			 /*####################
				Espace membre			 
			 #####################*/
			 
				case 'logout':	session_destroy();$page = 'accueil.php';echo "<head><meta http-equiv='refresh' content='0; URL=index.php?lang=$lang'></head>";break; // Page de log-out
				case 'token':						$page = './pages/paiements/main.php';				break; 	// Page de créditation de token
				case 'mini_token':					$page = './pages/paiements/mini_token.php';			break; 	// Page de créditation de mini token
				case 'compte': 						$page = './pages/compte/main.php';					 break; // Page d'accueil du compte
				case 'logs': 						$page = './pages/compte/historique.php';			 break; // Historique du compte
				case 'droits':						$page = './pages/compte/droit_list.php';			 break; // Gestion des droits
				case 'credits':						$page = './pages/compte/credits.php';				 break; // Achat de Credits
			 
		}
		
		
	}
	include_once($page);

?>