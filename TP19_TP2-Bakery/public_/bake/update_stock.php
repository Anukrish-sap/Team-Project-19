<?php
session_start();
include "dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bakeID = (int)$_POST['bakeID'];
    $amount = (int)$_POST['amount'];

    $stmt = $db->prepare("UPDATE inventory SET amount = :amount WHERE bakeID = :bakeID");
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':bakeID', $bakeID, PDO::PARAM_INT);

   if ($stmt->execute()) {
    $_SESSION['success'] = "Stock updated.";
} else {
    $_SESSION['error'] = "Failed to update stock.";
}

header("Location: stock.php");
exit();
}
?>