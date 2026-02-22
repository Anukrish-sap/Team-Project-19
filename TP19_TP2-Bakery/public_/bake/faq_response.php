<?php

$message = strtolower($_POST['message'] ?? '');

$faqs = [
    "delivery" => "We offer delivery within 2–3 working days.",
    "price" => "All product prices are listed on the products page.",
    "gluten" => "We provide gluten-free options. Please check product descriptions.",
    "contact" => "You can contact us using the Contact page form.",
    "account" => "You can register or log in using the account links in the navigation.",
    "basket" => "You can manage your basket using the basket page."
];

foreach ($faqs as $keyword => $response) {
    if (strpos($message, $keyword) !== false) {
        echo $response;
        exit;
    }
}

echo "Sorry, I do not understand that question yet. Please check our Help page.";