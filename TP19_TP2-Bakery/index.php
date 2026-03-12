<?php
// Debug (remove later)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Web base for browser URLs (links/images/css)
define('BASE_URL', '/public_/bake');

// File base for includes on disk (index.php is in /public_html)
define('BASE_PATH', __DIR__ . '/public_');

// DB connect is inside: public_html/public_/bake/dbconnect.php
require BASE_PATH . '/bake/dbconnect.php';

// Header is inside: public_html/public_/components/header_unified.php
include BASE_PATH . '/components/header_unified.php';

if (isset($_SESSION['logout'])) {
    echo "<p style='color: red;'>" . $_SESSION['logout'] . "</p>";
    unset($_SESSION['logout']);
}

try {
    $category = isset($_GET['category'])
        ? htmlspecialchars($_GET['category'], ENT_QUOTES, 'UTF-8')
        : null;

    $categoryMap = [
        'cakes'    => 1,
        'cookies'  => 2,
        'pastries' => 3,
        'bread'    => 4
    ];

    $sql = "SELECT bakes.bakeID, bakes.bakeName, bakes.description, bakes.price,
                   bakes.bakeTypeID, bakes.imageFileName
            FROM bakes
            WHERE 1=1";

    if ($category && isset($categoryMap[$category])) {
        $sql .= " AND bakes.bakeTypeID = :bakeTypeID";
    }

    $query = $db->prepare($sql);

    if ($category && isset($categoryMap[$category])) {
        $query->bindValue(':bakeTypeID', $categoryMap[$category], PDO::PARAM_INT);
    }

    $query->execute();
    $bakes = $query->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit();
}
?>

<main>

  <section class="home-hero">
    <div class="home-hero-inner">
      <span class="home-hero-label">Welcome to Bakes &amp; Cakes</span>
      <h1>Freshly baked treats for every occasion</h1>
      <p>From rich chocolate cakes to soft cookies and warm bread, Bakes &amp; Cakes brings fresh bakery goodness straight to your door.</p>
      <div class="home-hero-btns">
        <a href="<?= BASE_URL ?>/bakes.php" class="btn primary">Shop all products</a>
        <a href="<?= BASE_URL ?>/bakes.php?tag=gluten-free" class="btn secondary">View gluten free range</a>
      </div>
    </div>
  </section>

  <section class="home-section">
    <div class="home-section-inner">
      <div class="home-section-header">
        <span class="home-section-label">Handpicked for you</span>
        <h2>Featured Bakes</h2>
        <p>A small taste of what Bakes &amp; Cakes has to offer.</p>
      </div>
      <div class="featured-grid">
        <?php
        if (!empty($bakes)) {
          foreach ($bakes as $bake) {
            if (in_array((int)$bake['bakeID'], [1,2,3], true)) {
              echo "<div class='featured-card'>";
              echo "<div class='featured-card-img'>";
              if (!empty($bake['imageFileName'])) {
                $imagePath = BASE_URL . "/img/uploads/" . htmlspecialchars($bake['imageFileName'], ENT_QUOTES, 'UTF-8');
                echo "<img src='{$imagePath}' alt='" . htmlspecialchars($bake['bakeName'], ENT_QUOTES, 'UTF-8') . "'>";
              } else {
                echo "🎂";
              }
              echo "</div>";
              echo "<div class='featured-card-body'>";
              echo "<h3>" . htmlspecialchars($bake['bakeName'], ENT_QUOTES, 'UTF-8') . "</h3>";
              echo "<p>" . htmlspecialchars($bake['description'], ENT_QUOTES, 'UTF-8') . "</p>";
              echo "<span class='featured-card-price'>£" . number_format((float)$bake['price'], 2, '.', '') . "</span>";
              echo "</div></div>";
            }
          }
        } else {
          echo "<p>No bakes found.</p>";
        }
        ?>
      </div>
    </div>
  </section>

  <section class="home-section home-section-alt">
    <div class="home-section-inner">
      <div class="home-section-header">
        <span class="home-section-label">Explore</span>
        <h2>Browse by Category</h2>
      </div>
      <div class="categories-grid">
        <a href="<?= BASE_URL ?>/bakes.php?category=cakes" class="category-tile">
          <span class="category-tile-icon">🎂</span>
          <h3>Cakes</h3>
          <p>Celebration cakes, layer cakes, and loaf cakes for every event.</p>
        </a>
        <a href="<?= BASE_URL ?>/bakes.php?category=cookies" class="category-tile">
          <span class="category-tile-icon">🍪</span>
          <h3>Cookies</h3>
          <p>Soft, chewy, or crunchy cookies baked fresh daily.</p>
        </a>
        <a href="<?= BASE_URL ?>/bakes.php?category=pastries" class="category-tile">
          <span class="category-tile-icon">🥐</span>
          <h3>Pastries</h3>
          <p>Buttery croissants, danishes, and puff pastry delights.</p>
        </a>
        <a href="<?= BASE_URL ?>/bakes.php?category=bread" class="category-tile">
          <span class="category-tile-icon">🍞</span>
          <h3>Bread</h3>
          <p>Fresh loaves, rolls, and specialty breads.</p>
        </a>
      </div>
    </div>
  </section>

  <section class="home-section">
    <div class="home-section-inner">
      <div class="home-section-header">
        <span class="home-section-label">Dietary needs</span>
        <h2>Allergy Friendly Options</h2>
        <p>We understand how important it is to feel safe when ordering baked goods.</p>
      </div>
      <div class="allergy-cards">
        <div class="allergy-card">
          <div class="allergy-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <h4>Gluten Free Range</h4>
            <p>Look for the <span class="badge gluten-free">Gluten free</span> badge when browsing. These items are baked with gluten free ingredients.</p>
          </div>
        </div>
        <div class="allergy-card">
          <div class="allergy-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
          </div>
          <div>
            <h4>More Tags Coming Soon</h4>
            <p>We're expanding to support nut free and vegan options so you can filter for exactly what fits your needs.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="home-section home-section-alt">
    <div class="home-section-inner">
      <div class="about-strip">
        <div class="about-strip-text">
          <h2>About Bakes &amp; Cakes</h2>
          <p>A modern online bakery created by a student team project. Our goal is to offer a professional bakery experience that lets customers browse, filter, and order their favourite treats from home.</p>
          <a href="<?= BASE_URL ?>/about.php" class="btn secondary">Learn more about us</a>
        </div>
      </div>
    </div>
  </section>

  <section class="home-section">
    <div class="home-section-inner">
      <div class="contact-strip">
        <div class="contact-strip-text">
          <h2>Got a question or custom order?</h2>
          <p>Use our contact form to get in touch about custom cakes, large orders, or allergy questions.</p>
        </div>
        <a href="<?= BASE_URL ?>/contact.php" class="btn primary">Contact us</a>
      </div>
    </div>
  </section>

  <section class="home-section home-section-alt">
    <div class="home-section-inner">
      <div class="newsletter-block">
        <h2>Stay updated</h2>
        <p>Sign up to hear about new bakes, seasonal specials, and discounts.</p>
        <form action="#" method="post">
          <div class="newsletter-row">
            <label for="newsletter-email" class="visually-hidden">Email address</label>
            <input type="email" id="newsletter-email" name="newsletter_email" placeholder="Enter your email" required>
            <button type="submit" class="btn primary small">Sign up</button>
          </div>
        </form>
      </div>
    </div>
  </section>

</main>

<?php include BASE_PATH . '/components/footer.php'; ?>
<?php include BASE_PATH . '/components/script.html'; ?>

</body>
</html>
