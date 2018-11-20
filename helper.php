<?php


function ParseTime($day, $period)
{
	$date = date('Y-m-d\T', strtotime($day));
    $data = [];
    
    if ($period < 1 || $period > 5) {
        $period = 1;
    }
	switch ($period) {
		case 1:
		$data['start_time'] = $date . '06:45:00';
        $data['end_time'] = $date . '09:25:00';
        break;
		case 2:
		$data['start_time'] = $date . '09:35:00';
        $data['end_time'] = $date . '12:15:00';
        break;
		case 3:
		$data['start_time'] = $date . '12:30:00';
        $data['end_time'] = $date . '15:10:00';
        break;
		case 4:
		$data['start_time'] = $date . '15:20:00';
        $data['end_time'] = $date . '18:00:00';
        break;
		case 5:
		$data['start_time'] = $date . '18:00:00';
        $data['end_time'] = $date . '21:00:00';
        break;
	}

	return $data;
}

function parseRawData($raw)
{
	$data = [];
	$listThoiGian = $raw['thoiGian'];
	$length = count($listThoiGian);
	
	for($i = 0; $i < $length; $i += 2) {
		$timeRanges = explode(' ', $listThoiGian[$i]);
		$timeDetails = explode(' ', $listThoiGian[$i + 1]);

		$from = $timeRanges[1];
		$to = $timeRanges[3];

		$monday = strtotime(str_replace('/', '-', $from));
        $until = date('Y-m-d', strtotime(str_replace('/', '-', $to)));

        $day = date('Y-m-d', $monday);
        $weekDay = $timeDetails[1];
        $periods = $timeDetails[3];
        $period = 5;
		if ($weekDay > 2) {
			if ($weekDay == 3) {
				$day = date('Y-m-d', strtotime('+1 day', $monday));
			} else {
				$day = date('Y-m-d', strtotime('+' . ($weekDay - 2) . ' days', $monday));
			}
        }

        switch ($periods) {
            case '1,2,3':
                $period = 1;
                break;
            case '4,5,6':
                $period = 2;
                break;
            case '7,8,9':
                $period = 3;
                break;
            case '10,11,12':
                $period = 4;
                break;
            default :
                $period = 5;
        }

		$time = ParseTime($day, $period);
		$data[] = [
            'summary' => trim($raw['lopHocPhan']),
            'location' => trim($raw['diaDiem']),
            'start_time' => $time['start_time'],
            'end_time' => $time['end_time'],
            'until' => $until
        ];
    }
    
    return $data;
}

function parseRawDataLichThi($raw)
{
    $date = date_create_from_format('d/m/Y H:i', $raw['ngayThi'] . ' ' . $raw['caThi']);
    $arr = explode(':', $raw['caThi']);
    $startTime = $raw['caThi'];
    $endTime = (intval($arr[0]) + 2) . ':' . $arr[1];

    $data[] = [
        'summary' => trim($raw['lopHocPhan']),
        'location' => trim($raw['diaDiem']),
        'start_time' => $date->format('Y-m-d\T') . $startTime . ':00',
        'end_time' => $date->format('Y-m-d\T') . $endTime . ':00',
        'until' => $date->format('Y-m-d')
    ];

    return $data;
}

function dd($var)
{
    if (is_array($var)) {
        echo json_encode($var);
    } else {
        echo $var;
    }

    die();
}