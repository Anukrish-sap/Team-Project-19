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

<?php include '../components/header_unified.php'; ?>
            
            
    </div>
    </header>
    <div class="form-container">
        <h1>Create Your Account</h1>
        <p>Join to save your favorite recipes!</p>
        
        <form id="register-form" action="registeracc.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your Full Name" pattern="[a-zA-Z_ ]{1,50}" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" pattern="^.+@.+\..+$" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                 <input type="password" id="password" name="password" placeholder="Enter a password" pattern="[a-zA-Z0-9!@#$%^&*()_+\-={}|\\[\\]:;'<>?,./]{8,50}" required>
            </div>
            
       
            
          
            <button type="submit" class="submit-btn" name ="registerButton" value="registered">Register</button>

        
           
            
        </form>
        <p class="login-link">Already have an account? <a href="loginpage.php">Log in</a></p>
    </div>

    
        
      
  <?php include '../components/footer.php'; ?>
  <?php include '../components/script.html'; ?>
