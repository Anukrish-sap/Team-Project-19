<?php
require_once '../dbconnect.php';

$category = [
    'cakes'    => 1,
    'cookies'  => 2,
    'pastries' => 3,
    'bread'    => 4
];

$categoryID = isset($_GET['category']) ? $_GET['category'] : null;

$sql = "SELECT bakeID, bakeName, description, price, bakeTypeID, imageFileName
        FROM bakes
        LEFT JOIN inventory ON bakes.bakeID = inventory.bakeID
        WHERE 1=1;";
if ($categoryID && isset($category[$categoryID])) {
    $sql = " AND bakes.bakeTypeID = :bakeTypeID";
}

$query = $db->prepare($sql);
if ($categoryID && isset($category[$categoryID])) {
    $query->bindValue(':bakeTypeID', $category[$categoryID], PDO::PARAM_INT);
}
$query->execute();
$bakes = $query->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($bakes);
?>