<?php 
	if(file_exists('../../../db2b4d7b9d1a383adae91da71085d8411a/helpkids.db')){
		$sqlite = new PDO('sqlite:../../../db2b4d7b9d1a383adae91da71085d8411a/helpkids.db');
	}else if(file_exists('../db2b4d7b9d1a383adae91da71085d8411a/helpkids.db')){
		$sqlite = new PDO('sqlite:../db2b4d7b9d1a383adae91da71085d8411a/helpkids.db');
	}else{
		$sqlite = "";
	}
?>