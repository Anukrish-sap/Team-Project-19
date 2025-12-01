// register.php

<?php

session_start();
?>


<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="css/styles.css">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bakes & Cakes | Your home for all your bakes and cakes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="light">

<header class="site-header">
    <div class="logo-area">
        
        <img src="img/logo.png" alt="Bakes & Cakes logo" class="logo">
        <div class="brand-text">
            <h1>Bakes & Cakes</h1>
            <p class="tagline">Your home for all your bakes and cakes</p>
        </div>
    </div>

    <nav class="main-nav">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="products.php"   >Products</a></li>
            <li><a href="loginpage.php">login</a></li>
            <li class="has-dropdown">
                <a href="categories.php">Categories</a>
                <ul class="dropdown">
                    <li><a href="products.php?category=cakes">Cakes</a></li>
                    <li><a href="products.php?category=cookies">Cookies</a></li>
                    <li><a href="products.php?category=pastries">Pastries</a></li>
                    <li><a href="products.php?category=bread">Bread</a></li>
                </ul>
            </li>
            <li><a href="basket.php">Basket</a></li>
            <li><a href="loginpage.php">Login</a></li>
            <li><a href="register.php"class="active">Register</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

    <button id="theme-toggle" aria-label="Toggle light or dark mode">
       Dark mode
    </button>
</header> 
            
            
    <div class="form-container">
        <h1>Register Here.</h1>
        <p>Register to save your favourite bakes & make purchases</p>

        <form action="registeracc.php" method="POST">
            
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
            
            <button type="submit" name="registerButton" class="submit-btn">
                Register
            </button>
        </form>
    </div>

    
        
      
  <?php include '../components/footer.html'; ?>
  <?php include '../components/script.html'; ?>