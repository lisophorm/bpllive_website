<?php 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
require("../Connections/php.mysql.class.php");

$db= new MySQL(DB,DBUSER,DBPASS);

$input = file_get_contents('php://input');

  // jsonObj is empty, not working
  $jsonObj = json_decode($input,true);
  
	if(!$jsonObj) {
		die("nothing to do here");
	}
  
  $result=$db->Insert($jsonObj['user'],"users");
  
  $output="user ok |";
  
  
  if(!$result) {
		$output.= "! insert user:".$db->lastError."- q:".$db->lastQuery." |";
	} 
	
	if(array_key_exists("dreamteam",$jsonObj)) {
		$result=$db->Insert($jsonObj['dreamteam'],"dreamteams");
		
		  if(!$result) {
				$output.= "!dreamteam:".$db->lastError."- q:".$db->lastQuery." |";
			} else {
				$output.="dreamteam ok |";
			}
		
	}
	
	if(array_key_exists("userphoto",$jsonObj)) {
		$result=$db->Insert($jsonObj['userphoto'],"userphoto");
		
		  if(!$result) {
				$output.= "!userphoto:".$db->lastError."- q:".$db->lastQuery." |";
			} else {
				$output.="userphoto ok |";
			}
		
	}
	
	if(array_key_exists("scores",$jsonObj)) {
		$result=$db->Insert($jsonObj['scores'],"scores");
		
		  if(!$result) {
				$output.= "!scores:".$db->lastError."- q:".$db->lastQuery." ";
			} else {
				$output.="scores ok |";
			}
		
	}

  
  $user=$jsonObj['user'];
  
  $output.=print_r($jsonObj['user'],true);
  
  //file_put_contents(time().".txt",print_r($output,true) );
  
 echo $output;
  
?>