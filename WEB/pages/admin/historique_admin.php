<?php

	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	// Vérification que le membre est identifié
	
	
	if (!isset($_SESSION['af_id']))
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	// Vérification des droits admin

	
	elseif (GetInfo($_SESSION['af_id'], 'admin_level') < LVL_GESTION_LOGS && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'avez pas le niveau d\'administration suffisant ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	//################################################################################
	else
	{
		if(isset($_GET['page']) && is_numeric($_GET['page']))
			$page = $_GET['page'];
		else
			$page = 1;
			
		$titre = "Globale";
		
		if ($_GET['id'] && strlen(GetInfo($_GET['id'], 'username')) > 2)
		{
			$titre = "pour le compte de ".htmlentities(GetInfo($_GET['id'], 'username'))." ";
			$option_requete = "WHERE membre='".$_GET['id']."' or detail2='".GetInfo($_GET['id'], 'username')."'";
			$id = "&id=".$_GET['id']."";
		}

		// Nombre d'info par page
		$pagination = 15;
		// Numéro du 1er enregistrement à lire
		$limit_start = ($page - 1) * $pagination;
		$resultat = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_logs` ORDER BY id DESC LIMIT $limit_start, $pagination");
		
		$resultat = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_logs` $option_requete ORDER BY id DESC LIMIT $limit_start, $pagination");
			
		$client_token = GetInfo($_SESSION['af_id'], 'token');
		
		
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Activité '.$titre.'</h1></div>
				<div class="box-content">
    			<div class="table">
    				<table>
    					<thead>
    						<tr>
    							<td><div>Date</div></td>
								<td><div>Membre</div></td>
   								<td><div>Action</div></td>
   								<td><div>Détail 1</div></td>
   								<td><div>Détail 2</div></td>
								<td><div>IP</div></td>
   							</tr>
   						</thead>
   						<tbody>';
		/*$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_logs` $option_requete ORDER BY id DESC LIMIT ".LIMITE_RESULTATS_RECHERCHE."");*/
		$class = "odd";
		while($row = mysqli_fetch_array($resultat))
		{

			if ($class == "odd")	$class = "even";
			else 	$class = "odd";

			if (strlen($row['detail']) == 0)	$row['detail'] = "-";
			if (strlen($row['detail2']) == 0)	$row['detail2'] = "-";

			echo LogAffichage($class, $row['timestamp'], $row['action'], $row['detail'], $row['detail2'], $row['ip'], GetInfo($row['membre'], 'username'));

			}
		
		$nb_total = mysqli_query($conn, "SELECT COUNT(*) AS nb_total FROM `af_logs` $option_requete");
		$nb_total = mysqli_fetch_array($nb_total);
		$nb_total = $nb_total['nb_total'];
				
		$nb_pages = ceil($nb_total / $pagination);
		
		if ($page == 1)
			$pagemax = $page + 5;
		else if ($page == 2)
			$pagemax = $page + 4;
		else
			$pagemax = $page + 3;
			
		if ($page == $nb_pages)
			$pagemin = $page - 5;
		else if ($page == $nb_pages - 1)
			$pagemin = $page - 4;
		else
			$pagemin = $page - 3;
		$i1 = $page + 1;
		$i2 = $page - 1;

			
		echo '<p align="center">[';
		if ($page == 1)
			echo " Première < ";
		else 
			echo " <a href=\"?p=historique_admin$id&page=1\" style=\"text-decoration:none\"> <font color=\"blue\">Première</font></a> <a href=\"?p=historique_admin$id&page=$i2\" style=\"text-decoration:none\"><font color=\"blue\"><</font></a> ";
		for ($i = 1 ; $i <= $nb_pages ; $i++) {
			if ($i == $page )
				echo " $i";
			else if ($i < $pagemax && $i > $pagemin)
				echo " <a href=\"?p=historique_admin$id&page=$i\" style=\"text-decoration:none\"><font color=\"blue\">$i</font></a> ";
			/*else if ($i == $pagemax && $i != $nb_pages)
				echo " <a href=\"?p=historique_admin&page=$i1\" style=\"text-decoration:none\"><font color=\"blue\">></font></a> ";
			else if ($i == $pagemin && $i != 1)
				echo " <a href=\"?p=historique_admin&page=$i2\" style=\"text-decoration:none\"><font color=\"blue\"><</font></a> ";
			else if ($i == $pagemax && $i == $nb_pages)
				echo " <a href=\"?p=historique_admin&page=$i1\" style=\"text-decoration:none\"><font color=\"blue\">></font></a> <a href=\"?p=historique_admin&page=$i\" style=\"text-decoration:none\"><font color=\"blue\">Dernière</font></a> ";
			else if ($i == $pagemin && $i == 1)
				echo " <a href=\"?p=historique_admin&page=$i\" style=\"text-decoration:none\"> <font color=\"blue\">Première</font></a> <a href=\"?p=historique_admin&page=$i2\" style=\"text-decoration:none\"><font color=\"blue\"><</font></a> ";
			else if ($i > $pagemax && $i == $nb_pages)
				echo " <a href=\"?p=historique_admin&page=$i\" style=\"text-decoration:none\"><font color=\"blue\">Dernière</font></a> ";
			else if ($i < $pagemin && $i == 1)
				echo " <a href=\"?p=historique_admin&page=$i\" style=\"text-decoration:none\"><font color=\"blue\">Première</font></a> ";*/}
		if ($page == $nb_pages)
			echo " > Dernière ";
		else 
			echo " <a href=\"?p=historique_admin$id&page=$i1\" style=\"text-decoration:none\"><font color=\"blue\">></font></a> <a href=\"?p=historique_admin$id&page=$nb_pages\" style=\"text-decoration:none\"><font color=\"blue\">Dernière</font></a> ";
		
		echo ' ]</p><br>';
			
		echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
	}
	
?>