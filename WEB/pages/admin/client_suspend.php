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

	
	elseif (GetInfo($_SESSION['af_id'], 'admin_level') < LVL_GESTION_ADMIN && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'avez pas le niveau d\'administration suffisant ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	elseif ($_SESSION['af_steam_id'] == GetInfo($_POST['client_suspend'], 'steam_id') OR $_SESSION['af_steam_id'] == GetInfo($_GET['id'], 'steam_id') )
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous ne pouvez pas vous suspendre! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	elseif (ROOT_SITE == GetInfo($_POST['client_suspend'], 'steam_id') OR ROOT_SITE == GetInfo($_GET['id'], 'steam_id') )
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas autorisé à suspendre le compte ROOT! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	elseif ((GetInfo($_GET['id'], 'admin_level') > $_SESSION['af_admin_level'] OR GetInfo($_POST['client_suspend'], 'admin_level') >=  $_SESSION['af_admin_level']))
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas autorisé à suspendre un administrateur! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	//################################################################################
	else
	{
		
		if (strlen(GetInfo($_GET['id'], 'username')) > 2)					{$id_user = $_GET['id']			;$username = GetInfo($_GET['id'], 'username');				}
		if (strlen(GetInfo($_POST['client_suspend'], 'username')) > 2)			{$id_user = $_POST['client_suspend'];$username = GetInfo($_POST['client_suspend'], 'username');	}
		// echo "<script> alert('".$id_user."') </script>";
		if (strlen($username) > 2 && is_numeric($id_user) && $id_user > 0)
		{
			if ($_POST['admin_unsuspend_button'])
			{

				//mysqli_query($conn, "
				//	INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
				//	VALUES (NULL , '".time()."', 'Retrait Suspension', '".$_SESSION['af_id']."',
				//	 '".mysqli_real_escape_string($conn, $username)."', '-', '".$_SERVER["REMOTE_ADDR"]."'
				//	);
				//");
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Retrait Suspension', '".$id_user."',
					 'Levée par: ".mysqli_real_escape_string($conn, $_SESSION['af_pseudo'])."', 'VIP', '-'
					);
				");
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `is_suspended` = '0' WHERE `id` = '".$id_user."';");
				
				echo'
					<div class="notification success">
						<div class="messages">Ce compte n\'est plus suspendu<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
			}
			if ($_POST['admin_suspend_button'])
			{
	
				//mysqli_query($conn, "
				//	INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
				//	VALUES (NULL , '".time()."', 'Suspension', '".$_SESSION['af_id']."',
				//	 '".mysqli_real_escape_string($conn, $username)."', '-', '".$_SERVER["REMOTE_ADDR"]."'
				//	);
				//");
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Suspension', '".$id_user."',
					 'Posée par: ".mysqli_real_escape_string($conn, $_SESSION['af_pseudo'])."', 'VIP', '-'
					);
				");
				if (strlen($_POST['suspend_reason']) < 2)			$raison = "Contactez l\'administrateur";
				else												$raison = $_POST['suspend_reason'];
				mysqli_query($conn, "
							UPDATE `".SQL_PREFIX."_users` SET 
							`is_suspended` = '1',
							`suspend_admin` = '".mysqli_real_escape_string($conn, $_SESSION['af_pseudo'])."',
							`suspend_time` = '".time()."',
							`suspend_reason` = '".htmlentities(mysqli_real_escape_string($conn, $_POST['suspend_reason']), ENT_QUOTES, "UTF-8")."'
							WHERE `id` = '".$id_user."';");
				
				echo'
					<div class="notification success">
						<div class="messages">Ce compte est désormais suspendu<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
				}
			

			$requete = mysqli_query($conn, "SELECT steam_id, is_suspended, suspend_reason, suspend_admin, suspend_time FROM `".SQL_PREFIX."_users` WHERE `id`='".$id_user."' LIMIT 1");
			while($row = mysqli_fetch_array($requete))
			{
				$is_suspended =	$row['is_suspended']	;	$suspend_reason =			$row['suspend_reason'];			;	$suspend_admin = $row['suspend_admin'];
				$suspend_time =	$row['suspend_time']	;	$steam_id = 				$row['steam_id'];
				if (ROOT_SITE == $steam_id)
					$admin_level = 5;
			}
			
			echo '
				<div class="box-out">
					<div class="box-in">
						<div class="box-head"><h1>Gestion de suspension (\''.$username.'\')</h1></div>
						<div class="box-content">';
						
			
			if ($is_suspended == 0)
			{
				echo '
				<div class="form">
						<form METHOD="POST" name ="client_suspend" action="index.php">
									<fieldset>
										<input type="HIDDEN" id="client_suspend" name="client_suspend" value="'.$id_user.'">  
										
										<label for="medium_field">Steam ID</label>
										<input type="text" class="text small" maxlength="128" value="'.$steam_id.'" disabled/>';
				
				echo '					<label for="medium_field">Droits actuels de l\'utilisateur (Admins/Géofront)</label>';
				
				/*
					Listing des différents droits
				*/
				$geo_req = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='3' AND `ip_serveur`='-' AND `port_serveur`='-' LIMIT 1");
				$geo_date = mysqli_result($geo_req, 0, 'date_fin');
				
				if ($geo_date > time())
					echo '				<input type="checkbox" disabled checked> Géofrontiste (expire le '.date("d/m/Y", $geo_date).')<br>';
				else
					echo '				<input type="checkbox" disabled> Géofrontiste <i>(aucun accès)</i><br>';
					
					
				$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_server` ORDER BY id");
				while($row = mysqli_fetch_array($requete))
				{
					
					$current = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='1' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."' LIMIT 1");
					$adm_date = mysqli_result($current, 0, 'date_fin');
					if ($adm_date > time())
							echo '				<input type="checkbox" disabled checked> '.$row['server_name'].' (expire le '.date("d/m/Y", $adm_date).')<br>';
						else
							echo '				<input type="checkbox" disabled> '.$row['server_name'].' <i>(aucun accès)</i><br>';
				
				}
				/*
					Listing des différents droits // end
				*/
				
										
				echo '					<br>';
				echo '					<label for="medium_field">Raison de la suspension (visible par l\'utilisateur)</label>
										<textarea class="text textarea" cols="20" rows="5" name="suspend_reason" id="suspend_reason"></textarea>
										<input type="submit" name="admin_suspend_button" id="admin_suspend_button" value="Suspendre cet administrateur" class="submit" />  
											<br><br>
									</fieldset>
								</form>
							</div><!-- end div .form -->
					';
				}
				//################################################################
				
			else
			{
				echo '
				<div class="form">
						<form METHOD="POST" name ="client_suspend" action="index.php">
									<fieldset>
										<input type="HIDDEN" id="client_suspend" name="client_suspend" value="'.$id_user.'">  
										
										<label for="medium_field">Steam ID</label>
										<input type="text" class="text small" maxlength="128" value="'.$steam_id.'" disabled/>';
				
				echo '					<label for="medium_field">Droits actuels de l\'utilisateur (Admins/Géofront)</label>';
				
				/*
					Listing des différents droits
				*/
				$geo_req = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='3' AND `ip_serveur`='-' AND `port_serveur`='-' LIMIT 1");
				$geo_date = mysqli_result($geo_req, 0, 'date_fin');
				
				if ($geo_date > time())
					echo '				<input type="checkbox" disabled checked> Géofrontiste (expire le '.date("d/m/Y", $geo_date).')<br>';
				else
					echo '				<input type="checkbox" disabled> Géofrontiste <i>(aucun accès)</i><br>';
					
					
				$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_server` ORDER BY id");
				while($row = mysqli_fetch_array($requete))
				{
					
					$current = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='1' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."' LIMIT 1");
					$adm_date = mysqli_result($current, 0, 'date_fin');
					if ($adm_date > time())
							echo '				<input type="checkbox" disabled checked> '.$row['server_name'].' (expire le '.date("d/m/Y", $adm_date).')<br>';
						else
							echo '				<input type="checkbox" disabled> '.$row['server_name'].' <i>(aucun accès)</i><br>';
				
				}

				
				echo '
										<label for="medium_field">Suspendu par</label>
										<input type="text" class="text small" maxlength="128" value="'.$suspend_admin.'" disabled/>';
				echo '
										<label for="medium_field">Date de la suspension</label>
										<input type="text" class="text small" maxlength="128" value="'.date("d/m/Y à H:m:s", $suspend_time).'" disabled/>';

				echo '
										<label for="medium_field">Suspendu depuis</label>
										<input type="text" class="text small" maxlength="128" value="'.transforme((time() - $suspend_time)).'" disabled/>';
										
				echo '					<br>';
				echo '					<label for="medium_field">Raison de la suspension (visible par l\'utilisateur)</label>
										<textarea class="text textarea" cols="20" rows="5" name="suspend_reason" id="suspend_reason" disabled>'.$suspend_reason.'</textarea>
										<input type="submit" name="admin_unsuspend_button" id="admin_unsuspend_button" value="Lever la suspension" class="submit" />  
											<br><br>
									</fieldset>
								</form>
							</div><!-- end div .form -->
					';
				}
				//###############################################################

				echo '
						</div><!-- end div .box-content -->
					</div><!-- end div .box-in -->
				</div><!-- end div .box-out -->
				';
		}
	}
	
?>