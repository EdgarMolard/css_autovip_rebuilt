<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
		
		
	echo '
    <div class="box-out">
    	<div class="box-in">
    		<div class="box-head"><h1>Récupération du mot de passe</h1></div>
    		<div class="box-content">
    			<div class="text">';

	
	if (strlen($_GET['code']) < 16 OR !is_numeric($_GET['id']))
	{
		echo '
			<div class="notification error">
				<div class="messages">Ce lien de récupération n\'est pas valide, assurez vous de l\'avoir correctement recopié <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification error -->';
	}
	else
	{
		$sql = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM `".SQL_PREFIX."_users` WHERE recovery_code='".md5($_GET['code'])."' AND id='".$_GET['id']."' LIMIT 1"));
		if ($sql == 1)
		{
		
			$new_password = random(6);
			echo '
				<div class="notification success">
					<div class="messages">Votre mot de passe a bien été changé <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification success -->';
				
			mysqli_query($conn, ("UPDATE `".SQL_PREFIX."_users` SET `recovery_code` = '', `recovery_date` = '".time()."', password='".md5($new_password)."' WHERE `id` ='".$_GET['id']."';");
			echo "<p> Votre nouveau mot de passe est: <b>" . $new_password . "<br><br></b>Vous allez également le recevoir par e-mail.";
			
			$sql = mysqli_query($conn, ("SELECT mail, username FROM `".SQL_PREFIX."_users` WHERE `id`= '".$_GET['id']."' LIMIT 1");
			$mail = mysqli_result($sql, 0, 'mail');
			$username = mysqli_result($sql, 0, 'username');
			$message_recovery = "
				Bonjour, ".htmlentities($username, ENT_QUOTES).".
				<br>
				Votre nouveau mot de passe est : <b> ".$new_password."</b><br><br>
				Pensez à la changer depuis votre espace membre.
			";
				mail_envois($mail, "Votre nouveau mot de passe", $message_recovery);
			

		}
		else
			echo '
				<div class="notification error">
					<div class="messages">Ce lien de récupération n\'est pas valide, assurez vous de l\'avoir correctement recopié <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
				
	echo '		</div><!-- end div .text -->
    		</div><!-- end div .box-content -->
    	</div><!-- end div .box-in -->
    </div><!-- end div .box-out -->
	';
?>