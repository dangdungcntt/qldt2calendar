<?php
session_start();

if(isset($_SESSION['access_token'])) {
	header('Location: /');
	exit();	
}

require_once('google-calendar-api.php');
require_once('settings.php');

// Google passes a parameter 'code' in the Redirect Url
if(isset($_GET['code'])) {
	try {
		// Get the access token 
		$data = GoogleCalendarApi::GetAccessToken(CLIENT_ID, CLIENT_REDIRECT_URL, CLIENT_SECRET, $_GET['code']);
		
		// Save the access token as a session variable
		$_SESSION['access_token'] = $data['access_token'];

		// Redirect to the page where user can create event
		header('Location: index.php');
		exit();
	}
	catch(Exception $e) {
		echo $e->getMessage();
		exit();
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="user-scalable=yes, width=device-width, initial-scale=1.0, maximum-scale=1">

  <meta name="author" content="Dang Dung">
  <meta property="article:author" content="https://www.facebook.com/dangdung.json/"/>
  <meta name="description" content="Tạo event trên google calendar từ lịch học trên QLDT"/>
  <link rel="canonical" href="https://qldt2calendar.nddcoder.com"/>
  <title>QLDT to Calendar - NDDCoder</title>

  <meta property="og:locale" content="en_US"/>
  <meta property="og:type" content="website"/>
  <meta property="og:title" content="QLDT to Calendar"/>
  <meta property="og:description" content="Tạo event trên google calendar từ lịch học trên QLDT"/>
  <meta property="og:url" content="https://qldt2calendar.nddcoder.com"/>
  <meta property="og:site_name" content="qldt2calendar.nddcoder.com"/>
  <meta property="fb:app_id" content="173745476757677"/>
  <meta property="og:image" content="https://qldt2calendar.nddcoder.com/dist/share.png"/>
  <meta property="og:image:width" content="750"/>
  <meta property="og:image:height" content="375"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:description" content="Tạo event trên google calendar từ lịch học trên QLDT"/>
  <meta name="twitter:title" content="QLDT to Calendar"/>
  <meta name="twitter:image" content="https://qldt2calendar.nddcoder.com/dist/share.png"/>
<style type="text/css">

#logo {
	text-align: center;
	width: 200px;
    display: block;
    margin: 100px auto;
    border: 2px solid #2980b9;
    padding: 10px;
    background: none;
    color: #2980b9;
    cursor: pointer;
    text-decoration: none;
}

</style>
</head>

<body>

<?php

$login_url = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/calendar') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online';

?>

<a id="logo" href="<?= $login_url ?>">Login with Google</a>

</body>
</html>
