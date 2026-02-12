<?php
session_start();

if (!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

$basket = $_SESSION['basket'];

if (isset($_POST['remove_single'])) {
    $id = (int)$_POST['remove_single'];
    unset($basket[$id]);
}

if (isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id  = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($basket[$id]);
        } else {
            $basket[$id] = $qty;
        }
    }
}

$_SESSION['basket'] = $basket;

header('Location: basket.php');
exit;
