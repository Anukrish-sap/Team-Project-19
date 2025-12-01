<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// match phpMyAdmin settings
$dbname   = "cs2team19_db";
$dbhost   = "127.0.0.1";
$dbport   = "3307";   // IMPORTANT: same as in config.inc.php
$username = "root";
$password = "";

try {
    $dsn = "mysql:host=$dbhost;port=$dbport;dbname=$dbname;charset=utf8mb4";
    $db  = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
