<?php
session_start();

if (!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}


if (isset($_POST['remove_single'])) {
    $id = (int)$_POST['remove_single'];
    unset($_SESSION['basket'][$id]);
    header('Location: basket.php');
    exit;
}


if (isset($_POST['qty']) && is_array($_POST['qty'])) {
    $basket = $_SESSION['basket'];

    foreach ($_POST['qty'] as $id => $qty) {
    $id  = (int)$id;
    $qty = max(0, (int)$qty);
    $stmt = $db->prepare("SELECT amount FROM inventory WHERE bakeID = ?");
    $stmt->execute([$id]);
    $stock = (int)$stmt->fetchColumn();

    if ($qty > $stock) {
        $qty = $stock;
        $_SESSION['error_message'] = "The quantity must be less than the amount in stock.";
    }

    if ($qty === 0) {
        unset($basket[$id]);
    } else {
        $basket[$id] = $qty;
    }
}

    $_SESSION['basket'] = $basket;
}

header('Location: basket.php');
exit;