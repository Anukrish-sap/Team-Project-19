<?php
session_start();
require_once "dbconnect.php";

// If basket is empty, redirect back
if (empty($_SESSION['basket'])) {
    header("Location: basket.php");
    exit();
}


// PROCESS CHECKOUT (POST REQUEST)

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

// -------------------------------
// SHOW PAYMENT FORM (GET REQUEST)
// -------------------------------

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}

include '../components/header_unified.php';
?>

<main>
    <section class="hero">
        <div class="hero-content">
            <form id="paymentForm" method="post" action="checkout.php">
                <h1>Please enter your details for payment:</h1>

                <h3>Card Number:</h3>
                <input type="text" id="cardnumber" name="cardnumber" placeholder="Card Number" pattern="[0-9]{16}" required>
                <br><br>

                <h3>Full Name:</h3>
                <input type="text" id="Name" name="Name" placeholder="Full Name" pattern="[a-zA-Z ]{1,50}" required>
                <br><br>

                <h3>Billing Address:</h3>
                <input type="text" id="BAdd" name="BAdd" placeholder="Billing Address" pattern="[a-zA-Z0-9_ ]{1,50}" required>
                <<br><br>

                <h3>Country:</h3>
                <input type="text" id="Country" name="Country" placeholder="Country" pattern="[a-zA-Z ]{1,50}" required>
                <br><br>

                <h3>City:</h3>
                <input type="text" id="City" name="City" placeholder="City" pattern="[a-zA-Z ]{1,50}" required>
                <br><br>

                <h3>Postcode:</h3>
                <input type="text" id="postcode" name="postcode" placeholder="Postcode" pattern="[A-Za-z0-9\s]{3,10}" required>
                <br><br>

                <h3>Phone Number:</h3>
                <input type="tel" id="phone" name="phone" placeholder="Phone Number" pattern="\d{11}" maxlength="11" required>
                <br><br><br>

                <button type="submit" class="btn">Submit</button>
            </form>
        </div>
    </section>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>

</body>
</html>