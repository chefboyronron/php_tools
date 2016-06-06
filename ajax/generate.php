<?php
/**********************************************************************************************
 *	Ccstools Ver. 1.0.0 for developer
 * Ccstools also available using php
 * For security database name, table name and auto increment  column are set here
**********************************************************************************************/
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}
if(isset($_POST['mode'])){
	$mode = $_POST['mode'];
	if($mode == "generateCode"){
		$base_format = $_POST['base_format'];
		$zero_format = $_POST['zero_format'];
		$generate = new Generate();
		$code = $generate->generateCode(
			array(
				"db_name" => "../test.db",
				"table_name" => "files",
				"ai_column" => "id", // nums be datatype(int) and Auto Incremented
				"base_format" => $base_format,
				"zero_format" => $zero_format
			)
		);
		echo $code['generated'];
	}
	
	if($mode == "generateUID"){
		$generate = new Generate();
		$uid = $generate->generateUID();
		echo $uid;
	}
}else{
	echo "<h1>Not Found</h1>";
	echo "The requested URL ".$_SERVER["PHP_SELF"]." was not found on this server.";
	echo "<hr>";
	echo "Apache/2.4.9 (Win32) PHP/5.5.12 Server at localhost Port 80";
}

?>