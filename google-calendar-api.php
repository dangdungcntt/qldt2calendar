<?php

class GoogleCalendarApi
{

	private $access_token;

	public function __construct($access_token)
	{
		$this->access_token = $access_token;
	}
	
	public static function GetAccessToken($client_id, $redirect_uri, $client_secret, $code) {	
		$url = 'https://accounts.google.com/o/oauth2/token';			
		
		$curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to receieve access token');
			
		return $data;
	}

	public static function BuildEvent($event, $timezone)
	{	
		return [
			'reminders' => [
				'useDefault' => true
			],
			'summary' => $event['summary'],
			'location' => $event['location'],
			'start' => [
				'dateTime' => $event['start_time'],
				'timeZone' => $timezone
			],
			'end' => [
				'dateTime' => $event['end_time'],
				'timeZone' => $timezone
			],
			'recurrence' => [
				'RRULE:FREQ=WEEKLY;UNTIL=' . date('Ymd\T', strtotime($event['until'])) . '235959Z' //20110701T170000
			]
		];
	}


	public function GetUserCalendarTimezone() {
		$url_settings = 'https://www.googleapis.com/calendar/v3/users/me/settings/timezone';
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_settings);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $this->access_token));	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	
		$data = json_decode(curl_exec($ch), true); //echo '<pre>';print_r($data);echo '</pre>';
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to get timezone');

		return $data['value'];
	}

	public function CreateCalendar($summary, $timezone)
	{
		$url_create_calendar = 'https://www.googleapis.com/calendar/v3/calendars';

		$createData = [
			'summary' => $summary,
			'timezone' => $timezone
		];

		$response = $this->curl_post($url_create_calendar, $createData);

		if (!empty($response['error'])) {
			return false;
		}

		$url_update_calendar = 'https://www.googleapis.com/calendar/v3/users/me/calendarList/' . $response['id'];

		$updateData = [
			'defaultReminders' => [
				[
					'method' => 'email',
					'minutes' => 120
				]
			],
			'notificationSettings' => [
				'notifications' => [
					[
						'method' => 'email',
          				'type' => 'eventChange'
					]
				]
			]
		];

		$response = $this->curl_post($url_update_calendar, $updateData, 'PUT');

		if (!empty($response['error'])) {
			return false;
		}

		return $response['id'];
	}

	public function CreateCalendarEvent($calendar_id, $event) {
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';

		$response = $this->curl_post($url_events, $event);

		if (!empty($response['error'])) {
			return false;
		}

		return true;
	}

	private function curl_post($url, $data, $method = 'POST')
	{
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $this->access_token, 'Content-Type: application/json'));	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) {
			if (!empty($data['error'])) {
				return [
					'error' => $data['error']['message']
				];
			}

			return [
					'error' => 'Error when send request'
			];
		}

		return $data;
	}
}

?>