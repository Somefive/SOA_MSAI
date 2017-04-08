<?php
if ($_POST['type'] == "pixel") {
    $im = imagecreatetruecolor(320, 240);
    foreach (explode("|", $_POST['image']) as $y => $csv) {
        foreach (explode(";", $csv) as $x => $color) {
            imagesetpixel($im, $x, $y, $color);
        }
    }
} else {
    $im = imagecreatefrompng($_POST['image']);
}
$timestamp = time();
$image_name = "${_GET["username"]}_$timestamp.png";
if ($im) {
    if (!imagepng($im, "images/$image_name"))
        die(json_encode(["status"=>false,"message"=>"cannot save image"]));
} else {
    die(json_encode(["status"=>false,"message"=>"cannot recognize image"]));
}
