<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

$isLoggedIn = isset($_SESSION['userID']);
?>

<footer>
    <div class="footer-nav">
        <a href="<?= HOME_URL ?>">Home</a>
        <a href="<?= APP_URL ?>/bakes.php">Products</a>
        <a href="<?= APP_URL ?>/basket.php">Basket</a>
        <a href="<?= APP_URL ?>/about.php">About Us</a>
        <a href="<?= APP_URL ?>/contact.php">Contact</a>
        <a href="<?= APP_URL ?>/helppage.php">Help</a>

        <?php if (!$isLoggedIn): ?>
            <a href="<?= APP_URL ?>/loginpage.php">Login</a>
            <a href="<?= APP_URL ?>/register.php">Register</a>
        <?php endif; ?>
    </div>

    <div class="social-icons-centered">
        <a href="#" aria-label="Facebook">
            <img src="<?= APP_URL ?>/img/Facebook_Logo_2023.png" alt="Facebook">
        </a>
        <a href="#" aria-label="Twitter">
            <img src="<?= APP_URL ?>/img/Logo_of_Twitter.svg.png" alt="Twitter">
        </a>
        <a href="#" aria-label="Instagram">
            <img src="<?= APP_URL ?>/img/Instagram_icon.png" alt="Instagram">
        </a>
    </div>

    <div class="footer-bottom">
        <p>
            <a href="#">Terms of Use</a> -
            <a href="#">Privacy Policy</a> -
            &copy; <?= date('Y'); ?> Bakes&amp;Cakes. All rights reserved.
        </p>
        <p>
            Contact: <a href="mailto:info@group19.com">info@group19.com</a> |
            Made by Group 19
        </p>
    </div>
</footer>
