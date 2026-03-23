<?php 
session_start();
require_once 'dbconnect.php';
include '../components/header_unified.php';
?>


<main>
  <div class="about-page">

    <!-- Hero -->
    <div class="about-hero-block">
      <span class="about-hero-label">About Us</span>
      <h1>Our Story</h1>
      <p>Bakes & Cakes is an online bakery created to provide a variety of baked goods — including gluten free options. Our mission is simple: bring high quality treats directly to your home through a beautifully designed, easy to use platform, built by a group of university students.</p>
    </div>

    <div class="about-divider"></div>

    <!-- Info cards -->
    <div class="about-cards">

      <div class="about-card">
        <div class="about-card-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3C7 3 3 7 3 12s4 9 9 9 9-4 9-9-4-9-9-9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
        </div>
        <div class="about-card-body">
          <h3>Freshly Baked, Always</h3>
          <p>Every product is crafted personally and with care using the best quality ingredients. From rich chocolate cakes to warm pastries and soft cookies, our collection offers something for every taste.</p>
        </div>
      </div>

      <div class="about-card">
        <div class="about-card-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="about-card-body">
          <h3>Allergy Friendly Options</h3>
          <p>We know how important it is to feel safe when ordering. We offer clearly labelled gluten free options, with plans to expand into nut free and vegan categories. Got a personal request? Feel free to contact us.</p>
        </div>
      </div>

      <div class="about-card">
        <div class="about-card-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-4a4 4 0 11-8 0 4 4 0 018 0zM3 8a4 4 0 118 0"/></svg>
        </div>
        <div class="about-card-body">
          <h3>Created by Students, Built for Everyone</h3>
          <p>This platform is the result of a student team project for Aston University. We focused on real business requirements, accessibility, and modern web design to deliver a fully functional bakery website.</p>
        </div>
      </div>

    </div>

    <!-- Goals -->
    <p class="about-goals-title">Our Goals</p>
    <div class="about-goals">
      <div class="about-goal-item"><span class="about-goal-dot"></span> Provide an easy to use, professional online bakery experience</div>
      <div class="about-goal-item"><span class="about-goal-dot"></span> Offer an accessible platform for browsing and ordering products</div>
      <div class="about-goal-item"><span class="about-goal-dot"></span> Highlight allergy friendly and dietary specific items</div>
      <div class="about-goal-item"><span class="about-goal-dot"></span> Showcase strong teamwork and software development skills</div>
    </div>

  </div>
</main>

 <?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
<script src="js/theme.js"></script>

</body>
</html>
  