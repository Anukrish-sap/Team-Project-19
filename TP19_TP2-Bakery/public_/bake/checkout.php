<?php
session_start();
require_once "dbconnect.php";

if (empty($_SESSION['basket_items'])) {
    header("Location: basket.php");
    exit();
}

// On payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cardnumber'])) {

    $userID      = $_SESSION['userID'];
    $selectedKeys = isset($_SESSION['checkout_selected']) ? $_SESSION['checkout_selected'] : [];
    $itemsToProcess = [];

    if (!empty($selectedKeys)) {
        foreach ($selectedKeys as $key) {
            if (isset($_SESSION['basket_items'][$key])) {
                $itemsToProcess[$key] = $_SESSION['basket_items'][$key];
            }
        }
    } else {
        $itemsToProcess = $_SESSION['basket_items'];
    }

    if (empty($itemsToProcess)) {
        header("Location: basket.php");
        exit();
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO purchases (userID, purchaseDate) VALUES (?, NOW())");
        $stmt->execute([$userID]);
        $purchaseID = $db->lastInsertId();

        foreach ($itemsToProcess as $key => $item) {
            $bakeID   = (int)$item['bakeID'];
            $quantity = (int)$item['qty'];

            $stmt = $db->prepare("INSERT INTO purchaseItems (purchaseID, bakeID, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$purchaseID, $bakeID, $quantity]);

            $stmt = $db->prepare("SELECT amount FROM inventory WHERE bakeID = ?");
            $stmt->execute([$bakeID]);
            $currentStock = $stmt->fetchColumn();

            if ($currentStock === false) continue;

            $newStock = max(0, $currentStock - $quantity);
            $update   = $db->prepare("UPDATE inventory SET amount = ? WHERE bakeID = ?");
            $update->execute([$newStock, $bakeID]);

            unset($_SESSION['basket_items'][$key]);
        }

        unset($_SESSION['checkout_selected']);
        $db->commit();

    } catch (PDOException $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
        exit();
    }

    header("Location: checkout_success.php");
    exit();
}

// Arriving from basket with selected items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $_SESSION['checkout_selected'] = $_POST['selected_items'];
} elseif (!isset($_SESSION['checkout_selected'])) {
    header("Location: basket.php");
    exit();
}

// Build selected items for display
$selectedKeys  = $_SESSION['checkout_selected'];
$selectedItems = [];
$checkoutTotal = 0.0;

foreach ($selectedKeys as $key) {
    if (isset($_SESSION['basket_items'][$key])) {
        $item  = $_SESSION['basket_items'][$key];
        $line  = (float)$item['unitPrice'] * (int)$item['qty'];
        $selectedItems[] = [
            'key'       => $key,
            'bakeID'    => $item['bakeID'],
            'qty'       => $item['qty'],
            'unitPrice' => $item['unitPrice'],
            'size'      => $item['size'] ?? 0,
            'line'      => $line,
        ];
        $checkoutTotal += $line;
    }
}

if (!empty($selectedItems)) {
    $ids          = array_unique(array_column($selectedItems, 'bakeID'));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt         = $db->prepare("SELECT bakeID, bakeName, imageFileName FROM bakes WHERE bakeID IN ($placeholders)");
    $stmt->execute($ids);
    $bakeInfo = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $bakeInfo[(int)$r['bakeID']] = $r;
    }
    foreach ($selectedItems as &$si) {
        $si['bakeName']      = $bakeInfo[$si['bakeID']]['bakeName'] ?? 'Unknown';
        $si['imageFileName'] = $bakeInfo[$si['bakeID']]['imageFileName'] ?? '';
    }
    unset($si);
}

include '../components/header_unified.php';
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Checkout</span>
            <h1>Secure Checkout</h1>
            <p>Review your order and complete payment below.</p>
        </div>
    </section>

    <div class="checkout-wrapper">

        <!-- Order summary -->
        <div class="checkout-order-summary">
            <h3 class="checkout-summary-title">Order Summary</h3>
            <?php foreach ($selectedItems as $si): ?>
                <div class="checkout-summary-row">
                    <div class="checkout-summary-info">
                        <?php if (!empty($si['imageFileName'])): ?>
                            <img
                                src="img/uploads/<?= htmlspecialchars($si['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($si['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                                class="checkout-summary-img">
                        <?php endif; ?>
                        <div>
                            <strong><?= htmlspecialchars($si['bakeName'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if ((int)$si['size'] > 0): ?>
                                <span class="muted"> — <?= (int)$si['size'] ?>"</span>
                            <?php endif; ?>
                            <div class="muted" style="font-size:0.85rem;">
                                Qty: <?= (int)$si['qty'] ?> × £<?= number_format((float)$si['unitPrice'], 2) ?>
                            </div>
                        </div>
                    </div>
                    <strong>£<?= number_format((float)$si['line'], 2) ?></strong>
                </div>
            <?php endforeach; ?>
            <div class="checkout-summary-total">
                <span>Total</span>
                <strong>£<?= number_format($checkoutTotal, 2) ?></strong>
            </div>
        </div>

        <!-- Payment form -->
        <form method="post" action="checkout.php" class="checkout-form">
            <div class="form-group">
                <label for="cardnumber">Card Number</label>
                <input type="text" id="cardnumber" name="cardnumber"
                    placeholder="1234567812345678"
                    pattern="[0-9]{16}" maxlength="16" required>
            </div>
            <div class="form-group">
                <label for="Name">Full Name</label>
                <input type="text" id="Name" name="Name"
                    placeholder="Full Name"
                    pattern="[a-zA-Z ]{1,50}" required>
            </div>
            <div class="form-group">
                <label for="BAdd">Billing Address</label>
                <input type="text" id="BAdd" name="BAdd"
                    placeholder="Billing Address" required>
            </div>
            <div class="form-group">
                <label for="Country">Country</label>
                <input type="text" id="Country" name="Country"
                    placeholder="Country" required>
            </div>
            <div class="form-group">
                <label for="City">City</label>
                <input type="text" id="City" name="City"
                    placeholder="City" required>
            </div>
            <div class="form-group">
                <label for="postcode">Postcode</label>
                <input type="text" id="postcode" name="postcode"
                    placeholder="Postcode" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone"
                    placeholder="Phone Number"
                    pattern="\d{11}" maxlength="11" required>
            </div>
            <div class="form-group">
                <button type="submit" class="checkout-btn">
                    Complete Payment — £<?= number_format($checkoutTotal, 2) ?>
                </button>
            </div>
        </form>

        <div class="checkout-back-link">
            <a href="basket.php" class="btn secondary small">← Back to basket</a>
        </div>

    </div>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>