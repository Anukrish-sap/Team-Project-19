<?php 
$isLoggedIn = isset($_SESSION['userID']);
?>
<footer>
    <link rel="stylesheet" href="css/styleali.css">
    
    <!-- Top navigation links -->
    <div class="footer-nav">
        <a href="home.php">Home</a>
        <a href="bakes.php">Products</a>
        <a href="basket.php">Basket</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
        <a href="helppage.php">Help</a>
        
        <?php if (!$isLoggedIn): ?>
            <a href="loginpage.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
    
    <!-- Social icons centered -->
    <div class="social-icons-centered">
        <a href="#" aria-label="Facebook">
            <img src="img/Facebook_Logo_2023.png" alt="Facebook">
        </a>
        <a href="#" aria-label="Twitter">
            <img src="img/Logo_of_Twitter.svg.png" alt="Twitter">
        </a>
        <a href="#" aria-label="Instagram">
            <img src="img/Instagram_icon.png" alt="Instagram">
        </a>
    </div>
    
    <!-- Bottom text -->
    <div class="footer-bottom">
        <p>
            <a href="#">Terms of Use</a> - 
            <a href="#">Privacy Policy</a> - 
            &copy; <?php echo date('Y'); ?> Bakes&Cakes. All rights reserved.
        </p>
        <p>
            Contact: <a href="mailto:info@group19.com">info@group19.com</a> | 
            Made by Group 19
        </p>
    </div>
</footer>	