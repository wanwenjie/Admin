<?php

$from = isset($_GET['from'])?$_GET['from']:'';
$c = isset($_GET['c'])?$_GET['c']:'';
$id = isset($_GET['id'])?$_GET['id']:'';
if(!empty($from)){
	if($from == 'llj'){
		header("location:http://www.sv77.net/llj.php?c=".$c."&id=".$id);
	}
	if($from == 'link'){
		$url = $_SERVER["QUERY_STRING"];
		$url = substr($url,stripos($url,'&')+1);
		header("location:http://jx.sv77.net/yunparse/index.php?url=".$url);
	}
}