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


<main>




<section class="stock-hero">
    <h1>Stock Management</h1>
</section>

<section class="stock-welcome">
    <div>
        <h2>Welcome, admin: <?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p>You can manage the stock of the bakery here.</p>
    </div>
</section>

<section class ="section">
    <section class="section">
    <h2>Manage Stock</h2>
<?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
    <div style="
        background:#d4edda;
        color:#155724;
        padding:12px 18px;
        border-radius:6px;
        border:1px solid #c3e6cb;
        margin-bottom:20px;
        font-weight:600;
    ">
        ✔ Product added successfully
    </div>
<?php endif; ?>

<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div style="
        background:#f8d7da;
        color:#721c24;
        padding:12px 18px;
        border-radius:6px;
        border:1px solid #f5c6cb;
        margin-bottom:20px;
        font-weight:600;
    ">
        ✔ Product deleted successfully
    </div>
<?php endif; ?>




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
                            <form action="product_delete.php" method="post" 
      onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">

    <input type="hidden" name="bakeID" value="<?= (int)$row['bakeID'] ?>">

    <button type="submit" class="btn small" 
            style="background:#c62828;color:white;margin-top:8px;">
        Delete Product
    </button>
</form>

                </article>
            <?php endforeach; ?>
            
            <a href="product_add.php" class="card product-card" 
   style="display:flex;align-items:center;justify-content:center;
          height:200px;text-align:center;font-weight:600;
          font-size:1.1rem; border:2px dashed var(--accent); 
          color:var(--accent); cursor:pointer;">
    + Add New Product
</a>

            
        </div>
    <?php endif; ?>
</section>

                    </main>

<?php include '../components/footer.php'; ?>

<?php include '../components/script.html'; ?>

  


</body>
</html>
