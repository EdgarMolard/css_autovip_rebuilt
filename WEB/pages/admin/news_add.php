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
	//################################################################################

	
	if (isset($_POST['news_add']) && empty($_POST['preview']) && !empty($_POST['news_title']) && !empty($_POST['news_content']))
	{
		$requete_sql = "
			INSERT INTO `".SQL_PREFIX."_news` (`id` ,`news_title` ,`news_date` ,`news_content` ,`news_auteur`, `news_edit`, `news_editauteur`)
			VALUES 
			(NULL , '".mysqli_real_escape_string($conn, htmlentities($_POST['news_title'], ENT_QUOTES, "UTF-8"))."',
			'".time()."', '".mysqli_real_escape_string($conn, htmlentities($_POST['news_content'], ENT_QUOTES, "UTF-8"))."',
			'".mysqli_real_escape_string($conn, $_SESSION['af_pseudo'])."', '0', '');";
		
		echo '<meta http-equiv="refresh" content="1; URL=index.php?p=news_gestion">';
		mysqli_query($conn, $requete_sql);

		mysqli_query($conn, "
			INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
			VALUES (NULL , '".time()."', 'Ajout d\'une news', '".$_SESSION['af_id']."',
			'-', '-', '".$_SERVER["REMOTE_ADDR"]."');
			");
		echo'
			<div class="notification success">
				<div class="messages">La news a été ajouté avec succès, redirection en cours... <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification info -->
		';
	
	}
	else {

		echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Poster une news</h1></div>
					<div class="box-content">';
					
		if (isset($_POST['news_add']))
		{
				echo '<div class="notification error">
					<div class="messages">Vous n\'avez pas complété correctement le formulaire<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
		}
		echo '
		<div class="form">
				<form METHOD="POST" name ="server_add" action="index.php">
							<fieldset>
								<input type="HIDDEN" id="news_add" name="news_add" value="news_add"> 
								
								
								<label for="medium_field">Titre de la news</label>
								<input type="text" class="text medium" id="news_title" name="news_title" maxlength="128" />
								
								<label for="medium_field">Contenu de la news</label>
								<textarea class="text textarea" cols="80" rows="10" name="news_content" id="news_content"></textarea>
								
								
								<br><br>
								<input type="submit" value="Poster la news" class="submit" />  
								<input type="submit" name="preview" id="preview" value="Prévisualisation" class="submit" />  

							</fieldset>
						</form>
					</div><!-- end div .form -->
			';
			/* Re-remplir le formulaire si il y'a eu une erreur */
			
			if (isset($_POST['news_add'])) 
			{
				echo "<script>" . "\n";
				if (!empty($_POST['news_title']))
					echo "document.getElementById('news_title').value ='".strip_tags(addslashes($_POST['news_title']))."';" . "\n";
				if (!empty($_POST['news_content']))
					echo "document.forms.server_add.news_content.value = '".str_replace("\r\n", "\\n", strip_tags(addslashes($_POST['news_content'])))."'";
					//str_replace("<br>","\r\n",$var)
					
				echo "</script>" . "\n";
			}
			echo '	</div><!-- end div .box-content -->
			</div><!-- end div .box-in -->
		</div><!-- end div .box-out -->
		';
		
		if (isset($_POST['preview']))
		{
		
			echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Aperçu</h1></div>
					<div class="box-content">
						<div class="text">';
			 echo '<h5>'.htmlentities($_POST['news_title'],ENT_QUOTES, "UTF-8").' </h5><i><p> (Posté par '.$_SESSION['af_pseudo'].' le '.date("d/m/Y à H:i:s", time()).')</i></p>';
			echo '<p>' . BBcode(htmlentities($_POST['news_content'],ENT_QUOTES, "UTF-8")) . '</p>';
			echo '<hr />';
			
			
			echo '</div><!-- end div .text -->
					</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->
			</div><!-- end div .box-out -->
			';
		
		}
	}
	
?>