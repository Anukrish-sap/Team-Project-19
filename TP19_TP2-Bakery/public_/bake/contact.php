<?php
session_start();
require_once 'dbconnect.php';


if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

// logout message (if any)
if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}

// Always include header so the page looks consistent
include '../components/header_unified.php';
?>

<main>

    <section class="hero">
        <div class="hero-content">
            <div class="contact-container">
                <h1>Contact Us</h1>
            </div>
        </div>
    </section>

    <div class="contact-wrapper">

        <!-- If reviewadd.php is inside /public_/bake, this relative action is OK.
             To be safe from anywhere, use APP_URL explicitly: -->
        <form action="<?= APP_URL ?>/reviewadd.php" method="POST" class="contact-form">

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" placeholder="Enter the subject" required>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" placeholder="Enter your message" required></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn primary">Send Message</button>
            </div>

        </form>

        <div class="info-box">
            <h2>Contact Information</h2>
            <p>Email: <strong>info@group19.com</strong></p>
        </div>

    </div>

</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>

</body>
</html>
