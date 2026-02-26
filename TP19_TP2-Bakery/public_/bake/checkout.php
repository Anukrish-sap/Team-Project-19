<?php
session_start();
require_once "dbconnect.php";

if (empty($_SESSION['basket'])) {
    header("Location: basket.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cardnumber'])) {

    try {
        $db->beginTransaction();

        foreach ($_SESSION['basket'] as $bakeID => $quantity) {

            $stmt = $db->prepare("SELECT amount FROM inventory WHERE bakeID = ?");
            $stmt->execute([$bakeID]);
            $currentstock = $stmt->fetchColumn();

            if ($currentstock === false) continue;

            $newStock = max(0, $currentstock - $quantity);

            $update = $db->prepare("UPDATE inventory SET amount = ? WHERE bakeID = ?");
            $update->execute([$newStock, $bakeID]);
        }

        $db->commit();

    } catch (PDOException $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
        exit();
    }

    unset($_SESSION['basket']);
    header("Location: checkout_success.php");
    exit();
}

include '../components/header_unified.php';
?>
<link rel="stylesheet" href="css/styles.css">
<main>

<section class="hero">
    <div class="hero-content">
        <h1>Secure Checkout</h1>
        <p>Please enter your payment details below.</p>
    </div>
</section>

<div class="checkout-wrapper">

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
                placeholder="Billing Address"
                required>
        </div>

        <div class="form-group">
            <label for="Country">Country</label>
            <input type="text" id="Country" name="Country"
                placeholder="Country"
                required>
        </div>

        <div class="form-group">
            <label for="City">City</label>
            <input type="text" id="City" name="City"
                placeholder="City"
                required>
        </div>

        <div class="form-group">
            <label for="postcode">Postcode</label>
            <input type="text" id="postcode" name="postcode"
                placeholder="Postcode"
                required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone"
                placeholder="Phone Number"
                pattern="\d{11}" maxlength="11" required>
        </div>

        <div class="form-group">
            <button type="submit" class="checkout-btn">Complete Payment</button>
        </div>

    </form>

</div>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>