<?php
	$meta_title ="お問い合わせ｜";	
	require_once '../ini/pref_array.php';

// メール設定
$mail_company_name = "ストリートチルドレン復学プラミング";
$to_mail = "data@circus.ac";
$noreply = "noreply@mail.ne.jp";
$mail_header    = "From: =?UTF-8?B?" . base64_encode($mail_company_name) . "?=<" .$noreply. ">\n";


// 変数に格納

if(isset($_REQUEST["mode"])){
	$mode = $_REQUEST["mode"];
}else{
	$mode = "";
}
if(isset($_POST["check01"])){
	$check01 = $_POST["check01"];
}
if(isset($_POST["check02"])){
	$check02 = $_POST["check02"];
}
if(isset($_POST["i07"])){
	$i07 = $_POST["i07"];
}
if(isset($_POST["i08"])){
	$i07 = $_POST["i08"];
}
if(isset($_POST["i09"])){
	$i07 = $_POST["i09"];
}
if(isset($_POST["i10"])){
	$i07 = $_POST["i10"];
}
if(isset($_POST["i11"])){
	$i07 = $_POST["i11"];
}
if(isset($_POST["i12"])){
	$i07 = $_POST["i12"];
}
if(isset($_POST["i13"])){
	$i07 = $_POST["i13"];
}
if(isset($_POST["i14"])){
	$i07 = $_POST["i14"];
}
if(isset($_POST["i15"])){
	$i07 = $_POST["i15"];
}
if(isset($_POST["i16"])){
	$i07 = $_POST["i16"];
}

// お問い合わせ内容
$check_list01[1] = "資料請求（無料）";
$check_list01[2] = "当事業について";
$check_list02[3] = "協賛について";
$check_list02[4] = "その他";



if ($mode == "ok") {

	$dsp_check01 = "";
	if ($check01) {
		foreach ($check01 as $key => $val) {
			if ($dsp_check01) {
				$dsp_check01 .= "、";
			}
			$dsp_check01 .= $check_list01[$val];
		}
	}

	$dsp_check02 = "";
	if ($check02) {
		foreach ($check02 as $key => $val) {
			if ($dsp_check02) {
				$dsp_check02 .= "、";
			}
			$dsp_check02 .= $check_list02[$val];
		}
	}


	$subject = "【{$mail_company_name}】お問い合わせを受け付けました。";
    $mail_body = "<<< EOF
		▼お問い合せ内容

		お問い合わせ内容：{$dsp_check01}{$dsp_check02}

		ご質問・ご相談：{$i07}

		会社名：{$i08}

		お名前：{$i09}

		フリガナ：{$i10}

		メールアドレス：{$i11}

		お電話番号：{$i12}

		住所：〒{$i13} {$i14} {$i15}{$i16}

		EOF";

	$mail_body2 = "<<< EOF
		{$i09} 様
			
		この度は【{$mail_company_name}】にお問い合わせいただきありがとうございます。
		内容を確認後、担当者よりご連絡させていただきます。

		{$mail_body}
		EOF";

	
	@mb_language("ja");
    @mb_internal_encoding("utf-8");
    @mb_send_mail($to_mail, $subject, $mail_body, $mail_header);
	@mb_send_mail($i11, $subject, $mail_body2, $mail_header);

    header("location: index.php?mode=thank");
    exit;

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $Path = "../"; require_once '../inc/head.php'; ?>
<link rel="stylesheet" type="text/css" href="../css/contact.css" />
<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" />
<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
<script src="../js/jquery-1.11.3.min.js" type="text/javascript"></script>
<script src="../js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
<script src="../js/contact.js" type="text/javascript"></script>
<script src="../js/validator.js" type="text/javascript"></script>
<script src="../js/register.js" type="text/javascript"></script>
<script>
 $(document).ready(function () {
 
	$('.datepicker').datepicker({
		endDate: 'd'
	})
	
	$("#txtBirthdate").keydown(function(event){
	if ( event.keyCode != 46 ) {
    		// let it happen, don't do anything
			// Enter Key
			event.preventDefault();	
    	}
    
	});
	
	$("#txtcontactnum").keydown(function(event){
	if ( event.keyCode == 46 || event.keyCode == 8 || (event.keyCode >= 96 && event.keyCode <= 105)) {
    		// let it happen, don't do anything
    	}
		
    else {
    		// Ensure that it is a number and stop the keypress
    		if (event.keyCode < 48 || event.keyCode > 57 ) {
    			event.preventDefault();	
    		}	
    	}
	});
 
 });
</script>
</head>

<body>
<div id="wrapper" class="contact-contents">
	<?php $Path = "../"; require_once '../inc/header.php'; ?>    
    <div id="contents" class="w990 clear">
    	<div id="main" class="leftBox">
        	<h2 class="b_title"><span class="t_blue">登録</span></h2>
		
                        
                <!-- <form id="form1" class="contact_mail" method="post" enctype="multipart/form-data" name="form1" action="../set/form/contact_mail.php"> -->
                <form id="form1" action="#" method="post" enctype="multipart/form-data" name="form1" onsubmit="return Validator.Validate(this,1)">
                
     <div class="container">
    <div class="row">
        <form role="form">
            <div class="col-lg-6">
                <div class="well well-sm"><strong><span class="glyphicon glyphicon-asterisk"></span>Required Field</strong></div>
                <div class="form-group">
                    <label for="InputFirstName">First Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="txtfname" id="txtfname" placeholder="Enter First Name" required>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                    </div>
                </div>
				  <div class="form-group">
                    <label for="InputLastName">Last Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="txtlname" id="txtlname" placeholder="Enter Last Name" required>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="InputEmail">Email Address</label>
                    <div class="input-group">
                        <input type="email" class="form-control" id="txtEmail" name="txtEmail" placeholder="Enter Email" required>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="InputBirthDate">Birth Date</label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker"  data-date-format="mm/dd/yyyy" id="txtBirthdate" name="txtBirthdate" placeholder="Enter Birthdate" required>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                    </div>
                </div>
				<div class="form-group">
                    <label for="InputPassword">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="txtpassword" name="txtpassword" placeholder="Enter Password" required>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                    </div>
                </div>
				<div class="form-group">
                    <label for="InputConfirmPassword">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="txtconfirmpassword" name="txtconfirmpassword" placeholder="Enter Password" required>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                    </div>
                </div>	
				<div class="form-group">
                   <label for="InputGender">Gender</label>
				    <div class="input-group">
						<select class="form-control" id="selectgender">
							<option value="">--select one--</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
						<span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span>
					</div>		
                </div>
				<div class="form-group">
                    <label for="InputContactNumber">Contact Number</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="txtcontactnum" name="txtcontactnum" placeholder="Enter Contact Number" required>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                    </div>
                </div>
				
                <input type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary pull-left">
            </div>
        </form>
	    <br>
		<br>
		<!--<div class="col-lg-5 col-md-push-1">
            <div class="col-md-12">
                <div class="alert alert-success">
                    <strong><span class="glyphicon glyphicon-ok"></span> Success! Message sent.</strong>
                </div>
                <div class="alert alert-danger">
                    <span class="glyphicon glyphicon-remove"></span><strong> Error! Please check all page inputs.</strong>
                </div>
            </div> 
        </div> -->
    </div>
</div>
                </form>
                <!-- <script>
                    $("#form1").validate();
                </script> -->
              
        
        </div><!--main-->
        
        <div id="sub" class="rightBox">
        	<?php $Path = "../"; require_once '../inc/sub.php'; ?>
        </div>
        
    </div>
    <?php $Path = "../"; require_once '../inc/footer.php'; ?>
</div>
</body>
</html>