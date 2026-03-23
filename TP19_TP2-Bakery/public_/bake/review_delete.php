<?php
ob_start(); 
require_once 'dbconnect.php';


ob_end_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$reviewID = isset($_POST['reviewID']) ? (int)$_POST['reviewID'] : 0;
$userID   = (int)$_SESSION['userID'];

if ($reviewID <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid review ID.']);
    exit();
}

try {
    $sql = "DELETE FROM bakeReviews WHERE reviewID = :reviewID AND userID = :userID";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':reviewID', $reviewID, PDO::PARAM_INT);
    $stmt->bindValue(':userID',   $userID,   PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
  
        unset($_SESSION['review_success'], $_SESSION['review_error']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Review not found or permission denied.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}