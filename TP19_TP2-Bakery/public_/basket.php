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

    $sql = "SELECT bakeID, bakeName, description, price, imageFileName, bakeTypeID
            FROM bakes
            WHERE bakeID IN ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as &$item) {
        $id           = (int)$item['bakeID'];
        $item['qty']  = $basket[$id] ?? 0;
        $item['line'] = $item['price'] * $item['qty'];

        $totalQty  += $item['qty'];
        $totalCost += $item['line'];
    }
    unset($item);
}
?>
<?php include ../components/header.php;?>

<main class="section basket-page">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;">
        <h2>Basket</h2>
        <?php if (!empty($items)): ?>
            <a href="basket_clear.php" class="btn secondary small">Remove all</a>
        <?php endif; ?>
    </div>

    <div style="margin:1rem 0;padding:1rem;border-radius:0.9rem;border:1px solid var(--border-color);background:var(--card-bg);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
        <div>
            <h3 style="margin:0 0 0.25rem 0;">Summary</h3>
            <p style="margin:0;">Items: <strong><?= (int)$totalQty ?></strong></p>
        </div>
        <div style="text-align:right;">
            <p style="margin:0 0 0.35rem 0;">
                <strong>Total cost:</strong> £<?= number_format($totalCost, 2) ?>
            </p>
            <button type="button" class="btn primary small">Proceed to checkout</button>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <p>Your basket is empty.</p>
        <a href="bake/bakes.php" class="btn primary">Browse products</a>

    <?php else: ?>

        <form action="basket_update.php" method="post"
              style="display:flex;flex-direction:column;gap:1rem;">

            <?php foreach ($items as $item): ?>
                <div style="display:grid;grid-template-columns:90px 1fr auto;gap:0.75rem;align-items:flex-start;padding:0.9rem 1rem;border-radius:0.9rem;border:1px solid var(--border-color);background:var(--card-bg);">

                    <div>
                        <?php if (!empty($item['imageFileName'])): ?>
                            
                            <img
                                src="bake/img/uploads/<?= htmlspecialchars($item['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($item['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                                style="width:80px;height:80px;object-fit:cover;border-radius:0.6rem;"
                            >
                        <?php else: ?>
                            <div class="placeholder-image"
                                 style="width:80px;height:80px;border-radius:0.6rem;display:flex;align-items:center;justify-content:center;">
                                Bake
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <h4 style="margin:0 0 0.25rem 0;">
                            <?= htmlspecialchars($item['bakeName'], ENT_QUOTES, 'UTF-8') ?>
                        </h4>
                        <p style="margin:0 0 0.25rem 0;">
                            £<?= number_format($item['price'], 2) ?>
                        </p>
                        <?php if (!empty($item['description'])): ?>
                            <p style="margin:0;font-size:0.9rem;">
                                <?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:0.4rem;">
                        <button
                            type="submit"
                            name="remove_single"
                            value="<?= (int)$item['bakeID'] ?>"
                            class="btn secondary small"
                        >Remove</button>

                        <div style="display:flex;align-items:center;gap:0.35rem;">
                            <label style="font-size:0.85rem;">
                                Qty:
                                <input
                                    type="number"
                                    name="qty[<?= (int)$item['bakeID'] ?>]"
                                    value="<?= (int)$item['qty'] ?>"
                                    min="0"
                                    style="width:60px;padding:0.2rem 0.4rem;border-radius:999px;border:1px solid var(--border-color);"
                                >
                            </label>
                        </div>

                        <p style="margin:0;font-size:0.9rem;">
                            Line total:
                            <strong>£<?= number_format($item['line'], 2) ?></strong>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div style="margin-top:0.75rem;display:flex;flex-wrap:wrap;gap:0.5rem;">
                <button type="submit" class="btn primary">Update basket</button>
                <a href="bake/bakes.php" class="btn secondary">Continue shopping</a>
                <a href="basket_clear.php" class="btn secondary">Cancel order</a>
            </div>
        </form>

    <?php endif; ?>
</main>
    <?php include ../components/footer.php;?>

<?php include ../components/script.html;?>



</body>
</html>
