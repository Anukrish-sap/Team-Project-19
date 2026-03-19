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

            <div id="support-chat">
   <div id="chat-header" onclick="toggleChat()">
    <img src="<?= APP_URL ?>/img/support-icon.png" alt="Support">
</div>

    <div id="chat-body">
        <div id="chat-messages"></div>

        <form id="chat-form" onsubmit="sendMessage(event)">
            <input type="text" id="chat-input" placeholder="Ask a question..." required>
            <button type="submit">Send</button>
        </form>
    </div>
</div>

<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/Vas5olqgAFAa3qaaKhrAobN0TL42d0DnJ1gZyoDtnE6YjyNmrh66NFxmrbso-2uiaGyTF-4rEdCXk0_CrwqVj099712i5_lsDNX64rWdf3UaTC0sAzFGos-hgcNBDeC21hVp0AaduLgaYi9yZ3N5emUvXnQ4QfrJyvZyKC0LyBGzYLNEGmRkFIor9V2WDPm0G5fdweNL_RMF8t1AspX-k-Ng4LSVSumywtWWJcYgloB1BGLQklj1FmWOsU4O-flO-qA8KFWuRFT6woageX1y9ai1xXo8e94b2Q4YWf9KDBfV-palPdPC_-6l_9iyLAgIYk-cdyXQQ7gXSTPeJOegIYZVB5mwOJY6KmeyumUxDudzl9GaymRqJsU0nBZMLw3Ib99L9uet31reoocjpIENiEWbHgXfQ2myRORcE0GEqW6iLnUb4JZPjrslHtiukHzcxDdFXZ9oJGnSWVZVsQgSGomwjIT3qKjzxwjcPr09j1yfqCWYh0ZQj6BAgC1r2ns9a6V_3c7doDvbSXC-Htzpz_aBen5Enk2bSQzhD-C9eMONOcYD5ZosHOOFSGPdOBbbwU4JiKcrLWK4r3YcPz9f0PvnTzlU53Ak816CqL0GCj8pZhE8_aYZ7UN6DFnnD17TryJw5AjRRYkPHnt1ThX9T_cT02Q_Yh3q0O3vwVd7BQZZmghiQJnuFQlSPqMoehBx33F_C4ixwfiga4HvxD76ir4XKYu61FwcWrssJ5oCwXyBEOlmQYVHnHmxt1aouP9cad_s4LAO4cr6SufJtedcMp3e_fDTVQeGCCtQK8l26bStJ2fOvNi4jgoAsU_3MA3OIO3E2__wc3XsgWAx4tzRGRPObSqLMDc2VUWv79FdS-V-rEmDFF9MHnTNYa1ZORYT5eSeKJG7oUSIn88ZzPp7mry0ACu-ictJdLNNJg'></script><script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/G39zdaJrkb6rekZcA3rKu4_OgELfGOpmmfKv27gk5N1ceWIEycJwL5GVE7zuDXP0gHQYV5KS99CefLs6vWzEdE5kGxXTRVpK9EqtN6AyQXMnKqnTCh95rKgMZ4I-0ksGukT9z0eNPcoRKcFt9OkJ1nNQy5rgToYktJVfDtwog3Jok1zrda319Y0VIv_xw2cupWxaJfDOphVO7SKlqQJsAMxX_zKinmcc8jCDO_myLWkoBcjqf2kwb2fCfFV-XdJZbu_FylY1J0D3gW9namMs19V5JBFxOWB_B9DX3zsMTFNAPxOy2YhPu-Oz0pcAb1rNPtM3hPo2bArlefb2NGap3SsAkloQY0dS1iMpx5QjfDK6X84zqqdzTserJ2hX_Rz--upHOavvxYmB88bPEwAYYn_Ngk8EGLo9mncV8zQY6Iv06zm89sRLl4D_Nv1wnE5Gp2_4gvgIXsU33Hs0dYcwVh2cdZVnq6llYjoy-eedFV9jjLDtOSJ0k7V1_OYxZl_ZnoKv9W8PKG_3fRUdI4cN6OUVpZnN0KAEyF83VAxXinqKf8WTYyFGq2KA1kwBm8BYawBuJC0DLmhb7rV5zh_9bmhz8f_s2D7a-vBIq2doHNaFpy9X9MbwaiW5Tdc_5nLDIGIEi0o4z6iQV7uCXKShNCF_9itEWs9xYp_ZtofzR0iKvImqq2LDOjVFNg-jx_JRCZCMGQ8z39tCY-B4ROT5L6-6KlKM4ZtmUiBWDGsObB_aWQknNhdlxKqhFnF8-5MYZjIVXETx_FngDpBVUv8lStGBb6XgHatufre-69EyOdR7px4wGPOqvTO2xci61Q4vfCn533xu8sdQ6f4RldP9rPNnPxTgZmnhnpZVXOxcEh7C5dk6dn1IqjUABzoIEyQ9yRC5hulxcGnNkgB4kX2xc8mOOg9tSFl5s2vjJA'></script><script src="/public_/bake/js/support_chat.js"></script>