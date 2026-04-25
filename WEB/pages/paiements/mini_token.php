<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	if (RENTABILIWEB_PAIEMENT)
	{
		echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>MiniTokens </h1></div>
					<div class="box-content">
						<div class="text">
			';
		if (!isset($_SESSION['af_id']))
			echo '
					<div class="notification error">
						<div class="messages">Vous n\'êtes pas identifié! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification error -->';
		else
		{
			/*##############################################*/
			/*					code ici 					*/
			if (!is_numeric(MINITOKEN_CONVERSION)) 		define("MINITOKEN_CONVERSION", 100);
			$explication = "
			<h3>Les MiniTokens c'est quoi?</h3>
				<p>
					Les MiniTokens vous permettent d'obtenir des Tokens <b>GRATUITEMENT</b> (si vous prenez des offres gratuites), en regardant des publicités, ou en complétant des formulaires par exemple.<br>
					Une fois vos MiniTokens obtenus, vous pouvez les convertir en Token (<b>".MINITOKEN_CONVERSION."</b> MiniTokens = <b>1</b> Token)<br><br>
					Attention, la créditation des MiniTokens n'est pas directe, il est possible que vous ayez 15 minutes, 30 minutes, ou 1 heure d'attente (selon l'offre sélectionnée)<br><br>
					<i>(Si vous utilisez un script qui bloque les publicité (Exemple: AdBlocks), vous devez le désactiver)
				</p>
			</div><!-- end div .text -->
			";
			$user_mini_token = GetInfo($_SESSION['af_id'], 'mini_token');
			if (isset($_POST['mini_token']))
			{
				if ($user_mini_token >= MINITOKEN_CONVERSION)
				{
					$new_token = ($user_mini_token / MINITOKEN_CONVERSION);			$floor = floor($new_token); 		$mt_a_delete = ($floor * MINITOKEN_CONVERSION);					
					$user_mini_token = ($user_mini_token - $mt_a_delete);
					mysqli_query($conn,  "UPDATE `".SQL_PREFIX."_users` SET `mini_token` = mini_token - $mt_a_delete WHERE `id`='".$_SESSION['af_id']."'");
					mysqli_query($conn,  "UPDATE `".SQL_PREFIX."_users` SET `token` = token + $floor WHERE `id`='".$_SESSION['af_id']."'");
					mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`)
					VALUES (NULL , '".time()."', 'Conversion MiniTokens', '".$_SESSION['af_id']."','".$mt_a_delete."' , '".$floor."');
					");
					echo '
						<div class="notification success">
							<div class="messages">Conversion réussie! Vous allez être redirigé automatiquement... <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification success -->';
					echo '<meta http-equiv="refresh" content="2; URL=index.php?p=mini_token">';
				}
				else
					echo '
						<div class="notification error">
							<div class="messages">Vous n\'avez pas assez de MiniTokens! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
				
			}
			if ($user_mini_token >= MINITOKEN_CONVERSION)
			{
				$new_token = ($user_mini_token / MINITOKEN_CONVERSION);			$floor = floor($new_token); 		$mt_a_delete = ($floor * MINITOKEN_CONVERSION);					
				if ($mt_a_delete > 0 && $floor > 0)
					echo '
								<div class="form">
									<form action="index.php" method=post>
										<fieldset>
											<input type=hidden name=mini_token value=mini_token>
											<input type=submit  class="submit" value="Convertir '.$mt_a_delete.' MiniTokens pour obtenir '.$floor.' Token(s)!">
										</fieldset>
									</form>
								</div>
					';
				
			}
			echo $explication . '<hr>';
			
			echo '<center>
					<iframe src="https://payment.rentabiliweb.com/form/vc/?docId='.RENTABILIWEB_DOCID.'&siteId='.RENTABILIWEB_SITEID.'&cnIso=geoip&uid='.$_SESSION['af_id'].'&catId=5" width="700" height="1300" frameborder="0" scrolling="auto"></iframe>
				<center>
				';
			
			
			
			
			
			
			
			/*##############################################*/
			echo '
							
						</div><!-- end div .box-content -->
					</div><!-- end div .box-in -->
				</div><!-- end div .box-out -->
			';
			
			
		}
	}
		
		else
		{
			echo '
					<div class="notification error">
						<div class="messages">Le webmaster ne souhaite pas proposer ce paiement <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification error -->';
		}
?>