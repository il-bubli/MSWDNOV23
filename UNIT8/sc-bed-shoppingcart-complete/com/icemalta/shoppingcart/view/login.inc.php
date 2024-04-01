<?php
namespace com\icemalta\shoppingcart\view;

use com\icemalta\shoppingcart\model\User;
use com\icemalta\shoppingcart\model\Cart;

$redirect = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'products';
$loginError = false;
$registerSuccess = filter_input(INPUT_GET, 'registerSuccess', FILTER_VALIDATE_BOOLEAN) ?? false;
if (filter_var($_SERVER['REQUEST_METHOD'], FILTER_DEFAULT) === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
    $user = new User($email, $password);
    if ($user->login()) {
        $_SESSION['user'] = serialize($user);
        // If the user logs in and has an unsaved cart, save it.
        if (isset ($_SESSION['cart'])) {
            $cart = unserialize($_SESSION['cart']);
            if ($cart->id === 0) {
                $cart->setUserId($user->getId());
                $cart->save();
            }
        } else {
            // If the user logs in and has a pending cart, load it
            $cart = new Cart($user->getId());
            $cart = $cart->load();
            if ($cart->getItemCount() > 0) {
                $_SESSION['cart'] = serialize($cart);
            }
        }
        header("Location: index.php?view=$redirect");
    } else {
        $loginError = true;
    }
}
?>


<?php if ($loginError) { ?>
    <div class="alert alert-warning" role="alert">
        Your email address or password are incorrect. Please try again.
    </div>
<?php } ?>

<?php if ($registerSuccess) { ?>
    <div class="alert alert-success" role="alert">
        Your account has been created, you can now login.
    </div>
<?php } ?>

<main class="w-80 m-auto">
    <form method="post" action="index.php?view=login">
        <h1 class="h3 mb-3 fw-normal">Please sign in.</h1>

        <div class="form-floating">
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                required>
            <label for="email">Email address</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
        </div>

        <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>
        <a href='index.php?view=register'>No Account? Create One!</a>
    </form>
</main>