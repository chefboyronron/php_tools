<?php

//password compatibility
/*
   [md4], [md5md5], [crc32b], [crc32], [sha1], [tiger128,3],   [haval192,3], [haval224,3], [tiger160,3], [haval160,3], [haval256,3], [tiger192,3], [haval128,3], [tiger192,4], [tiger128,4], [tiger160,4 ], [haval160,4], [haval192,4], [haval256,4], [adler32] [haval128,4], [haval224,4], [ripemd256], [haval160,5], [haval128,5], [haval224,5], [haval192,5], [haval256,5], [sha256], [ripemd128]        [ripemd160], [ripemd320], [sha384], [sha512], [gost], [whirlpool], [snefru], [md2]
*/

class Helper extends Ccstool {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function table( $params = array() ){
		
		$this->response(false, 200, "No error found.");
		$this->response['css'] = "";
		$this->response['output'] = "";
		$output = "";
		
		if(!is_array($params)){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "parameter must be an array";
		}else{
			if(!isset($params['table']) || $params['table'] == ""){
				if(!isset($param['table']['responsive'])){
					$params['table']['responsive'] = false;
					$is_responsive = "";
				}
				if(!isset($param['table']['class'])){
					$params['table']['class'] = "table";
				}
				if(!isset($param['table']['thead'])){
					$params['table']['thead'] = true;
				}
			}else{
				if(!isset($params['table']['theadLabel'])){
					$params['table']['theadLabel'] = array();
					for( $label=0; $label < count($params['table']['theadLabel']); $label++ ){
						$params['table']['theadLabel'][$label] = "";
					}
				}
			}
			
			if(!isset($params['config']) || $params['config'] == ""){
				$library = strtolower($params['library']);
				switch ($library){
					
					case "bootstrap" :
						$this->response['assets'] = '
							<link rel="stylesheet" href="http://'.$_SERVER['HTTP_HOST'].'/snippets_ron/ccstools/libraries/bootstrap/css/bootstrap.min.css">
							<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].'/snippets_ron/ccstools/libraries/bootstrap/js/bootstrap.min.js"></script>
						';
						break;
					case "materialize" :
						$this->response['assets'] = '
							<link rel="stylesheet" href="http://'.$_SERVER['HTTP_HOST'].'/snippets_ron/ccstools/libraries/materialize/css/materialize.min.css">
							<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].'/snippets_ron/ccstools/libraries/materialize/js/materialize.min.js"></script>
						';
						break;
					default : 
						$this->response['assets'] = '
							<link rel="stylesheet" href="http://'.$_SERVER['HTTP_HOST'].'/snippets_ron/ccstools/libraries/bootstrap/css/bootstrap.min.css">
							<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].'/snippets_ron/ccstools/libraries/bootstrap/js/bootstrap.min.js"></script>
						';
				}
			}
			
			if(!isset($params['column']) || $params['column'] == ""){
				$params['column'] = 1;
			}
			
			if(!isset($params['row']) || $params['row'] == ""){
				$params['row'] = 1;
			}
			
			if($this->response['error'] == false && $this->response['code'] == 200){
				
				if($params['table']['responsive'] == true){
					$is_responsive = "responsive-table";
					$output .= '<div class="table-responsive">';
				}else{
					$is_responsive = "";
				}
				
				$output .= '<table class="'.$params['table']['class'].' '.$is_responsive.'">';
					
					if($params['table']['thead'] == true){
						$output .= '<thead><tr>';
							for($col=0;$col < count($params['table']['theadLabel']);$col++){
								$output .= '<th>';
									$output .= $params['table']['theadLabel'][$col];
								$output .= '</th>';
							}
						$output .= '</tr></thead>';
					}
					$output .= '<tbody>'; 
					
					$records = $this->paginateResult($params['data']['database'], $params['data']['table'], $params['data']['columns'], $params['options']);
					
					if(isset($params['data']['json'])){
						$encoded = $params['data']['json'];
					}else{
						$encoded = array();
					}
					
					foreach($records as $record){
						$output .= '<tr>';
						foreach($record as $k => $v){
							if(in_array($k, $encoded)){
								$output .= '<td style="vertical-align:top;">';
									$decoded = json_decode($v);
									$d = 1;
									foreach($decoded as $dk => $dv){
										if(file_exists("classes/Reciever.php")){
											$reciever = '/classes/Reciever.php?file='. $params['file']['path']."/".$dv;
										}else if(file_exists("../classes/Reciever.php")){
											$reciever = '../classes/Reciever.php?file=../'. $params['file']['path']."/".$dv;
										}else if(file_exists("../../classes/Reciever.php")){
											$reciever = '../../classes/Reciever.php?file=../../'. $params['file']['path']."/".$dv;
										}else{
											$reciever = '../../../classes/Reciever.php?file=../../../'. $params['file']['path']."/".$dv;
										}
										$output .= $d . '. <a href="'.$reciever.'">' . $dv . '</a><br>';
										$d++;
									}
								$output .= '</td>';
							}else{
								$output .= '<td style="vertical-align:top;">';
									$output .= $v;
								$output .= '</td>';
							}
						}
						$output .= '</tr>';
					}
					
					$output .= '<tbody>';
				
				$output .= '</table>';
				
				if($params['table']['responsive'] == true){
					$output .= '</div>';
				}
				
				$links = $this->paginateLinks($params['data']['database'], $params['data']['table'], $params['data']['columns'], $params['options']);
				
				$this->response['output'] = $output;
				$this->response['links'] = $links;
			}
			
		}
		return $this->response;
	}
	
	public function startSession($params = true){
		$version = phpversion();
		if( $version >= 5.4 ){
			if($params == true){
				if (session_status() == PHP_SESSION_NONE) {
					session_start();
				}
			}else{
				session_start();
			}
		}else{
			if($params == true){
				if(session_id() == '') {
					session_start();
				}
			}else{
				session_start();
			}
		}
	}
	
	public function checkEmailFormat($email = ""){
		
		$this->response(false,200,"No error found.");
		
		$regex = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/"; 
		if ( !preg_match( $regex, $email ) ) {
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "Invalid email address format.";
		}
		return $this->response;
	}
	
	public function cutCharactersJA($word, $start, $end){
		
		$wordLen = strlen($word);
		if($wordLen > $end){
			$new_word = mb_strcut($word, $start, $end, 'UTF-8') . "。。。";
		}else{
			$new_word = $word;
		}
		return $new_word;
	}
	
	public function setTimezone($timezone){
		date_default_timezone_get();
		date_default_timezone_set($timezone);
	}
	
	public function timeToAgo($timestamp){
		
		$etime = time() - $timestamp;
		if ($etime < 1){
			return 'Now';
		}
		$a = array( 
			365 * 24 * 60 * 60  =>  'year',
			30 * 24 * 60 * 60  =>  'month',
			24 * 60 * 60  =>  'day',
			60 * 60  =>  'hour',
			60  =>  'minute',
			1  =>  'second'
		);
		$a_plural = array(
			'year'   => 'years',
			'month'  => 'months',
			'day'    => 'days',
			'hour'   => 'hours',
			'minute' => 'minutes',
			'second' => 'seconds'
		);
		foreach ($a as $secs => $str){
			$d = $etime / $secs;
			if ($d >= 1){
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
			}
		}
	}
	
	public function htpasswdCreateAuth($username, $plainpasswd) {
		
		$tmp = "";
		$salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
		$len = strlen($plainpasswd);
		$text = $plainpasswd.'$apr1$'.$salt;
		$bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
		for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
		for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $plainpasswd{0}; }
		$bin = pack("H32", md5($text));
		for($i = 0; $i < 1000; $i++)
		{
			$new = ($i & 1) ? $plainpasswd : $bin;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $plainpasswd;
			$new .= ($i & 1) ? $bin : $plainpasswd;
			$bin = pack("H32", md5($new));
		}
		for ($i = 0; $i < 5; $i++)
		{
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
		}
		$tmp = chr(0).chr(0).$bin[11].$tmp;
		$tmp = strtr(strrev(substr(base64_encode($tmp), 2)),
		"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
		"./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
		return $username.":$"."apr1"."$".$salt."$".$tmp;
	}
	
	public function validateRegistration( $params = array() ){
		
		$this->response(false,200,"No error found.");
		$cond = array();
		
		if(!isset($params['database']['name'])){
			$this->response(true, 503, "Parameter error : databse > name => [database_name]");
		}
		
		if(!isset($params['database']['table'])){
			$this->response(true, 503, "Parameter error : databse > table => [table_name]");
		}
		
		if(isset($params['validate']['columns'])){
			$columns = $params['validate']['columns'];
			foreach($columns as $col => $cval){
				$cond[] = $col . " = '" . $cval;
			}
			$cond = implode("' OR ", $cond) . "'";
		}else{
			$this->response(true, 503, "Parameter error : validate > columns = array(key[colunm_name]=>value[value to validate])");
		}
		if($this->response['error'] == false && $this->response['code'] == 200){
			
			$sql = " SELECT COUNT(*) FROM ".$params['database']['table']." WHERE " . $cond;
			$prep = $this->connection($params['database']['name'])->query($sql);
			$count = $prep->fetch(PDO::FETCH_NUM);
			
			if($count[0] > 0){
				$this->response(true, 503, "Account already exists.");
			}
			
		}
		
		return $this->response;
	}
	
	public function registerAccount( $settings = array() ){
		
		$this->response(false,200,"No error found.");
		
		if(!is_array($settings) || count($settings) <= 0){
			$this->response(true, 503, "Parameter must be an array and not empty.");
		}
		if(!isset($settings['database']['columns'])){
			$this->response(true, 503, "Parameter error : databse > columns => array(key[colunm_name]=>value[inserted_value])");
		}
		
		if( $this->response["code"] == 200 && $this->response["error"] == false ){
			$checkAccount = $this->validateRegistration($settings);
			if($this->response['code'] == 200 && $this->response['error'] == false){
				$insert = $this->insertRecords($settings['database']['name'], $settings['database']['table'], $settings['database']['columns']);
				if($insert){
					$this->response["message"] = "Registration success.";
					if(isset($settings["redirect"])){
						if($settings['redirect'] != ""){
							if(isset($settings['auto_login'])){
								
							}
							header("location:".$settings['redirect']);
							exit;
						}
					}
				}
			}
		}
		if(isset($this->response["hash"])){
			unset($this->response["hash"]);
		}
		return $this->response;
	}
	
	public function loginAccount( $settings = array() ){
		
		$this->response(false, 200, "No error found.");
		
		if(count($settings) <= 0 || !is_array($settings)){
			$this->response(true, 503, "Parameter must be an array and not empty.");
		}
		
		if($this->response['code'] == 200 && $this->response['error'] == false ){
			
			$cond = array(
				"columns" => array()
			);
			
			if(isset($settings['database']['columns'])){
				$cond['columns'] = $settings['database']['columns'];
			}else{
				$cond['columns'][] = "*";
			}
			
			if(isset($settings['database']['or'])){
				$cond['or'] = $settings['database']['or'];
			}
			if(isset($settings['database']['and'])){
				$cond['and'] = $settings['database']['and'];
			}
			if(!isset($settings['database']['or']) && !isset($settings['database']['and'])){
				$cond['columns'][] = "*";
			}
			
			$row = $this->selectRecords($settings['database']['name'],$settings['database']['table'], $cond);
			if($row != null){
				$this->startSession();
				if(strtolower($settings['session']['type']) == "cookies"){
					$cookies = array();
					foreach($settings['session']['name'] as $cook){
						$cookies[$cook] = $row[$cook];
					}
					if(isset($settings['session']['expiration'])){
						$expiration = $settings['session']['expiration'];
					}else{
						$expiration = COOKIE_EXPIRED;
					}
					$cooked = setcookie("auth",json_encode($cookies),time() + $expiration,"/");
					if($cooked){
						if(isset($settings['redirect'])){
							if($settings['redirect'] != ""){
								header("location:".$settings['redirect']);
								exit;
							}
						}
						$this->response(false, 200, "Login success.");
					}
				}else{
					if(isset($_SESSION["auth"])){
						unset($_SESSION["auth"]);
					}
					foreach($settings['session']['name'] as $kses){
						$_SESSION['auth'][$kses] = $row[$kses];
					}
					if(isset($_SESSION["auth"])){
						if(count($_SESSION['auth']) > 0){
							if(isset($settings['redirect'])){
								if($settings['redirect'] != ""){
									header("location:".$settings['redirect']);
									exit;
								}
							}
							$this->response(false, 200, "Login success.");
						}
					}
				}
			}else{
				$this->startSession();
				if(isset($_SESSION['auth'])){
					unset($_SESSION['auth']);
				}
				if(isset($_COOKIE['auth'])){
					setcookie("auth",$_COOKIE['auth'],time()-1,"/");
				}
				$this->response(false, 200, "Login failed.");
			}
		}
		return $this->response;
	}
	
	public function logoutAccount($redirect = ""){
		$this->startSession();
		$this->response(false, 200, "Logout success.");
		
		if( $redirect == "" || is_array($redirect) ){
			$this->response(true, 503, "Invalid redirect parameter, redirect not valid.");
		}
		
		if(isset($_SESSION['auth'])){
			unset($_SESSION['auth']);
			if(isset($_SESSION['auth'])){
				$this->response(true, 503, "Logout failed.");
			}
			if( $redirect != "" || !is_array($redirect) ){
				header("location:".$redirect);
				exit;
			}
		}else{
			$this->response(false, 200, "Already logout.");
		}
		if(isset($_COOKIE['auth'])){
			$logout = setcookie("auth",$_COOKIE['auth'],time()-1,"/");
			if(!$logout){
				$this->response(true, 200, "Logout falied.");
			}
			if( $redirect != "" || !is_array($redirect) ){
				header("location:".$redirect);
				exit;
			}
		}else{
			$this->response(false, 200, "Already logout.");
		}
		return $this->response;
	}
	
	public function handleMagicQuotes(){
		if (get_magic_quotes_gpc()) {
			$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
			while (list($key, $val) = each($process)) {
				foreach ($val as $k => $v) {
					unset($process[$key][$k]);
					if (is_array($v)) {
						$process[$key][stripslashes($k)] = $v;
						$process[] = &$process[$key][stripslashes($k)];
					} else {
						$process[$key][stripslashes($k)] = stripslashes($v);
					}
				}
			}
			unset($process);
			return true;
		}else{
			return false;
		}
	}
	
}
?>