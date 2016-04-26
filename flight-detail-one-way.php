<?php
$session = isset($_GET['session']) ? trim($_GET['session']) : '';
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if ($session=='' || $code=='') {
    echo "kosong";
    die();
}

$code = str_replace("~", "%7E", $code);
$code = str_replace("^", "%5E", $code);
$code = str_replace("|", "%7C", $code);
$code = str_replace("\/", "%2F", $code);
$code = str_replace("/", "%2F", $code);
$code = str_replace(":", "%3A", $code);
$code = str_replace(" ", "+", $code);

$arrContextOptions=
	array(
        'http' => array(
            'header'  => "Cookie: ASP.NET_SessionId=".$session.";\r\n" . 
            			"Host: book.citilink.co.id\r\n" . 
            			"Referer: https://book.citilink.co.id/ScheduleSelect.aspx\r\n",
            'method'  => 'POST',
            'content' => '&__VIEWSTATE=%2FwEPDwUBMGRkBsrCYiDYbQKCOcoq%2FUTudEf14vk%3D&AvailabilitySearchInputScheduleSelectView%24DdlCurrencyDynamic=IDR&ControlGroupScheduleSelectView%24AvailabilityInputScheduleSelectView%24market1='.$code.'&ControlGroupScheduleSelectView%24ButtonSubmit=Lanjutkan',
        ),
    );
$response = file_get_contents("https://book.citilink.co.id/ScheduleSelect.aspx", false, stream_context_create($arrContextOptions));



$arrContextOptions=
	array(
        'http' => array(
            'header'  => "Cookie: ASP.NET_SessionId=".$session.";\r\n" . 
            			"Host: book.citilink.co.id\r\n" . 
            			"https://book.citilink.co.id/Passenger.aspx\r\n",
            'method'  => 'GET',
        ),
    );
$response = file_get_contents("https://book.citilink.co.id/Passenger.aspx", false, stream_context_create($arrContextOptions));

// $response = str_replace("images/", "https://book.citilink.co.id/images/", $response);
// $response = str_replace("css/", "https://book.citilink.co.id/css/", $response);
// $response = str_replace("js/", "https://book.citilink.co.id/js/", $response);
// echo $response;

$fareArr = array();



$titleSearchAwal = "<div>Penerbangan berangkat</div>";
$titleIndexAwal = strpos($response, $titleSearchAwal);
$titleIndexAkhir = strpos($response, "</caption>");
$title = substr($response, $titleIndexAwal, $titleIndexAkhir-$titleIndexAwal);
$title = str_replace($titleSearchAwal, "", $title);
$title = trim(preg_replace('/\s+/', " ", $title));
$fareArr['title'] = $title;



$priceBlockIndexAwal = strpos($response, "</caption>");
$priceBlockIndexAkhir = strpos($response, '<td colspan="3">');
$priceBlock = substr($response, $priceBlockIndexAwal, $priceBlockIndexAkhir - $priceBlockIndexAwal);

$adultDelimiter = "Adult</td>";
$adultArr = explode($adultDelimiter,$priceBlock);
if (count($adultArr)>1) {
    $adultCountBlock = $adultArr[0];
    $adultCountBlockArr = explode("<td>", $adultCountBlock);
    $adultCountStr = $adultCountBlockArr[count($adultCountBlockArr)-1];
    $adultCountStr = str_replace("<td>", "", $adultCountStr);
    $adultCountStr = str_replace(" ", "", $adultCountStr);
    $adultCount = $adultCountStr + 0;
    $fareArr['adult_count'] = $adultCount;

    $adultPriceSearchAwal = "<div>";
    $adultPriceIndexAwal = strpos($adultArr[1], $adultPriceSearchAwal);
    $adultPriceIndexAkhir = strpos($adultArr[1], "</div>");
    $adultPriceStr = substr($adultArr[1], $adultPriceIndexAwal, $adultPriceIndexAkhir - $adultPriceIndexAwal);
    $adultPriceStr = str_replace($adultPriceSearchAwal, "", $adultPriceStr);
    $adultPriceStr = str_replace("Rp.", "", $adultPriceStr);
    $adultPriceStr = str_replace(",", "", $adultPriceStr);
    $adultPriceStr = trim($adultPriceStr);
    $adultPrice = $adultPriceStr + 0;
    $fareArr['adult_price'] = $adultPrice;
}

$childDelimiter = "Child</td>";
$childArr = explode($childDelimiter,$priceBlock);
if (count($childArr)>1) {
    $childCountBlock = $childArr[0];
    $childCountBlockArr = explode("<td>", $childCountBlock);
    $childCountStr = $childCountBlockArr[count($childCountBlockArr)-1];
    $childCountStr = str_replace("<td>", "", $childCountStr);
    $childCountStr = str_replace(" ", "", $childCountStr);
    $childCount = $childCountStr + 0;
    $fareArr['child_count'] = $childCount;

    $childPriceSearchAwal = "<div>";
    $childPriceIndexAwal = strpos($childArr[1], $childPriceSearchAwal);
    $childPriceIndexAkhir = strpos($childArr[1], "</div>");
    $childPriceStr = substr($childArr[1], $childPriceIndexAwal, $childPriceIndexAkhir - $childPriceIndexAwal);
    $childPriceStr = str_replace($childPriceSearchAwal, "", $childPriceStr);
    $childPriceStr = str_replace("Rp.", "", $childPriceStr);
    $childPriceStr = str_replace(",", "", $childPriceStr);
    $childPriceStr = trim($childPriceStr);
    $childPrice = $childPriceStr + 0;
    $fareArr['child_price'] = $childPrice;
}

$infantDelimiter = "Infant</td>";
$infantArr = explode($infantDelimiter,$priceBlock);
if (count($infantArr)>1) {
    $infantCountBlock = $infantArr[0];
    $infantCountBlockArr = explode("<td>", $infantCountBlock);
    $infantCountStr = $infantCountBlockArr[count($infantCountBlockArr)-1];
    $infantCountStr = str_replace("<td>", "", $infantCountStr);
    $infantCountStr = str_replace(" ", "", $infantCountStr);
    $infantCount = $infantCountStr + 0;
    $fareArr['infant_count'] = $infantCount;

    $infantPriceSearchAwal = "<div>";
    $infantPriceIndexAwal = strpos($infantArr[1], $infantPriceSearchAwal);
    $infantPriceIndexAkhir = strpos($infantArr[1], "</div>");
    $infantPriceStr = substr($infantArr[1], $infantPriceIndexAwal, $infantPriceIndexAkhir - $infantPriceIndexAwal);
    $infantPriceStr = str_replace($infantPriceSearchAwal, "", $infantPriceStr);
    $infantPriceStr = str_replace("Rp.", "", $infantPriceStr);
    $infantPriceStr = str_replace(",", "", $infantPriceStr);
    $infantPriceStr = trim($infantPriceStr);
    $infantPrice = $infantPriceStr + 0;
    $fareArr['infant_price'] = $infantPrice;
}



$rawJson = json_encode($fareArr);
$jsonStr = str_replace('\u00a0', ' ', $rawJson);
header('Content-Type: application/json');
echo $jsonStr;
?>