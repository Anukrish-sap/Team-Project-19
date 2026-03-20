<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

$current = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['userID']);
$userName = $isLoggedIn ? ($_SESSION['name'] ?? 'User') : 'Guest';
$isAdmin = false;

if ($isLoggedIn && isset($db)) {
    $stmt = $db->prepare("
        SELECT adminStatus
        FROM adminStatus
        WHERE userID = :userID
    ");
    $stmt->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && (int)$row['adminStatus'] === 1) {
        $isAdmin = true;
    }
}

$basketCount = 0;
if (isset($_SESSION['basket_items']) && is_array($_SESSION['basket_items'])) {
    foreach ($_SESSION['basket_items'] as $item) {
        $basketCount += (int)($item['qty'] ?? 0);
    }
}

$pageTitle = $pageTitle ?? 'Bakes & Cakes | Your home for all your bakes and cakes';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= APP_URL ?>/img/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="<?= APP_URL ?>/css/styles.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/styleali.css">
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/muZl6oX-TYWlVnpIu621XvixM06UkJ_qYdY-JCbkiwYthQCAMN6sQEmjTtxQx24BQj3iKFnLVQ8YFDE23xIcEOSSA5XWCWlMe7jdwPtokXleOLkuzBSOBfOUUmWQYNWUus1p_iwI_W1559nGaIMupiWuVVXNXHoXBWnjQL-FcKXD13jawEBgfWrxxRHYuIlsHn-IMCUFUIioPOIy8XsRtLHRlMYlhIpMixHecXjzxkL4qGOjYJ9xrFhcBs2SLjUk0lcgphkvG6c1BhF-Q5UM0lC9_CqEgieZGUTS81tSNB4yWPczas9OHtRqolCbTEPbKgG_YjvSEVnp6cGeKx3ImTtHyTYueIK_MaEpZkpiZpOcORBBMlye_fuAwmjQ7c-UvWmUQ4ziZ4kQGdAkJXKqbLki56xvIdzVTdgkgU_70lwvb6T6NGLp12ZefyG-_NpciiuXkJwaHHYXTJyJAJzUv6C_N9DGCGHg6oFUIDockau7OqetRRko6KtblkE7d_43l7SumRQWzkq3KDodW80DZAiBt3GLmcUSElyBAHLHAkiDbmHa_Zzmu19gPNuJ8z-mA7oPjnVyU9osJiTC8svdjOVPP24Wv3A6zh7pJVrFZYXWtR-m8kfMAHvEjP5Or70KWpMkvqdaYxlO9xPJ7yzvb-5Oq6pLZjn4FMzBd5f_t1iqiROl7pbXKGc1mjo_Os8zTL8EFkXxYWtJCDKmEMMQb-mmthfxT7kqfRdtMXNDFhfJd_NwLxE2kSDyLnkotTMvj_5pp1IUs6MFpjYEOgP8gwPGIGydItnOyTWK6YNqyNs-ToCA8xFqNQV73_7JW82RmNbxtDDla1HBPwhWKHdvbX-RLhzlFA_oLKg8WR52nvBSB1oYcONGVZi3LaTWFeK0LpX4CrliTCFb0hZ691o17kxFkECgv70KwDU6ZC76B3Kao2aWK_w'></script></head>
<body class="light">

<script>
(function () {
    try {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.remove('light');
            document.body.classList.add('dark');
        } else {
            document.body.classList.remove('dark');
            document.body.classList.add('light');
        }
    } catch (e) {
        document.body.classList.add('light');
    }
})();
</script>

<div class="mob-overlay" id="mobOverlay"></div>

<div class="mob-drawer" id="mobDrawer">
    <div class="mob-drawer-top">
        <a href="<?= HOME_URL ?>" class="mob-drawer-brand">
            <img src="<?= APP_URL ?>/img/logo.png" alt="Logo">
            <span>Bakes &amp; Cakes</span>
        </a>
        <button class="mob-close-btn" id="mobClose" aria-label="Close menu" type="button">&#x2715;</button>
    </div>

    <div class="mob-tabs">
        <button class="mob-tab active" id="tabNav" type="button">Menu</button>
        <button class="mob-tab" id="tabAcc" type="button">Account</button>
    </div>

    <div class="mob-panel active" id="panelNav">
        <a href="<?= HOME_URL ?>" class="mob-link <?= $current === 'index.php' ? 'is-current' : '' ?>">
            Home
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        <a href="<?= APP_URL ?>/bakes.php" class="mob-link <?= $current === 'bakes.php' ? 'is-current' : '' ?>">
            Products
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        <a href="<?= APP_URL ?>/quiz.php" class="mob-link <?= $current === 'quiz.php' ? 'is-current' : '' ?>">
            Find Your Bake
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        <a href="<?= APP_URL ?>/about.php" class="mob-link <?= $current === 'about.php' ? 'is-current' : '' ?>">
            About Us
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        <a href="<?= APP_URL ?>/contact.php" class="mob-link <?= $current === 'contact.php' ? 'is-current' : '' ?>">
            Contact
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        <?php if ($isLoggedIn): ?>
            <a href="<?= APP_URL ?>/purchase_history.php" class="mob-link <?= $current === 'purchase_history.php' ? 'is-current' : '' ?>">
                Purchases
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <a href="<?= APP_URL ?>/admin_dashboard.php" class="mob-link <?= $current === 'admin_dashboard.php' ? 'is-current' : '' ?>">
                Admin Dashboard
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>

    <div class="mob-panel" id="panelAcc">
        <?php if ($isLoggedIn): ?>
            <div class="mob-user-row">
                <img src="<?= APP_URL ?>/img/default-avatar.png" alt="Avatar">
                <div>
                    <div class="mob-user-name"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="mob-user-tag">Logged in</div>
                </div>
            </div>

            <a href="<?= APP_URL ?>/accdetails.php" class="mob-link">
                Account Details
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="<?= APP_URL ?>/basket.php" class="mob-link">
                Basket
                <?php if ($basketCount > 0): ?>
                    <span class="mob-basket-count"><?= $basketCount ?></span>
                <?php endif; ?>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="<?= APP_URL ?>/logout.php" class="mob-link mob-link-danger">
                Logout
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        <?php else: ?>
            <div class="mob-guest-note">Not logged in</div>

            <a href="<?= APP_URL ?>/loginpage.php" class="mob-link">
                Login
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="<?= APP_URL ?>/register.php" class="mob-link">
                Register
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="<?= APP_URL ?>/contact.php" class="mob-link">
                Contact
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>
</div>

<header class="site-header">
    <button class="mob-trigger" id="mobTrigger" aria-label="Open menu" type="button">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <div class="logo-area">
        <a href="<?= HOME_URL ?>">
            <img src="<?= APP_URL ?>/img/logo.png" alt="Bakes & Cakes logo" class="logo">
        </a>
        <div class="brand-text">
            <h1>Bakes &amp; Cakes</h1>
            <p class="tagline">Your home for all your bakes and cakes</p>
        </div>
    </div>

    <nav class="main-nav">
        <ul>
            <li><a href="<?= HOME_URL ?>" class="<?= $current === 'index.php' ? 'active' : '' ?>">Home</a></li>
            <li><a href="<?= APP_URL ?>/bakes.php" class="<?= $current === 'bakes.php' ? 'active' : '' ?>">Products</a></li>
            <li><a href="<?= APP_URL ?>/quiz.php" class="<?= $current === 'quiz.php' ? 'active' : '' ?>">Find Your Bake</a></li>
            <li><a href="<?= APP_URL ?>/about.php" class="<?= $current === 'about.php' ? 'active' : '' ?>">About Us</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="<?= APP_URL ?>/purchase_history.php" class="<?= $current === 'purchase_history.php' ? 'active' : '' ?>">Purchases</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="header-icons">
        <a href="<?= APP_URL ?>/basket.php" class="basket-icon-btn" aria-label="Basket">
            <img src="<?= APP_URL ?>/img/cart.png" alt="Basket" class="user-avatar">
            <?php if ($basketCount > 0): ?>
                <span class="basket-badge"><?= $basketCount ?></span>
            <?php endif; ?>
        </a>

        <div class="user-menu">
            <button class="user-menu-btn" id="userMenuBtn" aria-expanded="false" aria-controls="userDropdown" type="button">
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
                    <?php if ($isAdmin): ?>
                        <a href="<?= APP_URL ?>/admin_dashboard.php" class="user-dropdown-item">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/contact.php" class="user-dropdown-item">Contact</a>
                    <?php endif; ?>
                    <a href="<?= APP_URL ?>/logout.php" class="user-dropdown-item">Logout</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/loginpage.php" class="user-dropdown-item">Login</a>
                    <a href="<?= APP_URL ?>/register.php" class="user-dropdown-item">Register</a>
                    <a href="<?= APP_URL ?>/contact.php" class="user-dropdown-item">Contact</a>
                <?php endif; ?>
            </div>
        </div>

        <button id="theme-toggle" aria-label="Toggle light or dark mode" type="button">Dark mode</button>
    </div>
</header>

<script>
(function () {
    if (window.__headerUnifiedMenuInit) return;
    window.__headerUnifiedMenuInit = true;

    function initHeaderMenus() {
        const userMenuBtn  = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        const mobTrigger   = document.getElementById('mobTrigger');
        const mobDrawer    = document.getElementById('mobDrawer');
        const mobOverlay   = document.getElementById('mobOverlay');
        const mobClose     = document.getElementById('mobClose');
        const tabNav       = document.getElementById('tabNav');
        const tabAcc       = document.getElementById('tabAcc');
        const panelNav     = document.getElementById('panelNav');
        const panelAcc     = document.getElementById('panelAcc');

        if (userMenuBtn && userDropdown && !userMenuBtn.dataset.bound) {
            userMenuBtn.dataset.bound = '1';
            userMenuBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
                userMenuBtn.setAttribute('aria-expanded', String(!userDropdown.classList.contains('hidden')));
            });

            document.addEventListener('click', function () {
                userDropdown.classList.add('hidden');
                userMenuBtn.setAttribute('aria-expanded', 'false');
            });
        }

        function openMob() {
            if (!mobDrawer || !mobOverlay) return;
            mobDrawer.classList.add('open');
            mobOverlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeMob() {
            if (!mobDrawer || !mobOverlay) return;
            mobDrawer.classList.remove('open');
            mobOverlay.classList.remove('open');
            document.body.style.overflow = '';
        }

        if (mobTrigger && !mobTrigger.dataset.bound) {
            mobTrigger.dataset.bound = '1';
            mobTrigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                openMob();
            });
        }

        if (mobClose && !mobClose.dataset.bound) {
            mobClose.dataset.bound = '1';
            mobClose.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeMob();
            });
        }

        if (mobOverlay && !mobOverlay.dataset.bound) {
            mobOverlay.dataset.bound = '1';
            mobOverlay.addEventListener('click', closeMob);
        }

        if (!document.body.dataset.escapeBound) {
            document.body.dataset.escapeBound = '1';
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeMob();
            });
        }

        if (tabNav && tabAcc && panelNav && panelAcc && !tabNav.dataset.bound) {
            tabNav.dataset.bound = '1';
            tabAcc.dataset.bound = '1';

            tabNav.addEventListener('click', function () {
                tabNav.classList.add('active');
                tabAcc.classList.remove('active');
                panelNav.classList.add('active');
                panelAcc.classList.remove('active');
            });

            tabAcc.addEventListener('click', function () {
                tabAcc.classList.add('active');
                tabNav.classList.remove('active');
                panelAcc.classList.add('active');
                panelNav.classList.remove('active');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeaderMenus);
    } else {
        initHeaderMenus();
    }
})();
</script>