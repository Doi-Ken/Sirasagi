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

$canvashash = abs(crc32($canvasData));
$saveData = './img/'.((string)$canvashash).'.png';

$canvasData = preg_replace("/data:[^,]+,/i","",$canvasData);

$canvasData = base64_decode($canvasData);
//echo $saveData;

//here something is wrong
$image = imagecreatefromstring($canvasData);
//to here

imagesavealpha($image, TRUE); // 透明色の有効
imagepng($image ,$saveData);


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

//$requestPng = "http://www.doi-ken.com/public_html/faceapi/img/"."result".".png";
$requestPng = "http://www.doi-ken.com/public_html/faceapi/img/".((string)$canvashash).".png";
$requestBody = '{"url": "'.$requestPng.'"}';

// Request body
//$request->setBody('{"url": "http://www.doi-ken.com/faceapi/img/result.png"}');
$request->setBody($requestBody);

try
{
    $response = $request->send();
    //echo $response->getBody();
}
catch (HttpException $ex)
{
   // echo $ex;
}

$ret = json_decode($response->getBody());
if(!$ret) {
    return null;
  }
  $scores = $ret[0]->scores;
  $emotions = array(
    'anger' => $scores->anger,
    'contempt' => $scores->contempt,
    'disgust' => $scores->disgust,
    'fear' => $scores->fear,
    'happiness' => $scores->happiness,
    'neutral' => $scores->neutral,
    'sadness' => $scores->sadness,
    'surprise' => $scores->surprise,
  );

  
echo nl2br("\n");;
echo $emotions['anger'];

    // 戻り値の中から一番顕著な感情を取得する。
    $max_point = max($emotions);
    $hit_emotions = array_keys($emotions, $max_point);
    $array_num = array_rand($hit_emotions, 1); // 同点対応
    $emotion = $hit_emotions[$array_num];

    $result['emotion'] = $emotion;
    $result['point'] = $emotions[$emotion];

    echo nl2br("\n");;
    echo $result['emotion'].": ".$result['point'];

   
    $png_alpha = './'.$result['emotion'].'.png';
   
    $image_back = imagecreatefrompng($saveData);
    $image_alpha = imagecreatefrompng($png_alpha);
    list($width, $height, $type, $attr) = getimagesize($saveData);

    

    $marge_right = 10;
    $marge_bottom = 10;
    $sx = imagesx($image_alpha);
    $sy = imagesy($image_alpha);
    
    // スタンプを、50% の不透明度で写真に重ねます
    imagecopymerge($image_back,  //コピー先の画像リンクリソース
    $image_alpha, //コピー元の画像リンクリソース
     imagesx($image_back) - $sx - $marge_right,
     imagesy($image_back) - $sy - $marge_bottom, 
     0, 0, imagesx($image_alpha), imagesy($image_alpha), 100);

    $saveData2 = './img/'.((string)$canvashash).'_.png';
    imagepng($image_back, $saveData2);
    imagedestroy($image_back);
    imagedestroy($image_alpha);
    $requestPng2 = "http://www.doi-ken.com/public_html/faceapi/img/".((string)$canvashash)."_.png";
    


?>

<!DOCTYPE html>
<html>
<head>
<meta content="width=device-width initial-scale=1.0 minimum-scale=1.0 maximum-scale=1.0 user-scalable=no" name="viewport">
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<title>表情認識できたよ！</title>


</head>
<body>

<style>
.flat_ss { 
    color: #484848;
    display: inline-block;
    height: 50px;
    font-size: 25px;
    line-height: 50px;
    vertical-align: middle;
    background: #eaeef1;
    text-decoration: none;
    margin: 1em;
}

.flat_ss .iconback{
    display: inline-block;
    width: 50px;
    height: 50px;
    text-align: center;
    color: white;
}
.flat_ss .iconback .fa{
    font-size: 25px;
    line-height: 50px;
}
.flat_ss .iconback .fa{
	transition: .3s;
}

.flat_ss .btnttl{
    display: inline-block;
    width: 120px;
    text-align: center;
    vertical-align:middle;
}

.flat_ss .tw {background:#1da1f3}
.flat_ss .fb {background:#3b75d4}
.flat_ss .fdly {background:#7ece46}
.flat_ss .pkt {background:#fd7171}
.flat_ss:hover .iconback .fa{
    -webkit-transform: rotateX(360deg);
    -ms-transform: rotateX(360deg);
    transform: rotateX(360deg);
}
</style>

<br>
<br>
<br>

<?php
echo "<a href=\"javascript:location.href='http://twitter.com/home?status='+encodeURI(document.title) + '"."(".$result['emotion'].") ".$requestPng2." (11月10日まで)+%2523白鷺祭  %2523計算知能工学研究室 %2523B4-E409 %2523大阪府立大学'\" class=\"flat_ss\">
<span class=\"iconback tw\"><i class=\"fa fa-twitter\"></i></span><span class=\"btnttl\">TWEET</span>
</a>";
echo "<img src=\"".$requestPng2."\" alt=\"recognitioinresult\" title=\"RecognitionResult\">";
?>
<br>

