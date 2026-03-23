<?php
session_start();
include "dbconnect.php";

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

$userID = $_SESSION['userID'];

// Check admin status
$stmt = $db->prepare("
    SELECT adminStatus 
    FROM adminStatus 
    WHERE userID = :userID
");
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if not admin
if (!$admin || (int)$admin['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}

include '../components/header_unified.php';
?>

<main>
    <section class="hero">
        <div class="hero-content">
            <div class="contact-container">
                <h1>Admin Dashboard</h1>
                <p>Welcome, <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>! Use the tools below to manage the site.</p>
            </div>
        </div>
    </section>

    <div class="dashboard-wrapper" style="display:flex; flex-wrap:wrap; gap:2rem; justify-content:center; padding:2rem 0;">

        <!-- Card for Reviews -->
        <a href="admin_reviews.php" class="dashboard-card" style="flex:1; min-width:220px; max-width:250px; background:#f4f4f4; padding:2rem; border-radius:1rem; text-align:center; text-decoration:none; color:#333; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
            <h2>Reviews</h2>
            <p>View and manage user reviews.</p>
        </a>

        <!-- Card for Stock -->
        <a href="stock.php" class="dashboard-card" style="flex:1; min-width:220px; max-width:250px; background:#f4f4f4; padding:2rem; border-radius:1rem; text-align:center; text-decoration:none; color:#333; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
            <h2>Inventory</h2>
            <p>Manage bakery products and inventory.</p>
        </a>

        <!-- Card for Admin Account Update -->
        <a href="adminAccUpdate.php" class="dashboard-card" style="flex:1; min-width:220px; max-width:250px; background:#f4f4f4; padding:2rem; border-radius:1rem; text-align:center; text-decoration:none; color:#333; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
            <h2>Account management </h2>
            <p>Update admin account details.</p>
        </a>

        <!-- Card for Statistics -->
        <a href="statistics.php" class="dashboard-card" style="flex:1; min-width:220px; max-width:250px; background:#f4f4f4; padding:2rem; border-radius:1rem; text-align:center; text-decoration:none; color:#333; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
            <h2>Statistics</h2>
            <p>View site performance and sales data.</p>
        </a>

    </div>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>