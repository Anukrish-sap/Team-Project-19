<?php
session_start();
require_once 'dbconnect.php';

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

include '../components/header_unified.php';

// ─── Map quiz answers → bakeTypeID ───────────────────────────────────────────
// bakeTypeID: 1=cakes, 2=cookies, 3=pastries, 4=bread
$matchedBakes   = [];
$quizSubmitted  = false;
$resultHeading  = '';
$resultSubtext  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['q_taste'])) {
    $quizSubmitted = true;

    $taste    = $_POST['q_taste']   ?? '';   // sweet | savoury | both
    $calorie  = $_POST['q_calorie'] ?? '';   // light | indulgent | dontmind
    $texture  = $_POST['q_texture'] ?? '';   // soft | crunchy | flaky | hearty
    $occasion = $_POST['q_occasion'] ?? '';  // everyday | special | breakfast | snack

    // Decide which category (bakeTypeID) fits best
    if ($taste === 'savoury') {
        $targetType = 4; // bread
        $resultHeading = 'Savoury Lover';
        $resultSubtext = 'You\'re all about bold, satisfying flavours. We\'ve matched you with our best bread and savoury bakes.';
    } elseif ($taste === 'sweet' && $texture === 'crunchy') {
        $targetType = 2; // cookies
        $resultHeading = 'The Cookie Monster';
        $resultSubtext = 'Crisp edges, chewy centres — you know what you want. Here are your perfect cookies.';
    } elseif ($taste === 'sweet' && $texture === 'flaky') {
        $targetType = 3; // pastries
        $resultHeading = 'Pastry Perfectionist';
        $resultSubtext = 'Buttery layers and delicate bakes are your thing. These pastries were made for you.';
    } elseif ($taste === 'both' || $occasion === 'breakfast') {
        $targetType = 3; // pastries — good for both sweet/savoury and breakfast
        $resultHeading = 'The Balanced Baker';
        $resultSubtext = 'Sweet or savoury, you want it all. Our pastries hit that perfect middle ground.';
    } elseif ($calorie === 'light') {
        $targetType = 2; // cookies — portioned treats
        $resultHeading = 'Mindful Muncher';
        $resultSubtext = 'Treating yourself doesn\'t mean going overboard. These perfectly-sized bakes are just right.';
    } else {
        $targetType = 1; // cakes — default indulgent sweet
        $resultHeading = 'Cake Connoisseur';
        $resultSubtext = 'Go big or go home. Life\'s too short for anything less than a proper cake.';
    }

    // Query real products from the database
    try {
        $stmt = $db->prepare("
            SELECT
                bakes.bakeID,
                bakes.bakeName,
                bakes.description,
                bakes.price,
                bakes.imageFileName,
                COALESCE(inventory.amount, 0) AS stockAmount
            FROM bakes
            LEFT JOIN inventory ON inventory.bakeID = bakes.bakeID
            WHERE bakes.bakeTypeID = :typeID
            ORDER BY stockAmount DESC, bakes.price ASC
            LIMIT 4
        ");
        $stmt->bindValue(':typeID', $targetType, PDO::PARAM_INT);
        $stmt->execute();
        $matchedBakes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $matchedBakes = [];
    }
}
?>

<style>
/* ── Quiz page styles ─────────────────────────────── */
.quiz-hero {
    text-align: center;
    padding: 3rem 1rem 1.5rem;
}
.quiz-hero .eyebrow {
    font-size: 0.78rem;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--accent, #8b2a7a);
    font-weight: 600;
    margin-bottom: 0.6rem;
}
.quiz-hero h2 {
    font-size: clamp(1.8rem, 5vw, 2.6rem);
    margin-bottom: 0.75rem;
}
.quiz-hero p {
    color: var(--text-muted, #666);
    max-width: 480px;
    margin: 0 auto;
    line-height: 1.7;
}

/* Steps */
.quiz-steps {
    display: flex;
    justify-content: center;
    gap: 0.4rem;
    margin: 1.5rem 0 0;
}
.quiz-step-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--border-color, #ddd);
    transition: background 0.3s;
}
.quiz-step-dot.active { background: var(--accent, #8b2a7a); }
.quiz-step-dot.done   { background: var(--accent, #8b2a7a); opacity: 0.4; }

/* Quiz wrapper */
.quiz-wrapper {
    max-width: 640px;
    margin: 0 auto;
    padding: 0 1rem 3rem;
}

/* Question slide */
.quiz-slide {
    display: none;
    animation: qFadeIn 0.35s ease both;
}
.quiz-slide.active { display: block; }

@keyframes qFadeIn {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}

.quiz-question-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--accent, #8b2a7a);
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.quiz-question-text {
    font-size: clamp(1.1rem, 3vw, 1.35rem);
    font-weight: 700;
    margin-bottom: 1.4rem;
    color: var(--heading-color, #1a1a1a);
}

/* Option buttons */
.quiz-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.quiz-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.9rem 1rem;
    background: var(--card-bg, #fff);
    border: 1.5px solid var(--border-color, #e0e0e0);
    border-radius: 0.85rem;
    cursor: pointer;
    transition: all 0.18s ease;
    text-align: left;
    font-family: inherit;
    font-size: 0.92rem;
    color: var(--text-color, #333);
    width: 100%;
}
.quiz-option:hover {
    border-color: var(--accent, #8b2a7a);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}
.quiz-option.selected {
    border-color: var(--accent, #8b2a7a);
    background: color-mix(in srgb, var(--accent, #8b2a7a) 8%, var(--card-bg, #fff));
    box-shadow: 0 2px 12px rgba(139,42,122,0.15);
}
.quiz-option-emoji { font-size: 1.4rem; flex-shrink: 0; }
.quiz-option-text  { font-weight: 500; }

/* Nav */
.quiz-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}
.btn-quiz-back {
    background: none;
    border: 1.5px solid var(--border-color, #ddd);
    color: var(--text-muted, #888);
    border-radius: 0.6rem;
    padding: 0.55rem 1.2rem;
    font-size: 0.88rem;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.18s;
}
.btn-quiz-back:hover { border-color: var(--accent, #8b2a7a); color: var(--accent, #8b2a7a); }
.btn-quiz-next {
    background: var(--accent, #8b2a7a);
    color: #fff;
    border: none;
    border-radius: 0.6rem;
    padding: 0.65rem 1.6rem;
    font-size: 0.92rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
    box-shadow: 0 3px 12px rgba(139,42,122,0.25);
}
.btn-quiz-next:hover   { opacity: 0.88; transform: translateY(-1px); }
.btn-quiz-next:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

/* ── Results ───────────────────────────────────────── */
.quiz-result-hero {
    text-align: center;
    padding: 2.5rem 1rem 1.5rem;
}
.result-badge {
    display: inline-block;
    background: var(--accent, #8b2a7a);
    color: #fff;
    font-size: 0.72rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    padding: 0.3rem 0.9rem;
    border-radius: 999px;
    font-weight: 600;
    margin-bottom: 0.75rem;
}
.quiz-result-hero h2 { margin-bottom: 0.6rem; }
.quiz-result-hero p  { color: var(--text-muted, #666); max-width: 480px; margin: 0 auto; line-height: 1.7; }

.result-products-section {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 1rem 3rem;
}
.result-products-section h3 { margin-bottom: 1.2rem; }

.retake-wrap {
    text-align: center;
    margin-top: 2rem;
}
.btn-retake {
    background: none;
    border: 1.5px solid var(--accent, #8b2a7a);
    color: var(--accent, #8b2a7a);
    border-radius: 0.6rem;
    padding: 0.65rem 1.8rem;
    font-size: 0.92rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}
.btn-retake:hover { background: var(--accent, #8b2a7a); color: #fff; }

@media (max-width: 500px) {
    .quiz-options { grid-template-columns: 1fr; }
}
</style>

<main>

<?php if ($quizSubmitted): ?>
<!-- ════════════════ RESULTS ════════════════ -->
<section class="quiz-result-hero">
    <div class="result-badge">✦ Your Match</div>
    <h2><?= htmlspecialchars($resultHeading, ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars($resultSubtext, ENT_QUOTES, 'UTF-8') ?></p>
</section>

<div class="result-products-section">
    <h3>We think you'll love these</h3>

    <?php if (empty($matchedBakes)): ?>
        <p>We couldn't find any matching bakes right now — check back soon!</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($matchedBakes as $row): ?>
                <a class="card product-card product-link"
                   href="<?= APP_URL ?>/bake_details.php?bakeID=<?= (int)$row['bakeID'] ?>">

                    <?php if (!empty($row['imageFileName'])): ?>
                        <img
                            src="<?= APP_URL ?>/img/uploads/<?= htmlspecialchars($row['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($row['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                            class="product-image"
                            style="height:140px;width:100%;object-fit:cover;border-radius:0.7rem;"
                        >
                    <?php else: ?>
                        <div class="product-image placeholder-image">Bake</div>
                    <?php endif; ?>

                    <h4><?= htmlspecialchars($row['bakeName'], ENT_QUOTES, 'UTF-8') ?></h4>

                    <?php if (!empty($row['description'])): ?>
                        <p><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>

                    <p class="price">From £<?= number_format((float)$row['price'], 2) ?></p>

                    <span class="view-desc">View details</span>

                    <?php if ((int)$row['stockAmount'] > 0): ?>
                        <div class="stock-line">In stock: <strong><?= (int)$row['stockAmount'] ?></strong></div>
                    <?php else: ?>
                        <div class="out-stock">Out of stock</div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="retake-wrap">
        <a href="<?= APP_URL ?>/quiz.php" class="btn-retake">🔄 Retake the quiz</a>
    </div>
</div>

<?php else: ?>
<!-- ════════════════ QUIZ ════════════════ -->
<div class="quiz-hero">
    <p class="eyebrow">✦ Personalised for you ✦</p>
    <h2>Find Your Perfect Bake</h2>
    <p>Answer 4 quick questions and we'll match you with treats you'll actually love.</p>
    <div class="quiz-steps">
        <div class="quiz-step-dot active" id="dot-0"></div>
        <div class="quiz-step-dot" id="dot-1"></div>
        <div class="quiz-step-dot" id="dot-2"></div>
        <div class="quiz-step-dot" id="dot-3"></div>
    </div>
</div>

<div class="quiz-wrapper">
    <form method="POST" action="<?= APP_URL ?>/quiz.php" id="quizForm">

        <!-- Q1: Sweet or Savoury -->
        <div class="quiz-slide active" id="slide-0">
            <div class="quiz-question-label">Question 1 of 4</div>
            <div class="quiz-question-text">Sweet or savoury — where does your heart lie?</div>
            <div class="quiz-options">
                <button type="button" class="quiz-option" data-q="q_taste" data-val="sweet">
                    <span class="quiz-option-emoji">🍰</span>
                    <span class="quiz-option-text">Definitely sweet</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_taste" data-val="savoury">
                    <span class="quiz-option-emoji">🧀</span>
                    <span class="quiz-option-text">Savoury all the way</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_taste" data-val="both">
                    <span class="quiz-option-emoji">⚖️</span>
                    <span class="quiz-option-text">I love both!</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_taste" data-val="sweet">
                    <span class="quiz-option-emoji">🍫</span>
                    <span class="quiz-option-text">Chocolate. Always.</span>
                </button>
            </div>
            <input type="hidden" name="q_taste" id="input_q_taste">
            <div class="quiz-nav">
                <span></span>
                <button type="button" class="btn-quiz-next" id="next-0" disabled onclick="nextSlide(0)">Continue →</button>
            </div>
        </div>

        <!-- Q2: Calorie conscious -->
        <div class="quiz-slide" id="slide-1">
            <div class="quiz-question-label">Question 2 of 4</div>
            <div class="quiz-question-text">How do you feel about calories?</div>
            <div class="quiz-options">
                <button type="button" class="quiz-option" data-q="q_calorie" data-val="light">
                    <span class="quiz-option-emoji">🥗</span>
                    <span class="quiz-option-text">Keeping it light</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_calorie" data-val="indulgent">
                    <span class="quiz-option-emoji">🎉</span>
                    <span class="quiz-option-text">Treat yourself!</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_calorie" data-val="dontmind">
                    <span class="quiz-option-emoji">🤷</span>
                    <span class="quiz-option-text">I don't really mind</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_calorie" data-val="light">
                    <span class="quiz-option-emoji">🌿</span>
                    <span class="quiz-option-text">Clean &amp; wholesome</span>
                </button>
            </div>
            <input type="hidden" name="q_calorie" id="input_q_calorie">
            <div class="quiz-nav">
                <button type="button" class="btn-quiz-back" onclick="prevSlide(1)">← Back</button>
                <button type="button" class="btn-quiz-next" id="next-1" disabled onclick="nextSlide(1)">Continue →</button>
            </div>
        </div>

        <!-- Q3: Texture -->
        <div class="quiz-slide" id="slide-2">
            <div class="quiz-question-label">Question 3 of 4</div>
            <div class="quiz-question-text">What texture do you go for?</div>
            <div class="quiz-options">
                <button type="button" class="quiz-option" data-q="q_texture" data-val="soft">
                    <span class="quiz-option-emoji">🍞</span>
                    <span class="quiz-option-text">Soft &amp; fluffy</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_texture" data-val="crunchy">
                    <span class="quiz-option-emoji">🍪</span>
                    <span class="quiz-option-text">Crunchy &amp; crisp</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_texture" data-val="flaky">
                    <span class="quiz-option-emoji">🥐</span>
                    <span class="quiz-option-text">Flaky &amp; buttery</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_texture" data-val="hearty">
                    <span class="quiz-option-emoji">🫓</span>
                    <span class="quiz-option-text">Dense &amp; hearty</span>
                </button>
            </div>
            <input type="hidden" name="q_texture" id="input_q_texture">
            <div class="quiz-nav">
                <button type="button" class="btn-quiz-back" onclick="prevSlide(2)">← Back</button>
                <button type="button" class="btn-quiz-next" id="next-2" disabled onclick="nextSlide(2)">Continue →</button>
            </div>
        </div>

        <!-- Q4: Occasion -->
        <div class="quiz-slide" id="slide-3">
            <div class="quiz-question-label">Question 4 of 4</div>
            <div class="quiz-question-text">What's the occasion?</div>
            <div class="quiz-options">
                <button type="button" class="quiz-option" data-q="q_occasion" data-val="everyday">
                    <span class="quiz-option-emoji">☕</span>
                    <span class="quiz-option-text">Everyday treat</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_occasion" data-val="special">
                    <span class="quiz-option-emoji">🎂</span>
                    <span class="quiz-option-text">Special occasion</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_occasion" data-val="breakfast">
                    <span class="quiz-option-emoji">🌅</span>
                    <span class="quiz-option-text">Weekend breakfast</span>
                </button>
                <button type="button" class="quiz-option" data-q="q_occasion" data-val="snack">
                    <span class="quiz-option-emoji">⚡</span>
                    <span class="quiz-option-text">Quick snack</span>
                </button>
            </div>
            <input type="hidden" name="q_occasion" id="input_q_occasion">
            <div class="quiz-nav">
                <button type="button" class="btn-quiz-back" onclick="prevSlide(3)">← Back</button>
                <button type="button" class="btn-quiz-next" id="next-3" disabled onclick="submitQuiz()">See my results 🎉</button>
            </div>
        </div>

    </form>
</div>

<?php endif; ?>
</main>

<script>
const totalSlides = 4;
const answers = {};

// Handle option selection
document.querySelectorAll('.quiz-option').forEach(btn => {
    btn.addEventListener('click', function () {
        const q   = this.dataset.q;
        const val = this.dataset.val;
        answers[q] = val;

        // Update hidden input
        const input = document.getElementById('input_' + q);
        if (input) input.value = val;

        // Highlight selected option in this group
        document.querySelectorAll(`.quiz-option[data-q="${q}"]`).forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');

        // Enable the next button for this slide
        const slideIndex = parseInt(this.closest('.quiz-slide').id.replace('slide-', ''));
        const nextBtn = document.getElementById('next-' + slideIndex);
        if (nextBtn) nextBtn.disabled = false;
    });
});

function nextSlide(current) {
    const currentSlide = document.getElementById('slide-' + current);
    const nextSlide    = document.getElementById('slide-' + (current + 1));
    if (!nextSlide) return;

    currentSlide.classList.remove('active');
    nextSlide.classList.add('active');
    updateDots(current + 1);
}

function prevSlide(current) {
    const currentSlide = document.getElementById('slide-' + current);
    const prevSlide    = document.getElementById('slide-' + (current - 1));
    if (!prevSlide) return;

    currentSlide.classList.remove('active');
    prevSlide.classList.add('active');
    updateDots(current - 1);
}

function updateDots(activeIndex) {
    for (let i = 0; i < totalSlides; i++) {
        const dot = document.getElementById('dot-' + i);
        dot.classList.remove('active', 'done');
        if (i < activeIndex)       dot.classList.add('done');
        else if (i === activeIndex) dot.classList.add('active');
    }
}

function submitQuiz() {
    document.getElementById('quizForm').submit();
}
</script>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
</body>
</html>