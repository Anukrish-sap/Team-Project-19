<?php 
session_start();
 if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
} else {
    include '../components/header.php';
}
?>
<main class="about-container">

      <section class="section">
    <section class="about-hero">
        <h2>Our Story</h2>
        <p>Bakes & Cakes is a online bakery created to provide a variety of baked goods including gluten free options.
        Our mission is simple: bring high quality baked goods directly to your home through
        a beautifully designed, easy to use online platform created by a group of university students.</p>
    </section>
</section>
          <section class="section section-alt">
    <section class="about-section">
        <h3>Freshly Baked, Always</h3>
        <p>Every product is crafted personally and with care using the best quality ingredients. From rich chocolate cakes 
        to warm pastries and soft cookies, our collection is designed to offer something for every taste including customers with allergens.</p>
    </section>
        </section>
        
<section class="section">
    <section class="about-section">
        <h3>Allergy Friendly Options</h3>
        <p>We know how important it is to feel safe when ordering baked treats. That's why we offer gluten free options, 
        clearly labelled on our website, with plans to expand into more dietary categories such as nut free and vegan, if you've got any personal requests please contact us.</p>
    </section>
        </section>
        
 <section class="section section-alt">
    <section class="about-section">
        <h3>Created by Students, Built for Everyone</h3>
        <p>This bakery platform is the result of a student team project for Aston University. We focused on real business 
        requirements, to create the most user accesible website, and modern web design to deliver a functional bakery website for you.</p>
    </section>
</section>
        
        <section class="section">
    <section class="about-section">
        <h3>Our Goals</h3>
        <ul>
            <li>Provide a easy to use professional online bakery experience</li>
            <li>Offer an accessible platform for browsing and ordering products</li>
            <li>Highlight allergy friendly and dietary specific items</li>
            <li>Showcase strong teamwork and software development skills</li>
        </ul>
    </section>
</section>
</main>

 <?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>
<script src="js/theme.js"></script>

</body>
</html>
  