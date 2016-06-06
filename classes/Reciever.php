<?php

function __autoload($classname){
	include $classname . "_class.php";
}

if(isset($_GET["file"])){
	$the_file = $_GET['file'];
	$Files = new Files();
	$Files->forceDownload($the_file);
}



?>