<?php

namespace abeautifulsite;
use Exception;
require $_SERVER['DOCUMENT_ROOT'].'/email/includes/src/abeautifulsite/SimpleImage.php';

function makescores($args){

	$img = new SimpleImage();
	
	$scores = explode(',', $args['scores']);
	$games = explode(',', $args['games']);
	$sheet = str_replace(',', '', $args['games']);
	$rows = array(10,61,112);
	
	$font = $_SERVER['DOCUMENT_ROOT'].'/email/font/premierleague-webfont.ttf';
	
	try {
	    $img->load($_SERVER['DOCUMENT_ROOT'].'/email/img/scoresheet-'.$sheet.'.png');
	    $count = 0;
	    foreach($games as $game){
		    $img->text($scores[$count], $font, 19, '#009deb', 'top left', 190, $rows[$count]);
		    if($game=='p'){
			    $img->text('KM/H', $font, 8, '#009deb', 'top left', 245, $rows[$count]+9);
		    }
		    $count ++;
	    }
	    $img->save($_SERVER['DOCUMENT_ROOT'].'/email/scores/'.$args['filename']);
	} catch(Exception $e) {
		print_r($e);
	}
	
	return '/email/scores/'.$args['filename'];
}