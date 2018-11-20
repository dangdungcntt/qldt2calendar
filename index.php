<?php
session_start();

if(!isset($_SESSION['access_token'])) {
	header('Location: /google-login.php');
	exit();	
}

require_once('settings.php');

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
		height: 300px;
	}
</style>
</head>

<body>

<div class="container">
<div class="page-header">
  <h1>QLDT to Calendar <small>Dung Nguyen <a href="mailto:dangdungcntt@gmail.com">dangdungcntt@gmail.com</a></small></h1>
</div>
<h2>Thêm lịch thi vào quản lý đào tạo <img src="/dist/new.png" alt="" width=100></h2>
<ol style="padding-left: 15px">
	<li><strong>Copy</strong> script dưới đây</li>
	<li><strong>Mở QLDT > Đăng ký thi > Tra cứu lịch thi cá nhân</strong></li>
	<li>Mở Devtools <strong>(Ctrl + Shift + J)</strong>, chọn tab <strong>Console</strong></li>
	<li><strong>Paste</strong> script và <strong>Enter</strong></li>
</ol>
<?php 
	$lichThi = file_get_contents('lichthi.js');
	$lichThi = str_replace('{{$access_token}}', $_SESSION['access_token'], $lichThi);
	$lichThi = str_replace('{{$ajax_url}}', AJAX_URL, $lichThi);
?>
<textarea>
<?= $lichThi ?>
</textarea>
<h2>Thêm lịch học vào quản lý đào tạo</h2>
<ol style="padding-left: 15px">
	<li>Copy script dưới đây</li>
	<li>Mở QLDT > Đăng kí học > Sinh viên đăng kí học</li>
	<li>Mở Devtools (Ctrl + Shift + J), chọn tab Console</li>
	<li>Paste script và Enter</li>
</ol>
<?php 
	$lichHoc = file_get_contents('client.js');
	$lichHoc = str_replace('{{$access_token}}', $_SESSION['access_token'], $lichHoc);
	$lichHoc = str_replace('{{$ajax_url}}', AJAX_URL, $lichHoc);
?>
<textarea>
<?= $lichHoc ?>
</textarea>
</div>

<div style="text-align: center">
	
</div>

</body>
</html>
