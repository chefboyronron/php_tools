<?php
/********************************************************************************
 *	[REGISTRATION]
 *	Module: Helper
 *	Method: registerAccount();
 *	Sub-Method: validateRegistration(), generateUID(), hashPassword()
 *	Params : (array)
 *		[database] => (array) {required}
 *			[name] => "database_name"(string) {required}
 *				- ! if using sqlite please include the path of database.
 *			[table] => "table_name"(string) {required}
 *			[columns] => (array) {required}
 *				- list of column you want to insert the data array(key["column_name"]=>value["designated_value"]) [associative_array]
 *		[validate] => (array) {required}
 *			[columns] => (array) {required}
 *				- list of column you want to check if record/s is existing.
 * 	[redirect] => (string) - url where you want to redirect the user {Not required}
 *
 *	[LOGIN]
 *	Module: Helper
 *	Method: loginAccount();
 *	Sub-method: selectRecords(), checkEmailFormat(), hashPassword()
 *	Params : (array)
 *		[database] => (array) {required}
 *			[name] => "database_name"(string) {required}
 *				- ! if using sqlite please include the path of database.
 *			[table] => "table_name"(string) {required}
 *			[columns] => (array) {required}
 *				- list of column selected to query array("column_name") [numeric_array]
 *			[or] => array([column_name] => [designated_value], [...]=>[...], .......) {required}
 *			[and] => array([column_name] => [designated_value], [...]=>[...], .......) {required}
 *		[session] => (array) {required}
 * 		[type] => (string) "session", "cookies" {required}
 *			[name] => array("session_name","another_name",.....) [numeric_array]
 *			[expiration] => (int) timestamp where the session expired {required in type [cookies]}
 * 	[redirect] =>  (string) - url where you want to redirect the user {Not required}
 *
 * [LOGOUT]
 *	Module: Helper
 * Method: logoutAccount();
 *	Params:
 *		[redirect] => (string)  url where you want to redirect the user {required}
 *
 *
 ********************************************************************************/
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}

//Create instance variable to use in this module
$helper = new Helper();
$helper->setTimezone("Asia/Manila");
$generate = new Generate();

//Sessions
echo "<h2>SESSIONS</h2>";
$helper->startSession();
echo '$_SESSION';
var_dump($_SESSION);
echo '$_COOKIE';
var_dump($_COOKIE);

//Registration
echo "<h2>Registration</h2>";
$email = "ccsronron777@gmail.com";
$validate_email = $helper->checkEmailFormat($email);
$password_plain = "mypassword";
$password = $helper->hashPassword($password_plain, "md5");
$date = date("Y-m-d H:i:s");
$uid = $generate->generateUID();
$username = "chefboyronron777";

if($validate_email['error'] == false && $password['hash'] != ""){
	
	$register = $helper->registerAccount(
		array(
			"database" => array(
				// "name" => "test",
				"name" => "../test.db",
				"table" => "site_users",
				"columns" => array(
					"uid" => $uid,
					"email" => $email,
					"username" => $username,
					"password" => $password["hash"],
					"registered_date" => $date,
					"last_login" => $date,
				),
			),
			"validate" => array(
				"columns" => array(
					"uid" => $uid,
					"email" => $email,
					"username" => $username
				)
			),
			"auto_login" => true,
			"redirect" => "http://www.google.com"
		)
	);
	var_dump($register);
}else{
	var_dump($validate_email);
}


//Logout
echo "<h2>Logout Result</h2>";
if(isset($_POST['logout'])){
	$logout = $helper->logoutAccount($_SERVER['PHP_SELF']);
	var_dump($logout);
}

//Login
echo "<h2>Login Results</h2>";
if(isset($_POST['login'])){
	$password = $helper->hashPassword($_POST['password'], "md5");
	$username = $_POST['username'];
	$login = $helper->loginAccount(
		array(
			"database" => array(
				// "name" => "test",
				"name" => "../test.db",
				"table" => "site_users",
				"columns" => array("uid", "email", "username", "password"),
				"or" => array(
					"email" => $username,
					"username" => $username
				),
				"and" => array(
					"password" => $password['hash']
				)
			),
			"session" => array(
				"type" => "cookies", //[session], [cookies]
				"name" => array("uid","email","username"),
				"expiration" => 60 * 60 *12
			),
			"redirect" => $_SERVER['PHP_SELF']
		)
	);
	var_dump($login);
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>REG-IN-OUT</title>
</head>
<body>
	<h2>Login Form</h2>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="login-form">
		<input type="text" name="username" placeholder="Username">
		<input type="password" name="password" placeholder="Password">
		<input type="submit" name="login" value="Login">
	</form>
	<h2>Logout Button</h2>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="login-form">
		<input type="submit" name="logout" value="Logout">
	</form>
	
</body>
</html>
