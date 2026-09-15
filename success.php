<?php

session_start();

require 'database/config.php';

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header('Location: signup.php');
    exit;
}

/*
 * Security: only the user who just signed up can see this page.
 * signup_function.php stores the new user's ID in the session
 * immediately after INSERT; we compare against that token here
 * and clear it so the page can only be loaded once.
 */
$allowedId = $_SESSION['new_user_id'] ?? null;
unset($_SESSION['new_user_id']);

if ($allowedId !== $id) {
    header('Location: login.php');
    exit;
}

$pdo = getConnection();

$sql = "
    SELECT id, username, email, number
    FROM `users`
    WHERE id = :id
";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(
    ':id',
    $id,
    PDO::PARAM_INT
);

$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: signup.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Account Created | Harvest Bread Co.</title>

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/auth.css">

</head>

<body class="auth-page">

<?php $page = 'success'; $minimalHeader = true; include 'partials/header.php'; ?>

<main class="auth-container">

    <section class="auth-box success-box">

        <h1>WELCOME!</h1>

        <p class="success-text">
            Your account was created successfully.
        </p>

        <div class="account-details">

            <p>
                <strong>ID</strong><br>
                <?= htmlspecialchars($user['id']) ?>
            </p>

            <p>
                <strong>USERNAME</strong><br>
                <?= htmlspecialchars($user['username']) ?>
            </p>

            <p>
                <strong>EMAIL</strong><br>
                <?= htmlspecialchars($user['email']) ?>
            </p>

            <p>
                <strong>NUMBER</strong><br>
                <?= htmlspecialchars($user['number']) ?>
            </p>

        </div>

        <a
            href="login.php"
            class="auth-button success-button"
        >
            LOGIN NOW
        </a>

    </section>

</main>

</body>
</html>