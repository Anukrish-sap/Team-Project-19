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
    header("Location: loginpage.php"); 
    $_SESSION['error'] = "You must be logged in to view account details";
    exit();
}
try {
    $acc = $db->prepare("SELECT email, name FROM users WHERE userID = :userID");
    $acc->bindParam(':userID', $_SESSION['userID']);
    $acc->execute();
    $user = $acc->fetch(PDO::FETCH_ASSOC);

    if (!$acc) {
        $_SESSION['error'] = "Error fetching account details: " . $db->errorInfo()[2];
        header("Location: home.php"); 
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching account details: " . $e->getMessage();
    header("Location: home.php"); 
    exit();
}
?>










 

<main>
    <!-- Hero section -->
    <section class="hero">
        <div class="hero-content">
          <?php
if ($user) {
    echo "<h2 id='ww'> You can edit your account details here, " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . ".</h2>";
}
?>
  <br>
</div>
        </section>




     
              
                
   <section class="section">
 <h2> Change Your Name:</h2>
        <div class="hero-buttons">
            
                <a href="namechange.php" class="btn primary">Name</a>
        </div>
        </section>

     <section class="section section-alt">
 <h2> Change Your Password:</h2>
        <div class="hero-buttons">
                <a href="passwordchange.php" class="btn primary">Password</a>
        </div>
        </section>
              

                  <section class="hero">
        <div class="hero-content">
            <h1> Alternatively, you may: </h1>
            <br>
</div>
        </section>
      
         <section class="section">
 <h2> Logout of your account:</h2>
        <div class="hero-buttons">
                <a href="logout.php" class="btn primary">Logout</a>
        </div>
        </section>
      
        <section class="section section-alt">
 <h2> Delete your account:</h2>
        <div class="hero-buttons">
                <a href="deleteaccount.php" class="btn primary">Delete your account</a>
        </div>
        </section>

           
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>


</body>
</html>

