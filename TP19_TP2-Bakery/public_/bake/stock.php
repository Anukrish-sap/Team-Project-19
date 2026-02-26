<?php

session_start();
include "dbconnect.php";

if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

$userID = $_SESSION['userID'];

// Check admin status
$stmt = $db->prepare("
    SELECT adminStatus 
    FROM adminStatus 
    WHERE userID = :userID
");
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();

$admin = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$admin || (int)$admin['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}


$sql = "
    SELECT 
        bakes.bakeID,
        bakes.bakeName,
        bakes.description,
        bakes.price,
        bakes.imageFileName,
        inventory.amount AS stockAmount
    FROM bakes
    LEFT JOIN inventory ON inventory.bakeID = bakes.bakeID
";

$query = $db->prepare($sql);
$query->execute();
$bakes = $query->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}
if (isset($_SESSION['userID'])) {
  include '../components/header_unified.php';

}


?>




<link rel="stylesheet" href="css/styles.css">




<section class="section">
   
            <h1>Stock Management</h1>
        </div>
    </div>
</section>

<section class ="section section-alt">

<h2> Welcome, admin: <?php
if (isset($_SESSION['name'])) {
    echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8');
} ?>. </h2>
   </br>
   <h3> You can manage the stock of the bakery here <h3>
</section>

<section class ="section">
    <section class="section">
    <h2>Manage Stock</h2>

    <?php if (empty($bakes)): ?>
        <p>No bakes found.</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($bakes as $row): ?>
                <article class="card product-card">

                    <?php if (!empty($row['imageFileName'])): ?>
                        <img
                            src="<?= APP_URL ?>/img/uploads/<?= htmlspecialchars($row['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($row['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                            class="product-image"
                            style="height:140px;width:100%;object-fit:cover;border-radius:0.7rem;"
                        >
                    <?php else: ?>
                        <div class="product-image placeholder-image">Bake</div>
                    <?php endif; ?>

                    <h4><?= htmlspecialchars($row['bakeName'], ENT_QUOTES, 'UTF-8') ?></h4>

                    <?php if (!empty($row['description'])): ?>
                        <p><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>

                    <p class="price">£<?= number_format((float)$row['price'], 2) ?></p>

                    <!-- ADMIN STOCK UPDATE FORM -->
                    <form action="update_stock.php" method="post" class="stock-update-form">
                        <input type="hidden" name="bakeID" value="<?= (int)$row['bakeID'] ?>">

                        <label>
                            Stock:
                            <input
                                type="number"
                                name="amount"
                                value="<?= (int)$row['stockAmount'] ?>"
                                min="0"
                                class="qty-input"
                            >
                        </label>

                        <button type="submit" class="btn small">Update Stock</button>
                    </form>

                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

    



  


</body>
</html>
<?php include '../components/footer.php'; ?>

<?php include '../components/script.html'; ?>