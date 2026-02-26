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
            inventory.amount AS stockAmount
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
    <section class="section">
        <h2><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="section-intro">
            <?php if ($search !== ''): ?>
                Showing results for "<strong><?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?></strong>"
                <?php if ($category && isset($categoryNames[$category])): ?>
                    in <?= htmlspecialchars($categoryNames[$category], ENT_QUOTES, 'UTF-8') ?>.
                <?php endif; ?>
            <?php elseif ($heading === 'All bakes'): ?>
                Browse all cakes, cookies, pastries and breads.
            <?php else: ?>
                Showing only <?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?>.
            <?php endif; ?>
        </p>
    </section>

    <div style="display:flex;justify-content:center;margin:1rem 0;">
        <form action="<?= APP_URL ?>/bakes.php" method="get"
              style="display:flex;align-items:center;gap:0.2rem;width:100%;max-width:900px;">

            <?php if ($category && isset($categoryMap[$category])): ?>
                <input type="hidden" name="category"
                       value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>

            <input type="text" name="search" placeholder="Search for a bake…"
                   value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                   style="flex:1;padding:0.75rem 1.2rem;border-radius:999px;border:1px solid var(--border-color);
                          background:var(--card-bg);color:var(--text-color);font-size:1rem;">

            <button type="submit" class="btn primary small"
                    style="padding:0.65rem 1.2rem;font-size:0.9rem;">
                Search
            </button>

            <?php if ($search !== '' || $category): ?>
                <a href="<?= APP_URL ?>/bakes.php"
                   class="btn secondary small"
                   style="padding:0.65rem 1.2rem;font-size:0.9rem;">
                   Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <section class="section section-alt">
        <h3>Browse by category</h3>

        <div class="card-grid categories-grid">
            <a href="<?= APP_URL ?>/bakes.php?category=cakes" class="card category-card">
                <h4>Cakes</h4>
                <p>Celebration cakes, layer cakes, and loaf cakes for every event.</p>
            </a>

            <a href="<?= APP_URL ?>/bakes.php?category=cookies" class="card category-card">
                <h4>Cookies</h4>
                <p>Soft, chewy, or crunchy cookies baked fresh daily.</p>
            </a>

            <a href="<?= APP_URL ?>/bakes.php?category=pastries" class="card category-card">
                <h4>Pastries</h4>
                <p>Buttery croissants, danishes, and puff pastry delights.</p>
            </a>

            <a href="<?= APP_URL ?>/bakes.php?category=bread" class="card category-card">
                <h4>Bread</h4>
                <p>Fresh loaves, rolls, and specialty breads.</p>
            </a>
        </div>
    </section>

    <?php if (!isset($_SESSION['userID'])): ?>
        <section class="section">
            <h3>Log in to order</h3>
            <p>
                To add items to your basket, please
                <a href="<?= APP_URL ?>/loginpage.php">log in</a> or
                <a href="<?= APP_URL ?>/register.php">register</a>.
            </p>
        </section>
    <?php else: ?>
        <section class="section">
            <h3>Welcome, <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?>!</h3>
            <p>Browse our delicious selection of bakes and add your favourites to your basket.</p>
        </section>
    <?php endif; ?>

    <section class="section">
        <?php if (empty($bakes)): ?>
            <p>No bakes found for this search or category.</p>
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

                        <?php if (isset($_SESSION['userID'])): ?>
                            <form action="<?= APP_URL ?>/../basket_add.php" method="post" class="add-to-basket-form">
                                <input type="hidden" name="bakeID" value="<?= (int)$row['bakeID'] ?>">

                                <label>
                                    Qty:
                                    <input
                                        type="number"
                                        name="qty"
                                        value="1"
                                        min="1"
                                        max="<?= (int)$row['stockAmount'] ?>"
                                        class="qty-input"
                                        oninvalid="this.setCustomValidity('The quantity must be equal to or less than the amount in stock')"
                                        oninput="this.setCustomValidity('')"
                                    >
                                </label>

                                <p>Amount in stock: <strong><?= (int)$row['stockAmount'] ?></strong></p>

                                <button type="submit" class="btn small">Add to basket</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>

</body>
</html>
