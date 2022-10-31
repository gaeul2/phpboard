<?php

$mysql_host = "localhost";
$mysql_user = "root";
$mysql_pw = "1234";
$mysql_db = "project";

$conn = mysqli_connect($mysql_host, $mysql_user, $mysql_pw, $mysql_db);

if (!$conn) {
    die("db연결 실패 :" . mysqli_connect_error());
}

