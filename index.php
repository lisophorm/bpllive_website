<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

// REQUEST URI
include_once('includes/request_uri.php');
$part = request_uri(1);

// ALLOWED URIs
include_once('includes/pages.php');

// DIRECTION
$page = null;

if(!$part){
	$page = 'index';
} elseif(isset($_pages[$part])){
	$page = $part;
} else {
	$page = '404';
}

// DEVICE DETECTION
require_once 'includes/Mobile-Detect/Mobile_Detect.php';
$detect = new Mobile_Detect;
$device = null;

if( $detect->isMobile() && !$detect->isTablet() ){
	$device = 'mobile';
} else {
	$device = 'desktop';
}

include_once('includes/functions.php');

include_once('template/header.php');

include_once('pages/'.$page.'.php');

include_once('template/footer.php');