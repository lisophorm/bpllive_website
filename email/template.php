<?php 

function email_template($part_string){
	
	$parts = explode(',', $part_string);
	$number_parts = count($parts);
	$odd = true; if ($number_parts % 2 == 0) $odd = false;
		echo "<br/> <br/> number_parts $number_parts<br/>";
	$blocks = array(
		// Club Zone
		'c'=>array(
				'filename'=>'clubzone',
				'sharefacebook'=>'http://www.bpllive.com/sharetrophy.php?share_type=FACEBOOK&urn='."#urn#",
				'sharetwitter'=>'http://www.bpllive.com/sharetrophy.php?share_type=TWITTER&urn='."#urn#",
				'facebook'=>'I got my hands on the Barclays Premier League trophy - head to Barclays Premier League Live for your chance to do the same.',
				'twitter'=>urlencode('I got my hands on the Barclays Premier League trophy - head to BPLLive.com for your chance to do the same.'),
				'tumblr'=>urlencode('I got my hands on the Barclays Premier League trophy - head to Barclays Premier League Live for your chance to do the same.')
			),
		// Dream Team
		'd'=>array(
				'filename'=>'dreamteam',
				'sharefacebook'=>'http://www.bpllive.com/sharedreamteam.php?share_type=FACEBOOK&urn='."#urn#",
				'sharetwitter'=>'http://www.bpllive.com/sharedreamteam.php?share_type=TWITTER&urn='."#urn#",
				'facebook'=>urlencode("This is my ")."#club_name_enc#".urlencode(" Dream Team. You can choose your team's all-time line-up at Barclays Premier League Live"),
				'twitter'=>urlencode("This is my ")."#club_name_enc#".urlencode(" Dream Team. You can choose your team's all-time line-up at BPLLive.com"),
				'tumblr'=>urlencode("This is my ")."#club_name_enc#".urlencode(" Dream Team. You can choose your team's all-time line-up at Barclays Premier League Live")
			),
		// Skills Zone
		's'=>array(
				'filename'=>'skillszone',
				'sharefacebook'=>'http://www.bpllive.com/sharescore.php?share_type=FACEBOOK&urn='."#urn#",
				'sharetwitter'=>'http://www.bpllive.com/sharescore.php?share_type=TWITTER&urn='."#urn#",
				'facebook'=>urlencode("Here's how I did at the Skills Zone - head to Barclays Premier League Live and see if you can beat me..."),
				'twitter'=>urlencode("Here's how I did at the Skills Zone - head to BPLLive.com and see if you can beat me..."),
				'tumblr'=>urlencode("Here's how I did at the Skills Zone - head to Barclays Premier League Live and see if you can beat me...")
			)
	);
	if (!function_exists('share')) { 
	function share($p, $blocks){
		return '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 10px;">
										<tr>
											<td width="190" align="right" valign="middle" style="font-size:12px; font-family: \'HelveticaNeue\', \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-weight: normal; line-height: 20px; margin: 0 0 10px;">
												Share:
											</td>
											<td align="right" width="30">
												<a href="'.$blocks[$p]['sharefacebook'].'"><img src="http://www.bpllive.com/email/img/facebook.gif" alt="" width="25" height="25" /></a>
											</td>
											<td align="right" width="30">
												<a href="'.$blocks[$p]['sharetwitter'].'"><img src="http://www.bpllive.com/email/img/twitter.gif" alt="" width="25" height="25" /></a>
											</td>
										<tr>
									</table>';
	}
	}
	$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>BPL Live Email</title>
		<meta name="viewport" content="width=620, initial-scale=1.0"/>
	</head>
	<body style="margin: 0; padding: 0;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr><td>
			
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
 					<tr><td style="border-bottom: 1px solid #c3c3c3; padding-bottom:10px;">
						<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
							<tr>
								<td>
									<a href="http://www.bpllive.com/"><img src="http://www.bpllive.com/email/img/bpllivelogo.gif" alt="" width="144" height="180" /></a>
								</td>
								
								<td valign="top" align="right">
									<img src="http://www.bpllive.com/email/img/broughtbybarclays.gif" alt="" width="160" height="86" />
									<br />
									';
	// CLUB BADGE AT TOP
	if($odd) $html .= '<img src="#club_badge_85#" alt="" height="85" />';
	$html .= '					</td>
							</tr>
		 				</table>
   					</td></tr>';
	if(trim($part_string)=="") {
				$html .= '<tr><td><br/><br/><br/><p style="font-size:20px; font-family: \'HelveticaNeue-Light\', \'Helvetica Neue Light\', \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-weight: 300; line-height: 32px; margin: 0 0 10px;">Hi #name#,<br />Thank you for visiting Barclays Premier League Live<br/>on #date#.<br/>We hope you enjoyed your day.</p><br/><br/><br/></tr></td>';
			} else {
		for($i=0;$i<$number_parts+1;$i++):
			
			if($i%2==0):
				
				$html .= '<tr><td style="padding-top:20px;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-bottom:20px;';
				if($number_parts>1&&$i<$number_parts-1) $html .= 'border-bottom: 1px solid #c3c3c3;';
				$html .= '"><tr>';
					
			endif; 
					
			//SELECT SIDE
			if($i%2==0){
				$html .= '<td width="280" style="padding-right:20px;" valign="top">';
			} else {
				$html .= '<td width="280" style="padding-left:20px;" valign="top">';
			}
					
			// CONTENT
				// FIRST BLOCK		
				if($i==0){
					
					$html .= '<p style="font-size:24px; font-family: \'HelveticaNeue-Light\', \'Helvetica Neue Light\', \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-weight: 300; line-height: 32px; margin: 0 0 10px;">Hi #name#,<br />Thank you for visiting Barclays Premier League Live on #date#.</p>';
					
					if(in_array('c', $parts)) $html .= '<p style="font-size:12px; font-family: \'HelveticaNeue\', \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-weight: normal; line-height: 20px; margin: 0 0 10px;">To download your photo please <a href="#image_c_full#" style="color:#009deb">click here</a>.</p>';
					
				// BLOCKS
				} else { 
				
					$p = $i-1; 
					
					$html .= '<img src="http://www.bpllive.com/email/img/'.$blocks[$parts[$p]]['filename'].'.gif" alt="" width="280" height="35" />';
					if($parts[$p]=='s' && $number_parts==3){
						$html .= '<img src="#image_'.$parts[$p].'#" alt="" width="280" height="141" style="padding-top:12px;padding-bottom:12px;" />';
					} elseif($parts[$p]=='s'){
						$html .= '<img src="#image_'.$parts[$p].'#" alt="" width="280" height="141" />';
					} elseif($parts[$p]=='d') {
						$html .= '<img src="#image_'.$parts[$p].'#" alt="" width="280" height="165" />';
					} else {
						$html .= '<img src="#image_'.$parts[$p].'#" alt="" width="280" height="210" />';
					}
					$html .= share($parts[$p],$blocks);
						
				}
					
			// END CONTENT
					
			$html .= '</td>';
					
			if($i%2==0&&$i==$number_parts){
				$html .= '<td width="280" style="padding-left:20px; padding-top:10px" valign="top" align="right">
								<img src="#club_badge_200#" alt=""height="200" />
							</td>';
			}
					
			if($i%2!=0||$i==$number_parts):
					
				$html .= '			</tr>
							</table>
						</td></tr>';
					
			endif; 
		
		endfor; 
			}
	
	$html .= '		<tr><td style="border-top: 1px solid #c3c3c3; padding-top:30px; padding-bottom:30px;">
						<a href="http://www.premierleague.com/"><img src="http://www.bpllive.com/email/img/footer_01.gif" alt="footer_01" width="212" height="18" /></a><img src="http://www.bpllive.com/email/img/footer_02.gif" alt="footer_02" width="46" height="18" /><a href="http://www.barclays.com/"><img src="http://www.bpllive.com/email/img/footer_03.gif" alt="footer_03" width="94" height="18" /></a><a href="http://www.easports.com/fifa"><img src="http://www.bpllive.com/email/img/footer_04.gif" alt="footer_04" width="38" height="18" /></a><a href="http://www.nike.com/gb/en_gb/c/football/"><img src="http://www.bpllive.com/email/img/footer_05.gif" alt="footer_05" width="53" height="18" /></a><a href="https://www.facebook.com/CarlsbergUK"><img src="http://www.bpllive.com/email/img/footer_06.gif" alt="footer_06" width="58" height="18" /></a><a href="http://www.toppsfootball.co.uk"><img src="http://www.bpllive.com/email/img/footer_07.gif" alt="footer_07" width="49" height="18" /></a><a href="http://www.supersport.com/football"><img src="http://www.bpllive.com/email/img/footer_08.gif" alt="footer_08" width="50" height="18" /></a>
   					</td></tr>

 				</table>
 			</td></tr>
 		</table>
 	</body>
</html>';
	
	return $html;

}