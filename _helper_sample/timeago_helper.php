<?php
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}
$helper = new Helper();
$setTimezone = $helper->setTimezone("Asia/Manila");
$timestamp = strtotime("2016-1-8 15:44:00");
$time = $helper->timeToAgo($timestamp);
echo $time;

?>
