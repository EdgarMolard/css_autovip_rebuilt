<?php

	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	
	echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Créditation Allopass </h1></div>
				<div class="box-content">
					<div class="text">
		';
	//##############################################################################################
	// Formulaire button 
		$allopass_info = '
			<p>Ce rechargement vous créditera de <b> 1 token </b></p>
			<p>En cas de problèmes avec les codes, nous vous conseillons de les noter sur un bout de papier.<br>
			Si vous rencontrez un problème, merci de contacter un administrateur depuis notre Forum.<br></p>

			<p><b>Si vous êtes mineur, vous devez avoir l\'autorisation de vos parents ou de votre tuteur légal!</b></p>
			<p><b>Les tokens ne peuvent pas être remboursés !</b></p><br>
			<center>
			<!-- Begin Allopass Checkout-Button Code -->
			<script type="text/javascript" src="https://payment.allopass.com/buy/checkout.apu?ids='.ALLOPASS_IDS.'&idd='.ALLOPASS_IDD.'&data='.$_SESSION['af_id'].'&lang=fr"></script>
			<noscript>
			 <a href="https://payment.allopass.com/buy/buy.apu?ids='.ALLOPASS_IDS.'&idd='.ALLOPASS_IDD.'&data='.$_SESSION['af_id'].'" style="border:0">
			  <img src="https://payment.allopass.com/static/buy/button/fr/162x56.png" style="border:0" alt="Buy now!" />
			 </a>
			</noscript>
			<!-- End Allopass Checkout-Button Code -->
			</center>
		';
	
	//##############################################################################################
	
	// -------- Verification que le membre soit identifie
	if (!isset($_SESSION['af_id']) && !isset($_GET['c']))
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
				
	// -------- Affichage code bon/mauvais + créditation
	
	
	if (ALLOPASS_PAIEMENT && isset($_GET['c']) && ($_GET['sp'] != 1))
	{	
		//debut
		if(!empty($_GET['DATAS'])) {
			$RECALL = $_GET['RECALL'];
			if( trim($RECALL) == "" ) 
				echo "Probleme detecte, contactez un administrateur";		
			else
			{
				echo '<!-- VERIFICATION ALLOPASS -->';
				$RECALL = urlencode( $RECALL );
				$AUTH = urlencode(ALLOPASS_AUTHID);
				$r = @file( "https://payment.allopass.com/api/checkcode.apu?code=$RECALL&auth=$AUTH" );
				$z = substr( $r[0],0,2 );
				
				$requete = mysqli_query($conn, "SELECT `data` FROM `".SQL_PREFIX."_paiements` WHERE `data`='".$RECALL."' LIMIT 0,1");

				$result = mysqli_num_rows($requete);

				if( (substr( $r[0],0,2 ) != "OK") OR ($RECALL != ALLOPASS_CODETEST && $result!== false && $result > 0))
				{
					if (substr( $r[0],0,2 ) != "OK")
						$paiement_erreur = "Le code est incorrect";
					else
						$paiement_erreur = "Le code a déjà été utilisé";
					echo $allopass_info;
				}
				else
				{
					if (GetInfo($_GET['data'], 'id') > 0 && is_numeric($_GET['data']))
					{
						mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token + 1 WHERE `id`='".$_GET['data']."'");
						mysqli_query($conn, "
						INSERT INTO `".SQL_PREFIX."_paiements` (`id` ,`user` ,`data` ,`tokens` ,`date` ,`paiement_type`)
						VALUES ( NULL, '".$_GET['data']."', '".$RECALL."', '1', '".time()."', 'Allopass');
						");
						
						
						mysqli_query($conn, "
						INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
						VALUES (NULL , '".time()."', 'Creditation', '".$_GET['data']."', 'Allopass', '".$RECALL."', '".$_SERVER['REMOTE_ADDR']."');
						");
						echo "<script> alert('Votre compte a correctement été crédité de 1 token'); </script>";
						echo '<meta http-equiv="refresh" content="0; URL=index.php?p=token">';
					}
				}
			}
		}
		//fin
	}
	// ------- Affichage du formulaire Allopass
	if (ALLOPASS_PAIEMENT && (!isset($_GET['c']) OR $_GET['sp'] == 1)  && $_SESSION['af_id'] > 0 && is_numeric($_SESSION['af_id']))
		echo $allopass_info;
	
	echo '
					</div><!-- end div .text -->
				</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
	';
	//##############################################################################################
?>