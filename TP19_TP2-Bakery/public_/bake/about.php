<?php 
session_start();
 if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
} else {
    include '../components/header.php';
}
?>
<style>
  .about-page {
    max-width: 740px;
    margin: 0 auto;
    padding: 3.5rem 1.5rem 5rem;
  }

  /* Hero */
  .about-hero-block {
    margin-bottom: 3.5rem;
  }
  .about-hero-label {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--accent);
    background: rgba(192,123,80,0.1);
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    margin-bottom: 1rem;
  }
  .about-hero-block h1 {
    font-size: clamp(2rem, 5vw, 2.8rem);
    font-weight: 800;
    margin: 0 0 1rem;
    line-height: 1.1;
    letter-spacing: -0.03em;
  }
  .about-hero-block p {
    font-size: 1.05rem;
    line-height: 1.75;
    opacity: 0.75;
    max-width: 580px;
    margin: 0;
  }

  /* Divider */
  .about-divider {
    height: 1px;
    background: var(--border-color);
    opacity: 0.5;
    margin: 0 0 2.75rem;
  }

  /* Info cards */
  .about-cards {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    margin-bottom: 3rem;
  }
  .about-card {
    display: flex;
    gap: 1.25rem;
    padding: 1.5rem 1.6rem;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 1.1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }
  .about-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  }
  .about-card-icon {
    width: 46px;
    height: 46px;
    border-radius: 0.75rem;
    background: rgba(192,123,80,0.1);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 0.1rem;
  }
  .about-card-body h3 {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0 0 0.45rem;
    letter-spacing: -0.01em;
  }
  .about-card-body p {
    margin: 0;
    font-size: 0.93rem;
    line-height: 1.7;
    opacity: 0.7;
  }

  /* Goals section */
  .about-goals-title {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    opacity: 0.45;
    margin: 0 0 1rem;
  }
  .about-goals {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
  }
  .about-goal-item {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.9rem 1.2rem;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.85rem;
    font-size: 0.94rem;
    font-weight: 500;
  }
  .about-goal-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
    flex-shrink: 0;
  }

  @media (max-width: 480px) {
    .about-page { padding: 2rem 1rem 4rem; }
    .about-card { flex-direction: column; gap: 0.9rem; }
  }
</style>

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
  
