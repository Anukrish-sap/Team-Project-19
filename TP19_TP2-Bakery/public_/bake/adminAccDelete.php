<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "dbconnect.php";

/* Ensure admin is logged in */
if (!isset($_SESSION['userID'])) {
    $_SESSION['error'] = "You must be logged in.";
    header("Location: login.php");
    exit();
}

/* Ensure logged-in user is an admin */
$adminCheck = $db->prepare("
    SELECT adminStatus 
    FROM adminStatus 
    WHERE userID = :uid
");
$adminCheck->bindParam(':uid', $_SESSION['userID'], PDO::PARAM_INT);
$adminCheck->execute();
$adminRow = $adminCheck->fetch(PDO::FETCH_ASSOC);

if (!$adminRow || (int)$adminRow['adminStatus'] !== 1) {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: home.php");
    exit();
}

/* Ensure admin selected a user */
if (!isset($_SESSION['accDetAdm'])) {
    $_SESSION['error'] = "No account selected.";
    header("Location: adminAccUpdate.php");
    exit();
}

$targetUserID = $_SESSION['accDetAdm'];

/* Fetch target user details */
$stmt = $db->prepare("SELECT email, name FROM users WHERE userID = :uid");
$stmt->bindParam(':uid', $targetUserID, PDO::PARAM_INT);
$stmt->execute();
$targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$targetUser) {
    $_SESSION['error'] = "User not found.";
    header("Location: adminAccUpdate.php");
    exit();
}

/* Handle form submission */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // If admin clicked "No"
    if (isset($_POST['cancel'])) {
        header("Location: admin_account_edit.php");
        exit();
    }

    $adminPassword = $_POST['password'];

    // Fetch admin's own password
    $adminStmt = $db->prepare("SELECT password FROM users WHERE userID = :uid");
    $adminStmt->bindParam(':uid', $_SESSION['userID'], PDO::PARAM_INT);
    $adminStmt->execute();
    $adminData = $adminStmt->fetch(PDO::FETCH_ASSOC);

    if (!$adminData || !password_verify($adminPassword, $adminData['password'])) {
        $_SESSION['error'] = "Incorrect admin password.";
        header("Location: adminAccDelete.php");
        exit();
    }

    try {
        $db->beginTransaction();

        /* Delete related data */
        $del1 = $db->prepare("DELETE FROM purchases WHERE userID = :uid");
        $del1->bindParam(':uid', $targetUserID);
        $del1->execute();

        $del2 = $db->prepare("DELETE FROM bakeReviews WHERE userID = :uid");
        $del2->bindParam(':uid', $targetUserID);
        $del2->execute();

        $del3 = $db->prepare("DELETE FROM adminStatus WHERE userID = :uid");
        $del3->bindParam(':uid', $targetUserID);
        $del3->execute();

        /* Delete user account */
        $del4 = $db->prepare("DELETE FROM users WHERE userID = :uid");
        $del4->bindParam(':uid', $targetUserID);
        $del4->execute();

        $db->commit();

        unset($_SESSION['accDetAdm']);

        $_SESSION['success'] = "User account deleted successfully.";
        header("Location: adminAccUpdate.php");
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: adminAccDelete.php");
        exit();
    }
}

include '../components/header_unified.php';
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Admin Panel</span>
            <h1>Delete User Account</h1>
            <p>You are about to permanently delete <?= htmlspecialchars($targetUser['name']) ?>’s account.</p>
        </div>
    </section>

    <section class="section">
        <div class="namechange-wrapper">

            <?php if (isset($_SESSION['error'])): ?>
                <div class="namechange-alert namechange-alert-error">
                    ✖ <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="namechange-card">
                <div class="namechange-card-header">
                    <div class="namechange-card-icon danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Confirm Deletion</h2>
                        <p>You must confirm your identity to proceed.</p>
                    </div>
                </div>

                <form action="adminAccDelete.php" method="POST" class="namechange-form">

                    <div class="namechange-field">
                        <label for="password">Re-enter Your Admin Password</label>
                        <input type="password" id="password" name="password"
                               placeholder="Enter your admin password" required>
                    </div>

                    <div class="namechange-field">
                        <p><strong>Are you sure you want to delete this user’s account?</strong></p>
                        <p style="font-size: 0.9rem; color: #555;">
                            All purchase history, account details, and associated data for this user will be permanently removed.
                        </p>
                        <p style="color: red; font-weight: bold; margin-top: 0.5rem;">
                            WARNING: THIS ACTION CANNOT BE UNDONE
                        </p>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" name="confirm" class="btn primary namechange-btn" style="background: red;">
                            Yes, Delete This Account
                        </button>

                        <button type="submit" name="cancel" class="btn namechange-btn">
                            No, Cancel
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </section>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
</body>
</html>