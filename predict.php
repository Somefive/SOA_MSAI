<?php
include_once "tools.php";
if ($user == null)
    header("Location: index.php");
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>二手车预测平台</title>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.bootcss.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
</head>
<body>
<div style="text-align: center">
    <h3 style="margin: 20px" id="welcome">Welcome To Use Automobile Prediction! <?=$user["username"];?></h3>
    <h5 style="margin: 20px" id="info">Age:&nbsp;<?=$user["age"]?>&nbsp;Gender:&nbsp;<?=$user["gender"]?></h5>
</div>
<div style="text-align: right">
    <a class="btn btn-info" href="logout.php" style="margin: 20px">logout</a>
</div>
<form style="margin: 50px">
    <div class="form-group">
        <label>make</label>
        <input type="text" class="form-control" id="make" placeholder="make">
    </div>
    <div class="form-group">
        <label>body-style</label>
        <input type="text" class="form-control" id="body-style" placeholder="body-style">
    </div>
    <div class="form-group">
        <label>wheel-base</label>
        <input type="number" class="form-control" id="wheel-base" placeholder="wheel-base">
    </div>
    <div class="form-group">
        <label>engine-size</label>
        <input type="number" class="form-control" id="engine-size" placeholder="engine-size">
    </div>
    <div class="form-group">
        <label>horsepower</label>
        <input type="number" class="form-control" id="horsepower" placeholder="horsepower">
    </div>
    <div class="form-group">
        <label>peak-rpm</label>
        <input type="number" class="form-control" id="peak-rpm" placeholder="peak-rpm">
    </div>
    <div class="form-group">
        <label>highway-mpg</label>
        <input type="number" class="form-control" id="highway-mpg" placeholder="highway-mpg">
    </div>
    <button id="predict" type="button" class="btn btn-success">Predict</button>
    <h3 style="margin: 20px" id="price">Predict Price: $0</h3>
</form>
<script>
    $(function() {
        $('#predict').click(function () {
            $('#price').text('Predicting...');
            $.post('predict-api.php',{
                'make': $('#make').val(),
                'body-style': $('#body-style').val(),
                'wheel-base': $('#wheel-base').val(),
                'engine-size': $('#engine-size').val(),
                'horsepower': $('#horsepower').val(),
                'peak-rpm': $('#peak-rpm').val(),
                'highway-mpg': $('#highway-mpg').val()
            }, function(data) {
                $('#price').text('Predict Price: $ '+data.data);
            }, 'json');
        })
    });
</script>
</body>
</html>
