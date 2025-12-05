<?php
session_start();
require_once 'dbconnect.php';

// logout message (if any)
if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}

// choose header depending on login state
if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
} else {
    include '../components/header.php';
}

try {
    // Optional filters from query string
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $search   = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Map category slug -> bakeTypeID in DB
    $categoryMap = [
        'cakes'    => 1,
        'cookies'  => 2,
        'pastries' => 3,
        'bread'    => 4
    ];

    // Pretty names for the heading
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
            bakes.imageFileName
        FROM bakes
        WHERE 1 = 1
    ";

    // Filter by category if present
    if ($category && isset($categoryMap[$category])) {
        $sql .= " AND bakes.bakeTypeID = :bakeTypeID";
    }

    // Filter by search term if provided
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

    // Page heading
    $heading = 'All bakes';
    if ($search !== '') {
        $heading = 'Search results';
    } elseif ($category && isset($categoryNames[$category])) {
        $heading = $categoryNames[$category];
    }

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
    <!-- from /bake/ to /bake/css -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="light">

<main>
    <!-- Heading -->
    <section class="section">
        <h2><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="section-intro">
            <?php if ($search !== ''): ?>
                Showing results for
                "<strong><?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?></strong>"
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

    <!-- centred search bar -->
    <div style="display:flex;justify-content:center;margin:1rem 0;">
        <form action="bakes.php" method="get"
              style="
                  display:flex;
                  align-items:center;
                  gap:0.2rem;
                  width:100%;
                  max-width:900px;
              ">

            <?php if ($category && isset($categoryMap[$category])): ?>
                <input type="hidden" name="category"
                       value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>

            <input
                type="text"
                name="search"
                placeholder="Search for a bake…"
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                style="
                    flex:1;
                    padding:0.75rem 1.2rem;
                    border-radius:999px;
                    border:1px solid var(--border-color);
                    background:var(--card-bg);
                    color:var(--text-color);
                    font-size:1rem;
                "
            >

            <button type="submit" class="btn primary small"
                    style="padding:0.65rem 1.2rem;font-size:0.9rem;">
                Search
            </button>

            <?php if ($search !== '' || $category): ?>
                <a href="bakes.php"
                   class="btn secondary small"
                   style="padding:0.65rem 1.2rem;font-size:0.9rem;">
                   Clear
                </a>
            <?php endif; ?>

        </form>
    </div>

    <!-- Browse by category  -->
    <section class="section section-alt">
        <h3>Browse by category</h3>

        <div class="card-grid categories-grid">
            <a href="bakes.php?category=cakes" class="card category-card">
                <h4>Cakes</h4>
                <p>Celebration cakes, layer cakes, and loaf cakes for every event.</p>
            </a>

            <a href="bakes.php?category=cookies" class="card category-card">
                <h4>Cookies</h4>
                <p>Soft, chewy, or crunchy cookies baked fresh daily.</p>
            </a>

            <a href="bakes.php?category=pastries" class="card category-card">
                <h4>Pastries</h4>
                <p>Buttery croissants, danishes, and puff pastry delights.</p>
            </a>

            <a href="bakes.php?category=bread" class="card category-card">
                <h4>Bread</h4>
                <p>Fresh loaves, rolls, and specialty breads.</p>
            </a>
        </div>
    </section>

       <!-- Products grid -->
    <section class="section">
        <?php if (empty($bakes)): ?>
            <p>No bakes found for this search or category.</p>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($bakes as $row): ?>
                    <article class="card product-card">
                        <?php if (!empty($row['imageFileName'])): ?>
                            <img
                                src="img/uploads/<?= htmlspecialchars($row['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
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
            </div>
        <?php endif; ?>
    </section>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>

</body>
</html>
