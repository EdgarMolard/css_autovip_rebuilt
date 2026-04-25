<?php
	define("CHECK_IN", 1);
	include('./../../configuration.php');
	include('./../../fonctions.php');
		
	function PaiementDone($id_membre, $token, $transaction_id, $montant, $steam_id) {
		$conn = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_BDD);
		
		if ($token < 999)
		{
			mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token + $token WHERE `id`='".$id_membre."'");
			mysqli_query($conn, "INSERT INTO `".SQL_PREFIX."_paiements` (`id` ,`user` ,`data` ,`tokens` ,`date` ,`paiement_type`)
			VALUES ( NULL, '".$id_membre."', '".$transaction_id."', '".$token."', '".time()."', 'PayPal');
			");

			mysqli_query($conn, "INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
			VALUES (NULL , '".time()."', 'Creditation', '".$id_membre."', 'PayPal (".$montant." ".PAYPAL_CURRENCY.")', '".$transaction_id."', '".$ip."');
			");
		}
		else
		{
			$query = "SELECT * FROM `".SQL_PREFIX."_droits WHERE user_id = '".$id_membre."' AND `type_droit`=1";
			$result = mysqli_query($conn, $query);
					
			if(mysqli_num_rows($result) > 0)
			{
				mysqli_query($conn, "UPDATE `".SQL_PREFIX."_droits` SET `date_fin` = '2147483647' WHERE `user_id` ='".$id_membre."' AND `type_droit`=1");
			}
			else
			{
				mysqli_query($conn, "INSERT INTO `".SQL_PREFIX."_droits` (`id` ,`user_id` ,`type_droit` ,`date_start` ,`date_fin` ,`is_suspended` ,`ip_serveur` ,`port_serveur` ,`steam_id`) 
				VALUES (NULL ,'".$id_membre."','1','".time()."','2147483647','0', '0','0','".$steam_id."');");
			}
			
			mysqli_query($conn, "INSERT INTO `".SQL_PREFIX."_paiements` (`id` ,`user` ,`data` ,`tokens` ,`date` ,`paiement_type`)
			VALUES ( NULL, '".$id_membre."', '".$transaction_id."', '9', '".time()."', 'PayPal');
			");
			
			mysqli_query($conn, "INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
			VALUES (NULL , '".time()."', 'VIP LifeTime', '".$id_membre."', 'PayPal (".$montant." ".PAYPAL_CURRENCY.")', '".$transaction_id."', '".$ip."');
			");
		}
		mysqli_close($conn);
	}
	
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}

	$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Host: www.paypal.com\r\n";
	$header .= "Connection: close\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

	// assign posted variables to local variables
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number']; // id commande
	$payment_status = $_POST['payment_status']; // Completed,
	$payment_amount = $_POST['mc_gross']; //0.01
	$payment_currency = $_POST['mc_currency']; 
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];
	$idmembre = $_POST['custom'];
	$steamid = $_POST['steamid'];
	$ip = $_SERVER["REMOTE_ADDR"]; 
	$timestamp = time();
	
	if (!$fp) {
	//Erreur
	} 

	if ($fp) {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
	$res = fgets ($fp, 1024);
	$res = trim($res);
	if (strcmp ($res, "VERIFIED") == 0) {
		$conn = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_BDD);
		$doublonidtrans = mysqli_num_rows(mysqli_query($conn, "SELECT data FROM `".SQL_PREFIX."_paiements` WHERE data='".$txn_id."' LIMIT 1"));
		mysqli_close($conn);
		if ($payment_status == "Completed" && $payment_currency == PAYPAL_CURRENCY && $doublonidtrans == 0 && $receiver_email = PAYPAL_MAIL) {
			if ($item_number == 1 && is_numeric($idmembre) && PAYPAL_PRIX_1 == $payment_amount)
				PaiementDone($idmembre, PAYPAL_TOKEN_1, $txn_id, $payment_amount, $steamid);
				
			if ($item_number == 2 && is_numeric($idmembre) && PAYPAL_PRIX_2 == $payment_amount)
				PaiementDone($idmembre, PAYPAL_TOKEN_2, $txn_id, $payment_amount, $steamid);
				
			if ($item_number == 3 && is_numeric($idmembre) && PAYPAL_PRIX_3 == $payment_amount)
				PaiementDone($idmembre, PAYPAL_TOKEN_3, $txn_id, $payment_amount, $steamid);
				
			if ($item_number == 4 && is_numeric($idmembre) && PAYPAL_PRIX_4 == $payment_amount)
				PaiementDone($idmembre, PAYPAL_TOKEN_4, $txn_id, $payment_amount, $steamid);
		  
			if ($item_number == 5 && is_numeric($idmembre) && PAYPAL_PRIX_5 == $payment_amount)
				PaiementDone($idmembre, PAYPAL_TOKEN_5, $txn_id, $payment_amount, $steamid);
			}


		}
	else if (strcmp ($res, "INVALID") == 0) {
			//Si vous souhaitez logguer les erreurs, votre code ici =)
		}
	}
	fclose ($fp);
	}
	
?>