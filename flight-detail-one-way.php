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
                        "Content-Type: application/x-www-form-urlencoded\r\n" .
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
                        "Content-Type: application/x-www-form-urlencoded\r\n" .
                        "Referer: https://book.citilink.co.id/Passenger.aspx\r\n",
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
    $fareArr['flight_price']['adult_count'] = $adultCount;

    $adultPriceSearchAwal = "<div>";
    $adultPriceIndexAwal = strpos($adultArr[1], $adultPriceSearchAwal);
    $adultPriceIndexAkhir = strpos($adultArr[1], "</div>");
    $adultPriceStr = substr($adultArr[1], $adultPriceIndexAwal, $adultPriceIndexAkhir - $adultPriceIndexAwal);
    $adultPriceStr = str_replace($adultPriceSearchAwal, "", $adultPriceStr);
    $adultPriceStr = str_replace("Rp.", "", $adultPriceStr);
    $adultPriceStr = str_replace(",", "", $adultPriceStr);
    $adultPriceStr = trim($adultPriceStr);
    $adultPrice = $adultPriceStr + 0;
    $fareArr['flight_price']['adult_price'] = $adultPrice;
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
    $fareArr['flight_price']['child_count'] = $childCount;

    $childPriceSearchAwal = "<div>";
    $childPriceIndexAwal = strpos($childArr[1], $childPriceSearchAwal);
    $childPriceIndexAkhir = strpos($childArr[1], "</div>");
    $childPriceStr = substr($childArr[1], $childPriceIndexAwal, $childPriceIndexAkhir - $childPriceIndexAwal);
    $childPriceStr = str_replace($childPriceSearchAwal, "", $childPriceStr);
    $childPriceStr = str_replace("Rp.", "", $childPriceStr);
    $childPriceStr = str_replace(",", "", $childPriceStr);
    $childPriceStr = trim($childPriceStr);
    $childPrice = $childPriceStr + 0;
    $fareArr['flight_price']['child_price'] = $childPrice;
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
    $fareArr['flight_price']['infant_count'] = $infantCount;

    $infantPriceSearchAwal = "<div>";
    $infantPriceIndexAwal = strpos($infantArr[1], $infantPriceSearchAwal);
    $infantPriceIndexAkhir = strpos($infantArr[1], "</div>");
    $infantPriceStr = substr($infantArr[1], $infantPriceIndexAwal, $infantPriceIndexAkhir - $infantPriceIndexAwal);
    $infantPriceStr = str_replace($infantPriceSearchAwal, "", $infantPriceStr);
    $infantPriceStr = str_replace("Rp.", "", $infantPriceStr);
    $infantPriceStr = str_replace(",", "", $infantPriceStr);
    $infantPriceStr = trim($infantPriceStr);
    $infantPrice = $infantPriceStr + 0;
    $fareArr['flight_price']['infant_price'] = $infantPrice;
}



$pTaxIndexAwal = strpos($response, "<td><strong>Passenger Tax/Fees</strong></td>");
$pTaxIndexAkhir = strpos($response, "<td><br><strong>Infant Tax/Fees</strong></td>");
$pTaxBlock = substr($response, $pTaxIndexAwal, $pTaxIndexAkhir-$pTaxIndexAwal);

$pPscDelimiter = "Passenger Service Charge / PSC</span></td>";
$pPscArr = explode($pPscDelimiter, $pTaxBlock);
if (count($pPscArr)>1) {
    $pPscBlock = $pPscArr[1];
    $pPscSearchAwal = '<td><span class="floatRight clearRight">';
    $pPscIndexAwal = strpos($pPscBlock, $pPscSearchAwal);
    $pPscIndexAkhir = strpos($pPscBlock, "</span></td>");
    $pPscStr = substr($pPscBlock, $pPscIndexAwal, $pPscIndexAkhir-$pPscIndexAwal);
    $pPscStr = str_replace($pPscSearchAwal, "", $pPscStr);
    $pPscStr = str_replace("Rp.", "", $pPscStr);
    $pPscStr = str_replace(",", "", $pPscStr);
    $pPscStr = trim($pPscStr);
    $pPsc = $pPscStr + 0;
    $fareArr['passenger_tax']['passenger_service_charge'] = $pPsc;
}

$pIcDelimiter = "Insurance Surcharge</span></td>";
$pIcArr = explode($pIcDelimiter, $pTaxBlock);
if (count($pIcArr)>1) {
    $pIcBlock = $pIcArr[1];
    $pIcSearchAwal = '<td><span class="floatRight clearRight">';
    $pIcIndexAwal = strpos($pIcBlock, $pIcSearchAwal);
    $pIcIndexAkhir = strpos($pIcBlock, "</span></td>");
    $pIcStr = substr($pIcBlock, $pIcIndexAwal, $pIcIndexAkhir-$pIcIndexAwal);
    $pIcStr = str_replace($pIcSearchAwal, "", $pIcStr);
    $pIcStr = str_replace("Rp.", "", $pIcStr);
    $pIcStr = str_replace(",", "", $pIcStr);
    $pIcStr = trim($pIcStr);
    $pIc = $pIcStr + 0;
    $fareArr['passenger_tax']['insurance_surcharge'] = $pIc;
}

$pVatDelimiter = "Value Added Tax</span></td>";
$pVatArr = explode($pVatDelimiter, $pTaxBlock);
if (count($pVatArr)>1) {
    $pVatBlock = $pVatArr[1];
    $pVatSearchAwal = '<td><span class="floatRight clearRight">';
    $pVatIndexAwal = strpos($pVatBlock, $pVatSearchAwal);
    $pVatIndexAkhir = strpos($pVatBlock, "</span></td>");
    $pVatStr = substr($pVatBlock, $pVatIndexAwal, $pVatIndexAkhir-$pVatIndexAwal);
    $pVatStr = str_replace($pVatSearchAwal, "", $pVatStr);
    $pVatStr = str_replace("Rp.", "", $pVatStr);
    $pVatStr = str_replace(",", "", $pVatStr);
    $pVatStr = trim($pVatStr);
    $pVat = $pVatStr + 0;
    $fareArr['passenger_tax']['value_added_tax'] = $pVat;
}



$infantTaxDelimiter = "<td><br><strong>Infant Tax/Fees</strong></td>";
$infantTaxArr = explode($infantTaxDelimiter, $response);
if (count($infantTaxArr)>1) {
    $infantTaxBody = $infantTaxArr[1];
    $infantTaxIndexAkhir = strpos($infantTaxBody, "</tbody>");
    $infantTaxBlock = substr($infantTaxBody, 0, $infantTaxIndexAkhir);

    $iIcDelimiter = "Insurance Surcharge</span></td>";
    $iIcArr = explode($iIcDelimiter, $infantTaxBlock);
    if (count($iIcArr)>1) {
        $iIcBlock = $iIcArr[1];
        $iIcSearchAwal = '<td><span class="floatRight clearRight">';
        $iIcIndexAwal = strpos($iIcBlock, $iIcSearchAwal);
        $iIcIndexAkhir = strpos($iIcBlock, "</span></td>");
        $iIcStr = substr($iIcBlock, $iIcIndexAwal, $iIcIndexAkhir-$iIcIndexAwal);
        $iIcStr = str_replace($iIcSearchAwal, "", $iIcStr);
        $iIcStr = str_replace("Rp.", "", $iIcStr);
        $iIcStr = str_replace(",", "", $iIcStr);
        $iIcStr = trim($iIcStr);
        $iIc = $iIcStr + 0;
        $fareArr['infant_tax']['insurance_surcharge'] = $iIc;
    }

    $iVatDelimiter = "Value Added Tax</span></td>";
    $iVatArr = explode($iVatDelimiter, $infantTaxBlock);
    if (count($iVatArr)>1) {
        $iVatBlock = $iVatArr[1];
        $iVatSearchAwal = '<td><span class="floatRight clearRight">';
        $iVatIndexAwal = strpos($iVatBlock, $iVatSearchAwal);
        $iVatIndexAkhir = strpos($iVatBlock, "</span></td>");
        $iVatStr = substr($iVatBlock, $iVatIndexAwal, $iVatIndexAkhir-$iVatIndexAwal);
        $iVatStr = str_replace($iVatSearchAwal, "", $iVatStr);
        $iVatStr = str_replace("Rp.", "", $iVatStr);
        $iVatStr = str_replace(",", "", $iVatStr);
        $iVatStr = trim($iVatStr);
        $iVat = $iVatStr + 0;
        $fareArr['infant_tax']['value_added_tax'] = $iVat;
    }
}



$totalHargaBlockSearchAwal = '<td><strong>Total harga</strong></td>';
$totalHargaBlockIndexAwal = strpos($response, $totalHargaBlockSearchAwal);
$totalHargaBlockIndexAkhir = strpos($response, '<p>Semua jumlah yang ditampilkan adalah dalam IDR.</p>');
$totalHargaBlock = substr($response, $totalHargaBlockIndexAwal, $totalHargaBlockIndexAkhir-$totalHargaBlockIndexAwal);
$totalHargaBlock = str_replace($totalHargaBlockSearchAwal, "", $totalHargaBlock);

$totalHargaSearchAwal = '<td class="right">';
$totalHargaIndexAwal = strpos($totalHargaBlock, $totalHargaSearchAwal);
$totalHargaIndexAkhir = strpos($totalHargaBlock, '</td>');
$totalHargaStr = substr($totalHargaBlock, $totalHargaIndexAwal, $totalHargaIndexAkhir-$totalHargaIndexAwal);
$totalHargaStr = str_replace($totalHargaSearchAwal, "", $totalHargaStr);
$totalHargaStr = str_replace("Rp.", "", $totalHargaStr);
$totalHargaStr = str_replace(",", "", $totalHargaStr);
$totalHargaStr = trim($totalHargaStr);
$totalHarga = $totalHargaStr + 0;
$fareArr['total_harga'] = $totalHarga;



$rawJson = json_encode($fareArr);
$jsonStr = str_replace('\u00a0', ' ', $rawJson);
header('Content-Type: application/json');
echo $jsonStr;
?>