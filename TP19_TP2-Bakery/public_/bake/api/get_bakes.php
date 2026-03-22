<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once '../dbconnect.php';

$category = [
    'cakes'    => 1,
    'cookies'  => 2,
    'pastries' => 3,
    'bread'    => 4
];

$categoryID = isset($_GET['category']) ? $_GET['category'] : null;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$tag = isset($_GET['tag']) ? $_GET['tag'] : null;

$sql = "SELECT 
            bakes.bakeID, 
            bakes.bakeName, 
            bakes.description, 
            bakes.price, 
            bakes.bakeTypeID, 
            bakes.imageFileName, 
            bakes.isGlutenFree,
            COALESCE(inventory.amount, 0) AS stockAmount
        FROM bakes
        LEFT JOIN inventory ON bakes.bakeID = inventory.bakeID
        WHERE 1=1";

if ($categoryID && isset($category[$categoryID])) {
    $sql .= " AND bakes.bakeTypeID = :bakeTypeID";
}

if ($tag === 'gluten-free') {
    $sql .= " AND bakes.isGlutenFree = 1";
}

if ($searchTerm !== '') {
    $sql .= " AND (bakes.bakeName LIKE :search OR bakes.description LIKE :search)";
}

$sql .= " ORDER BY bakes.bakeName ASC";

$query = $db->prepare($sql);

if ($categoryID && isset($category[$categoryID])) {
    $query->bindValue(':bakeTypeID', $category[$categoryID], PDO::PARAM_INT);
}

if ($searchTerm !== '') {
    $query->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
}

$query->execute();
$bakes = $query->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($bakes);
?>