<?php
session_start();
require_once 'bake/dbconnect.php';

$basket = isset($_SESSION['basket']) && is_array($_SESSION['basket'])
    ? $_SESSION['basket']
    : [];

$items     = [];
$totalQty  = 0;
$totalCost = 0.0;

if (!empty($basket)) {
    $ids = array_keys($basket);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT bakeID, bakeName, description, price, imageFileName
            FROM bakes
            WHERE bakeID IN ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as &$item) {
        $id          = (int)$item['bakeID'];
        $item['qty'] = $basket[$id] ?? 0;
        $item['line'] = $item['price'] * $item['qty'];
        $totalQty    += $item['qty'];
        $totalCost   += $item['line'];
    }
    unset($item);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Basket | Bakes & Cakes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="light">

<header class="site-header">
    <div class="logo-area">
        <img src="images/logo.png" alt="Bakes & Cakes logo" class="logo">
        <div class="brand-text">
            <h1>Bakes & Cakes</h1>
            <p class="tagline">Your home for all your bakes and cakes</p>
        </div>
    </div>

    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="bake/bakes.php">Products</a></li>
            <li><a href="basket.php" class="active">Basket</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

    <button id="theme-toggle" aria-label="Toggle light or dark mode">
        Dark mode
    </button>
</header>

<main class="section basket-page">
    <div class="basket-header-row">
        <h2>Basket</h2>
        <?php if (!empty($items)): ?>
            <a href="basket_clear.php" class="btn secondary small">Remove all</a>
        <?php endif; ?>
    </div>

    <div class="basket-summary-card">
        <div>
            <h3>Summary</h3>
            <p>Items: <strong><?= $totalQty ?></strong></p>
        </div>
        <div class="basket-summary-right">
            <p><strong>Total cost:</strong> £<?= number_format($totalCost, 2) ?></p>
            <button type="button" class="btn primary small">Proceed to checkout</button>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <p>Your basket is empty.</p>
        <a href="bake/bakes.php" class="btn primary">Browse products</a>
    <?php else: ?>

        <form action="basket_update.php" method="post" class="basket-items">
            <?php foreach ($items as $item): ?>
                <div class="basket-item-card">
                    <div class="basket-item-left">
                        <?php if (!empty($item['imageFileName'])): ?>
                            <img
                                src="images/<?= htmlspecialchars($item['imageFileName']) ?>.png"
                                alt="<?= htmlspecialchars($item['bakeName']) ?>"
                                class="basket-img"
                            >
                        <?php else: ?>
                            <div class="basket-img placeholder-image">Bake</div>
                        <?php endif; ?>
                    </div>

                    <div class="basket-item-middle">
                        <h4 class="basket-item-name">
                            <?= htmlspecialchars($item['bakeName']) ?>
                        </h4>
                        <p class="basket-item-price">
                            £<?= number_format($item['price'], 2) ?>
                        </p>
                        <?php if (!empty($item['description'])): ?>
                            <p class="basket-item-desc">
                                <?= htmlspecialchars($item['description']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="basket-item-right">
                        <button
                            type="submit"
                            name="remove_single"
                            value="<?= (int)$item['bakeID'] ?>"
                            class="btn secondary small"
                        >Remove</button>

                        <div class="basket-qty-controls">
                            <input
                                class="qty-input"
                                type="number"
                                name="qty[<?= (int)$item['bakeID'] ?>]"
                                value="<?= (int)$item['qty'] ?>"
                                min="0"
                            >
                        </div>

                        <p class="basket-line-total">
                            Line total:
                            <strong>£<?= number_format($item['line'], 2) ?></strong>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="basket-footer-actions">
                <button type="submit" class="btn primary">Update basket</button>
                <a href="bake/bakes.php" class="btn secondary">Continue shopping</a>
            </div>
        </form>

    <?php endif; ?>
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
