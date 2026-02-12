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
    include '../components/header.php';
}


?>



<body>
    <section class="hero">
        <div class="hero-content">
<div class="contact-container">
    <h1>Contact Us</h1>
</div>
</section>



    <div>
   <form action="reviewadd.php" method="POST">
    <section class="section">
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" placeholder="Enter your full name">
    </section>

    <section class="section section-alt">
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email address">
    </section>

    <section class="section">
        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" placeholder="Enter the subject">
    </section>

    <section class="section section-alt">
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="5" placeholder="Enter your message"></textarea>
    </section>

    <section class="section">
        <button type="submit">Submit</button>
    </section>
</form>
        <section class="section section-alt">
    <div class="info-box">
        <h2>Contact</h2>
        <p>Email: <strong>info@group19.com</strong></p>
    </div>
        </section>
</div>

</body>
</html>
<?php include '../components/footer.php'; ?>

<?php include '../components/script.html'; ?>