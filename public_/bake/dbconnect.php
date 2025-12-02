<?php
// LOCAL XAMPP database settings
$dbname   = "cs2team19_db";   // the database you imported in phpMyAdmin
$dbhost   = "127.0.0.1";      // or "localhost"
$dbport   = 3307;             // your phpMyAdmin is on 127.0.0.1:3307
$username = "root";
$password = "";               // no password for local root

try {
    $db = new PDO(
        "mysql:host=$dbhost;port=$dbport;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
