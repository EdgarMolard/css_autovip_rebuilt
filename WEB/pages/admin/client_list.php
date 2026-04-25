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
	//################################################################################
	else
	{
		echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Gestion des comptes</h1></div>
					<div class="box-content">';
		echo '
		<div class="form">
				<form METHOD="POST" name ="client_search" action="index.php">
							<fieldset>
								<input type="HIDDEN" id="client_search" name="client_search" value="client_search"> 
								<label for="medium_field">Contenu à rechercher</label>
								<input type="text" class="text small" id="search" name="search" maxlength="128" />
								
								<input type="submit" name="preview" id="preview" value="Rechercher" class="submit" />  

							</fieldset>
						</form>
					</div><!-- end div .form -->
			';
			
			echo '
					</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->
			</div><!-- end div .box-out -->
			';
			if ($_POST['client_search'] && !empty($_POST['search']))
			{
				echo '
					<div class="box-out">
						<div class="box-in">
							<div class="box-head"><h1>Résultat de la recherche</h1></div>
							<div class="box-content">
							<div class="table">';
				/*
						Recherche
				*/
				$champs = mysqli_real_escape_string($conn, $_POST['search']);
				$requete = "
					SELECT id FROM `".SQL_PREFIX."_users` WHERE
					steam_id LIKE '%".$champs."%' OR username LIKE '%".$champs."%' OR mail LIKE '%".$champs."%'
				";
				// WHERE `steam_id` LIKE '%STEAM_0:0:6232422%'
				$resultats = mysqli_num_rows(mysqli_query($conn, $requete));
				if ($resultats > LIMITE_RESULTATS_RECHERCHE)
					echo '
							<div class="notification error">
								<div class="messages">Trop de résultats trouvés ('.$resultats.')! Le webmaster a limité à '.LIMITE_RESULTATS_RECHERCHE.' résultats <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							</div><!-- end div .notification error -->';
				elseif ($resultats < 1)
					echo '
							<div class="notification error">
								<div class="messages">Aucun résultat n\'a été trouvé pour \''.htmlentities($champs, ENT_QUOTES).'\'<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							</div><!-- end div .notification error -->';
				else
				{
				
					// echo '
						// <div class="notification info">
								// <div class="messages">'.$resultats.' résultat(s) trouvé(s) pour \''.htmlentities($champs, ENT_QUOTES).'\' <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							// </div><!-- end div .notification info -->
					// ';
					
					echo'
    				<table>
    					<thead>
    						<tr>
    							<td><div>Pseudonyme</div></td>
   								<td><div>E-mail</div></td>
								<td><div>Steam ID</div></td>
								<td><div>Tokens</div></td>
								<td><div>Actions</div></td>
   							</tr>
   						</thead>
   						<tbody>
					';
					$class = "odd";
					$requete = mysqli_query($conn, "
						 SELECT id, username, token, is_suspended, steam_id, mail FROM `".SQL_PREFIX."_users` WHERE
						 steam_id LIKE '%".$champs."%' OR username LIKE '%".$champs."%' OR mail LIKE '%".$champs."%'
					 ");

					while($row = mysqli_fetch_array($requete))
					{
						if ($class == "odd")	$class = "even";
						else 	$class = "odd";

						echo '
											<tr>
												<td><div class="'.$class.'">'.RedThis($row['username'], $champs).'</div></td>
												<td><div class="'.$class.'">'.RedThis($row['mail'], $champs).' </div></td>
												<td><div class="'.$class.'">'.RedThis($row['steam_id'], $champs).'</div></td>
												<td><div class="'.$class.'">'.$row['token'].'</div></td>
												<td><div class="'.$class.'"><center>';

						if ($_SESSION['af_admin_level'] >= LVL_GESTION_SITEADMINS || $_SESSION['af_admin_level'] >= LVL_GESTION_TOKEN)
							echo'				 <a class="tooltip" href="index.php?p=client_edit&id='.$row['id'].'" title="Editer ce membre"><img src="./img/icon/famfamfam/note_edit.png" alt="" /></a>';
							
						if ($_SESSION['af_admin_level'] >= LVL_GESTION_ADMIN)
						{
							if ($row['is_suspended'] > 0)
								echo '					<a class="tooltip" href="index.php?p=client_suspend&id='.$row['id'].'" title="Retirer la suspension"><img src="./img/icon/famfamfam/user_green.png" alt="" /></a>';
							else
								echo '					<a class="tooltip" href="index.php?p=client_suspend&id='.$row['id'].'" title="Suspendre ce membre"><img src="./img/icon/famfamfam/user_red.png" alt="" /></a>';
						}	
						if ($_SESSION['af_admin_level'] >= LVL_GESTION_LOGS)

							echo '					<a class="tooltip" href="index.php?p=historique_admin&id='.$row['id'].'" title="Voir l\'historique du membre"><img src="./img/icon/famfamfam/keyboard_magnify.png" alt="" /></a>';
					
							
						echo '						</center></td>
											</tr>';
					}
					
					echo '
								</tbody>
							</table>
						</div>
						<div class="text">
					';
				}
				
				
				echo '</div><!-- end div .text -->
					
						</div><!-- end div .box-content -->
					</div><!-- end div .box-in -->
				</div><!-- end div .box-out -->
				';
			}
		
	}
	
?>