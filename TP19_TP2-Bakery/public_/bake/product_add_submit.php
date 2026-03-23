
<?php
session_start();

require_once 'dbconnect.php';

if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

// Validate required fields
if (
    !isset($_POST['bakeName'], $_POST['description'], $_POST['price'],
            $_POST['bakeTypeID'], $_POST['stockAmount'])
) {
    die("Missing required fields.");
}

$bakeName    = trim($_POST['bakeName']);
$description = trim($_POST['description']);
$price       = floatval($_POST['price']);
$bakeTypeID  = intval($_POST['bakeTypeID']);
$stockAmount = intval($_POST['stockAmount']);

//uploading the image 
if (!isset($_FILES['imageFile']) || $_FILES['imageFile']['error'] !== UPLOAD_ERR_OK) {
    die("Image upload failed.");
}

$uploadDir = __DIR__ . "/img/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$originalName = basename($_FILES['imageFile']['name']);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($extension, $allowed)) {
    die("Invalid image format.");
}

$newFileName = uniqid("bake_", true) . "." . $extension;
$targetPath = $uploadDir . $newFileName;

if (!move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetPath)) {
    die("Failed to save uploaded image.");
}

//inserts the bake
$stmt = $db->prepare("
    INSERT INTO bakes (bakeName, description, price, bakeTypeID, imageFileName)
    VALUES (:bakeName, :description, :price, :bakeTypeID, :imageFileName)
");

$stmt->execute([
    ':bakeName'      => $bakeName,
    ':description'   => $description,
    ':price'         => $price,
    ':bakeTypeID'    => $bakeTypeID,
    ':imageFileName' => $newFileName
]);

$bakeID = $db->lastInsertId();

//inserts stock into inventory
$stmt = $db->prepare("
    INSERT INTO inventory (bakeID, amount)
    VALUES (:bakeID, :amount)
");

$stmt->execute([
    ':bakeID' => $bakeID,
    ':amount' => $stockAmount
]);


// Handles ingredients

$names     = $_POST['ingredientName'] ?? [];
$quantities = $_POST['ingredientQuantity'] ?? [];

for ($i = 0; $i < count($names); $i++) {

    $name = trim($names[$i]);
    $qty  = trim($quantities[$i]);

    if ($name === "" || $qty === "") continue;

    // 1. Check if ingredient exists
    $stmt = $db->prepare("SELECT ingredientID FROM ingredients WHERE ingredientName = :name");
    $stmt->execute([':name' => $name]);
    $ingredient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ingredient) {
        $ingredientID = $ingredient['ingredientID'];
    } else {
        // Insert new ingredient
        $stmt = $db->prepare("INSERT INTO ingredients (ingredientName) VALUES (:name)");
        $stmt->execute([':name' => $name]);
        $ingredientID = $db->lastInsertId();
    }

    // 2. Insert into bakeIngredients
    $stmt = $db->prepare("
        INSERT INTO bakeIngredients (bakeID, ingredientID, quantity)
        VALUES (:bakeID, :ingredientID, :quantity)
    ");

    $stmt->execute([
        ':bakeID'       => $bakeID,
        ':ingredientID' => $ingredientID,
        ':quantity'     => $qty
    ]);
}

// Redirect
header("Location: stock.php?added=1");
exit();
?>