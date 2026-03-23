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
$resultEmoji   = '';

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
        $resultEmoji   = '&#x1F9C0;';
    } elseif ($taste === 'sweet' && $texture === 'crunchy') {
        $targetType    = 2;
        $resultHeading = 'The Cookie Monster';
        $resultSubtext = 'Crisp edges, chewy centres — you know what you want. Here are your perfect cookies.';
        $resultEmoji   = '&#x1F36A;';
    } elseif ($taste === 'sweet' && $texture === 'flaky') {
        $targetType    = 3;
        $resultHeading = 'Pastry Perfectionist';
        $resultSubtext = 'Buttery layers and delicate bakes are your thing. These pastries were made for you.';
        $resultEmoji   = '&#x1F950;';
    } elseif ($taste === 'both' || $occasion === 'breakfast') {
        $targetType    = 3;
        $resultHeading = 'The Balanced Baker';
        $resultSubtext = 'Sweet or savoury, you want it all. Our pastries hit that perfect middle ground.';
        $resultEmoji   = '&#x2696;&#xFE0F;';
    } elseif ($calorie === 'light') {
        $targetType    = 2;
        $resultHeading = 'Mindful Muncher';
        $resultSubtext = 'Treating yourself doesn\'t mean going overboard. These perfectly-sized bakes are just right.';
        $resultEmoji   = '&#x1F957;';
    } else {
        $targetType    = 1;
        $resultHeading = 'Cake Connoisseur';
        $resultSubtext = 'Go big or go home. Life\'s too short for anything less than a proper cake.';
        $resultEmoji   = '&#x1F370;';
    }

    // Save to session so accdetails.php can display it
    $_SESSION['quiz_result'] = [
        'heading' => $resultHeading,
        'subtext' => $resultSubtext,
        'emoji'   => $resultEmoji,
    ];

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

<main>

<?php if ($quizSubmitted): ?>

<div class="result-hero">
    <div class="result-badge">&#x2756; Your Match</div>
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

                    <p class="price">From &#xa3;<?= number_format((float)$row['price'], 2) ?></p>
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
        <a href="<?= APP_URL ?>/quiz.php" class="btn-retake">&#x1F504; Retake the quiz</a>
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
                <div class="quiz-q-text">Sweet or savoury &mdash; where does your heart lie?</div>
                <div class="quiz-tiles">
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="sweet">
                        <span class="quiz-tile-emoji">&#x1F370;</span> Definitely sweet
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="savoury">
                        <span class="quiz-tile-emoji">&#x1F9C0;</span> Savoury all the way
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="both">
                        <span class="quiz-tile-emoji">&#x2696;&#xFE0F;</span> I love both!
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_taste" data-val="sweet">
                        <span class="quiz-tile-emoji">&#x1F36B;</span> Chocolate. Always.
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
                        <span class="quiz-tile-emoji">&#x1F957;</span> Keeping it light
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_calorie" data-val="indulgent">
                        <span class="quiz-tile-emoji">&#x1F389;</span> Treat yourself!
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_calorie" data-val="dontmind">
                        <span class="quiz-tile-emoji">&#x1F937;</span> I don't really mind
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_calorie" data-val="light">
                        <span class="quiz-tile-emoji">&#x1F33F;</span> Clean &amp; wholesome
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
                        <span class="quiz-tile-emoji">&#x1F35E;</span> Soft &amp; fluffy
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_texture" data-val="crunchy">
                        <span class="quiz-tile-emoji">&#x1F36A;</span> Crunchy &amp; crisp
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_texture" data-val="flaky">
                        <span class="quiz-tile-emoji">&#x1F950;</span> Flaky &amp; buttery
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_texture" data-val="hearty">
                        <span class="quiz-tile-emoji">&#x1FAD3;</span> Dense &amp; hearty
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
                        <span class="quiz-tile-emoji">&#x2615;</span> Everyday treat
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_occasion" data-val="special">
                        <span class="quiz-tile-emoji">&#x1F382;</span> Special occasion
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_occasion" data-val="breakfast">
                        <span class="quiz-tile-emoji">&#x1F305;</span> Weekend breakfast
                    </button>
                    <button type="button" class="quiz-tile" data-q="q_occasion" data-val="snack">
                        <span class="quiz-tile-emoji">&#x26A1;</span> Quick snack
                    </button>
                </div>
                <input type="hidden" name="q_occasion" id="input_q_occasion">
            </div>

            <div class="quiz-submit-wrap">
                <button type="button" class="quiz-submit-btn" onclick="submitQuiz()">
                    Find My Perfect Bake &#x1F389;
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