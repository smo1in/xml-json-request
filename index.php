<?php


$data = [
    "firstName" => "Vasya",
    "lastName" => "Pupkin",
    "dateOfBirth" => "1984-07-31",
    "Salary"  => "1000",
    "creditScore" => "good"
];

$creditScore = $data["creditScore"];

$score = ($creditScore == "good") ? 700 : 300;

//31556926 - количество секунд в году.
$age = floor((time() - strtotime($data["dateOfBirth"])) / 31556926);


$xml = <<<XML
<?xml version='1.0' ?>
<userInfo version="1.6">
    <firstName>{$data["firstName"]}</firstName>
    <lastName>{$data["lastName"]}</lastName>
    <salary>{$data["salary"]}</salary>
    <age>$age</age>
    <creditScore>$score</creditScore>
</userInfo>
XML;

$dataJson = json_encode($data);

// отправляем запрос - $responce сохраняет ответ
$responseXml = http_post_curl('text/xml', $xml);
$responceJson = http_post_curl('application/json', $dataJson);


function http_post_curl($content_type, $post_data)
{
    $url = 'http://site.ua/xml.php';
    // init curl
    $curl = curl_init($url);
    // curl options
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //теперь curl вернет нам ответ, а не выведет
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
    curl_setopt($curl, CURLOPT_POST, TRUE); //передача данных методом POST

    $http_headers = array(
        'Content-type: ' . $content_type
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $http_headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data); //тут переменные которые будут переданы методом POST

    $response = curl_exec($curl);
    if ($response === FALSE) {
        return FALSE;
    }

    return $response;
}

$answerXml = simplexml_load_file($responseXml);

if ($answerXml->returnCodeDescription == SUCCESS) {
    echo "Sold";
} elseif ($answerXml->returnCodeDescription == REJECT) {
    echo "Reject";
} else {
    echo "Error";
};


$answerJson =  json_decode($responceJson);

if ($answerJson->SubmitDataResult == success) {
    echo "Sold";
} elseif ($answerJson->SubmitDataResult == reject) {
    echo "Reject";
} else {
    echo "Error";
};
