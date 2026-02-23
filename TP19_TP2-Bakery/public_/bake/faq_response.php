<?php
session_start();

$message = strtolower(trim($_POST['message'] ?? ''));

if (!$message) {
    echo "How can I help you today?";
    exit;
}

// Store last message in session for context
$_SESSION['last_message'] = $message;

// Intent keyword groups
$intents = [
    "order" => ["order", "buy", "purchase", "place order"],
    "availability" => ["available", "availability", "stock", "in stock"],
    "delivery" => ["delivery", "shipping", "postage"],
    "payment" => ["payment", "pay", "card", "checkout"],
    "account" => ["login", "register", "account", "sign in", "sign up"],
    "basket" => ["basket", "cart"],
    "help" => ["help", "support", "problem", "issue"],
    "contact" => ["contact", "email", "phone"],
    "about" => ["about", "company", "who are you"]
];

// Score each intent
$scores = [];

foreach ($intents as $intent => $keywords) {
    $scores[$intent] = 0;
    foreach ($keywords as $word) {
        if (strpos($message, $word) !== false) {
            $scores[$intent]++;
        }
    }
}

// Find best match
$bestIntent = array_keys($scores, max($scores))[0];

$response = "";

// Only respond if at least 1 keyword matched
if (max($scores) > 0) {

    switch ($bestIntent) {

        case "order":
            $response = "You can place an order through our Products page by adding items to your basket and proceeding to checkout. Are you looking for a specific product?";
            break;

        case "availability":
            $response = "If a product appears on our Products page, it is currently in stock. Would you like help checking a specific item?";
            break;

        case "delivery":
            $response = "We offer delivery on all orders. Delivery options and estimated times are shown at checkout. Would you like more information about delivery costs?";
            break;

        case "payment":
            $response = "We accept secure online payments during checkout. If you are experiencing an issue, please let me know what stage you are stuck at.";
            break;

        case "account":
            $response = "You can register or log in using the links at the top of the page. Are you having trouble signing in?";
            break;

        case "basket":
            $response = "You can manage your basket from the Basket page where you can update quantities or proceed to checkout.";
            break;

        case "help":
            $response = "I am here to help. Could you describe the issue in a little more detail?";
            break;

        case "contact":
            $response = "You can reach us through the Contact page. Would you like me to direct you there?";
            break;

        case "about":
            $response = "You can learn more about us on the About Us page. Is there something specific you would like to know?";
            break;
    }

} else {

    // Context awareness example
    if (isset($_SESSION['last_message'])) {
        $response = "I want to make sure I help correctly. Could you give a bit more detail about what you need assistance with?";
    } else {
        $response = "How can I assist you today?";
    }
}

echo $response;
