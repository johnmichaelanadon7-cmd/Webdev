<?php

session_start();

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$id      = $_GET['id'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Harvest Bread Co.</title>

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/auth.css">
</head>

<body class="auth-page">

<?php $page = 'signup'; include 'partials/header.php'; ?>

<main class="auth-container">

    <section class="auth-box">

        <h1>SIGN UP</h1>

        <p class="auth-subtitle">
            Create your Harvest Bread Co. account
        </p>

        <?php if ($status === 'error'): ?>

            <div class="auth-message error">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="signup_function.php">

            <div class="form-group">

                <label for="username">USERNAME</label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    required
                >

            </div>

            <div class="form-group">

                <label for="email">EMAIL</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="number">NUMBER</label>

                <input
                    type="tel"
                    id="number"
                    name="number"
                    placeholder="Enter your phone number"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">PASSWORD</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <div class="form-group">

                <label for="confirmation_password">
                    CONFIRMATION PASSWORD
                </label>

                <input
                    type="password"
                    id="confirmation_password"
                    name="confirmation_password"
                    placeholder="Confirm your password"
                    required
                >

            </div>

            <button
                type="submit"
                name="signup"
                class="auth-button"
            >
                SIGN UP
            </button>

        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="login.php">LOGIN</a>
        </p>

    </section>

</main>

<script src="script.js"></script>

</body>
</html>