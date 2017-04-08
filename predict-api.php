<?php
include_once "tools.php";
//if ($user == null)
//    die(response("not authorized."));
$data = [
    "Inputs"=> [
        "input1"=> [
            "ColumnNames"=> [
                "make",
                "body-style",
                "wheel-base",
                "engine-size",
                "horsepower",
                "peak-rpm",
                "highway-mpg"
            ],
      "Values"=> [
                [
                    $_POST["make"],
                    $_POST["body-style"],
                    $_POST["wheel-base"],
                    $_POST["engine-size"],
                    $_POST["horsepower"],
                    $_POST["peak-rpm"],
                    $_POST["highway-mpg"]
                ],
            ]
    ]
  ],
  "GlobalParameters"=> (object)[]
];
$res = CurlPredict(json_encode($data));
$resobj = json_decode($res, true);
if (isset($resobj["error"])) {
    die(response($resobj["error"]["message"]));
} else {
    $score = number_format(doubleval($resobj["Results"]["output1"]["value"]["Values"][0][0]),2);
    die(response($res, $score, true));
}
