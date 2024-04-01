<?php
namespace com\icemalta\shoppingcart\view;

if (!isset ($_SESSION['user'])) {
    header('Location: index.php?view=login&redirect=checkout');
}
$cart = unserialize($_SESSION['cart']);
$user = unserialize($_SESSION['user']);
$cart->setUserId($user->getId());
$cart->save();
$cart->checkout();
unset($_SESSION['cart']);
?>

<div class="grid gap-0 row-gap-3">
    <div class="p-2 g-col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Checkout</h5>
            </div>
        </div>
    </div>

    <div class="p-2 g-col-12">
        <div class="alert alert-success">
            Thank you for your purchase!
        </div>
    </div>
</div>