<?php
namespace com\icemalta\shoppingcart\view;
$products = $_SESSION['products'];

?>

<div class="grid gap-0 row-gap-3">
    <div class="p-2 g-col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Our Courses</h5>
            </div>
        </div>
    </div>

    <?php foreach ($products as $product) { ?>
    <div class="p-2 g-col-12">
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="assets/<?= $product->getFeaturedImage() ?>" class="img-fluid rounded-start" alt="...">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><?= $product->getName() ?></h5>
                        <p class="card-text"><?= $product->getDescription() ?></p>
                        <p>
                        <form method="post" action="index.php?view=cart">
                            <input type="hidden" name="action" value="upsert">
                            <input type="hidden" name="productId" value="<?= $product->getId() ?>">
                            <button type="submit" class="btn btn-success"><i class="bi-cart-fill"></i> Enrol</button>
                        </form>
                        </p>
                        <p class="card-text"><small class="text-body-secondary">Class will open when 5 participants have
                                signed up.</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>