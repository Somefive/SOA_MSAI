<?php
setcookie("access_token","",time()+86400, "/");
header("Location: index.php");
