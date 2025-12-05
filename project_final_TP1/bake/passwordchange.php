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

    // Validate new password
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
        // Fetch current password hash
        $stmt = $db->prepare("SELECT password FROM users WHERE userID = :userID");
        $stmt->bindParam(':userID', $_SESSION['userID'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: passwordchange.php");
            exit();
        }

        // Verify old password
        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect.";
            header("Location: passwordchange.php");
            exit();
        }

        // Hash new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<?php include '../components/header_l.php'; ?>

<main>
    <section class="hero">
        <div class="hero-content">
            <h2 id="ww">Change Your Password</h2>
        </div>
    </section>

    <section class="section">
        <h1>Your Account Settings</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?= htmlspecialchars($_SESSION['success']); ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="passwordchange.php" method="POST" class="card">

            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
            <br><br>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <br><br>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <br><br>

            <button type="submit" class="btn primary">Change Password</button>

        </form>
    </section>
</main>

<?php include '../components/footer.php'; ?>

<?php include '../components/script.html'; ?>

</body>
</html>
