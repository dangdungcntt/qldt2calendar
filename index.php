<?php
session_start();

if(!isset($_SESSION['access_token'])) {
	header('Location: /google-login.php');
	exit();	
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="user-scalable=yes, width=device-width, initial-scale=1.0, maximum-scale=1">
<title>QLDT to Calendar - NDDCoder</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">

<style>
    * {
		padding: 0;
		margin: 0;
	}
	textarea {
		width: 100%;
		height: 500px;
	}
</style>
</head>

<body>

<?php 
	$script = file_get_contents('client.js');
	$script = str_replace('{{$access_token}}', $_SESSION['access_token'], $script);
	$script = str_replace('{{$ajax_url}}', 'https://qldt2calendar.nddcoder.com/ajax.php', $script);
?>
<div class="container">
<div class="page-header">
  <h1>QLDT to Calendar <small>Dung Nguyen <a href="mailto:dangdungcntt@gmail.com">dangdungcntt@gmail.com</a></small></h1>
</div>
<ol style="padding-left: 15px">
	<li>Copy script dưới đây</li>
	<li>Mở QLDT > Đăng kí học > Sinh viên đăng kí học</li>
	<li>Mở Devtools (Ctrl + Shift + J), chọn tab Console</li>
	<li>Paste script và Enter</li>
</ol>
<textarea>
<?= $script ?>
</textarea>
</div>

<div style="text-align: center">
	
</div>

</body>
</html>
