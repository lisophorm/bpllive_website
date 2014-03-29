<?php require_once('./Connections/php.mysql.class.php'); ?>
<?php

$db= new MySQL(DB,DBUSER,DBPASS);

$user=$db->Select("users",array("urn"=>$_GET['urn']));

if(!$user) {
	die("error quering user:".$db->lastError);
}

if($db->records==0) {
	die("direct access not allowed");
}

$photo=$db->Select("dreamteams",array("urn"=>$_GET['urn']));

switch($_GET['share_type']) {
	case "TWITTER":
	$incrementfield="twitter_click_dreamteam";
	break;
	case "FACEBOOK":
	$incrementfield="facebook_click_dreamteam";
	break;
	default:
	$incrementfield="email_click_dreamteam";
	break;
}

$basename=explode("/",$_SERVER['REQUEST_URI']);

$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);
if(strpos($useragent,"twitter")===false && !isset($_GET['no_index'])) {
	$result=$db->ExecuteSQL("update stats set ".$incrementfield."=".$incrementfield."+1 where urn='".$db->SecureData($_GET['urn'])."'");
	
	if(!$result) {
		die("error updating stats".$db->lastQuery."-".$db->lastError);
	}
}


header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

?>
<!doctype html>
<html>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# youarefootbal: http://ogp.me/ns/fb/youarefootbal#">
<meta charset="utf-8">
<meta property="og:title" content="#BPLLive" />
<meta property="fb:admins" content="595373701" />
<meta property="fb:app_id" content="830620426955447" />
<meta property="og:image" content="https://www.bpllive.com/sitethumbs/dream/<?php echo $photo['filename']; ?>" /> 
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:image:width" content="1000" />
<meta property="og:image:height" content="700" />
<meta property="og:description" content="This is my <?php echo $photo['teamname']; ?> Dream Team. I choose my team’s all-time line-up at Barclays Premier League Live in Johannesberg, South Africa." />
<meta property="og:url" content="https://www.bpllive.com<?php echo $_SERVER['REQUEST_URI']; ?>">
<meta property="og:type"  content="youarefootbal:photo" /> 
<meta property="og:determiner" content="a" />
<title>#BPLLive</title>
<style type="text/css">
body {
    background-image:url(assets/background.gif);
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
img, img a {
	border:0px;
}
.centralimage {
	/*max-width:80%;*/
}

.buttons img {
	margin-left:auto;
	margin-right:auto;
}
</style>
<link rel="stylesheet" href="/assets/css/normalize.css" media="screen">
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
</head>

<body>
<div style="width:850px;margin-left:auto;margin-right:auto;background-color:#FFF;">
  <div style="width:369px;float:left;background-color:#ffffff;margin:0px;padding:0px;">
    <img src="/landingpage_assets/bpllogo.gif" width="148" height="164" alt=""/>
    <p style="font-size:35px;color:#1e3052">This is my <?php echo $photo['teamname']; ?> Dream Team. </p>
    <p style="font-size:14px;color:#1e3052">I choose my team’s all-time line-up at Barclays Premier League Live in Johannesberg, South Africa.</p>
  </div>
  <div style="width:457px;float:right;background-color:#ffffff;border:1px solid #000000;box-shadow: 5px 5px 5px #888888;margin-bottom:20px;text-align:center;padding-top:20px;">
      <a href="#"><img class="centralimage" style="border:none;" src="https://www.bpllive.com/bplphotos/<?php echo $photo['filename']; ?>" alt="" width="420" height="312" /></a>
      <div style="text-align:center;">
      <div class="buttons" style="padding-top:10px;padding-bottom:20px;"><a href="/download.php?<?php echo isset($_GET['no_index'])?"no_index=1&":"";  ?>urn=<?php echo $user['urn']; ?>&type=dreamteam"><img src="/landingpage_assets/download.gif" style="border:none;" width="29" height="27" alt="Download Photo" /></a><a href="/sharedreamteam.php?urn=<?php echo $user['urn']; ?>&share_type=TWITTER"><img style="border:none;" src="/landingpage_assets/twittershare.gif" width="32" height="27" alt="Share on Twitter" /></a><a href="/sharedreamteam.php?urn=<?php echo $user['urn']; ?>&share_type=FACEBOOK"><img src="/landingpage_assets/fbshare.gif" style="border:none;" width="38" height="27" alt="Share on Facebook" /></a></div>
      </div>

  </div>
  <div style="width:100%;clear:both;text-align:left;border-top:1px solid #606060;margin-top:30px;"><div style="display:inline">
  
						<a href="http://www.premierleague.com/"><img src="/landingpage_assets/premierleague.com.gif" alt="footer_01" width="365" height="54"></a><a href="http://www.barclays.com/"><img src="/landingpage_assets/barclays.gif" alt="footer_03" width="117" height="54"></a><a href="http://www.easports.com/fifa"><img src="/landingpage_assets/easports.gif" alt="footer_04" width="54" height="54"></a><a href="http://www.nike.com/gb/en_gb/c/football/"><img src="/landingpage_assets/nike.gif" alt="footer_05" width="72" height="54"></a><a href="https://www.facebook.com/CarlsbergUK"><img src="/landingpage_assets/carlsberg.gif" alt="footer_06" width="80" height="54"></a><a href="http://www.toppsfootball.co.uk"><img src="/landingpage_assets/topps.gif" alt="footer_07" width="69" height="54"></a><a href="http://www.supersport.com/football"><img src="/landingpage_assets/sport.gif" alt="footer_08" width="69" height="54"></a>
   					
  </div>
  </div>
</div>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-48978787-1']);
_gaq.push(['_trackPageview', '/<?php echo $basename[1]; ?>']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; 

ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';

var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
</div>
</body>
</html>