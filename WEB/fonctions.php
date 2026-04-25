<?php
global $conn;
$conn = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_BDD);

if(!$conn) die('Erreur de connexion : ' . mysqli_connect_errno());

mysqli_query($conn, "SET NAMES 'utf8'");

function date_fr($date) {
	$jour = array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"); 
	$mois = array("","Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Decembre"); 
	return $jour[date("w",$date )].' '.date("d",$date).' '.$mois[date("n",$date)] . ' ' . date("Y",$date) . ' à ' . date("H:i",$date);
}
function protect($str) {
    return htmlspecialchars(mysqli_real_escape_string($conn, $str));
}

function filter($in) {
	$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
	$replace = array ('e','a','i','u','o','c','_','_');
	return preg_replace($search, $replace, $in);
}

function mysqli_result($result, $iRow, $field = 0)
{
    if(!mysqli_data_seek($result, $iRow))
        return false;
    if(!($row = mysqli_fetch_array($result)))
        return false;
    if(!array_key_exists($field, $row))
        return false;
    return $row[$field];
}

function RedThis($string, $search)
{
	return str_ireplace($search, '<span style="color:red;">'.$search.'</span>', $string);
}

function VipAffichage($class, $steamid, $time_fin)
{
	if ($time_fin > time())
	{
		$return = '<tr>';
		
		$return .= '
			<td><div class="'.$class.'"><span style="color: #00CC00;"><a href="https://steamcommunity.com/profiles/'.steam2friend($steamid).'" target="_blank">'.GetName($steamid, 'username').'</a> </span></div></td>
		';	
		
		$return .='
				<td><div class="'.$class.'"><span style="color: #00CC00;">'.$steamid.'</a></span></div></td>
		';
		
		if ($time_fin == 2147483647)
		{
			$return .='
					<td style="width: 150px;"><div class="'.$class.'" style="width: 150px;"><span style="color: #00CC00;">LifeTime</span></div></td>
			';
		}
		else
		{
			$return .='
					<td style="width: 150px;"><div class="'.$class.'" style="width: 150px;"><span style="color: #00CC00;">'.date("d/m/Y - H:i:s", $time_fin).'</span></div></td>
			';
		}
				
		$return .= '</tr>';
		return $return;
	}
}

function DayAffichage($class, $time, $steamid)
{
	$return = '
		<tr>
			<td style="width: 150px;"><div class="'.$class.'" style="width: 150px;"><span style="color: #00CC00;"> <img src="./img/icon/famfamfam/hourglass.png" alt> '.date("d/m/Y - H:i:s", $time).'</span></div></td>';	
	
	$return .='
			<td><div class="'.$class.'"><span style="color: #00CC00;"><a href="https://steamcommunity.com/profiles/'.steam2friend($steamid).'" target="_blank">'.GetName($steamid, 'username').'</a></span></div></td>
	';
	
	$return .= '</tr>';
	return $return;
		
}

function TopAffichage($class, $time, $name, $steamid, $i)
{
	$day = $time / 86400;
	$day = floor($day);
	$remainder = $time % 86400;
	$hours = $remainder / 3600;
	$hours = floor($hours);
	$remainder2 = $remainder % 3600;
	$minutes = $remainder2 / 60;
	$minutes = floor($minutes);
	
	if ($day < 10){
		$jrpre = "0";
	}else{
		$jrpre = "";
	}
	
	if ($hours < 10){
		$hrpre = "0";
	}else{
		$hrpre = "";
	}

	if ($minutes < 10){
		$minpre = "0";
	}else{
		$minpre = "";
	}
	
	if ($steamid != "BOT" && $time > 0)
	{		
		if ($i > 0)
		{
			if ($i == 1)
			{
				$i = "<span style=\"color:gold\">1</span>";
			}
			else if ($i == 2)
			{
				$i = "<span style=\"color:grey\">2</span>";
			}
			else if ($i == 3)
			{
				$i = "<span style=\"color:brown\">3</span>";
			}

			$return = '
				<tr>
					<td style="width: 20px;"><div class="'.$class.'" style="width: 20px;"><span style="color: #00CC00;">'.$i.'</span></div></td>';

			$return .= '
					<td style="width: 150px;"><div class="'.$class.'" style="width: 150px;"><span style="color: #00CC00;">'.$jrpre.$day.'jr '.$hrpre.$hours.'hr '.$minpre.$minutes.'m</span></div></td>';
		}
		else
		{
			$return = '
				<tr>
					<td style="width: 150px;"><div class="'.$class.'" style="width: 150px;"><span style="color: #00CC00;">'.$jrpre.$day.'jr '.$hrpre.$hours.'hr '.$minpre.$minutes.'m</span></div></td>';
		}
		
		$return .='
				<td><div class="'.$class.'"><span style="color: #00CC00;"><a href="https://steamcommunity.com/profiles/'.steam2friend($steamid).'" target="_blank">'.utf8_decode($name).'</a></span></div></td>
		';
		
		$return .= '</tr>';
	}
	
	return $return;
}

function LogAffichage($class, $time, $action, $detail, $detail2, $ip, $auteur)
{
	if (strlen($detail2) < 2)	$detail2 = '&zwnj;';
	if ($detail == '-')		$detail = "&zwnj;";
	
	switch ($action) 
	{
	
		case 'Suspension': 
			$couleur = '#FF5757';
			$icon = './img/icon/famfamfam/user_red.png';
			break;
		case 'Retrait Suspension': 
			$couleur = '#99CC33'; 
			$icon = './img/icon/famfamfam/user_green.png';
			break;
		case 'Inscription': 
			$couleur = '#736F6E'; 
			$icon = './img/icon/famfamfam/user_add.png';
			break;
		case 'Creditation': 
			$couleur = '#3399FF'; 
			$icon = './img/icon/famfamfam/coins_add.png';
			break;
		case 'Edition Membre': 
			$couleur = '#FF6633'; 
			$icon = './img/icon/famfamfam/user_edit.png';
			break;
		case 'Achat': 
			$couleur = '#00CC00'; 
			$icon = './img/icon/famfamfam/award_star_gold_3.png';
			break;
		case 'Activation': 
			$couleur = '#00CC00'; 
			$icon = './img/icon/famfamfam/hourglass.png';
			break;
		case 'Prolongation': 
			$couleur = '#00CC00'; 
			$icon = './img/icon/famfamfam/award_star_bronze_3.png';
			break;
		case 'Renouvellement': 
			$couleur = '#00CC00'; 
			$icon = './img/icon/famfamfam/award_star_bronze_3.png';
			break;			
		case 'VIP LifeTime': 
			$couleur = '#FF7400'; 
			$icon = './img/icon/famfamfam/star.png';
			break;
		case 'VIP Gratuit':
			$couleur = '#3399FF';
			$icon = './img/avent.png';
			break;
		case "Suppression d'une news": 
			$couleur = '#990000'; 
			$icon = './img/icon/famfamfam/newspaper_delete.png';
			break;
		case "Ajout d'une news": 
			$couleur = '#990000'; 
			$icon = './img/icon/famfamfam/newspaper_add.png';
			break;
		case "Edit d'une news": 
			$couleur = '#990000'; 
			$icon = './img/icon/famfamfam/newspaper_go.png';
			break;
		case "Suppression de serveur": 
			$couleur = '#336666'; 
			$icon = './img/icon/famfamfam/server_delete.png';
			break;
		case "Ajout de serveur": 
			$couleur = '#336666'; 
			$icon = './img/icon/famfamfam/server_add.png';
			break;
			
	}

	$return = '
		<tr>
			<td><div class="'.$class.'"><span style="color: '.$couleur.';"> <img src="'.$icon.'" alt> '.date("d/m / H:i", $time).'			</span></div></td>';
	
	if (!empty($auteur))			$return .= '<td><div class="'.$class.'"><span style="color: '.$couleur.';"><a href="https://steamcommunity.com/profiles/'.steam2friend(GetPage($auteur, 'steam_id')).'" target="_blank">'.$auteur.'</a></span></div></td>';
	
	$return .=' 	
			<td><div class="'.$class.'"><span style="color: '.$couleur.';">'.$action.'</span></div></td>
			<td><div class="'.$class.'"><span style="color: '.$couleur.';">'.$detail.'</span></div></td>
			<td><div class="'.$class.'"><span style="color: '.$couleur.';">'.$detail2.'</spann></div></td>
	';
	if (!empty($ip))			$return .= '<td><div class="'.$class.'"><span style="color: '.$couleur.';">'.$ip.'</span></div></td>';
	else if ($_GET['p'] == "historique_admin")						$return .= '<td><div class="'.$class.'">&zwnj;</div></td>';
	
	$return .= '</tr>';
	return $return;
		
}

function show_error($erreur)
{
	if (strlen($erreur) > 3) 	
		return '<p> <span style=color:red>' . $erreur . '</span></p>';
}

function GetInfo($user_id, $base)
{
	$conn = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_BDD);

	if(!$conn) die('Erreur de connexion : ' . mysqli_connect_errno());

	mysqli_query($conn, "SET NAMES 'utf8'");
	
	$result = mysqli_query($conn, "SELECT `$base` FROM `".SQL_PREFIX."_users` WHERE id='".$user_id."' LIMIT 0,1");
	if (mysqli_num_rows($result) == 1) 
	{
		$resultat = mysqli_fetch_array($result);
		return $resultat[$base];
	}
	else {
		return NULL;
	}
	
	mysqli_close($conn);
}

function GetName($user_id, $base)
{
	$conn = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_BDD);

	if(!$conn) die('Erreur de connexion : ' . mysqli_connect_errno());

	mysqli_query($conn, "SET NAMES 'utf8'");
	
	$result = mysqli_query($conn, "SELECT `$base` FROM `".SQL_PREFIX."_users` WHERE steam_id='".$user_id."' LIMIT 0,1");
	if (mysqli_num_rows($result) == 1) 
	{
		$resultat = mysqli_fetch_array($result);
		return $resultat[$base];
	}
	else
		return NULL;
	
	mysqli_close($conn);
}

function GetPage($user_id, $base)
{
	$conn = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_BDD);

	if(!$conn) die('Erreur de connexion : ' . mysqli_connect_errno());

	mysqli_query($conn, "SET NAMES 'utf8'");
	
	$result = mysqli_query($conn, "SELECT `$base` FROM `".SQL_PREFIX."_users` WHERE username='".$user_id."' LIMIT 0,1");
	if (mysqli_num_rows($result) == 1) 
	{
		$resultat = mysqli_fetch_array($result);
		return $resultat[$base];
	}
	else
		return NULL;
	
	mysqli_close($conn);
}

//Fonctions du SB
function validate_steam($steam)
{
	if (!defined('STEAM_FORMAT')) define('STEAM_FORMAT', "/^STEAM_[01]:[012345]:[0-9]+$/");
	return preg_match(STEAM_FORMAT, $steam) ? true : false;
}
function BBcode($string) {
    $string = nl2br($string);
    $format_search = array(
        '#\[b\](.*?)\[/b\]#is', // Bold ([b]text[/b]
        '#\[i\](.*?)\[/i\]#is', // Italics ([i]text[/i]
        '#\[u\](.*?)\[/u\]#is', // Underline ([u]text[/u])
        '#\[s\](.*?)\[/s\]#is', // Strikethrough ([s]text[/s])
        '#\[code\](.*?)\[/code\]#is', // Monospaced code [code]text[/code])
        '#\[size=([1-9]|1[0-9]|20)\](.*?)\[/size\]#is', // Font size 1-20px [size=20]text[/size])
        '#\[color=\#?([A-F0-9]{3}|[A-F0-9]{6})\](.*?)\[/color\]#is', // Font color ([color=#00F]text[/color])
        '#\[url=((?:ftp|https?)://.*?)\](.*?)\[/url\]#i', // Hyperlink with descriptive text ([url=http://url]text[/url])
        '#\[url\]((?:ftp|https?)://.*?)\[/url\]#i', // Hyperlink ([url]http://url[/url])
        '#\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]#i', // Image ([img]http://url_to_image[/img])
        '#\:agr:#is', // Agressive
        '#\:ang:#is', // Colere
        '#\:D#is', // Big Smile
        '#\:\)#is', // Smile
        '#\:o#is', // Amazed
        '#\:8#is', // Ignore
        '#\:X#is' // ><
    );
    // The matching array of strings to replace matches with
    $format_replace = array(
        '<b>$1</b>',
        '<em>$1</em>',
        '<span style="text-decoration: underline;">$1</span>',
        '<span style="text-decoration: line-through;">$1</span>',
        '<pre>$1</' . 'pre>',
        '<span style="font-size: $1px;">$2</span>',
        '<span style="color: #$1;">$2</span>',
        '<a href="$1">$2</a>',
        '<a href="$1">$1</a>',
        '<img src="$1" alt="" />',
        '<img src="img/smileys/Aggressive.png" alt="Agressive" />',
        '<img src="img/smileys/Angry.png" alt="Angry" />',
        '<img src="img/smileys/Big Grin.png" alt="Big Grin" />',
        '<img src="img/smileys/Cool.png" alt="Cool" />',
        '<img src="img/smileys/Bored.png" alt="Bored" />',
        '<img src="img/smileys/Giggle.png" alt="Giggle" />',
        '<img src="img/smileys/Dead Bunneh.png" alt="Dead Bunneh" />'
    );
    $string = preg_replace($format_search, $format_replace, $string);
    return $string;
}


function sb_addaccount($id_compte, $pseudonyme, $steam_id)
{
	if (validate_steam($steam_id) && $id_compte > 0)
	{
		$user = ADMIN_PREFIX_SB . htmlentities(filter($pseudonyme), ENT_QUOTES) . ADMIN_FIN_PSEUDO_SB;

		$result = mysqli_query($conn, "SELECT authid FROM sb_admins WHERE authid='".$steam_id."'");
		$result2 = mysqli_query($conn, "SELECT user FROM sb_admins WHERE user='".$user."'");
		if (mysqli_num_rows($result2) == 1) 
			$user = $user . rand(100, 1000) . rand(1, 3);
			
		if (mysqli_num_rows($result) == 0) { // Il n'a aucun compte SB, on lui en crée un
				$insert = "
				INSERT INTO `".SB_PREFIX."_admins` 
				(`aid`, `user`, `authid`, `password`, `gid`, `email`, `validate`, `extraflags`, `immunity`, `srv_group`, `srv_flags`, `srv_password`, `lastvisit`)
				VALUES (NULL, '".mysqli_real_escape_string($conn, $user)."', '".$steam_id."', '".rand(1,100000)."', '-1', '".mysqli_real_escape_string($conn, GetInfo($id_compte, 'mail'))."', '0', '0', '0', '0', '0', NULL, NULL);
				";
				mysqli_query($conn, $insert);
		}
	}
}


// Avoir la durée entre 2 dates en seconde
/* Original : http://formation-php.blogspot.com/2007/09/convertir-un-nombre-de-secondes-en.html */
function transforme($time)
{
	if ($time>=86400)
	{
		$jour = floor($time/86400);			$reste = $time%86400;			 $heure = floor($reste/3600);
		$reste = $reste%3600;				 $minute = floor($reste/60);	$seconde = $reste%60;
		$result = $jour.'j '.$heure.'h '.$minute.'min '.$seconde.'s';
    }
    elseif ($time < 86400 AND $time>=3600)
    {
		$heure = floor($time/3600);
		$reste = $time%3600;

		$minute = floor($reste/60);

		$seconde = $reste%60;
		$result = $heure.'h '.$minute.'min '.$seconde.' s';
    }
    elseif ($time<3600 AND $time>=60)
    {
		$minute = floor($time/60);
		$seconde = $time%60;
		$result = $minute.'min '.$seconde.'s';
    }
    elseif ($time < 60)
		$result = $time.'s';
    return $result;
}
function inscription_mail($mail, $pseudo, $steam_id, $login, $pass)
{
	$content = nl2br(MAIL_INSCRIPTION_CONTENT);
    $format_search = array(
        '{SITE}',
        '{SITE_URL}',
        '{IDENTIFIANT}',
		'{MOT_DE_PASSE}',
		'{FORUM_URL}',
		'{SOURCEBANS_URL}',
		'{GROUPE_STEAM}',
		'{CONTACT_RESPONSABLE}',
		'{NOM_TEAM}',
		'{PSEUDO}',
		'{STEAM_ID}'
    );

    $format_replace = array(
		NOM_SITE,
		'<a href="'.URL_SITE.'">'.URL_SITE.'</a>',
		$login,
		$pass,
		'<a href="'.URL_FORUM.'">'.URL_FORUM.'</a>',
		'<a href="'.URL_SOURCEBANS.'">'.URL_SOURCEBANS.'</a>',
		'<a href="'.GROUPE_STEAM.'">'.GROUPE_STEAM.'</a>',
		'<a href="mailto:'.MAIL_CONTACT.'">'.MAIL_CONTACT.'</a>',
		NOM_TEAM,
		$pseudo,
		$steam_id
    );
	
    $content = str_replace($format_search, $format_replace, $content);
	mail_envois($mail, MAIL_INSCRIPTION_TITLE, $content);
}
function mail_envois($mail, $sujet, $contenu)
{	
	$headers ='From: "'.MAIL_AUTEUR.'" <"'.MAIL_REPLY.'">'."\n";
	$headers .='Reply-To: '.MAIL_REPLY.''."\n";
	$headers .='Content-Type: text/html; charset="UTF-8"'."\n";
	$headers .='Content-Transfer-Encoding: 8bit';
	 
	mail($mail, $sujet, $contenu, $headers);
}

function ListeStaff($class, $steamid, $level)
{	
	$return = '<tr>
			<td><center><div class="'.$class.'"><a href="https://steamcommunity.com/profiles/'.steam2friend($steamid).'" target="_blank"><img src="http://steamsignature.com/status/french/'.steam2friend($steamid).'.png" alt=""/></a><a href="steam://friends/add/'.steam2friend($steamid).'"><img src="http://steamsignature.com/AddFriend.png" alt="Ajouter en ami"/></a></div></td></center>
			';
	
	if ($steamid == ROOT_SITE) $return .= '<td><center><div class="'.$class.'"><span style="color: #FF0000;">'.LEVEL_NAME_ROOT.'</span></div></td></tr></center>';
	else if ($level == 5) $return .= '<td><center><div class="'.$class.'"><span style="color: #2E64FE;">'.LEVEL_NAME_5.'</span></div></td></tr></center>';
	else if ($level == 4) $return .= '<td><center><div class="'.$class.'"><span style="color: #AEB404;">'.LEVEL_NAME_4.'</span></div></td></tr></center>';
	else if ($level == 3) $return .= '<td><center><div class="'.$class.'"><span style="color: #FE642E;">'.LEVEL_NAME_3.'</span></div></td></tr></center>';
	else if ($level == 2) $return .= '<td><center><div class="'.$class.'"><span style="color: #FF00FF;">'.LEVEL_NAME_2.'</span></div></td></tr></center>';
	else if ($level == 1) $return .= '<td><center><div class="'.$class.'"><span style="color: #00CC00;">'.LEVEL_NAME_1.'</span></div></td></tr></center>';

	return $return;
}

function steam2friend($steam_id) {
    $steam_id=strtolower($steam_id);
    if (substr($steam_id,0,7)=='steam_1' || substr($steam_id,0,7)=='steam_0') {
        $tmp=explode(':',$steam_id);
        if ((count($tmp)==3) && is_numeric($tmp[1]) && is_numeric($tmp[2])){
            return str_replace(".0000000000", "",bcadd((($tmp[2]*2)+$tmp[1]),'76561197960265728'));
        }
		else return false;
	}
	else {
		return false;
	}
} 
	
function random($car) {
	$string = "";
	$chaine = "abcdefghijklmnpqrstuvwxy123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	srand((double)microtime()*1000000);
	for($i=0; $i<$car; $i++) {
		$string .= $chaine[rand()%strlen($chaine)];
	}
	return $string;
}
?>