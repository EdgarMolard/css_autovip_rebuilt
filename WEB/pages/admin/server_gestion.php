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

	
	elseif (GetInfo($_SESSION['af_id'], 'admin_level') < LVL_GESTION_SERVEURS && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'avez pas le niveau d\'administration suffisant ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	
	
	else
	{
	
		if ($_GET['remove_server'] && is_numeric($_GET['remove_server']))
		{
			
			$server_req = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_server` WHERE `id` = '".$_GET['remove_server']."'");

			mysqli_query($conn, "
				INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
				VALUES (NULL , '".time()."', 'Suppression de serveur', '".$_SESSION['af_id']."',
				'".mysqli_result($server_req , 0, 'server_name')."', '".mysqli_result($server_req , 0, 'server_ip').":".mysqli_result($server_req , 0, 'server_port')."',
				'".$_SERVER["REMOTE_ADDR"]."'
				);
			");
			mysqli_query($conn, "DELETE FROM `".SQL_PREFIX."_server` WHERE `id` = '".$_GET['remove_server']."'");
		}
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Gestion des serveurs</h1></div>
				<div class="box-content">
    			<div class="table">
    				<table>
    					<thead>
    						<tr>
    							<td><div>Serveur</div></td>
   								<td><div>Vip Prix</div></td>
								<td><div>Admin Prix</div></td>
								<td><div>Leadeur</div></td>
								<td><div>Actions</div></td>
   							</tr>
   						</thead>
   						<tbody>';
		$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_server` ORDER BY id DESC");
		$class = "odd";
		while($row = mysqli_fetch_array($requete))
		{
			if ($class == "odd")	$class = "even";
			else 	$class = "odd";
			
			if (strlen($row['vip_prix']) == 0 OR $row['vip_prix'] == 0)	$row['vip_prix'] = "N/A";
			if (strlen($row['admin_prix']) == 0 OR $row['admin_prix'] == 0)	$row['admin_prix'] = "N/A";
								
			echo '
								<tr>
    								<td><div class="'.$class.'"><a class="tooltip" href="#" title="IP: '.$row['server_ip'].' / Port: '.$row['server_port'].'  / Type: '.$row['server_type'].'">'.$row['server_name'].'</a></div></td>
									<td><div class="'.$class.'">'.$row['vip_prix'].'</div></td>
									<td><div class="'.$class.'">'.$row['admin_prix'].'</div></td>
									<td><div class="'.$class.'">'.$row['server_manager'].'</div></td>
									<td><div class="'.$class.'"><a class="tooltip" href="index.php?p=server_gestion&remove_server='.$row['id'].'" title="Effacer ce serveur">Supprimer</a></div></td>
    							</tr>';
		}
		echo '
    				</tbody>
    			</table>
			</div>
			<div class="text">
		';
				
		echo '<p><img src="./img/icon/famfamfam/add.png"> <a class="tooltip" href="index.php?p=server_add" title="Ajouter un nouveau serveur">Nouveau serveur </a></p><br>';
				
		echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
		
	}
?>