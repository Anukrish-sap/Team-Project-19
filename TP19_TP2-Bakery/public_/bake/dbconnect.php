<?php
//db details:
$dbname = "cs2team19_db";
$dbhost = "localhost";
$username = "cs2team19";
$password = "zJkk2WXNP5VvRctKxzvvJxd4Y";

//and connect. Use a 'try' command just in case.
try{
$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $username, $password);
}catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>