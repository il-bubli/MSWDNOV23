<?php
namespace com\icemalta\shoppingcart\view;

use com\icemalta\shoppingcart\model\Cart;

$_SESSION['cart'] = new Cart();
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