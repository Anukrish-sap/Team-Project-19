<?php
session_start();
require_once 'dbconnect.php';

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

if (!isset($_SESSION['userID'])) {
    echo "<p>You must be logged in to view your purchase history.</p>";
    exit;
}



$userID = $_SESSION['userID'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_again_id'])) {
    $reorderID = (int)$_POST['order_again_id'];

    $itemsSQL = "
        SELECT pi.bakeID, pi.quantity, pi.unitPrice, b.bakeName
        FROM purchaseItems pi
        JOIN bakes b ON pi.bakeID = b.bakeID
        WHERE pi.purchaseID = :pid
    ";
    $itemsQuery = $db->prepare($itemsSQL);
    $itemsQuery->bindValue(':pid', $reorderID, PDO::PARAM_INT);
    $itemsQuery->execute();
    $reorderItems = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);

    if (!isset($_SESSION['basket_items'])) {
        $_SESSION['basket_items'] = [];
    }

    foreach ($reorderItems as $ri) {
        $key = $ri['bakeID'] . ':0';
        if (isset($_SESSION['basket_items'][$key])) {
            $_SESSION['basket_items'][$key]['qty'] += (int)$ri['quantity'];
        } else {
            $_SESSION['basket_items'][$key] = [
                'bakeID'    => (int)$ri['bakeID'],
                'size'      => 0,
                'unitPrice' => $ri['unitPrice'],
                'qty'       => (int)$ri['quantity'],
            ];
        }
    }

    header("Location: basket.php");
    exit;
}
include '../components/header_unified.php';
try {
    $purchaseSQL = "
        SELECT purchaseID, purchaseDate
        FROM purchases
        WHERE userID = :uid
        ORDER BY purchaseDate DESC
    ";
    $purchaseQuery = $db->prepare($purchaseSQL);
    $purchaseQuery->bindValue(':uid', $userID, PDO::PARAM_INT);
    $purchaseQuery->execute();
    $purchases = $purchaseQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Account</span>
            <h1>Purchase History</h1>
            <p>All orders placed under your account, most recent first.</p>
        </div>
    </section>

    <section class="section">
        <div class="purchase-wrapper">

            <?php if (empty($purchases)): ?>
                <div class="purchase-empty">
                    <div class="purchase-empty-icon">ðŸ›?ï¸?</div>
                    <h3>No purchases yet</h3>
                    <p>You haven't placed any orders. Browse our range to get started.</p>
                    <a href="<?= APP_URL ?>/bakes.php" class="btn primary">Browse products</a>
                </div>

            <?php else: ?>

                <?php foreach ($purchases as $purchase):
                    $itemsSQL = "
                        SELECT pi.bakeID, pi.quantity, pi.unitPrice, b.bakeName, b.imageFileName
                        FROM purchaseItems pi
                        JOIN bakes b ON pi.bakeID = b.bakeID
                        WHERE pi.purchaseID = :pid
                    ";
                    $itemsQuery = $db->prepare($itemsSQL);
                    $itemsQuery->bindValue(':pid', $purchase['purchaseID'], PDO::PARAM_INT);
                    $itemsQuery->execute();
                    $items = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);

                    $orderTotal = 0;
                    foreach ($items as $item) {
                        $orderTotal += $item['unitPrice'] * $item['quantity'];
                    }
                ?>

                <div class="purchase-card">

                    <!-- Order header -->
                    <div class="purchase-card-header">
                        <div class="purchase-card-meta">
                            <span class="purchase-order-label">Order #<?= strtoupper(substr(md5($purchase['purchaseID'] . 'bakes19'), 0, 6)) ?></span>
                            <span class="purchase-order-date">
                                <?= date('d M Y, H:i', strtotime($purchase['purchaseDate'])) ?>
                            </span>
                        </div>
                        <div class="purchase-card-total">
                            <span class="purchase-total-label">Order total</span>
                            <span class="purchase-total-amount">Â£<?= number_format($orderTotal, 2) ?></span>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="purchase-items">
                        <?php foreach ($items as $item): ?>
                            <div class="purchase-item-row">
                                <div class="purchase-item-img-wrap">
                                    <?php if (!empty($item['imageFileName'])): ?>
                                        <img
                                            src="<?= APP_URL ?>/img/uploads/<?= htmlspecialchars($item['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($item['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                                            class="purchase-item-img">
                                    <?php else: ?>
                                        <div class="purchase-item-img purchase-item-placeholder">ðŸŽ‚</div>
                                    <?php endif; ?>
                                </div>
                                <div class="purchase-item-info">
                                    <strong><?= htmlspecialchars($item['bakeName'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span class="purchase-item-qty">Qty: <?= (int)$item['quantity'] ?></span>
                                </div>
                                <div class="purchase-item-price">
                                    <span>Â£<?= number_format((float)$item['unitPrice'], 2) ?> each</span>
                                    <strong>Â£<?= number_format($item['unitPrice'] * $item['quantity'], 2) ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order footer -->
                    <div class="purchase-card-footer">
                        <form method="post" action="purchase_history.php">
                            <input type="hidden" name="order_again_id" value="<?= (int)$purchase['purchaseID'] ?>">
                            <button type="submit" class="btn primary small purchase-reorder-btn">
                                 Order Again
                            </button>
                        </form>
                    </div>

                </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>