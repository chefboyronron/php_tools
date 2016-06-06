<?php

class Mail extends Ccstool {
	
	public $headers;
	public $message;
	public $subject;
	public $reciepient;
	public $bcc;
	public $cc;
	public $replyTo;
	
	public function __construct(){
		parent::__construct();
		$this->bcc = "";
		$this->cc = "";
	}
	
	public function sendMail( $from = "", $to = array(), $subject = "", $message = "", $headers = array(), $bcc = array(), $cc = array(), $reply_to = array() ){
		
		$this->response['error'] = false;
		$this->response['message'] = "No error found.";
		$this->response['code'] = 200;
		
		if(isset($headers['type'])){
			$headers['type'] = strtolower($headers['type']);
			if(!isset($headers['charset'])){
				$headers['charset'] = "UTF-8";
			}
			switch($headers['type']){
				case "html" : 
					$typo = 'MIME-Version: 1.0' . "\r\n";
					$typo .= "Content-type: text/html; charset=" . $headers['charset'] . "\r\n";
					$typo .= "X-Priority: 1\r\n"; 
					$typo .= "X-MSMail-Priority: High\r\n";  
					break;
				case "text" :
					$typo = 'MIME-Version: 1.0' . "\r\n";
					$typo .= "Content-type: text/plain; charset=" . $headers['charset'] . "\r\n";
					$typo .= "X-Priority: 1\r\n"; 
					$typo .= "X-MSMail-Priority: High\r\n"; 
					break;
				case "file" :
					$typo = '';
					break;
				default :
					$typo = '';
			}
		}else{
			$typo = 'MIME-Version: 1.0' . "\r\n";
			$typo .= "Content-type: text/plain; charset=" . $headers['charset'] . "\r\n";
			$typo .= "X-Priority: 1\r\n"; 
			$typo .= "X-MSMail-Priority: High\r\n"; 
		}
		
		$header = $typo;
		if( count($to) <= 0 ){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "You need to input atleast one reciepient";
		}
		$this->reciepient = implode(',', $to);
		$header .= 'To: ' . $this->reciepient . "\r\n";
			
		if($from == ""){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "You need to input an address where the mail came from.";
		}
		$header .= 'From: ' . $from . "\r\n";
		
		if( count($bcc) > 0 ){
			$this->bcc = implode(',', $bcc);
			$header .= 'Cc: ' . $this->bcc . "\r\n";
		}
		
		if( count($cc) > 0 ){
			$this->cc = implode(',', $cc);
			$header .= 'Bcc: ' . $this->cc . "\r\n";
		}
		
		if( count($reply_to) <= 0 ){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "You need to input atleast one email where reply to.";
		}
		$this->replyTo = implode(',', $reply_to);
		$header .= "Reply-To: " . $this->replyTo . "\r\n"; ;
		
		if($subject == ""){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "Email subject is required.";
		}
		$this->subject = $subject;
		
		if($message == ""){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "Message content must not be empty.";
		}
		$this->message = $message;
		
		if($this->response['error'] == false && $this->response['code'] == 200){
			$send = mail($this->reciepient, $this->subject, $this->message, $header);
			if(!$send){
				$this->response['error'] = true;
				$this->response['code'] = 503;
				$this->response['message'] = "Message not sent.";
			}else{
				$this->response['message'] = "Message Sent.";
			}
		}
		//return an array()
		return $this->response;
	}
	
	public function sendAttachment( $files = array(), $from = "", $to = array(), $subject = "", $message = "", $headers = array() ,$bcc = array(), $cc = array(), $reply_to = array() ){
		
		$this->response['error'] = false;
		$this->response['message'] = "No error found.";
		$this->response['code'] = 200;
		
		if(count($files) <= 0){
			$this->response['error'] = true;
			$this->response['message'] = "No file was uploaded.";
			$this->response['code'] = 503;
		}else{
			
			foreach($files as $k => $v){
				$file_tmp_name = $_FILES[$k]['tmp_name'];
				$file_name = $_FILES[$k]['name'];
				$file_size = $_FILES[$k]['size'];
				$file_type = $_FILES[$k]['type'];
				$file_error = $_FILES[$k]['error'];
			}
			
			if( count($to) <= 0 ){
				$this->response['error'] = true;
				$this->response['code'] = 503;
				$this->response['message'] = "You need to input atleast one reciepient";
			}
			$this->reciepient = implode(',', $to);
			
			if( count($reply_to) <= 0 ){
				$this->response['error'] = true;
				$this->response['code'] = 503;
				$this->response['message'] = "You need to input atleast one email where reply to.";
			}
			$this->replyTo = implode(',', $reply_to);
			
			if($file_error > 0){
				$this->response['error'] = true;
				$this->response['message'] = "File error.";
				$this->response['code'] = 503;
			}
			
			$handle = fopen($file_tmp_name, "r");
			$content = fread($handle, $file_size);
			fclose($handle);
			$encoded_content = chunk_split(base64_encode($content));
			$boundary = md5("ccstool");
			
			$header = "MIME-Version: 1.0\r\n"; 
			$header .= "From:".$from."\r\n"; 
			$header .= "Reply-To: ".$this->replyTo."" . "\r\n";
			if( count($bcc) > 0 ){
				$this->bcc = implode(',', $bcc);
				$header .= 'Cc: ' . $this->bcc . "\r\n";
			}
			
			if( count($cc) > 0 ){
				$this->cc = implode(',', $cc);
				$header .= 'Bcc: ' . $this->cc . "\r\n";
			}
			$header .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n"; 
			
			if(isset($headers['type'])){
				$headers['type'] = strtolower($headers['type']);
			}else{
				$headers['type'] = "plain";
			}
			if(!isset($headers['charset'])){
				$headers['charset'] = "UTF-8";
			}
			$body = "--$boundary\r\n";
			$body .= "Content-Type: text/".$headers['type']."; charset=".$headers['charset']."\r\n";
			$body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
			$body .= chunk_split(base64_encode($message)); 
			
			$body .= "--$boundary\r\n";
			$body .="Content-Type: $file_type; name=\"$file_name\"\r\n";
			$body .="Content-Disposition: attachment; filename=\"$file_name\"\r\n";
			$body .="Content-Transfer-Encoding: base64\r\n";
			$body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n"; 
			$body .= $encoded_content; 
			
			if($this->response['error'] == false && $this->response['code'] == 200){
				$sentMail = @mail($this->reciepient, $subject, $body, $header);
			}
			
		}
		return $this->response;
	}
	
}
?>