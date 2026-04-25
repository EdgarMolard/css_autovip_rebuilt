<?php
	/*
		TODO LIST : 
		- Retirer les PO quand un membre s'abonne
		- Faire passer le timestamp geo en priorité 
		- Ajouter des comptes sur sourcebans (1 par utilisateur bien sûr)
	
	*/
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	if (!isset($_SESSION['af_id']))
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
				
	else
	{
		//##################################################################################### 
		//############ ACHAT DE DROITS
		$client_token = GetInfo($_SESSION['af_id'], 'token');
		$access_vip = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE `id` ='".$_SESSION['af_id']."' AND `type_droit`='1' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'"));
		$access_admin = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='2' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'"));
		$suspension = GetInfo($_SESSION['af_id'], 'suspend_reason');
		$suspended = nl2br(GetInfo($_SESSION['af_id'], 'is_suspended'));
		
		if (strlen($suspension) == 0)	$suspension = "Aucun raison n'a été indiquée";
		
		if ($suspended > 0 && $_GET['t'] != "1" || $suspended > 0 && $_GET['t'] != "4")
			echo '
			<div class="notification error">
				<div class="messages">Vous ne pouvez pas acheter d\'Accès VIP, votre compte est suspendu pour la raison suivante : <i>'.$suspension.'</i><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification info -->';
		else if($access_vip == 0 && $client_token == 0)
			echo '
			<div class="notification warning">
				<div class="messages">Vous n\'avez aucun Token et n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=token">Créditation Tokens</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification info -->';
		else if($access_admin == 0 && $client_token == 0)
			echo '
			<div class="notification warning">
				<div class="messages">Vous n\'avez aucun Token et n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=token">Créditation Tokens</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification info -->';

		if ($_GET['t'] > 0 && $_GET['t'] < 5 && is_numeric($_GET['server_id']))
		{
		
			$type = $_GET['t'];
			$client_token = GetInfo($_SESSION['af_id'], 'token');
			//Récupération du prix de l'offre
			if ($type == 3)	{$droit_name = "Geofront";$name_serveur = "-";$port_serveur = "-";$ip_serveur = "-";}
			if ($type == 1)	{$droit_name = "ViP";$champs = "vip_prix";}
			if ($type == 2)	{$droit_name = "Admin";$champs = "admin_prix";}
			if ($type == 4)	{$droit_name = "3Jours Test";$champs = "vip_prix";}
			if ($type != 3)
			{
				$prix_req = mysqli_query($conn, "SELECT `".$champs."`, `server_ip`, `server_port`, `server_name` FROM `".SQL_PREFIX."_server` WHERE `id`= '".$_GET['server_id']."' LIMIT 0,1");
				$prix = mysqli_result($prix_req,0, $champs);
				$ip_serveur = mysqli_result($prix_req,0, 'server_ip');
				$port_serveur = mysqli_result($prix_req,0, 'server_port');
				$name_serveur = mysqli_result($prix_req,0, 'server_name');
				if (!$prix OR $prix < 1 OR !is_numeric($prix))	$prix = 0;
			}
			else
				$prix = TOKEN_GEO;
				
				
			// L'offre est désactivée
			if ($prix < 1)
				echo '
						<div class="notification error">
							<div class="messages">Vous ne pouvez pas accédez à cette offre, contactez le webmaster <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
						
			// On arrive pas à récupérer le port du serveur
			elseif (!is_numeric($port_serveur) && $type != 3)
				echo '
						<div class="notification error">
							<div class="messages">Une erreur inconnue vient de se produire <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
						
						
			// L'utilisateur n'a pas assez de token
			elseif ($client_token < $prix && $type != 4)
				echo '
						<div class="notification error">
							<div class="messages">Vous n\'avez pas assez de tokens (<a href="index.php?p=token">Créditez votre compte</a>) <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
			// L'utilisateur est suspendu
			elseif (GetInfo($_SESSION['af_id'], 'is_suspended') > 0 && $type == 1 || GetInfo($_SESSION['af_id'], 'is_suspended') > 0 && $type == 4)
				echo '
						<div class="notification error">
							<div class="messages">Vous ne pouvez pas acheter d\'Accès VIP, votre compte est suspendu pour la raison suivante : <i>'.$suspension.'</i><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
			// Toutes les conditions semblent bonnes, on active ses droits =)
			elseif ($type == 1)
			{
				//On verifie si l'utilisateur prolonge, ou non
				$vip_req = mysqli_query($conn, "SELECT id, date_start, date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'");
				$data = mysqli_result($vip_req,0, 'date_fin');
				$start = mysqli_result($vip_req,0, 'date_start');
				$id_droit = mysqli_result($vip_req,0, 'id');
				if (!$start)			$start = time();
				
				if ($data > time())		$expiration = ($data + (86400 * 31));
				else					{$new = 1;$expiration = (time() + (86400 * 31));}

				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token - $prix WHERE `id`='".$_SESSION['af_id']."'");
				
				if ($new)
				{
					$query = "SELECT user_id FROM af_droits WHERE user_id = '".$_SESSION['af_id']."' AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'";
					$result = mysqli_query($conn, $query);
					
					if(mysqli_num_rows($result) > 0)
					{
						$queryy = "SELECT action FROM af_logs WHERE membre = '".$_SESSION['af_id']."' AND action = 'Achat'";
						$resultt = mysqli_query($conn, $queryy);
						
						mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = '".$expiration."' WHERE `id` ='".$id_droit."' AND `type_droit`=1 AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'");
						
						if(mysqli_num_rows($resultt) > 0)
						{
							mysqli_query($conn, "
								INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
								VALUES (NULL , '".time()."', 'Renouvellement', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
							");
							
							echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=renew">';
						}
						else
						{
							mysqli_query($conn, "
									INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
									VALUES (NULL , '".time()."', 'Achat', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
									");
									
							echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=new">';
						}
					}
					else
					{
						mysqli_query($conn, "
									INSERT INTO `".SQL_PREFIX."_droits` (`id` ,`user_id` ,`type_droit` ,`date_start` ,`date_fin` ,`is_suspended` ,`ip_serveur` ,`port_serveur` ,`steam_id`)
									VALUES (NULL ,'".$_SESSION['af_id']."','".$type."','".$start."','".$expiration."','0', '".$ip_serveur."','".$port_serveur."','".$_SESSION['af_steam_id']."');
									");
						mysqli_query($conn, "
									INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
									VALUES (NULL , '".time()."', 'Achat', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
									");
						echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=new">';
					}
				}
				else
				{
					$queryy = "SELECT action FROM af_logs WHERE membre = '".$_SESSION['af_id']."' AND action = 'Achat'";
					$resultt = mysqli_query($conn, $queryy);
					
					mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = '".$expiration."' WHERE `id` ='".$id_droit."' AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'");
					
					if(mysqli_num_rows($resultt) > 0)
					{					
						mysqli_query($conn, "
							INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
							VALUES (NULL , '".time()."', 'Prolongation', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
						");
						
						echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=extend">';
					}
					else
					{
						mysqli_query($conn, "
								INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
								VALUES (NULL , '".time()."', 'Achat', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
								");
									
						echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=new">';
					}
				}
			}
			elseif ($type == 2)
			{
				//On verifie si l'utilisateur prolonge, ou non
				$admin_req = mysqli_query($conn, "SELECT id, date_start, date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'");
				$data = mysqli_result($admin_req,0, 'date_fin');
				$start = mysqli_result($admin_req,0, 'date_start');
				$id_droit = mysqli_result($admin_req,0, 'id');
				if (!$start)			$start = time();
				
				if ($data > time())		$expiration = ($data + (86400 * 31));
				else					{$new = 1;$expiration = (time() + (86400 * 31));}

				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token - $prix WHERE `id`='".$_SESSION['af_id']."'");
				
				if ($new)
				{
					$query = "SELECT user_id FROM af_droits WHERE user_id = '".$_SESSION['af_id']."' AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'";
					$result = mysqli_query($conn, $query);
					
					if(mysqli_num_rows($result) > 0)
					{
						$queryy = "SELECT action FROM af_logs WHERE membre = '".$_SESSION['af_id']."' AND action = 'Achat'";
						$resultt = mysqli_query($conn, $queryy);
						
						mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = '".$expiration."' WHERE `id` ='".$id_droit."' AND `type_droit`=1 AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'");
						
						if(mysqli_num_rows($resultt) > 0)
						{
							mysqli_query($conn, "
								INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
								VALUES (NULL , '".time()."', 'Renouvellement', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
							");
							
							echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=renew">';
						}
						else
						{
							mysqli_query($conn, "
									INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
									VALUES (NULL , '".time()."', 'Achat', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
									");
									
							echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=new">';
						}
					}
					else
					{
						mysqli_query($conn, "
									INSERT INTO `".SQL_PREFIX."_droits` (`id` ,`user_id` ,`type_droit` ,`date_start` ,`date_fin` ,`is_suspended` ,`ip_serveur` ,`port_serveur` ,`steam_id`)
									VALUES (NULL ,'".$_SESSION['af_id']."','".$type."','".$start."','".$expiration."','0', '".$ip_serveur."','".$port_serveur."','".$_SESSION['af_steam_id']."');
									");
						mysqli_query($conn, "
									INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
									VALUES (NULL , '".time()."', 'Achat', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
									");
						echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=new">';
					}
				}
				else
				{
					$queryy = "SELECT action FROM af_logs WHERE membre = '".$_SESSION['af_id']."' AND action = 'Achat'";
					$resultt = mysqli_query($conn, $queryy);
					
					mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = '".$expiration."' WHERE `id` ='".$id_droit."' AND `type_droit`=2");
					
					if(mysqli_num_rows($resultt) > 0)
					{					
						mysqli_query($conn, "
							INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
							VALUES (NULL , '".time()."', 'Prolongation', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
						");
						
						echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=extend">';
					}
					else
					{
						mysqli_query($conn, "
								INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
								VALUES (NULL , '".time()."', 'Achat', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
								");
									
						echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=new">';
					}
				}
			}
			elseif ($type == 4)
			{
				//On verifie si l'utilisateur prolonge, ou non
				$vip_req = mysqli_query($conn, "SELECT id, date_start, date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='".$type."' AND `ip_serveur`='".$ip_serveur."' AND `port_serveur`='".$port_serveur."'");
				$data = mysqli_result($vip_req,0, 'date_fin');
				$start = mysqli_result($vip_req,0, 'date_start');
				$id_droit = mysqli_result($vip_req,0, 'id');
				if (!$start)			$start = time();
				
				$expiration = (time() + 86400*3);
				
				$query = "SELECT user_id FROM af_droits WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='1' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'";
				$result = mysqli_query($conn, $query);
					
				if(mysqli_num_rows($result) == 0)
				{
					mysqli_query($conn, "
								INSERT INTO `".SQL_PREFIX."_droits` (`id` ,`user_id` ,`type_droit` ,`date_start` ,`date_fin` ,`is_suspended` ,`ip_serveur` ,`port_serveur` ,`steam_id`)
								VALUES (NULL ,'".$_SESSION['af_id']."','1','".$start."','".$expiration."','0', '".$ip_serveur."','".$port_serveur."','".$_SESSION['af_steam_id']."');
								");
					mysqli_query($conn, "
								INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
								VALUES (NULL , '".time()."', 'Activation', '".$_SESSION['af_id']."','".$droit_name."' , 'Tous', '".$_SERVER["REMOTE_ADDR"]."');
								");
					echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=test">';
				}
				else
					echo '<meta http-equiv="refresh" content="1; URL=index.php?p=droits&c=test_error">';
			}
		
		}
		//############ ACHAT DE DROITS
		//##################################################################################### 
		
		if ($suspended == 0)
		{
			echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Panel VIP</h1></div>				
					<div class="box-content">';
					if ($_GET['c'] == "new")
					{
						echo'
								<div class="notification success">
									<div class="messages">Vos droits ont été ajoutés avec succès !<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
								</div><!-- end div .notification info -->
							';
					}
					elseif ($_GET['c'] == "renew")
					{
						echo'
								<div class="notification success">
									<div class="messages">Votre accès a bien été renouvelé de 31 jours !<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
								</div><!-- end div .notification info -->
							';	
					}
					elseif ($_GET['c'] == "extend")
					{					
						echo'
							<div class="notification success">
								<div class="messages">Votre accès a bien été prolongé de 31 jours !<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							</div><!-- end div .notification info -->
						';	
					}
					elseif ($_GET['c'] == "test")
					{					
						echo'
							<div class="notification success">
								<div class="messages">Votre période de test du VIP a bien été lancée !<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							</div><!-- end div .notification info -->
						';	
					}
					elseif ($_GET['c'] == "test_error")
					{					
						echo'
							<div class="notification error">
								<div class="messages">Vous ne pouvez pas utiliser votre période de test !<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							</div><!-- end div .notification info -->
						';	
					}
					echo '
					<div class="table">
						<table>
							<thead>
								<tr>
									<td><div>Serveur</div></td>
									<td><div>Accès Vip</div></td>
									<td><div>Accès Admin</div></td>
									<td><div>Période de Test</div></td>
								</tr>
							</thead>
							<tbody>';
							$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_server`");
							$class = "odd";
							$is_geo = mysqli_num_rows(mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND date_fin > ".time()." AND `type_droit`='3' AND `ip_serveur`='-' AND `port_serveur`='-' LIMIT 1"));
							if ($is_geo)
							{
								$req_geo = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='3' AND `ip_serveur`='-' AND `port_serveur`='-'");
								$timestamp_geo = mysqli_result($req_geo,0, 'date_fin');
							}
							while($row = mysqli_fetch_array($requete))
							{
								if ($class == "odd")	$class = "even";
								else 	$class = "odd";
									
								//$row['']
									//Access ViP = 1, Admin = 2, Geo = 3.
									echo '
									<tr>
										<td><div class="'.$class.'"><a class="tooltip" title="Tous les Serveurs présents dans la Liste des Avantages">'.$row['server_name'].'</a></div></td>';
										
										$access_vip = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='1' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'"));
										$access_admin = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='2' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'"));
										$access_test = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_users` WHERE `id`= '".$_SESSION['af_id']."' AND `test`='1'"));
										$status_req = mysqli_query($conn, "SELECT is_suspended FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='2' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'");
										$status = mysqli_result($status_req, 0, 'is_suspended');
										
										
										if ($status > 0 OR GetInfo($_SESSION['af_id'], 'is_suspended') > 0)		$status = "<span style=color:red>SUSPENDU</span>";
										
										/* Champs ViP */
										if ($access_vip == 0 && $timestamp_geo < time() && $row['vip_prix'] > 0)
											if ($suspended > 0)
												$vip_content = '<span style=color:red>Compte Suspendu</span>';
											else
												$vip_content = '<a class="tooltip" href="index.php?p=droits&t=1&server_id='.$row['id'].'" title="Devenir VIP sur tous les serveurs ">Devenir VIP (1 mois / '.$row['vip_prix'].' Token)</a>';
										elseif ($access_vip == 1 OR ($timestamp_geo > time() && $row['vip_prix'] > 0))
										{
											$vip_req = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='1' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'");
											$data = mysqli_result($vip_req,0, 'date_fin');
											if ($data < $timestamp_geo && $timestamp_geo > $data)		$data = $timestamp_geo;
											
											if ($data != $timestamp_geo)
											{
												if ($data > time())
													if ($suspended > 0)
														$vip_content = '<span style=color:red>Compte Suspendu</span>';
													else
														if ($data < 2147483647)
															$vip_content = '<span style=color:green>Expire le: '.date("d/m/Y", $data).' à '.date("H:i", $data).' </span><a class="tooltip" href="index.php?p=droits&t=1&server_id='.$row['id'].'" title="Prolonger votre accès de 31 jours">[Prolonger]</a>';
														else
															$vip_content = '<span style=color:green>Expire le: Jamais</span>';
												else
													if ($suspended > 0)
														$vip_content = '<span style=color:red>Compte Suspendu</span>';
													else
														$vip_content = '<span style=color:red>Expiré le: '.date("d/m/Y", $data).' à '.date("H:i", $data).' </span> <a class="tooltip" href="index.php?p=droits&t=1&server_id='.$row['id'].'" title="Renouveler votre accès de 31 jours">[Renouveler]</a>';
											}
											else
											{
												$vip_content = '<span style=color:green>Accès géofront</span>';
											}
											
										}
										elseif(strlen($row['vip_prix']) == 0 OR $row['vip_prix'] == 0) $vip_content = "N/A";
							
										/* Fin champ ViP */
										
										/* Admin */
										
										if ($access_admin == 0 && $timestamp_geo < time() && $row['admin_prix'] > 0)
											if ($suspended > 0)
												$admin_content = '<span style=color:red>Compte Suspendu</span>';
											else
												$admin_content = '<a class="tooltip" href="index.php?p=droits&t=2&server_id='.$row['id'].'" title="Devenir Admin sur tous les serveurs ">Devenir Admin (1 mois / '.$row['admin_prix'].' Token)</a>';
										elseif ($access_admin == 1 OR ($timestamp_geo > time() && $row['admin_prix'] > 0))
										{
											$admin_req = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='2' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'");
											$data = mysqli_result($admin_req,0, 'date_fin');
											if ($data < $timestamp_geo && $timestamp_geo > $data)		$data = $timestamp_geo;
											
											if ($data != $timestamp_geo)
											{
												if ($data > time())
													if ($suspended > 0)
														$admin_content = '<span style=color:red>Compte Suspendu</span>';
													else
														if ($data < 2147483647)
															$admin_content = '<span style=color:green>Expire le: '.date("d/m/Y", $data).' à '.date("H:i", $data).' </span><a class="tooltip" href="index.php?p=droits&t=2&server_id='.$row['id'].'" title="Prolonger votre accès de 31 jours">[Prolonger]</a>';
														else
															$admin_content = '<span style=color:green>Expire le: Jamais</span>';
												else
													if ($suspended > 0)
														$admin_content = '<span style=color:red>Compte Suspendu</span>';
													else
														$admin_content = '<span style=color:red>Expiré le: '.date("d/m/Y", $data).' à '.date("H:i", $data).' </span> <a class="tooltip" href="index.php?p=droits&t=2&server_id='.$row['id'].'" title="Renouveler votre accès de 31 jours">[Renouveler]</a>';
											}
											else
											{
												$admin_content = '<span style=color:green>Accès géofront</span>';
											}
											
										}
										elseif(strlen($row['admin_prix']) == 0 OR $row['admin_prix'] == 0) $admin_content = "N/A";
										
										/* Fin champ Admin */

										/* Champs Test */
										if ($access_vip == 0 && $timestamp_geo < time() && $row['vip_prix'] > 0)
											if ($suspended > 0)
												$test_content = '<span style=color:red>Compte Suspendu</span>';
											else
												if ($access_test == 0)
													$test_content = '<a class="tooltip" href="index.php?p=droits&t=4&server_id='.$row['id'].'" title="Tester le VIP pendant trois jours">Tester le VIP (3Jours)</a>';
												else
													$test_content = '<span style=color:red>Utilisée</span>';
										elseif ($access_vip == 1 OR ($timestamp_geo > time() && $row['vip_prix'] > 0))
										{
											$vip_req = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='1' AND `ip_serveur`='".$row['server_ip']."' AND `port_serveur`='".$row['server_port']."'");
											$data = mysqli_result($vip_req,0, 'date_fin');
											if ($data < $timestamp_geo && $timestamp_geo > $data)		$data = $timestamp_geo;
											
											if ($data != $timestamp_geo)
											{
												if ($data > time())
													if ($suspended > 0)
														$test_content = '<span style=color:red>Compte Suspendu</span>';
													else
														$test_content = '<span style=color:red>Utilisée</span>';
												else
													if ($suspended > 0)
														$test_content = '<span style=color:red>Compte Suspendu</span>';
													else
														$test_content = '<span style=color:red>Utilisée</span>';
											}
											
										}
									
									/* Fin champ Test */
										
										if (strlen($status) < 5)	$status = "<span style=color:orange>...</span>";
										echo '<td><div class="'.$class.'">'.$vip_content.'</div></td>
										<td><div class="'.$class.'">'.$admin_content.'</div></td>
										<td><div class="'.$class.'">'.$test_content.'</div></td>

									</tr>';

							}

					

					
			echo '		</tbody></div> ';
			
			
			echo '<div class="text">';
			if (TOKEN_GEO > 0){
				if (!$is_geo)		echo '<p><img src="./img/icon/famfamfam/add.png"> <a class="tooltip" href="index.php?p=droits&t=3&server_id=1" title="Un géofront possède un accès ViP et Admin sur tous les serveurs!">[Devenir Géofront pendant 1 mois pour '.TOKEN_GEO.' tokens]</a></p>';
				else								echo '<p>Vous êtes actuellement Géofront jusqu\'au <b>'.date("d/m/Y", $timestamp_geo).'</b><br><a class="tooltip" href="index.php?p=droits&t=3&server_id=1" title="Un géofront possède un accès ViP et Admin sur tous les serveurs!">[Prolonger 1 mois pour '.TOKEN_GEO.' tokens]</a></p>';
			}
			echo '</div>';
			
			echo'		</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->		
			</div><!-- end div .box-out -->
			';
		}
		
	}
?>