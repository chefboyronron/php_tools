<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ccstools ver. 1.0.0</title>
	<script type="text/javascript" src="../libraries/jquery/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="../libraries/jquery/jquery-migrate-1.2.1.min.js"></script>
</head>
<body>

<button class="generate-uid">Generate UID</button>
<button class="generate-code">Generate Code</button>
<br><br>
<span class="code" style="border: 1px solid #f0aaaa; padding: 10px;">Genedated code and uid display's here.</span>

<script type="text/javascript" src="../js/generate.js"></script>
<script>
$(document).ready(function(){
	$('.generate-uid').click(function(){
		Generate.generateUID("code");
	})
	$('.generate-code').click(function(){
		Generate.generateCode("code");
	})
});
</script>
</body>
</html>