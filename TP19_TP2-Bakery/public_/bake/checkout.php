<?php
session_start();
require_once "dbconnect.php";

if (empty($_SESSION['basket_items'])) {
    header("Location: basket.php");
    exit();
}

// On payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cardnumber'])) {

    $userID       = $_SESSION['userID'];
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

           $unitPrice = (float)($item['unitPrice'] ?? 0);
$stmt = $db->prepare("INSERT INTO purchaseItems (purchaseID, bakeID, quantity, unitPrice) VALUES (?, ?, ?, ?)");
$stmt->execute([$purchaseID, $bakeID, $quantity, $unitPrice]);

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
        $item = $_SESSION['basket_items'][$key];
        $line = (float)$item['unitPrice'] * (int)$item['qty'];
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

$deliveryFee = $checkoutTotal < 25.00 ? 4.99 : 0.00;
$grandTotal  = $checkoutTotal + $deliveryFee;

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

    <div class="checkout-split-wrapper">

        <!-- ===== LEFT: Payment & Delivery ===== -->
        <div class="checkout-left">

            <form method="post" action="checkout.php" class="checkout-form" id="paymentForm">

                <!-- Delivery or Pickup -->
                <div class="checkout-section-block">
                    <h3 class="checkout-block-title">Fulfilment Method</h3>
                    <div class="checkout-fulfilment-row">

                        <label class="checkout-fulfilment-option">
                            <input type="radio" name="fulfilment" value="delivery" checked class="checkout-radio">
                            <div class="checkout-fulfilment-card" id="cardDelivery">
                                <img src="<?= APP_URL ?>/img/delivery-icon.png" alt="Delivery">
                                <div>
                                    <strong>Delivery</strong>
                                    <span><?= $deliveryFee > 0 ? '£' . number_format($deliveryFee, 2) : 'Free' ?></span>
                                </div>
                            </div>
                        </label>

                        <label class="checkout-fulfilment-option">
                            <input type="radio" name="fulfilment" value="pickup" class="checkout-radio">
                            <div class="checkout-fulfilment-card" id="cardPickup">
                                <img src="<?= APP_URL ?>/img/pickup-icon.png" alt="Pick Up">
                                <div>
                                    <strong>Pick Up</strong>
                                    <span>Free — collect in store</span>
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

                <!-- Delivery address -->
                <div class="checkout-section-block" id="deliveryFields">
                    <h3 class="checkout-block-title">Delivery Address</h3>
                    <div class="checkout-field">
                        <label for="BAdd">Billing / Delivery Address</label>
                        <input type="text" id="BAdd" name="BAdd" placeholder="123 Baker Street" required>
                    </div>
                    <div class="checkout-field-row">
                        <div class="checkout-field">
                            <label for="City">City</label>
                            <input type="text" id="City" name="City" placeholder="City" required>
                        </div>
                        <div class="checkout-field">
                            <label for="postcode">Postcode</label>
                            <input type="text" id="postcode" name="postcode" placeholder="AB12 3CD" required>
                        </div>
                    </div>
                    <div class="checkout-field">
                        <label for="Country">Country</label>
                        <input type="text" id="Country" name="Country" placeholder="United Kingdom" required>
                    </div>
                </div>

                <!-- Pickup info -->
                <div class="checkout-section-block hidden" id="pickupInfo">
                    <h3 class="checkout-block-title">Pick Up Details</h3>
                    <div class="checkout-pickup-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <strong>Bakes &amp; Cakes Store</strong>
                            <p>Aston University, Birmingham, B4 7ET</p>
                            <p class="muted">Mon–Fri: 9am–5pm &nbsp;|&nbsp; Sat: 10am–3pm</p>
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="checkout-section-block">
                    <h3 class="checkout-block-title">Contact Details</h3>
                    <div class="checkout-field">
                        <label for="Name">Full Name</label>
                        <input type="text" id="Name" name="Name" placeholder="Full Name" pattern="[a-zA-Z ]{1,50}" required>
                    </div>
                    <div class="checkout-field">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="07700000000" pattern="\d{11}" maxlength="11" required>
                    </div>
                </div>

                <!-- Payment -->
                <div class="checkout-section-block">
                    <h3 class="checkout-block-title">Payment Details</h3>

                    <div class="checkout-field">
                        <label for="cardnumber">Card Number</label>
                        <input type="text" id="cardnumber" name="cardnumber"
                            placeholder="1234 5678 1234 5678"
                            pattern="[0-9]{16}" maxlength="16" required>
                    </div>

                    <div class="checkout-field-row">
                        <div class="checkout-field">
                            <label for="expiry">Expiration (MM/YY)</label>
                            <input type="text" id="expiry" name="expiry"
                                placeholder="MM/YY"
                                pattern="(0[1-9]|1[0-2])\/([0-9]{2})"
                                maxlength="5" required>
                        </div>
                        <div class="checkout-field">
                            <label for="cvv">Security Code (CVV)</label>
                            <input type="text" id="cvv" name="cvv"
                                placeholder="3–4 digits"
                                pattern="[0-9]{3,4}" maxlength="4" required>
                        </div>
                    </div>

                    <div class="checkout-field">
                        <label for="cardname">Name on Card</label>
                        <input type="text" id="cardname" name="cardname"
                            placeholder="As it appears on your card"
                            pattern="[a-zA-Z ]{1,50}" required>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="checkout-pay-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Complete Payment — £<?= number_format($grandTotal, 2) ?>
                </button>

            </form>

            <div class="checkout-back-link">
                <a href="basket.php" class="btn secondary small">← Back to basket</a>
            </div>

        </div>

        <!-- ===== RIGHT: Order Summary ===== -->
        <div class="checkout-right">
            <div class="checkout-order-summary">
                <h3 class="checkout-summary-title">Order Summary</h3>

                <div class="checkout-summary-items">
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
                                    <div class="checkout-summary-qty">
                                        Qty: <?= (int)$si['qty'] ?> × £<?= number_format((float)$si['unitPrice'], 2) ?>
                                    </div>
                                </div>
                            </div>
                            <strong>£<?= number_format((float)$si['line'], 2) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="checkout-summary-subtotals">
                    <div class="checkout-summary-line">
                        <span>Subtotal</span>
                        <span>£<?= number_format($checkoutTotal, 2) ?></span>
                    </div>
                    <div class="checkout-summary-line">
                        <span>
                            Delivery
                            <?php if ($deliveryFee === 0.0): ?>
                                <span class="checkout-free-badge">Free</span>
                            <?php else: ?>
                                <span class="checkout-delivery-note">Orders under £25</span>
                            <?php endif; ?>
                        </span>
                        <span><?= $deliveryFee > 0 ? '£' . number_format($deliveryFee, 2) : '£0.00' ?></span>
                    </div>
                </div>

                <div class="checkout-summary-total">
                    <span>Total</span>
                    <strong>£<?= number_format($grandTotal, 2) ?></strong>
                </div>

                <?php if ($deliveryFee > 0): ?>
                    <p class="checkout-delivery-tip">
                        💡 Add £<?= number_format(25 - $checkoutTotal, 2) ?> more for free delivery.
                    </p>
                <?php endif; ?>
            </div>
        </div>

    </div>

</main>

<script>
const radioDelivery  = document.querySelector('input[value="delivery"]');
const radioPickup    = document.querySelector('input[value="pickup"]');
const deliveryFields = document.getElementById('deliveryFields');
const pickupInfo     = document.getElementById('pickupInfo');
const cardDelivery   = document.getElementById('cardDelivery');
const cardPickup     = document.getElementById('cardPickup');

function updateFulfilment() {
    if (radioDelivery.checked) {
        deliveryFields.classList.remove('hidden');
        pickupInfo.classList.add('hidden');
        cardDelivery.classList.add('is-active');
        cardPickup.classList.remove('is-active');
        deliveryFields.querySelectorAll('input').forEach(i => i.required = true);
    } else {
        deliveryFields.classList.add('hidden');
        pickupInfo.classList.remove('hidden');
        cardPickup.classList.add('is-active');
        cardDelivery.classList.remove('is-active');
        deliveryFields.querySelectorAll('input').forEach(i => i.required = false);
    }
}

radioDelivery.addEventListener('change', updateFulfilment);
radioPickup.addEventListener('change', updateFulfilment);
updateFulfilment();
</script>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>