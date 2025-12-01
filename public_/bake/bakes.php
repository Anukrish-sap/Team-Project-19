<?php
session_start();
require_once 'dbconnect.php';

$sql = "SELECT bakeID, bakeName, description, price, imageFileName
        FROM bakes
        ORDER BY bakeName";
$stmt  = $db->query($sql);
$bakes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bakes & Cakes | Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- bakes.php is inside /bake so we go up one level to css -->
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="light">

<header class="site-header">
    <div class="logo-area">
        <img src="../images/logo.png" alt="Bakes & Cakes logo" class="logo">
        <div class="brand-text">
            <h1>Bakes & Cakes</h1>
            <p class="tagline">Your home for all your bakes and cakes</p>
        </div>
    </div>

    <nav class="main-nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="bakes.php" class="active">Products</a></li>
            <li><a href="../basket.php">Basket</a></li>
            <li><a href="../login.php">Login</a></li>
            <li><a href="../register.php">Register</a></li>
            <li><a href="../contact.php">Contact</a></li>
            <li><a href="../about.php">About</a></li>
        </ul>
    </nav>

    <button id="theme-toggle" aria-label="Toggle light or dark mode">
        Dark mode
    </button>
</header>

<main>
    <section class="section">
        <h2>All bakes</h2>
        <p class="section-intro">Browse all cakes, cookies, pastries and breads.</p>

        <div class="card-grid">
            <?php if (empty($bakes)): ?>
                <p>No bakes found.</p>
            <?php else: ?>
                <?php foreach ($bakes as $row): ?>
                    <article class="card product-card">
                        <?php if (!empty($row['imageFileName'])): ?>
                            <img
                                src="../images/<?= htmlspecialchars($row['imageFileName']) ?>.png"
                                alt="<?= htmlspecialchars($row['bakeName']) ?>"
                                class="product-image"
                                style="height:140px;width:100%;object-fit:cover;border-radius:0.7rem;"
                            >
                        <?php else: ?>
                            <div class="product-image placeholder-image">Bake</div>
                        <?php endif; ?>

                        <h4><?= htmlspecialchars($row['bakeName']) ?></h4>

                        <?php if (!empty($row['description'])): ?>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                        <?php endif; ?>

                        <p class="price">£<?= number_format($row['price'], 2) ?></p>

                        <form action="../basket_add.php" method="post" class="add-to-basket-form">
                            <input type="hidden" name="bakeID" value="<?= (int)$row['bakeID'] ?>">
                            <label>
                                Qty:
                                <input type="number" name="qty" value="1" min="1" class="qty-input">
                            </label>
                            <button type="submit" class="btn small">Add to basket</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="footer-content">
        <p>Bakes & Cakes - Student Bakery Project</p>
        <p>123 Example Street, Birmingham, UK</p>
        <p>Email: <a href="mailto:bakesandcakes@contact.com">bakesandcakes@contact.com</a></p>
        <p>&copy; <?= date('Y'); ?> Bakes & Cakes</p>
    </div>
</footer>

<script>
const toggleButton = document.getElementById('theme-toggle');
const body = document.body;

toggleButton.addEventListener('click', function () {
    if (body.classList.contains('light')) {
        body.classList.remove('light');
        body.classList.add('dark');
        toggleButton.textContent = 'Light mode';
    } else {
        body.classList.remove('dark');
        body.classList.add('light');
        toggleButton.textContent = 'Dark mode';
    }
});
</script>

</body>
</html>
