<?php
// index.php - Bakes & Cakes homepage
session_start();
include "dbconnect.php";

try {
 $category = isset($_GET['category']) ? htmlspecialchars($_GET['category'], ENT_QUOTES, 'UTF-8') : null;

 $categoryMap = [
    'cakes' => 1,
    'cookies' => 2,
    'pastries' => 3,
    'bread' => 4
 ];

 $sql = "select bakes.bakeID, bakes.bakeName, bakes.description, bakes.price, bakes.bakeTypeID, bakes.imageFileName from bakes where 1=1";

 if($category && isset($categoryMap[$category])) {
   $sql .= " AND bakes.bakeTypeID = :bakeTypeID";
 }

 $query = $db -> prepare($sql);

 if ($category && isset($categoryMap[$category])) {
    $query->bindValue(':bakeTypeID', $categoryMap[$category], PDO::PARAM_INT);

 }
 $query->execute();
    $bakes = $query->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>



















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bakes & Cakes | Your home for all your bakes and cakes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="light">

<header class="site-header">
    <div class="logo-area">
        
        <img src="img/logo.png" alt="Bakes & Cakes logo" class="logo">
        <div class="brand-text">
            <h1>Bakes & Cakes</h1>
            <p class="tagline">Your home for all your bakes and cakes</p>
        </div>
    </div>

    <nav class="main-nav">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="products.php"  class="active" >Products</a></li>
            <li><a href="loginpage.php">login</a></li>
            <li class="has-dropdown">
                <a href="categories.php">Categories</a>
                <ul class="dropdown">
                    <li><a href="products.php?category=cakes">Cakes</a></li>
                    <li><a href="products.php?category=cookies">Cookies</a></li>
                    <li><a href="products.php?category=pastries">Pastries</a></li>
                    <li><a href="products.php?category=bread">Bread</a></li>
                </ul>
            </li>
            <li><a href="basket.php">Basket</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

    <button id="theme-toggle" aria-label="Toggle light or dark mode">
       Dark mode
    </button>
</header>

   
            
            
            
            
<main>
    <!-- Hero section -->
    <section class="hero">
        <div class="hero-content">
            <h2>Freshly baked treats for every occasion</h2>
            <p>
                From rich chocolate cakes to soft cookies and warm bread,
                Bakes & Cakes brings fresh bakery goodness to your door.
            </p>
            <div class="hero-buttons">
                <a href="products.php" class="btn primary">Shop all products</a>
                <a href="products.php?tag=gluten-free" class="btn secondary">View gluten free range</a>
            </div>
        </div>
    </section>

    <!-- Featured products (static placeholders for now) -->
    <section class="section">
        <h3>Our Bakes</h3>
        <p class="section-intro">View all our bakes we have to offer.</p>

        <div class="card-grid">
    <?php
    if (!empty($bakes)) {
        foreach ($bakes as $bake) {
            echo "<article class='card product-card'>";
            echo "<div class='product-image placeholder-image'>";
            if (!empty($bake['imageFileName'])) {
            $imagePath = "img/uploads/" . htmlspecialchars($bake['imageFileName'], ENT_QUOTES, 'UTF-8');
            echo "<img src='" . $imagePath . "' alt='Bake Image' style='max-width: 300px; height: auto;'><br>";
        }
        echo "</div>";
            echo "<h4>" . htmlspecialchars($bake['bakeName'], ENT_QUOTES, 'UTF-8') . "</h4>";
            echo "<p>" . htmlspecialchars($bake['description'], ENT_QUOTES, 'UTF-8') . "</p>";
            echo "<p class='price'>£" . number_format((float)$bake['price'], 2, '.', '') . "</p>";
            echo "<a href='product.php?id=" . $bake['bakeID'] . "' class='btn small'>View details</a>";
            echo "</article>";
        }
    } else {
        echo "<p>No bakes found.</p>";
    }
    ?>

        </div>
    </section>

    <!-- Categories section -->
    <section class="section section-alt">
        <h3>Browse by category</h3>

        <div class="card-grid categories-grid">
            <a href="products.php?category=cakes" class="card category-card">
                <h4>Cakes</h4>
                <p>Celebration cakes, layer cakes, and loaf cakes for every event.</p>
            </a>

            <a href="products.php?category=cookies" class="card category-card">
                <h4>Cookies</h4>
                <p>Soft, chewy, or crunchy cookies baked fresh daily.</p>
            </a>

            <a href="products.php?category=pastries" class="card category-card">
                <h4>Pastries</h4>
                <p>Buttery croissants, danishes, and puff pastry delights.</p>
            </a>

            <a href="products.php?category=bread" class="card category-card">
                <h4>Bread</h4>
                <p>Fresh loaves, rolls, and specialty breads.</p>
            </a>
        </div>
    </section>

    <!-- Allergy friendly focus -->
    <section class="section">
        <h3>Allergy friendly options</h3>
        <p class="section-intro">
            We understand how important it is to feel safe when ordering baked goods.
            Our range starts with gluten free items, and the website is designed so the team can add more
            dietary tags in the future.
        </p>

        <div class="allergy-grid">
            <div>
                <h4>Gluten free range</h4>
                <p>
                    Look for the <span class="badge gluten-free">Gluten free</span> badge when browsing products.
                    These items are baked with gluten free ingredients.
                </p>
            </div>
            <div>
                <h4>Future dietary tags</h4>
                <p>
                    The system will be extended to support more tags such as nut free and vegan,
                    so customers can filter products that fit their needs.
                </p>
            </div>
        </div>
    </section>

    <!-- About preview -->
    <section class="section section-alt">
        <div class="about-preview">
            <div>
                <h3>About Bakes & Cakes</h3>
                <p>
                    Bakes & Cakes is a modern online bakery created by a student team project.
                    Our goal is to offer a professional bakery experience that lets customers browse,
                    filter, and order their favourite treats from home.
                </p>
                <a href="about.php" class="btn secondary">Learn more about us</a>
            </div>
        </div>
    </section>

    <!-- Contact preview -->
    <section class="section">
        <h3>Got a question or custom order idea?</h3>
        <p class="section-intro">
            Use our Contact form to get in touch about custom cakes, large orders, or allergy questions.
        </p>
        <a href="contact.php" class="btn primary">Contact us</a>
    </section>

    <!-- Newsletter (optional) -->
    <section class="section section-alt">
        <h3>Stay updated</h3>
        <p class="section-intro">
            Sign up to hear about new bakes, seasonal specials, and discounts.
        </p>
        <form class="newsletter-form" action="#" method="post">
            <label for="newsletter-email" class="visually-hidden">Email address</label>
            <input type="email" id="newsletter-email" name="newsletter_email" placeholder="Enter your email" required>
            <button type="submit" class="btn primary small">Sign up</button>
        </form>
    </section>
</main>

<?php include '../components/footer.html'; ?>

<script>
// Simple light or dark mode toggle
const toggleButton = document.getElementById('theme-toggle');
const body = document.body;

toggleButton.addEventListener('click', function () {
    if (body.classList.contains('light')) {
        body.classList.remove('light');
        body.classList.add('dark');
        toggleButton.textContent = 'Light mode';
    } else {
        body.classList.remove('dark');
        body.classList.add('light');
        toggleButton.textContent = 'Dark mode';
    }
});
</script>

</body>
</html>

