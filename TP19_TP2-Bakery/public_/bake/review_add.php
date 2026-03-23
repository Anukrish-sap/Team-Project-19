<?php
session_start();
require_once 'dbconnect.php';

if (!defined('APP_URL')) define('APP_URL', '/public_/bake');

// Must be logged in
if (!isset($_SESSION['userID'])) {
    header('Location: ' . APP_URL . '/loginpage.php');
    exit;
}

// Must be POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/bakes.php');
    exit;
}

$userID     = (int)$_SESSION['userID'];
$bakeID     = isset($_POST['bakeID'])     ? (int)$_POST['bakeID']    : 0;
$rating     = isset($_POST['rating'])     ? (int)$_POST['rating']     : 0;
$reviewText = isset($_POST['reviewText']) ? trim($_POST['reviewText']) : '';

$redirect = APP_URL . '/bake_details.php?bakeID=' . $bakeID . '#tab-reviews';

// Basic validation
if ($bakeID <= 0 || $rating < 1 || $rating > 5 || $reviewText === '') {
    $_SESSION['review_error'] = 'Please fill in all fields and select a rating.';
    header('Location: ' . $redirect);
    exit;
}

$reviewText = mb_substr($reviewText, 0, 1000);

try {
    // One review per user per bake
    $sqlCheck = "SELECT COUNT(*) FROM bakeReviews WHERE bakeID = :bakeID AND userID = :userID";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->bindValue(':bakeID', $bakeID, PDO::PARAM_INT);
    $stmtCheck->bindValue(':userID', $userID, PDO::PARAM_INT);
    $stmtCheck->execute();

    if ((int)$stmtCheck->fetchColumn() > 0) {
        $_SESSION['review_error'] = 'You have already reviewed this product.';
        header('Location: ' . $redirect);
        exit;
    }

    // Insert
    $sql = "INSERT INTO bakeReviews (bakeID, userID, rating, reviewText) VALUES (:bakeID, :userID, :rating, :reviewText)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':bakeID',     $bakeID,     PDO::PARAM_INT);
    $stmt->bindValue(':userID',     $userID,     PDO::PARAM_INT);
    $stmt->bindValue(':rating',     $rating,     PDO::PARAM_INT);
    $stmt->bindValue(':reviewText', $reviewText, PDO::PARAM_STR);
    $stmt->execute();

    $_SESSION['review_success'] = 'Your review has been posted — thank you!';

} catch (PDOException $e) {
    $_SESSION['review_error'] = 'Something went wrong. Please try again.';
}

header('Location: ' . $redirect);
exit;