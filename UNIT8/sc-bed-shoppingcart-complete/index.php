<?php

require 'com/icemalta/shoppingcart/model/DBConnect.php';
require 'com/icemalta/shoppingcart/model/User.php';
require 'com/icemalta/shoppingcart/model/Product.php';
require 'com/icemalta/shoppingcart/model/Cart.php';
session_start();



// Show the correct view
$views = ['products', 'cart', 'checkout', 'login', 'register', 'account'];
$currentView = 'products';
$redirect = filter_input(INPUT_GET, 'view', FILTER_DEFAULT);
if ($redirect !== null && in_array($redirect, $views)) {
    $currentView = $redirect;
}

// Determine cart size for badge
$cartSize = isset($_SESSION['cart']) ? unserialize($_SESSION['cart'])->getItemCount() : 0;
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