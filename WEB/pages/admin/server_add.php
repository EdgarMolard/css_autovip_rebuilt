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
	//################################################################################
	
	if ( isset($_POST['server_add']) && !empty($_POST['server_ip']) && !empty($_POST['server_port']))
	{
	
		$result = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_server` WHERE server_ip='".trim($_POST['server_ip'])."' AND server_port='".trim($_POST['server_port'])."'LIMIT 0,1");
		$count = mysqli_num_rows($result);

			
	}
	
	if (isset($_POST['server_add']) && $count < 1 && !empty($_POST['server_name']) && !empty($_POST['server_ip']) && !empty($_POST['server_rcon'])  && !empty($_POST['server_port']) && !empty($_POST['server_ldr']) && !empty($_POST['server_type']) && is_numeric($_POST['server_port']) && is_numeric($_POST['admin_prix']) && is_numeric($_POST['vip_prix']))
	{
		$requete_sql = "
			INSERT INTO `".SQL_PREFIX."_server` (`id` ,`server_name` ,`server_ip` ,`server_port` ,`vip_prix` ,`admin_prix` ,`server_added_by` ,`server_type` ,`server_manager` ,`rcon_password`)
			VALUES (NULL ,
			'".mysqli_real_escape_string($conn, $_POST['server_name'])."',
			'".mysqli_real_escape_string($conn, trim($_POST['server_ip']))."',
			'".mysqli_real_escape_string($conn, trim($_POST['server_port']))."',
			'".mysqli_real_escape_string($conn, $_POST['vip_prix'])."',
			'".mysqli_real_escape_string($conn, $_POST['admin_prix'])."', 
			'".mysqli_real_escape_string($conn, $_SESSION['af_pseudo'])."',
			'".mysqli_real_escape_string($conn, $_POST['server_type'])."',
			'".mysqli_real_escape_string($conn, $_POST['server_ldr'])."',
			'".mysqli_real_escape_string($conn, $_POST['server_rcon'])."'
			);
		";
		mysqli_query($conn, "
			INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
			VALUES (NULL , '".time()."', 'Ajout de serveur', '".$_SESSION['af_id']."',
			'".mysqli_real_escape_string($conn, $_POST['server_name'])."',
			'".mysqli_real_escape_string($conn, trim($_POST['server_ip'])).":".mysqli_real_escape_string($conn, trim($_POST['server_port']))."', '".$_SERVER["REMOTE_ADDR"]."');
			");
			echo '<meta http-equiv="refresh" content="1; URL=index.php?p=server_gestion">';
			mysqli_query($conn, $requete_sql);
		echo'
			<div class="notification success">
				<div class="messages">Le serveur a été ajouté avec succès, redirection en cours... <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification info -->
		';
	
	}
	else {
		echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Ajout d\'un nouveau serveur</h1></div>
					<div class="box-content">';
					
		if (isset($_POST['server_add']))
		{
			if ($count > 0)
				echo '<div class="notification error">
					<div class="messages">Ce serveur est déjà présent ('.$_POST['server_ip'].':'.$_POST['server_port'].')<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
			else 
				echo '<div class="notification error">
					<div class="messages">Vous n\'avez pas complété correctement le formulaire<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
		}
		echo '
		<div class="form">
				<form METHOD="POST" action="index.php">
							<fieldset>
								<input type="HIDDEN" id="server_add" name="server_add" value="server_add"> 
								
								
								<label for="medium_field">Nom du serveur</label>
								<input type="text" class="text medium" id="server_name" name="server_name" maxlength="64" />
								
								<label for="medium_field">IP du serveur</label>
								<input type="text" class="text medium" maxlength="20" id="server_ip" name="server_ip" />
								
								<label for="medium_field">Port du serveur</label>
								<input type="text" class="text small" id="server_port" name="server_port" maxlength="5" />

								<label for="medium_field">Responsable du Serveur</label>
								<input type="text" class="text small" id="server_ldr"  name="server_ldr" maxlength="128" />
								
								<label for="multi_select">Type du serveur (FFA, BaJail, Minigames, Deathrun etc)</label>
								<input type="text" class="text medium" id="server_type" name="server_type" maxlength="128" />
								
								<label for="multi_select">RCON du serveur</label>
								<input type="password" class="text medium" id="server_rcon" name="server_rcon" maxlength="128" />
								
								<label for="select_field">Prix accès VIP</label>
								
								<select name="vip_prix" id="vip_prix" class="text">
									<option value="0">Désactivé (pas d\'accès admin sur ce serveur)</option>
									<option value="1">1 Token</option>
									<option value="2">2 Tokens</option>
									<option value="3">3 Tokens</option>
									<option value="4">4 Tokens</option>
									<option value="5">5 Tokens</option>
									<option value="6">6 Tokens</option>
									<option value="7">7 Tokens</option>
									<option value="8">8 Tokens</option>
									<option value="9">9 Tokens</option>
									<option value="10">10 Tokens</option>
								</select>

								 <label for="select_field">Prix accès Admin</label>

								<select name="admin_prix" id="admin_prix" class="text">
									<option value="0">Désactivé (pas d\'accès admin sur ce serveur)</option>
									<option value="1">1 Token</option>
									<option value="2">2 Tokens</option>
									<option value="3">3 Tokens</option>
									<option value="4">4 Tokens</option>
									<option value="5">5 Tokens</option>
									<option value="6">6 Tokens</option>
									<option value="7">7 Tokens</option>
									<option value="8">8 Tokens</option>
									<option value="9">9 Tokens</option>
									<option value="10">10 Tokens</option>
								</select>
								
								
								<br><br>
								<input type="submit" value="Ajouter ce serveur" class="submit" />  

							</fieldset>
						</form>
					</div><!-- end div .form -->
			';
			/* Re-remplir le formulaire si il y'a eu une erreur */
			
			if (isset($_POST['server_add'])) 
			{
				echo "<script>" . "\n";
				if (!empty($_POST['server_name']))
					echo "document.getElementById('server_name').value ='".strip_tags(addslashes($_POST['server_name']))."';" . "\n";
					
				if (!empty($_POST['server_ip']))
					echo "document.getElementById('server_ip').value ='".strip_tags(addslashes($_POST['server_ip']))."';" . "\n";
					
				if (!empty($_POST['server_port']) && is_numeric($_POST['server_port']))
					echo "document.getElementById('server_port').value ='".strip_tags(addslashes($_POST['server_port']))."';" . "\n";
					
				if (!empty($_POST['server_ldr']))
					echo "document.getElementById('server_ldr').value ='".strip_tags(addslashes($_POST['server_ldr']))."';" . "\n";
					
				if (!empty($_POST['server_type']))
					echo "document.getElementById('server_type').value ='".strip_tags(addslashes($_POST['server_type']))."';" . "\n";
					
					
					
					
				if ($_POST['vip_prix'] > 0 && is_numeric($_POST['vip_prix']))
					echo 'document.getElementById("vip_prix").selectedIndex='.$_POST['vip_prix'].';' . "\n";
				if ($_POST['admin_prix'] > 0 && is_numeric($_POST['admin_prix']))
					echo 'document.getElementById("admin_prix").selectedIndex='.$_POST['admin_prix'].';' . "\n";
					
				echo "</script>" . "\n";
			}
			echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
	}
	
?>