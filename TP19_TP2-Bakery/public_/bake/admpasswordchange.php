<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "dbconnect.php";

/* 1. Ensure admin is logged in */
if (!isset($_SESSION['userID'])) {
    $_SESSION['error'] = "You must be logged in as an admin.";
    header("Location: login.php");
    exit();
}

/* 2. Ensure admin has selected a user to edit */
if (!isset($_SESSION['accDetAdm'])) {
    $_SESSION['error'] = "No account selected to modify.";
    header("Location: adminAccUpdate.php");
    exit();
}

$targetUserID = $_SESSION['accDetAdm'];

/* 3. Handle form submission */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (strlen($newPassword) < 8) {
        $_SESSION['error'] = "New password must be at least 8 characters long.";
        header("Location: admpasswordchange.php");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "New passwords do not match.";
        header("Location: admpasswordchange.php");
        exit();
    }

    try {
        /* Hash new password */
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        /* Update target user's password */
        $update = $db->prepare("
            UPDATE users 
            SET password = :password 
            WHERE userID = :uid
        ");
        $update->bindParam(':password', $newPasswordHash, PDO::PARAM_STR);
        $update->bindParam(':uid', $targetUserID, PDO::PARAM_INT);
        $update->execute();

        $_SESSION['success'] = "Password updated successfully!";
        header("Location: admin_account_edit.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: admpasswordchange.php");
        exit();
    }
}

include '../components/header_unified.php';
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Admin Panel</span>
            <h1>Change User Password</h1>
            <p>Update the password for the selected user account.</p>
        </div>
    </section>

    <section class="section">
        <div class="namechange-wrapper">

            <?php if (isset($_SESSION['success'])): ?>
                <div class="namechange-alert namechange-alert-success">
                    ✔ <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

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
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Update User Password</h2>
                        <p>Enter a new password for this user.</p>
                    </div>
                </div>

                <form action="admpasswordchange.php" method="POST" class="namechange-form">

                    <div class="namechange-field">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password"
                               placeholder="At least 8 characters" minlength="8" required>
                    </div>

                    <div class="namechange-field">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                               placeholder="Repeat the new password" required>
                    </div>

                    <button type="submit" class="btn primary namechange-btn">
                        Change Password
                    </button>

                </form>
            </div>

        </div>
    </section>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
</body>
</html>