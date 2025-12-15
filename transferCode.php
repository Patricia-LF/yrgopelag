<?php

declare(strict_types=1);

session_start();

// This password is fetched from a database based on the given email address
// from the request. Example SQL-query:
// SELECT * FROM users WHERE email = $_POST['email']
$password = '$2y$10$lhi5VQSGjTx9kFyW3caI6uK4InE.oRPjtz8t0bnilQXTdbHAZpqpa';

if (isset($_POST['password'])) {
    if (password_verify($_POST['password'], $password)) {
        $_SESSION['authenticated'] = true;
    }
}

// We use the header function to redirect the user back to the start page.
header('Location: /');
?>



<?php
// We start the session in order to check wheater the user is logged in or not.
// If we don't start the session we can't fetch the authenticated variable from
// the request.
session_start();
?>
<!DOCTYPE html>

<body>
    <!--
    We check if the user is authenticated or not. If the user isn't logged
    in we display the login form and if the user is authenticated we display
    the text "You're logged in!".
    -->
    <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] !== true) : ?>
        <form action="login.php" method="post">
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
            </div>

            <button type="submit">Login</button>
        </form>
    <?php else : ?>
        <p>You're logged in!</p>
    <?php endif; ?>
</body>