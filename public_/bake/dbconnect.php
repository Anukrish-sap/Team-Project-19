<?php
//db details:
$dbname = "cs2team19_db";
$dbhost = "localhost";
$username = "u-240078164";
$password = "Bdpa2M03Yu0aBlO";

//and connect. Use a 'try' command just in case.
try{
$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $username, $password);
}catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>