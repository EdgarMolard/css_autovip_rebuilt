<?php
if (CHECK_IN != 1) 
	die("Vous n'êtes pas autorisé à afficher cette page");
	
	$suspension = GetInfo($_SESSION['af_id'], 'suspend_reason');
	$suspended = nl2br(GetInfo($_SESSION['af_id'], 'is_suspended'));
	
	if (strlen($suspension) == 0)	$suspension = "Aucun raison n'a été indiquée";		
	
	if (!isset($_SESSION['af_id']))
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	else if ($suspended > 0)
		echo '
		<div class="notification error">
			<div class="messages">Vous ne pouvez pas créditer votre compte en Tokens, celui-ci est suspendu pour la raison suivante : <i>'.$suspension.'</i><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
		</div><!-- end div .notification info -->';
	else
	{
		$client_token = GetInfo($_SESSION['af_id'], 'token');
		$access_vip = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE `user_id`= '".$_SESSION['af_id']."' AND `type_droit`='1'"));
			
		if ($_GET['c'] == "mauvais_code")
		{
			echo '
				<div class="notification error">
					<div class="messages">Votre code a été refusé par le serveur, contactez le webmaster ou le fournisseur du code<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';	
		}
		elseif ($_GET['c'] == "bon_code")
		{
			if($access_vip == 0 && $client_token >= 1)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 1 Token : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			elseif($access_vip == 1 && $client_token >= 1)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 1 Token<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
		}		
		elseif ($_GET['c'] == "bon_paypal")
		{
			if($access_vip == 0 && $client_token >= 1)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 1 Token : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			elseif($access_vip == 1 && $client_token >= 1)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 1 Token<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
		}		
		elseif ($_GET['c'] == "bon_paypal2")
		{
			if($access_vip == 0 && $client_token >= 3)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 3 Tokens : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			elseif($access_vip == 1 && $client_token >= 3)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 3 Tokens<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			else
			{
				if ($access_vip == 0 && $client_token == 1)
					echo '
					<div class="notification info">
						<div class="messages">Vous avez '.$client_token.' Token, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
				elseif($access_vip == 0 && $client_token > 1)
					echo '
					<div class="notification info">
						<div class="messages">Vous avez '.$client_token.' Tokens, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
			}
		}		
		elseif ($_GET['c'] == "bon_paypal3")
		{
			if($access_vip == 0 && $client_token >= 5)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 5 Tokens : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			elseif($access_vip == 1 && $client_token >= 5)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 5 Tokens<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			else
			{
				if ($access_vip == 0 && $client_token == 1)
					echo '
					<div class="notification info">
						<div class="messages">Vous avez '.$client_token.' Token, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
				elseif($access_vip == 0 && $client_token > 1)
					echo '
					<div class="notification info">
						<div class="messages">Vous avez '.$client_token.' Tokens, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
			}
		}
		elseif ($_GET['c'] == "bon_paypal4")
		{
			if($access_vip == 0 && $client_token >= 10)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 10 Tokens : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			elseif($access_vip == 1 && $client_token >= 10)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre compte a correctement été crédité de 10 Tokens<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
			else
			{
				if ($access_vip == 0 && $client_token == 1)
					echo '
					<div class="notification info">
						<div class="messages">Vous avez '.$client_token.' Token, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
				elseif($access_vip == 0 && $client_token > 1)
					echo '
					<div class="notification info">
						<div class="messages">Vous avez '.$client_token.' Tokens, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->';
			}
		}	
		elseif ($_GET['c'] == "bon_paypal5")
		{
			if($access_vip == 1)
			{
				echo '
					<div class="notification success">
						<div class="messages">Votre Abonnement VIP à vie est désormais actif !<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification success -->';
			}
		}		
		elseif ($_GET['c'] != "mauvais_code" || $_GET['c'] != "bon_code" || $_GET['c'] != "bon_paypal" || $_GET['c'] == "bon_paypal2" || $_GET['c'] == "bon_paypal3")
		{
			if ($access_vip == 0 && $client_token == 1)
				echo '
				<div class="notification info">
					<div class="messages">Vous avez '.$client_token.' Token, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification info -->';
			elseif($access_vip == 0 && $client_token > 1)
				echo '
				<div class="notification info">
					<div class="messages">Vous avez '.$client_token.' Tokens, et vous n\'êtes pas VIP, cliquez ici pour y remédier : <a href="./index.php?p=droits">Devenir VIP</a><div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification info -->';
		}			
					
		if (isset($_SESSION['af_id']) && STARPASS_PAIEMENT)			include("./pages/paiements/starpass.php");		 /* StarPass */
		if (isset($_SESSION['af_id']) && ALLOPASS_PAIEMENT)			include("./pages/paiements/allopass.php");		/* Allopass */
		if (isset($_SESSION['af_id']) && PAYPAL_PAIEMENT)			include("./pages/paiements/paypal.php");		/* PayPal */
	}

	


?>