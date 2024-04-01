<?php
namespace com\icemalta\shoppingcart\view;

if (filter_var($_SERVER['REQUEST_METHOD'], FILTER_DEFAULT) === 'POST') {
    unset($_SESSION['user']);
    session_destroy();
    header('Location: index.php?view=products');
} elseif (!isset ($_SESSION['user'])) {
    header('Location: index.php?view=login&redirect=account');
}
$user = unserialize($_SESSION['user']);
?>

<div class="grid gap-0 row-gap-3">
    <div class="p-2 g-col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">My Account</h5>
            </div>
        </div>
    </div>
</div>

<div class="p-2 g-col-12">
    <div class="card mb-3">
        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Access Level</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?= $user->getEmail() ?>
                    </td>
                    <td>
                        <?= $user->getFirstName() ?>
                    </td>
                    <td>
                        <?= $user->getLastName() ?>
                    </td>
                    <td>
                        <?= $user->getAccessLevel() ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="p-2 g-col-12">
    <div class="card mb-3">
        <div class="col-md-4">
            <form method="post" action="index.php?view=account">
            <button type="submit" class="btn btn-danger"><i class="bi-box-arrow-right"></i>
                Sign out</button>
            </form>
        </div>
    </div>
</div>