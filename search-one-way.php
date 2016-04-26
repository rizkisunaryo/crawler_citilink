<?php

$origin = isset($_GET['origin']) ? trim($_GET['origin']) : '';
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$date = isset($_GET['date']) ? trim($_GET['date']) : '';
$adult = isset($_GET['adult']) ? $_GET['adult'] : 0;
$child = isset($_GET['child']) ? $_GET['child'] : 0;
$infant = isset($_GET['infant']) ? $_GET['infant'] : 0;



header('Content-Type: application/json');
$resp = array();

if ($origin=='' || $destination=='' || $date=='') {
	$resp['message'] = 'incomplete request';
	echo json_encode($resp);
	die();
}

if ($origin==$destination) {
	$resp['message'] = 'origin and destination cannot be the same';
	echo json_encode($resp);
	die();
}

if ($adult==0 && $child==0) {
	$resp['message'] = 'adult and child cannot be 0';
	echo json_encode($resp);
	die();
}

if ($infant > $adult) {
	$resp['message'] = 'infant cannot be more than adult';
	echo json_encode($resp);
	die();
}

$dateParse = date_parse($date);
if ($dateParse['warning_count']>0 || $dateParse['error_count']>0) {
	$resp['message'] = 'date format is YYYY-MM-DD';
	echo json_encode($resp);
	die();
}



$inpUri = 
	"https://book.citilink.co.id/Search.aspx?"
	."Page=Select"
	."&RadioButtonMarketStructure=OneWay"
	."&TextBoxMarketOrigin1=".$origin
	."&TextBoxMarketDestination1=".$destination
	."&DropDownListMarketMonth1=".substr($date, 0, 7)
	."&DropDownListMarketDay1=".substr($date, 8)
	."&DropDownListMarketMonth2"
	."&DropDownListMarketDay2"
	."&DropDownListPassengerType_ADT=".$adult
	."&DropDownListPassengerType_INFANT=".$infant
	."&DropDownListCurrency=IDR"
	."&OrganizationCode=QG"
	."&DropDownListPassengerType_CHD=".$child
	."&culture=id-ID";
// echo $inpUri."<br />";
$headers = get_headers($inpUri);

$cookie = '';
foreach ($headers as $header) {
	if (strpos($header, 'Set-Cookie') !== false) {
		$headerM = str_replace("Set-Cookie: ", "", $header);
		$headerArr = explode(";", $headerM);
		$cookie .= $headerArr[0] . "; ";
	}
}



$flightArr = array();
$flightArr['cookie'] = $cookie;

$sessionAwalIndex = strpos($cookie, "ASP.NET_SessionId=");
$sessionAkhirIndex = strpos($cookie, ";");
$session = substr($cookie, $sessionAwalIndex, $sessionAkhirIndex - $sessionAwalIndex);
$session = str_replace("ASP.NET_SessionId=", "", $session);
$flightArr['session'] = $session;



$arrContextOptions=
	array(
        'http' => array(
            'header'  => "Cookie: ". $cookie . "\r\n",
            'method'  => 'GET',
        ),
    );
$response = file_get_contents("https://book.citilink.co.id/ScheduleSelect.aspx", false, stream_context_create($arrContextOptions));
// echo $cookie . '\n';
// var_dump($http_response_header);
// echo $response;

$responseArr = explode('fareCol2', $response);
foreach ($responseArr as $key => $value) {
	$timeDestArr = explode("<tr", $value);
	$timeDest = $timeDestArr[count($timeDestArr)-1];
	// echo $key . ':::::' . $timeDest;
	$timeDestDetailArr = explode('<td class="center">', $timeDest);
	foreach ($timeDestDetailArr as $key2 => $value2) {
		// echo $key . ':::::' . $key2 . ':::::' . $value2;
	}
	if ($key < count($responseArr) - 1) {
		$depArriveArrArr = explode("<BR><BR>", $timeDestDetailArr[1]);
		foreach ($depArriveArrArr as $key2 => $value2) {
			$depArriveArr = explode("<BR>", $value2);
			$flightArr['flights'][$key]['flight_details'][$key2]['depart'] = trim(str_replace("</td>", "", $depArriveArr[0]));
			$flightArr['flights'][$key]['flight_details'][$key2]['arrive'] = trim(str_replace("</td>", "", $depArriveArr[1]));
		}
		$oriDestArrArr = explode("<BR><BR>", $timeDestDetailArr[2]);
		foreach ($oriDestArrArr as $key2 => $value2) {
			$oriDestArr = explode("<BR>", $value2);
			$flightArr['flights'][$key]['flight_details'][$key2]['origin'] = trim(str_replace("</td>", "", $oriDestArr[0]));
			$flightArr['flights'][$key]['flight_details'][$key2]['destination'] = trim(str_replace("</td>", "", $oriDestArr[1]));
		}
		$flightCodeFull = substr($timeDestDetailArr[3], 0, strpos($timeDestDetailArr[3], '</td'));
		$flightCodeArr = explode("/", $flightCodeFull);
		foreach ($flightCodeArr as $key2 => $value2) {
			$flightArr['flights'][$key]['flight_details'][$key2]['flight_code'] = trim(preg_replace('/\s+/', "", $value2));
		}
	}
	if ($key > 0) {
		$valueStrPos = strpos($value, 'value="') + 7;
		$rpStrPos = strpos($value, '">Rp');
		$code = substr($value, $valueStrPos, ($rpStrPos-$valueStrPos));
		$flightArr['flights'][$key-1]['code'] = $code;

		$priceAwalStrPos = $rpStrPos + 2;
		$pStrPos = strpos($value, '</p>');
		$price = substr($value, $priceAwalStrPos, ($pStrPos-$priceAwalStrPos));
		$price = str_replace('Rp.', '', $price);
		$price = str_replace(',', '', $price);
		$price = $price + 0;
		$flightArr['flights'][$key-1]['price'] = $price;
	}
}
$rawJson = json_encode($flightArr);
$jsonStr = str_replace('\u00a0', ' ', $rawJson);
echo $jsonStr;
?>