<?php
session_start();
include "dbconnect.php"; //connect to database

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); //get email from form
    $password = $_POST['password']; //get password from form

    // Fetch user details including email and name
    $details = $db->prepare("SELECT userID, email, name, password FROM users WHERE email = :email"); 
    $details->bindParam(':email', $email);
    $details->execute();
    $userData = $details->fetch(PDO::FETCH_ASSOC);

    if ($userData && password_verify($password, $userData["password"])) {
        // Set session variables
        $_SESSION['userID'] = $userData['userID'];
        $_SESSION['email']  = $userData['email'];
        $_SESSION['name']   = $userData['name']; 

        header("Location: home.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: loginpage.php");
        exit();
    }
}
?>