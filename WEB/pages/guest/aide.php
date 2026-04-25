<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
		
		
	echo '
    <div class="box-out">
    	<div class="box-in">
    		<div class="box-head"><h1>F.A.Q</h1></div>
    		<div class="box-content">
    			<div class="text">';
?>

<div class="notification info">
	<div class="messages"><b>Où trouvé son SteamID ?</div>
</div><!-- end div .notification info -->
<div class="notification success">
	<div class="messages"><b>Il vous suffit tous simplement de taper la commande <font color="blue">status</font> dans la console quand vous êtes en jeu</b></div>
</div><!-- end div .notification success -->
</br>

<div class="notification info">
	<div class="messages"><b>J'ai acheté des tokens, mais je ne les est pas reçus, pourquoi ?</div>
</div><!-- end div .notification info -->
<div class="notification success">
	<div class="messages"><b>Cela est un bug, il vous faut le signaler à l'administrateur du site.</b></div>
</div><!-- end div .notification success -->
</br>

<div class="notification info">
	<div class="messages"><b>J'ai souscris à un abonnement ViP/ADMIN mais je ne le suis pas sur le serveur, pourquoi ?</div>
</div><!-- end div .notification info -->
<div class="notification success">
	<div class="messages"><b>Il vous faut patientez le temps que la liste se mette à jours sur l'ensemble des serveurs.</b></div>
</div><!-- end div .notification success -->
</br>


<?php
					echo '<hr />
    			</div><!-- end div .text -->
    		</div><!-- end div .box-content -->
    	</div><!-- end div .box-in -->
    </div><!-- end div .box-out -->
	';
?>