<?php
session_start();

include "dbconnect.php";

/* 
   1. ADMIN ACCESS CHECK
 */
if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

$adminCheck = $db->prepare("
    SELECT adminStatus 
    FROM adminStatus 
    WHERE userID = :uid
");
$adminCheck->bindParam(':uid', $_SESSION['userID'], PDO::PARAM_INT);
$adminCheck->execute();
$adminRow = $adminCheck->fetch(PDO::FETCH_ASSOC);

// If not admin → redirect
if (!$adminRow || (int)$adminRow['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}

/* 
   2. ENSURE ADMIN SELECTED A USER
 */
if (!isset($_SESSION['accDetAdm'])) {
    header("Location: adminAccUpdate.php?error=no_account_selected");
    exit();
}

$targetUserID = $_SESSION['accDetAdm'];

/* 
   3. FETCH TARGET USER DETAILS
*/
try {
    $acc = $db->prepare("SELECT email, name FROM users WHERE userID = :userID");
    $acc->bindParam(':userID', $targetUserID, PDO::PARAM_INT);
    $acc->execute();
    $user = $acc->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: adminAccUpdate.php?error=user_not_found");
        exit();
    }

} catch (PDOException $e) {
    header("Location: adminAccUpdate.php?error=db_error");
    exit();
}

include '../components/header_unified.php';
?>

<style>
/* (same CSS as accdetails.php — unchanged) */
</style>

<main>
  <div class="acc-page">

    <div class="acc-hero">
      <?php 
        $initial = strtoupper(mb_substr($user['name'], 0, 1));
      ?>
        <div class="acc-avatar"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></div>
        <span class="acc-hero-label">Admin Panel</span>

        <h1>Managing <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>’s Account</h1>

        <p>Manage <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>’s profile and account settings below.</p>
    </div>

    <p class="acc-section-title">Account Settings</p>
    <div class="acc-cards">

      <!-- Change Name -->
      <a href="admnamechange.php?uid=<?= $targetUserID ?>" class="acc-card">
        <div class="acc-card-left">
          <div class="acc-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.267.652 5.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div class="acc-card-text">
            <strong>Change User’s Name</strong>
            <span>Update how this user’s name appears</span>
          </div>
        </div>
        <span class="acc-card-arrow">›</span>
      </a>

      <!-- Change Password -->
      <a href="admpasswordchange.php?uid=<?= $targetUserID ?>" class="acc-card">
        <div class="acc-card-left">
          <div class="acc-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
          </div>
          <div class="acc-card-text">
            <strong>Change User’s Password</strong>
            <span>Update this user’s login credentials</span>
          </div>
        </div>
        <span class="acc-card-arrow">›</span>
      </a>

    </div>

    <div class="acc-divider"></div>

    <p class="acc-section-title">Other Options</p>
    <div class="acc-cards">

      <!-- Return Button -->
      <a href="adminAccUpdate.php" class="acc-card">
        <div class="acc-card-left">
          <div class="acc-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v1"/>
            </svg>
          </div>
          <div class="acc-card-text">
            <strong>Return</strong>
            <span>Return to admin account selection</span>
          </div>
        </div>
        <span class="acc-card-arrow">›</span>
      </a>

      <!-- Delete Account -->
     <a href="adminAccDelete.php?uid=<?= $targetUserID ?>" class="acc-card danger-card">
    <div class="acc-card-left">
        <div class="acc-card-icon danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/>
            </svg>
        </div>
        <div class="acc-card-text">
            <strong style="color:#c0392b;">Delete Account</strong>
            <span>Permanently remove this user’s account</span>
        </div>
    </div>
    <span class="acc-card-arrow" style="color:#e0a0a0;">›</span>
</a>

    </div>

  </div>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>