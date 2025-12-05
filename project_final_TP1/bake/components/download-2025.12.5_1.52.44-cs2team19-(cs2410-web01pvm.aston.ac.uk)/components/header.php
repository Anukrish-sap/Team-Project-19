<?php 
$current = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="UTF-8">
    <title>Bakes & Cakes | Your home for all your bakes and cakes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
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
            <li><a href="home.php" class="<?= $current == 'home.php' ? 'active' : '' ?>">Home</a></li>
            <li><a href="bakes.php" class="<?= $current == 'bakes.php' ? 'active' : '' ?>">Products</a></li>
           
           
            <li><a href="basket.php" class="<?= $current == 'basket.php' ? 'active' : '' ?>">Basket</a></li>
           
            <li><a href="contact.php" class="<?= $current == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
            <li><a href="about.php" class="<?= $current == 'about.php' ? 'active' : '' ?>">About</a></li>
             <li><a href="loginpage.php" class="<?= $current == 'loginpage.php' ? 'active' : '' ?>">Login</a></li>
             <li><a href="register.php" class="<?= $current == 'register.php' ? 'active' : '' ?>">Register</a></li>
        </ul>
    </nav>

    <button id="theme-toggle" aria-label="Toggle light or dark mode">
        Dark mode
    </button>

</header>







