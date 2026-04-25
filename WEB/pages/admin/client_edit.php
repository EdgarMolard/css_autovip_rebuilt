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

	
	//elseif ((GetInfo($_SESSION['af_id'], 'admin_level') < LVL_GESTION_SITEADMINS || GetInfo($_SESSION['af_id'], 'admin_level') < LVL_GESTION_TOKEN) && ROOT_SITE != $_SESSION['af_steam_id'])
	elseif (GetInfo($_SESSION['af_id'], 'admin_level') < LVL_GESTION_TOKEN && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'avez pas le niveau d\'administration suffisant ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	elseif ( (ROOT_SITE == GetInfo($_GET['id'], 'steam_id') OR ROOT_SITE == GetInfo($_POST['client_edit'], 'steam_id')) && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas autorisé à editer le compte ROOT! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	elseif (GetInfo($_GET['id'], 'admin_level') > $_SESSION['af_admin_level'] && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas autorisé à editer cet administrateur! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	//################################################################################
	else
	{
		
		if (strlen(GetInfo($_GET['id'], 'username')) > 2)					{$id_user = $_GET['id']			;$username = GetInfo($_GET['id'], 'username');				}
		if (strlen(GetInfo($_POST['client_edit'], 'username')) > 2)			{$id_user = $_POST['client_edit'];$username = GetInfo($_POST['client_edit'], 'username');	}

		if (strlen($username) > 2 && is_numeric($id_user) && $id_user > 0)
		{
		
			/* Mot de passe */
			if ( isset($_POST['mot_de_passe']) && isset($_POST['pass_change']) && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_SITEADMINS))
			{
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `password` = '".mysqli_real_escape_string($conn, md5($_POST['pass_change']))."' WHERE `id` = '".$id_user."';");
				echo'
					<div class="notification success">
						<div class="messages">Le mot de passe de l\'utilisateur a été changé<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
					'Mot de passe', '".mysqli_real_escape_string($conn, $username)."',
					'".$_SERVER["REMOTE_ADDR"]."'
					);
				");
			}
			
			/* Steam Id */
			if ( isset($_POST['steam_id']) && isset($_POST['steam_id_change']) && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_SITEADMINS))
			{
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `steam_id` = '".mysqli_real_escape_string($conn, $_POST['steam_id_change'])."' WHERE `id` = '".$id_user."';");
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `steam_id` = '".mysqli_real_escape_string($conn, $_POST['steam_id_change'])."' WHERE `user_id` = '".$id_user."';");
				echo'
					<div class="notification success">
						<div class="messages">Le steam id de l\'utilisateur a été changé<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
					'Mot de passe', '".mysqli_real_escape_string($conn, $username)."',
					'".$_SERVER["REMOTE_ADDR"]."'
					);
				");
			}
			
			/* Level Admin */
			
			if ( isset($_POST['admin_level_button']) && $_POST['admin_level'] < $_SESSION['af_admin_level']  && ($_SESSION['af_admin_level'] >= LVL_GESTION_SITEADMINS OR ROOT_SITE == $_SESSION['af_steam_id']) && $_POST['admin_level'] >= 0 && $_POST['admin_level'] <= 5 && is_numeric($_POST['admin_level']))
			{
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `admin_level` = '".$_POST['admin_level']."' WHERE `id` = '".$id_user."';");
				echo'
					<div class="notification success">
						<div class="messages">Le niveau d\'administration de l\'utilisateur a été modifié<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
					'Admin Level (=> ".$_POST['admin_level'].")', '".mysqli_real_escape_string($conn, $username)."',
					'".$_SERVER["REMOTE_ADDR"]."'
					);
				");
			}
			else
			{
				if ($_SESSION['af_admin_level'] <= $_POST['admin_level'])
					echo '
							<div class="notification error">
								<div class="messages">Vous ne pouvez pas modifier un niveau d\'administrateur égal ou supérieur au votre <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							</div><!-- end div .notification error -->';
			}
			//
			/* Changement de Mail */
			if ( isset($_POST['email_edit']) && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_SITEADMINS))
			{
				$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_users` WHERE `id`='".$id_user."' LIMIT 1");
				while($row = mysqli_fetch_array($requete))
				{
					$mail = $row['mail'];
				}
				$requete = mysqli_query($conn, "SELECT mail FROM `".SQL_PREFIX."_users` WHERE `mail`='".mysqli_real_escape_string($conn, $_POST['email_mod'])."' LIMIT 0,1");
				$result = mysqli_num_rows($requete);
				if($_POST['email_mod'] == $mail)
				{
					echo'
						<div class="notification error">
							<div class="messages">L\'adresse e-mail est identique à l\'ancienne<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
				}
				elseif ($result!== false && $result > 0)
				{
					echo'
						<div class="notification error">
							<div class="messages">L\'adresse e-mail est déjà utilisée<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
				}
				elseif (strlen($_POST['email_mod']) == 0) 
				{
					echo'
						<div class="notification error">
							<div class="messages">Vous n\'avez pas indiqué d\'adresse e-mail<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
				}
				elseif (strlen($_POST['email_mod']) > 128)
				{
					echo'
						<div class="notification error">
							<div class="messages">L\'adresse e-mail est trop longue<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
				}
				elseif(!filter_var($_POST['email_mod'], FILTER_VALIDATE_EMAIL))
				{
					echo'
						<div class="notification error">
							<div class="messages">L\'adresse e-mail n\'est pas correcte<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
				}
				else
				{
					mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `mail` = '".mysqli_real_escape_string($conn, $_POST['email_mod'])."' WHERE `id` = '".$id_user."';");
					echo'
						<div class="notification success">
							<div class="messages">Le changement d\'adresse e-mail a bien été effectué<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
					mysqli_query($conn, "
						INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
						VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
						'Email Changé (".mysqli_real_escape_string($conn, $_POST['email_mod']).")', '".mysqli_real_escape_string($conn, $username)."',
						'".$_SERVER["REMOTE_ADDR"]."'
						);
					");
				}
			}
			//
			//
			/* Retrait de token */
			
			if ( isset($_POST['retrait_token']) && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_TOKEN) && $_POST['token_mod'] > 0 && is_numeric($_POST['token_mod']))
			{
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token - ".mysqli_real_escape_string($conn, $_POST['token_mod'])." WHERE `id` = '".$id_user."';");
				echo'
					<div class="notification success">
						<div class="messages">Le retrait de token a bien été effectué<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
					'Tokens (- ".mysqli_real_escape_string($conn, $_POST['token_mod']).")', '".mysqli_real_escape_string($conn, $username)."',
					'".$_SERVER["REMOTE_ADDR"]."'
					);
				");
			}
			/* Ajout de token */
			
			if ( isset($_POST['ajout_token']) && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_TOKEN) && $_POST['token_mod'] > 0 && is_numeric($_POST['token_mod']))
			{
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token + ".mysqli_real_escape_string($conn, $_POST['token_mod'])." WHERE `id` = '".$id_user."';");
				echo'
					<div class="notification success">
						<div class="messages">L\'ajout de token a bien été effectué<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
					'Tokens (+ ".mysqli_real_escape_string($conn, $_POST['token_mod']).")', '".mysqli_real_escape_string($conn, $username)."',
					'".$_SERVER["REMOTE_ADDR"]."'
					);
				");
			}
			//
			/* Ajout de jours */
			
			if ( isset($_POST['ajout_vip']) && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_TOKEN) && $_POST['vip_mod'] > 0 && is_numeric($_POST['vip_mod']))
			{
				$query = "SELECT user_id FROM af_droits WHERE user_id = '".$id_user."'";
				$result = mysqli_query($conn, $query);
				$add_time = $_POST['vip_mod'] * 86400;
				$vip_req = mysqli_query($conn, "SELECT id, date_start, date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='1'");
				$data = mysqli_result($vip_req,0, 'date_fin');
				$expiration = ($data + $add_time);
					
				if(mysqli_num_rows($result) > 0)
				{
					if ($data > time())
						mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = ".mysqli_real_escape_string($conn, $expiration)." WHERE `user_id` = '".$id_user."' AND `type_droit`='1';");
					else
					{
						$expiration = time() + $add_time;
						mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = ".mysqli_real_escape_string($conn, $expiration)." WHERE `user_id` = '".$id_user."' AND `type_droit`='1';");
					}
					
					echo'
					<div class="notification success">
						<div class="messages">L\'ajout de journées VIP a bien été effectué<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
					'Jours VIP (+ ".mysqli_real_escape_string($conn, $_POST['vip_mod']).")', '".mysqli_real_escape_string($conn, $username)."',
					'".$_SERVER["REMOTE_ADDR"]."'
					);
				");
				}
				else
				{
					echo'
						<div class="notification error">
							<div class="messages">L\'utilisateur n\'a pas encore acheté d\'accès VIP<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
				}				
			}
			
			/* Retrait de jours */
			
			if ( isset($_POST['retrait_vip']) && (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_TOKEN) && $_POST['vip_mod'] > 0 && is_numeric($_POST['vip_mod']))
			{
				$query = "SELECT user_id FROM af_droits WHERE user_id = '".$id_user."'";
				$result = mysqli_query($conn, $query);
				$add_time = $_POST['vip_mod'] * 86400;
				$vip_req = mysqli_query($conn, "SELECT id, date_start, date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='1'");
				$data = mysqli_result($vip_req,0, 'date_fin');
				$expiration = ($data - $add_time);	
				
				if(mysqli_num_rows($result) > 0)
				{						
					mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = ".mysqli_real_escape_string($conn, $expiration)." WHERE `user_id` = '".$id_user."' AND `type_droit`='1';");
					echo'
						<div class="notification success">
							<div class="messages">Le retrait de journées VIP a bien été effectué<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
					mysqli_query($conn, "
						INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre`, `detail`, `detail2`, `ip`)
						VALUES (NULL , '".time()."', 'Edition Membre', '".$_SESSION['af_id']."',
						'Jours VIP (- ".mysqli_real_escape_string($conn, $_POST['vip_mod']).")', '".mysqli_real_escape_string($conn, $username)."',
						'".$_SERVER["REMOTE_ADDR"]."'
						);
					");
				}
				else
				{
					echo'
						<div class="notification success">
							<div class="messages">L\'utilisateur n\'a pas encore acheté d\'accès VIP<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification info -->
					';
				}					
			}
			
			$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_users` WHERE `id`='".$id_user."' LIMIT 1");
			while($row = mysqli_fetch_array($requete))
			{
				$mail = 		$row['mail']			;	$date_register = 	$row['date_register']		;	$ip_register = $row['ip_register'];
				$lastseen =	 	$row['lastseen']		;	$lastseen_ip = 		$row['lastseen_ip']			;	$token		= $row['token'];
				$is_suspended =	$row['is_suspended']	;	$steam_id =			$row['steam_id'];			;	$admin_level = $row['admin_level'];
				$mini_token =	$row['mini_token']		;
				if (ROOT_SITE == $steam_id)
					$admin_level = 5;
			}
			
			$access_vip = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='1'"));
			
			if ($is_suspended == "0")	$is_suspended = "<span style='color:green'>ACTIF</span>";
			if ($is_suspended == "1")	$is_suspended = "<span style='color:red'>SUSPENDU</span>";
			if ($access_vip == 1)
			{
				$vip_req = mysqli_query($conn, "SELECT date_fin FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$id_user."' AND `type_droit`='1'");
				$is_vip = mysqli_result($vip_req,0, 'date_fin');
				
				if ($is_vip > time())
				{
					if ($is_vip < 2147483647)
						$is_vip = '<span style="color:green">'.date("d/m/Y", $is_vip).' à '.date("H:i", $is_vip).'</span>';
					else
						$is_vip = "<span style='color:green'>LifeTime</span>";
				}
				else $is_vip = "<span style='color:red'>Expiré</span>";
			}
			else $is_vip = "<span style='color:red'>Non</span>";
			
			echo '
				<div class="box-out">
					<div class="box-in">
						<div class="box-head"><h1>Gestion du compte \''.$username.'\'</h1></div>
						<div class="box-content">';
			echo '
			<div class="text">
				<h3>Informations du compte</h3>
					<p>
						<p>Pseudonyme:<b> <a href="https://steamcommunity.com/profiles/'.steam2friend($steam_id).'" target="_blank">'.$username.'</a><br></b>
						E-mail : <b>'.$mail.'</b><br>
						Steam ID :<b> '.$steam_id.'</b><br><br>
						
						Statut :<b> '.$is_suspended.'</b><br><br>
						VIP : <b> '.$is_vip.'</b><br><br>
						
						Tokens : <b>'.$token.'</b>
						
						<p>Inscription: Le <b>'.date("d/m/Y à H:i:s", $date_register).'</b> avec l\'ip <b>'.$ip_register.'</b><i> ('.gethostbyaddr($ip_register).')</i></b><br>
						Dernière connexion:  Le <b>'.date("d/m/Y à H:i:s", $lastseen).'</b> avec l\'ip <b>'.$lastseen_ip.' </b><i>('.gethostbyaddr($lastseen_ip).')</b></i></p>
					</p>
				<hr>
				<h3>Actions</h3>
				
				
			</div>
			<div class="form">
					<form METHOD="POST" name ="client_edit" action="index.php">
								<fieldset>
									<input type="HIDDEN" id="client_edit" name="client_edit" value="'.$id_user.'">  
									';
			//LVL_GESTION_ADMIN
			if (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_SITEADMINS)
				echo '								
						<label for="medium_field">Changer d\'adresse e-mail</label>
							<input type="text" class="text small" name="email_mod" maxlength="128" value=""/>
							<p>
						<input type="submit" name="email_edit" id="email_edit" value="Modifier" class="submit" />  
						<br><br>					
						<label for="medium_field">Mot de passe</label>
							<input type="text" class="text small" name="pass_change" maxlength="128" value=""/>
							<p>
						<input type="submit" name="mot_de_passe" id="mot_de_passe" value="Modifier" class="submit" />   
						<br><br>
						<label for="medium_field">Steam Id</label>
							<input type="text" class="text small" name="steam_id_change" maxlength="128" value=""/>
							<p>
						<input type="submit" name="steam_id" id="steam_id" value="Modifier" class="submit" />   
						<br><br>
						';
			if (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_SITEADMINS)
				echo '
										<label for="select_field">Niveau d\'accès admin (site)</label>
											<select name="admin_level" id="admin_level" class="text">
											<option value="0">Aucun accès</option>
											<option value="1">Administrateur Niveau 1</option>
											<option value="2">Administrateur Niveau 2</option>
											<option value="3">Administrateur Niveau 3</option>
											<option value="4">Administrateur Niveau 4</option>
											<option value="5">Administrateur Niveau 5</option>
										</select>
										<p>
										<input type="submit" name="admin_level_button" id="admin_level_button" value="Modifier" class="submit" />  
		
										<br><br>

					';
			if (ROOT_SITE == $_SESSION['af_steam_id'] OR $_SESSION['af_admin_level'] >= LVL_GESTION_TOKEN)
			{
				echo '								
						<label for="medium_field">Ajouter / Retirer des tokens</label>
							<input type="text" class="text small" name="token_mod" maxlength="128" value="0"/>
							<p>
						<input type="submit" name="ajout_token" id="ajout_token" value="Ajouter" class="submit" />  
						<input type="submit" name="retrait_token" id="retrait_token" value="Retirer" class="submit" />  
						<br><br>
						';
				
				echo '								
						<label for="medium_field">Ajouter / Retirer des jours de VIP</label>
							<input type="text" class="text small" name="vip_mod" maxlength="128" value="0"/>
							<p>
						<input type="submit" name="ajout_vip" id="ajout_vip" value="Ajouter" class="submit" />  
						<input type="submit" name="retrait_vip" id="retrait_vip" value="Retirer" class="submit" />  
						<br><br>
						';
									
				echo '
									
									</fieldset>
								</form>
							</div><!-- end div .form -->
					';
			}
				if ($admin_level > 0 && $admin_level <= 5 && is_numeric($admin_level))
					echo '<script>document.getElementById("admin_level").selectedIndex='.$admin_level.';' . "</script>\n";
				echo '
						</div><!-- end div .box-content -->
					</div><!-- end div .box-in -->
				</div><!-- end div .box-out -->
				';
		}
	}
	
?>