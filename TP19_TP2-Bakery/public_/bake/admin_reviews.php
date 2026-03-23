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

if (!$admin || (int)$admin['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}

// ===== Handle POST updates =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewID'])) {
    $reviewID = (int)$_POST['reviewID'];

    if (isset($_POST['answered'])) {
        $stmt = $db->prepare("UPDATE reviews SET answered = 1 WHERE reviewID = :reviewID");
        $stmt->bindParam(':reviewID', $reviewID, PDO::PARAM_INT);
        $stmt->execute();
    } elseif (isset($_POST['admin_note'])) {
        $admin_note = $_POST['admin_note'];
        $stmt = $db->prepare("UPDATE reviews SET admin_note = :admin_note WHERE reviewID = :reviewID");
        $stmt->bindParam(':admin_note', $admin_note, PDO::PARAM_STR);
        $stmt->bindParam(':reviewID', $reviewID, PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: admin_reviews.php"); // reload page after update
    exit();
}

// ===== Fetch reviews =====
$stmt = $db->query("
    SELECT reviewID, fullname, emailaddress, subject, message, created_at, answered, admin_note
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
                        <th>Created At</th>
                        <th>Answered ✅</th>
                        <th>Admin Note</th>
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
                                <td><?= !empty($row['created_at']) ? date("d/m/Y H:i", strtotime($row['created_at'])) : "No date"; ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="reviewID" value="<?= $row['reviewID'] ?>">
                                        <input type="checkbox" name="answered" value="1" <?= $row['answered'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="reviewID" value="<?= $row['reviewID'] ?>">
                                        <input type="text" name="admin_note" value="<?= htmlspecialchars($row['admin_note']) ?>" onblur="this.form.submit()" placeholder="Add note">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No reviews found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>