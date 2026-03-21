<?php
session_start();

include "dbconnect.php";

// Reward points system
if (isset($_SESSION['userID']) && !isset($_SESSION['points_added'])) {

    $_SESSION['points_added'] = true;

    $userID = $_SESSION['userID'];

//calculation of total from basket
$total = 0;

    if (isset($_SESSION['basket']) && is_array($_SESSION['basket'])) {
        foreach ($_SESSION['basket'] as $item) {

            //check for safety if doesnt work
            $price = isset($item['price']) ? $item['price'] : 0;
            $quantity = isset($item['quantity']) ? $item['quantity'] : 0;

            $total += ($price * $quantity);
        }
    }
    
//1 point for every pound spent
    $rewardPoints = floor($total);
//databaase
    $stmt = $db->prepare("UPDATE users 
                          SET points = points + :points 
                          WHERE userID = :userID");

    $stmt->bindParam(':points', $rewardPoints);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
}

// Clear the basket if it still exists
if (isset($_SESSION['basket'])) {
    unset($_SESSION['basket']);
}

// Load the correct header
if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
} else {
    include '../components/header.php';
}
?>

<main class="section" style="text-align:center; padding:2rem 1rem;">

    <h3 style="margin-bottom:1rem;">Payment Successful</h3>

    <p style="margin-bottom:1rem;">
        Thank you for your order!
    </p>

    <p style="margin-bottom:1rem;">
        You earned <strong><?php echo isset($rewardPoints) ? $rewardPoints : 0; ?> points</strong>.
    </p>


    <p style="font-size:1.1rem; margin-bottom:2rem;">
        <a href="home.php" class="btn primary">Click here to return to the home page</a>
    </p>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
