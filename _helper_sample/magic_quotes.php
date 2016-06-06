<?php
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}
$helper = new Helper();

$helper->handleMagicQuotes();


?>
