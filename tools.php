<?php
$redis = new Redis();
$GLOBALS = json_decode(file_get_contents(__DIR__."/conf.json"),true);
$redis->connect($GLOBALS["redis-host"], $GLOBALS["redis-port"]);
$redis->auth($GLOBALS["redis-password"]);
function response($message = null, $data = null, $status = false) {
    return json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
}
function CurlGet($url, $params = null) {
    $ch = curl_init();
    if ($params != null)
        $url .= "?";
    foreach ($params as $key => $value)
        $url .= "$key=$value&";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, $GLOBALS["App-Name"]);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function CurlPost($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERAGENT, $GLOBALS["App-Name"]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Ocp-Apim-Subscription-Key: ' . $GLOBALS["Ocp-Apim-Subscription-Key"])
    );
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
$user = null;
if (key_exists("access_token", $_COOKIE)) {
    $res = $redis->get("msai::user::token::".$_COOKIE["access_token"]);
    if ($res != false) {
        $obj = $redis->get("msai::user::id::".$res);
        if ($obj != false)
            $user = json_decode($obj, true);
    }
}