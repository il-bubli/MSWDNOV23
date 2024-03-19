<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">eShop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $currentView === 'products' ? 'active' : '' ?>" aria-current="page" href="index.php">Home</a>
        </li>
      </ul>
      <div class="d-flex">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <!-- <li class="nav-item">
              <a class="nav-link" href="/account" role="button">
                <i class="bi-person-fill"></i>
              </a>
            </li> -->
            <li class="nav-item">
              <a class="nav-link <?= $currentView === 'cart' ? 'active' : '' ?>" href="index.php?view=cart" role="button">
                <i class="bi-cart-fill"></i>
                <?php if ($cartSize > 0) { ?>
                <span class="badge rounded-pill badge-notification bg-danger"><?= $cartSize ?></span>
                <?php } ?>
              </a>
            </li>
        </ul>
      </div>
    </div>
  </div>
</nav>