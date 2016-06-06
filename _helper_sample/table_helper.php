<?php
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}
$helper = new Helper();
$table = $helper->table(array(
	"library" => "materialize",
	"table" => array(
		"responsive" => true,
		"class" => "table",
		"thead" => true,
		"theadLabel" => array("Id", "File name", "Thumbnail", "Date"),
	),
	"data" => array(
		// "database" => "test",
		"database" => "../test.db", //sqlite
		"table" => "files",
		"columns" => array("id", "filename", "thumbnail", "timestamp"),
		"json" => array("permalink","filename")
	),
	"file" => array(
		"path" => "_php_code_sample/uploads"
	),
	"options" => array(
		"per_page" => 1,
		"order" => "id ASC",
	),
));
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/snippets_ron/ccstools/libraries/fontawesome/css/font-awesome.min.css">
	<script type="text/javascript" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/snippets_ron/ccstools/libraries/jquery/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/snippets_ron/ccstools/libraries/jquery/jquery-migrate-1.2.1.min.js"></script>
	<?php echo $table['assets']; ?>
</head>
<body>
	<div class="container">
		
		<div class="row">
			
			<div class="">
			
				<?php echo $table['output']; ?>
			
			</div>
			
		</div>
		
		<div class="row">
		
			<div class="">
			
				<?php echo $table['links']; ?>
			
			</div>
		
		</div>
	
	</div>
	
</body>
</html>
