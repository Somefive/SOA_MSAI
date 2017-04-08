<?php
include_once "tools.php";
if ($user != null) {
    header("Location: predict.php");
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>SOA_MSAI</title>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.bootcss.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
    <script src="jquery.webcam.min.js"></script>
</head>
<style>
    .bg{
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        background: url("background.jpg") no-repeat;
        background-size: 100% 100%;
        filter: blur(10px) brightness(0.75);
        z-index: -500;
    }
</style>
<body style="height: 100%; width: 100%">
<div class="bg">
</div>
<div style="top: 50%; left: 50%; position: absolute; transform: translate(-50%, -50%);">
    <h3 style="color: white">Welcome to MSAI system</h3>
    <div id="webcam"></div>
    <div>
        <button class="btn btn-success" style="width: 320px" onclick="capture(false);" >拍照登录</button>
    </div>
    <div class="input-group" style="width: 320px">
        <input type="text" class="form-control" placeholder="注册用户名" id="username">
        <span class="input-group-btn">
        <button class="btn btn-info" onclick="capture(true)">拍照注册</button>
      </span>
    </div
    <input type="text" placeholder="注册用户名" name="username" id="username">
</div>

<script>
    var upload_url = "";
    function capture(isRegister) {
        if (isRegister) upload_url = "/faceregister.php?username="+$('#username').val();
        else upload_url = "/facelogin.php?username="+$('#username').val();
        webcam.capture();
    }
    function postToServer(image) {
        $.post(upload_url, {type: "data", image: image}, function(data) {
            if (data.status === false) {
                alert(data.message);
            } else {
                alert(data.message);
                window.location = "predict.php";
            }
        },"json");
    }
    $(function() {
        var pos = 0, ctx = null, saveCB, image = [];

        var canvas = document.createElement("canvas");
        canvas.setAttribute('width', 320);
        canvas.setAttribute('height', 240);

        if (canvas.toDataURL) {

            ctx = canvas.getContext("2d");

            image = ctx.getImageData(0, 0, 320, 240);

            saveCB = function(data) {

                var col = data.split(";");
                var img = image;

                for(var i = 0; i < 320; i++) {
                    var tmp = parseInt(col[i]);
                    img.data[pos + 0] = (tmp >> 16) & 0xff;
                    img.data[pos + 1] = (tmp >> 8) & 0xff;
                    img.data[pos + 2] = tmp & 0xff;
                    img.data[pos + 3] = 0xff;
                    pos+= 4;
                }

                if (pos >= 4 * 320 * 240) {
                    ctx.putImageData(img, 0, 0);
                    postToServer(canvas.toDataURL("image/png"));
                    pos = 0;
                }
            };

        } else {

            saveCB = function(data) {
                image.push(data);

                pos+= 4 * 320;

                if (pos >= 4 * 320 * 240) {
                    postToServer(image.join('|'));
                    pos = 0;
                }
            };
        }

        $("#webcam").webcam({

            width: 320,
            height: 240,
            mode: "callback",
            swffile: "/download/jscam_canvas_only.swf",

            onSave: saveCB,

            onCapture: function () {
                webcam.save();
            },

            debug: function (type, string) {
                console.log(type + ": " + string);
            }
        });

    });
</script>
</body>
</html>