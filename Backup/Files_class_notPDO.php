<?php

class Files extends Ccstool {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function forceDownload($request = ""){
		$file = $_GET['file'];
		if(is_file($file)) {	
			// required for IE
			if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}

			// get the file mime type using the file extension
			switch(strtolower(substr(strrchr($file, '.'), 1))) {
				case 'pdf': $mime = 'application/pdf'; break;
				case 'zip': $mime = 'application/zip'; break;
				case 'jpeg':
				case 'jpg': $mime = 'image/jpg'; break;
				default: $mime = 'application/force-download';
			}
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file)).' GMT');
			header('Cache-Control: private',false);
			header('Content-Type: '.$mime);
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.filesize($file));
			header('Connection: close');
			readfile($file);
			exit();
		}
	}
	
	public function digitalStorageConverter($value = 0){
		
		if($value <= 1023){
			//Byte
			$converted = number_format($value,2) . " B";
		}else if($value >= 1024 && $value <= 1048575){
			//kilobyte
			$value = ($value/1024);
			$converted = number_format($value,2,".","") . " KB";
		}else if($value >= 1048576 && $value <= 1073741823){
			//megabyte
			$value = (($value/1024) / 1024);
			$converted = number_format($value,2,".","") . " MB";
		}else if($value >= 1073741824 && $value <= 1099511627775){
			//gigabyte
			$value = ((($value/1024) / 1024) / 1024);
			$converted = number_format($value,2,".","") . " GB";
		}else if($value >= 1099511627776 && $value <= 1125899906842623){
			//terabyte
			$value = (((($value/1024) / 1024) / 1024) / 1024);
			$converted = number_format($value,2,".","") . " TB";
		}else if($value >= 1125899906842624 && $value <= 1152921504606846975){
			//petabyte
			$value = ((((($value/1024) / 1024) / 1024) / 1024) / 1024);
			$converted = number_format($value,2,".","") . " PB";
		}else if($value >= 1152921504606846976 && $value <= 1180591620717411303423){
			//exabyte
			$value = (((((($value/1024) / 1024) / 1024) / 1024) / 1024) / 1024);
			$converted = number_format($value,2,".","") . " EB";
		}else if($value >= 1180591620717411303424 && $value <= 1208925819614629174706175){
			//zettabyte
			$value = ((((((($value/1024) / 1024) / 1024) / 1024) / 1024) / 1024) / 1024);
			$converted = number_format($value,2,".","") . " ZB";
		}else if($value >= 1208925819614629174706176){
			//yottabyte
			$value = (((((((($value/1024) / 1024) / 1024) / 1024) / 1024) / 1024) / 1024) / 1024);
			$converted = number_format($value,2,".","") . " YB";
		}
		
		return $converted;
	}
	
	public function fileUploader( $files = array(), $options = array()  ){
		
		$where = array();
		$allowed_formats = array();
		$files_uploaded = array();
		$extra_fields = array();
		$extra_colunm = array();
		$extra_values =array();
		$ext_update = array();
		$ext_colunm = "";
		$ext_values = "";
		$separator = "";
		$has_error = array();
		$response = array();
		$response['error'] = false;
		$response['code'] = 200;
		$response['message'] = 'No error found.';
		
		if( count($options) <= 0  || !is_array($options) ){
			$response['error'] = true;
			$response['code'] = 503;
			$response['message'] = 'Second parameter must be an array or missing';
		}
		
		if( isset($options["allowed_formats"]) ){
			
			foreach($options["allowed_formats"] as $k => $format){
				
				$format = strtolower($format);
				
				if($format == "jpg"){
					$allowed_formats[] = "image/jpg";
				}
				if($format == "jpeg"){
					$allowed_formats[] = "image/jpeg";
				}
				if($format == "png"){
					$allowed_formats[] = "image/png";
				}
				if($format == "gif"){
					$allowed_formats[] = "image/gif";
				}
				if($format == "pdf"){
					$allowed_formats[] = "application/pdf";
				}
				if($format == "docx"){
					$allowed_formats[] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
				}
				if($format == "xlsx"){
					$allowed_formats[] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
				}
				if($format == "rar" || $format == "zip"){
					$allowed_formats[] = "application/octet-stream";
				}
				if($format == "txt"){
					$allowed_formats[] = "text/plain";
				}
				if($format == "mp4"){
					$allowed_formats[] = "video/mp4";
				}
				if($format == "mp3"){
					$allowed_formats[] = "audio/mp3";
				}
				
			}
			
		}
		
		foreach($files as $file){
			foreach( $file['name'] as $file_name => $fvalue){
				if($fvalue == ""){
					$response['error'] = true;
					$response['code'] = 503;
					$response['message'] = 'Please select a file.';
				}
			}
		}
		
		$error_count = 0;
		if($response['error'] == false && $response['code'] == 200){
			
			foreach($files as $file){
				
				foreach($file as $f){
					$count = count($f) - 1;
				}
				
				for($x = 0; $x <= $count; $x++){
					
					if( $file['error'][$x] < 1 ){
						if(in_array($file['type'][$x], $allowed_formats)){
							$has_error[] = "true";
						}else{
							$has_error[] = "false";
						}
					}
				}
			}
			
			if(in_array("false", $has_error)){
				$error_count += 1;
			}
			
			foreach($files as $file){
				
				foreach($file as $f){
					$count = count($f) - 1;
				}
				
				for($x = 0; $x <= $count; $x++){
					
					if( $file['error'][$x] < 1 ){
						
						if( $error_count <= 0 ){
							
							if($file['size'][$x] < $options['max_file_size']){
								
								$wext = explode(".",$file['name'][$x]);
								
								$ext = end($wext);
								
								if(!isset($options['new_name'])){
									$new_name = md5(uniqid(time(), true)) . "-" . $x . "." . $ext;
								}else{
									$new_name = $options['new_name'] . "-" . $x . "." .$ext;
								}
								$upload = move_uploaded_file($file['tmp_name'][$x],$options['path'].$new_name);
								if($upload){
									$response['message'] = 'File/s uploaded.';
									$files_uploaded[] = $options['path'].$new_name;
								}else{
									$response['error'] = true;
									$response['code'] = 503;
									$response['message'] = 'Error on uploading file/s';
								}
								
							}else{
								$response['error'] = true;
								$response['code'] = 503;
								$response['message'] = 'File size should be not greather than ' . $this->digitalStorageConverter($options['max_file_size']);
							}
							
						}else{
							$response['error'] = true;
							$response['code'] = 503;
							$response['message'] = 'One of the file is not supported.';
						}
						
					}else{
						$response['error'] = true;
						if($file['error'][$x] == 1){
							$response['code'] =  "Error ".$file['error'][$x].": The upleaded file exceeds server's file size, please contact your server administrator.";
						}else if($file['error'][$x] == 2){
							$response['code'] =  "Error ".$file['error'][$x].": The upleaded file exceeds the HTML form file size, please check the input tag with name = MAX_FILE_SIZE.";
						}else if($file['error'][$x] == 3){
							$response['code'] =  "Error ".$file['error'][$x].": The uploaded file was only partiolly uploaded, please upload the file again.";
						}else if($file['error'][$x] == 4){
							$response['code'] =  "Please select a file.";
						}else if($file['error'][$x] == 6){
							$response['code'] =  "Error ".$file['error'][$x].": Missing a temporary folder.";
						}else if($file['error'][$x] == 7){
							$response['code'] =  "Error ".$file['error'][$x].": Failed to write file to disk.";
						}else if($file['error'][$x] == 8){
							$response['code'] =  "Error ".$file['error'][$x].": A PHP extension stopped the file upload.";
						}
						$response['message'] = 'There is an error on the file uploaded.';
					}
					
				}
				
			}
			
			if($response['code'] == 200 && $response['error'] == false){
			
				if( isset($options['database']) ){
					
					if(isset($options['database']['extra_fields'])){
						
						$extra_datas = $options['database']['extra_fields'];
						foreach($extra_datas as $ed => $ved){
							$extra_colunm[] = $this->connection($options['database']['name'])->real_escape_string($ed);
							$extra_values[] = $this->connection($options['database']['name'])->real_escape_string($ved);
						}
						
						$ext_colunm = implode(",", $extra_colunm);
						$ext_values = "'" . implode("','", $extra_values) . "'";
						
						for( $ext=0; $ext <= (count($extra_colunm)-1); $ext++ ){
							$ext_update[] = $extra_colunm[$ext] ."='". $extra_values[$ext] . "'";
						}
						
						$separator = ",";
					}
								
					if(strtolower($options['database']['mode']) == "insert" || strtolower($options['database']['mode']) == "update"){
						
						if( strtolower($options['database']['mode']) == "insert" ){
							
							$insert = "INSERT INTO ".$options['database']['table']." (".$options['database']['column'].$separator.$ext_colunm.") VALUES ('".json_encode($files_uploaded)."'".$separator.$ext_values.")";
							
							$result = $this->connection($options['database']['name'])->query($insert);
							
							if($result){
								$response['error'] = false;
								$response['code'] = 200;
								$response['message'] = 'Files has been uploaded and saved to database.';
							}else{
								$response['error'] = true;
								$response['code'] = 500;
								$response['message'] = 'Fatal error on inserting permalink to database.';
							}
							
						}
						
						if( strtolower($options['database']['mode']) == "update" ){
							
							foreach($options['database']['settings']['where'] as $w){
								
								foreach($w as $col => $vcol){
									$where[] = $col ."='". $vcol . "'";
								}
								
								if( count($where) > 1 ){
									$where_cond = implode(" AND ", $where);
								}else{
									$where_cond = implode("", $where);
								}
								
								$select = "SELECT * FROM ".$options['database']['table']." WHERE " . $where_cond;
								$select_result = $this->connection($options['database']['name'])->query($select);
								$select_count = $select_result->num_rows;
								
								if($select_count > 0){
									while( $row = $select_result->fetch_assoc() ){
										
										$column = json_decode($row[$options['database']['column']]);
										
										foreach($column as $colmn){
											if(file_exists($colmn)){
												unlink($colmn);
											}
										}
										
										$update = "UPDATE ".$options['database']['table']." SET ".$options['database']['column']."='".json_encode($files_uploaded)."'".$separator.implode(",",$ext_update)." WHERE " . $where_cond;
										$update_result = $this->connection($options['database']['name'])->query($update);
										
										if($update_result){
											$response['error'] = false;
											$response['code'] = 200;
											$response['message'] = 'File has been updated.';
										}else{
											$response['error'] = true;
											$response['code'] = 500;
											$response['message'] = 'Fatal error on updating permalink to database.';
										}
									}	
								}
								unset($where);
								$where = array();
							}	
						}
					}else{
						$response['error'] = true;
						$response['code'] = 503;
						$response['message'] = 'files was uploaded but not inserted to database, mode must be [insert] or [update]';
					}
				}
			}	
		}
		return $response;
	}
	
}

?>