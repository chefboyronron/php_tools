<?php
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}
$helper = new Helper();
$validate = $helper->checkEmailFormat("ron@gmail.com");

var_dump($validate);
?>
