<?php
session_start();
include "dbconnect.php";

if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

// Check admin status
$userID = $_SESSION['userID'];
$stmt = $db->prepare("SELECT adminStatus FROM adminStatus WHERE userID = :userID");
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || (int)$admin['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewID'])) {
    $reviewID = (int)$_POST['reviewID'];

    if (isset($_POST['answered'])) {
        $stmt = $db->prepare("UPDATE reviews SET answered = 1 WHERE reviewID = :reviewID");
        $stmt->bindParam(':reviewID', $reviewID, PDO::PARAM_INT);
        $stmt->execute();
    } else if (isset($_POST['admin_note'])) {
        $admin_note = $_POST['admin_note'];
        $stmt = $db->prepare("UPDATE reviews SET admin_note = :admin_note WHERE reviewID = :reviewID");
        $stmt->bindParam(':admin_note', $admin_note, PDO::PARAM_STR);
        $stmt->bindParam(':reviewID', $reviewID, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header("Location: reviews_admin.php"); // redirect back to reviews page
exit();