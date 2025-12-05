<?php
session_start();
include "dbconnect.php";

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}
if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
} else {
    include '../components/header.php';
}

try {
    $category = isset($_GET['category']) ? htmlspecialchars($_GET['category'], ENT_QUOTES, 'UTF-8') : null;

    $categoryMap = [
        'cakes' => 1,
        'cookies' => 2,
        'pastries' => 3,
        'bread' => 4
    ];

    $sql = "select bakes.bakeID, bakes.bakeName, bakes.description, bakes.price, bakes.bakeTypeID, bakes.imageFileName from bakes where 1=1";

    if ($category && isset($categoryMap[$category])) {
        $sql .= " AND bakes.bakeTypeID = :bakeTypeID";
    }

    $query = $db->prepare($sql);

    if ($category && isset($categoryMap[$category])) {
        $query->bindValue(':bakeTypeID', $categoryMap[$category], PDO::PARAM_INT);
    }
    $query->execute();
    $bakes = $query->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>


<main>

    <section class="hero">
        <div class="hero-content">
            <form id="paymentForm">
                <h1>Please enter your details for payment:</h1>

                <h3>Card Number:</h3>
                <input type="text" id="cardnumber" name="cardnumber" placeholder="Card Number" pattern="[0-9]{16}" required>
                <br><br>

                <h3>Full Name:</h3>
                <input type="text" id="Name" name="Name" placeholder="Full Name" pattern="[a-zA-Z]{1,50}" required>
                <br><br>

                <h3>Billing Address:</h3>
                <input type="text" id="BAdd" name="BAdd" placeholder="Billing Address" pattern="[a-zA-Z0-9_]{1,50}" required>
                <br><br>

                <h3>Country:</h3>
                <input type="text" id="Country" name="Country" placeholder="Country" pattern="[a-zA-Z]{1,50}" required>
                <br><br>

                <h3>City:</h3>
                <input type="text" id="City" name="City" placeholder="City" pattern="[a-zA-Z]{1,50}" required>
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

    <script>
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (this.checkValidity()) {
            this.reset();
            location.reload();
        }
    });
    </script>
</main>

<?php include '../components/footer.php'; ?>

<?php include '../components/script.html'; ?>

</body>
</html>