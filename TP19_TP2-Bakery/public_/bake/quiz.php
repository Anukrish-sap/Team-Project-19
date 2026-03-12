<?php
session_start();
require_once 'dbconnect.php';

if (!defined('HOME_URL')) define('HOME_URL', '/index.php');
if (!defined('APP_URL'))  define('APP_URL', '/public_/bake');

include '../components/header_unified.php';

$matchedBakes  = [];
$quizSubmitted = false;
$resultHeading = '';
$resultSubtext = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['q_taste'])) {
    $quizSubmitted = true;

    $taste    = $_POST['q_taste']    ?? '';
    $calorie  = $_POST['q_calorie']  ?? '';
    $texture  = $_POST['q_texture']  ?? '';
    $occasion = $_POST['q_occasion'] ?? '';

    if ($taste === 'savoury') {
        $targetType    = 4;
        $resultHeading = 'Savoury Lover';
        $resultSubtext = 'You\'re all about bold, satisfying flavours. We\'ve matched you with our best savoury bakes.';
    } elseif ($taste === 'sweet' && $texture === 'crunchy') {
        $targetType    = 2;
        $resultHeading = 'The Cookie Monster';
        $resultSubtext = 'Crisp edges, chewy centres — you know what you want. Here are your perfect cookies.';
    } elseif ($taste === 'sweet' && $texture === 'flaky') {
        $targetType    = 3;
        $resultHeading = 'Pastry Perfectionist';
        $resultSubtext = 'Buttery layers and delicate bakes are your thing. These pastries were made for you.';
    } elseif ($taste === 'both' || $occasion === 'breakfast') {
        $targetType    = 3;
        $resultHeading = 'The Balanced Baker';
        $resultSubtext = 'Sweet or savoury, you want it all. Our pastries hit that perfect middle ground.';
    } elseif ($calorie === 'light') {
        $targetType    = 2;
        $resultHeading = 'Mindful Muncher';
        $resultSubtext = 'Treating yourself doesn\'t mean going overboard. These perfectly-sized bakes are just right.';
    } else {
        $targetType    = 1;
        $resultHeading = 'Cake Connoisseur';
        $resultSubtext = 'Go big or go home. Life\'s too short for anything less than a proper cake.';
    }

    try {
        $stmt = $db->prepare("
            SELECT bakes.bakeID, bakes.bakeName, bakes.description,
                   bakes.price, bakes.imageFileName,
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
/* All variables match styles.css exactly:
   --bg-color, --card-bg, --card-alt-bg, --text-color,
   --accent, --accent-soft, --accent-dark, --border-color */

.quiz-page {
    max-width: 680px;
    margin: 2.5rem auto 4rem;
    padding: 0 1rem;
}

.quiz-page-hero {
    text-align: center;
    margin-bottom: 2rem;
}
.quiz-page-hero h2 {
    font-size: clamp(1.6rem, 4vw, 2.2rem);
    margin-bottom: 0.5rem;
    color: var(--text-color);
}
.quiz-page-hero p {
    color: var(--text-color);
    opacity: 0.7;
    font-size: 0.97rem;
    line-height: 1.65;
}

.quiz-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 1.1rem;
    padding: 2rem 2rem 2.2rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}

.quiz-question-block {
    margin-bottom: 2rem;
}

.quiz-q-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.13em;
    color: var(--accent);
    font-weight: 600;
    margin-bottom: 0.3rem;
}
.quiz-q-text {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 1rem;
}

.quiz-tiles {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.65rem;
}
.quiz-tile {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.8rem 1rem;
    background: var(--card-alt-bg);
    border: 1.5px solid var(--border-color);
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.18s ease;
    font-family: inherit;
    font-size: 0.91rem;
    font-weight: 500;
    color: var(--text-color);
    text-align: left;
    width: 100%;
}
.quiz-tile:hover {
    border-color: var(--accent);
    background: var(--card-bg);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.quiz-tile.selected {
    border-color: var(--accent);
    background: var(--card-bg);
    box-shadow: 0 0 0 2px var(--accent-soft);
}
.quiz-tile-emoji { font-size: 1.3rem; flex-shrink: 0; }

.quiz-divider {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 1.8rem 0;
}

.quiz-submit-wrap { margin-top: 2rem; }
.quiz-submit-btn {
    width: 100%;
    padding: 0.9rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 0.75rem;
    cursor: pointer;
    border: none;
    background: var(--accent);
    color: #fff;
    transition: background-color 0.2s ease, transform 0.1s ease;
    font-family: inherit;
}
.quiz-submit-btn:hover {
    background: var(--accent-dark);
    transform: translateY(-1px);
}
.quiz-error {
    color: #b00020;
    font-size: 0.85rem;
    margin-top: 0.6rem;
    display: none;
    text-align: center;
}

/* Results */
.result-hero {
    text-align: center;
    padding: 2.5rem 1rem 1.5rem;
}
.result-badge {
    display: inline-block;
    background: var(--accent);
    color: #fff;
    font-size: 0.72rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    padding: 0.3rem 0.9rem;
    border-radius: 999px;
    font-weight: 600;
    margin-bottom: 0.75rem;
}
.result-hero h2 {
    margin-bottom: 0.5rem;
    color: var(--text-color);
}
.result-hero p {
    color: var(--text-color);
    opacity: 0.7;
    max-width: 460px;
    margin: 0 auto;
    line-height: 1.7;
}

.result-products {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 1rem 3rem;
}
.result-products h3 {
    margin-bottom: 1.2rem;
    color: var(--text-color);
}

.retake-wrap { text-align: center; margin-top: 2rem; }
.btn-retake {
    display: inline-block;
    background: none;
    border: 1.5px solid var(--accent);
    color: var(--accent);
    border-radius: 999px;
    padding: 0.65rem 1.8rem;
    font-size: 0.92rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-retake:hover {
    background: var(--accent);
    color: #fff;
}

/* Reuse existing card styles */
.product-link { display:block; text-decoration:none; color:inherit; height:100%; }
.product-card { cursor:pointer; transition:transform 0.12s ease; }
.product-card:hover { transform:translateY(-2px); }
.view-desc { margin-top:0.6rem; display:inline-block; font-weight:600; font-size:0.95rem; text-decoration:underline; opacity:0.9; }
.stock-line { margin-top:0.35rem; font-size:0.9rem; opacity:0.9; }
.out-stock  { margin-top:0.35rem; color:#b00020; font-weight:700; }

@media (max-width: 480px) {
    .quiz-tiles { grid-template-columns: 1fr; }
    .quiz-card  { padding: 1.4rem 1.1rem; }
}
</style>

<main>

<?php if ($quizSubmitted): ?>

<div class="result-hero">
    <div class="result-badge">✦ Your Match</div>
    <h2><?= htmlspecialchars($resultHeading, ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars($resultSubtext, ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="result-products">
    <h3>We think you'll love these</h3>

    <?php if (empty($matchedBakes)): ?>
        <p>No matching bakes found right now — check back soon!</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($matchedBakes as $row): ?>
                <a class="card product-card product-link"
                   href="<?= APP_URL ?>/bake_details.php?bakeID=<?= (int)$row['bakeID'] ?>">

                    <?php if (!empty($row['imageFileName'])): ?>
                        <img src="<?= APP_URL ?>/img/uploads/<?= htmlspecialchars($row['imageFileName'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($row['bakeName'], ENT_QUOTES, 'UTF-8') ?>"
                             class="product-image"
                             style="height:140px;width:100%;object-fit:cover;border-radius:0.7rem;">
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

<div class="quiz-page">
    <div class="quiz-page-hero">
        <h2>Find Your Perfect Bake</h2>
        <p>Answer 4 quick questions and we'll match you with treats you'll love.</p>
    </div>

    <div class="quiz-card">
        <form method="POST" action="<?= APP_URL ?>/quiz.php" id="quizForm">

            <!-- Q1 -->
            <div class="quiz-question-block">
                <div class="quiz-q-label">Question 1</div>
                <div class="quiz-q-text">Sweet or savoury — where does your heart lie?</div>
                <div class="quiz-tiles">
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="sweet">
                        <span class="quiz-tile-emoji">🍰</span> Definitely sweet
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="savoury">
                        <span class="quiz-tile-emoji">🧀</span> Savoury all the way
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="both">
                        <span class="quiz-tile-emoji">⚖️</span> I love both!
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="sweet">
                        <span class="quiz-tile-emoji">🍫</span> Chocolate. Always.
                    </button>
                </div>
                <input type="hidden" name="q_taste" id="input_q_taste">
            </div>

            <hr class="quiz-divider">

            <!-- Q2 -->
            <div class="quiz-question-block">
                <div class="quiz-q-label">Question 2</div>
                <div class="quiz-q-text">How do you feel about calories?</div>
                <div class="quiz-tiles">
                    <button type="button" class="quiz-tile" data-q="q_calorie" data-val="light">
                        <span class="quiz-tile-emoji">🥗</span> Keeping it light
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_calorie" data-val="indulgent">
                        <span class="quiz-tile-emoji">🎉</span> Treat yourself!
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_calorie" data-val="dontmind">
                        <span class="quiz-tile-emoji">🤷</span> I don't really mind
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_calorie" data-val="light">
                        <span class="quiz-tile-emoji">🌿</span> Clean &amp; wholesome
                    </button>
                </div>
                <input type="hidden" name="q_calorie" id="input_q_calorie">
            </div>

            <hr class="quiz-divider">

            <!-- Q3 -->
            <div class="quiz-question-block">
                <div class="quiz-q-label">Question 3</div>
                <div class="quiz-q-text">What texture do you go for?</div>
                <div class="quiz-tiles">
                    <button type="button" class="quiz-tile" data-q="q_texture" data-val="soft">
                        <span class="quiz-tile-emoji">🍞</span> Soft &amp; fluffy
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_texture" data-val="crunchy">
                        <span class="quiz-tile-emoji">🍪</span> Crunchy &amp; crisp
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_texture" data-val="flaky">
                        <span class="quiz-tile-emoji">🥐</span> Flaky &amp; buttery
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_texture" data-val="hearty">
                        <span class="quiz-tile-emoji">🫓</span> Dense &amp; hearty
                    </button>
                </div>
                <input type="hidden" name="q_texture" id="input_q_texture">
            </div>

            <hr class="quiz-divider">

            <!-- Q4 -->
            <div class="quiz-question-block">
                <div class="quiz-q-label">Question 4</div>
                <div class="quiz-q-text">What's the occasion?</div>
                <div class="quiz-tiles">
                    <button type="button" class="quiz-tile" data-q="q_occasion" data-val="everyday">
                        <span class="quiz-tile-emoji">☕</span> Everyday treat
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_occasion" data-val="special">
                        <span class="quiz-tile-emoji">🎂</span> Special occasion
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_occasion" data-val="breakfast">
                        <span class="quiz-tile-emoji">🌅</span> Weekend breakfast
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_occasion" data-val="snack">
                        <span class="quiz-tile-emoji">⚡</span> Quick snack
                    </button>
                </div>
                <input type="hidden" name="q_occasion" id="input_q_occasion">
            </div>

            <div class="quiz-submit-wrap">
                <button type="button" class="quiz-submit-btn" onclick="submitQuiz()">
                    Find My Perfect Bake 🎉
                </button>
                <div class="quiz-error" id="quizError">
                    Please answer all questions before continuing.
                </div>
            </div>

        </form>
    </div>
</div>

<?php endif; ?>
</main>

<script>
document.querySelectorAll('.quiz-tile').forEach(btn => {
    btn.addEventListener('click', function () {
        const q = this.dataset.q;
        document.querySelectorAll(`.quiz-tile[data-q="${q}"]`).forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('input_' + q).value = this.dataset.val;
        document.getElementById('quizError').style.display = 'none';
    });
});

function submitQuiz() {
    const required = ['q_taste', 'q_calorie', 'q_texture', 'q_occasion'];
    const allAnswered = required.every(q => document.getElementById('input_' + q).value !== '');
    if (!allAnswered) {
        document.getElementById('quizError').style.display = 'block';
        return;
    }
    document.getElementById('quizForm').submit();
}
</script>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
</body>
</html>