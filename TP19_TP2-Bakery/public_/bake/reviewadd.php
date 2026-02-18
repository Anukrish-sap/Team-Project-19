// reviewadd.php


<?php
session_start();
include "dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $fullname = htmlspecialchars($_POST['fullname'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars($_POST['subject'], ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
   

    // Debug incoming POST data to verify it's being received
    if (empty($fullname)  || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: review.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: contact.php");
        exit();
    }

   

   
    $reviewAdd = $db->prepare("INSERT INTO reviews (fullname, emailaddress, subject, message) VALUES (:fullname, :email, :subject, :message)"); // ADDED name column
    $reviewAdd->bindParam(':fullname', $fullname);
    $reviewAdd->bindParam(':email', $email);
    $reviewAdd->bindParam(':subject', $subject);
    $reviewAdd->bindParam(':message', $message);
    
    try {
        if ($reviewAdd->execute()) {
            $_SESSION['success'] = "Review created successfully!";
            header("Location: contact.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to add the review. Please try again.";
            header("Location: contact.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error creating review: " . $e->getMessage();
        header("Location: contact.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: contact.php");
    exit();
}
?>