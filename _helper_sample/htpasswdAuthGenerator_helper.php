<?php
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}
$helper = new Helper();
$username = 'user1';
$password = 'htv00htv00';
$auth = $helper->htpasswdCreateAuth($username,$password);
echo $auth;
?>
