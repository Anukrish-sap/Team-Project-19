<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "dbconnect.php";

if (!isset($_SESSION['userID'])) {
    $_SESSION['error'] = "You must be logged in.";
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (strlen($newPassword) < 8) {
        $_SESSION['error'] = "New password must be at least 8 characters long.";
        header("Location: passwordchange.php");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "New passwords do not match.";
        header("Location: passwordchange.php");
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT password FROM users WHERE userID = :userID");
        $stmt->bindParam(':userID', $_SESSION['userID'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: passwordchange.php");
            exit();
        }

        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect.";
            header("Location: passwordchange.php");
            exit();
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $update = $db->prepare("UPDATE users SET password = :password WHERE userID = :userID");
        $update->bindParam(':password', $newPasswordHash, PDO::PARAM_STR);
        $update->bindParam(':userID', $_SESSION['userID'], PDO::PARAM_INT);
        $update->execute();

        $_SESSION['success'] = "Password updated successfully!";
        header("Location: passwordchange.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: passwordchange.php");
        exit();
    }
}

include '../components/header_unified.php';
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Account Settings</span>
            <h1>Change Your Password</h1>
            <p>Update your password to keep your account secure.</p>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Update Password</h2>
                        <p>Enter your current password and choose a new one.</p>
                    </div>
                </div>

                <form action="passwordchange.php" method="POST" class="namechange-form">

                    <div class="namechange-field">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password"
                               placeholder="Enter your current password" required>
                    </div>

                    <div class="namechange-field">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password"
                               placeholder="At least 8 characters" minlength="8" required>
                    </div>

                    <div class="namechange-field">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                               placeholder="Repeat your new password" required>
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