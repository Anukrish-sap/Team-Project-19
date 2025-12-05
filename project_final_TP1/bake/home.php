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
                <a href="bakes.php" class="btn primary">Shop all products</a>
                <a href="products.php?tag=gluten-free" class="btn secondary">View gluten free range</a>
            </div>
        </div>
    </section>

    <!-- Featured products (static placeholders for now) -->
    <section class="section">
        <h3>Featured Bakes</h3>
        <p class="section-intro">A small taste of what Bakes & Cakes has to offer.</p>

        <div class="card-grid">
    <?php
    if (!empty($bakes)) {
        foreach ($bakes as $bake) {
            if (in_array($bake['bakeID'] , [1,2,3])) {
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
            echo "</article>";
        }
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


<?php include '../components/footer.php'; ?>

<?php include '../components/script.html'; ?>

</body>
</html>