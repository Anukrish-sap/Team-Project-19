<?php
session_start();
require_once 'dbconnect.php';
include '../components/header_unified.php';

$basket = (isset($_SESSION['basket_items']) && is_array($_SESSION['basket_items']))
    ? $_SESSION['basket_items']
    : [];

$items     = [];
$totalQty  = 0;
$totalCost = 0.0;

if (!empty($basket)) {
    $ids = [];
    foreach ($basket as $key => $bi) {
        $ids[] = (int)$bi['bakeID'];
    }
    $ids = array_values(array_unique($ids));

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT
        b.bakeID,
        b.bakeName,
        b.description,
        b.imageFileName,
        COALESCE(i.amount, 0) AS stockAmount
    FROM bakes b
    LEFT JOIN inventory i ON i.bakeID = b.bakeID
    WHERE b.bakeID IN ($placeholders)";

    $stmt = $db->prepare($sql);
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $byId = [];
    foreach ($rows as $r) {
        $byId[(int)$r['bakeID']] = $r;
    }

    foreach ($basket as $key => $bi) {
        $bakeID = (int)$bi['bakeID'];
        if (!isset($byId[$bakeID])) continue;

        $p         = $byId[$bakeID];
        $qty       = (int)($bi['qty'] ?? 0);
        $size      = (int)($bi['size'] ?? 0);
        $unitPrice = (float)($bi['unitPrice'] ?? 0);

        if ($qty <= 0) continue;

        $line = $unitPrice * $qty;

        $items[] = [
            'key'           => $key,
            'bakeID'        => $bakeID,
            'bakeName'      => $p['bakeName'],
            'description'   => $p['description'],
            'imageFileName' => $p['imageFileName'],
            'stockAmount'   => (int)$p['stockAmount'],
            'qty'           => $qty,
            'size'          => $size,
            'unitPrice'     => $unitPrice,
            'line'          => $line,
        ];

        $totalQty  += $qty;
        $totalCost += $line;
    }
}
?>

<main class="section basket-page">

    <div class="basket-header-row">
        <h2>Basket</h2>
        <?php if (!empty($items)): ?>
            <a href="basket_clear.php" class="btn secondary small">Remove all</a>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>

        <div class="basket-empty">
            <div class="basket-empty-icon">ðŸ›’</div>
            <h3>Your basket is empty</h3>
            <p>Looks like you haven't added anything yet.</p>
            <a href="bakes.php" class="btn primary">Browse products</a>
        </div>

    <?php else: ?>

        <div class="basket-summary-card">
            <div>
                <h3>Summary</h3>
                <p>Items: <strong><?= (int)$totalQty ?></strong></p>
            </div>
            <div class="basket-summary-right">
                <p class="basket-total-label">Total cost:</p>
                <p class="basket-total-amount">Â£<?= number_format($totalCost, 2) ?></p>
                <p class="basket-selected-total" id="selectedSummary" style="display:none;">
                    Selected: <strong id="selectedAmount">Â£0.00</strong>
                </p>
            </div>
        </div>

        <div class="basket-select-all-row">
            <label class="basket-checkbox-label">
                <input type="checkbox" id="selectAll" class="basket-checkbox">
                <span>Select all</span>
            </label>
            <span class="basket-select-hint">Select items to proceed to checkout</span>
        </div>

        <form action="basket_update.php" method="post" class="basket-items" id="basketForm">

            <?php foreach ($items as $item): ?>
                <div class="basket-item-card"
                     data-price="<?= number_format($item['line'], 2, '.', '') ?>"
                     data-key="<?= htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="basket-item-check">
                        <input
                            type="checkbox"
                            name="selected_items[]"
                            value="<?= htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8') ?>"
                            class="basket-checkbox item-checkbox"
                            form="checkoutForm">
                    </div>

                    <div class="basket-item-left">
                        <?php if (!empty($item['imageFileName'])): ?>
                            <img
                                src="img/uploads/<?= htmlspecialchars($item['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($item['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                                class="basket-img">
                        <?php else: ?>
                            <div class="basket-img placeholder-image">Bake</div>
                        <?php endif; ?>
                    </div>

                    <div class="basket-item-middle">
                        <h4><?= htmlspecialchars($item['bakeName'], ENT_QUOTES, 'UTF-8') ?></h4>
                        <p class="basket-item-price">
                            Â£<?= number_format((float)$item['unitPrice'], 2) ?>
                            <?php if ((int)$item['size'] > 0): ?>
                                <span class="muted">(<?= (int)$item['size'] ?>")</span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($item['description'])): ?>
                            <p class="basket-item-desc"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="basket-item-right">
                        <button type="submit" name="remove_single"
                                value="<?= htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8') ?>"
                                class="btn secondary small">Remove</button>

                        <div class="basket-qty-row">
                            <label class="basket-qty-label">
                                Qty:
                                <input
                                    class="qty-input"
                                    type="number"
                                    name="qty[<?= htmlspecialchars($item['key'], ENT_QUOTES, 'UTF-8') ?>]"
                                    value="<?= (int)$item['qty'] ?>"
                                    min="0"
                                    max="<?= (int)$item['stockAmount'] ?>"
                                    required>
                            </label>
                        </div>

                        <p class="basket-line-total">
                            Line total: <strong>Â£<?= number_format((float)$item['line'], 2) ?></strong>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="basket-footer-actions">
                <button type="submit" class="btn primary">Update basket</button>
                <a href="bakes.php" class="btn secondary">Continue shopping</a>
                <a href="basket_clear.php" class="btn secondary">Cancel order</a>
            </div>

        </form>

        <form action="checkout.php" method="post" id="checkoutForm"></form>

        <div class="basket-checkout-btn-wrap">
            <button type="submit" form="checkoutForm" class="btn primary basket-checkout-btn" id="checkoutBtn" disabled>
                Proceed to checkout (<span id="checkoutCount">0</span> item<span id="checkoutPlural">s</span>)
            </button>
        </div>

    <?php endif; ?>
</main>

<script>
const checkboxes      = document.querySelectorAll('.item-checkbox');
const selectAll       = document.getElementById('selectAll');
const checkoutBtn     = document.getElementById('checkoutBtn');
const countSpan       = document.getElementById('checkoutCount');
const pluralSpan      = document.getElementById('checkoutPlural');
const selectedSummary = document.getElementById('selectedSummary');
const selectedAmount  = document.getElementById('selectedAmount');

function updateCheckout() {
    let count = 0;
    let total = 0;

    checkboxes.forEach(cb => {
        const card = cb.closest('.basket-item-card');
        if (cb.checked) {
            count++;
            total += parseFloat(card.dataset.price || 0);
            card.classList.add('is-selected');
        } else {
            card.classList.remove('is-selected');
        }
    });

    countSpan.textContent  = count;
    pluralSpan.textContent = count === 1 ? '' : 's';
    checkoutBtn.disabled   = count === 0;

    if (count > 0) {
        selectedSummary.style.display = 'block';
        selectedAmount.textContent    = 'Â£' + total.toFixed(2);
    } else {
        selectedSummary.style.display = 'none';
    }

    selectAll.checked       = count === checkboxes.length && count > 0;
    selectAll.indeterminate = count > 0 && count < checkboxes.length;
}

if (checkboxes.length > 0) {
    checkboxes.forEach(cb => cb.addEventListener('change', updateCheckout));
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateCheckout();
        });
    }
    updateCheckout();
}
</script>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
</body>
</html>