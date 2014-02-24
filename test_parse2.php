<?php
require_once('Connections/php.mysql.class.php');
error_reporting(E_ALL);

// DUPLICATE FUNCTION FOR TESTING
function shortenURL($url) {
	$result=file_get_contents("http://is.gd/create.php?format=simple&url=".$url);
	return $result;
}

include($_SERVER['DOCUMENT_ROOT'].'/email/makescores.php');
include($_SERVER['DOCUMENT_ROOT'].'/email/template.php');

$db= new MySQL(DB,DBUSER,DBPASS);

$user=$db->ExecuteSQL("SELECT
c.id AS id,
c.issynced AS issynced,
c.urn AS uniqueid,
c.firstname AS firstname,
c.lastname AS lastname,
c.email AS email,
teams.team_name AS team_name,
teams.icon_85 AS icon_85,
teams.icon_200 AS icon_200,
c.added AS added,
c.last_email_event AS last_email_event,
c.server_result AS server_result,
r.game0 AS game0,
r.game1 AS game1,
r.game2 AS game2,
userphoto.filename AS imgtrophy,
dreamteams.filename AS imgdreamteam
from ((((`users` `c` left join `scores` `r` on((`r`.`urn` = `c`.`urn`))) left join `userphoto` on((`c`.`urn` = `userphoto`.`urn`))) left join `dreamteams` on((`c`.`urn` = `dreamteams`.`urn`))) join `teams` on((`c`.`team_id` = `teams`.`id`)))
WHERE
c.urn='A1393032123627Z'
");

var_dump($user);
echo "error:".$db->lastError;
if (!$user) {
	die("mysql error:"+$db->lastError+" query:"+$db->lastQuery);
} else {
	var_dump($user);
	echo "<br/>name:".$user['firstname'];
	$urn=$user['uniqueid'];
	echo "total rows:".$db->records;
}
echo "<br/><br/>";

// Static Vars
$url = 'http://www.bpllive.com';

// User Vars
$name = ucfirst($user['firstname']);
$date = date("l j F Y",strtotime($user['added']));
//$urn = '12385102438965';
$teamname = $user['team_name'];
//$clubzonephoto = 'http://www.bpllive.com//280x165';
$clubzonephoto=makeThumb("bplphotos/".$user['imgtrophy'],280,"sitethumbs/trophy");

//$dreamteamphoto = 'http://placehold.it/280x165';
$dreamteamphoto =makeThumbPNG("bplphotos/".$user['imgdreamteam'],280,"sitethumbs/dream");

$game0 = trim($user['game0']);
$game1 = trim($user['game1']); //Power
$game2 = trim($user['game2']); //Control

// Games
$games = '';
$scores = '';
if($game0){
	$games .= 'a,';
	$scores .= $game0.',';
}
if($game1){
	$games .= 'p,';
	$scores .= $game1.',';
}
if($game2){
	$games .= 'c,';
	$scores .= $game2.',';
}
$games = substr($games, 0, -1);
$scores = substr($scores, 0, -1);

// Parts
$parts = '';
if($clubzonephoto) $parts .= 'c,';
if($dreamteamphoto) $parts .= 'd,';
if($scores) $parts .= 's,';
$parts = substr($parts, 0, -1);

// Get the template
$body= email_template($parts);

// Start replacing template parts ->
$body=str_replace("#name#",$name,$body);
$body=str_replace("#date#",$date,$body);

// Club Zone Photo
if($clubzonephoto){
	$image_c = $clubzonephoto;
	$image_c_enc = urlencode($image_c);
	$image_c_shortlink = shortenURL($image_c);
	$image_c_shortlink_enc = urlencode($image_c_shortlink);
	$body=str_replace("#image_c#",$image_c,$body);
	$body=str_replace("#image_c_full#",$url."/bplphotos/".basename($image_c),$body);
	$body=str_replace("#image_c_enc#",$image_c_enc,$body);
	$body=str_replace("#image_c_shortlink#",$image_c_shortlink,$body);
	$body=str_replace("#image_c_shortlink_enc#",$image_c_shortlink_enc,$body);
}

// Dream Team Photo
if($dreamteamphoto){
	$image_d = $dreamteamphoto;
	$image_d_enc = urlencode($image_d);
	$image_d_shortlink = shortenURL($image_d);
	$image_d_shortlink_enc = urlencode($image_d_shortlink);
	$body=str_replace("#image_d#",$image_d,$body);
	$body=str_replace("#image_d_enc#",$image_d_enc,$body);
	$body=str_replace("#image_d_shortlink#",$image_d_shortlink,$body);
	$body=str_replace("#image_d_shortlink_enc#",$image_d_shortlink_enc,$body);
}

// Skills Zone Scores
if($scores){
	$image_s = $url.\abeautifulsite\makescores(array('filename'=>$urn.'.png','scores'=>$scores,'games'=>$games));
	$image_s_enc = urlencode($image_s);
	$image_s_shortlink = shortenURL($image_s);
	$image_s_shortlink_enc = urlencode($image_s_shortlink);
	$body=str_replace("#image_s#",$image_s,$body);
	$body=str_replace("#image_s_enc#",$image_s_enc,$body);
	$body=str_replace("#image_s_shortlink#",$image_s_shortlink,$body);
	$body=str_replace("#image_s_shortlink_enc#",$image_s_shortlink_enc,$body);
}

// Club Badges
$club_name_enc = urlencode($teamname);
$club_badge_85 = $url.'/email/img/badges_small/'.str_replace(' ', '-', $teamname).'.gif';
$club_badge_200 = $url.'/email/img/badges_large/'.str_replace(' ', '-', $teamname).'.gif';
$body=str_replace("#club_name_enc#",$club_name_enc,$body);
$body=str_replace("#club_badge_85#",$club_badge_85,$body);
$body=str_replace("#club_badge_200#",$club_badge_200,$body);

function makeThumb($filename,$newwidth,$destpath) {
	global $url;
/*
 * PHP GD
 * resize an image using GD library
 */

if(!file_exists($filename)) {
	die("file non trovato".$filename);
}

list($width,$height) = getimagesize($filename);



if(!$width) {
	return false;
}

$ratio=$width/$height;
$newheight=$newwidth*$ratio;


// Load
$thumb = imagecreatetruecolor($newwidth, $newheight);
$source = imagecreatefromjpeg($filename);

// Resize
imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

// Output and free memory
//the resized image will be 400x300
$newpath=$destpath."/".basename($filename);
if(!imagejpeg($thumb,$newpath,90)) {
	return false;
} else {
	return $url."/".$newpath;
}

}

function makeThumbPNG($filename,$newwidth,$destpath) {
global $url;
/*
 * PHP GD
 * resize an image using GD library
 */

if(!file_exists($filename)) {
	die("file non trovato".$filename);
}



// Load
$thumb = imagecreatetruecolor(286,165);
imagealphablending($thumb,true);

 $backgroundColor = imagecolorallocate($thumb, 255, 255, 255);
imagefill($thumb, 0, 0, 0xffffff);

$source = imagecreatefrompng($filename);

if(!imagecolortransparent($source,0xffffff)) {
	die("no trasp");
}

// Resize

imagecopyresampled($thumb, $source, 0, 0, 0, 0, 236, 165, 1000, 700);

// Output and free memory
//the resized image will be 400x300
$newpath=$destpath."/".basename($filename);
if(!imagepng($thumb,$newpath)) {
	return false;
} else {
	return $url."/".$newpath;
}

}

// BODY READY ->
echo $body;