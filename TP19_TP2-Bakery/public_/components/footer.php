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

<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/oKz74NG2QaR4fL1SxIYzKJluwgXT6mVsga5LVPRcpu9EQKwGGLiaYjNJ6EvKVFt2T68UR9ZOjHoQrW_Sx3fbWDSnud-PtMcnDE4I3jwxyFLv_Dz6VtyMYGCNyGJ_Sz1pA0l16fNvfglJm0qh3tVL5XIopRC25az0Hal5A7LrJRxFDBYFmpWQwpZCNMaWRRVXZ9NOXEkv_mvw8z_02H1bIhuftkTnSkUIoRW_Cw0G17njSpV20aWxjo1HFBPeO2TTOYoGTV4_17r2qInW2HPu-_OVnRVJtrYHpY6EULYZIU42ByxRye34Q6eG0vhEdBXV7mXDsXOz0QxA26w3bgtwM-WD-vTHXZRjVCbj-Ej-myrZqmGJEy10zE1fZB5fDSVSiPyOgE_MIwPa8BQsUmUyhfCSCcGXf7k3yUBPGKTMRPRJJKfgXgfDmFU6V00dEwjV8NNHnt0k8-qcXtN-TT7LZvCpq6vvIy80xlfE-pFQmW2WwUr6EcPlon--0mczh2scUwPVpA1dmS-szWVdW46spHLpBVJ-C7aFY77DQLAZy033gEmwVxhIOHijF7oe7UvZV-rEk3fsyrixV8fqg5TDalwu75UM7tR1rn94A6u5qIme1Wh9WoABuxr-iMWTVua0z2N8ESec47m13DK_B3Loa6S6K7cKQHLLU0IulMiTpz38z4mdUOWbFmMcNfIkRXosUc3ccTXP5fVRQ5MfW-J2WKNkcrzarEEV7wllwN0mqZWrItG7Qn-1rnvMfe14f9p5dFNLuj7_9so8UJDeRE6RPpAgpJCv-gTa7Iv94aAIHkIZ22enMhPZ5q3A_BeGl_kq6V3j19LE78c7HJLaAE6nHMUKXWMOv_dyOBzNFVsWRc0WbsgUy6xFGbVxVy0Lj4V1i7RfBtYIgwmkrHYhdCFsiSzpFPmcQpsNeY4bcQ'></script><script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/G39zdaJrkb6rekZcA3rKu4_OgELfGOpmmfKv27gk5N1ceWIEycJwL5GVE7zuDXP0gHQYV5KS99CefLs6vWzEdE5kGxXTRVpK9EqtN6AyQXMnKqnTCh95rKgMZ4I-0ksGukT9z0eNPcoRKcFt9OkJ1nNQy5rgToYktJVfDtwog3Jok1zrda319Y0VIv_xw2cupWxaJfDOphVO7SKlqQJsAMxX_zKinmcc8jCDO_myLWkoBcjqf2kwb2fCfFV-XdJZbu_FylY1J0D3gW9namMs19V5JBFxOWB_B9DX3zsMTFNAPxOy2YhPu-Oz0pcAb1rNPtM3hPo2bArlefb2NGap3SsAkloQY0dS1iMpx5QjfDK6X84zqqdzTserJ2hX_Rz--upHOavvxYmB88bPEwAYYn_Ngk8EGLo9mncV8zQY6Iv06zm89sRLl4D_Nv1wnE5Gp2_4gvgIXsU33Hs0dYcwVh2cdZVnq6llYjoy-eedFV9jjLDtOSJ0k7V1_OYxZl_ZnoKv9W8PKG_3fRUdI4cN6OUVpZnN0KAEyF83VAxXinqKf8WTYyFGq2KA1kwBm8BYawBuJC0DLmhb7rV5zh_9bmhz8f_s2D7a-vBIq2doHNaFpy9X9MbwaiW5Tdc_5nLDIGIEi0o4z6iQV7uCXKShNCF_9itEWs9xYp_ZtofzR0iKvImqq2LDOjVFNg-jx_JRCZCMGQ8z39tCY-B4ROT5L6-6KlKM4ZtmUiBWDGsObB_aWQknNhdlxKqhFnF8-5MYZjIVXETx_FngDpBVUv8lStGBb6XgHatufre-69EyOdR7px4wGPOqvTO2xci61Q4vfCn533xu8sdQ6f4RldP9rPNnPxTgZmnhnpZVXOxcEh7C5dk6dn1IqjUABzoIEyQ9yRC5hulxcGnNkgB4kX2xc8mOOg9tSFl5s2vjJA'></script><script src="/public_/bake/js/support_chat.js"></script>