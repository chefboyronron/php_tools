<?php
/*
 * max_file_size = int, numeric value of file in KB
 * allowed_formats = array() extensions of files, not all formats supported. ["jpg", "jpeg", "png", "gif", "pdf", "docx", "xlsx", "txt", "rar", "zip", "mp4", "mp3"]
 * database = use to store records to database, set this parameter if want to use database
 * name = if using sqlite make sure the value of database=>name us equal to [database path] else [database name]
 * mode = [insert] or [update]
 * table = table name where you want to insert the record
 * extra_fields = array() extra inserted records
 * permalink = string[column name], name of colunm where you want to store the permanent link of the file [ex record: http://domain.com/directory/file.txt]
 * fileneme = string[column name], name of the colunm where you want to store file's name
 * settings = [multidimentional associative array] array(
 *		where = array(
 *			array("id"=>[value])
 *		)
 * )
*/
if(isset($_FILES['file'])){
	
	function __autoload($classname){
		include '../classes/'.$classname.'_class.php';
	}
	
	$generate = new Generate();
	$file = new Files();
	
	$last_inserted_id = 1;
	$date = date("Y-m-d H:i:s");
	
	$upload = $file->fileUploader($_FILES, array(
		"max_file_size" => ((1024 * 2) * 1024), // 2mb
		"allowed_formats" => array("jpg", "jpeg", "png", "gif", "PDF", "docx", "xlsx", "txt", "rar", "zip", "mp4", "mp3"),
		"path" =>"uploads/",
		"new_name" => $generate->generateUID(),
		"database" => array(
			// "mode" => "insert",
			"mode" => "update",
			// "name" => "test",
			"name" => "../test.db",
			"table" => "files",
			"extra_fields" => array(
				"timestamp"=>$date,
				"thumbnail"=>"thumbnail"
			),
			"permalink" => "permalink",
			"filename" => "filename",
			"settings" => array(
				"where" => array( 
					array("id"=> 1)
					// array("id"=> 24)
				)
			)
		)
	) );
	var_dump($upload);
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Uploader</title>
</head>
<body>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file[]" multiple="multiple">
		<input type="submit" name="send" value="Send">
	</form>

</body>
</html>