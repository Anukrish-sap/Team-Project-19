// registeracc.php


<?php
session_start();
include "dbconnect.php";
include "pass_strength.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $rawPassword = $_POST['password'];
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');

    // Debug incoming POST data to verify it's being received
    if (empty($rawPassword)  || empty($email) || empty($name)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: register.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: register.php");
        exit();
    }

    // Check password strength
    if (!checkPassStrength($rawPassword)) {
        $_SESSION['error'] = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
        header("Location: register.php");
        exit();
    }

  

    // Hash the password
    $password = password_hash($rawPassword, PASSWORD_DEFAULT);

    // Check for duplicate email
    $checkemail = $db->prepare("SELECT email FROM users WHERE email = :email");
    $checkemail->bindParam(':email', $email);
    try {
        $checkemail->execute();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error checking email: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }

    if ($checkemail->rowCount() > 0) {
        $_SESSION['error'] = "Username already exists. Please choose a different username.";
        header("Location: register.php");
        exit();
    }

    // Insert new user into the database
    $nameAdd = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)"); // ADDED name column
    $nameAdd->bindParam(':name', $name);
    $nameAdd->bindParam(':email', $email);
    $nameAdd->bindParam(':password', $password);
    
    try {
        if ($nameAdd->execute()) {
            $_SESSION['success'] = "Account created successfully!";
            header("Location: loginpage.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to create the account. Please try again.";
            header("Location: register.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error creating account: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: register.php");
    exit();
}
?>