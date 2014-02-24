<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head><?php 

require("../Connections/php.mysql.class.php");

$db= new MySQL(DB,DBUSER,DBPASS);

$input = file_get_contents('php://input');

  // jsonObj is empty, not working
  $jsonObj = json_decode($input,true);
  
  $result=$db->Insert($jsonObj['user'],"users");
  
  $output="ok";
  
  
  if(!$result) {
		$output.= "db error on insert user".$db->lastError."<br/>";
	} else {
		$output.="user insert ok ";
	}
	
	if(array_key_exists("dreamteam",$jsonObj)) {
		$result=$db->Insert($jsonObj['dreamteam'],"dreamteams");
		
		  if(!$result) {
				$output.= "db error on insert dreamteam".$db->lastError."<br/>";
			} else {
				$output.="dreamteam ok ";
			}
		
	}
	
	if(array_key_exists("userphoto",$jsonObj)) {
		$result=$db->Insert($jsonObj['userphoto'],"userphoto");
		
		  if(!$result) {
				$output.= "db error on insert userphoto".$db->lastError."<br/>";
			} else {
				$output.="userphoto ok ";
			}
		
	}
	
	if(array_key_exists("scores",$jsonObj)) {
		$result=$db->Insert($jsonObj['scores'],"scores");
		
		  if(!$result) {
				$output.= "db error on insert scores".$db->lastError."<br/>";
			} else {
				$output.="scores ok ";
			}
		
	}

  
  $user=$jsonObj['user'];
  
  $output.=print_r($jsonObj['user'],true);
  
  file_put_contents(time().".txt",print_r($output,true) );
  
 echo $output;
  
?></body>
</html>
