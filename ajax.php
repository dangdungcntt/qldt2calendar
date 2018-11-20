<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	echo 'POST';
	exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header("Status: 405 Method not allowed");
	header('Location: /');
	echo 'Method not allowed';
	exit();
}

header('Content-type: application/json');

$_POST = json_decode(file_get_contents('php://input'), true);

if (empty($_POST['access_token'])) {
	//header('Location: /google-login.php');
	header('Bad Request', true, 400);
	echo json_encode(array( 'error' => 1, 'message' => 'Missing access_token' ));
	die();
}

require_once('google-calendar-api.php');
require_once __DIR__ . '/' . 'helper.php';

try {
	// Get input data
	$data = $_POST['data'];
	$ajaxType = $_POST['ajax_type'] ?: 'lich_hoc';
	$capi = new GoogleCalendarApi($_POST['access_token']);
	$userTimezone = $capi->GetUserCalendarTimezone();
	$listEvents = [];

	foreach($data as $raw) {

		$events = $ajaxType == 'lich_thi' ? parseRawDataLichThi($raw) : parseRawData($raw);

		foreach ($events as $event) {
			$listEvents[] = GoogleCalendarApi::BuildEvent($event, $userTimezone);
		}
	}

	$calendarName = $_POST['calendar_name'];
	
	$calendarID = $capi->CreateCalendar($calendarName, $userTimezone);

	if (!$calendarID) {
		header('Bad Request', true, 400);
		echo json_encode(array( 'error' => 1, 'message' => 'Cannot create Calendar' ));
		exit();
	}
	
	// Create event on $calendarID calendar
	foreach ($listEvents as $event) {
		$capi->CreateCalendarEvent($calendarID, $event);
	}

	$publicURL = "https://calendar.google.com/calendar/embed?src={$calendarID}&ctz={$userTimezone}";

	// header('Location: ' . $publicURL);
	echo json_encode([
		'success' => true,
		'url' => $publicURL
	]);
	exit();
}
catch(Exception $e) {
	header('Bad Request', true, 400);
    echo json_encode(array( 'error' => 1, 'message' => $e->getMessage() ));
}

?>