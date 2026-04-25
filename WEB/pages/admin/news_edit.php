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

	
	if (isset($_POST['edit_now']) && empty($_POST['preview']) && !empty($_POST['news_title']) && !empty($_POST['news_content']))
	{

		if (is_numeric($_GET['edit']) OR is_numeric($_POST['news_id']))
		{
			if (is_numeric($_GET['edit']))			$id_news = $_GET['edit'];
			else									$id_news = $_POST['news_id'];
			
			$sql = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_news`  WHERE `id` = '".$id_news."' LIMIT 1");
			$data =				 mysqli_result($sql, 0, 'news_date');
			$news_title = 		mysqli_result($sql, 0, 'news_title');
			$news_content = 		mysqli_result($sql, 0, 'news_content');
			
			
			if ($data > 1)
			{
				echo '<meta http-equiv="refresh" content="1; URL=index.php?p=news_gestion">';
				$requete_sql = "
					UPDATE `".SQL_PREFIX."_news` SET
					`news_editauteur` = '".mysqli_real_escape_string($conn, $_SESSION['af_pseudo'])."',
					`news_title` = '".mysqli_real_escape_string($conn, htmlentities($_POST['news_title'], ENT_QUOTES, "UTF-8"))."',
					`news_edit` = '".time()."',
					`news_content` = '".mysqli_real_escape_string($conn, htmlentities($_POST['news_content'], ENT_QUOTES, "UTF-8"))."'
					WHERE `af_news`.`id` = '".$id_news."';
				";
				mysqli_query($conn, $requete_sql);
				mysqli_query($conn, "
					INSERT INTO `".SQL_PREFIX."_logs` (`id` ,`timestamp` ,`action` ,`membre` ,`detail`, `detail2`, `ip`)
					VALUES (NULL , '".time()."', 'Edit d\'une news', '".$_SESSION['af_id']."',
					'".mysqli_real_escape_string($conn, htmlentities($news_title, ENT_QUOTES, "UTF-8"))."', '-', '".$_SERVER["REMOTE_ADDR"]."');
					");
;
		
				echo'
					<div class="notification success">
						<div class="messages">La news a été modifiée avec succès, redirection en cours... <div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
					</div><!-- end div .notification info -->
				';
			}
		}
	}
	else 
	{
		if (is_numeric($_GET['edit']) OR is_numeric($_POST['news_id']))
		{
			if (is_numeric($_GET['edit']))			$id_news = $_GET['edit'];
			else									$id_news = $_POST['news_id'];
			
			$sql = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_news`  WHERE `id` = '".$id_news."' LIMIT 1");
			$data =				 mysqli_result($sql, 0, 'news_date');
			$news_title = 		mysqli_result($sql, 0, 'news_title');
			$news_content = 		mysqli_result($sql, 0, 'news_content');

				
				
			if ($data < 1) 
				echo '<div class="notification error">
					<div class="messages">Impossible de trouver la news que vous souhaitez éditer...<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
				</div><!-- end div .notification error -->';
			else 
			{
				if (!isset($_POST['news_title']))			$_POST['news_title'] = html_entity_decode($news_title, ENT_QUOTES,"UTF-8");
				if (!isset($_POST['news_content']))			$_POST['news_content'] = html_entity_decode($news_content, ENT_QUOTES,"UTF-8");
				echo '
					<div class="box-out">
						<div class="box-in">
							<div class="box-head"><h1>Editer une news</h1></div>
							<div class="box-content">';
							
				if (isset($_POST['news_edit']) && !isset($_POST['preview']))
				{
						echo '<div class="notification error">
							<div class="messages">Vous n\'avez pas complété correctement le formulaire<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
						</div><!-- end div .notification error -->';
				}
				echo '
				<div class="form">
						<form METHOD="POST" name ="server_add" action="index.php">
									<fieldset>
										<input type="HIDDEN" id="news_edit" name="news_edit" value="news_edit"> 
										<input type="HIDDEN" id="news_id" name="news_id" value="'.$id_news.'">
										
										<label for="medium_field">Titre de la news</label>
										<input type="text" class="text medium" id="news_title" name="news_title" maxlength="128" />
										
										<label for="medium_field">Contenu de la news</label>
										<textarea class="text textarea" cols="80" rows="10" name="news_content" id="news_content"></textarea>
										
										
										<br><br>
										<input type="submit" name="edit_now" id="edit_now" value="Editer la news" class="submit" />  
										<input type="submit" name="preview" id="preview" value="Prévisualisation" class="submit" />  

									</fieldset>
								</form>
							</div><!-- end div .form -->
					';
					/* Re-remplir le formulaire si il y'a eu une erreur */
					
					echo "<script>" . "\n";
					
					if (!empty($_POST['news_title']))		echo "document.getElementById('news_title').value ='".strip_tags(addslashes($_POST['news_title']))."';" . "\n";
					if (!empty($_POST['news_content']))		echo "document.forms.server_add.news_content.value = '".str_replace("\r\n", "\\n", strip_tags(addslashes($_POST['news_content'])))."'";
						
					echo "</script>" . "\n";
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
					 echo '<h5>'.htmlentities($_POST['news_title'],ENT_QUOTES, "UTF-8").' </h5><i><p> (Posté par '.$_SESSION['af_pseudo'].' le '.date("m/d/Y à H:i:s", time()).')</i></p>';
					echo '<p>' . BBcode(htmlentities($_POST['news_content'],ENT_QUOTES, "UTF-8")) . '</p>';
					echo '<hr />';
					
					
					echo '</div><!-- end div .text -->
							</div><!-- end div .box-content -->
						</div><!-- end div .box-in -->
					</div><!-- end div .box-out -->
					';
				}
			}
		}
		else
			echo '<div class="notification error">
				<div class="messages">Impossible de trouver la news que vous souhaitez éditer...<div class="close"><img src="img/icon/close.png" alt="close" /></div></div>
			</div><!-- end div .notification error -->';
	}
	
?>