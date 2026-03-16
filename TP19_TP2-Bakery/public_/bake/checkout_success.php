<?php
session_start();

include "dbconnect.php";

// Reward points system
if (isset($_SESSION['userID'])) {

    $userID = $_SESSION['userID'];
    $rewardPoints = 10;

    $stmt = $db->prepare("UPDATE users SET points = points + :points WHERE userID = :userID");
    $stmt->bindParam(':points', $rewardPoints);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
}

// Clear the basket if it still exists
unset($_SESSION['basket']);

// Load the correct header
if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
} else {
    include '../components/header.php';
}
?>

<main class="section" style="text-align:center; padding:2rem 1rem;">

    <h3 style="margin-bottom:1rem;">Payment Successful</h3>

    <p style="font-size:1.1rem; margin-bottom:2rem;">
        <a href="home.php" class="btn primary">Click here to return to the home page</a>
    </p>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
