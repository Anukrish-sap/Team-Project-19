<?php
session_start();
require_once 'dbconnect.php';

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}

include '../components/header_unified.php';

$category = isset($_GET['category']) ? $_GET['category'] : null;
$tag      = isset($_GET['tag']) ? $_GET['tag'] : null;
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';

$categoryNames = [
    'cakes'    => 'Cakes',
    'cookies'  => 'Cookies',
    'pastries' => 'Pastries',
    'bread'    => 'Bread'
];

$heading    = 'All bakes';
$subheading = 'Browse our freshly baked products.';

if ($search !== '') {
    $heading    = 'Search results';
    $subheading = 'Showing results for "' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '".';
} elseif ($tag === 'gluten-free') {
    $heading    = 'Gluten Free Range';
    $subheading = 'Browse all of our gluten free bakes.';
} elseif ($category && isset($categoryNames[$category])) {
    $heading    = $categoryNames[$category];
    $subheading = 'Browse our ' . strtolower($categoryNames[$category]) . '.';
}
?>

<main>
    <?php if (!isset($_SESSION['userID'])): ?>
        <section class="section">
            <h3>Log in to order</h3>
            <p>
                To place an order, please
                <a href="<?= APP_URL ?>/loginpage.php">log in</a> or
                <a href="<?= APP_URL ?>/register.php">register</a>.
            </p>
        </section>
    <?php else: ?>
        <section class="section">
            <h3>Welcome, <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?>!</h3>
            <p>Click any bake to view sizes, servings, ingredients and reviews.</p>
        </section>
    <?php endif; ?>

    <section class="section">
        <div class="page-heading-block">
            <h1 id="headingBakes">Fresh Bakeries</h1>
            <p id="subheadingBakes">Our Freshly Baked Goods</p>
        </div>

        <form id="searchBarForm" class="product-search-form">
            <input
                type="text"
                id="searchInput"
                class="product-search-input"
                placeholder="Search for a bake..."
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
            >
            <button type="submit" class="buttonE">Search</button>
            <button type="button" class="buttonE price-filter-toggle-btn" id="filterToggleBtn" onclick="togglePriceFilter()">
                 Filter
            </button>
        </form>

        <div class="buttons product-filter-buttons">
            <button class="buttonE" onclick="loadProducts()">All</button>
            <button class="buttonE" onclick="loadProducts('cakes')">Cakes</button>
            <button class="buttonE" onclick="loadProducts('cookies')">Cookies</button>
            <button class="buttonE" onclick="loadProducts('pastries')">Pastries</button>
            <button class="buttonE" onclick="loadProducts('bread')">Bread</button>
            <button type="button" class="buttonE" onclick="loadProducts(null, '', 'gluten-free')">Gluten Free</button>
        </div>

        <!-- Price Filter (hidden by default) -->
        <div class="price-filter-bar" id="priceFilterBar" style="display:none;">
            <span class="price-filter-label">Price</span>

            <div class="price-filter-sliders">
                <div class="price-range-wrap">
                    <label for="priceMin">Min &nbsp;<strong>£<span id="priceMinDisplay">0</span></strong></label>
                    <input type="range" id="priceMin" min="0" max="100" value="0" step="1" oninput="onPriceChange()">
                </div>
                <div class="price-range-wrap">
                    <label for="priceMax">Max &nbsp;<strong>£<span id="priceMaxDisplay">100</span></strong></label>
                    <input type="range" id="priceMax" min="0" max="100" value="100" step="1" oninput="onPriceChange()">
                </div>
            </div>

            <span class="price-filter-active-badge" id="priceActiveBadge">✦ Filter active</span>

            <button class="buttonE price-filter-reset" onclick="resetPriceFilter()">Reset</button>
        </div>

        <div id="bakes-container" class="card-grid"></div>
    </section>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/fQ0vGkoo4GHw272LgfNeonO2MgpUlDKpLtZKDCi_IVsT8AYLc0Jm-31BgILOHkh9HqL6ne0HuFLpe58Z6BbhHbwkAyWEIp3ZV9gzi5m3Y1oxJV0HGcoG2TwEn6yUB7uETNPLHPxsuPldhXCjyiIian2kmlSTzjd742_cIWUUV4IseCbNnXj4_x4b08jUB9jURnC5D8Ak_zT3I48--wTPDEiVnXFlyMdqxqdom3CIjvv9aC3YY_Q5ZmeVMxeg4GatmtOMN-3AFNJXdE_Twglm3UVhLayP6DaML13jxbtQkbChCKINgUPSB6Wj-Hu2bnyaXWEUw5dl0MlWG0-y7ibS09ituxX6eMW-4m472Qxrqx2C_Y7x7_9KfvnGC5zpFtWS_RO71L5lzVZOISFd3KtWBPvbw-O2KE61SIC05yrvuXZmZLarVwGCrRuR8WUNEk4xLxFz3PDBmAh3U4rPiieDnnmaUbjk5PeWjXfq9WTJ87D8fnIePvukpC8eco2nzGHLIyQO4i8aIETk78Xcycxafyzd_bbzz3aCBo-tFTMBP6sthYeNWT_F6DPWJOfJMPZJZVy4N9z9AJFoelboT4iv-WwSVwo2MTcpJLC5Yx4HYJecac6EidfgL5ffgmyKofXX6t4QYozJX6YyRd9HVAheX-7J2bFl5vqKkIQRs4AuSizsxhJbU1eYZP6wiSpX9RnJ1VyrkXQK46S_MLBoRbqHMOYN_tH_Uj9kqPa4do77Hy1IFhtR0q3VLQP5t4rpbW_kDZWJVMi6NiaGXeTpFMU4DtAeonw0kvysd65IuJ-xkpQBngya7P4JUXKSp9RkWvjEXGkajDSXX2nYnL6LMDfL1QRFY2eMOlZP-r-mWviw_7F_d5vvZVzXxR2nmAYrlQK6xpKnwBEqR3-VadsGYa5MTERX09PU'></script>
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/A9NCM7FiWq4d1_C3ER86Tin6GqUYcic-Y3-C59b-px1itRIuWHHsvW3MvbkQ1WJw2B8LGFmQoZ__GQK5jNUkbt8AqFXRzPhQbqk6qNAkeXWYtwwOdhNKpQjAypq2rGB2AEjUx1A5ug89APk4vl0mv0MZMkKm4NOBAnEwwyDrKHqfNIzPMVyp-V6DvJh8NTpxWtzYckfZbZ-l6cOvb9rVumxoP-FuCqY3N1kvINJh24wvIi078888Sdip0lnd5Qbqd_knVUkX_eOMBSqqV42dGoy3_6BlOxXXT99YtHEIqtMuDit_j8sBeaSDOOfk5DYh9I4qb648P4T7Wn0ccBHTnmwPD1Q-HCgNr_x1g1IyMnBqPoHmXUZmefmg6fk0eC1rRCipfvVu3JO8M_BCsmHDp8RnJWXJTu97UIC4fkVutjIO9Q4YoOT7e23fYoQziZnmbrOuOYPMdJ9h7Bv65LL-1tL8eai9Ye63SG9jov-0IkPSkCsHG9-wL051nhBo-bxrdniEboLgzW3Pu47wls7zrdSFnxqcM4Jod398s6nNpt0s3RHNhZVyUlRv0diwEvA8U2EQZ5VyyYXfAYrnrUnLaIslLHREz5jCQm8ahl3wYeiCaOZqQODHCDga9_6Hm5xjAd5Ozou3GImY3-bH5cpIDlbtN5jAvqM3VROLQuFSPrw9oABwkYMOnaDQmlEkpP5eqAjuVoc9rs4TCcru-RC83JHEPfm-j0OepV4nuT5bF6qx6VVIs3N0r6yfSwQlQm9RV7HREWBi7qb5IVaWq5YByqyiWESgxOfaJXBYiduIzxf2Po0j4JGVGtMuFd2hf-kUe9pM7TbHYzkEFZzOmZiB2D_FHQtZCpskViquc1TdegdQye_OfZfH5zLeFB7DmtjJ9vrGyLAQAcf1J1bbHl6P8rr74Qjk'></script>
<script src="js/products.js"></script>

</body>
</html>