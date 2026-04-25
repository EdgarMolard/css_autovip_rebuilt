<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	if (PAYPAL_PAIEMENT)
	{
		echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Créditation PayPal</h1></div>
					<div class="box-content">
						<div class="text">
			';
			
		//##############################################################################################
		
		// -------- Verification que le membre soit identifie
		if (!isset($_SESSION['af_id']))
			echo '
					<div class="notification error">
						<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification error -->';
					
					
		//Les différents formulaires
		$formulaire_paypal_1 = '
						<div class="form">
							<form action=https://www.paypal.com/cgi-bin/webscr method=post>
								<fieldset>
									<input type=hidden name=cmd value=_xclick>
									<input type=hidden name=business value=\''.PAYPAL_MAIL.'\'>
									<input type=hidden name=undefined_quantity value=0>
									<input type=hidden name=item_name value=\''.PAYPAL_NOM_PRODUIT_1.'\'>
									<input type=hidden name=amount value=\''.PAYPAL_PRIX_1.'\'>
									<input type=hidden name=item_number value=\'1\'>
									<input type=hidden name=custom value=\''.$_SESSION['af_id'].'\'>
									<input type=hidden name=steamid value=\''.$_SESSION['af_steam_id'].'\'>
									<input type=hidden name=currency_code value=\''.PAYPAL_CURRENCY.'\'>
									<input type=hidden name=shipping value=0.00>
									<input type=hidden name=return value=\''.PAYPAL_RETURN_PAGE1.'\'>
									<input type=hidden name=cancel_return value=\''.PAYPAL_CANCEL_PAGE.'\'>
									<input type=hidden name=no_note value=0>
									<input type=hidden name=notify_url value=\''.PAYPAL_URL_NOTIFICATION.'\'>
									<input type=submit class="submit" value="'.PAYPAL_NOM_PRODUIT_1.' ('.PAYPAL_PRIX_1 . ' ' . PAYPAL_CURRENCY .')">
								</fieldset>
							</form>
						</div>
						';
		$formulaire_paypal_2 = '
						<div class="form">
							<form action=https://www.paypal.com/cgi-bin/webscr method=post>
									<input type=hidden name=cmd value=_xclick>
									<input type=hidden name=business value=\''.PAYPAL_MAIL.'\'>
									<input type=hidden name=undefined_quantity value=0>
									<input type=hidden name=custom value=\''.$_SESSION['af_id'].'\'>
									<input type=hidden name=steamid value=\''.$_SESSION['af_steam_id'].'\'>
									<input type=hidden name=item_number value=\'2\'>
									<input type=hidden name=item_name value=\''.PAYPAL_NOM_PRODUIT_2.'\'>
									<input type=hidden name=amount value=\''.PAYPAL_PRIX_2.'\'>
									<input type=hidden name=currency_code value=\''.PAYPAL_CURRENCY.'\'>
									<input type=hidden name=shipping value=0.00>
									<input type=hidden name=return value=\''.PAYPAL_RETURN_PAGE2.'\'>
									<input type=hidden name=cancel_return value=\''.PAYPAL_CANCEL_PAGE.'\'>
									<input type=hidden name=no_note value=0>
									<input type=hidden name=notify_url value=\''.PAYPAL_URL_NOTIFICATION.'\'>
									<input type=submit class="submit" value="'.PAYPAL_NOM_PRODUIT_2.' ('.PAYPAL_PRIX_2 . ' ' . PAYPAL_CURRENCY .')">
							</form>
						</div>
						';
						
		$formulaire_paypal_3 = '
						<div class="form">
							<form action=https://www.paypal.com/cgi-bin/webscr method=post>
									<input type=hidden name=cmd value=_xclick>
									<input type=hidden name=business value=\''.PAYPAL_MAIL.'\'>
									<input type=hidden name=undefined_quantity value=0>
									<input type=hidden name=custom value=\''.$_SESSION['af_id'].'\'>
									<input type=hidden name=steamid value=\''.$_SESSION['af_steam_id'].'\'>
									<input type=hidden name=item_number value=\'3\'>
									<input type=hidden name=item_name value=\''.PAYPAL_NOM_PRODUIT_3.'\'>
									<input type=hidden name=amount value=\''.PAYPAL_PRIX_3.'\'>
									<input type=hidden name=currency_code value=\''.PAYPAL_CURRENCY.'\'>
									<input type=hidden name=shipping value=0.00>
									<input type=hidden name=return value=\''.PAYPAL_RETURN_PAGE3.'\'>
									<input type=hidden name=cancel_return value=\''.PAYPAL_CANCEL_PAGE.'\'>
									<input type=hidden name=no_note value=0>
									<input type=hidden name=notify_url value=\''.PAYPAL_URL_NOTIFICATION.'\'>
									<input type=submit class="submit" value="'.PAYPAL_NOM_PRODUIT_3.' ('.PAYPAL_PRIX_3 . ' ' . PAYPAL_CURRENCY .')">
							</form>
						</div>
						';
						
		$formulaire_paypal_4 = '
						<div class="form">
							<form action=https://www.paypal.com/cgi-bin/webscr method=post>
									<input type=hidden name=cmd value=_xclick>
									<input type=hidden name=business value=\''.PAYPAL_MAIL.'\'>
									<input type=hidden name=undefined_quantity value=0>
									<input type=hidden name=custom value=\''.$_SESSION['af_id'].'\'>
									<input type=hidden name=steamid value=\''.$_SESSION['af_steam_id'].'\'>
									<input type=hidden name=item_number value=\'4\'>
									<input type=hidden name=item_name value=\''.PAYPAL_NOM_PRODUIT_4.'\'>
									<input type=hidden name=amount value=\''.PAYPAL_PRIX_4.'\'>
									<input type=hidden name=currency_code value=\''.PAYPAL_CURRENCY.'\'>
									<input type=hidden name=shipping value=0.00>
									<input type=hidden name=return value=\''.PAYPAL_RETURN_PAGE4.'\'>
									<input type=hidden name=cancel_return value=\''.PAYPAL_CANCEL_PAGE.'\'>
									<input type=hidden name=no_note value=0>
									<input type=hidden name=notify_url value=\''.PAYPAL_URL_NOTIFICATION.'\'>
									<input type=submit class="submit" value="'.PAYPAL_NOM_PRODUIT_4.' ('.PAYPAL_PRIX_4 . ' ' . PAYPAL_CURRENCY .')">
							</form>
						</div>
						';
			
		$formulaire_paypal_5 = '<hr>
						<div class="form">
							<form action=https://www.paypal.com/cgi-bin/webscr method=post>
									<input type=hidden name=cmd value=_xclick>
									<input type=hidden name=business value=\''.PAYPAL_MAIL.'\'>
									<input type=hidden name=undefined_quantity value=0>
									<input type=hidden name=custom value=\''.$_SESSION['af_id'].'\'>
									<input type=hidden name=steamid value=\''.$_SESSION['af_steam_id'].'\'>
									<input type=hidden name=item_number value=\'4\'>
									<input type=hidden name=item_name value=\''.PAYPAL_NOM_PRODUIT_5.'\'>
									<input type=hidden name=amount value=\''.PAYPAL_PRIX_5.'\'>
									<input type=hidden name=currency_code value=\''.PAYPAL_CURRENCY.'\'>
									<input type=hidden name=shipping value=0.00>
									<input type=hidden name=return value=\''.PAYPAL_RETURN_PAGE5.'\'>
									<input type=hidden name=cancel_return value=\''.PAYPAL_CANCEL_PAGE.'\'>
									<input type=hidden name=no_note value=0>
									<input type=hidden name=notify_url value=\''.PAYPAL_URL_NOTIFICATION.'\'>
									<input type=submit class="submit" value="'.PAYPAL_NOM_PRODUIT_5.' ('.PAYPAL_PRIX_5. ' ' . PAYPAL_CURRENCY .')">
							</form>
						</div>
						';
		

		$paypal_description = "<p>Ce rechargement vous créditera de <b>1, 3, 6, 12 tokens </b> ou bien <b><u>à vie !</b></u></p>
			<p>En cas de problèmes, nous vous conseillons de noter le numéro de la transaction quelque part.<br>
			Si vous rencontrez un problème, merci de contacter un administrateur depuis notre Forum.<br></p>

			<p><b>Si vous êtes mineur, vous devez avoir l'autorisation de vos parents ou de votre tuteur légal !</b></p>
			
			<p><b>Les tokens ne peuvent pas être remboursés !</b></p><br>";
		echo "<left>". $paypal_description;
		echo  "<center>";
		
		if (PAYPAL_PRIX_1 > 0)		echo $formulaire_paypal_1;
		if (PAYPAL_PRIX_2 > 0)		echo $formulaire_paypal_2; 
		if (PAYPAL_PRIX_3 > 0)		echo $formulaire_paypal_3; 
		if (PAYPAL_PRIX_4 > 0)		echo $formulaire_paypal_4; 
		if (PAYPAL_PRIX_5 > 0)		echo $formulaire_paypal_5; 
			
		echo "</center>";
		//##############################################################################################
		
		
		echo '
						</div><!-- end div .text -->
					</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->
			</div><!-- end div .box-out -->
		';
	}
?>