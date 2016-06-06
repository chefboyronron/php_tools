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
	}
	
	public function connection($db) {
		$connection = new mysqli($this->host, $this->user, $this->password, $db);
		return $connection;
		
		// try {
			// $connection = new PDO('mysql:host=localhost;dbname='.$db.'', $this->user, $this->password);
		// } catch (PDOException $e) {
			// print "Error!: " . $e->getMessage() . "<br/>";
			// die();
		// }
		
		// return $connection;
		
	}
	
	public function closeConnection(){
		$this->connection()->close();
	}
	
	public function getDataType($db_name,$table_name,$column_name){
		$result = $this->connection($db_name)->query('SELECT '.$column_name.' FROM '. $table_name);
		$column = $result->fetch_field();
		return $column->type;
	}
	
	public function recordCount($db_name, $sql){
		$result = $this->connection($db_name)->query($sql);
		$count = $result->num_rows;
		return $count;
	}
	
	public function fetchRecords($db_name, $sql){
		$results = $this->connection($db_name)->query($sql);
		return $results;
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
		
		$total_records = $this->recordCount($db_name, 'SELECT ' . $columns . ' FROM ' . $table_name);
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
		
		$result = $this->connection($db_name)->query('SELECT '.$columns.' FROM ' . $table_name . $order . $per_page . $offset);
		
		
		if($result){
			while($row = $result->fetch_assoc()){
				$data[$x++] = $row;
			}
		}
		return $data;
	}
	
	public function paginateLinks( $db_name = "", $table_name = "", $columns = array(), $settings = array() ){
		
		$output = "";
		
		$columns = implode(',', $columns);
		
		$total_records = $this->recordCount($db_name, 'SELECT ' . $columns . ' FROM ' . $table_name);
		
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
						<a href="#" class="waves-effect">
							<span aria-hidden="true">'.$i.'</span>
						</a>
					</li>';
				}else{
					$output .= '<li>
						<a href="#" class="waves-effect"" data-page="'.$i.'">
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
					<span aria-hidden="true"><i class="fa fa-chevron-right fa-lg"></i>;</span>
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