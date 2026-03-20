<?php
session_start();
require_once "dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Admin credentials
    $adminEmail = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $adminPassword = $_POST['password'];

    // Account email (right-side form)
    $accountEmail = htmlspecialchars($_POST['account_email'], ENT_QUOTES, 'UTF-8');

    /* 
       1. Validate ADMIN credentials
     */

    $adminSQL = "
        SELECT u.userID, u.password, u.email, u.name, a.adminStatus
        FROM users u
        JOIN adminStatus a ON a.userID = u.userID
        WHERE u.email = :email
    ";

    $stmt = $db->prepare($adminSQL);
    $stmt->bindParam(':email', $adminEmail, PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin || (int)$admin['adminStatus'] !== 1) {
       $_SESSION['adm_error'] = "Error: Incorrect Admin or Account details entered.";
header("Location: adminAccUpdate.php");
exit();
    }

    if (!password_verify($adminPassword, $admin['password'])) {
        $_SESSION['adm_error'] = "Error: Incorrect Admin or Account details entered.";
header("Location: adminAccUpdate.php");
exit();
    }

    /* 
       2. Validate ACCOUNT email
     */

    $accSQL = "
        SELECT u.userID, u.email, a.adminStatus
        FROM users u
        JOIN adminStatus a ON a.userID = u.userID
        WHERE u.email = :email
    ";

    $stmt2 = $db->prepare($accSQL);
    $stmt2->bindParam(':email', $accountEmail, PDO::PARAM_STR);
    $stmt2->execute();
    $account = $stmt2->fetch(PDO::FETCH_ASSOC);

    if (!$account || (int)$account['adminStatus'] !== 0) {
       $_SESSION['adm_error'] = "Error: Incorrect Admin or Account details entered.";
header("Location: adminAccUpdate.php");
exit();
    }

    /* 
       3. SUCCESS → store token
   */

    $_SESSION['accDetAdm'] = $account['userID'];

    header("Location: admin_account_edit.php");
    exit();
}
?>
