<?php
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['basket_items']) || !is_array($_SESSION['basket_items'])) {
  $_SESSION['basket_items'] = [];
}

$basket = $_SESSION['basket_items'];

/* remove one line item (key = bakeID:size) */
if (isset($_POST['remove_single'])) {
  $key = (string)$_POST['remove_single'];
  unset($basket[$key]);
  $_SESSION['basket_items'] = $basket;
  header('Location: basket.php');
  exit;
}

/* update quantities */
if (isset($_POST['qty']) && is_array($_POST['qty'])) {
  foreach ($_POST['qty'] as $key => $qty) {
    $key = (string)$key;
    $qty = max(0, (int)$qty);

    if (!isset($basket[$key])) continue;

    $bakeID = (int)$basket[$key]['bakeID'];

    // stock check
    $stmt = $db->prepare("SELECT COALESCE(amount,0) FROM inventory WHERE bakeID = ?");
    $stmt->execute([$bakeID]);
    $stock = (int)$stmt->fetchColumn();

    if ($stock > 0 && $qty > $stock) {
      $qty = $stock;
      $_SESSION['error_message'] = "The quantity must be less than the amount in stock.";
    }

    if ($qty === 0) {
      unset($basket[$key]);
    } else {
      $basket[$key]['qty'] = $qty;
    }
  }

  $_SESSION['basket_items'] = $basket;
}

header('Location: basket.php');
exit;