<?php
function __autoload($classname){
	var_dump($classname);
	// If you are using this outside the ccstools directory just change the path where the classes sitting down
	include '../classes/'.$classname.'_class.php';
}
// GENERATE TRANSACTION NUMBER
$generate = new Generate();
//Return an array( 'error' => false, 'code' => 200, 'message' => 'No error found.', 'generated' => "RGPS000000184391")
$code = $generate->generateCode(
	array(
		"db_name" => "../test.db", // IF using sqlite value should be the path of the database
		// "db_name" => "test",
		"table_name" => "files",
		"ai_column" => "id",
		"base_format" => "HHD-",
		"zero_format" => "000000000000"
	)
);

var_dump($code);
?>