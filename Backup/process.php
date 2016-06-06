<?php
if(isset($_POST['mode'])){
	
	include_once "../control/assets/php/connection.php";
	function APIrequest($url, $data){
		$build = $url . http_build_query($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $build);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1);
		$output = curl_exec($ch);
		if ($output === FALSE) {
		   $output = "cURL Error: " . curl_error($ch);
		}else{
			$output = json_decode($output);
		}
		$info = curl_getinfo($ch);
		return $output;
	}
	function generate_account($sqlite,$url,$transaction_number,$price){
		$sql = "SELECT * FROM anonymous WHERE is_selected = 0 ORDER BY RANDOM() LIMIT 1";
		$query = $sqlite->prepare($sql);
		$result = $query->execute();
		$row = $query->fetch(PDO::FETCH_ASSOC);
		$data = array(
			"key"=>"R1bC9Gp3110S0o",
			"mode"=>"check_payment",
			"email"=> $row['email']
		);
		$payment_status = APIrequest($url, $data);
		if($payment_status->record_count == 0){
			$data = array(
				"key"=>"R1bC9Gp3110S0o",
				"mode"=>"cart",
				"product_id" => 296426,
				"transaction_number" => $transaction_number,
				"email" => $row['email'],
				"amount" => 1,
				"price" => $price,
				"partner_id" => 8
			);
			$cart = APIrequest($url, $data);
			if($cart->code == 503){
				$row['email'] = "";
				$row['password'] = "";
				$row['is_selected'] = 0;
				$row['id'] = 0;
				return $row;
			}else{
				return $row;
			}
		}else{
			generate_account($sqlite,$url,$transaction_number,$price);
		}	
	}
	$sql = "SELECT MAX(id) as max FROM donations";
	$result = $sqlite->query($sql);
	$max = $result->fetch(PDO::FETCH_ASSOC);
	if($max['max'] == "" || $max['max'] == 0){
		$append = 1;
	}else{
		$append = ($max['max']+1);
	}
	function generate($gen){
		$base = "HHD-".date('Y')."-";
		$zeros = '000000';
		$zeros_len = strlen($zeros);
		$gen_len = strlen($gen);
		return $generated = $base . substr($zeros,0,$zeros_len-$gen_len).$gen;
	}
	$transaction_number = generate($append);
	$uid = md5(uniqid(time(), true));
	$url = "http://www.hallohallo.com/contest/memberdb_api/?";
	$mode = $_POST['mode'];
	
	if(!empty($sqlite)){
		
		if($mode == 'guest'){
			
			$response = array();
			$response['code'] = 200;
			
			$user_email = $_POST['email'];
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$password = $_POST['password'];
			$hash_password = md5($password);
			$phone = $_POST['contact_number'];
			$birthdate = $_POST['firstname'];
			$gender = $_POST['gender'];
			$price = $_POST['price'];
			$type = "Guest";
			$date = date("Y-m-d h:m:s");
			$payment_status = "New";
			
			$user_id = 0;
			
			$data = array(
				"key"=>"R1bC9Gp3110S0o",
				"mode"=>"get",
				"email"=>$user_email 
			);
			$output = APIrequest($url, $data);
			$user_account = $output[0];
			$user_account = $user_account->user_account;
			
			if(isset($user_account->count) ){
				$response['message'] = "ok";
				$response['code'] = 200;
				
				$sql = "INSERT INTO donations ( uid, user_id, firstname, lastname, email, password, birthdate, gender, amount, type, transaction_number, date, payment_status, phone ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				
				$query = $sqlite->prepare($sql);
				$query->bindParam(1, $uid);
				$query->bindParam(2, $user_id);
				$query->bindParam(3, $firstname);
				$query->bindParam(4, $lastname);
				$query->bindParam(5, $user_email);
				$query->bindParam(6, $hash_password);
				$query->bindParam(7, $birthdate);
				$query->bindParam(8, $gender);
				$query->bindParam(9, $price);
				$query->bindParam(10, $type);
				$query->bindParam(11, $transaction_number);
				$query->bindParam(12, $date);
				$query->bindParam(13, $payment_status);
				$query->bindParam(14, $phone);
				$result = $query->execute();
				
				if($result){
					$id = $sqlite->lastInsertId();
					$response['inserted_id'] = $id;
					$response['username'] = $user_email;
					
					$len_pass = strlen($password);
					$dot_password = "";
					for($x = 1; $x <= $len_pass; $x++){
						$dot_password .= "*";
					}
					$response['password'] = $dot_password;
					$data = array(
						"key" => "R1bC9Gp3110S0o",
						"mode" => "put",
						"user_id"=> $id,
						"fname" => $firstname,
						"lname" => $lastname,
						"email" => $user_email,
						"birthdate" => $birthdate,
						"contact_number" => $phone,
						"p1" => $password,
						"p2" => $password,
						"partner_id" => 8,
						"force_active"=>"yes"
					);
					$output = APIrequest($url, $data);
					if($output->code == 200){
						
						$memberdb = $output->registration;
						$paired_uid = $memberdb->uid;
						
						$mall = $output->mall;
						$mregistration = $mall->registration;
						$user_id = $mregistration->user_id;
						
						//Api cart data
						$data = array(
							"key"=>"R1bC9Gp3110S0o",
							"mode"=>"cart",
							"product_id" => 296426,
							"transaction_number" => $transaction_number,
							"email" => $user_email,
							"amount" => 1,
							"price" => $price,
							"partner_id" => 8
						);
						$cart = APIrequest($url, $data);
						if($cart->code == 503){
							$response['message'] = $cart->message;
							$response['code'] = 503;
						}else{
							$sql = "UPDATE donations SET uid='$paired_uid', user_id='$user_id' WHERE id='$id'";
							$result = $sqlite->exec($sql);
							if($result){
								$response['message'] = "success";
								$response['code'] = 200;
							}else{
								$response['message'] = $result->errorCode();
								$response['code'] = 503;
							}
						}
					}
				}else{
					$response['message'] = "ok";
					$response['code'] = 503;
				}
			}else{
				$response['message'] = "Email address not available.";
				$response['code'] = 503;
			}
			
			echo json_encode($response);
			
			
		}
		
		if($mode == 'member'){
			
			$response = array();
			$response['code'] = 200;
			$email = $_POST['email'];
			$price = $_POST['price'];
			$data = array(
				"key"=>"R1bC9Gp3110S0o",
				"mode"=>"get",
				"email"=>$email 
			);
			$output = APIrequest($url, $data);
			$user_account = $output[0];
			$user_account = $user_account->user_account;
			if(isset($user_account->count) ){
				$response['message'] = "No account found in Hallo Hallo allicance.";
				$response['code'] = 503;
			}else{
				$response['code'] = 200;
				$mall_info = $output[4];
				$mall_info = $mall_info->mall;
				$member_info = $output[2];
				$member_info = $member_info->member_info;
				//Data for records
				$user_id = $mall_info->user_id;
				$firstname = $user_account->firstname;
				$lastname = $user_account->lastname;
				$user_email = $user_account->email;
				$password = $user_account->password;
				$birthdate = $user_account->birthday;
				$gender = $member_info->gender;
				if($gender == 1){
					$gender = "MALE";
				}else if($gender == 2){
					$gender = "FEMALE";
				}else{
					$gender = "";
				}
				$type = "Member";
				$date = date("Y-m-d h:m:s");
				$payment_status = "New";
				$phone = "";
				//Api cart data
				$data = array(
					"key"=>"R1bC9Gp3110S0o",
					"mode"=>"cart",
					"product_id" => 296426,
					"transaction_number" => $transaction_number,
					"email" => $email,
					"amount" => 1,
					"price" => $price,
					"partner_id" => 8
				);
				$cart = APIrequest($url, $data);
				if($cart->code == 503){
					$response['message'] = $cart->message;
					$response['code'] = 503;
				}else{
					$response['mall'] = "success";
					$sql = "INSERT INTO donations ( uid, user_id, firstname, lastname, email, password, birthdate, gender, amount, type, transaction_number, date, payment_status, phone ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$query = $sqlite->prepare($sql);
					$query->bindParam(1, $uid);
					$query->bindParam(2, $user_id);
					$query->bindParam(3, $firstname);
					$query->bindParam(4, $lastname);
					$query->bindParam(5, $user_email);
					$query->bindParam(6, $password);
					$query->bindParam(7, $birthdate);
					$query->bindParam(8, $gender);
					$query->bindParam(9, $price);
					$query->bindParam(10, $type);
					$query->bindParam(11, $transaction_number);
					$query->bindParam(12, $date);
					$query->bindParam(13, $payment_status);
					$query->bindParam(14, $phone);
					$result = $query->execute();
					if($result){
						$response['message'] = "success";
						$response['code'] = 200;
					}else{
						$response['message'] = $result->errorCode();
						$response['code'] = 503;
					}
				}
			}
			echo json_encode($response);
			curl_close($ch);
		}
		
		if($mode == 'anonymous'){
			
			$response = array();
			$response['code'] = 200;
			
			$user_id = 0;
			$firstname = "Anonymous";
			$lastname = "Anonymous";
			$user_email = "Anonymous";
			$password = "";
			$birthdate = "0000-00-00";
			$gender = "Anonymous";
			$price = $_POST['price'];
			$type = "Anonymous";
			$transaction_number = $transaction_number;
			$date = "0000-00-00 00:00:00";
			$payment_status = "NEW";
			$phone = "Anonymous";
			
			$sql = "INSERT INTO donations ( uid, user_id, firstname, lastname, email, password, birthdate, gender, amount, type, transaction_number, date, payment_status, phone ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$query = $sqlite->prepare($sql);
			$query->bindParam(1, $uid);
			$query->bindParam(2, $user_id);
			$query->bindParam(3, $firstname);
			$query->bindParam(4, $lastname);
			$query->bindParam(5, $user_email);
			$query->bindParam(6, $password);
			$query->bindParam(7, $birthdate);
			$query->bindParam(8, $gender);
			$query->bindParam(9, $price);
			$query->bindParam(10, $type);
			$query->bindParam(11, $transaction_number);
			$query->bindParam(12, $date);
			$query->bindParam(13, $payment_status);
			$query->bindParam(14, $phone);
			$result = $query->execute();
			
			if($result){
				$sql = "SELECT * FROM anonymous";
				$query = $sqlite->prepare($sql);
				$result = $query->execute();
				while($row = $query->fetch(PDO::FETCH_ASSOC)){
					$data = array(
						"key"=>"R1bC9Gp3110S0o",
						"mode"=>"check_payment",
						"email"=> $row['email']
					);
					$payment_status = APIrequest($url, $data);
					if($payment_status->record_count == 0){
						$id = $row['id'];
						$sql = " UPDATE anonymous SET is_selected = 0 WHERE id = $id ";
						$query = $sqlite->prepare($sql);
						$result = $query->execute();
					}
				}
				$row = generate_account($sqlite,$url,$transaction_number,$price);
				$response['cart'] = $row;
				$id = $row['id'];
				$sql = " UPDATE anonymous SET is_selected='1' WHERE id=$id ";
				$query = $sqlite->prepare($sql);
				$result = $query->execute();
				if($result){
					$response['message'] = "success";
					$response['code'] = 200;
					$response['email'] = $row['email'];
					$response['password'] = $row['password'];
				}
			}else{
				$response['code'] = 503;
				$response['message'] = "Fatal Error: anonymous";
			}
			echo json_encode($response);
		}
		
	}else{
		echo "Connection Error";
	}
}
?>