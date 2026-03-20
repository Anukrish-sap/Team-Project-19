<?php
session_start();
include "dbconnect.php";

/* 
   1. ADMIN ACCESS CHECK
 */
if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

$adminCheck = $db->prepare("
    SELECT adminStatus 
    FROM adminStatus 
    WHERE userID = :uid
");
$adminCheck->bindParam(':uid', $_SESSION['userID'], PDO::PARAM_INT);
$adminCheck->execute();
$adminRow = $adminCheck->fetch(PDO::FETCH_ASSOC);

if (!$adminRow || (int)$adminRow['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}

/* 
   2. ENSURE ADMIN SELECTED A USER
*/
if (!isset($_SESSION['accDetAdm'])) {
    header("Location: adminAccUpdate.php?error=no_account_selected");
    exit();
}

$targetUserID = $_SESSION['accDetAdm'];

/* 
   3. FETCH TARGET USER DETAILS
 */
try {
    $stmt = $db->prepare("SELECT name, email FROM users WHERE userID = :uid");
    $stmt->bindParam(':uid', $targetUserID, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: adminAccUpdate.php?error=user_not_found");
        exit();
    }

} catch (PDOException $e) {
    header("Location: adminAccUpdate.php?error=db_error");
    exit();
}

/* 
   4. HANDLE FORM SUBMISSION
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = trim($_POST['new_name']);

    if ($newName === "") {
        $error = "Name cannot be empty.";
    } else {
        try {
            $update = $db->prepare("UPDATE users SET name = :newName WHERE userID = :uid");
            $update->bindParam(':newName', $newName, PDO::PARAM_STR);
            $update->bindParam(':uid', $targetUserID, PDO::PARAM_INT);
            $update->execute();

            $_SESSION['success'] = "User’s name updated successfully.";
            header("Location: admin_account_edit.php");
            exit();

        } catch (PDOException $e) {
            $error = "Database error updating name.";
        }
    }
}

include '../components/header_unified.php';
?>

<style>
/* Simple styling consistent with your theme */
.namechange-wrapper {
    max-width: 600px;
    margin: 3rem auto;
    padding: 2rem;
    background: var(--card-bg);
    border-radius: 1rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.namechange-wrapper h1 {
    margin-bottom: 0.5rem;
}

.namechange-wrapper p {
    opacity: 0.7;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.4rem;
}

.submit-btn {
    margin-top: 1rem;
}
</style>

<main>
    <div class="namechange-wrapper">

        <h1>Change <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>’s Name</h1>
        <p>Enter a new name for this user. This will update how their name appears across the site.</p>

        <?php if (!empty($error)): ?>
            <p style="color:red; font-weight:600;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="new_name">New Name</label>
                <input 
                    type="text" 
                    id="new_name" 
                    name="new_name" 
                    required 
                    value="<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <button type="submit" class="submit-btn">Update Name</button>
        </form>

        <a href="admin_account_edit.php" style="display:block; margin-top:1.5rem; text-align:center;">
            ← Return to Account Management
        </a>

    </div>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
