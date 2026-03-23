<?php
session_start();
require_once __DIR__ . '/dbconnect.php';
$bakeID      = isset($_POST['bakeID']) ? (int)$_POST['bakeID'] : 0;
$qty         = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
$size        = isset($_POST['size']) ? (int)$_POST['size'] : 0;
$cakeMessage = isset($_POST['cakeMessage']) ? trim($_POST['cakeMessage']) : '';
$host        = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$detailURL   = $host . "/public_/bake/bake_details.php?bakeID=" . $bakeID;
$fallbackURL = $host . "/public_/bake/bakes.php";

if ($bakeID <= 0 || $qty <= 0) {
  header("Location: " . $fallbackURL);
  exit;
}

try {
  $stmt = $db->prepare("
    SELECT b.price, b.bakeTypeID, COALESCE(i.amount,0) AS stockAmount
    FROM bakes b
    LEFT JOIN inventory i ON i.bakeID = b.bakeID
    WHERE b.bakeID = :id
    LIMIT 1
  ");
  $stmt->execute([':id' => $bakeID]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  header("Location: " . $fallbackURL);
  exit;
}

if (!$row) {
  header("Location: " . $fallbackURL);
  exit;
}

$isCake    = ((int)$row['bakeTypeID'] === 1);
$basePrice = (float)$row['price'];
$stock     = (int)$row['stockAmount'];
$baseSize  = 6;

function calcCakePrice(float $basePrice, int $size, int $baseSize = 6): float {
  if ($size === $baseSize) return $basePrice;
  $d   = $size - $baseSize;
  $raw = $basePrice * pow(1.1, $d);
  return (float)ceil($raw);
}

if (!$isCake) {
  $size        = 0;
  $unitPrice   = $basePrice;
  $cakeMessage = '';
} else {
  $allowedSizes = [6,7,8,9,10,11,12,13,14];
  if (!in_array($size, $allowedSizes, true)) $size = 6;
  $unitPrice   = calcCakePrice($basePrice, $size, $baseSize);
  $cakeMessage = mb_substr($cakeMessage, 0, 40);
}

if ($stock <= 0) {
  header("Location: " . $detailURL);
  exit;
}

$qty = min(max(1, $qty), $stock);
$key = $bakeID . ':' . $size;

if (!isset($_SESSION['basket_items']) || !is_array($_SESSION['basket_items'])) {
  $_SESSION['basket_items'] = [];
}

if (!isset($_SESSION['basket_items'][$key])) {
  $_SESSION['basket_items'][$key] = [
    'bakeID'      => $bakeID,
    'size'        => $size,
    'unitPrice'   => number_format($unitPrice, 2, '.', ''),
    'qty'         => 0,
    'cakeMessage' => $cakeMessage,
  ];
}

$_SESSION['basket_items'][$key]['qty'] += $qty;
$_SESSION['basket_items'][$key]['qty']  = min($_SESSION['basket_items'][$key]['qty'], $stock);

if ($isCake) {
  $_SESSION['basket_items'][$key]['cakeMessage'] = $cakeMessage;
}


$redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : $fallbackURL;
header("Location: " . $redirect);
exit;