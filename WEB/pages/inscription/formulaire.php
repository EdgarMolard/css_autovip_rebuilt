<?php

	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
	
	// L'inscription se fait automatiquement via Steam OpenID
	// Rediriger vers la page de login
	if (!isset($_SESSION['af_id'])) {
		if(strcmp($lang, "fr") == 0) {
			echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Inscription</h1></div>
					<div class="box-content">
						<div class="text">
							<h2>Bienvenue!</h2>
							<p>L\'inscription sur le site se fait automatiquement lors de votre première connexion avec votre compte Steam.</p>
							<p>Un compte sera créé automatiquement avec votre pseudo Steam et votre Steam ID.</p>
							<br/>
							<center>
								<a href="login.php?lang=' . $lang . '" class="submit" style="display: inline-block; padding: 10px 30px; background: #333; color: white; text-decoration: none; border-radius: 5px;">
									Se connecter avec Steam
								</a>
							</center>
							<hr />
						</div><!-- end div .text -->
					</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->
			</div><!-- end div .box-out -->
			';
		} else {
			echo '
			<div class="box-out">
				<div class="box-in">
					<div class="box-head"><h1>Sign Up</h1></div>
					<div class="box-content">
						<div class="text">
							<h2>Welcome!</h2>
							<p>Registration on the site is done automatically when you first log in with your Steam account.</p>
							<p>An account will be created automatically with your Steam nickname and Steam ID.</p>
							<br/>
							<center>
								<a href="login.php?lang=' . $lang . '" class="submit" style="display: inline-block; padding: 10px 30px; background: #333; color: white; text-decoration: none; border-radius: 5px;">
									Sign in with Steam
								</a>
							</center>
							<hr />
						</div><!-- end div .text -->
					</div><!-- end div .box-content -->
				</div><!-- end div .box-in -->
			</div><!-- end div .box-out -->
			';
		}
	} else {
		// L'utilisateur est déjà connecté
		echo '<meta http-equiv="refresh" content="0; URL=index.php?lang=' . $lang . '">';
	}
?>
