<?php

// DUPLICATE FUNCTION FOR TESTING
function shortenURL($url) {
	$result=file_get_contents("http://is.gd/create.php?format=simple&url=".$url);
	return $result;
}

include($_SERVER['DOCUMENT_ROOT'].'/email/makescores.php');
include($_SERVER['DOCUMENT_ROOT'].'/email/template.php');

$url = 'http://www.bpllive.com';

$name = 'Alfonso';
$date = 'Saturday 29 March 2014';

$urn = '12385102438965';
$scores = '99,103,66';
//$parts = 'c,d,s';
$parts = $_GET['parts'];

// CLUB ZONE PHOTO
$image_c = 'http://placehold.it/280x165';
$image_c_enc = urlencode($image_c);
$image_c_shortlink = shortenURL($image_c);
$image_c_shortlink_enc = urlencode($image_c_shortlink);

// DREAM TEAM PHOTO
$image_d = 'http://placehold.it/280x165';
$image_d_enc = urlencode($image_d);
$image_d_shortlink = shortenURL($image_d);
$image_d_shortlink_enc = urlencode($image_d_shortlink);

// SKILLS ZONE SCORES
$image_s = $url.\abeautifulsite\makescores(array('filename'=>$urn.'.png','scores'=>$scores));
$image_s_enc = urlencode($image_s);
$image_s_shortlink = shortenURL($image_s);
$image_s_shortlink_enc = urlencode($image_s_shortlink);

$club_name_enc = urlencode('Arsenal');
$club_badge_85 = $url.'/email/img/badges_small/Arsenal.gif';
$club_badge_200 = $url.'/email/img/badges_large/Arsenal.gif';

$body= email_template($parts);
//echo $body;

$body=str_replace("#name#",$name,$body);
$body=str_replace("#date#",$date,$body);
$body=str_replace("#image_c#",$image_c,$body);
$body=str_replace("#image_c_enc#",$image_c_enc,$body);
$body=str_replace("#image_c_shortlink#",$image_c_shortlink,$body);
$body=str_replace("#image_c_shortlink_enc#",$image_c_shortlink_enc,$body);
$body=str_replace("#image_d#",$image_d,$body);
$body=str_replace("#image_d_enc#",$image_d_enc,$body);
$body=str_replace("#image_d_shortlink#",$image_d_shortlink,$body);
$body=str_replace("#image_d_shortlink_enc#",$image_d_shortlink_enc,$body);
$body=str_replace("#image_s#",$image_s,$body);
$body=str_replace("#image_s_enc#",$image_s_enc,$body);
$body=str_replace("#image_s_shortlink#",$image_s_shortlink,$body);
$body=str_replace("#image_s_shortlink_enc#",$image_s_shortlink_enc,$body);
$body=str_replace("#club_name_enc#",$club_name_enc,$body);
$body=str_replace("#club_badge_85#",$club_badge_85,$body);
$body=str_replace("#club_badge_200#",$club_badge_200,$body);

echo $body;