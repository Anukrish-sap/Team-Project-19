<?php
include "dbconnect.php";
session_start();

if (isset($_SESSION['success'])) {
    echo "<p style='color: green;'>" . $_SESSION['success'] . "</p>";
    unset($_SESSION['success']); // Clear the success message after displaying
}

if (isset($_SESSION['error'])) {
    echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']); // Clear the error message after displaying
}
if(isset($_SESSION['login1'])){
    echo "<p style='color: red;'>".$_SESSION['login1']."</p>"; //if there is an error, show this message
    unset($_SESSION['login1']); //unset the error message so it doesn't show again
}

unset ($_SESSION['uid']);
unset ($_SESSION['username']);

?>
<link rel="stylesheet" href="css/styleali.css">
<link rel="stylesheet" href="css/styles.css">


<?php include '../components/header.php'; ?>

    <div class="form-container">
        <h1>Welcome Back!</h1>
        <p>Log in to access your saved bakery favourites</p>

        <form action="verify_credentials.php" method="POST">
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    autocomplete="email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password">
                <div class="forgot-password">
                    <a href="forgot-password.html">Forgot your password?</a>
                </div>
            </div>
            
            <button type="submit" name="loginButton" class="submit-btn">
                Log In
            </button>
        </form>
    </div>

    
       
      
  <?php include '../components/footer.php'; ?>
  <?php include '../components/script.html'; ?>
