<?php require_once('./Connections/php.mysql.class.php'); ?>
<?php
$db= new MySQL(DB,DBUSER,DBPASS);

$user=$db->Select("users",array("urn"=>$_GET['urn']));

if($db->records==0) {
	die("user not found - direct access not allowed");
}


switch(trim($_GET['type'])) {
	case "trophy":
		$table="userphoto";
		$field="trophy_downloads";
	break;
	case "dreamteam":
		$table="dreamteams";
		$field="dreamteam_downloads";
	break;
	default:
		die("direct access not allowed:".$_GET['type']);
	break;
}

$photo=$db->Select($table,array("urn"=>$_GET['urn']));

if(!isset($_GET['no_index'])) {
	$result=$db->ExecuteSQL("update stats set ".$field."=".$field."+1 where urn='".$db->SecureData($_GET['urn'])."'");
	
	if(!$result) {
		die("error updating stats".$db->lastQuery."-".$db->lastError);
	}
}


$file =  "bplphotos/".$photo['filename'];
//echo "path: $file";
//echo "size:".filesize($file);
//readfile($file);
//die();

if (strpos($file, '../') !== false ||
    strpos($file, "..\\") !== false ||
    strpos($file, '/..') !== false ||
    strpos($file, '\..') !== false ||
	strpos($file, '.php') !== false ||
	strpos($file, '://') !== false)
{
    die("death to the hacker");
}



if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');
  
header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
 header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;



mysql_free_result($Recordset1);
?>
