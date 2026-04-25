<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
		
		
	echo '
    <div class="box-out">
    	<div class="box-in">
    		<div class="box-head"><h1>Erreur</h1></div>
    		<div class="box-content">
    			<div class="text">
    				<h1>Cette page n\'existe pas/plus</h1>

    				<hr />
    			</div><!-- end div .text -->
    		</div><!-- end div .box-content -->
    	</div><!-- end div .box-in -->
    </div><!-- end div .box-out -->
	';
?>