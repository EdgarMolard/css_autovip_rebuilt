<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
		
		
		
										// <label for="medium_field">Mot de passe actuel</label>
										// <input type="password" class="text medium" id="current_pw" name="current_pw" maxlength="128" />
										
										// <label for="medium_field">Nouveau mot de passe</label>
										// <input type="password" class="text medium" id="new_pw" name="new_pw" maxlength="128" />
										
										// <label for="medium_field">Retaper le nouveau mot de passe</label>
										// <input type="password" class="text medium" id="new_pw2" name="new_pw2" maxlength="128" />
	if (!isset($_SESSION['af_id']))
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	else
	{

	
		if (GetInfo($_SESSION['af_id'], 'is_suspended') > 0)
			$status = "
			<p>Etat de votre compte: <b> <span style=color:red><u>SUSPENDU</b></u></span></p>
			";
		else
			$status = "<p>Etat de votre compte: <b> <span style=color:green>ACTIF</span> </b> </p>";
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Aperçu de votre compte</h1></div>
				<div class="box-content">
					<div class="text">
						<h1>'.$_SESSION['af_pseudo'].'</h1>
						'.$status.' 
						
						<p>Votre adresse e-mail: <b> '.GetInfo($_SESSION['af_id'], 'mail').' </b><br>
						Votre SteamID:<b> '.GetInfo($_SESSION['af_id'], 'steam_id').' </b><br>
						Votre Profil Steam:<b> <a href="https://steamcommunity.com/profiles/'.steam2friend(GetInfo($_SESSION['af_id'], 'steam_id')).'" target="_blank" style="text-decoration:none">'.$_SESSION['af_pseudo'].'</a></b></p>';
						
				  echo '
					</div><!-- end div .text -->
				</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
		
		//
		
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Changement de mot de passe</h1></div>
				<div class="box-content">
					<div class="form">';
					
		/* Changement de mdp */
		if ($_POST['new_pw'])
		{
			if (!$_POST['new_pw']  OR !$_POST['new_pw2'])
			{
				echo '
					<div class="notification error">
						<div class="messages">Vous n\'avez pas remplis complètement le formulaire<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification error -->';
			}
			elseif ($_POST['new_pw'] != $_POST['new_pw2'])
			{
				echo '
					<div class="notification error">
						<div class="messages">Les mots de passes ne sont pas identiques<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification error -->';
			}
			elseif (strlen($_POST['new_pw']) < 5)
			{
				echo '
					<div class="notification error">
						<div class="messages">Votre mot de passe est trop court (6 caractères minimum)<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification error -->';
			}
			else
			{
				$sql = mysqli_num_rows(mysqli_query($conn, "SELECT username FROM `".SQL_PREFIX."_users` WHERE id='".$_SESSION['af_id']."' LIMIT 1"));

				if ($sql)
				{
					mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET password='".mysqli_real_escape_string(md5($_POST['new_pw']))."' WHERE `id` ='".$_SESSION['af_id']."';");
					
					echo '
						<div class="notification success">
							<div class="messages">Votre mot de passe a correctement été changé<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification success -->';
				}
			}
		}	
					
		echo '					<form METHOD="POST" name ="new_pw" action="index.php">
										<fieldset>
											<input type="HIDDEN" id="new_pw" name="new_pw" value="new_pw"> 
										
											
											<label for="medium_field">Nouveau mot de passe</label>
											<input type="password" class="text medium" id="new_pw" name="new_pw" maxlength="128" />
											
											<label for="medium_field">Retaper le nouveau mot de passe</label>
											<input type="password" class="text medium" id="new_pw2" name="new_pw2" maxlength="128" />
											

											<br><br>
											<input type="submit" value="Changer votre mot de passe" class="submit" />  

										</fieldset>
									</form>
					</div><!-- end div .text -->
				</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
	}
?>