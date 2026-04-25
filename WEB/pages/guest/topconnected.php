<?php	
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	else
	{
		if(isset($_GET['page']) && is_numeric($_GET['page']))
			$page = $_GET['page'];
		else
			$page = 1;

		// Nombre d'info par page
		$pagination = 15;
		// Numéro du 1er enregistrement à lire
		$limit_start = ($page - 1) * $pagination;
		$TopSql = mysqli_connect(SQL_HOST, "TimerConnected", "TimerConnected", "TimerConnected");
		if(!$TopSql) die('Erreur de connexion : ' . mysqli_connect_errno());
		mysqli_query($TopSql, "SET NAMES 'utf8'");
		$resultat = mysqli_query($TopSql, "SELECT * FROM `Time_Players` ORDER BY Time DESC LIMIT $limit_start, $pagination");
		
		if ($_POST['search'])
		{
			$resultat = mysqli_query($TopSql, "SELECT * FROM `Time_Players` WHERE `Pseudo` LIKE '%".mysqli_real_escape_string($TopSql, $_POST['search'])."%' ORDER BY Time DESC LIMIT $limit_start, $pagination");
		}
		$num = ($page - 1) * $pagination;


		if ($_GET['action'] == "delete" && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_ADMIN))
		{
			mysqli_query($TopSql, "DELETE FROM Time_Players;");
			mysqli_query($TopSql, "ALTER TABLE Time_Players AUTO_INCREMENT = 0;");
			
			echo '
			<div class="notification success">
					<div class="messages">La liste a bien été réinitialisée<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification success -->';
		}
		if ($_SESSION['af_id'] && $_SESSION['af_ip_client'])
		{
			$client_token = GetInfo($_SESSION['af_id'], 'token');
			$access_vip = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='1'"));
			
			$suspension = GetInfo($_SESSION['af_id'], 'suspend_reason');
			$suspended = nl2br(GetInfo($_SESSION['af_id'], 'is_suspended'));
			
			if (strlen($suspension) == 0)	$suspension = "Aucun raison n'a été indiquée";
			
			if ($suspended > 0)
				echo '
				<div class="notification error">
					<div class="messages">Votre compte est actuellement suspendu pour le motif suivant : <i>'.$suspension.'</i><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification info -->';
			else if ($access_vip == 0 && $client_token == 1)
				echo '
				<div class="notification warning">
					<div class="messages">Vous avez '.$client_token.' Token, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits&t=1&server_id=42">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification info -->';
			elseif($access_vip == 0 && $client_token > 1)
				echo '
				<div class="notification warning">
					<div class="messages">Vous avez '.$client_token.' Tokens, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits&t=1&server_id=42">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification info -->';
			elseif($access_vip == 0 && $client_token == 0)
				echo '
				<div class="notification warning">
					<div class="messages">Vous n\'avez aucun Token et n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=token">Créditation Tokens</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification info -->';
		}
		
		if ($_POST['search'])
		{
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Joueurs les plus connectés sur nos Serveurs ce mois-ci</h1></div>
				<div class="box-content">
    			<div class="table">
    				<table>
    					<thead>
    						<tr>
    							<td><div>Temps</div></td>
								<td><div>Joueur</div></td>
   							</tr>
   						</thead>
   						<tbody>';
		}
		else
		{
			echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Joueurs les plus connectés sur nos Serveurs ce mois-ci</h1></div>
					<div class="box-content">
					<div class="table">
						<table>
							<thead>
								<tr>
									<td><div>N°</div></td>
									<td><div>Temps</div></td>
									<td><div>Joueur</div></td>
								</tr>
							</thead>
							<tbody>';
		}
		/*$requete = mysqli_query($conn, "SELECT * FROM `rankme` ORDER BY connected DESC LIMIT ".LIMITE_RESULTATS_RECHERCHE."");*/
		$class = "odd";
		while($row = mysqli_fetch_array($resultat))
		{
			if ($row['SteamId'] != "BOT" && $row['Time'] > 0)
			{
				if ($class == "odd")	$class = "even";
				else 	$class = "odd";

				$num++;
			}

			if ($_POST['search'])
			{
				$num = -1;
				echo TopAffichage($class, $row['Time'], $row['Pseudo'], $row['SteamId'], $num);
			}
			else
			{
				echo TopAffichage($class, $row['Time'], $row['Pseudo'], $row['SteamId'], $num);			
			}
		}
		
		echo '
			<div class="notification info">
				<div class="messages">Le classement se réinitialise chaque début de mois, et les 3 joueurs top classés lors de la réinitialisation remportent deux à une semaine de VIP ! (2Semaines pour le 1er et 1Semaine pour le 2ème et 3ème).<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification info -->';
		
		if ((ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_ADMIN) && $_GET['action'] != "confirm")
		{
			echo '<a style="text-decoration: none;" href="index.php?p=top&action=confirm">Réinitialiser la liste des Top Connectés</a>';
		}
		else if ((ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_ADMIN) && $_GET['action'] == "confirm")
		{
			echo '<a style="text-decoration: none;" href="index.php?p=top&action=delete">Confirmer la réinitialisation ?</a>';
		}

		echo '<br>
		<div class="form">
				<form METHOD="POST" name ="client_search" action="index.php?p=top">
							<fieldset>
								<input type="HIDDEN" id="client_search" name="client_search" value="client_search">
								<input type="text" class="text small" id="search" name="search" maxlength="128" placeholder="Pseudonyme" />
								
								<input type="submit" name="preview" id="preview" value="Rechercher" class="submit" />  

							</fieldset>
						</form>
					</div><!-- end div .form -->
			';
		
		if (!$_POST['search'])
		{
			$nb_total = mysqli_query($TopSql, 'SELECT COUNT(IF(Time>0,1,null)) AS nb_total FROM `Time_Players`');
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
				echo " <a href=\"?p=top&page=1\" style=\"text-decoration:none\"> <font color=\"blue\">Première</font></a> <a href=\"?p=top&page=$i2\" style=\"text-decoration:none\"><font color=\"blue\"><</font></a> ";
			for ($i = 1 ; $i <= $nb_pages ; $i++) 
			{
				if ($i == $page )
					echo " $i";
				else if ($i < $pagemax && $i > $pagemin)
					echo " <a href=\"?p=top&page=$i\" style=\"text-decoration:none\"><font color=\"blue\">$i</font></a> ";
			}
			if ($page == $nb_pages)
				echo " > Dernière ";
			else 
				echo " <a href=\"?p=top&page=$i1\" style=\"text-decoration:none\"><font color=\"blue\">></font></a> <a href=\"?p=top&page=$nb_pages\" style=\"text-decoration:none\"><font color=\"blue\">Dernière</font></a> ";
				
			echo ' ]</p><br>';
		}
		else
		{
			echo '<br><br>';
		}
		
		mysqli_close($Topsql);

		echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
	}
?>