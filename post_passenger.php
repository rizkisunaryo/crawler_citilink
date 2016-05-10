<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 5/6/2016
 * Time: 5:07 PM
 */
include_once("functions.php");

$req = json_decode(file_get_contents('php://input'));
$resp = array();

if(!isset($req->session) || isEmpty($req->session)) {
    $resp['message'] = "incomplete request";
    echo json_encode($resp);
    die();
}
$resp['session'] = $req->session;

$reqAddressPerson=null;
if (isset($req->address_person)) {
    $reqAddressPerson = $req->address_person;
} else {
    $resp['message'] = "incomplete request";
    echo json_encode($resp);
    die();
}

if (isEmpty($reqAddressPerson->title) || isEmpty($reqAddressPerson->first_name) || isEmpty($reqAddressPerson->last_name) || isEmpty($reqAddressPerson->address) || isEmpty($reqAddressPerson->city) || isEmpty($reqAddressPerson->phone) || isEmpty($reqAddressPerson->email_address)
    || !isset($reqAddressPerson->title) || !isset($reqAddressPerson->first_name) || !isset($reqAddressPerson->last_name) || !isset($reqAddressPerson->address) || !isset($reqAddressPerson->city) || !isset($reqAddressPerson->phone) || !isset($reqAddressPerson->email_address)) {
    $resp['message'] = "incomplete request";
    echo json_encode($resp);
    die();
}

if ( ( !isset($req->adult_child_passengers) || count($req->adult_child_passengers)<1 )
    && ( !isset($req->infant_passengers) || count($req->infant_passengers)<1) ) {
    $resp['message'] = "incomplete request";
    echo json_encode($resp);
    die();
}



$inpBody = '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUBMGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFR0NPTlRST0xHUk9VUFBBU1NFTkdFUiRQYXNzZW5nZXJJbnB1dFZpZXdQYXNzZW5nZXJWaWV3JENoZWNrQm94SW5zdXJhbmNlBUFDT05UUk9MR1JPVVBQQVNTRU5HRVIkUGFzc2VuZ2VySW5wdXRWaWV3UGFzc2VuZ2VyVmlldyRDaGVja0JveFBNSXZkWh6Cdtm1mad5oP%2B7VGz2nQKe&pageToken=&';

$inp = array();

$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$DropDownListTitle'] = $reqAddressPerson->title;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxFirstName'] = $reqAddressPerson->first_name;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxLastName'] = $reqAddressPerson->last_name;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxAddressLine1'] = $reqAddressPerson->address;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxCity'] = $reqAddressPerson->city;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxWorkPhone'] = $reqAddressPerson->phone;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxOtherPhone'] = $reqAddressPerson->phone;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxHomePhone'] = $reqAddressPerson->phone;
$inp['CONTROLGROUPPASSENGER$ContactInputPassengerView$TextBoxEmailAddress'] = $reqAddressPerson->email_address;

foreach($req->adult_child_passengers as $k=>$v) {
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListTitle_'.$k] = $v->title;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$TextBoxFirstName_'.$k] = $v->first_name;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$TextBoxLastName_'.$k] = $v->last_name;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$TextBoxMiddleName_'.$k] = 'middle';
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListBirthDateDay_'.$k] = $v->birth_date_day;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListBirthDateMonth_'.$k] = $v->birth_date_month;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListBirthDateYear_'.$k] = $v->birth_date_year;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListDocumentDateDay0_'.$k] = 1;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListDocumentDateMonth0_'.$k] = 1;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListDocumentDateYear0_'.$k] = 1;
}

foreach($req->infant_passengers as $k=>$v) {
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListAssign_'.$k.'_'.$k] = $k;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$TextBoxFirstName_'.$k.'_'.$k] = $v->first_name;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$TextBoxLastName_'.$k.'_'.$k] = $v->last_name;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListGender_'.$k.'_'.$k] = $v->gender;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$TextBoxMiddleName_'.$k.'_'.$k] = 'middle';
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListBirthDateDay_'.$k.'_'.$k] = $v->birth_date_day;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListBirthDateMonth_'.$k.'_'.$k] = $v->birth_date_month;
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$DropDownListBirthDateYear_'.$k.'_'.$k] = $v->birth_date_year;
}

if (isset($req->pmi) && $req->pmi==1) {
    $inp['CONTROLGROUPPASSENGER$PassengerInputViewPassengerView$CheckBoxPMI'] = 'on';
}

$inpBody .= http_build_query($inp);
$inpBody .= '&CONTROLGROUPPASSENGER$ItineraryDistributionInputPassengerView$Distribution=2&CONTROLGROUPPASSENGER$ButtonSubmit=Lanjutkan';



$arrContextOptions=
    array(
        'http' => array(
            'header'  => "Cookie: ASP.NET_SessionId=".$req->session.";\r\n" .
                "Host: book.citilink.co.id\r\n" .
                "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Referer: https://book.citilink.co.id/Passenger.aspx\r\n",
            'method'  => 'POST',
            'content' => $inpBody,
        ),
    );
file_get_contents("https://book.citilink.co.id/Passenger.aspx", false, stream_context_create($arrContextOptions));

$arrContextOptions=
    array(
        'http' => array(
            'header'  => "Cookie: ASP.NET_SessionId=".$req->session.";\r\n" .
                "Host: book.citilink.co.id\r\n" .
                "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Referer: https://book.citilink.co.id/Passenger.aspx\r\n",
            'method'  => 'GET',
        ),
    );
$response = file_get_contents("https://book.citilink.co.id/Insurance.aspx", false, stream_context_create($arrContextOptions));

//$response = str_replace("images/", "https://book.citilink.co.id/images/", $response);
//$response = str_replace("css/", "https://book.citilink.co.id/css/", $response);
//$response = str_replace("js/", "https://book.citilink.co.id/js/", $response);
//echo $response;





$inpInsurance = array();

$key1BlockArr = explode('klik disini</a></p><br><input type="hidden" name="', $response);
if (count($key1BlockArr)<2) {
    $resp['message'] = "error";
    echo json_encode($resp);
    die();
}
$key1Block = $key1BlockArr[1];
$key1 = substr($key1Block, 0, strpos($key1Block, '"'));

$value1BlockArr = explode($key1 . '" value="', $response);
if (count($value1BlockArr)<2) {
    $resp['message'] = "error";
    echo json_encode($resp);
    die();
}
$value1Block = $value1BlockArr[1];
$value1 = substr($value1Block, 0, strpos($value1Block, '"'));

$inpInsurance[$key1] = $value1;



$key2BlockArr = explode('<select id="group_establishment_0" name="', $response);
if (count($key2BlockArr)<2) {
    $resp['message'] = "error";
    echo json_encode($resp);
    die();
}
$key2Block = $key2BlockArr[1];
$key2 = substr($key2Block, 0, strpos($key2Block, '"'));

$value2BlockArr = explode('"><option value="">Tidak ada cakupan</option>', $response);
if (count($value2BlockArr)<2) {
    $resp['message'] = "error";
    echo json_encode($resp);
    die();
}
$value2Block = $value2BlockArr[1];
$value2SearchAwal = '<option value="';
$value2IndexAwal = strpos($value2Block, $value2SearchAwal);
$value2IndexAkhir = strpos($value2Block, '" selected>');
$value2 = substr($value2Block, $value2IndexAwal, $value2IndexAkhir - $value2IndexAwal);
$value2 = str_replace($value2SearchAwal, "", $value2);

$inpInsurance[$key2] = $value2;



$key3 = str_replace("itemKey", "primaryParticipantNumber", $key2);
$value3 = 0;

$inpInsurance[$key3] = $value3;



$inpBodyInsurance = "__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUBMGRkBsrCYiDYbQKCOcoq%2FUTudEf14vk%3D&pageToken=&ControlGroupInsuranceView%24ButtonSubmit=Lanjutkan&";
$inpBodyInsurance .= http_build_query($inpInsurance);



$arrContextOptions=
    array(
        'http' => array(
            'header'  => "Cookie: ASP.NET_SessionId=".$req->session.";\r\n" .
                "Host: book.citilink.co.id\r\n" .
                "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Referer: https://book.citilink.co.id/Insurance.aspx\r\n",
            'method'  => 'POST',
            'content' => $inpBodyInsurance,
        ),
    );
file_get_contents("https://book.citilink.co.id/Insurance.aspx", false, stream_context_create($arrContextOptions));

$arrContextOptions=
    array(
        'http' => array(
            'header'  => "Cookie: ASP.NET_SessionId=".$req->session.";\r\n" .
                "Host: book.citilink.co.id\r\n" .
                "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Referer: https://book.citilink.co.id/Insurance.aspx\r\n",
            'method'  => 'GET',
        ),
    );
$response = file_get_contents("https://book.citilink.co.id/SeatMap.aspx", false, stream_context_create($arrContextOptions));

$response = str_replace("images/", "https://book.citilink.co.id/images/", $response);
$response = str_replace("css/", "https://book.citilink.co.id/css/", $response);
$response = str_replace("js/", "https://book.citilink.co.id/js/", $response);
echo $response;