<?php

session_start();

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | Harvest Bread Co.</title>

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/auth.css">

</head>

<body class="auth-page">

<?php $page = 'login'; include 'partials/header.php'; ?>

<main class="auth-container">

    <section class="auth-box">

        <h1>LOGIN</h1>

        <p class="auth-subtitle">
            Welcome back to Harvest Bread Co.
        </p>

        <?php if ($status === 'error'): ?>

            <div class="auth-message error">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <?php if ($status === 'success'): ?>

            <div class="auth-message success">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="login_function.php">

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

                <label for="password">PASSWORD</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button
                type="submit"
                name="login"
                class="auth-button"
            >
                LOGIN
            </button>

        </form>

        <p class="auth-switch">
            Don't have an account?
            <a href="signup.php">SIGN UP</a>
        </p>

    </section>

</main>

<script src="script.js"></script>

</body>
</html>