<?php
session_start();
header('Content-Type: application/json');

$message = strtolower(trim($_POST['message'] ?? ''));

if (!$message) {
    echo json_encode([
        "reply" => "Hello. I can help with orders, delivery times, product information, payments, or account support.",
        "buttons" => ["How to Order", "Delivery Times", "View Products"]
    ]);
    exit;
}

function scoreIntent($message, $keywords) {
    $score = 0;
    foreach ($keywords as $word) {
        if (strpos($message, $word) !== false) {
            $score++;
        }
    }
    return $score;
}

$intents = [

    "order" => [
        "keywords" => ["order", "buy", "purchase", "how can i order", "how do i order", "place order"],
        "response" => "Ordering is simple. Visit our Products page, select your items, add them to your basket, and proceed to checkout.",
        "buttons" => ["View Products", "View Basket"]
    ],

    "products" => [
        "keywords" => ["what products", "what do you sell", "what have you got", "items available", "products available"],
        "response" => "We offer a range of freshly prepared baked goods including cakes, pastries, and specialty treats. You can browse the full selection on our Products page.",
        "buttons" => ["View Products"]
    ],

    "delivery" => [
        "keywords" => ["delivery", "shipping", "how long", "when will", "delivery time"],
        "response" => "Our standard delivery time is 2 to 3 working days. You will receive confirmation once your order has been dispatched.",
        "buttons" => ["Track an Order", "Place an Order"]
    ],

    "availability" => [
        "keywords" => ["available", "in stock", "availability"],
        "response" => "If a product appears on our Products page, it is currently in stock. Availability updates automatically.",
        "buttons" => ["View Products"]
    ],

    "payment" => [
        "keywords" => ["payment", "pay", "card", "checkout problem", "card declined"],
        "response" => "Payments are processed securely during checkout. If your card was declined, please check your details or contact your bank.",
        "buttons" => ["Go to Basket"]
    ],

    "account" => [
        "keywords" => ["login", "register", "account", "sign in", "sign up"],
        "response" => "You can log in or create an account using the navigation menu. Let me know if you are experiencing access issues.",
        "buttons" => ["Login", "Register"]
    ],

    "help" => [
        "keywords" => ["help", "support", "problem", "issue"],
        "response" => "I am here to assist. Could you describe your issue in a little more detail?",
        "buttons" => []
    ],

    "greeting" => [
        "keywords" => ["hi", "hello", "hey"],
        "response" => "Hello. How can I assist you today?",
        "buttons" => ["How to Order", "Delivery Times", "View Products"]
    ]

];

$bestIntent = null;
$highestScore = 0;

foreach ($intents as $intent => $data) {
    $score = scoreIntent($message, $data["keywords"]);
    if ($score > $highestScore) {
        $highestScore = $score;
        $bestIntent = $intent;
    }
}

if ($highestScore > 0 && $bestIntent !== null) {
    $response = $intents[$bestIntent]["response"];
    $buttons = $intents[$bestIntent]["buttons"];
} else {
    $response = "I can help with ordering, delivery times, product details, payments, or account access. What would you like assistance with?";
    $buttons = ["How to Order", "Delivery Times", "View Products"];
}

echo json_encode([
    "reply" => $response,
    "buttons" => $buttons
]);
