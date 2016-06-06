<?php
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}

$mail = new Mail();

$headers = array(
			"type" => "html", // html, text
			"charset" => "UTF-8"
		);

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

$subject = "せん";

$to = array(
			"zumba@mailinator.com",
			"ccsronron@gmail.com"
		);
$from = "info@hallohallodeal.com";

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

$send_mail = $mail->sendMail( $from, $to, $subject, $message, $headers, $bcc, $cc, $reply_to );

var_dump($send_mail);























?>