<?php
session_start();
require_once 'dbconnect.php';

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message     = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        try {
            // Check if email exists
            $stmt = $db->prepare("SELECT userID, name FROM users WHERE email = :email");
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $message     = 'If that email is registered, a new password has been sent.';
                $messageType = 'success';
            } else {
                // Generate random password
                $newPassword    = bin2hex(random_bytes(5));
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password in DB
                $update = $db->prepare("UPDATE users SET password = :password WHERE userID = :userID");
                $update->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
                $update->bindValue(':userID', $user['userID'], PDO::PARAM_INT);
                $update->execute();

                // Send email using Aston server
                $to      = $email;
                $subject = 'Bakes & Cakes - Your new password';
                $body    = "Hi " . $user['name'] . ",\n\n"
                         . "You requested a password reset for your Bakes & Cakes account.\n\n"
                         . "Your new temporary password is: " . $newPassword . "\n\n"
                         . "Please log in and change your password as soon as possible.\n\n"
                         . "Login here: https://cs2team19.cs2410-web01pvm.aston.ac.uk/public_/bake/loginpage.php\n\n"
                         . "If you did not request this, please contact us immediately.\n\n"
                         . "Bakes & Cakes Team";

                $headers  = "From: cs2team19@cs2410-web01pvm.aston.ac.uk\r\n";
                $headers .= "Reply-To: cs2team19@cs2410-web01pvm.aston.ac.uk\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                ini_set('sendmail_from', 'cs2team19@cs2410-web01pvm.aston.ac.uk');

                $sent = mail($to, $subject, $body, $headers);

                $message     = 'If that email is registered, a new password has been sent.';
                $messageType = 'success';
            }

        } catch (PDOException $e) {
            $message     = 'Something went wrong. Please try again.';
            $messageType = 'error';
        }
    }
}

include '../components/header_unified.php';
?>

<main>

    <section class="namechange-hero">
        <div class="namechange-hero-inner">
            <span class="namechange-hero-label">Account</span>
            <h1>Forgot Password</h1>
            <p>Enter your email address and we'll send you a new temporary password.</p>
        </div>
    </section>

    <section class="section">
        <div class="namechange-wrapper">

            <?php if (!empty($message)): ?>
                <div class="namechange-alert <?= $messageType === 'success' ? 'namechange-alert-success' : 'namechange-alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($messageType !== 'success'): ?>
            <div class="namechange-card">
                <div class="namechange-card-header">
                    <div class="namechange-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Reset Password</h2>
                        <p>We'll send a new password to your registered email.</p>
                    </div>
                </div>

                <form action="forgot-password.php" method="POST" class="namechange-form">
                    <div class="namechange-field">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                               placeholder="Enter your registered email"
                               required>
                    </div>
                    <button type="submit" class="btn primary namechange-btn">
                        Send New Password
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <div style="text-align:center; margin-top:1rem;">
                <a href="<?= APP_URL ?>/loginpage.php" class="btn secondary small">← Back to login</a>
            </div>

        </div>
    </section>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>