

<?php 
session_start();
 if (isset($_SESSION['userID'])) {
    include '../components/header_l.php';
} else {
    include '../components/header.php';
}
?>

<main>
    <section class="section">
        <h2>Help & Frequently Asked Questions</h2>
        <p class="section-intro">
            Find answers to common questions about ordering, delivery, allergies, and more.
        </p>
    </section>

    <section class="section section-alt">
        <h3>Ordering & Payment</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>How do I place an order?</h4>
                <p>
                    Browse our products, add items to your basket, and proceed to checkout. 
                    You'll need to create an account or log in to complete your purchase.
                </p>
            </div>

            <div class="faq-item">
                <h4>What payment methods do you accept?</h4>
                <p>
                    We accept all major credit and debit cards.
                </p>
            </div>

            <div class="faq-item">
                <h4>Can I modify or cancel my order?</h4>
                <p>
                    Orders can be modified or cancelled within 2 hours of placing them. 
                    Please contact us immediately via the <a href="contact.php">Contact page</a> if you need to make changes.
                </p>
            </div>

            <div class="faq-item">
                <h4>Do you offer gift cards?</h4>
                <p>
                    Yes! Gift cards are available in various denominations. Contact us to purchase a gift card for your loved ones.
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <h3>Delivery & Collection</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>What are your delivery areas?</h4>
                <p>
                    We currently deliver within a 10-mile radius of Birmingham city centre. 
                    Enter your postcode at checkout to check if we deliver to your area.
                </p>
            </div>

            <div class="faq-item">
                <h4>How much is delivery?</h4>
                <p>
                    Delivery costs £3.99 for orders under £25, and is free for orders over £25.
                </p>
            </div>

            <div class="faq-item">
                <h4>When will my order arrive?</h4>
                <p>
                    Standard delivery takes 1-2 business days. You can choose a specific delivery date at checkout. 
                    Same-day delivery is available for orders placed before 10am.
                </p>
            </div>
        </div>
    </section>

     <section class="section section-alt">
        <h3>Allergies & Dietary Requirements</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>Do you offer gluten-free products?</h4>
                <p>
                    Yes! Look for the <span class="badge gluten-free">Gluten free</span> badge on product pages. 
                    You can also filter products by dietary requirements.
                </p>
            </div>

            <div class="faq-item">
                <h4>How do you prevent cross-contamination?</h4>
                <p>
                    Our gluten-free products are prepared in a dedicated area with separate equipment. 
                    However, our kitchen does handle allergens, so please contact us if you have severe allergies.
                </p>
            </div>

            <div class="faq-item">
                <h4>Can I request custom allergen-free items?</h4>
                <p>
                    We're happy to discuss custom orders! Use our <a href="contact.php">Contact form</a> 
                    to tell us about your requirements, and we'll see what we can do.
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <h3>Product Information</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>How long do your products stay fresh?</h4>
                <p>
                    Most items stay fresh for 3-5 days when stored properly. 
                    Specific storage instructions are included with your order.
                </p>
            </div>

            <div class="faq-item">
                <h4>Can I freeze your baked goods?</h4>
                <p>
                    Yes! Most of our products freeze well for up to 3 months. 
                    We recommend defrosting at room temperature before enjoying.
                </p>
            </div>

            <div class="faq-item">
                <h4>Do you use organic ingredients?</h4>
                <p>
                    We use locally sourced ingredients where possible and are committed to quality. 
                    Specific ingredient information is available on each product page.
                </p>
            </div>

            <div class="faq-item">
                <h4>Can I see the nutritional information?</h4>
                <p>
                    Nutritional information is available on request. 
                    Please contact us with the specific product names you're interested in.
                </p>
            </div>
        </div>
    </section>

      <section class="section section-alt">
        <h3>Account & Registration</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>Do I need an account to order?</h4>
                <p>
                    Yes, you need to <a href="register.php">create an account</a> to place orders. 
                    This helps us track your order history and save your preferences.
                </p>
            </div>

            <div class="faq-item">
                <h4>I forgot my password. What should I do?</h4>
                <p>
                    Click the "Forgot your password?" link on the <a href="loginpage.php">login page</a>. 
                    You'll receive an email with instructions to reset your password.
                </p>
            </div>

            <div class="faq-item">
                <h4>How do I update my account details?</h4>
                <p>
                    Log in to your account and visit your account settings page to update your email, 
                    password, or delivery address.
                </p>
            </div>

            <div class="faq-item">
                <h4>Is my information secure?</h4>
                <p>
                    Yes! We use industry-standard security measures to protect your personal information 
                    and payment details. We never share your data with third parties.
                </p>
            </div>
        </div>
    </section>

        <section class="section">
        <h3>Custom Orders & Special Occasions</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>Can you add personalized messages?</h4>
                <p>
                    Absolutely! You can add a personalized message to most cakes at checkout, 
                    or contact us for more elaborate custom text and decorations.
                </p>
            </div>

            <div class="faq-item">
                <h4>What about large orders for events?</h4>
                <p>
                    We can handle bulk orders for corporate events, parties, and weddings. 
                    Please use our <a href="contact.php">Contact form</a> to discuss your requirements.
                </p>
            </div>

            <div class="faq-item">
                <h4>How much notice do you need for custom orders?</h4>
                <p>
                    We recommend at least 1 week for standard custom orders and 2-3 weeks for 
                    elaborate celebration cakes or large events.
                </p>
            </div>
        </div>
    </section>

      <section class="section section-alt">
        <h3>Returns & Quality</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>What if I'm not satisfied with my order?</h4>
                <p>
                    Customer satisfaction is our priority. If you're not happy with your order, 
                    please contact us within 24 hours and we'll make it right.
                </p>
            </div>

            <div class="faq-item">
                <h4>Can I return or exchange items?</h4>
                <p>
                    Due to the nature of fresh food, we cannot accept returns. However, 
                    if there's an issue with quality or your order, we'll offer a refund or replacement.
                </p>
            </div>

            <div class="faq-item">
                <h4>What if my delivery arrives damaged?</h4>
                <p>
                    Please take photos and contact us immediately. We'll arrange a replacement 
                    or full refund for any damaged items.
                </p>
            </div>

            <div class="faq-item">
                <h4>How can I provide feedback?</h4>
                <p>
                    We love hearing from our customers! You can leave reviews on product pages 
                    or contact us directly through our <a href="contact.php">Contact form</a>.
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="help-contact">
            <h3>Still need help?</h3>
            <p>
                If you couldn't find the answer to your question, we're here to help! 
                Get in touch with our friendly team.
            </p>
            <div class="contact-options">
                <a href="contact.php" class="btn primary">Contact us</a>
                <p class="contact-info">
                    Email: <a href="mailto:info@group19.com">info@group19.com</a>
                </p>
            </div>
        </div>
    </section>
</main>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>


<style>
.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.faq-item {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9b88f;
}

.faq-item h4 {
    color: #c97b4a;
    margin-bottom: 10px;
    font-size: 1.1em;
}

.faq-item p {
    color: #555;
    line-height: 1.6;
}

.faq-item a {
    color: #3b271d;
    font-weight: bold;
}

.faq-item a:hover {
    text-decoration: underline;
}

.help-contact {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.contact-options {
    margin-top: 20px;
}

.contact-info {
    margin-top: 15px;
    color: #555;
}

.contact-info a {
    color: #3b271d;
    font-weight: bold;
}

body.dark .faq-item {
    background: #2a2a2a;
    border-color: #444;
}

body.dark .faq-item h4 {
    color: #e9b88f;
}

body.dark .faq-item p,
body.dark .contact-info {
    color: #ccc;
}
</style>

</body>
</html>
