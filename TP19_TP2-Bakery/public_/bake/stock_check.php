<?php
require_once 'dbconnect.php';

function get_allowed_quantity($bakeID, $requestedQty) {
    global $db;

    // Fetch stock
    $stmt = $db->prepare("SELECT amount FROM inventory WHERE bakeID = ?");
    $stmt->execute([$bakeID]);
    $stock = (int)$stmt->fetchColumn();

    // If no stock row exists, treat as 0
    if ($stock <= 0) {
        return 0;
    }

    // Cap the requested quantity to available stock
    return min($requestedQty, $stock);
}