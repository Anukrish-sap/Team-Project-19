<?php
session_start();
include "dbconnect.php";

/* Reset tokens on page load */
unset($_SESSION['accDetAdm']);
unset($_SESSION['selected_admin_email']);
unset($_SESSION['selected_admin_password']);



/* Error messages */
if (isset($_SESSION['adm_error'])) {
    echo "<p style='color:red; font-weight:600; text-align:center;'>" . $_SESSION['adm_error'] . "</p>";
    unset($_SESSION['adm_error']);
}
?>

<link rel="stylesheet" href="css/styleali.css">
<link rel="stylesheet" href="css/styles.css">

<?php include '../components/header_unified.php'; ?>



<form action="verify_creds_adm.php" method="POST">

<div class="dual-form-wrapper">

    <!-- LEFT PANEL — ADMIN DETAILS -->
    <div class="form-container">
        <h1>Admin Verification</h1>
        <p>You are logged in as:</p>

        <div class="form-group">
            <label>Admin Email</label>
            <p style="font-weight:600; margin:0;">
                <?= htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <!-- Hidden field so backend still receives the email -->
        <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
            <label for="password_admin">Confirm Password</label>
            <input 
                type="password"
                id="password_admin"
                name="password"
                required
                autocomplete="current-password">
        </div>
    </div>

    <!-- RIGHT PANEL — ACCOUNT DETAIL UPDATE -->
    <div class="form-container">
        <h1>Account to Manage</h1>
        <p>Enter the email of the account you want to modify</p>

        <div class="form-group">
            <label for="email_account">Account Email</label>
            <input 
                type="email"
                id="email_account"
                name="account_email"
                required
                autocomplete="email">
        </div>

        <button type="submit" name="loginButton" class="submit-btn">
            Continue
        </button>
    </div>

</div>

</form>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>