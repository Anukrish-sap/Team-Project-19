<?php

session_start();
include "dbconnect.php";

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}
if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
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
?>


<style>
  .acc-page {
    max-width: 680px;
    margin: 0 auto;
    padding: 3rem 1.5rem 5rem;
  }
  .acc-hero { margin-bottom: 2.5rem; }
  .acc-hero-label {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--accent);
    background: rgba(192,123,80,0.1);
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    margin-bottom: 0.9rem;
  }
  .acc-hero h1 {
    font-size: clamp(1.6rem, 4vw, 2.2rem);
    font-weight: 800;
    margin: 0 0 0.4rem;
    line-height: 1.15;
    letter-spacing: -0.02em;
  }
  .acc-hero p { margin: 0; font-size: 0.97rem; opacity: 0.65; }
  .acc-avatar {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: var(--accent);
    color: #fff;
    font-size: 1.6rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.2rem;
    box-shadow: 0 4px 18px rgba(192,123,80,0.35);
  }
  .acc-section-title {
    font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.12em; text-transform: uppercase;
    opacity: 0.45; margin: 0 0 0.85rem;
  }
  .acc-cards { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2.75rem; }
  .acc-card {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; padding: 1.2rem 1.4rem;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    text-decoration: none; color: var(--text-color);
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  }
  .acc-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.09);
    border-color: var(--accent);
  }
  .acc-card-left { display: flex; align-items: center; gap: 1rem; }
  .acc-card-icon {
    width: 42px; height: 42px; border-radius: 0.65rem;
    background: rgba(192,123,80,0.1); color: var(--accent);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1.15rem;
  }
  .acc-card-icon.danger { background: rgba(192,57,43,0.08); color: #c0392b; }
  .acc-card-text strong { display: block; font-size: 0.97rem; font-weight: 700; margin-bottom: 0.15rem; }
  .acc-card-text span { font-size: 0.83rem; opacity: 0.55; }
  .acc-card-arrow {
    color: var(--border-color); font-size: 1.1rem;
    transition: color 0.15s ease, transform 0.15s ease; flex-shrink: 0;
  }
  .acc-card:hover .acc-card-arrow { color: var(--accent); transform: translateX(3px); }
  .acc-card.danger-card:hover { border-color: #e0a0a0; }
  .acc-card.danger-card:hover .acc-card-arrow { color: #c0392b; }
  .acc-divider { height: 1px; background: var(--border-color); opacity: 0.5; margin: 0.5rem 0 2rem; }
  @media (max-width: 480px) {
    .acc-page { padding: 2rem 1rem 4rem; }
    .acc-card { padding: 1rem 1.1rem; }
  }
</style>







 

<main>
  <div class="acc-page">

    <div class="acc-hero">
      <?php if ($user):
        $initial = strtoupper(mb_substr($user['name'], 0, 1));
      ?>
        <div class="acc-avatar"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></div>
        <span class="acc-hero-label">Your Account</span>
        <h1>Hello, <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?> 👋</h1>
        <p>Manage your profile, security, and account settings below.</p>
      <?php endif; ?>
    </div>

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
        <span class="acc-card-arrow">›</span>
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
        <span class="acc-card-arrow">›</span>
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
        <span class="acc-card-arrow">›</span>
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
        <span class="acc-card-arrow" style="color:#e0a0a0;">›</span>
      </a>

    </div>

  </div>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>


</body>
</html>

