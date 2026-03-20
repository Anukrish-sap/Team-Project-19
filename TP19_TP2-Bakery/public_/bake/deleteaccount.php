<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "dbconnect.php";

/* 1. User must be logged in */
if (!isset($_SESSION['userID'])) {
    $_SESSION['error'] = "You must be logged in.";
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

/* 2. Handle form submission */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // If user clicked "No"
    if (isset($_POST['cancel'])) {
        header("Location: accdetails.php");
        exit();
    }

    $password = $_POST['password'];

    // Fetch user password
    $stmt = $db->prepare("SELECT password FROM users WHERE userID = :uid");
    $stmt->bindParam(':uid', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Incorrect password.";
        header("Location: deleteaccount.php");
        exit();
    }

    try {
        $db->beginTransaction();

        /* Delete related data */
        $del1 = $db->prepare("DELETE FROM purchases WHERE userID = :uid");
        $del1->bindParam(':uid', $userID);
        $del1->execute();

        $del2 = $db->prepare("DELETE FROM bakeReviews WHERE userID = :uid");
        $del2->bindParam(':uid', $userID);
        $del2->execute();

        $del3 = $db->prepare("DELETE FROM adminStatus WHERE userID = :uid");
        $del3->bindParam(':uid', $userID);
        $del3->execute();

        /* Delete user account */
        $del4 = $db->prepare("DELETE FROM users WHERE userID = :uid");
        $del4->bindParam(':uid', $userID);
        $del4->execute();

        $db->commit();

        /* Destroy session */
        session_unset();
        session_destroy();

        header("Location: /index.php");
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: deleteaccount.php");
        exit();
    }
}

include '../components/header_unified.php';
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Account Settings</span>
            <h1>Delete Your Account</h1>
            <p>This will permanently remove your account and all associated data.</p>
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
                    <div class="namechange-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v2m0 4h.01M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Confirm Account Deletion</h2>
                        <p>Please confirm your identity and decision.</p>
                    </div>
                </div>

                <form action="deleteaccount.php" method="POST" class="namechange-form">

                    <div class="namechange-field">
                        <label for="password">Re-enter Password</label>
                        <input type="password" id="password" name="password"
                               placeholder="Enter your password" required>
                    </div>

                    <div class="namechange-field">
                        <p><strong>Are you sure you want to delete your account?</strong></p>
                        <p style="font-size: 0.9rem; color: #555;">
                            All purchase history, account details, and associated data will be permanently removed.
                        </p>
                        <p style="color: red; font-weight: bold; margin-top: 0.5rem;">
                            WARNING: THIS ACTION CANNOT BE UNDONE
                        </p>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" name="confirm" class="btn primary namechange-btn" style="background: red;">
                            Yes, Delete My Account
                        </button>

                        <button type="submit" name="cancel" class="btn namechange-btn">
                            No, Keep My Account
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