<?php

	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé afficher cette page");
	// Vérification que le membre est identifié
	
	
	if (!isset($_SESSION['af_id']))
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	// Vérification des droits admin

	
	elseif (GetInfo($_SESSION['af_id'], 'admin_level') < LVL_GESTION_LOGS && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'avez pas le niveau d\'administration suffisant ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	//################################################################################
	else
	{
		$vipcount = mysqli_query($conn, "SELECT COUNT(date_fin) FROM `".SQL_PREFIX."_droits` WHERE `date_fin` > ".time()." AND `type_droit` = 1;");
		$data = mysqli_fetch_array($vipcount);
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Liste des VIP</h1></div>
				<div class="box-content">
				Nombre de VIP : '.$data[0].'
    			<div class="table">
    				<table>
    					<thead>
    						<tr>
    							<td><div>Membre</div></td>
								<td><div>SteamID</div></td>
								<td><div>Date de Fin</div></td>
   							</tr>
   						</thead>
   						<tbody>';
		$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_droits` WHERE type_droit = 1 ORDER BY date_fin");
		$class = "odd";
		while($row = mysqli_fetch_array($requete))
		{

			if ($class == "odd")	$class = "even";
			else $class = "odd";

			echo VipAffichage($class, $row['steam_id'], $row['date_fin']);

			}
		echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
	}
	
?>