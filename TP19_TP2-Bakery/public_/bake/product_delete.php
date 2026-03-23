<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

if (!isset($_POST['bakeID'])) {
    die("Invalid request.");
}

$bakeID = intval($_POST['bakeID']);

// Delete child rows first (safe method)
$db->prepare("DELETE FROM bakeIngredients WHERE bakeID = ?")->execute([$bakeID]);
$db->prepare("DELETE FROM inventory WHERE bakeID = ?")->execute([$bakeID]);
$db->prepare("DELETE FROM bakeReviews WHERE bakeID = ?")->execute([$bakeID]);
$db->prepare("DELETE FROM purchaseItems WHERE bakeID = ?")->execute([$bakeID]);

// Delete the bake itself
$db->prepare("DELETE FROM bakes WHERE bakeID = ?")->execute([$bakeID]);

header("Location: stock.php?deleted=1");
exit();
?>