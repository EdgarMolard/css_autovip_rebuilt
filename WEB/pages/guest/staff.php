<?php
	echo '
	<div class="box-out">
		<div class="box-in">
			<div class="box-head"><h1>Le Staff</h1></div>
			<div class="box-content">
			<div class="table">
				<table>
					<thead>
						<tr>
							<td><div>Pseudo</div></td>
							<td><div>Fonction</div></td>
						</tr>
					</thead>
					<tbody>';
	$StaffSql = mysqli_connect("127.0.0.1", "Sourcebans", "Sourcebans", "Sourcebans");
	if(!$StaffSql) die('Erreur de connexion : ' . mysqli_connect_errno());
	mysqli_query($StaffSql, "SET NAMES 'utf8'");
	//$requete = mysqli_query($conn, "SELECT authid, srv_group FROM `sb_admins` WHERE srv_group != NULL ORDER BY user DESC");
	$requete = mysqli_query($conn, "SELECT * FROM `sb_admins`");
	$class = "even";
	while($row = mysqli_fetch_array($requete)) {
		echo $row['authid'];
		//echo ListeStaff($class, $row['authid'], $row['srv_group']);
	}
	
	mysqli_close($StaffSql);
	
	echo '	</div><!-- end div .box-content -->
		</div><!-- end div .box-in -->
	</div><!-- end div .box-out -->
	';	
?>