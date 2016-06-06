<?php
if(isset($_FILES['file'])){
	
	var_dump($_FILES);
	
	function __autoload($classname){
		include '../classes/'.$classname.'_class.php';
	}

	$mail = new Mail();

	$from = "ccs0data@hotmail.com";
	$to = array(
		"ccsronron@gmail.com",
		"food@mailinator.com"
	);
	$subject = "My subject";

	$message = '
	  <p>せん</p>
	  <table>
		<tr>
		  <th>Person</th><th>Day</th><th>Month</th><th>Year</th>
		</tr>
		<tr>
		  <td>Joe</td><td>3rd</td><td>August</td><td>1970</td>
		</tr>
		<tr>
		  <td>Sally</td><td>17th</td><td>August</td><td>1973</td>
		</tr>
	  </table>
	';

	$headers = array(
		"type" => "html", //html, plain
		"charset" => "utf-8"
	);

	$bcc = array(
		"manimal@mailinator.com",
		"sfg@mailinator.com"
	);

	$cc = array(
		"hulahula@mailinator.com",
		"hbc@mailinator.com"
	);
	$reply_to = array(
		"ladylonglegs@mailinator.com"
	);

	$send = $mail->sendAttachment( $file = $_FILES, $from, $to, $subject, $message, $headers, $bcc, $cc, $reply_to );

	var_dump($send);
	
}

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Mail attachment</title>
</head>
<body>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file">
		<input type="submit" name="Send" value="Send">
	</form>
</body>
</html>

