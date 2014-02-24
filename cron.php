<!doctype html>
<html>
<head>
<!--meta http-equiv="refresh" content="5"-->
<meta charset="utf-8">
<title>Cron job</title>
</head>
<body>

<?php
die($_SERVER['DOCUMENT_ROOT']);

$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);
echo "server root:".$_SERVER['DOCUMENT_ROOT'];

chdir(dirname(__FILE__));
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/php.mysql.class.php');

//
global $rootDir;

global $currentURN;

$rootDir="/var/www/vhosts/wassermanexperience.com/sunglasshut/";
$filename=$rootDir."status.txt";
$dirContent=array();
echo $_SERVER['DOCUMENT_ROOT'];

//print "difference is:".count($diff)."\n";

//if(count($diff)>0) {
	$xmlList=rglob("./batchincoming",'/\.xml$/i');
	
	$storelog=false;
	
	//$result=$db->Select("users",array("urn"=>$xml->urn));
	ob_start();
	for($i=0;$i<count($xmlList);$i++) {
		//$basename=stristr(
		echo "now parsing ".$xmlList[$i]."<br/>";
		$currentXML=parseXML("./batchincoming/".$xmlList[$i]);
		
		

		
		if((file_exists("./batchincoming/".$currentXML->destFileName) && (file_exists("./batchincoming/".$currentXML->destFileName) || file_exists("./photo/".$currentXML->destFileName)))) {
			$storelog=true;
			echo "file found";
			$result=processFile($currentXML);
			if($result==true) {
				$currentURN=$currentXML->urn;
				break;
			}
			
		} else if((file_exists("./video/".$currentXML->destFileName) && file_exists("./video/".changeExt($currentXML->destFileName)) && file_exists("./squarethumbs/".changeExt($currentXML->destFileName)) )) {
			$storelog=true;
			$result=processFile($currentXML);
			if($result==true) {
				$currentURN=$currentXML->urn;
				break;
			}
		}
		

		

		
	}
	
	ob_end_flush(); // closing the inner envelope will activate URL rewriting
	$bufferdata = ob_get_contents();
	ob_end_clean();
	
	echo "data:".$bufferdata;
	
	echo "<br/>current urn:$currentURN<br/>";
	
	if($storelog) {
		echo "storelog true";
		$db= new MySQL(DB,DBUSER,DBPASS);
		$risultato=$db->Insert(array("urn"=>$currentURN,"dump"=>$bufferdata),"cron_logs");
	} else {
		echo "storelog false";
	}
	
	if(!$risultato) {
		echo "DB LOGGING ERROR:".$db->lastError." query was:".$db->lastQuery;
	}
	
	//$db->CloseConnection();
	
	function changeExt($fileName) {
		return preg_replace('"\.mp4$"', '.jpg', $fileName);
	}

	function processFile($xml) {
		
		$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);
		
		echo "process file func<br/>";
		var_dump($xml);
		

		
		$db= new MySQL(DB,DBUSER,DBPASS);
		$result=$db->InsertUpdate(array("email_hash"=>md5($xml->email),"mediatype"=>$xml->mediaType,"post_type"=>$xml->usertype,"urn"=>$xml->urn),"stats",array("urn"=>$xml->urn));
		if(!$result) {
			die("error inserting stats:".$db->lastError);
		}
		$db->CloseConnection();

		$greeting=trim($xml->personalNote);
		if($greeting!="") {
			echo "note found!<br/>";
		}
		if($xml->mediaType=="jpgfile") {
			echo "jpg found!<br/>";
			
			makeSquareThumb($xml->destFileName);
			
			
			
			switch ($xml->usertype) {
				case "EMAIL":
				echo "this is email<br/>";
				emailUser($xml);
				return true;
				break;
				case "FACEBOOK":
				facebookUser($xml);
				echo "this is facebook<br/>";
				return true;
				break;
				case "TWITTER":
				echo "this is twitter<br/>";
				tweetUser($xml);
				return true;
				break;
			}
			
		} else if($xml->mediaType=="videofile") {
			switch ($xml->usertype) {
				case "EMAIL":
				echo "this is email<br/>";
				emailVideoUser($xml);
				return true;
				break;
				case "FACEBOOK":
				//facebookUser($xml);
				echo "this is facebook<br/>";
				facebookVideoUser($xml);
				return true;
				break;
				case "TWITTER":
				echo "this is twitter<br/>";
				tweetVideoUser($xml);
				return true;
				break;
			}
		}
		
		
	}

function tweetVideoUser($xml) {
	
	if(!isset($_SESSION)) {
		session_start();
	}

	
	chdir(dirname(__FILE__));
	
	require_once($_SERVER['DOCUMENT_ROOT']."/twitter/twitteroauth/twitteroauth.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/twitter/config.php");
	
	if($xml->mediaType=="jpgfile") {
		$label="photo";
	} else {
		$label="video";
	}
	
	$rewritetwit="https://www.barclaysyouarefootball.uk.com/twittervideo/";
	

	$shortenurl=shortenURL($rewritetwit.$xml->urn);
	
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->urn.".xml",$_SERVER['DOCUMENT_ROOT']."/batchprocessed/".$xml->urn.".xml");
	//rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->destFileName,$_SERVER['DOCUMENT_ROOT']."/".$label."/".$xml->destFileName);
	

	
	$db= new MySQL(DB,DBUSER,DBPASS);
	
	$result=$db->Update("users",array("personalnote"=>$xml->personalNote,"tablet_id"=>$xml->tablet_id),array("urn"=>$xml->urn));
	
	$user=$db->Select("users",array("urn"=>$xml->urn));
	
	if(!$user) {
		die("mysql error".$db->lastError);
	}
	
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $user['token'], $user['token_secret']);

	/* If method is set change API call made. Test is called by default. */
	$content = $connection->get('account/verify_credentials');

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
$resulto=$connection->post('statuses/update', array('status' => 'We love cheering from the stands, come rain or shine! #YouAreFootball '.$shortenurl,
"lat"=>$xml->lat,"long"=>$xml->long,"display_coordinates"=>"true"));

	$resultmessage="SUCCESS";
	
	$tweetid=0;
	
	if(isset($resulto->errors)) {
		$resultmessage=print_r($resulto->errors,true);
		echo "ci sono errori<br/>";
		echo "messaggio:".$resulto->errors[0]->message;
	} else {
		$tweetid=$resulto->id_str;
	}
	
		$result=$db->Insert(array("shorturl"=>$shortenurl,"mediatype"=>$xml->mediaType,"urn"=>$xml->urn,"post_type"=>$xml->usertype,"filename"=>$xml->destFileName,"publish_result"=>$resultmessage,"social_id"=>$resulto->id_str),"userphoto",true);
	if(!$result) {
		echo "error inserting userphoto on FB:".$db->lastError;
	}
	
	$db->CloseConnection();
	
}


function facebookVideoUser($xml) {
	chdir(dirname(__FILE__));
	
	if($xml->mediaType=="jpgfile") {
		$label="photo";
	} else {
		$label="video";
	}
	
	
	//rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->destFileName,$_SERVER['DOCUMENT_ROOT']."/".$label."/".$xml->destFileName);
	
	require_once ($_SERVER['DOCUMENT_ROOT'].'/php-sdk/src/facebook.php');

// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
	  'appId'  => '496676837086475',
	  'secret' => '28d3454e73bc048ef0dd5b61529b9a58',
	));

	$db= new MySQL(DB,DBUSER,DBPASS);
	
	$result=$db->Update("users",array("personalnote"=>$xml->personalNote,"tablet_id"=>$xml->tablet_id),array("urn"=>$xml->urn));
	
	$user=$db->Select("users",array("urn"=>$xml->urn));
	
	if(!$user) {
		die("mysql error".$db->lastError);
	}
	
	$facebook->setAccessToken($user['token']);


	try {
		$fbuser = $facebook->api('/me', 'get');
	} catch (FacebookApiException $e) {
		echo "result=ERROR&message=".urlencode($e->getMessage());
		die();
	}
	
	$facebook->setFileUploadSupport(true);
	
	$args = array('title' => 'My video from the match on Saturday','description'=>'We love cheering from the stands, come rain or shine! #YouAreFootball'); //,'place'=>'50.712560964795'
//$args = array('message' => 'Nézd meg, ahogy a Vodafone McLaren Mercedes F1 versenyautójában ülök a Vodafone Kezdj El Valami Újat Hétvégén!');
	$args['videoData'] = '@' . realpath($_SERVER['DOCUMENT_ROOT']."/".$label."/".basename($xml->destFileName));

	var_dump($fbuser);

	$resultmessage="SUCCESS";
	
	try {
		$data = $facebook->api('/me/videos', 'post', $args); 
	} catch (FacebookApiException $e) {
		$resultmessage=$e->getMessage();
		
		echo "ERROR FB POST:".$e->getMessage()."<br/>";
		
	}
	echo "result of publish video:<br/>";
	var_dump($data);
	
	if(isset($data['id'])) {
		$post_url = "https://graph.facebook.com/".$data['id']."/tags/"
        . $user['id']."?access_token=".$user['token']."&x=" . 68 ."&y=65"
         ."&method=POST";
        $response = file_get_contents($post_url);
		echo "******** response of taging".$response."<br/>";
	}

	if(isset($data['id'])) {
		$postId=$data['id'];
	} else {
		$postId=0;
	}

	$result=$db->Insert(array("mediatype"=>$xml->mediaType,"urn"=>$xml->urn,"post_type"=>$xml->usertype,"filename"=>$xml->destFileName,"publish_result"=>$resultmessage,"social_id"=>$postId),"userphoto",true);
	if(!$result) {
		echo "error inserting userphoto on FB:".$db->lastError;
	}
	$db->CloseConnection();
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->urn.".xml",$_SERVER['DOCUMENT_ROOT']."/batchprocessed/".$xml->urn.".xml");
}

function makeSquareThumb($image) {
	
	$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);
	
	$root=$_SERVER['DOCUMENT_ROOT'];
	echo "server root:".$root;
	
	echo "making thumb for $image<br/>";
	
	if((file_exists("./thumbsincoming/".$image))) {
		echo "raw thumbnail data found</br>";
	$exeout="thumbnail row 1".shell_exec("convert $root/thumbsincoming/$image -resize 200x200^ -gravity center -crop 200x200+0+0 $root/temp/thumbphase1.png  2>&1");
		
	} else {
			$exeout="thumbnail row 1".shell_exec("convert $root/batchincoming/$image -resize 200x200^ -gravity center -crop 200x200+0+0 $root/temp/thumbphase1.png  2>&1");
		echo "no raw thumbnail, using processed pic</br>";
	}
	$exeout.="thumbnail row 2".shell_exec("convert $root/temp/thumbphase1.png $root/assets/overlay_small_thumb.png -composite -quality 90%  $root/squarethumbs/$image  2>&1");
	
	@unlink("$root/temp/thumbphase1.png");
	
	@unlink("$root/thumbsincoming/$image");
	
	echo "result of thumb conv:".$exeout."<br/>";
	


}


function emailVideoUser($xml) {
	chdir(dirname(__FILE__));
	

	$db= new MySQL(DB,DBUSER,DBPASS);
	$result=$db->Select("users",array("urn"=>$xml->urn));
	
	if(!$result) {
		die("mysql error:".$db->lastError);
	}
	echo "rows found:".$db->records."<br/>";
	if($db->records==0) {
		echo "insert new record<br/>";
		$result=$db->Insert(array("usertype"=>$xml->usertype,"personalnote"=>$xml->personalNote,"urn"=>$xml->urn,"current_location"=>$xml->current_location,"firstname"=>$xml->firstname,"lastname"=>$xml->lastname,"email"=>$xml->email,"mobile"=>$xml->mobile,"added"=>$xml->added,"tablet_id"=>$xml->tablet_id),"users",true);
	} else {
		echo "update record<br/>";
		$result=$db->Update("users",array("personalnote"=>$xml->personalNote,"tablet_id"=>$xml->tablet_id),array("urn"=>$xml->urn));
	}
	if(!$result) {
		echo "db error".$db->lastError."<br/>";
	}
	echo "emailing user<br/>";
	print_r($xml);
	
	if($xml->mediaType=="jpgfile") {
		$label="photo";
		$link="https://www.barclaysyouarefootball.uk.com/showphoto.php?urn=";
		$rewriteurl="https://www.barclaysyouarefootball.uk.com/showphoto/";
		$rewritefb="https://www.barclaysyouarefootball.uk.com/fbphoto/";
		$rewritetwit="https://www.barclaysyouarefootball.uk.com/twitterphoto/";
	} else {
		$label="video";
		$link="https://www.barclaysyouarefootball.uk.com/showvideo.php?urn=";
		$rewriteurl="https://www.barclaysyouarefootball.uk.com/showvideo/";
		$rewritefb="https://www.barclaysyouarefootball.uk.com/fbvideo/";
		$rewritetwit="https://www.barclaysyouarefootball.uk.com/twittervideo/";
	}

	$shortenurl=shortenURL($rewritetwit.$xml->urn);

	require_once($_SERVER['DOCUMENT_ROOT'].'/phpmailer/class.phpmailer.php');
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"
	
	$mail->SMTPDebug = false;
	$mail->do_debug = 0;

	$mail->IsSMTP();
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Host       = "ssl://smtp.sendgrid.net"; // sets the SMTP server
	$mail->Port       = 465;   
	$mail->Username   = "wasserman"; // SMTP account username
	$mail->Password   = "k0st0golov";        // SMTP account password

	$mail->AddReplyTo("noreply@barclaysyouarefootball.uk.com","Barclays");
	
	$mail->SetFrom("noreply@barclaysyouarefootball.uk.com","Barclays");
	$mail->CharSet="UTF-8";
	
	$mail->AddAddress($xml->email,$xml->firstname." ".$xml->lastname);
	
	$mail->Subject    = "Your Barclays Premier Laegue supporter ".$label." ";
	$mail->AltBody    = "Please use an html compatible viewer!\n\n"; // optional, comment out and test
	
	$body=file_get_contents($_SERVER['DOCUMENT_ROOT']."/emailer/videomailtemplate.html");
	
	$body=str_replace("#name#",$xml->firstname,$body);
	$body=str_replace("#mediatype#",$label,$body);
	$body=str_replace("#link#",$rewriteurl.$xml->urn,$body);
	//$body=str_replace("#filename#",$_POST['file']."&urn=".$row_user['urn'],$body);
	$body=str_replace("#urn#",$xml->urn,$body);
	$body=str_replace("#filename#",preg_replace('"\.mp4$"', '.jpg', $xml->destFileName),$body);
	//$mail->AddBCC("lisophorm@gmail.com","Alfonso");
	$mail->MsgHTML($body);
	
	$mail->AddCustomHeader(sprintf( 'X-SMTPAPI: %s', '{"unique_args": {"urn":"'.$xml->urn.'"},"category": "YouAreFootball"}' ) );
	
	//$basefile=urldecode(basename($_POST['file']));
	//$mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT']."/rendered/".$basefile,"logo_2u",$basefile); // attachment
	
	if(!$mail->Send()) {
	  $emailresult= $mail->ErrorInfo;
	  //echo("result=ERROR&message=".urlencode("Error while sending email:".$result));
	} else {
	 $emailresult="SUCCESS";
	}
	
	$result=$db->Insert(array("shorturl"=>$shortenurl,"mediatype"=>$xml->mediaType,"urn"=>$xml->urn,"post_type"=>$xml->usertype,"filename"=>$xml->destFileName,"publish_result"=>$emailresult),"userphoto",true);
	if(!$result) {
		echo "error inserting email:".$db->lastError;
	}
	$db->CloseConnection();
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->urn.".xml",$_SERVER['DOCUMENT_ROOT']."/batchprocessed/".$xml->urn.".xml");
	return true;
	
}

function emailUser($xml) {
	chdir(dirname(__FILE__));
	
	createThumbs($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->destFileName,$_SERVER['DOCUMENT_ROOT']."/thumbs/".$xml->destFileName,400);
	
	$db= new MySQL(DB,DBUSER,DBPASS);
	$result=$db->Select("users",array("urn"=>$xml->urn));
	
	if(!$result) {
		die("mysql error:".$db->lastError);
	}
	echo "rows found:".$db->records."<br/>";
	if($db->records==0) {
		echo "insert new record<br/>";
		$result=$db->Insert(array("usertype"=>$xml->usertype,"personalnote"=>$xml->personalNote,"urn"=>$xml->urn,"current_location"=>$xml->current_location,"firstname"=>$xml->firstname,"lastname"=>$xml->lastname,"email"=>$xml->email,"mobile"=>$xml->mobile,"added"=>$xml->added,"tablet_id"=>$xml->tablet_id),"users",true);
	} else {
		echo "update record<br/>";
		$result=$db->Update("users",array("personalnote"=>$xml->personalNote,"tablet_id"=>$xml->tablet_id),array("urn"=>$xml->urn));
	}
	if(!$result) {
		echo "db error".$db->lastError."<br/>";
	}
	echo "emailing user<br/>";
	print_r($xml);
	
	if($xml->mediaType=="jpgfile") {
		$label="photo";
		$link="https://www.barclaysyouarefootball.uk.com/showphoto.php?urn=";
		$rewriteurl="https://www.barclaysyouarefootball.uk.com/showphoto/";
		$rewritefb="https://www.barclaysyouarefootball.uk.com/fbphoto/";
		$rewritetwit="https://www.barclaysyouarefootball.uk.com/twitterphoto/";
	} else {
		$label="video";
		$link="https://www.barclaysyouarefootball.uk.com/showvideo.php?urn=";
		$rewriteurl="https://www.barclaysyouarefootball.uk.com/showvideo/";
		$rewritefb="https://www.barclaysyouarefootball.uk.com/fbphoto/";
		$rewritetwit="https://www.barclaysyouarefootball.uk.com/twitterphoto/";
	}

	$shortenurl=shortenURL($rewritetwit.$xml->urn);

	require_once($_SERVER['DOCUMENT_ROOT'].'/phpmailer/class.phpmailer.php');
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"
	
	$mail->SMTPDebug = false;
	$mail->do_debug = 0;

	$mail->IsSMTP();
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Host       = "ssl://smtp.sendgrid.net"; // sets the SMTP server
	$mail->Port       = 465;   
	$mail->Username   = "wasserman"; // SMTP account username
	$mail->Password   = "k0st0golov";        // SMTP account password

	$mail->AddReplyTo("noreply@barclaysyouarefootball.uk.com","Barclays");
	
	$mail->SetFrom("noreply@barclaysyouarefootball.uk.com","Barclays");
	$mail->CharSet="UTF-8";
	
	$mail->AddAddress($xml->email,$xml->firstname." ".$xml->lastname);
	
	$mail->Subject    = "Your Barclays Premier League supporter ".$label." ";
	//$mail->AddBCC("lisophorm@gmail.com","Alfonso");
	$mail->AltBody    = "Please use an html compatible viewer!\n\n"; // optional, comment out and test
	
	$body=file_get_contents($_SERVER['DOCUMENT_ROOT']."/emailer/mailtemplate_onsite.html");
	
	$firstCap=ucfirst($xml->firstname);
	$body=str_replace("#name#",$firstCap,$body);
	$body=str_replace("#mediatype#",$label,$body);
	$body=str_replace("#link#",$rewriteurl.$xml->urn,$body);
	//$body=str_replace("#filename#",$_POST['file']."&urn=".$row_user['urn'],$body);
	$body=str_replace("#urn#",$xml->urn,$body);
	$body=str_replace("#filename#",$xml->destFileName,$body);
	
	$mail->MsgHTML($body);
	
	$mail->AddCustomHeader(sprintf( 'X-SMTPAPI: %s', '{"unique_args": {"urn":"'.$xml->urn.'"},"category": "YouAreFootball"}' ) );

	//$basefile=urldecode(basename($_POST['file']));
	//$mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT']."/rendered/".$basefile,"logo_2u",$basefile); // attachment
	
	/*if(!$mail->Send()) {
	  $emailresult= $mail->ErrorInfo;

	} else {
	 $emailresult="SUCCESS";
	}*/
	
	$emailresult="PROCESSED";
	
	$result=$db->Insert(array("shorturl"=>$shortenurl,"mediatype"=>$xml->mediaType,"urn"=>$xml->urn,"post_type"=>$xml->usertype,"filename"=>$xml->destFileName,"publish_result"=>$emailresult),"userphoto",true);
	if(!$result) {
		echo "error inserting email:".$db->lastError;
	}
	$db->CloseConnection();
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->urn.".xml",$_SERVER['DOCUMENT_ROOT']."/batchprocessed/".$xml->urn.".xml");
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->destFileName,$_SERVER['DOCUMENT_ROOT']."/".$label."/".$xml->destFileName);
	return true;
	
}

function tweetUser($xml) {
	
	if(!isset($_SESSION)) {
		session_start();
	}

	
	chdir(dirname(__FILE__));
	
	require_once($_SERVER['DOCUMENT_ROOT']."/twitter/twitteroauth/twitteroauth.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/twitter/config.php");
	
	if($xml->mediaType=="jpgfile") {
		$label="photo";
	} else {
		$label="video";
	}
	
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->urn.".xml",$_SERVER['DOCUMENT_ROOT']."/batchprocessed/".$xml->urn.".xml");
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->destFileName,$_SERVER['DOCUMENT_ROOT']."/".$label."/".$xml->destFileName);
	

	
	$db= new MySQL(DB,DBUSER,DBPASS);
	
	$result=$db->Update("users",array("personalnote"=>$xml->personalNote,"tablet_id"=>$xml->tablet_id),array("urn"=>$xml->urn));
	
	$user=$db->Select("users",array("urn"=>$xml->urn));
	
	if(!$user) {
		die("mysql error".$db->lastError);
	}
	
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $user['token'], $user['token_secret']);

	/* If method is set change API call made. Test is called by default. */
	$content = $connection->get('account/verify_credentials');
	
	if(!file_exists("./photo/".$xml->destFileName)) {
		die("photo file ".$xml->destFileName." not found");
	}

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
$resulto=$connection->post('statuses/update_with_media', array('status' => 'We love cheering from the stands, come rain or shine! #YouAreFootball','media[]'  => file_get_contents("./photo/".$xml->destFileName),
"lat"=>$xml->lat,"long"=>$xml->long,"display_coordinates"=>"true"));

	$resultmessage="SUCCESS";
	
	$tweetid=0;
	
	if(isset($resulto->errors)) {
		$resultmessage=print_r($resulto->errors,true);
		echo "ci sono errori<br/>";
		echo "messaggio:".$resulto->errors[0]->message;
	} else {
		$tweetid=$resulto->id_str;
	}
	
		$result=$db->Insert(array("mediatype"=>$xml->mediaType,"urn"=>$xml->urn,"post_type"=>$xml->usertype,"filename"=>$xml->destFileName,"publish_result"=>$resultmessage,"social_id"=>$resulto->id_str),"userphoto",true);
	if(!$result) {
		echo "error inserting userphoto on FB:".$db->lastError;
	}
	
	$db->CloseConnection();
	
}

function facebookUser($xml) {
	chdir(dirname(__FILE__));
	
	if($xml->mediaType=="jpgfile") {
		$label="photo";
	} else {
		$label="video";
	}
	
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->urn.".xml",$_SERVER['DOCUMENT_ROOT']."/batchprocessed/".$xml->urn.".xml");
	rename($_SERVER['DOCUMENT_ROOT']."/batchincoming/".$xml->destFileName,$_SERVER['DOCUMENT_ROOT']."/".$label."/".$xml->destFileName);
	
	require_once ($_SERVER['DOCUMENT_ROOT'].'/php-sdk/src/facebook.php');

// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
	  'appId'  => '496676837086475',
	  'secret' => '28d3454e73bc048ef0dd5b61529b9a58',
	));

	$db= new MySQL(DB,DBUSER,DBPASS);
	
	$result=$db->Update("users",array("personalnote"=>$xml->personalNote,"tablet_id"=>$xml->tablet_id),array("urn"=>$xml->urn));
	
	$user=$db->Select("users",array("urn"=>$xml->urn));
	
	if(!$user) {
		die("mysql error".$db->lastError);
	}
	
	$facebook->setAccessToken($user['token']);


	try {
		$fbuser = $facebook->api('/me', 'get');
	} catch (FacebookApiException $e) {
		echo "result=ERROR&message=".urlencode($e->getMessage());
		die();
	}
	
	$facebook->setFileUploadSupport(true);
	
	$args = array('message' => "We love cheering from the stands, come rain or shine! #YouAreFootball"); //,'place'=>'50.712560964795'
//$args = array('message' => 'Nézd meg, ahogy a Vodafone McLaren Mercedes F1 versenyautójában ülök a Vodafone Kezdj El Valami Újat Hétvégén!');
	$args['image'] = '@' . realpath($_SERVER['DOCUMENT_ROOT']."/".$label."/".basename($xml->destFileName));

	var_dump($fbuser);

	$resultmessage="SUCCESS";
	
	try {
		$data = $facebook->api('/me/photos', 'post', $args); 
	} catch (FacebookApiException $e) {
		$resultmessage=$e->getMessage();
		
		echo "ERROR FB POST:".$e->getMessage()."<br/>";
		
	}
	
	if(isset($data['id'])) {
		$post_url = "https://graph.facebook.com/".$data['id']."/tags/"
        . $user['id']."?access_token=".$user['token']."&x=" . 68 ."&y=65"
         ."&method=POST";
        $response = file_get_contents($post_url);
		echo "******** response of taging".$response."<br/>";
	}

	if(isset($data['id'])) {
		$postId=$data['id'];
	} else {
		$postId=0;
	}

	$result=$db->Insert(array("mediatype"=>$xml->mediaType,"urn"=>$xml->urn,"post_type"=>$xml->usertype,"filename"=>$xml->destFileName,"publish_result"=>$resultmessage,"social_id"=>$postId),"userphoto",true);
	if(!$result) {
		echo "error inserting userphoto on FB:".$db->lastError;
	}
	$db->CloseConnection();
	
}

function shortenURL($url) {
	$result=file_get_contents("http://is.gd/create.php?format=simple&url=".$url);
	return $result;
}


/* SimpleXMLElement Object ( [added] => 2013-08-11 13:52:51 [isConnected] => 0 [isBatch] => 1 [photo] => SimpleXMLElement Object ( ) [video] => SimpleXMLElement Object ( ) [personalNote] => note taken while offline email [lat] => 0 [long] => 0 [current_location] => Arsenal Stadium [event_location] => Arsenal Stadium [urn] => ZZ84IS0WJKNR7CJF [usertype] => EMAIL [firstname] => alfo [lastname] => offline [email] => lisophorm@gmail.com [mobile] => 585757555868 [destFileName] => ZZ84IS0WJKNR7CJF-ESGH.jpg [mediaType] => jpgfile ) /*
/**
* Recursive version of glob
*
* @return array containing all pattern-matched files.
*
* @param string $sDir      Directory to start with.
* @param string $sPattern  Pattern to glob for.
* @param int $nFlags       Flags sent to glob.
*/
function rglob($sDir, $regEx, $nFlags = NULL)
  {
	  chdir(dirname(__FILE__));
	  
	$result=array();
  if ($handle = opendir($sDir)) {
  while (false !== ($file = readdir($handle))) {
	  //echo "$file\n";
	  preg_match($regEx, $file, $matches);
	  if ($file != '.' && $file != '..' && count($matches) > 0) {
		  $result[]=$file;
		  //print("<pre>$regEx $sDir $file \n=");
	  }
	  
	  }
	}
	//print "array:".is_array($result)."\n";
	return $result;
  
} 
function array_lower($orig) {
	$dest=array();
	for ($i=0;$i<count($orig);$i++) {
		$dest[]=strtolower($orig[$i]);
	}
	return $dest;
}
function parseXML($xmlfile) {

	$file = fopen($xmlfile,"r");
	$content=fread($file,filesize($xmlfile));
	fclose($file);
	
	
		return simplexml_load_string($content);
	
	
}
function parseResult($xmlfile,$pic) {


	
	$xml = simplexml_load_string($content);
	echo "date:".date("d-m-Y",(int)$xml->added);
	print_r($xml);
	$token=trim($xml->token);
	echo "token length:".strlen($token)."<br/>";
	if($xml->facebook==1) {
		$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"http://sunglasshut.wassermanexperience.com/setuser.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
				"urn=".$xml->urn."&token=".$xml->token."&current_location=".$xml->current_location);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	} else {
		
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"http://sunglasshut.wassermanexperience.com/register.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
				"location=".$xml->location."&hometown=".$xml->hometown."&urn=".$xml->urn."&firstname=".$xml->firstname."&lastname=".$xml->lastname."&email=".$xml->email."&mobile=".$xml->mobile."&current_location=".$xml->current_location);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	}
	
	echo "server output registration $server_output <br/>";
	
	$insertSQL = sprintf("update users set added=%s,offline=1 where urn=%s",
			 GetSQLValueString($xml->added, "text"),
             GetSQLValueString($xml->urn, "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die("result=ERROR&message=".urlencode("error updating batch timestamp".mysql_error()));
	

	
	echo "execute cron with photo,<br/>";
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"http://sunglasshut.wassermanexperience.com/uploader.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
				"&file=".$xml->destFileName);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	
	echo "result of file upload:".$server_output."<br/>";
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"http://sunglasshut.wassermanexperience.com/publishphoto.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
				"urn=".$xml->urn."&file=".$pic."&prize=".$xml->grabPrize."&score=".$xml->ticketsWon);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	
	echo "server output publish photo $server_output <br/>";
	
	echo "end of cron,<br/>";
	
	curl_close ($ch);

	
	$insertSQL = sprintf("update users set server_result=%s where urn=%s",
                       GetSQLValueString($server_output, "text"),
					   GetSQLValueString($xml->urn, "text")

					   );

  		mysql_select_db($database_localhost, $localhost);
  		$Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
		
	rename( "/var/www/vhosts/wassermanexperience.com/sunglasshut/batchincoming/".$xmlfile  , "/var/www/vhosts/wassermanexperience.com/sunglasshut/batchprocessed/".$xmlfile);
	
	
	
}
function oldProcessfile($xml,$img,$delete,$urn) {
	global $database_localhost,$username_localhost,$password_localhost,$localhost;
	@unlink(( "/var/www/vhosts/ignitesocial.co.uk/httpdocs/summertimeremote/batchincoming/".$delete));
	
	mysql_select_db($database_localhost, $localhost);
	$query_Recordset1 = sprintf("SELECT * FROM users WHERE urn = %s", GetSQLValueString($urn, "text"));
	$Recordset1 = mysql_query($query_Recordset1, $localhost) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	$totalRows_Recordset1 = mysql_num_rows($Recordset1);
	
	if($totalRows_Recordset1>0) {
		echo "file exists!<br/>";
		@unlink("/var/www/vhosts/ignitesocial.co.uk/httpdocs/summertimeremote/batchincoming/".$xml);
		@unlink("/var/www/vhosts/ignitesocial.co.uk/httpdocs/summertimeremote/batchincoming/".$img);
		return false;
	}
	
	print "processing: $xml - $img rootDir: $rootdir \n";
	$processresult=true;
	insertRecord($xml,$img);
	if(!rename( "/var/www/vhosts/ignitesocial.co.uk/httpdocs/summertimeremote/batchincoming/".$xml  , "/var/www/vhosts/ignitesocial.co.uk/httpdocs/summertimeremote/batchprocessed/".$xml)) {
		$processresult=false;
	}

	if($processresult) {
		echo "SUCCESS \n";
	} else {
		echo "FAIL \n";
	}
	
}
function insertRecord($xmlFile,$imgfile) {
	print "inserting record: $xmlFile \n";
	$fileXML="/var/www/vhosts/ignitesocial.co.uk/httpdocs/summertimeremote/batchincoming/".$xmlFile;
	$file = fopen($fileXML,"r");
	$content=fread($file,filesize($fileXML));
	fclose($file);
	
	preg_match('#<firstname>(.*)</firstname>#Us', $content, $firstname);
	preg_match('#<lastname>(.*)</lastname>#Us', $content, $lastname);
	preg_match('#<email>(.*)</email>#Us', $content, $email);
	preg_match('#<urn>(.*)</urn>#Us', $content, $urn);
	preg_match('#<usertime>(.*)</usertime>#Us', $content, $usertime);
	preg_match('#<game1>(.*)</game1>#Us', $content, $game1);
	preg_match('#<game2>(.*)</game2>#Us', $content, $game2);
	preg_match('#<game3>(.*)</game3d>#Us', $content, $game3);
	preg_match('#<game4>(.*)</game4>#Us', $content, $game4);
	preg_match('#<game5>(.*)</game5>#Us', $content, $game5);
	
	echo "postcard: $imgfile <br/>";
	
	if(strlen(trim($imgfile))>2 && file_exists("batchincoming/".$imgfile)) {
		echo $urn[1]."postcard exists<br/>";
		rename( "batchincoming/".$imgfile  , "postcards/".$imgfile);
		$haspostcard=true;
	} else {
		echo $urn[1]."no postcard present<br/>";
		$haspostcard=false;
	}
	
   	echo("processing this one game 5:".$game5[5]);
	
	global $database_localhost,$username_localhost,$password_localhost,$localhost;
  $insertSQL = sprintf("INSERT INTO users (firstname, lastname, email, urn, usertime, game1, game2, game3,game4,game5) VALUES (%s, %s, %s, %s,timestamp(%s), %s, %s, %s,%s,%s)",
                       GetSQLValueString($firstname[1], "text"),
                       GetSQLValueString($lastname[1], "text"),
                       GetSQLValueString($email[1], "text"),
                       GetSQLValueString($urn[1], "text"),
                       GetSQLValueString($usertime[1], "text"),
                       GetSQLValueString($game1[1], "text"),
                       GetSQLValueString($game2[1], "text"),
					   GetSQLValueString($game3[1], "text"),
					   GetSQLValueString($game4[1], "text"),
					   GetSQLValueString($game5[1], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
  
  $userid=mysql_insert_id();
  

  
  if(strtotime($usertime[1])>strtotime("2012-05-01 01:00:00")) {
	 echo "sending email<br/>";

  

  	require_once('phpmailer/class.phpmailer.php');
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"
	
	//$mail->IsSendmail(); // telling the class to use SendMail transport
	
	$mail->IsSMTP();
	$mail->Host = "localhost";
	
	$mail->SMTPDebug  = 2;
	
	$body             = file_get_contents('Emailer_Layered_FIN.htm');
	$body             = eregi_replace("[\]",'',$body);
	
	if($game1[1]=="") {
		$game1[1]=0;
	}
	if($game2[1]=="") {
		$game2[1]=0;
	}
	if($game3[1]=="") {
		$game3[1]=0;
	}
	if($game4[1]=="") {
		$game4[1]=0;
	}
	if($game5[1]=="") {
		$game5[1]=0;
	}
	
	$body=str_replace("#score1#",$game1[1],$body);
	$body=str_replace("#score2#",$game2[1],$body);
	$body=str_replace("#score3#",$game3[1],$body);
	$body=str_replace("#score4#",$game4[1],$body);
	$body=str_replace("#score5#",$game5[1],$body);
	$total=round(($game1[1]+$game2[1]+$game3[1]+$game4[1]+$game5[1])/5);
	
	$body=str_replace("#total#",$total,$body);
	
	
	
	$mail->AddReplyTo("Noreply@carlsberg.co.uk","Carlsberg Photo Booth");
	
	$mail->SetFrom('Noreply@carlsberg.co.uk', 'Carlsberg Photo Booth');
	
	
	$mail->AddAddress($email[1],$firstname[1]." ".$lastname[1]);
	
	$mail->Subject    = "Your Carlsberg Scorecard";
	
	$mail->AltBody    = "To see your picture, please use a HTML compatible viewer!"; // optional, comment out and test
	
	
	if($haspostcard) {
		  $body=str_replace("#image#",'<tr><td colspan="4"><img src="postcards/'.$imgfile.'" width="650" height="487" style="display:block;" border="0" id="Emailer_650x562px_r3_c2" alt="" /></td></tr>',$body);
		  $mail->AddAttachment("/var/www/vhosts/ignitionsecure.co.uk/httpdocs/car1008/postcards/".$imgfile); // attachment
	} else {
		$body=str_replace("#image#","",$body);
	}
	$mail->MsgHTML($body);
	if(!$mail->Send()) {
	  $result= $mail->ErrorInfo;
	} else {
	  $result= "OK";
	}


	  } else {
		  echo "not sending email<br/>";
	  }
  
}

function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth ) 
{
  // open the directory


  // loop through it, looking for any/all JPG files:

      echo "Creating thumbnail for $pathToImages <br />";

      // load image and get image size
      $img = imagecreatefromjpeg( $pathToImages );
      $width = imagesx( $img );
      $height = imagesy( $img );

      // calculate thumbnail size
      $new_width = $thumbWidth;
      $new_height = floor( $height * ( $thumbWidth / $width ) );

      // create a new temporary image
      $tmp_img = imagecreatetruecolor( $new_width, $new_height );

      // copy and resize old image into new image 
	  imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
      //imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

      // save thumbnail into a file
      imagejpeg( $tmp_img, $pathToThumbs,90 );
    

}

?></body>
</html>