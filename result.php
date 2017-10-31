<?php


require_once 'HTTP/Request2.php';
require('setting.php');
// function h($string) {
//     if (is_array($string)) {
//         return array_map("h", $string);
//     } else {
//         return htmlspecialchars($string, ENT_QUOTES,'UTF-8');
//     }
// }
// echo "<pre>$_POST------------------";
// print_r(h($_POST));
// echo "---------------</pre>";

//canvasデータがPOSTで送信されてきた場合
$canvasData = $_POST["canvasData"];

$canvasData = preg_replace("/data:[^,]+,/i","",$canvasData);

$canvasData = base64_decode($canvasData);

$image = imagecreatefromstring($canvasData);

imagesavealpha($image, TRUE); // 透明色の有効
imagepng($image ,'./img/result.png');


// NOTE: You must use the same region in your REST call as you used to obtain your subscription keys.
//   For example, if you obtained your subscription keys from westcentralus, replace "westus" in the 
//   URL below with "westcentralus".
$request = new Http_Request2('https://westus.api.cognitive.microsoft.com/emotion/v1.0/recognize');
$url = $request->getUrl();

$api_key = new API();

$headers = array(
    // Request headers
    'Content-Type' => 'application/json',

    // NOTE: Replace the "Ocp-Apim-Subscription-Key" value with a valid subscription key.
    'Ocp-Apim-Subscription-Key' => $api_key->EMOTION_API_KEY,
);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_POST);

// Request body
$request->setBody('{"url": "https://emotionwebsto.blob.core.windows.net/handson/emotionweb_happiness.jpg"}');

try
{
    $response = $request->send();
    echo $response->getBody();
}
catch (HttpException $ex)
{
    echo $ex;
}

//header('Location: tweet.html');

?>

<!DOCTYPE html>
<html>
<head>
<meta content="width=device-width initial-scale=1.0 minimum-scale=1.0 maximum-scale=1.0 user-scalable=no" name="viewport">
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<title>Tweetする？</title>


</head>
<body>

<style>
input {
	font-size: 20px;
}
#camera {
	width: 400px;
	height: 300px;
}
#canvas,#camera {
	border: 1px solid #000;
}
</style>

<br>
<br>
<br>
<a href="javascript:location.href='http://twitter.com/home?status='+encodeURI(document.title)+'%20'+encodeURI(location.href)+'img/test.jpg' + '(11月10日まで)+%2523白鷺祭  %2523計算知能工学研究室 %2523B4-E409'">Tweetするよ！</a>
<br>
<br>


