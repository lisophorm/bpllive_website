<?php
  
function request_uri($i){
	$request  = $_SERVER['REQUEST_URI'];
	$params     = explode("/", $request);
	return $params[$i];
}
  
  