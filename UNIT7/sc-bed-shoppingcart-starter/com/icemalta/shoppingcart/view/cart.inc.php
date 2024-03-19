<?php
namespace com\icemalta\shoppingcart\view;

use com\icemalta\shoppingcart\model\Cart;

$products = $_SESSION['products'];
$cart = $_SESSION['cart'] ?? new Cart();

if (filter_var($_SERVER['REQUEST_METHOD']) === 'POST') {
    $action = filter_input(INPUT_POST,'action');
    $productId = filter_input(INPUT_POST,'productId', FILTER_SANITIZE_NUMBER_INT);

    switch($action)
    {
        case 'upsert':
            $product = current(array_filter($products, fn($product) => $product->getId() == $productId));
            $qty = filter_input(INPUT_POST,'qty', FILTER_SANITIZE_NUMBER_INT) ?? 1;
            $cart->upsertProduct($product, $qty);
            break;
        case 'delete':
            $cart->removeProduct($productId);
            break;
   }

   $_SESSION['cart'] = $cart;
}

$showDepositWarning = count(array_filter($cart->cartItems, fn($cartItem) => $cartItem->product->requiresDeposit())) > 0;
?>

<div class="grid gap-0 row-gap-3">
    <div class="p-2 g-col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Your Cart</h5>
            </div>
        </div>
    </div>

    <?php if ($showDepositWarning) { ?>
        <div class="p-2 g-col-12">
            <div class="alert alert-warning">
                One or more of the items in your cart require a deposit.
            </div>
        </div>
    <?php } ?>

    <div class="p-2 g-col-12">
        <div class="card">
            <div class="card-body">
                <?php if (count($cart->cartItems) > 0) { ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart->cartItems as $cartItem) { ?>
                                <tr>
                                    <th scope="row">
                                        <?= $cartItem->product->getName() ?>
                                    </th>
                                    <td>
                                        <form method="post" action="/index.php?view=cart"
                                            class="row row-cols-lg-auto g-3 align-items-center">
                                            <input type="hidden" name="action" value="upsert">
                                            <input type="hidden" value="<?= $cartItem->product->getId() ?>" name="productId">
                                            <div class="col-12">
                                                <input class="form-control form-control-sm" type="number" min="0"
                                                    value="<?= $cartItem->qty ?>" name="qty">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-sm btn-secondary"><i
                                                        class="bi-arrow-clockwise"></i></button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>&euro;
                                        <?= $cartItem->getSubtotal() ?>
                                    </td>
                                    <td>
                                        <form method="post" action="/index.php?view=cart">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" value="<?= $cartItem->product->getId() ?>" name="productId">
                                            <button type="submit" class="btn btn-sm btn-danger"><i
                                                    class="bi-trash3-fill"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    Your cart is empty.
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if (count($cart->cartItems) > 0) { ?>
        <div class="p-2 g-col-12">
            <div class="card">
                <div class="card-header">
                    Your total
                </div>
                <div class="card-body">
                    <form name="checkoutForm" action="index.php?view=checkout" method="post">
                        <div class="row justify-content-between">
                            <div class="col-md-8">
                                <h3>&euro;
                                    <?= $cart->getTotal() ?>
                                </h3>
                            </div>
                            <div class="col-md-4">
                                <div class="row justify-content-end">
                                    <div class="col-md-8">
                                        <input class="form-control float-end" type="email" placeholder="Your email here">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-success float-end"><i class="bi-cart-fill"></i>
                                            Checkout</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</div>