<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	
	if (!empty($_GET['lang'])){
		$lang=$_GET['lang'];
	}
	else {
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
		if (empty($_GET['lang'])){
			$lang='fr';
		}
	}
?>

<!-- BEGIN SLIDEBAR -->
<div id="slidebar">
    <div id="menu">
    	<div class="menu-item">
    		<?php 
				if(strcmp($lang, "fr") == 0) {
					echo "<a href='index.php?lang=$lang' style='text-decoration:none'><h3><span id='home'></span>Accueil</h3></a>";
				}
				else {
					echo "<a href='index.php?lang=$lang' style='text-decoration:none'><h3><span id='home'></span>Home</h3></a>";
				}
				if(strcmp($lang, "fr") == 0) {
					echo "<a href='index.php?p=avantages&lang=$lang' style='text-decoration:none'><h3><span id='advantages'></span>Avantages</h3></a>";
				}
				else {
					echo "<a href='index.php?p=avantages&lang=$lang' style='text-decoration:none'><h3><span id='advantages'></span>Advantages</h3></a>";
				}
				if(strcmp($lang, "fr") == 0) {
					echo "<a href='index.php?p=staff&lang=$lang' style='text-decoration:none'><h3><span id='staff'></span>Equipe</h3></a>";
				}
				else {
					echo "<a href='index.php?p=staff&lang=$lang' style='text-decoration:none'><h3><span id='staff'></span>Staff</h3></a>";
				}
			?>
    	</div><!-- end div .menu-item -->
    	<div class="menu-item">
			<?php
			/* Liste des serveurs */
			$total = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM `".SQL_PREFIX."_server`"));
			if ($total > 0)
			{
				echo '
				<div class="menu-item">';
					if(strcmp($lang, "fr") == 0) {
						echo '<h3 class="close"><span id="serveurs"></span></span>Serveurs<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
					}
					else {
						echo '<h3 class="close"><span id="serveurs"></span></span>Servers<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
					}
					echo '<div class="menu-overflow">
						<div class="menu-content">
							<ul>';

				$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_server` ORDER BY server_name");
				$class = "odd";
				while($row = mysqli_fetch_array($requete))
				{
				if ($row['vip_prix'] > 0)	$vip_echo = '<img align="right" src="./img/icon_vip.png">';
				else						$vip_echo = "";
				
				if ($row['admin_prix'] > 0)	$admin_echo = '<img align="right" src="./img/icon_admin.png">';
				else						$admin_echo = "";
				
				
					echo '<li><a href="steam://connect/'.$row['server_ip'].':'.$row['server_port'].'"><img src="./img/icon/famfamfam/server_go.png" alt="" />'.$row['server_name'].'  '. $vip_echo . $admin_echo .' </a> </li>';
				}
							
								echo '
							</ul>
						</div><!-- end div .menu-content -->
					</div><!-- end div .menu-overflow -->
				</div><!-- end div .menu-item -->';
			}
			/* Serveurs liste fin*/
			?>
		<div class="menu-item">
    		<?php
				if(strcmp($lang, "fr") == 0) {
					echo '<h3 class="close"><span id="stats"></span>Statistiques<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
				else {
					echo '<h3 class="close"><span id="stats"></span>Statistics<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
			?>
    		<div class="menu-overflow">
    			<div class="menu-content">
    				<ul>
						<?php
							if(strcmp($lang, "fr") == 0) {
								echo "<li><a href='index.php?p=top&lang=$lang'><img src='img/icon/famfamfam/clock.png' />Top Connectés</a></li>";
							}
							else {
								echo "<li><a href='index.php?p=top&lang=$lang'><img src='img/icon/famfamfam/clock.png' />Top Connected</a></li>";
							}
						?>
    				</ul>
    			</div><!-- end div .menu-content -->
    		</div><!-- end div .menu-overflow -->
    	</div><!-- end div .menu-item -->
		</div>
		<div class="menu-item">
    		<?php
				if(strcmp($lang, "fr") == 0) {
					echo '<h3 class="open"><span id="myaccount"></span>Mon Compte<img src="img/icon/m-close.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
				else {
					echo '<h3 class="open"><span id="myaccount"></span>My account<img src="img/icon/m-close.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
			?>		
    		<div class="menu-overflow">
    			<div class="menu-content">
    				<ul>
					<?php
						if (!$_SESSION['af_id'] && !$_SESSION['af_ip_client']) {
							if(strcmp($lang, "fr") == 0) {
								echo "<li><a href='steam_redirect.php?lang=$lang'><img src='img/icon/famfamfam/page_edit.png' />Se connecter</a></li>";
							}
							else {
								echo "<li><a href='steam_redirect.php?lang=$lang'><img src='img/icon/famfamfam/page_edit.png' />Sign In</a></li>";
							}
						}
						else {
							if (PAYPAL_PAIEMENT OR ALLOPASS_PAIEMENT OR STARPASS_PAIEMENT)
							{
								echo "<li><a href='index.php?p=token&lang=$lang'><img src='img/starpass.png?v=1.1' />Créditation Tokens </a></li>";
			
							}
							if (RENTABILIWEB_PAIEMENT) {
								if(strcmp($lang, "fr") == 0) {
									echo "<li><a href='index.php?p=mini_token&lang=$lang'><img src='img/icon/famfamfam/coins_add.png' />Gagnez des MiniTokens</a></li>";
								}
								else {
									echo "<li><a href='index.php?p=mini_token&lang=$lang'><img src='img/icon/famfamfam/coins_add.png' />Win a MiniTokens</a></li>";
								}
							}
							if(strcmp($lang, "fr") == 0) {
								echo "<li><a href='index.php?p=droits&lang=$lang'><img src='img/icon/famfamfam/wand.png' />Activer Vip</a></li>";
								//echo "<li><a href='index.php?p=credits&lang=$lang'><img src='img/icon/famfamfam/coins.png' />Crédits Store</a></li>";
								echo "<li><a href='index.php?p=logs&lang=$lang'><img src='img/icon/famfamfam/keyboard_magnify.png' />Historique de mon compte</a></li>";
							}
							else {
								echo "<li><a href='index.php?p=droits&lang=$lang'><img src='img/icon/famfamfam/wand.png' />Activate Vip</a></li>";
								//echo "<li><a href='index.php?p=credits&lang=$lang'><img src='img/icon/famfamfam/coins.png' />Store Credits</a></li>";
								echo "<li><a href='index.php?p=logs&lang=$lang'><img src='img/icon/famfamfam/keyboard_magnify.png' />Account History</a></li>";
							}
							
						// RENTABILIWEB_PAIEMENT
						}	
					?>
    				</ul>
    			</div><!-- end div .menu-content -->
    		</div><!-- end div .menu-overflow -->
    	</div><!-- end div .menu-item -->
		<?php		
		/* Partie communauté */
		if (URL_FORUM || URL_SOURCEBANS || GROUPE_STEAM || MAIL_CONTACT)
		{
			echo '
			<div class="menu-item">';
				if(strcmp($lang, "fr") == 0) {
					echo '<h3 class="close"><span id="community"></span>Communauté<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
				else {	
					echo '<h3 class="close"><span id="community"></span>Community<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
				echo '<div class="menu-overflow">
					<div class="menu-content">
						<ul>';
							if (URL_FORUM)
									echo '<li><a href="'.URL_FORUM.'" target="_blank"><img src="./img/forum_16.png" alt="" />Forum</a></li>';
							if (URL_TS3)
								echo '<li><a href="'.URL_TS3.'"><img src="./img/ts3.png" alt="" />TeamSpeak 3</a></li>';
							if (URL_DISCORD)
								echo '<li><a href="'.URL_DISCORD.'"><img src="./img/discord.png" alt="" />Discord</a></li>';
							if (URL_SOURCEBANS)
								echo '<li><a href="'.URL_SOURCEBANS.'" target="_blank"><img src="./img/web.png" alt="" />SourceBans</a></li>';
							if (GROUPE_STEAM) {
								if(strcmp($lang, "fr") == 0) {
									echo '<li><a href="'.GROUPE_STEAM.'" target="_blank"><img src="./img/steam_small.png" alt="" />Groupe Steam</a></li>';
								}
								else {
									echo '<li><a href="'.GROUPE_STEAM.'" target="_blank"><img src="./img/steam_small.png" alt="" />Steam Group</a></li>';
								}
							}
							if (MAIL_CONTACT)
								echo '<li><a href="mailto:'.MAIL_CONTACT.'"><img src="./img/icon/famfamfam/email_link.png" alt="" />Contactez nous</a></li>';
								
							echo '
						</ul>
					</div><!-- end div .menu-content -->
				</div><!-- end div .menu-overflow -->
			</div><!-- end div .menu-item -->';
		}
		/* Fin Partie communauté */

		/* Partie Administration */
		if ($_SESSION['af_admin_level'] >= LVL_GESTION_ADMIN)
		{
			echo '
			<div class="menu-item">';
				if(strcmp($lang, "fr") == 0) {
					echo '<h3 class="close"><span id="admin"></span>Administration<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
				else {
					echo '<h3 class="close"><span id="admin"></span>Administration<img src="img/icon/m-open.png?v=1.1" style="padding:11px; padding-right:0px;" align="right" alt="" /></h3>';
				}
				echo'<div class="menu-overflow">
					<div class="menu-content">
						<ul>';
							if ($_SESSION['af_admin_level'] >= LVL_NEWS) {
								if(strcmp($lang, "fr") == 0) {
									echo "<li><a href='index.php?p=news_gestion&lang=$lang'><img src='./img/icon/famfamfam/newspaper.png' />Gestion des news</a></li>";
								}
								else {
									echo "<li><a href='index.php?p=news_gestion&lang=$lang'><img src='./img/icon/famfamfam/newspaper.png' />News management</a></li>";
								}
							}
							if ($_SESSION['af_admin_level'] >= LVL_GESTION_ADMIN) {
								if(strcmp($lang, "fr") == 0) {
									echo "<li><a href='index.php?p=client_list&lang=$lang'><img src='./img/icon/famfamfam/user_orange.png' />Gestion des comptes</a></li>";
								}
								else {
									echo "<li><a href='index.php?p=client_list&lang=$lang'><img src='./img/icon/famfamfam/user_orange.png' />Account management</a></li>";
								}
								
							}
							if ($_SESSION['af_admin_level'] >= LVL_GESTION_SERVEURS) {
								if(strcmp($lang, "fr") == 0) {
									echo "<li><a href='index.php?p=server_gestion&lang=$lang'><img src='./img/icon/famfamfam/server_edit.png' />Gestion des serveurs</a></li>";
								}
								else {
									echo "<li><a href='index.php?p=server_gestion&lang=$lang'><img src='./img/icon/famfamfam/server_edit.png' />Servers management</a></li>";
								}
							}
							if ($_SESSION['af_admin_level'] >= LVL_GESTION_LOGS) {
								if(strcmp($lang, "fr") == 0) {
									echo "<li><a href='index.php?p=historique_admin&lang=$lang'><img src='./img/icon/famfamfam/keyboard_magnify.png' />Historique global</a></li>";
								}
								else {
									echo "<li><a href='index.php?p=historique_admin&lang=$lang'><img src='./img/icon/famfamfam/keyboard_magnify.png' />Global history</a></li>";
								}
							}
							if ($_SESSION['af_admin_level'] >= LVL_GESTION_LOGS) {
								if(strcmp($lang, "fr") == 0) {
									echo "<li><a href='index.php?p=liste_vip&lang=$lang'><img src='./img/icon/famfamfam/textfield_key.png' />Liste des VIP</a></li>";
								}
								else {
									echo "<li><a href='index.php?p=liste_vip&lang=$lang'><img src='./img/icon/famfamfam/textfield_key.png' />VIP list</a></li>";
								}
								
							}
							echo '
						</ul>
					</div><!-- end div .menu-content -->
				</div><!-- end div .menu-overflow -->
			</div><!-- end div .menu-item -->';
		}
		/* Fin Partie Administration */
		?>
		<div id="menu">
			<div class="menu-item">
				<?php 
					if(strcmp($lang, "fr") == 0) {
						echo '<a href="index.php?p=aide" style="text-decoration:none"><h3><span id="aide"></span>Aide</h3></a>';
					}
					else {
						echo '<a href="index.php?p=aide" style="text-decoration:none"><h3><span id="aide"></span>Help</h3></a>';
					}
				?>
			</div>
		</div>
    </div><!-- end div #menu -->
</div><!-- end div #slidebar -->
<!-- END SLIDEBAR -->