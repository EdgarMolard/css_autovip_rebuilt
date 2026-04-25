<?php

	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	// Vérification que le membre est identifié
	
	
	if (!isset($_SESSION['af_id']))
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'êtes pas identifié ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	// Vérification des droits admin

	
	elseif (GetInfo($_SESSION['af_id'], 'admin_level') < LVL_NEWS && ROOT_SITE != $_SESSION['af_steam_id'])
	{
		echo '
				<div class="notification error">
					<div class="messages">Vous n\'avez pas le niveau d\'administration suffisant ! <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
	}
	
	
	
	else
	{
	
		if ($_GET['remove_news'] && is_numeric($_GET['remove_news']))
		{
			mysqli_query($conn, "DELETE FROM `".SQL_PREFIX."_news` WHERE `id` = '".$_GET['remove_news']."'");
			mysqli_query($conn, "
				INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
				VALUES (NULL , '".time()."', 'Suppression d\'une news', '".$_SESSION['af_id']."',
				'-', '-', '".$_SERVER["REMOTE_ADDR"]."');
				");
		}
		echo '
		<div class="box-out">
			<div class="box-in">
				<div class="box-head"><h1>Gestion des news</h1></div>
				<div class="box-content">
    			<div class="table">
    				<table>
    					<thead>
    						<tr>
    							<td><div>Titre</div></td>
   								<td><div>Date</div></td>
								<td><div>Auteur</div></td>
								<td><div>Action</div></td>
   							</tr>
   						</thead>
   						<tbody>';
		$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_news` ORDER BY id DESC");
		$class = "odd";
		while($row = mysqli_fetch_array($requete))
		{
			if ($class == "odd")	$class = "even";
			else 	$class = "odd";

			echo '
								<tr>
    								<td><div class="'.$class.'">'.$row['news_title'].'</div></td>
									<td><div class="'.$class.'">'.date("d/m/Y à H:i:s", $row['news_date']).' </div></td>
									<td><div class="'.$class.'">'.$row['news_auteur'].'</div></td>
									<td><div class="'.$class.'">
										<a class="tooltip" href="index.php?p=news_edit&edit='.$row['id'].'" title="Modifier cette news">Modifier</a> -
										<a class="tooltip" href="index.php?p=news_gestion&remove_news='.$row['id'].'" title="Effacer cette nouveauté">Supprimer</a>
									</div></td>
    							</tr>';
		}
		echo '
    				</tbody>
    			</table>
			</div>
			<div class="text">
		';
				
		echo '<p><img src="./img/icon/famfamfam/add.png"> <a class="tooltip" href="index.php?p=news_add" title="Ajouter une nouvelle news">Poster une news</a></p><br>';
				
		echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
		
	}
?>