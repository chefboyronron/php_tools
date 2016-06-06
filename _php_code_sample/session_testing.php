<?php
function __autoload($classname){
	// If you are using this outside the ccstools directory just change the path where the classes sitting down
	include '../classes/'.$classname.'_class.php';
}
$helper = new Helper();

$helper->startSession();

echo "<h2>Cookie</h2>";
var_dump($_COOKIE);
if(isset($_COOKIE['auth'])){
	$auth = json_decode($_COOKIE['auth']);
	var_dump($auth);
}
echo "<h2>Session</h2>";
var_dump($_SESSION);

?>