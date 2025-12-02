<?php
session_start();
require_once 'dbconnect.php';

try {
    // Category from URL
    $category = isset($_GET['category']) ? $_GET['category'] : null;

    // Category slug → bakeTypeID
    $categoryMap = [
        'cakes'    => 1,
        'cookies'  => 2,
        'pastries' => 3,
        'bread'    => 4
    ];

    // For heading display
    $categoryNames = [
        'cakes'    => 'Cakes',
        'cookies'  => 'Cookies',
        'pastries' => 'Pastries',
        'bread'    => 'Bread'
    ];

    // Base SQL
    $sql = "
        SELECT bakeID, bakeName, description, price, bakeTypeID, imageFileName
        FROM bakes
        WHERE 1=1
    ";

    // Filter by category
    if ($category && isset($categoryMap[$category])) {
        $sql .= " AND bakeTypeID = :bakeTypeID";
    }

    $query = $db->prepare($sql);

    if ($category && isset($categoryMap[$category])) {
        $query->bindValue(':bakeTypeID', $categoryMap[$category], PDO::PARAM_INT);
    }

    $query->execute();
    $bakes = $query->fetchAll(PDO::FETCH_ASSOC);

    // Heading
    $heading = $category ? ($categoryNames[$category] ?? 'All bakes') : 'All bakes';

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bakes & Cakes | Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Correct CSS path from /bake/ -->
    <link rel="stylesheet" href="../css/Styles.css">
</head>
<body class="light">

<header class="site-header">
    <div class="logo-area">
        <!-- Correct logo path -->
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

            <li class="has-dropdown">
                <a href="bakes.php">Categories</a>
                <ul class="dropdown">
                    <li><a href="bakes.php?category=cakes">Cakes</a></li>
                    <li><a href="bakes.php?category=cookies">Cookies</a></li>
                    <li><a href="bakes.php?category=pastries">Pastries</a></li>
                    <li><a href="bakes.php?category=bread">Bread</a></li>
                </ul>
            </li>

            <li><a href="../basket.php">Basket</a></li>
            <li><a href="../login.php">Login</a></li>
            <li><a href="../register.php">Register</a></li>
            <li><a href="../contact.php">Contact</a></li>
            <li><a href="../about.php">About</a></li>
        </ul>
    </nav>

    <button id="theme-toggle">Dark mode</button>
</header>

<main class="section">
    <h2><?= htmlspecialchars($heading) ?></h2>

    <p class="section-intro">
        <?= ($heading === 'All bakes') ? 'Browse all cakes, cookies, pastries and breads.' : "Showing only $heading." ?>
    </p>

    <div class="card-grid">

        <?php if (empty($bakes)): ?>
            <p>No bakes found for this category.</p>

        <?php else: ?>
            <?php foreach ($bakes as $row): ?>
                <article class="card product-card">

                    <?php if (!empty($row['imageFileName'])): ?>
                        <!-- CORRECT product image path -->
                        <img
                            src="img/<?= htmlspecialchars($row['imageFileName']) ?>"
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
</main>

<!-- Browse by category section -->
<section class="section section-alt">
    <h3>Browse by category</h3>

    <div class="card-grid categories-grid">

        <a href="bakes.php?category=cakes" class="card category-card">
            <img src="img/chocolatefudgecake.png" alt="Cakes">
            <h4>Cakes</h4>
            <p>Celebration cakes, layer cakes, and loaf cakes.</p>
        </a>

        <a href="bakes.php?category=cookies" class="card category-card">
            <img src="img/cookiecollection.png" alt="Cookies">
            <h4>Cookies</h4>
            <p>Fresh soft and crunchy cookies daily.</p>
        </a>

        <a href="bakes.php?category=pastries" class="card category-card">
            <img src="img/croissant.png" alt="Pastries">
            <h4>Pastries</h4>
            <p>Buttery croissants and sweet pastries.</p>
        </a>

        <a href="bakes.php?category=bread" class="card category-card">
            <img src="img/white_loaf.png" alt="Bread">
            <h4>Bread</h4>
            <p>Freshly baked loaves and rolls.</p>
        </a>

    </div>
</section>

<footer class="site-footer">
    <div class="footer-content">
        <p>Bakes & Cakes - Student Bakery Project</p>
        <p>Email: <a href="mailto:bakesandcakes@contact.com">bakesandcakes@contact.com</a></p>
        <p>&copy; <?= date('Y'); ?> Bakes & Cakes</p>
    </div>
</footer>

<script>
const toggleButton = document.getElementById('theme-toggle');
toggleButton.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    toggleButton.textContent = document.body.classList.contains('dark')
        ? 'Light mode'
        : 'Dark mode';
});
</script>

</body>
</html>
