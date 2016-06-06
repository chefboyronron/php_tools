<?php

function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}

$file = new Files();

$convertion = $file->digitalStorageConverter(  (1024 * 1024 * 5) * 1024); //5GB mb

var_dump($convertion);

?>