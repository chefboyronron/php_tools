<?php
if(!file_exists("config/config.php")){
	include "../config/config.php";
}else{
	include "config/config.php";
}

class Ccstool {
	
	public function __construct(){
		$this->host = HOST;
		$this->user = USER;
		$this->password = PASS;
		$this->response = array();
		$this->response["error"] = false;
		$this->response["code"] = 200;
		$this->response["message"] = "No error found.";
	}
	
	public function connection($db) {
		
		try {
			$engine = strtolower(ENGINE);
			if($engine == "sqlite"){
				$connection = new PDO("sqlite:".$db);
				$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			}else{
				$connection = new PDO('mysql:host=localhost;dbname='.$db.'', $this->user, $this->password);
				$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		return $connection;
		
	}
	
	public function closeConnection(){
		$this->connection()->close();
	}
	
	public function response($error = "", $code ="", $message = ""){
		
		if(is_array($message)){
			$this->response["error"] = $error;
			$this->response["code"] = $code;
			$this->response["message"][] = $message;
		}else{
			$this->response["error"] = $error;
			$this->response["code"] = $code;
			$this->response["message"] = $message;
		}
		
		
	}
	
	public function getDataType($db_name,$table_name,$column_name){
		
		$result = $this->connection($db_name)->query('SELECT '.$column_name.' FROM '. $table_name);
		$meta = $result->getColumnMeta(0);
		return $meta['native_type'];
		
	}
	
	public function recordCount($db_name, $sql){
		
		$prep = $this->connection($db_name)->prepare($sql);
		$prep->execute(); 
		$count = $prep->fetchColumn(); 
		return $count;
		
	}
	
	public function fetchRecords($db_name, $sql){
		$results = $this->connection($db_name)->query($sql);
		return $results;
	}
	
	public function selectRecords($db_name = "", $table_name = "", $cond = array()){
		
		if( count($cond) <= 0 || !is_array($cond) || $table_name == "" || is_array($table_name) || $db_name == "" || is_array($db_name) ){
			$this->response['message'] = array();
			if( $db_name == "" || is_array($db_name) ){
				$this->response(true, 503, array("Select error: first parameter must be a string."));
			}
			if( $table_name == "" || is_array($table_name) ){
				$this->response(true, 503, array("Select error: second parameter must be a string."));
			}
			if( !is_array($cond) ){
				$this->response(true, 503, array("Select error: third parameter must be an array."));
			}
		}
		
		if($this->response['error'] == false && $this->response['code'] == 200){
			
			if(isset($this->response['hash'])){
				unset($this->response['hash']);
			}
			
			$aor = array();
			$aand = array();
			if(isset($cond['columns'])){
				$columns = array_filter($cond['columns']);
				$columns = implode(",",$columns);
			}else{
				$columns = "*";
			}
			
			if( isset($cond['or']) || isset($cond['and']) ){
				
				$where = " WHERE ";
				
				if(isset($cond['or'])){
					foreach($cond['or'] as $ok => $ov){
						$aor[] = $ok . "='" . $ov . "'";
					}
					$or = "( " . implode(" OR ", $aor) . " )";
					$and = " AND ";
				}else{
					$or = "";
					$and = "";
				}
				
				if(isset($cond['and'])){
					foreach($cond['and'] as $ka => $va){
						$aand[] = $ka . "='" . $va ."'";
					}
					if(count($aand) > 0){
						$and = $and . implode(" AND ", $aand);
					}else{
						$and = implode(" AND ", $aand);
					}
				}else{
					$and = "";
				}
				
			}else{
				$where = "";
				$or = "";
				$and = "";
			}
			
			$sql = "SELECT ".$columns." FROM ". $table_name . $where . $or . $and;
			
			$result = $this->connection($db_name)->query($sql)->fetch(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}else{
				return $this->response(true, 503, "No record.");
			}
		}else{
			return $this->response;
		}
		
	}
	
	public function insertRecords($db_name = "", $table_name = "", $columns = array()){
		
		if(!is_array($columns)){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "Insert error: second parameter must be an array.";
		}
		if($table_name == "" || is_array($table_name)){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "Table name is missing.";
		}
		if($db_name == "" || is_array($db_name)){
			$this->response['error'] = true;
			$this->response['code'] = 503;
			$this->response['message'] = "Database name is missing.";
		}
		$cond = array();$targets = array();$values = array();$prep = array();$exec_k = array();$exec_v = array();$execute = array();$prep_stm = array();
		if($this->response['error'] == false && $this->response['code'] == 200){
			foreach($columns as $col => $cval){
				$targets[] = $col;
				$values[] = $cval;
				$prep[] = $col;
				$prep_stm[] = ":". $col;
			}
			
			$targets = implode(",", $targets);
			$prep_stm = implode(",",$prep_stm);
			
			$sql = "INSERT INTO ".$table_name."(".$targets.") VALUES(".$prep_stm.")";
			$prepare = $this->connection($db_name)->prepare($sql);
			
			foreach($prep as $kp => $pv){
				$exec_k[] = $pv;
			}
			foreach($values as $kv => $vv){
				$exec_v[] = $vv;
			}
			for($x=0;$x< count($exec_k); $x++){
				$replace = preg_replace("/:/","",$exec_k[$x]);
				$bind = $prepare->bindParam($exec_k[$x], $$replace);
			}
			
			for($x=0;$x< count($exec_k); $x++){
				$variables = preg_replace("/:/","",$exec_k[$x]);
				$$variables = $exec_v[$x];
			}
			$insert = $prepare->execute();
		}else{
			$insert = false;
		}
		return $insert;
	}
	
	public function hashPassword($password = "", $algo = "md5"){
		
		$this->response(false, 200, "No error found.");
		
		if($password == "" || is_array($password)){
			$this->response(true, 503, "Please input your password.");
			$this->response["hash"] = "";
		}else{
			$algo = strtolower($algo);
			$this->response["hash"] = hash($algo, $password, false);
		}
		return $this->response;
	}
	
	public function paginateResult( $db_name = "", $table_name = "", $columns = array(), $settings = array() ){
		$data = array();
		$x = 0;
		$columns = implode(',', $columns);
		
		if( count( $settings >= 0 ) ){
			
			if(isset($settings["per_page"] )){
				$per_page = " LIMIT " . $settings["per_page"] . " ";
			}else{
				$per_page = "";
			}
			
			if(isset($settings["order"] )){
				$order = " ORDER BY " . $settings["order"] . " ";
			}else{
				$order = "";
			}	
			
		}else{
			$per_page = "";
			$order = "";
		}
		
		$total_records = $this->recordCount($db_name, 'SELECT COUNT(*) FROM ' . $table_name);
		
		$last_page = ceil($total_records/$settings["per_page"]);
		
		if(isset($_REQUEST['page'])){
			$page = preg_replace('#[^0-9]#i','',$_REQUEST['page']);
			if($_REQUEST['page'] < 1 || $_REQUEST['page'] == ""){
				$page = $last_page;
			}else if($_REQUEST['page'] > $last_page){
				$page = $last_page;
			}else{
				$page = $page;
			}
		}else{
			$page = 1;
		}
		
		$offset = ' OFFSET ' . (($page - 1) * $settings["per_page"]);
		
		$select = 'SELECT '.$columns.' FROM ' . $table_name . $order . $per_page . $offset;
		$select_prep = $this->connection($db_name)->prepare($select);
		$result = $select_prep->execute();
		
		if($result){
			while( $row = $select_prep->fetch(PDO::FETCH_ASSOC) ){
				$data[$x++] = $row;
			}
		}
		return $data;
	}
	
	public function paginateLinks( $db_name = "", $table_name = "", $columns = array(), $settings = array() ){
		
		$output = "";
		
		$columns = implode(',', $columns);
		
		$total_records = $this->recordCount($db_name, 'SELECT COUNT(*) FROM ' . $table_name);
		
		$last_page = ceil($total_records/$settings["per_page"]);
		
		if(isset($_REQUEST['page'])){
			$page = preg_replace('#[^0-9]#i','',$_REQUEST['page']);
			if($_REQUEST['page'] < 1 || $_REQUEST['page'] == ""){
				$page = $last_page;
			}else if($_REQUEST['page'] > $last_page){
				$page = $last_page;
			}else{
				$page = $page;
			}
		}else{
			$page = 1;
		}
		
		$add1 = $page + 1;
		$sub1 = $page - 1;
		$lpm1 = $last_page - 1;
		
		$output .= '<div class="panel-footer nav"><ul class="pagination">'; 
		if($page > 1){
			$output .= '<li>
				<a href="'.$_SERVER['PHP_SELF'].'?page='.$sub1.'" class="prev waves-effect">
					<span aria-hidden="true"><i class="fa fa-chevron-left fa-lg"></i></span>
				</a>
			</li>';
		}else{
			$output .= '<li class="disabled">
				<a href="#">
					<span aria-hidden="true"><i class="fa fa-chevron-left fa-lg"></i></span>
				</a>
			</li>';
		}
		
		if($last_page <= 6){
			for($i=1;$i<=$last_page;$i++){
				if($page == $i){
					$output .= '<li class="active">
						<a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect">
							<span aria-hidden="true">'.$i.'</span>
						</a>
					</li>';
				}else{
					$output .= '<li>
						<a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect"" data-page="'.$i.'">
							<span aria-hidden="true">'.$i.'</span>
						</a>
					</li>';
				}
			}
		}else{
			if($page < 6){
				for($i = 1; $i <= 6; $i++){
					if($page == $i){
						$output .= '<li class="active"><a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect" data-page="'.$i.'">'.$i.'</a></li>';
					}else{
						$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect" data-page="'.$i.'">'.$i.'</a></li>';
					}
				}
				$output .= '<li class="disabled"><span class="dot">....</span></li>';
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page='.$lpm1.'" class="waves-effect" data-page="'.$lpm1.'">'.$lpm1.'</a></li>';
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page='.$last_page.'" class="waves-effect" data-page="'.$last_page.'">'.$last_page.'</a></li>';
			}elseif($last_page - 4 > $page && $page > 3){
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page=1" class="waves-effect" data-page="1">1</a></li>';
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page=2" class="waves-effect" data-page="2">2</a></li>';
				$output .= '<li class="disabled"><span class="dot">....</span></li>';
				for($i = $page - 2; $i <= $page + 2; $i++){
					if($page == $i){
						$output .= '<li class="active"><a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect" data-page="'.$i.'">'.$i.'</a></li>';
					}else{
						$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect""data-page="'.$i.'">'.$i.'</a></li>';
					}
				}
				$output .= '<li class="disabled"><span class="dot">....</span></li>';
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page='.$lpm1.'" class="waves-effect" data-page="'.$lpm1.'">'.$lpm1.'</a></li>';
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page='.$last_page.'" class="pagi-numbers" data-page="'.$last_page.'">'.$last_page.'</a></li>';
			}else{
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page=1" class="waves-effect" data-page="1">1</a></li>';
				$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page=2" class="waves-effect" data-page="2">2</a></li>';
				$output .= '<li class="disabled"><span class="dot">....</span></li>';
				for($i = $last_page - 5; $i <= $last_page; $i++){
					if($page == $i){
						$output .= '<li class="active"><a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect" data-page="'.$i.'">'.$i.'</a></li>';
					}else{
						$output .= '<li><a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'" class="waves-effect" data-page="'.$i.'">'.$i.'</a></li>';
					}
				}
			}
		}
		
		if($page < $last_page){
			$output .= '<li>
				<a href="'.$_SERVER['PHP_SELF'].'?page='.$add1.'" class="next waves-effect">
					<span aria-hidden="true"><i class="fa fa-chevron-right fa-lg"></i></span>
				</a>
			</li>';
		}else{
			$output .= '<li class="disabled">
				<a href="#">
					<span aria-hidden="true"><i class="fa fa-chevron-right fa-lg"></i></span>
				</a>
			</li>';
		}
		$output .= '</ul>';
			$output .= '<div style="float:right;"><input type="text"></div>';
		$output .= '</div>';
		
		return $output;
	}

}
?>