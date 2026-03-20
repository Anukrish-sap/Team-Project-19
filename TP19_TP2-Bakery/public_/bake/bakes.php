<?php
session_start();
require_once 'dbconnect.php';

/**
 * URL roots
 * index.php is at /index.php
 * app pages/assets are at /public_/bake
 */
if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}

include '../components/header_unified.php';

try {
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $search   = isset($_GET['search']) ? trim($_GET['search']) : '';

    $categoryMap = [
        'cakes'    => 1,
        'cookies'  => 2,
        'pastries' => 3,
        'bread'    => 4
    ];

    $categoryNames = [
        'cakes'    => 'Cakes',
        'cookies'  => 'Cookies',
        'pastries' => 'Pastries',
        'bread'    => 'Bread'
    ];

    $sql = "
        SELECT
            bakes.bakeID,
            bakes.bakeName,
            bakes.description,
            bakes.price,
            bakes.bakeTypeID,
            bakes.imageFileName,
            COALESCE(inventory.amount, 0) AS stockAmount
        FROM bakes
        LEFT JOIN inventory ON inventory.bakeID = bakes.bakeID
        WHERE 1 = 1
    ";

    if ($category && isset($categoryMap[$category])) {
        $sql .= " AND bakes.bakeTypeID = :bakeTypeID";
    }

    if ($search !== '') {
        $sql .= " AND (bakes.bakeName LIKE :search OR bakes.description LIKE :search)";
    }

    $query = $db->prepare($sql);

    if ($category && isset($categoryMap[$category])) {
        $query->bindValue(':bakeTypeID', $categoryMap[$category], PDO::PARAM_INT);
    }

    if ($search !== '') {
        $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }

    $query->execute();
    $bakes = $query->fetchAll(PDO::FETCH_ASSOC);

    $heading = 'All bakes';
    if ($search !== '') {
        $heading = 'Search results';
    } elseif ($category && isset($categoryNames[$category])) {
        $heading = $categoryNames[$category];
    }

} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit();
}
?>

<main>
    <?php if (!isset($_SESSION['userID'])): ?>
        <section class="section">
            <h3>Log in to order</h3>
            <p>
                To place an order, please
                <a href="<?= APP_URL ?>/loginpage.php">log in</a> or
                <a href="<?= APP_URL ?>/register.php">register</a>.
            </p>
        </section>
    <?php else: ?>
        <section class="section">
            <h3>Welcome, <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?>!</h3>
            <p>Click any bake to view sizes, servings, ingredients and reviews.</p>
        </section>
    <?php endif; ?>

    <section class="section">
        <form id="searchBarForm" class="product-search-form">
            <input
                type="text"
                id="searchInput"
                class="product-search-input"
                placeholder="Search for a bake..."
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
            >
            <button type="submit" class="btn primary small product-search-btn">
                Search
            </button>
        </form>

        <div class="buttons product-filter-buttons">
            <button type="button" class="buttonE" onclick="loadProducts()">All</button>
            <button type="button" class="buttonE" onclick="loadProducts('cakes')">Cakes</button>
            <button type="button" class="buttonE" onclick="loadProducts('cookies')">Cookies</button>
            <button type="button" class="buttonE" onclick="loadProducts('pastries')">Pastries</button>
            <button type="button" class="buttonE" onclick="loadProducts('bread')">Bread</button>
        </div>

        <div id="bakes-container" class="card-grid"></div>
    </section>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
<script src="js/products.js"></script>

</body>
</html>