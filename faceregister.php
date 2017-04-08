<?php
require_once "upload.php";
require_once "tools.php";
if (empty($_GET["username"]))
    die(json_encode(["status"=>false, "message"=>"username cannot be empty"]));
$obj = $redis->get("msai::user::id::".$_GET["username"]);
if ($obj != false)
    die(json_encode(["status"=>false, "message"=>"User Already Exists"]));
$personGroupId="default";
$url_create_person="https://westus.api.cognitive.microsoft.com/face/v1.0/persongroups/${personGroupId}/persons";
$res = CurlPost($url_create_person,json_encode(["name"=>$_GET["username"]]));
if ($res == false)
    die(json_encode(["status"=>false, "message"=>"msai create person fail"]));
else {
    $resobj = json_decode($res, true);
    if (isset($resobj["error"]))
        die(json_encode(["status"=>false, "message"=>$resobj["error"]["message"]]));
    else {
        $token = $resobj["personId"];
        $personId = $token;
        $res = CurlPost("https://westus.api.cognitive.microsoft.com/face/v1.0/persongroups/${personGroupId}/persons/${personId}/persistedFaces", json_encode(["url"=>$GLOBALS["url"]."/images/$image_name"]));
        if ($res == false)
            die(json_encode(["status"=>false, "message"=>"msai add person face fail"]));
        else {
            $resobj = json_decode($res, true);
            if (isset($resobj["error"])){
                CurlDelete("https://westus.api.cognitive.microsoft.com/face/v1.0/persongroups/default/persons/${personId}");
                die(json_encode(["status"=>false, "message"=>$resobj["error"]["message"]]));
            }
            $detect_url="https://westus.api.cognitive.microsoft.com/face/v1.0/detect";
            $res = CurlPost($detect_url,json_encode(["url"=>$GLOBALS["url"]."/images/$image_name"]));
            if ($res == null)
                die(response("detection failed"));
            $resobj = json_decode($res, true);
            if (isset($resobj["error"]) )
                die(response($resobj["error"]["message"]));
            if (count($resobj) == 0)
                die(response("no face detected"));
            if (count($resobj) > 1)
                die(response("multi faces detected"));
            $attr = $resobj[0]["faceAttributes"];
            $detect_res = $resobj;
            CurlPost("https://westus.api.cognitive.microsoft.com/face/v1.0/persongroups/${personGroupId}/train","");
        }
        $res1 = $redis->set("msai::user::id::".$_GET["username"], json_encode(["username"=>$_GET["username"], "register_time"=>$timestamp, "token" => $token, "age"=>$attr["age"], "gender"=>$attr["gender"]]));
        $res2 = $redis->set("msai::user::token::$token", $_GET["username"]);
        if ($res1 == false || $res2 == false)
            die(json_encode(["status"=>false, "message"=>"redis save fail"]));
        setcookie("access_token",$token,$timestamp+86400, "/");
        die(json_encode(["status"=>true, "message"=>"Register Succeed.", "token"=>$token, "data"=>$detect_res]));
    }
}