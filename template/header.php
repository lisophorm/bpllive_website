<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>BPL Live | <?php echo $_pages[$page]['title'] ?></title>
		
		<meta name="HandheldFriendly" content="True">
		
		<?php if($detect->isTablet()&&$detect->isIOS()): ?>
		<meta name="viewport" content="width=device-height, target-densitydpi=160dpi, user-scalable=yes">
		<?php elseif($detect->isTablet()): ?>
		<meta name="viewport" content="width=1024, target-densitydpi=160dpi, user-scalable=yes">
		<?php else: ?>
		<meta name="viewport" content="width=device-width, target-densitydpi=160dpi, user-scalable=no">
		<meta name="MobileOptimized" content="320">
		<?php endif; ?>
		
		<link rel="stylesheet" href="/assets/css/normalize.css" media="screen">
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<link rel="stylesheet" href="/assets/css/jquery.bxslider.css" media="screen">
		<link rel="stylesheet" href="/assets/css/style.css" media="screen">
		
		<!--[if lte IE 7]>
			<link rel="stylesheet" href="/assets/css/ie7.css" media="screen">
		<![endif]-->
		
		<!--[if lte IE 6]>
			<link rel="stylesheet" href="/assets/css/ie6.css" media="screen">
			<link rel="stylesheet" href="/assets/css/ie.png.css" media="screen">
		<![endif]-->
		
		<!--[if lt IE 9]>
			<script type="text/javascript" src="/assets/js/html5.js"></script>
		<![endif]-->
		
		<script src="//code.jquery.com/jquery-1.9.1.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<script src="/assets/js/retina.js"></script>
		<script src="/assets/js/jquery.bxslider.min.js"></script>
		<script src="/assets/js/<?php echo $device ?>.js"></script>
		
	    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyBi5HlewT9t3Tc3aAqJk-RvVsFue4jVENk&sensor=false"></script>

	    <script src="/assets/js/map.js"></script>
		 
	</head>
	<body class="<?php echo $device.' '.$page ?>">
	
	<div class="wrapper">
	
	<header>
		<div class="wrap">
			<?php if($device == 'mobile'): ?><div class="access">
				<div class="pull-left">
					MENU
				</div>
				<div class="pull-right">
					<i class="fa fa-bars"></i>
				</div>
				<div class="clear"></div>
			</div><?php endif; ?>
			<hgroup>
				<menu>
				<?php if($device == 'mobile'): ?>
					<li class="normal home"><a href="/"><i class="fa fa-home"></i>&nbsp; HOME</a></li>
				<?php endif; ?>
					<?php display_nav($page, $_pages) ?>
					<div class="clear"></div>
				</menu>
			</hgroup>
		</div>
	</header>
	
	<div class="wrap main">
	
		<?php if($device == 'mobile'): ?>	
			<div id="branding">
				<div id="logo">
					<a href="/"><?php display_img('bpll-logo',$device,'png',120,148) ?></a>
				</div>
				<div id="background">
					<?php display_img('backgrounds/background01',$device,'jpg',508,320) ?>
				</div>
			</div>
		<?php endif; ?>
	
		<?php if($device == 'desktop'): ?>	
		<div id="branding">
			<div id="logo">
				<a href="/"><?php display_img('bpll-logo',$device,'png',150,185) ?></a>
			</div>
			<div id="bbb">
				<?php display_img('bbb',$device,'png',141,43) ?>
			</div>
		</div>
		<?php endif; ?>
		
		<?php if(($device == 'desktop' && $page == 'index')||($page != 'index')): ?>
		<div id="content">
		<?php endif; ?>
		
		