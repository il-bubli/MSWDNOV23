<?php
session_start();
$email = '';
$comments = '';
$maxSize = ini_get('upload_max_filesize');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user = filter_input(INPUT_POST, 'username');
    $_SESSION['username'] = $user;
    
    $rememberMe = filter_input(INPUT_POST,'remember');
    if ($rememberMe) {
        setcookie('username','$user', time() +604800,'/');
    }

}elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate email

    if (isset($inputEmail) && $inputEmail !== '') {
        if (filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {
            $email = $inputEmail;
        } else {
            $errors[] = 'E-mail address is invalid.';
        }
    } else {
        $errors[] = 'E-mail address is missing';
    }

    // Validate comments
    $inputComments = filter_input(INPUT_POST, 'comments');
    if (isset($inputComments) && $inputComments !== '') {
        $comments = $inputComments;
    } else {
        $errors[] = 'Comments are missing.';
    }

    // Validate reason for contact
    $inputReason = filter_input(INPUT_POST, 'reason', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!isset($inputReason) || count($inputReason) === 0) {
        $errors[] = 'Please choose at least one reason for contacting us.';
    }

    // Validate contact method
    $inputContactMethod = filter_input(INPUT_POST, 'contact_method', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!isset($inputContactMethod) || count($inputContactMethod) === 0) {
        $errors[] = 'Please specify a method by which we can get in touch.';
    }


    $comments = $_POST['comments'];
    if (count($errors) === 0) {
        printf('<p>Received email: %s and comments %s</p>', $email, $comments);
        echo '<p>Reasons for contacting us:</p>';
        echo '<ul>';
        foreach ($_POST['reason'] as $reason) {
            echo "<li>$reason</li>";
        }
        echo '</ul>';
        printf('<p>We can contact you by %s</p>', join(', ', $_POST['contact_method']));
        printf('You %s want to subscribe to our newsletter', $_POST['newsletter'] === 'yes' ? '' : 'do not');
    }

    if (isset($_FILES['uploadedFile']) && is_uploaded_file($_FILES['uploadedFile']['tmp_name'])) {
        $uploadedFile = $_FILES['uploadedFile']['tmp_name'];

        // Validate file type
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $uploadedFile);
        $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'The file you uploaded is not an accepted type (accepted: PNG, JPG, GIF).';
        }

        // Validate file size
        if ($_FILES['uploadedFile']['size'] > 2097152) {
            $errors[] = "The file you uploaded is too big. Max size: $maxSize";

            if (count($errors) === 0) {
                $uploadFolder = 'uploads/';
                $uniqueId = time() . uniqid(rand());
                $origName = pathinfo($_FILES['uploadedFile']['name'])['filename'];
                $ext = pathinfo($_FILES['uploadedFile']['name'])['extension'];
                $destFile = $uploadFolder . $origName . "_$uniqueId." . $ext;
                if (move_uploaded_file($uploadedFile, $destFile)) {
                    echo "<p>File uploaded successfully.</p>";
                } else {
                    echo "<p>There was an error uploading the file.</p>";
                }
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Using Forms</title>
    </head>
    <body>
    <style>
        .validation_message {
            font-size: small;
            font-style: italic;
            color: red;
            margin: 0;
            display: none;
        }

        .validation_invalid {
            outline: 1px solid red;
        }
    </style>
    <?php if (count($errors) > 0) { ?>
            <div id="error_alert"
                style="display: block; background-color: #e3767d; border-radius: 5px; max-width: 400px; padding: 5px;">
                <?php
                foreach ($errors as $error) {
                    echo "<li>$error</li>";
                }
                ?>
            </div>
    <?php } ?>

        <h3>Contact Us</h3>
        <form id="contactForm" ectype="multipart/form-data" name="contact_form" method="POST" action="index.php">
            <label for="email" style="display: inline-block; width: 100px;">Email</label>
            <input type="email" id="email" name="email" value="<?= $email ?>"><br><br>
            <p class="validation_message">Please enter a valid email address.</p>

            <label for="reason" style="display: inline-block; width: 100px;">Query Type</label>
            <select name="reason[]" id="reason" multiple>
                <option name="tech_support" <?= isset($_POST['reason']) && in_array('Technical Support', $_POST['reason']) ? 'selected' : '' ?>>Technical Support</option>
                <option name="sales" <?= isset($_POST['reason']) && in_array('Sales', $_POST['reason']) ? 'selected' : '' ?>>
                    Sales</option>
                <option name="bug" <?= isset($_POST['reason']) && in_array('Bug Report', $_POST['reason']) ? 'selected' : '' ?>>Bug Report</option>
                <option name="general" <?= isset($_POST['reason']) && in_array('General', $_POST['reason']) ? 'selected' : '' ?>>General</option>
            </select>
            <p class="validation_message">Please choose one or more reasons for your query.</p>
            <br><br>

            <label for="contact_method" style="display: inline-block; width: 100px;">Contact me by</label>
            <input type="checkbox" name="contact_method[]" value="email" <?= isset($_POST['contact_method']) && in_array('email', $_POST['contact_method']) ? 'checked' : '' ?>>Email
            <input type="checkbox" name="contact_method[]" value="phone" <?= isset($_POST['contact_method']) && in_array('phone', $_POST['contact_method']) ? 'checked' : '' ?>>Phone
            <input type="checkbox" name="contact_method[]" value="sms" <?= isset($_POST['contact_method']) && in_array('sms', $_POST['contact_method']) ? 'checked' : '' ?>>SMS
            <input type="checkbox" name="contact_method[]" value="whatsapp" <?= isset($_POST['contact_method']) && in_array('whatsapp', $_POST['contact_method']) ? 'checked' : '' ?>>WhatsApp
            <p class="validation_message">Please tell us how you'd like us to get back to you.</p>
            <br><br>

            <label for="newsletter" style="display: inline-block; width: 100px">Subscribe to Newsletter</label>
            <input type="radio" name="newsletter" value="yes" <?= isset($_POST['newsletter']) && $_POST['newsletter'] === 'yes' ? 'checked' : '' ?>>Yes
            <input type="radio" name="newsletter" value="no" <?= !isset($_POST['newsletter']) || $_POST['newsletter'] === 'no' ? 'checked' : '' ?>>No
            <br><br>

            <label for="comments" style="display: inline-block; width: 100px;">Comments</label>
            <textarea id="comments" name="comments"><?= $comments ?></textarea><br>
            p class="validation_message">Please enter your query.</p>
            <br><br>
            
            <label for="uploadedFile" style="display: inline-block; width: 100px;">Attachment</label>
            <input type="file" name="uploadedFile" accept="image/png, image/jpeg, image/gif"> (Max Size:
            <?= $maxSize ?>)
            <br><br>

            <label style="display: inline-block; width: 100px;"><label>
            <input type="submit" value="Send">
        </form>

        <?php
        if (isset($_SESSION['username'])) {
            printf('<h3>Welcome back, %s', $_SESSION['username']);
        } elseif (isset($_COOKIE['username'])) {
            $_SESSION['username'] = $_COOKIE['username'];
            printf('<h3>Welcome back, %s', $_SESSION['username']);
        }else {
        ?>
        <form name = "welcomeForm" method="post" action="index.php" style="margin-top: 20px;">
            <label for="username" style="display: inline-block; width: 100px;"> Username</label>

            <label style="display: inline-block; width 100px;"></label>
            <input type="checkbox" name="remember" value="remember">Remember me<br><br>
            <label style="display: inline-block; width: 100px;"></label>

            <input type="text" name="username" id="username" required>
            <input type="hidden" name="action" value="saveUser">
            <input type="submit" value="Save">

        </form>
        <?php } ?>

        <script>
        document.getElementById('contactForm').addEventListener('submit', validateForm);

        function validateForm(evt) {
            let contains_errors = false;

            // Reset validation
            messages = document.getElementsByClassName('validation_message');
            Array.from(messages).forEach((el) => {
                el.style.display = 'none';
            });
            invalid = document.getElementsByClassName('validation_invalid');
            Array.from(invalid).forEach((el) => {
                el.classList.remove('validation_invalid');
            });

            // Email
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            const email = document.getElementById('email');
            if (email.value === '' || !re.test(email.value.toLowerCase())) {
                contains_errors = true;
                email.classList.add('validation_invalid');
                getNextSibling(email, '.validation_message').style.display = 'block';
            }

            // Query Type
            const selected = document.querySelectorAll('#reason option:checked');
            const values = Array.from(selected).map(el => el.value);
            if (values.length === 0) {
                contains_errors = true;
                const reason = document.getElementById('reason');
                reason.classList.add('validation_invalid');
                getNextSibling(reason, '.validation_message').style.display = 'block';
            }

            // Contact Method
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            if (!checkedOne) {
                contains_errors = true;
                for (checkbox of checkboxes) {
                    checkbox.classList.add('validation_invalid');
                }
                getNextSibling(checkboxes[0], '.validation_message').style.display = 'block';
            }

            // Comments
            const comments = document.getElementById('comments');
            if (comments.value === '') {
                contains_errors = true;
                comments.classList.add('validation_invalid');
                getNextSibling(comments, '.validation_message').style.display = 'block';
            }

            if (contains_errors) {
                evt.preventDefault();
            }
        }

        // https://gomakethings.com/finding-the-next-and-previous-sibling-elements-that-match-a-selector-with-vanilla-js/
        const getNextSibling = function (elem, selector) {
            // Get the next sibling element
            var sibling = elem.nextElementSibling;
            // If there's no selector, return the first sibling
            if (!selector) return sibling;
            // If the sibling matches our selector, use it
            // If not, jump to the next sibling and continue the loop
            while (sibling) {
                if (sibling.matches(selector)) return sibling;
                sibling = sibling.nextElementSibling
            }
        };
    </script>
    </body>
</html>