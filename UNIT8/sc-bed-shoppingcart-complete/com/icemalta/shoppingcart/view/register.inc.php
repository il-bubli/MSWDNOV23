<?php
namespace com\icemalta\shoppingcart\view;

use com\icemalta\shoppingcart\model\User;

$registerError = false;
if (filter_var($_SERVER['REQUEST_METHOD'], FILTER_DEFAULT) === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
    $user = new User($email, $password);
    if ($user->isEmailAvailable()) {
        $firstName = filter_input(INPUT_POST, 'firstName', FILTER_DEFAULT);
        $lastName = filter_input(INPUT_POST, 'lastName', FILTER_DEFAULT);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->save();
        header('Location: index.php?view=login&registerSuccess=true');
    } else {
        $registerError = true;
    }
}
?>


<?php if ($registerError) { ?>
        <div class="alert alert-warning" role="alert">
            That email address is already in use. If you have an account, please <a href='index.php?view=login'>sign in</a>.
        </div>
<?php } ?>

<main class="w-80 m-auto">
    <form method="post" action="index.php?view=register">
        <h1 class="h3 mb-3 fw-normal">Create an account.</h1>

        <div class="form-floating">
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
            <label for="email">Email address</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
        </div>
        <div class="form-floating">
            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Your name here"
                required>
            <label for="firstName">First name</label>
        </div>
        <div class="form-floating">
            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Your surname here"
                required>
            <label for="lastName">Surname</label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit">Register</button>
        <a href='index.php?view=register'>No Account? Create One!</a>
    </form>
</main>