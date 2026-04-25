<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
		
	echo '
    <div class="box-out">
    	<div class="box-in">
    		<div class="box-head"><h1>Actualités</h1></div>
    		<div class="box-content">
    			<div class="text">';
		if (!is_numeric(NEWS_A_AFFICHER) OR NEWS_A_AFFICHER < 1) define("NEWS_A_AFFICHER", 5);
		
		$requete = mysqli_query($conn, "SELECT * FROM `".SQL_PREFIX."_news` ORDER BY id DESC LIMIT ".NEWS_A_AFFICHER."");
		$class = "odd";
		while($row = mysqli_fetch_array($requete))
		{
			$edit = '';
			if ($row['news_edit'] > 0)
				$edit = ' (Dernière édition par '.$row['news_editauteur'].' le '.date("d/m/Y à H:i", $row['news_edit']).')';
			echo '<h5>'.$row['news_title'].' </h5><i>';
			echo '<p>' . BBcode($row['news_content']) . '</p>';
			echo '<h6>Posté par <b>'.$row['news_auteur'].'</b> | <b>Le '.date("d/m/Y à H:i", $row['news_date']).'</b> '.$edit.'</i></p></h6>';

			echo '<hr />';
		
		}
	echo '</div><!-- end div .text -->
    		</div><!-- end div .box-content -->
    	</div><!-- end div .box-in -->
    </div><!-- end div .box-out -->
	';
?>