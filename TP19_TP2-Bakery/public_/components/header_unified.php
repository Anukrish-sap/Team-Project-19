<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

$current = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['userID']);
$userName = $isLoggedIn ? $_SESSION['name'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bakes & Cakes | Your home for all your bakes and cakes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon + CSS -->
    <link rel="icon" href="<?= APP_URL ?>/img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/styles.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/styleali.css">
</head>

<body class="light">

<header class="site-header">
    <div class="logo-area">
        <a href="<?= HOME_URL ?>">
            <img src="<?= APP_URL ?>/img/logo.png" alt="Bakes & Cakes logo" class="logo">
        </a>

        <div class="brand-text">
            <h1>Bakes & Cakes</h1>
            <p class="tagline">Your home for all your bakes and cakes</p>
        </div>
    </div>

    <nav class="main-nav">
        <ul>
            <li>
                <a href="<?= HOME_URL ?>" class="<?= $current === 'index.php' ? 'active' : '' ?>">
                    Home
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/bakes.php" class="<?= $current === 'bakes.php' ? 'active' : '' ?>">
                    Products
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/basket.php" class="<?= $current === 'basket.php' ? 'active' : '' ?>">
                    Basket
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/contact.php" class="<?= $current === 'contact.php' ? 'active' : '' ?>">
                    Contact
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/helppage.php" class="<?= $current === 'helppage.php' ? 'active' : '' ?>">
                    Help
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Dropdown -->
    <div class="user-menu">
        <button class="user-menu-btn" id="userMenuBtn" aria-expanded="false" aria-controls="userDropdown">
            <img src="<?= APP_URL ?>/img/default-avatar.png" alt="User avatar" class="user-avatar">
            <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                <path d="M6 9L1 4h10L6 9z"/>
            </svg>
        </button>

        <div class="user-dropdown hidden" id="userDropdown">
            <?php if ($isLoggedIn): ?>
                <div class="user-dropdown-header">
                    <span class="user-dropdown-name"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <a href="<?= APP_URL ?>/accdetails.php" class="user-dropdown-item">Account Details</a>
                <a href="<?= APP_URL ?>/logout.php" class="user-dropdown-item">Logout</a>
            <?php else: ?>
                <a href="<?= APP_URL ?>/loginpage.php" class="user-dropdown-item">Login</a>
                <a href="<?= APP_URL ?>/register.php" class="user-dropdown-item">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <button id="theme-toggle" aria-label="Toggle light or dark mode">Dark mode</button>
</header>

<script>
const userMenuBtn = document.getElementById('userMenuBtn');
const userDropdown = document.getElementById('userDropdown');

if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('hidden');
        userMenuBtn.setAttribute(
            'aria-expanded',
            String(!userDropdown.classList.contains('hidden'))
        );
    });

    document.addEventListener('click', () => {
        userDropdown.classList.add('hidden');
        userMenuBtn.setAttribute('aria-expanded', 'false');
    });
}
</script>
