<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8"> <!-- required -->
	<title>Document</title>
</head>
<body>
	
<?php
function __autoload($classname){
	include '../classes/'.$classname.'_class.php';
}
$helper = new Helper();
$word = $helper->cutCharactersJA("足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます 足の速い茶色のキツネがぐうたら犬を跳び越えます", 0, 100);

echo $word;
?>
	
</body>
</html>
