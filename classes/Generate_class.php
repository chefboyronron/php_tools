<?php
/**********************************************************************************************
 *	Ccstools Ver. 1.0.0 for developer  BY: ccs0rs00
 * Ccstools also available using ajax
 *	Example usage
 *
	function __autoload($classname){
		// If you are using this outside the ccstools directory just change the path where the classes/classname_class.php located
		include 'classes/'.$classname.'_class.php';
	}
 * GENERATE TRANSACTION NUMBER
		$generate = new Generate();
		//Return an array( 'error' => false, 'code' => 200, 'message' => 'No error found.', 'generated' => "RGPS000000184391")
		$code = $generate->generateCode(
			array(
				"db_name" => "hallo2mall",
				"table_name" => "cscart_users",
				"ai_column" => "user_id",
				"base_format" => "RGPS",
				"zero_format" => "000000000000"
			)
		);
		echo $code['generated'];
 * GENERATE UNIQUE USER ID
		$generate = new Generate();
		$uid = $generate->generateUID();
		echo $uid;
***********************************************************************************************/

class Generate extends Ccstool {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function generator($base,$zeros,$gen){
		$zeros_len = strlen($zeros);
		$gen_len = strlen($gen);
		return $generated = $base . substr($zeros,0,$zeros_len-$gen_len).$gen;
	}
	
	public function generateCode($params = array()){
		
		$this->response['error'] = false;
		$this->response['code'] = 200;
		$this->response['message'] = "No error found.";
		if(is_array($params)){
			if(count($params) == 0){
				$this->response['error'] = true;
				$this->response['code'] = 503;
				$this->response['message'] = 'Parameter must be an array([db_name => (string)database name], [table_name => (string)table name], [ai_column => (string)auto increment column name])';
			}else{
				if(!isset($params['zero_format']) || $params['zero_format'] == ""){
					$this->response['error'] = true;
					$this->response['code'] = 503;
					$this->response['message'] = "zero_format is required. EX: 00000000";
					$this->response['generated'] = false;
				}
				if(!isset($params['ai_column']) || $params['ai_column'] == ""){
					$this->response['error'] = true;
					$this->response['code'] = 503;
					$this->response['message'] = "ai_column (AUTO INCREMENT) is required.";
					$this->response['generated'] = false;
				}else{
					if(strtolower(ENGINE) != "sqlite"){
						$type = $this->getDataType($params['db_name'],$params['table_name'],$params['ai_column']);
						if($type != "LONG" && $type != "INT24"){
							$this->response['error'] = true;
							$this->response['code'] = 503;
							$this->response['message'] = "ai_column value must be INT data type column.";
							$this->response['generated'] = false;
						}	
					}
				}
				if(!isset($params['table_name']) || $params['table_name'] == ""){
					$this->response['error'] = true;
					$this->response['code'] = 503;
					$this->response['message'] = "table_name is required.";
					$this->response['generated'] = false;
				}
				if(!isset($params['db_name']) || $params['db_name'] == ""){
					$this->response['error'] = true;
					$this->response['code'] = 503;
					$this->response['message'] = "db_name is required.";
					$this->response['generated'] = false;
				}
				
				if($this->response['error'] == false || $this->response['code'] == 200){
					
					$sql = 'SELECT MAX('.$params['ai_column'].') AS result FROM '.$params['table_name'].'';
					$prep = $this->connection($params['db_name'])->prepare($sql);
					$exec = $prep->execute();
					
					if($exec){
						$count = $prep->fetch(PDO::FETCH_ASSOC);
						if($count > 0){
							$this->response['generated'] = $this->generator($params['base_format'], $params['zero_format'], $count['result']+1);
						}else{
							$this->response['generated'] = $this->generator($params['base_format'], $params['zero_format'], $count['result']);
						}
					}else{
						$this->response['error'] = true;
						$this->response['code'] = 503;
						$this->response['message'] = "table_name or ai_column is not exists.";
						$this->response['generated'] = false;
					}
				}
			}
			return $this->response;
		}
		$this->closeConnection();
	}
	public function generateUID(){
		$uid = md5(uniqid(time(), true));
		return $uid;
	}
}
?>