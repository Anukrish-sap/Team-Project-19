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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Name</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<?php include '../components/header_l.php'; ?>

<main>
    <section class="hero">
        <div class="hero-content">
            <h2 id="ww">Change your account name from:
                <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>
            </h2>
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

        <form action="namechange.php" method="POST" class="card">

            <label for="name_change">New Name:</label>
            <input type="text" id="name_change" name="name_change" required>

            <br><br>

            <label for="password">Confirm Password:</label>
            <input type="password" id="password" name="password" required>

            <br><br>

            <button type="submit" class="btn primary">Change Name</button>
        </form>
    </section>
</main>

<?php include '../components/footer.php'; ?>

<?php include '../components/script.html'; ?>

</body>
</html>
