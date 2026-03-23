<?php

session_start();
include "dbconnect.php";

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}
if (isset($_SESSION['userID'])) {
    include '../components/header_unified.php';
} else {
    header("Location: loginpage.php"); 
    $_SESSION['error'] = "You must be logged in to view account details";
    exit();
}
try {
    $acc = $db->prepare("SELECT email, name FROM users WHERE userID = :userID");
    $acc->bindParam(':userID', $_SESSION['userID']);
    $acc->execute();
    $user = $acc->fetch(PDO::FETCH_ASSOC);

    if (!$acc) {
        $_SESSION['error'] = "Error fetching account details: " . $db->errorInfo()[2];
        header("Location: home.php"); 
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching account details: " . $e->getMessage();
    header("Location: home.php"); 
    exit();
}

$quizResult = isset($_SESSION['quiz_result']) ? $_SESSION['quiz_result'] : null;
?>

<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/nYAZK4gbNeTGJ6R8NV4OGt4c-y96MotgGmFXrBbj28CXvlCJ2KeaVTGmkvmhdqtXYYXnkeubir6vXJSEJ0GnHMTMVLOKh2YScO6m0gxvopJ1iNdDEmCVeYU5rAGrTYrGsMR8ZmTpMoCG3F-qK_O0fW017w2fX273aTv-OYEySFywKjAFktZMGrIvXUpQjFYihprsx8vSZ71sDsZUo9ZTHWpxSOpIi7nPQJp4Lj883WLp1zgz8r5jGD335kTTiWGXSO8uW8ML3ixWlMBYpJwJWtqqS30zjoOvuah5cTSZsd3sn7bM07dRNhGrzcqPo0U3G7EPUj5BvnV-LwMVgShzmOz1-0xpbai3HA18NoCMsmEWe_qU1GgpPJ8W7pSA09cPlsJ6uDZZb8vS2C4ucfV-SornSEKG3i2Qk83oGCW_mj4JiWcXtFSkWiVuL7u-LcdXt20kUjofJ6jsUj10daz9p_IAW3eZeo7hVptPMSUyHM_l8ad_8sW-WpiPNY5dHaT3FSw8OMW0UHO2-i5brEJtZvnttkI3BDblcfEBoDJQxbysHHSb2_9vyIo1l2u82SVPQv1OmyPuRgiHKH8D56GX65HJVoYWR8SPjIw8hJJ2lFUWMx8mac9ehGh_HyMSyd4cYCDDPDJeUz3ie5_FGmp79hMII9Y-9Yb_3YhQtwkfOTpr0Ahsz08JkjwpgGgMgcrRrLqTudGZEogFNrwhjX0ve5UrCQ2imm3qJnb5iltWTEcZMhxxum75kL5CvQ7uXEsdfC0YwsYvOMjqOayNYO35HpddNRyZkIe59XVGxZUbmqvkXG_kP_EPJmqi4VldZm4MF-KR5DQxk_5_g-SjQya_6Z7jVxDsyh8Y90PIb3p0-DUlUSFkArruGK1ii77DnBrlidZj3t7YZWSXpC8-qerqdrgmH8s7x_hvhyw'></script>

<main>
  <div class="acc-page">

    <div class="acc-hero">
      <?php if ($user):
        $initial = strtoupper(mb_substr($user['name'], 0, 1));
      ?>
        <div class="acc-avatar"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></div>
        <span class="acc-hero-label">Your Account</span>
        <h1>Hello, <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?> &#x1F44B;</h1>
        <p>Manage your profile, security, and account settings below.</p>
      <?php endif; ?>
    </div>

    <!-- MY PREFERENCES -->
    <p class="acc-section-title">My Preferences</p>
    <div class="pref-card">
      <?php if ($quizResult): ?>
        <div class="pref-result">
          <div class="pref-emoji"><?= $quizResult['emoji'] ?></div>
          <div class="pref-result-text">
            <strong><?= htmlspecialchars($quizResult['heading'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span><?= htmlspecialchars($quizResult['subtext'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>
        <a href="<?= APP_URL ?>/quiz.php" class="pref-retake">&#x1F504; Redo quiz</a>
      <?php else: ?>
        <p class="pref-empty">You haven't taken the quiz yet &mdash; find out which bakes suit you best!</p>
        <a href="<?= APP_URL ?>/quiz.php" class="pref-retake">&#x2728; Take the quiz</a>
      <?php endif; ?>
    </div>

    <div class="acc-divider"></div>

    <p class="acc-section-title">Account Settings</p>
    <div class="acc-cards">

      <a href="namechange.php" class="acc-card">
        <div class="acc-card-left">
          <div class="acc-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.267.652 5.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <div class="acc-card-text">
            <strong>Change Name</strong>
            <span>Update how your name appears on the site</span>
          </div>
        </div>
        <span class="acc-card-arrow">&#x203A;</span>
      </a>

      <a href="passwordchange.php" class="acc-card">
        <div class="acc-card-left">
          <div class="acc-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
          </div>
          <div class="acc-card-text">
            <strong>Change Password</strong>
            <span>Update your login credentials</span>
          </div>
        </div>
        <span class="acc-card-arrow">&#x203A;</span>
      </a>

    </div>

    <div class="acc-divider"></div>

    <p class="acc-section-title">Other Options</p>
    <div class="acc-cards">

      <a href="logout.php" class="acc-card">
        <div class="acc-card-left">
          <div class="acc-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
          </div>
          <div class="acc-card-text">
            <strong>Log Out</strong>
            <span>Sign out of your account</span>
          </div>
        </div>
        <span class="acc-card-arrow">&#x203A;</span>
      </a>

      <a href="deleteaccount.php" class="acc-card danger-card">
        <div class="acc-card-left">
          <div class="acc-card-icon danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
          </div>
          <div class="acc-card-text">
            <strong style="color:#c0392b;">Delete Account</strong>
            <span>Permanently remove your account and data</span>
          </div>
        </div>
        <span class="acc-card-arrow" style="color:#e0a0a0;">&#x203A;</span>
      </a>

    </div>

  </div>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>

</body>
</html>