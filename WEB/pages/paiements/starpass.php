<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	if (STARPASS_PAIEMENT)
	{
		echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Créditation StarPass </h1></div>
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
					
		//##############################################################################################
		

		echo '	
			<p>Ce rechargement vous créditera de <b> 1 token </b>!</p>
			<p>En cas de problèmes avec les codes, nous vous conseillons de les noter quelque part.<br>
			Si vous rencontrez un problème, merci de contacter un administrateur depuis notre Forum.<br></p>

			<p><b>Si vous êtes mineur, vous devez avoir l\'autorisation de vos parents ou de votre tuteur légal !</b></p>
			
			<p><b>Les tokens ne peuvent pas être remboursés !</b></p><br>
			<center>
					<div id="starpass_'.STARPASS_IDD.'"></div>
					<script type="text/javascript" src="https://script.starpass.fr/script.php?idd='.STARPASS_IDD.'&amp;verif_en_php=1&amp;datas='.$_SESSION['af_id'].'&amp;theme=black_neon">
					</script>
					<noscript>
					Veuillez activer le Javascript de votre navigateur s\'il vous plaît.<br />
					<a href="https://www.starpass.fr/">Micro Paiement StarPass</a>
					</noscript>
				</center>
		';
		echo '
						</div><!-- end div .text -->
					</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->
			</div><!-- end div .box-out -->
		';

	
	//Déclaration des variables
	if ($_GET['sp'] == 1 && $_GET['c'] != "mauvais_code")
	{

		$ident=$idp=$ids=$idd=$codes=$code1=$code2=$code3=$code4=$code5=$datas='';
		$idp = STARPASS_IDP;
		//$ids n'est plus utilisé, mais il faut conserver la variable pour une question de compatibilité
		$idd = STARPASS_IDD;
		$ident=$idp.";".$ids.";".$idd;
		//On récupère le(s) code(s) sous la forme "xxxxxxxx;xxxxxxxx"
		if(isset($_POST['code1'])) $code1 = $_POST['code1'];
		if(isset($_POST['code2'])) $code2 = ";".$_POST['code2'];
		if(isset($_POST['code3'])) $code3 = ";".$_POST['code3'];
		if(isset($_POST['code4'])) $code4 = ";".$_POST['code4'];
		if(isset($_POST['code5'])) $code5 = ";".$_POST['code5'];
		$codes=$code1.$code2.$code3.$code4.$code5;
		//On récupère le champ DATAS"
		if(isset($_POST['DATAS'])) $datas = $_POST['DATAS'];
		//On encode les trois chaines en URL
		$ident=urlencode($ident);
		$codes=urlencode($codes);
		$datas=urlencode($datas);

		/* Envoie de la requête vers le serveur StarPass
		Dans la variable tab[0] on récupère la réponse du serveur
		Dans la variable tab[1] on récupère l'URL d'accès ou d'erreur suivant la réponse du serveur */
		$get_f=@file("https://script.starpass.fr/check_php.php?ident=$ident&codes=$codes&DATAS=$datas");
		if(!$get_f)
		{
			exit("Votre serveur n'a pas accès au serveur de Starpass, merci de contacter votre hébergeur.");
		}
		$tab = explode("|",$get_f[0]);

		if(!$tab[1]) $url = "https://vip.lastfate.fr/index.php?p=token&c=mauvais_code";
		else $url = $tab[1];

		

		// dans $pays on a le pays de l'offre. exemple "fr"
		$pays = $tab[2];
		// dans $palier on a le palier de l'offre. exemple "Plus A"
		$palier = urldecode($tab[3]);
		// dans $id_palier on a l'identifiant de l'offre
		$id_palier = urldecode($tab[4]);
		// dans $type on a le type de l'offre. exemple "sms", "audiotel, "cb", etc.
		$type = urldecode($tab[5]);
		// vous pouvez à tout moment consulter la liste des paliers à l'adresse : http://script.starpass.fr/palier.php

		//Si $tab[0] ne répond pas "OUI" l'accès est refusé
		//On redirige sur l'URL d'erreur
		if(substr($tab[0],0,3) != "OUI")
		{
			echo '<meta http-equiv="refresh" content="0; URL=index.php?p=token&c=mauvais_code&sp=1">';
		}
		else
		{
		
			$requete = mysqli_query($conn, "SELECT `data` FROM `".SQL_PREFIX."_paiements` WHERE `data`='".$code1."' LIMIT 0,1");

			$result = mysqli_num_rows($requete);

			if($code1 != STARPASS_CODETEST && $result!== false && $result > 0)
			{
				echo "<script> alert('Ce code a déjà été utilisé !'); </script>";
			}
			else
			{
				if ($code1 != STARPASS_CODETEST)
				{
					mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token + 1 WHERE `id`='".$datas."'");
					mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_paiements` (`id` ,`user` ,`data` ,`tokens` ,`date` ,`paiement_type`)
					VALUES ( NULL, '".$datas."', '".$code1."', '1', '".time()."', 'StarPass');
					");
									
									
					mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Creditation', '".$datas."', 'StarPass', '".$code1."', '".$_SERVER['REMOTE_ADDR']."');
					");
				}
				elseif($code1 == STARPASS_CODETEST)
				{
					mysqli_query($conn, "UPDATE `".SQL_PREFIX."_users` SET `token` = token + 1 WHERE `id`='".$datas."'");
					mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_paiements` (`id` ,`user` ,`data` ,`tokens` ,`date` ,`paiement_type`)
					VALUES ( NULL, '".$datas."', 'Code Secret', '1', '".time()."', 'StarPass');
					");
									
									
					mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Creditation', '".$datas."', 'StarPass', 'Code Secret', '".$_SERVER['REMOTE_ADDR']."');
					");
				}
				echo '<meta http-equiv="refresh" content="0; URL=index.php?p=token&c=bon_code">';
			}
		}
	}
}
?>
