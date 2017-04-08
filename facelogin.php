<?php
require_once "upload.php";
require_once "tools.php";
$personGroupId="default";
$detect_url="https://westus.api.cognitive.microsoft.com/face/v1.0/detect";
$res = CurlPost($detect_url,json_encode(["url"=>$GLOBALS["url"]."/images/$image_name"]));
if ($res == null)
    die(response("MSAI detect fail"));
$resobj = json_decode($res,true);
if (isset($resobj["error"]))
    die(response($resobj["error"]["message"]));
if (count($resobj) == 0)
    die(response("no face detected"));
else if (count($resobj) > 1)
    die(response("multi faces detected"));
else {
    $faceid = $resobj[0]["faceId"];
    $identify_url = "https://westus.api.cognitive.microsoft.com/face/v1.0/identify";
    $res = CurlPost($identify_url, json_encode(["personGroupId"=>$personGroupId,"faceIds"=>[$faceid],"confidenceThreshold"=>0.5]));
    if ($res == null)
        die(response("Face Identify failed"));
    $resobj = json_decode($res, true);
    if (isset($resobj["error"]))
        die(response($resobj["error"]["message"]));
    $candidates = $resobj[0]["candidates"];
    if (count($candidates) == 0)
        die(response("Person not recognized as registered. Please register first."));
    $personId = $candidates[0]["personId"];
    $token = $personId;
    if ($redis->get("msai::user::token::".$token) == null)
        die(response("Please Register first."));
    setcookie("access_token",$token,$timestamp+86400, "/");
    die(json_encode(["status"=>true, "message"=>"Register Succeed.", "token"=>$token]));
}