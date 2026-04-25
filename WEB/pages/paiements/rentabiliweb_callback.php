<?php

	define("CHECK_IN", 1);
	include('./../../configuration.php');
	include('./../../fonctions.php');

	$docId		= (int) $_GET['docId'];
	$uid		= $_GET['uid'];
	$awards		= (int) $_GET['awards'];
	$trId		= $_GET['trId'];
	$promoId	= ((isset($_GET['promoId'])) ? (int) $_GET['promoId'] : 0 );
	$hash		= $_GET['hash'];



	# check hash value
	if(md5($uid . $awards . $trId . RENTABILIWEB_HASH) == $hash && RENTABILIWEB_PAIEMENT == 1) {
		
		# DB connect

		$sql = connect_sql();
		
		
		$rs = mysqli_query($conn, 'SELECT COUNT(1) AS NB FROM history_payments WHERE external_reference = \''.addslashes($trId).'\'');
		$ifFindTr = (int) mysqli_result($rs, 0, 'NB');
		unset($rs);
		
		if($ifFindTr == 0) {
			mysqli_query($conn,  "UPDATE `".SQL_PREFIX."_users` SET `mini_token` = mini_token + $awards WHERE `id`='".addslashes($uid)."'");
			mysqli_query($conn,  'INSERT INTO history_payments (doc_id, user_id, Gold, external_reference, promo_id, date) ' 
						.'VALUE('.$docId.', \''.addslashes($uid).'\', '.$awards.', \''.addslashes($trId).'\', '.$promoId.', NOW())');
			mysqli_query($conn, "
			INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`)
			VALUES (NULL , '".time()."', 'Creditation', '".addslashes($uid)."','MiniTokens' , '".$awards."');
			");
						
			echo 'OK ID : ' . $uid;
			
		}
		else {
			# this transaction reference already added
			# add in error log 	
			
			echo 'NOT FAIR :(';
		}
		
		mysqli_close($conn);
	}
	else {
		# hash error
		# add in error log 	
		
		echo 'KO - 1';
		
	}




?>