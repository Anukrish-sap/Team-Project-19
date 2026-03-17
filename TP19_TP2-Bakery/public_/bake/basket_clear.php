<?php
session_start();

unset($_SESSION['basket_items']);

header('Location: basket.php');
exit;