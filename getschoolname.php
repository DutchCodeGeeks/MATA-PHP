<?php
require "utils.php";
$result=curlget("https://schoolkiezer.magister.net/home/query?filter=".encodeURIComponent($_GET["name"]));
if($result[0]=="<")$result='[{"Licentie":"Er is een fout opgetreden","Url":"FAIL"}]';
die($result);
?>