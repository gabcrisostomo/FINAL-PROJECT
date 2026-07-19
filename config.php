<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "thread_trend";

$conn = mysqli_connect($host,$user, $pass,$dbname);

if (!$conn) {
    die("Database connection failure framework error: " . mysqli_connect_error());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>