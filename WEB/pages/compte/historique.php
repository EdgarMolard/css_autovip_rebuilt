<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	if (!isset($_SESSION['af_id']))
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
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
		$resultat = mysqli_query($conn, "SELECT timestamp, action, detail, detail2 FROM `".SQL_PREFIX."_logs` WHERE `membre`='".$_SESSION['af_id']."' ORDER BY id DESC LIMIT $limit_start, $pagination");
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
		
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Activité de votre compte</h1></div>
				<div class="box-content">
    			<div class="table">
    				<table>
    					<thead>
    						<tr>
    							<td><div>Date</div></td>
   								<td><div>Action</div></td>
   								<td><div>Détail 1</div></td>
   								<td><div>Détail 2</div></td>
   							</tr>
   						</thead>
   						<tbody>';
						/*$requete = mysqli_query($conn, "SELECT timestamp, action, detail, detail2 FROM `".SQL_PREFIX."_logs` WHERE `membre`='".$_SESSION['af_id']."' ORDER BY id DESC LIMIT ".LIMITE_RESULTATS_RECHERCHE."");*/
						$class = "odd";
						while($row = mysqli_fetch_array($resultat))
						{
						
							if ($class == "odd")	$class = "even";
							else 	$class = "odd";
								
							if (strlen($row['detail']) == 0)	$row['detail'] = "-";
							if (strlen($row['detail2']) == 0)	$row['detail2'] = "-";
							if ($row['detail'] == "ViP")	$row['detail'] = "VIP";
							if ($row['detail2'] == "Tous")	$row['detail2'] = "Tous les Serveurs";
							echo LogAffichage($class, $row['timestamp'], $row['action'], $row['detail'], $row['detail2'], NULL, NULL);
							//function LogAffichage($class, $time, $action, $detail, $detail2, $ip)
							// echo '
								// <tr>
    								// <td><div class="'.$class.'">'.date("m/d/Y à H:i:s", $row['timestamp']).'</div></td>
									// <td><div class="'.$class.'">'.$row['action'].'</div></td>
									// <td><div class="'.$class.'">'.$row['detail'].'</div></td>
									// <td><div class="'.$class.'">'.$row['detail2'].'</div></td>
    							// </tr>';
						}

		$nb_total = mysqli_query($conn, 'SELECT COUNT(case when membre='.$_SESSION['af_id'].' then 1 else null end) AS nb_total FROM `af_logs`');
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
			echo " <a href=\"?p=logs&page=1\" style=\"text-decoration:none\"> <font color=\"blue\">Première</font></a> <a href=\"?p=logs&page=$i2\" style=\"text-decoration:none\"><font color=\"blue\"><</font></a> ";
		for ($i = 1 ; $i <= $nb_pages ; $i++) 
		{
			if ($i == $page )
				echo " $i";
			else if ($i < $pagemax && $i > $pagemin)
				echo " <a href=\"?p=logs&page=$i\" style=\"text-decoration:none\"><font color=\"blue\">$i</font></a> ";
		}
		if ($page == $nb_pages)
			echo " > Dernière ";
		else 
			echo " <a href=\"?p=logs&page=$i1\" style=\"text-decoration:none\"><font color=\"blue\">></font></a> <a href=\"?p=logs&page=$nb_pages\" style=\"text-decoration:none\"><font color=\"blue\">Dernière</font></a> ";
			
		echo ' ]</p><br>';
				

				
		echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
	}
?>