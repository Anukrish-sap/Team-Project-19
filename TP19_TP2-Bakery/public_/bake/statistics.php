<?php
session_start();
require_once 'dbconnect.php';
include '../components/header_unified.php';

// Fetch most purchased products
$sql = "
    SELECT 
        b.bakeID,
        b.bakeName,
        b.imageFileName,
        SUM(pi.quantity) AS totalQuantity
    FROM purchaseItems pi
    JOIN bakes b ON pi.bakeID = b.bakeID
    GROUP BY b.bakeID, b.bakeName, b.imageFileName
    ORDER BY totalQuantity DESC
";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$maxQty = !empty($results) ? max(array_column($results, 'totalQuantity')) : 1;
?>

<main>

    <section class="stock-hero">
        <div class="stock-hero-inner">
            <span class="stock-hero-label">Admin Panel</span>
            <h1>Sales Statistics</h1>
            <p>A visual breakdown of your best-selling products based on total purchases.</p>
        </div>
    </section>

    <section class="section">

        <?php if (empty($results)): ?>
            <p class="muted">No purchase data available yet.</p>
        <?php else: ?>

            <div class="stats-chart">
                <?php foreach ($results as $i => $row):
                    $percent = ($row['totalQuantity'] / $maxQty) * 100;
                    $rank = $i + 1;
                ?>
                <div class="stats-row">

                    <div class="stats-rank"><?= $rank ?></div>

                    <div class="stats-img-wrap">
                        <?php if (!empty($row['imageFileName'])): ?>
                            <img
                                src="<?= APP_URL ?>/img/uploads/<?= htmlspecialchars($row['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($row['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                                class="stats-img"
                            >
                        <?php else: ?>
                            <div class="stats-img stats-img-placeholder">🎂</div>
                        <?php endif; ?>
                    </div>

                    <div class="stats-info">
                        <div class="stats-name"><?= htmlspecialchars($row['bakeName'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="stats-bar-wrap">
                            <div class="stats-bar" style="width: <?= round($percent) ?>%;"></div>
                        </div>
                    </div>

                    <div class="stats-qty">
                        <span class="stats-qty-number"><?= (int)$row['totalQuantity'] ?></span>
                        <span class="stats-qty-label">sold</span>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </section>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>