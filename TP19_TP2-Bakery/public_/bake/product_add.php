<?php
session_start();
require_once 'dbconnect.php';

if (!defined('APP_URL')) define('APP_URL', '/public_/bake');

if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

$userID = $_SESSION['userID'];

$stmt = $db->prepare("
    SELECT adminStatus 
    FROM adminStatus 
    WHERE userID = :userID
");
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || (int)$admin['adminStatus'] !== 1) {
    header("Location: home.php");
    exit();
}

include '../components/header_unified.php';
?>

<main>
  <section class="section">
    <a href="<?= APP_URL ?>/stock.php" class="back-btn">← Back to Stock</a>

    <div class="details-layout">

      <div class="details-image-wrap" id="imagePreviewBox">
        <div class="details-image placeholder-image" id="previewPlaceholder" style="opacity:1;">
          <p style="text-align:center; opacity:0.8;">Upload Image</p>
        </div>
        <img id="previewImage" class="details-image" style="display:none;">
      </div>

      <div class="details-right">

        <h1 class="details-title">Add New Bake</h1>

        <form action="<?= APP_URL ?>/product_add_submit.php" method="post" enctype="multipart/form-data" class="details-form">

          <div class="option-block">
            <h3 class="block-title">Bake Name</h3>
            <input 
              type="text" 
              name="bakeName" 
              class="message-box" 
              placeholder="e.g. Chocolate Fudge Cake"
              required
            >
          </div>

          <div class="option-block">
            <h3 class="block-title">Description</h3>
            <textarea 
              name="description" 
              class="message-box" 
              placeholder="Describe the bake..."
              maxlength="300"
              required
            ></textarea>
          </div>

          <div class="option-block">
            <h3 class="block-title">Base Price (£)</h3>
            <input 
              type="number" 
              name="price" 
              class="qty-input" 
              min="0" 
              step="0.01" 
              placeholder="0.00"
              required
            >
          </div>

          <div class="option-block">
            <h3 class="block-title">Bake Category</h3>
            <select name="bakeTypeID" class="qty-input" required>
              <option value="">Select category...</option>
              <option value="1">Cake</option>
              <option value="2">Cookie</option>
              <option value="3">Pastry</option>
              <option value="4">Bread</option>
            </select>
          </div>

          <div class="option-block">
  <h3 class="block-title">Ingredients</h3>

 <table id="ingredientsTable" class="ingredients-table">
  <thead>
    <tr>
      <th>Ingredient</th>
      <th>Quantity</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <input type="text" name="ingredientName[]" class="ingredient-input" placeholder="Flour" required>
      </td>
      <td>
        <input type="text" name="ingredientQuantity[]" class="ingredient-input" maxlength="100" placeholder="e.g. 50g / 2 / 1 tbsp / 150 ml" required>
      </td>
      <td>
        <button type="button" class="remove-row-btn" onclick="removeIngredientRow(this)">✖</button>
      </td>
    </tr>
  </tbody>
</table>

<button type="button" class="add-row-btn" onclick="addIngredientRow()">+ Add Ingredient</button>
</div>

          <div class="option-block">
            <h3 class="block-title">Initial Stock Amount</h3>
            <input 
              type="number" 
              name="stockAmount" 
              class="qty-input" 
              min="0" 
              value="0"
              required
            >
          </div>

          <div class="option-block">
            <h3 class="block-title">Upload Image</h3>
            <input 
              type="file" 
              name="imageFile" 
              accept="image/*"
              id="imageInput"
              required
            >
          </div>

          <div class="buy-row">
            <button type="submit" class="add-btn">
              Add Bake to Database
            </button>
          </div>

        </form>

      </div>
    </div>

    <div class="tabs-bar">
      <button class="active" type="button">Instructions</button>
    </div>

    <div class="tab-panel">
      <p class="muted">
        Fill out all fields and upload an image.  
        After submitting, the bake will appear in the stock list.
      </p>
    </div>

  </section>
</main>

<script>
function addIngredientRow() {
    const tableBody = document.querySelector("#ingredientsTable tbody");

    const row = document.createElement("tr");
    row.innerHTML = `
        <td>
          <input type="text" name="ingredientName[]" class="ingredient-input" placeholder="" required>
        </td>
        <td>
          <input type="text" name="ingredientQuantity[]" class="ingredient-input" maxlength="100" placeholder="e.g. 50g / 1 tbsp / 2 ml" required>
        </td>
        <td>
          <button type="button" class="remove-row-btn" onclick="removeIngredientRow(this)">✖</button>
        </td>
    `;

    tableBody.appendChild(row);
}

function removeIngredientRow(button) {
    const row = button.closest("tr");
    const tableBody = document.querySelector("#ingredientsTable tbody");

    if (tableBody.rows.length > 1) {
        row.remove();
    }
}
</script>