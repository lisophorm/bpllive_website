<?php
require_once( 'Connections/php.mysql.class.php' );
error_reporting( E_ALL );
if ( isset( $_GET[ 'urn' ] ) ) {
    $currentURN = $_GET[ 'urn' ];
} else {
    $currentURN = "A1393253207071Z";
}
function update( $urn, $value )
{
    $db = new MySQL( DB, DBUSER, DBPASS );
    
    if ( $value == "COMPLETE" ) {
        $issynced = 1;
    } else {
        $issynced = 0;
    }
    
    $result = $db->Update( "users", array(
         "issynced" => $issynced,
        "files_present" => $issynced,
        "$issynced" => $value 
    ), array(
         "urn" => $urn 
    ) );
    
    
    
    if ( !$result ) {
        die( "mysql error on updae status:" . $db->lastError . " query:" . $db->lastQuery );
    }
    
}

// DUPLICATE FUNCTION FOR TESTING
function shortenURL( $url )
{
    $result = file_get_contents( "http://is.gd/create.php?format=simple&url=" . $url );
    return $result;
}
function get_file_extension( $file_name )
{
    return substr( strrchr( $file_name, '.' ), 1 );
}
function makeThumb( $filename, $newwidth, $destpath )
{
    global $url;
    if ( get_file_extension( $filename ) != "jpg" ) {
        return false;
    }
    /*
     * PHP GD
     * resize an image using GD library
     */
    if ( !file_exists( $filename ) ) {
        return false;
    }
    list( $width, $height ) = getimagesize( $filename );
    if ( !$width ) {
        return false;
    }
    $ratio     = $width / $height;
    $newheight = $newwidth * $ratio;
    // Load
    $thumb     = imagecreatetruecolor( 280, 210 );
    $source    = imagecreatefromjpeg( $filename );
    // Resize
    imagecopyresized( $thumb, $source, 0, 0, 0, 0, 280, 210, $width, $height );
    // Output and free memory
    //the resized image will be 400x300
    $newpath = $destpath . "/" . basename( $filename );
    if ( !imagejpeg( $thumb, $newpath, 90 ) ) {
        return false;
    } else {
        return $url . "/" . $newpath;
    }
}
function makeThumbDREAM( $filename, $newwidth, $destpath )
{
	global $url;
    if ( get_file_extension( $filename ) != "jpg" ) {
        return false;
    }
    /*
     * PHP GD
     * resize an image using GD library
     */
    if ( !file_exists( $filename ) ) {
        return false;
    }
    list( $width, $height ) = getimagesize( $filename );
    if ( !$width ) {
        return false;
    }
    $ratio     = $width / $height;
    $newheight = $newwidth * $ratio;
    // Load
    $thumb     = imagecreatetruecolor( 286, 165 );
    $source    = imagecreatefromjpeg( $filename );
    // Resize
    imagecopyresized( $thumb, $source, 0, 0, 0, 0, 286, 165, $width, $height );
    // Output and free memory
    //the resized image will be 400x300
    $newpath = $destpath . "/" . basename( $filename );
    if ( !imagejpeg( $thumb, $newpath, 90 ) ) {
        return false;
    } else {
        return $url . "/" . $newpath;
    }
}
include( 'email/makescores.php' );
include( 'email/template.php' );
$db    = new MySQL( DB, DBUSER, DBPASS );
$users = $db->ExecuteSQL( "SELECT distinct
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
from ((((`users` `c` left join `scores` `r` on((`r`.`urn` = `c`.`urn`))) left join `userphoto` on((`c`.`urn` = `userphoto`.`urn`))) left join `dreamteams` on((`c`.`urn` = `dreamteams`.`urn`))) left join `teams` on((`c`.`team_id` = `teams`.`id`)))
WHERE isnull(c.issynced) limit 20" );
//var_dump($user);
//echo "error:".$db->lastError;
if ( !$users ) {
    die( "mysql error:" + $db->lastError + " query:" + $db->lastQuery );
} else {
    var_dump( $users );
}
if($db->records==0) {
	die("No records to process");
} else if($db->records==1) {
	$user=$users;
	$users= array();
	array_push($users,$user);
}
echo "query: -" . $db->lastQuery . "<br/>";
if ( count( $users ) > 0 ) {
	//die("total users".count($users));
    for ( $i = 0; $i < count( $users ); $i++ ) {
       	$user = $users[ $i ];
        echo "<br/>unique id:" . $user[ 'uniqueid' ] . "<br/>";
        //echo "<br/>name:".$user['firstname'];
        $urn      = $user[ 'uniqueid' ];
		$result=$db->ExecuteSQL("insert ignore into stats (urn,added) values (".$user[ 'uniqueid' ].",NOW())");
        $result=$db->Insert(array("urn"=>$user[ 'uniqueid' ] ),"stats",true);
		if(!$result) {
			die("error updating stats ".$db->lastQuery);
		}

        $url      = 'http://www.bpllive.com';
        // User Vars
        $name     = ucfirst( $user[ 'firstname' ] );
        $date     = date( "l j F Y", strtotime( $user[ 'added' ] ) );
        //$urn = '12385102438965';
        $teamname = $user[ 'team_name' ];
        //$clubzonephoto = 'http://www.bpllive.com//280x165';__CLASS__
        if ( get_file_extension( $user[ 'imgtrophy' ] ) == "jpg" && !file_exists( "bplphotos/" . $user[ 'imgtrophy' ] ) ) {
            update( $user[ 'uniqueid' ], "NOTROPHY" );
            $notrophy = true;
            echo "missing photo";
        } else {
            $notrophy = false;
        }
        $clubzonephoto = makeThumb( "bplphotos/" . $user[ 'imgtrophy' ], 280, "sitethumbs/trophy" );
        //$dreamteamphoto = 'http://placehold.it/280x165';
        if ( get_file_extension( $user[ 'imgdreamteam' ] ) == "png" && !file_exists( "bplphotos/" . $user[ 'imgdreamteam' ] ) ) {
            update( $user[ 'uniqueid' ], "NODREAM" );
            $nodream = true;
            echo "missing dream";
        } else {
            $nodream = false;
        }
		if($notrophy || $nodream) {
			echo "<br/>Exit loop<br/>";
			break;
		}
        $dreamteamphoto = makeThumbDREAM( "bplphotos/" . $user[ 'imgdreamteam' ], 280, "sitethumbs/dream" );
        $game0          = trim( $user[ 'game0' ] );
        $game1          = trim( $user[ 'game1' ] ); //Power
        $game2          = trim( $user[ 'game2' ] ); //Control
		
        // Games
        $games          = '';
        $scores         = '';
        if ( $game0 ) {
            $games .= 'a,';
            $scores .= $game0 . ',';
        }
        if ( $game1 ) {
            $games .= 'p,';
            $scores .= $game1 . ',';
        }
        if ( $game2 ) {
            $games .= 'c,';
            $scores .= $game2 . ',';
        }
        $games  = substr( $games, 0, -1 );
        $scores = substr( $scores, 0, -1 );
        // Parts
        $parts  = '';
        if ( $clubzonephoto )
            $parts .= 'c,';
        if ( $dreamteamphoto )
            $parts .= 'd,';
        if ( $scores )
            $parts .= 's,';
        $parts = substr( $parts, 0, -1 );
        // Get the template
        $body  = email_template( $parts );
        // Start replacing template parts ->
        $body  = str_replace( "#name#", $name, $body );
        $body  = str_replace( "#date#", $date, $body );
        // Club Zone Photo
        if ( $clubzonephoto ) {
            $image_c               = $clubzonephoto;
            $image_c_enc           = urlencode( $image_c );
            $image_c_shortlink     = shortenURL( $url . "/ttrophy/" . $user[ 'uniqueid' ] );
            $image_c_shortlink_enc = urlencode( $image_c_shortlink );
            $body                  = str_replace( "#image_c#", $image_c, $body );
			$body                  = str_replace( "#facebook_c_link#", $url . "/fbtrophy/" . $user[ 'uniqueid' ], $body );
			$body                  = str_replace( "#twitter_c_link#", $url . "/ttrophy/" . $user[ 'uniqueid' ], $body );
            $body                  = str_replace( "#image_c_full#", $url . "/trophy/" . $user[ 'uniqueid' ], $body );
            $body                  = str_replace( "#image_c_enc#", $image_c_enc, $body );
            $body                  = str_replace( "#image_c_shortlink#", $image_c_shortlink, $body );
            $body                  = str_replace( "#image_c_shortlink_enc#", $image_c_shortlink_enc, $body );
			
			$result=$db->Update("userphoto",array("shortlink"=>$image_c_shortlink),array("urn"=>$user[ 'uniqueid' ] ));
			
        }
        // Dream Team Photo
        if ( $dreamteamphoto ) {
            $image_d               = $dreamteamphoto;
            $image_d_enc           = urlencode( $image_d );
            $image_d_shortlink     = shortenURL( $url . "/tdreamteam/" . $user[ 'uniqueid' ] );
            $image_d_shortlink_enc = urlencode( $image_d_shortlink );
			$body                  = str_replace( "#facebook_d_link#", $url . "/fbtrophy/" . $user[ 'uniqueid' ], $body );			
            $body                  = str_replace( "#image_d#", $image_d, $body );
            $body                  = str_replace( "#image_d_enc#", $image_d_enc, $body );
            $body                  = str_replace( "#image_d_shortlink#", $image_d_shortlink, $body );
            $body                  = str_replace( "#image_d_shortlink_enc#", $image_d_shortlink_enc, $body );
			
			$result=$db->Update("dreamteams",array("shortlink"=>$image_d_shortlink),array("urn"=>$user[ 'uniqueid' ] ));
			
        }
        // Skills Zone Scores
        if ( $scores ) {
            $image_s               = $url . \abeautifulsite\makescores( array(
                 'filename' => $urn . '.png',
                'scores' => $scores,
                'games' => $games 
            ) );
            $image_s_enc           = urlencode( $image_s );
            $image_s_shortlink     = shortenURL( $image_s );
            $image_s_shortlink_enc = urlencode( $image_s_shortlink );
            $body                  = str_replace( "#image_s#", $image_s, $body );
            $body                  = str_replace( "#image_s_enc#", $image_s_enc, $body );
            $body                  = str_replace( "#image_s_shortlink#", $image_s_shortlink, $body );
            $body                  = str_replace( "#image_s_shortlink_enc#", $image_s_shortlink_enc, $body );
        }
        // Club Badges
        $club_name_enc  = urlencode( $teamname );
        $club_badge_85  = $url . '/email/img/badges_small/' . str_replace( ' ', '-', $teamname ) . '.gif';
        $club_badge_200 = $url . '/email/img/badges_large/' . str_replace( ' ', '-', $teamname ) . '.gif';
		$body           = str_replace( "#urn#", $user[ 'uniqueid' ], $body );
        $body           = str_replace( "#club_name_enc#", $club_name_enc, $body );
        $body           = str_replace( "#club_badge_85#", $club_badge_85, $body );
        $body           = str_replace( "#club_badge_200#", $club_badge_200, $body );
        // BODY READY ->
        echo $body;
		
		// sends out the fuckin email
		
	require_once($_SERVER['DOCUMENT_ROOT'].'/phpmailer/class.phpmailer.php');
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"
	
	$mail->SMTPDebug = true;
	$mail->do_debug = 0;

	$mail->IsSMTP();
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Host       = "ssl://smtp.sendgrid.net"; // sets the SMTP server
	$mail->Port       = 465;   
	$mail->Username   = "wasserman"; // SMTP account username
	$mail->Password   = "k0st0golov";        // SMTP account password

	$mail->AddReplyTo("noreply@bpllive.com","Barclays");
	
	$mail->SetFrom("noreply@bpllive.com","Barclays");
	$mail->CharSet="UTF-8";
	
	$mail->AddAddress($user['email'],$user['firstname']." ".$user['lastname']);
	//$mail->AddAddress("bratcliffe@wmgllc.com","Becca");
	//$mail->AddAddress("emyhill@wmgllc.com","ed");
	//$mail->AddAddress("aflorio@wmgllc.com","Alfo");
	
	
	$mail->Subject    = "Your BPLLive email";
	$mail->AddBCC("lisophorm@gmail.com","Alfonso");
	$mail->AltBody    = "Please use an html compatible viewer!\n\n"; // optional, comment out and test
	
	
	$mail->MsgHTML($body);
	
	$mail->AddCustomHeader(sprintf( 'X-SMTPAPI: %s', '{"unique_args": {"urn":"'.$urn.'"},"category": "BPLLive"}' ) );

	//$basefile=urldecode(basename($_POST['file']));
	//$mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT']."/rendered/".$basefile,"logo_2u",$basefile); // attachment
	
	if(!$mail->Send()) {
	  $emailresult= $mail->ErrorInfo;

	} else {
	 $emailresult="SUCCESS";
	}
	
	$result=$db->Update("users",array("issynced"=>1,"server_result"=>$emailresult),array("urn"=>$urn ));

	
	if(!$result) {
		echo "error inserting email:".$db->lastError;
	}


		
    }
} else {
    echo "Nothingtododo";
}