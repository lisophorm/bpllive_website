<?php require_once('./Connections/php.mysql.class.php'); ?>
<?php

function shortenURL( $url )
{
    $result = file_get_contents( "http://is.gd/create.php?format=simple&url=" . $url );
    return $result;
}

$db= new MySQL(DB,DBUSER,DBPASS);

$user=$db->Select("users",array("urn"=>$_GET['urn']));

if($db->records==0) {
	die("direct access not allowed");
}

$photo=$db->Select("userphoto",array("urn"=>$_GET['urn']));

switch($_GET['share_type']) {
	case "TWITTER":
		$incrementfield="twitter_share_skills";
		$url="http://www.twitter.com/share?text=".urlencode("Here's how I did at the Skills Zone - I tested my skills at Barclays Premier League Live in Johannesberg, South Africa. ")."&url=".shortenURL("https://www.bpllive.com/tskills/".$db->SecureData($_GET['urn']));
	break;
	case "FACEBOOK":
		$incrementfield="facebook_share_skills";
		$url="https://www.facebook.com/dialog/feed?app_id=830620426955447&display=page&name=".urlencode("#BPLLive")."&link=".urlencode("https://www.bpllive.com/fbskills/".$user['urn'])."&picture=".urlencode("http://www.bpllive.com/email/scores/".$photo['urn'].".png")."&redirect_uri=".urlencode("https://www.bpllive.com");
	break;
	default:
		die("direct access not allowed");
	break;
}


$result=$db->ExecuteSQL("update stats set ".$incrementfield."=".$incrementfield."+1 where urn='".$db->SecureData($_GET['urn'])."'");

if(!$result) {
	die("error updating stats".$db->lastQuery."-".$db->lastError);
}



header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.


?>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Refresh" content="2; url=<?php echo $url; ?>" />
<title>#BPLLive - Share </title>
</head>
<body>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-48978787-1']);
_gaq.push(['_trackSocial', '<?php echo strtolower($db->SecureData($_GET['share_type'])); ?>', 'share', '/skills']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; 

ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';

var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
</body>
</html>