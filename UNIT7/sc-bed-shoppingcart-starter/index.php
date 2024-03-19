<?php
require 'com/icemalta/shoppingcart/model/Product.php';
require 'com/icemalta/shoppingcart/model/Cart.php';
session_start();

// Bootstrap products
use com\icemalta\shoppingcart\model\Product;

$products = [
    new Product(1, 'Getting Started with React', 90, 'Get quickly up and running with React and create a profile website in just 3 hours!', 'masterclass_react.png'),
    new Product(2, 'AI with TensorFlow & Python', 90, 'Create a machine learning model using Python and TensorFlow, focusing on image recognition and classification.', 'masterclass_ai.png'),
    new Product(3, 'Game Development with Unity', 360, 'Create a clone of the popular 2D platformer featuring an Italian plumber, starting from scratch', 'masterclass_game.png', true),
    new Product(4, 'Introduction to CSS FlexBox', 90, 'FlexBox can revolutionise how you create responsive websites. Learn how in just 3 hours!', 'masterclass_css.png'),
];
$_SESSION['products'] = $products;

// Show the correct view
$views = ['products', 'cart', 'checkout'];
$currentView = 'products';
$redirect = filter_input(INPUT_GET, 'view', FILTER_DEFAULT);
if ($redirect !== null && in_array($redirect, $views)) {
    $currentView = $redirect;
}

// Determine cart size for badge
$cartSize = isset($_SESSION['cart']) ? $_SESSION['cart']->getItemCount() : 0;
?>
<!doctype html>
<html lang="en">

<head>
    <title>Shopping Cart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles/main.css">
</head>

<body data-bs-theme="dark">
    <div class="container">
        <?php include("com/icemalta/shoppingcart/view/menu.inc.php"); ?>
        <?php include("com/icemalta/shoppingcart/view/$currentView.inc.php"); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>