<?php

function display_nav($current, $_pages){
	foreach($_pages as $key => $page){
		if($page['nav']){
			$class = 'normal';
			if($key==$current) $class = 'active';
			echo '<li class="'.$class.'"><a href="/'.$key.'/">'.strtoupper($page['title']).'</a></li>';
		}
	}
}

function display_img($name, $device, $extension, $width=null, $height=null){
	if($width=='one_col'){
		if($device=='desktop'){
			$width = 220;
			$height = 140;
		} else {
			$width = 270;
			$height = 178;
		}
	}
	if($width=='slide'){
		if($device=='desktop'){
			$width = 700;
			$height = 480;
		} else {
			$width = 320;
			$height = 220;
		}
	}
	if($width) $width = "width='$width'";
	if($height) $height = "height='$height'";
	echo "<img src='/assets/img/$name-$device.$extension' $width $height alt='' />";
}