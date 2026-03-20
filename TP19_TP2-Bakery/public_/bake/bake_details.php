<?php

session_start();
require_once 'dbconnect.php';

if (!defined('APP_URL')) define('APP_URL', '/public_/bake');

include '../components/header_unified.php';

$bakeID = isset($_GET['bakeID']) ? (int)$_GET['bakeID'] : 0;
if ($bakeID <= 0) {
  echo "<main class='section'><p>Invalid product.</p></main>";
  include '../components/footer.php';
  include '../components/script.html';
  echo "</body></html>";
  exit();
}

try {
  $sqlBake = "
    SELECT
      b.bakeID,
      b.bakeName,
      b.description,
      b.price,
      b.imageFileName,
      b.bakeTypeID,
      COALESCE(i.amount, 0) AS stockAmount
    FROM bakes b
    LEFT JOIN inventory i ON i.bakeID = b.bakeID
    WHERE b.bakeID = :bakeID
    LIMIT 1
  ";
  $qb = $db->prepare($sqlBake);
  $qb->bindValue(':bakeID', $bakeID, PDO::PARAM_INT);
  $qb->execute();
  $bake = $qb->fetch(PDO::FETCH_ASSOC);

  if (!$bake) {
    echo "<main class='section'><p>Product not found.</p></main>";
    include '../components/footer.php';
    include '../components/script.html';
    echo "</body></html>";
    exit();
  }

  $sqlIng = "
    SELECT ing.ingredientName
    FROM bakeIngredients bi
    JOIN ingredients ing ON ing.ingredientID = bi.ingredientID
    WHERE bi.bakeID = :bakeID
    ORDER BY ing.ingredientName
  ";
  $qi = $db->prepare($sqlIng);
  $qi->bindValue(':bakeID', $bakeID, PDO::PARAM_INT);
  $qi->execute();
  $ingredients = $qi->fetchAll(PDO::FETCH_COLUMN);

  // Reviews for this bake
  $sqlReviews = "
    SELECT br.reviewID, br.userID, br.rating, br.reviewText, br.createdAt, u.name
    FROM bakeReviews br
    JOIN users u ON u.userID = br.userID
    WHERE br.bakeID = :bakeID
    ORDER BY br.createdAt DESC
  ";
  $qr = $db->prepare($sqlReviews);
  $qr->bindValue(':bakeID', $bakeID, PDO::PARAM_INT);
  $qr->execute();
  $reviews = $qr->fetchAll(PDO::FETCH_ASSOC);

  // Review summary
  $sqlReviewSummary = "
    SELECT COUNT(*) AS reviewCount, AVG(rating) AS avgRating
    FROM bakeReviews
    WHERE bakeID = :bakeID
  ";
  $qs = $db->prepare($sqlReviewSummary);
  $qs->bindValue(':bakeID', $bakeID, PDO::PARAM_INT);
  $qs->execute();
  $reviewSummary = $qs->fetch(PDO::FETCH_ASSOC);

  $reviewCount = (int)($reviewSummary['reviewCount'] ?? 0);
  $avgRating = $reviewSummary['avgRating'] !== null
    ? round((float)$reviewSummary['avgRating'], 1)
    : null;

  // Has the logged-in user already reviewed this bake?
  $userAlreadyReviewed = false;
  if (isset($_SESSION['userID'])) {
    $sqlOwn = "SELECT COUNT(*) FROM bakeReviews WHERE bakeID = :bakeID AND userID = :userID";
    $stmtOwn = $db->prepare($sqlOwn);
    $stmtOwn->bindValue(':bakeID', $bakeID, PDO::PARAM_INT);
    $stmtOwn->bindValue(':userID', (int)$_SESSION['userID'], PDO::PARAM_INT);
    $stmtOwn->execute();
    $userAlreadyReviewed = ((int)$stmtOwn->fetchColumn() > 0);
  }

} catch (PDOException $e) {
  echo "<main class='section'><p>Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p></main>";
  include '../components/footer.php';
  include '../components/script.html';
  echo "</body></html>";
  exit();
}

$isCake    = ((int)$bake['bakeTypeID'] === 1);
$baseSize  = 6;
$basePrice = (float)$bake['price'];
$sizes     = [6,7,8,9,10,11,12,13,14];

$servesMap = [
  6  => "8–10",
  7  => "10–12",
  8  => "12–16",
  9  => "16–20",
  10 => "20–24",
  11 => "24–30",
  12 => "30–36",
  13 => "36–44",
  14 => "44–52",
];

$initialSize   = 6;
$initialPrice  = $basePrice;
$initialServes = $servesMap[$initialSize] ?? "—";

$stock = (int)$bake['stockAmount'];
$img   = !empty($bake['imageFileName']) ? $bake['imageFileName'] : null;
$desc  = !empty($bake['description']) ? $bake['description'] : '';
?>

<main>
  <section class="section">
    <a href="<?= APP_URL ?>/bakes.php" class="back-btn">← Back to Shop</a>

    <div class="details-layout">

      <!-- LEFT IMAGE -->
      <div class="details-image-wrap">
        <?php if ($img): ?>
          <img
            class="details-image"
            src="<?= APP_URL ?>/img/uploads/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
            alt="<?= htmlspecialchars($bake['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
          >
        <?php else: ?>
          <div class="details-image placeholder-image">No image</div>
        <?php endif; ?>
      </div>

      <!-- RIGHT INFO -->
      <div class="details-right">

        <h1 class="details-title"><?= htmlspecialchars($bake['bakeName'], ENT_QUOTES, 'UTF-8') ?></h1>

        <div class="review-summary">
          <?php if ($reviewCount > 0): ?>
            <span class="review-stars">
              <?= str_repeat('★', (int)round($avgRating)) . str_repeat('☆', 5 - (int)round($avgRating)) ?>
            </span>
            <span class="review-text">
              <?= htmlspecialchars((string)$avgRating, ENT_QUOTES, 'UTF-8') ?>/5
              (<?= $reviewCount ?> review<?= $reviewCount !== 1 ? 's' : '' ?>)
            </span>
          <?php else: ?>
            <span class="review-text">No reviews yet</span>
          <?php endif; ?>
        </div>

        <div class="price-row">
          <span class="from muted">From</span>
          <div class="price-big">£<span id="priceDisplay"><?= number_format((float)$initialPrice, 2) ?></span></div>
        </div>

        <?php if ($stock > 0): ?>
          <p class="stock-line muted">In stock: <strong><?= $stock ?></strong></p>
        <?php else: ?>
          <p class="out-stock">Out of stock</p>
        <?php endif; ?>

        <p class="details-desc">
          <?= $desc !== '' ? htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') : 'No description available.' ?>
        </p>

        <?php if ($isCake): ?>
          <div class="option-block">
            <h3 class="block-title">Size &amp; Serving</h3>
            <div class="size-grid" id="sizeGrid">
              <?php foreach ($sizes as $s): ?>
                <button
                  type="button"
                  class="size-card <?= $s === $initialSize ? 'active' : '' ?>"
                  data-size="<?= (int)$s ?>"
                  data-serves="<?= htmlspecialchars($servesMap[$s] ?? '—', ENT_QUOTES, 'UTF-8') ?>"
                >
                  <div class="inch"><?= (int)$s ?>"</div>
                  <div class="serves">serves <?= htmlspecialchars($servesMap[$s] ?? '—', ENT_QUOTES, 'UTF-8') ?></div>
                </button>
              <?php endforeach; ?>
            </div>
            <p class="muted selected-line">
              <strong>Selected:</strong> <span id="selectedSize"><?= (int)$initialSize ?></span>"
              · <strong>Serves:</strong> <span id="servesDisplay"><?= htmlspecialchars($initialServes, ENT_QUOTES, 'UTF-8') ?></span>
            </p>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['userID'])): ?>
          <form action="<?= APP_URL ?>/basket_add.php" method="post" class="details-form">

            <input type="hidden" name="bakeID" value="<?= (int)$bakeID ?>">

            <?php if ($isCake): ?>
              <input type="hidden" name="size" id="basketSize" value="<?= (int)$initialSize ?>">
              <input type="hidden" name="unitPrice" id="basketUnitPrice" value="<?= number_format((float)$initialPrice, 2, '.', '') ?>">
            <?php else: ?>
              <input type="hidden" name="size" value="0">
              <input type="hidden" name="unitPrice" value="<?= number_format((float)$initialPrice, 2, '.', '') ?>">
            <?php endif; ?>

            <?php if ($isCake): ?>
              <div class="option-block">
                <h3 class="block-title">Personalised Message On Cake</h3>
                <textarea
                  class="message-box"
                  name="cakeMessage"
                  id="cakeMessage"
                  maxlength="40"
                  placeholder="e.g. Happy Birthday!"
                  <?= ($stock <= 0) ? 'disabled' : '' ?>
                ></textarea>
                <div class="muted small-note">Max 40 characters.</div>
              </div>
            <?php endif; ?>

            <div class="buy-row">
              <div class="qty-row">
                <button type="button" class="qty-btn" id="qtyMinus" <?= ($stock <= 0) ? 'disabled' : '' ?>>−</button>
                <input
                  class="qty-input"
                  type="number"
                  name="qty"
                  id="qtyInput"
                  value="1"
                  min="1"
                  max="<?= $stock ?>"
                  <?= ($stock <= 0) ? 'disabled' : '' ?>
                >
                <button type="button" class="qty-btn" id="qtyPlus" <?= ($stock <= 0) ? 'disabled' : '' ?>>+</button>
              </div>
              <button type="submit" class="add-btn" <?= ($stock <= 0) ? 'disabled' : '' ?>>
                Add to Basket
              </button>
            </div>

          </form>

        <?php else: ?>
          <p class="muted">
            <a href="<?= APP_URL ?>/loginpage.php">Log in</a> to add this to your basket.
          </p>
        <?php endif; ?>

      </div>
    </div>

    <!-- BOTTOM TABS -->
    <div class="tabs-bar">
      <button class="active" data-tab="desc" type="button">Description</button>
      <button data-tab="ingredients" type="button">Ingredients & Allergy Advice</button>
      <button data-tab="nutrition" type="button">Nutritional Information</button>
      <button data-tab="reviews" type="button">
        Reviews<?= $reviewCount > 0 ? ' (' . $reviewCount . ')' : '' ?>
      </button>
    </div>

    <div class="tab-panel" id="tab-desc">
      <p><?= $desc !== '' ? htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') : 'No description available.' ?></p>
    </div>

    <div class="tab-panel" id="tab-ingredients" style="display:none;">
      <?php if (empty($ingredients)): ?>
        <p>No ingredients listed.</p>
      <?php else: ?>
        <p><?= htmlspecialchars(implode(', ', $ingredients), ENT_QUOTES, 'UTF-8') ?></p>
        <p class="muted">Allergy advice: please contact us if you have any allergies.</p>
      <?php endif; ?>
    </div>

    <div class="tab-panel" id="tab-nutrition" style="display:none;">
      <p class="muted">Nutritional information will be added soon.</p>
    </div>

    <!-- REVIEWS TAB -->
    <div class="tab-panel" id="tab-reviews" style="display:none;">
      <div class="review-tab-wrap">

        <h3>Customer Reviews</h3>

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['review_success'])): ?>
          <div class="review-flash review-flash--success">
            <span>✓</span>
            <?= htmlspecialchars($_SESSION['review_success'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <?php unset($_SESSION['review_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['review_error'])): ?>
          <div class="review-flash review-flash--error">
            <span>✕</span>
            <?= htmlspecialchars($_SESSION['review_error'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <?php unset($_SESSION['review_error']); ?>
        <?php endif; ?>

        <!-- Write a review form -->
        <?php if (isset($_SESSION['userID']) && !$userAlreadyReviewed): ?>
          <form action="<?= APP_URL ?>/review_add.php" method="post" class="review-form" id="review-form-wrap">
            <input type="hidden" name="bakeID" value="<?= (int)$bakeID ?>">

            <p class="review-form-title">Write a Review</p>

            <!-- Star rating picker -->
            <div>
              <p class="review-field-label">Rating</p>
              <div class="star-picker">
                <?php
                  $labels = [5=>'Excellent', 4=>'Very Good', 3=>'Good', 2=>'Poor', 1=>'Very Poor'];
                  for ($i = 5; $i >= 1; $i--):
                ?>
                  <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
                  <label for="star<?= $i ?>" class="star-label">
                    <span class="star-label__stars"><?= str_repeat('★', $i) ?></span>
                    <span><?= $labels[$i] ?></span>
                  </label>
                <?php endfor; ?>
              </div>
            </div>

            <!-- Review textarea -->
            <div>
              <p class="review-field-label">Your Review</p>
              <textarea
                name="reviewText"
                id="reviewText"
                rows="4"
                maxlength="1000"
                placeholder="Share your thoughts on this product…"
                required
              ></textarea>
              <p class="review-char-count" id="charCount">0 / 1000</p>
            </div>

            <button type="submit" class="btn primary review-submit-btn">
              ✓ &nbsp;Submit Review
            </button>

          </form>

          <script>
            // Star picker highlight
            document.querySelectorAll('.star-picker input[type=radio]').forEach(radio => {
              radio.addEventListener('change', () => {
                document.querySelectorAll('.star-label').forEach(lbl => lbl.classList.remove('is-selected'));
                const chosen = document.querySelector('.star-picker input:checked');
                if (chosen) document.querySelector(`label[for="${chosen.id}"]`).classList.add('is-selected');
              });
            });
            // Char counter
            const ta = document.getElementById('reviewText');
            const cc = document.getElementById('charCount');
            if (ta && cc) {
              ta.addEventListener('input', () => { cc.textContent = ta.value.length + ' / 1000'; });
            }
          </script>

        <?php elseif ($userAlreadyReviewed): ?>
          <div class="review-flash review-flash--info" id="already-reviewed-notice">
            <span>★</span>
            You've already reviewed this product. Delete your review below if you'd like to update it.
          </div>
        <?php else: ?>
          <p class="review-login-prompt">
            <a href="<?= APP_URL ?>/loginpage.php">Log in</a> to leave a review.
          </p>
        <?php endif; ?>

        <div class="reviews-divider"></div>

        <!-- Reviews list -->
        <div class="reviews-list">
          <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review):
              $isOwner = isset($_SESSION['userID']) && (int)$_SESSION['userID'] === (int)$review['userID'];
            ?>
              <div class="review-card" id="review-<?= (int)$review['reviewID'] ?>">
                <div class="review-card-top">
                  <div class="review-card-meta">
                    <strong><?= htmlspecialchars($review['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <time datetime="<?= htmlspecialchars($review['createdAt'], ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars(date('j M Y', strtotime($review['createdAt'])), ENT_QUOTES, 'UTF-8') ?>
                    </time>
                  </div>
                  <div class="review-card-actions">
                    <span class="review-stars-inline" aria-label="<?= (int)$review['rating'] ?> out of 5 stars">
                      <?= str_repeat('★', (int)$review['rating']) . str_repeat('☆', 5 - (int)$review['rating']) ?>
                    </span>
                    <?php if ($isOwner): ?>
                      <button
                        type="button"
                        class="review-delete-btn"
                        onclick="deleteReview(<?= (int)$review['reviewID'] ?>, this)"
                        title="Delete your review"
                      >
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        Remove
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
                <p><?= nl2br(htmlspecialchars($review['reviewText'], ENT_QUOTES, 'UTF-8')) ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="reviews-list-empty">No reviews yet — be the first to share your thoughts!</p>
          <?php endif; ?>
        </div>

      </div>
    </div>

  </section>
</main>

<script>
  function deleteReview(reviewID, btn) {
    if (!confirm('Remove your review? This cannot be undone.')) return;

    btn.disabled = true;
    btn.textContent = 'Removing…';

    fetch('<?= APP_URL ?>/review_delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'reviewID=' + encodeURIComponent(reviewID)
    })
    .then(r => r.text())
    .then(raw => {
      let data = {};
      try { data = JSON.parse(raw); } catch(e) { /* ignore parse errors */ }

      if (data.success === false) {
        alert(data.message || 'Could not delete review. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Remove';
        return;
      }
     
      const card = document.getElementById('review-' + reviewID);
      if (card) {
        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        card.style.opacity = '0';
        card.style.transform = 'translateY(-6px)';
      }
      setTimeout(() => {
        window.location.href = window.location.pathname + '?bakeID=<?= (int)$bakeID ?>#tab-reviews';
      }, 350);
    })
    .catch(() => {
      
      window.location.href = window.location.pathname + '?bakeID=<?= (int)$bakeID ?>#tab-reviews';
    });
  }
</script>

<script>
  const isCake = <?= $isCake ? 'true' : 'false' ?>;
  const baseSize = <?= (int)$baseSize ?>;
  const basePrice = <?= json_encode((float)$basePrice) ?>;

  const priceEl = document.getElementById("priceDisplay");
  const servesEl = document.getElementById("servesDisplay");
  const selectedSizeEl = document.getElementById("selectedSize");
  const sizeCards = document.querySelectorAll(".size-card");
  const basketSize = document.getElementById("basketSize");
  const basketUnitPrice = document.getElementById("basketUnitPrice");

  function calcPrice(size){
    if (!isCake) return basePrice;
    if (size === baseSize) return basePrice;
    const d = size - baseSize;
    const raw = basePrice * Math.pow(1.1, d);
    return Math.ceil(raw);
  }

  function setSize(btn){
    const size = parseInt(btn.dataset.size, 10);
    const serves = btn.dataset.serves || "—";
    const price = calcPrice(size);

    if (selectedSizeEl) selectedSizeEl.textContent = size;
    if (servesEl) servesEl.textContent = serves;
    if (priceEl) priceEl.textContent = Number.isInteger(price) ? price : price.toFixed(2);
    if (basketSize) basketSize.value = size;
    if (basketUnitPrice) basketUnitPrice.value = price.toFixed(2);
  }

  if (isCake) {
    sizeCards.forEach(btn => {
      btn.addEventListener("click", () => {
        sizeCards.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        setSize(btn);
      });
    });
    const active = document.querySelector(".size-card.active");
    if (active) setSize(active);
  }

  const qtyMinus = document.getElementById("qtyMinus");
  const qtyPlus  = document.getElementById("qtyPlus");
  const qtyInput = document.getElementById("qtyInput");

  function clampQty(v){
    const min = qtyInput ? parseInt(qtyInput.min || "1", 10) : 1;
    const max = qtyInput ? parseInt(qtyInput.max || "9999", 10) : 9999;
    if (isNaN(v)) v = min;
    return Math.max(min, Math.min(max, v));
  }

  if (qtyMinus && qtyInput) {
    qtyMinus.addEventListener("click", () => {
      qtyInput.value = clampQty(parseInt(qtyInput.value || "1", 10) - 1);
    });
  }
  if (qtyPlus && qtyInput) {
    qtyPlus.addEventListener("click", () => {
      qtyInput.value = clampQty(parseInt(qtyInput.value || "1", 10) + 1);
    });
  }
  if (qtyInput) {
    qtyInput.addEventListener("input", () => {
      qtyInput.value = clampQty(parseInt(qtyInput.value || "1", 10));
    });
  }

  const tabButtons = document.querySelectorAll(".tabs-bar button");
  const panels = {
    desc: document.getElementById("tab-desc"),
    ingredients: document.getElementById("tab-ingredients"),
    nutrition: document.getElementById("tab-nutrition"),
    reviews: document.getElementById("tab-reviews"),
  };

  tabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      tabButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      Object.values(panels).forEach(p => p.style.display = "none");
      if (panels[btn.dataset.tab]) panels[btn.dataset.tab].style.display = "block";
    });
  });

  const hash = window.location.hash.replace('#', '');
  if (hash && panels[hash]) {
    tabButtons.forEach(b => b.classList.remove("active"));
    const matchBtn = document.querySelector(`.tabs-bar button[data-tab="${hash}"]`);
    if (matchBtn) matchBtn.classList.add("active");
    Object.values(panels).forEach(p => p.style.display = "none");
    panels[hash].style.display = "block";
  }
</script>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
</body>
</html>