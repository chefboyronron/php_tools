<?php

function __autoload($classname){
	include "../classes/".$classname."_class.php";
}
if(isset($_GET['file'])){
	$Files = new Files();
	$Files->forceDownload($_GET['file']);
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Force download</title>
</head>
<body>

	<a href="force_download_php.php?file=force_download/file.txt">Download text file.</a><br>
	<a href="force_download_php.php?file=force_download/file.pdf">Download pdf file.</a><br>
	<a href="force_download_php.php?file=force_download/file.docx">Download docx file.</a><br>
	<a href="force_download_php.php?file=force_download/file.rar">Download rar file.</a><br>
	
</body>
</html>