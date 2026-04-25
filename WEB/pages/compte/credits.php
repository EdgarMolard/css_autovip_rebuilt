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
		//##################################################################################### 
		//############ ACHAT DE DROITS
		$client_token = GetInfo($_SESSION['af_id'], 'token');
		
		if($client_token == 0)
			echo '
			<div class="notification warning">
				<div class="messages">Vous n\'avez aucun Token, cliquez ici pour y remédier : <a href="./index.php?p=token">Créditation Tokens</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification info -->';

		if ($_GET['t'] == 1)
		{		
			$type = $_GET['t'];
			$client_token = GetInfo($_SESSION['af_id'], 'token');
			//Récupération du prix de l'offre
			if ($type == 1)	{
				$droit_name = "Crédits";
				$champs = "prix";
				
				$prix_req = mysqli_query($conn, "SELECT `".$champs."` FROM `".SQL_PREFIX."_credits` WHERE `montant`= '".$_GET['amount']."' LIMIT 0,1");
				$prix = mysqli_result($prix_req,0, $champs);
				if (!$prix OR $prix < 1 OR !is_numeric($prix))	$prix = 0;
			}
				
				
			// L'offre est désactivée
			if ($prix < 1)
				echo '
						<div class="notification error">
							<div class="messages">Vous ne pouvez pas accédez à cette offre, contactez le webmaster <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
						
						
			// L'utilisateur n'a pas assez de token
			elseif ($client_token < $prix && $type == 1)
				echo '
						<div class="notification error">
							<div class="messages">Vous n\'avez pas assez de Tokens (<a href="index.php?p=token">Créditez votre compte</a>) <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
			// Toutes les conditions semblent bonnes, on active ses droits =)
			elseif ($type == 1)
			{
				//On verifie si l'utilisateur prolonge, ou non
				$toks = explode(":", $_SESSION['af_steam_id']); 
				$odd = (int) $toks[1]; 
				$halfAID = (int) $toks[2];				
				$auth = ($halfAID * 2) + $odd;
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token - $prix WHERE `id`='".$_SESSION['af_id']."'");				
				
				$query = "SELECT auth FROM store_users WHERE `auth` = '".$auth."'";
				$result = mysqli_query($conn, $query);
				
				$credits = $_GET['amount'];
					
				if ($prix == 3) $credits = $credits + 1000;
				
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Achat', '".$_SESSION['af_id']."','".$credits." ".$droit_name."' , 'MiniGames', '".$_SERVER["REMOTE_ADDR"]."');
				");
				
				if(mysqli_num_rows($result) > 0)
				{					
					$user_credits = mysqli_query($conn, "SELECT `credits` FROM `store_users` WHERE `auth`= '".$auth."' LIMIT 0,1");
					$credits = mysqli_result($user_credits,0, 'credits');
					$credits = $credits + $_GET['amount'];
					
					if ($prix == 3) $credits = $credits + 1000;
					
					mysqli_query($conn, "UPDATE `store_users` SET `credits` = '".$credits."' WHERE `auth`='".$auth."'");						
				}
				else
				{
					$credits = $_GET['amount'];
					
					if ($prix == 3) $credits = $credits + 1000;
					
					mysqli_query($conn, "
						INSERT INTO `store_users` (`id` ,`auth` ,`name` ,`credits`)
						VALUES (NULL ,'".$auth."','".$_SESSION['af_pseudo']."','".$credits."');
					");
				}
				
				echo '<meta http-equiv="refresh" content="1; URL=index.php?p=credits&c=ok">';
			}
		
		}
		//############ ACHAT DE DROITS
		//##################################################################################### 
		
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Achat de Crédits (Store LastFate)</h1></div>				
				<div class="box-content">';
				if ($_GET['c'] == "ok")
				{
					echo'
							<div class="notification success">
								<div class="messages">Vous venez d\'obtenir vos crédits !<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
							</div><!-- end div .notification info -->
						';
				}
				echo '
				<div class="table">
					<table>
						<thead>
							<tr>
								<td><div>Montant</div></td>
								<td><div>Prix</div></td>
							</tr>
						</thead>
						<tbody>';
						$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_credits` ORDER BY prix");
						$class = "odd";
						while($row = mysqli_fetch_array($requete))
						{
							if ($class == "odd")	$class = "even";
							else 	$class = "odd";
									
							if (strlen($row['prix']) == 0 OR $row['prix'] == 0)	$row['prix'] = "N/A";
							//$row['']
								if ($row['prix'] == 3) 
								{
									$promo = '(+1000) ';
								}
								else $promo = '';
								
								echo '
								<tr>
									
									<td><div class="'.$class.'"><a class="tooltip" title="Directement disponibles après l\'achat">'.$row['montant'].' '.$promo.'Crédits</a></div></td>';
										
									/* Champs credits */
									
									$montant = $row['montant'];
										
									if ($row['prix'] > 1) $plural = 's';
									else $plural = '';
										
									/* Fin champ credits */
										
									echo "<td><div class=\"".$class."\"><a href=\"?p=credits&t=1&amount=$montant\">".$row['prix']." Token".$plural."</a></div></td>";
								echo "</tr>";

						}

					

					
			echo '		</tbody></div> ';
			
			
			echo'		</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->		
			</div><!-- end div .box-out -->
			';
		}
?>