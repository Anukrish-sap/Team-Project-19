<?php
session_start();
?>

<?php include '../components/header.html'; ?>
<html>
    <body>
        <h1>Contact Us</h1>
        <section class="contact-info">
            <div>
            <label for="full-name">Full Name:</label>
            <input type="text" id="full-name" name="full-name" placeholder="Enter your full name"><br><br>
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address"><br><br>
            <label for="subject">Subject:</label><br>
            <input type="text" id="subject" name="subject" placeholder="Enter the subject"><br><br>
            <label for="message">Message:</label><br>
            <textarea id="message" name="message" rows="5" cols="30" placeholder="Enter your message"></textarea><br><br>
            <button type="submit">Submit</button>
            </div>
        </section>
    </body>
</html>

<?php include '../components/footer.html'; ?>
<?php include '../components/script.html'; ?>
