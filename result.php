<?php
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
imagepng($image ,'./result.png');

header('Location: tweet.html');

?>