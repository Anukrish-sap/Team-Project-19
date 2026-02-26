<?php
session_start();

$bakeID = isset($_POST['bakeID']) ? (int)$_POST['bakeID'] : 0;
$qty    = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($bakeID > 0 && $qty > 0) {
    if (!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) {
        $_SESSION['basket'] = [];
    }

    $current = $_SESSION['basket'][$bakeID] ?? 0;
    
    $allowed = get_allowed_quantity($bakeID, $current + $qty);

    // add or increase quantity
    $_SESSION['basket'][$bakeID] = $allowed;

}

// send user back where they came from
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: ' . $redirect);
exit;<?php
session_start();

$bakeID = isset($_POST['bakeID']) ? (int)$_POST['bakeID'] : 0;
$qty    = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($bakeID > 0 && $qty > 0) {
    if (!isset($_SESSION['basket']) || !is_array($_SESSION['basket'])) {
        $_SESSION['basket'] = [];
    }

    $current = $_SESSION['basket'][$bakeID] ?? 0;
    
    $allowed = get_allowed_quantity($bakeID, $current + $qty);

    // add or increase quantity
    $_SESSION['basket'][$bakeID] = $allowed;

}

// send user back where they came from
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: ' . $redirect);
exit;