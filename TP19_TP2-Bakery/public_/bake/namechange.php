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

    $newName = trim($_POST['name_change']);
    $passwordInput = $_POST['password'];

    if (empty($newName) || strlen($newName) < 3 || strlen($newName) > 50) {
        $_SESSION['error'] = "Invalid name. It must be between 3 and 50 characters.";
        header("Location: namechange.php");
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT password FROM users WHERE userID = :userID");
        $stmt->bindParam(':userID', $_SESSION['userID'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: namechange.php");
            exit();
        }

        if (!password_verify($passwordInput, $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect.";
            header("Location: namechange.php");
            exit();
        }

        $update = $db->prepare("UPDATE users SET `name` = :name WHERE userID = :userID");
        $update->bindParam(':name', $newName, PDO::PARAM_STR);
        $update->bindParam(':userID', $_SESSION['userID'], PDO::PARAM_INT);
        $update->execute();

        $_SESSION['name'] = $newName;
        $_SESSION['success'] = "Name changed successfully!";
        header("Location: namechange.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: namechange.php");
        exit();
    }
}

include '../components/header_unified.php';
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Account Settings</span>
            <h1>Change Your Name</h1>
            <p>Currently signed in as <strong><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Update Display Name</h2>
                        <p>Enter your new name and confirm with your password.</p>
                    </div>
                </div>

                <form action="namechange.php" method="POST" class="namechange-form">

                    <div class="namechange-field">
                        <label for="name_change">New Name</label>
                        <input type="text" id="name_change" name="name_change"
                               placeholder="Enter your new name"
                               minlength="3" maxlength="50" required>
                    </div>

                    <div class="namechange-field">
                        <label for="password">Confirm Password</label>
                        <input type="password" id="password" name="password"
                               placeholder="Enter your current password" required>
                    </div>

                    <button type="submit" class="btn primary namechange-btn">
                        Change Name
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