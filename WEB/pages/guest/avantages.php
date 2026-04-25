<?php
	if (CHECK_IN != 1) 
		die("Vous n'êtes pas autorisé à afficher cette page");
		
		
	echo '
    <div class="box-out">
    	<div class="box-in">
    		<div class="box-head"><h1>Les avantages sur notre serveur</h1></div>
    		<div class="box-content">
    			<div class="text">';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script type="text/javascript">
			//<!--
					function change_onglet(name)
					{
							document.getElementById('onglet_'+anc_onglet).className = 'onglet_1 onglet';
							document.getElementById('onglet_'+name).className = 'onglet_1 onglet';
							document.getElementById('contenu_onglet_'+anc_onglet).style.display = 'none';
							document.getElementById('contenu_onglet_'+name).style.display = 'block';
							anc_onglet = name;
					}
			//-->
			</script>
	</head>

	<body>
		<div class="systeme_onglets">
			<div class="onglets">
				<span class="onglet_1 onglet" id="onglet_1" onclick="javascript:change_onglet('1');">Awp</span>
				<span class="onglet_2 onglet" id="onglet_2" onclick="javascript:change_onglet('2');">Retake</span>
				<!--<span class="onglet_3 onglet" id="onglet_3" onclick="javascript:change_onglet('3');">BaJail</span>-->
			</div>
			<div class="contenu_onglets">
				<div class="contenu_onglet" id="contenu_onglet_1">
					<h5><b>Serveurs Awp (37.187.91.146:27018)</b></h5>
					<p>
						<font color="black">• Accès au Clan Tag </font><font color="red">uniquement</font><font color="black"> pour les </font><font color="blue">VIP</font><font color="black">.</font><br>
						<font color="black">• Vous pouvez paramétrer vos couleurs via</font> <font color="blue">!cc</font>.<br>
						<font color="black">• Accès à dix skins pour les</font> <font color="blue">VIP</font>.<br>
						<font color="black">• Gain de crédits amélioré sur les kills, et gain de</font> <font color="blue">toutes les 5minutes</font><font color="black">.</font><br>
						<font color="black">• Lorsque vous mourrez vous ne pouvez pas perdre</font> <font color="blue">de crédits</font><font color="black">.</font><br>
						<!--<font color="black">• Électrocutez vos ennemis</font><font color="red"> lors de leur mort</font><font color="black">.</font><br>-->
						<font color="black">• Tirez des balles</font><font color="red"> de paintball</font><font color="black">.</font><br>
						<font color="black">• Gardez vos armes d'event pendant</font><font color="red"> des rounds events</font><font color="black">.</font><br>
						<font color="black">• Augmentations des dégâts avec</font><font color="red"> votre couteau</font><font color="black">.</font><br>
						<font color="black">• Lors des évènements unscops</font><font color="red"> vous regagnez une arme </font><font color="black">quand vous souhaitez viser.</font><br>
					</p>
				</div>
			</div>
			<div class="contenu_onglets">
				<div class="contenu_onglet" id="contenu_onglet_2">
					<h5><b>Serveurs Retake (37.187.91.146:27016 | 37.187.91.146:27017)</b></h5>
					<p>
						<font color="black">• Accès au Clan Tag </font><font color="red">uniquement</font><font color="black"> pour les </font><font color="blue">VIP</font><font color="black">.</font><br>
						<font color="black">• Vous pouvez paramétrer vos couleurs via</font> <font color="blue">!cc</font>.<br>
						<!--<font color="black">• Électrocutez vos ennemis</font><font color="red"> lors de leur mort</font><font color="black">.</font><br>-->
						<font color="black">• Accès aux armes supplémentaires</font><font color="red"> Mag-7, Mp7, Sawed-Off, Ssg08</font><font color="black">.</font><br>
						<font color="black">• Faîtes un spawn électrique grâce à votre </font><font color="red"> zeus</font><font color="black"> en poche.</font><br>
						<font color="black">• Vous pouvez spawn avec </font><font color="red"> 1 à 3</font><font color="black"> stuffs déjà prédéfinis.</font><br>
						<font color="black">• <font color="red">[VIP A VIE]</font> Customiser votre tag clan avec la commande </font><font color="red">!tag</font><font color="black">.</font><br>
					</p>
				</div>
			</div>
			<!--<div class="contenu_onglets">
				<div class="contenu_onglet" id="contenu_onglet_3">
					<h5><b>Serveurs BaJail (37.187.91.146:27019)</b></h5>
					<p>
						<font color="black"><font color="green">[Général]</font> • Accès au Clan Tag <font color="green">uniquement</font> pour les <font color="blue">VIP</font>.</font><br>
						<font color="black"><font color="green">[Général]</font> • Vous avez <font color="green">+20hp</font> au spawn.</font><br>
						<font color="black"><font color="green">[Général]</font> • Vous avez accès à la commande <font color="green">!gift</font>.</font><br>
						<font color="black"><font color="green">[Général]</font> • Vous pouvez stocker jusqu'à <font color="green">3000</font> points boutiques.</font><br>
						<font color="black"><font color="green">[Général]</font> • Vous avez -20% sur toute la boutique <font color="green">!store</font>.</font><br>
						<font color="black"><font color="green">[Général]</font> • Gagnez +50pdv lorsque vous récupérez le <font color="green">deagle magique</font>.</font><br>
						<font color="black"><font color="red">[Détenu]</font> • Aléatoirement vous pouvez spawn dans la <font color="green">Jail Vip</font>.</font><br>
						<font color="black"><font color="red">[Détenu]</font> • Grâce à vos flashbangs vous pouvez vous <font color="green">téléporter</font>.</font><br>
						<font color="black"><font color="red">[Détenu]</font> • Accès à des DVs uniques tel que : <font color="green">Cut vitesse, Cut 3e personne, Grenade paradise, Escorte Vip, Guerre de pompe</font>, d'autres feront leurs apparitions.</font><br>
						<font color="black"><font color="blue">[Gardien]</font> • L'utilisation de la commande <font color="green">!taser</font> est augmenté de 1.</font><br>
						<font color="black"><font color="blue">[Gardien]</font> • L'utilisation de la commande <font color="green">!rw</font> est augmenté de 2.</font><br>
						<font color="black"><font color="blue">[Gardien]</font> • Lors de votre DCT vous gagnez <font color="green">+50hp</font>.</font><br>
						<font color="black"><font color="blue">[Gardien]</font> • Vous avez de nouvelles munitions tel que <font color="green"> Munitions Glacées, Incendiaires et Grenade infirmière</font>.</font><br>
						<font color="black"><font color="blue">[Gardien]</font> • Accès au skin de capitaine <font color="green">UNIQUE</font>.</font><br>
					</p>
				</div>
			</div>-->
		</div>
			
		<script type="text/javascript">
				//<!--
						var anc_onglet = '1';
						change_onglet(anc_onglet);
				//-->
		</script>
	</body>
</html>

<?php
echo '
</div>
</div>';
?>