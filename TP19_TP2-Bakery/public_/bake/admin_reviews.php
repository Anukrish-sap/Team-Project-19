<?php
session_start();
include "dbconnect.php";


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

// If not admin, redirect
if (!$admin || (int)$admin['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}

$stmt = $db->query("
SELECT fullname, emailaddress, subject, message, created_at 
FROM reviews 
ORDER BY reviewID DESC
");

$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../components/header_unified.php';

?>
<link rel="stylesheet" href="<?= APP_URL ?>/css/styles.css">

<main>
    <section class="hero">
        <div class="hero-content">
            <div class="contact-container">
                <h1>Admin - Reviews</h1>
            </div>
        </div>
    </section>

    <div class="contact-wrapper">
    <div class="table-responsive">
        <table class="reviews-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Subject</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reviews): ?>
                    <?php foreach ($reviews as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['emailaddress']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars($row['message']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No reviews found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>