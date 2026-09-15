<?php

session_start();

require 'database/config.php';
require 'error_helpers.php';

if (!isset($_POST['login'])) {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if ($email === '') {
    $errors[] = 'Email is required.';
}

if ($password === '') {
    $errors[] = 'Password is required.';
}

if (!empty($errors)) {

    $message = implode(' ', $errors);

    header(
        'Location: login.php?status=error&message='
        . urlencode($message)
    );

    exit;
}

try {

    $pdo = getConnection();

    $sql = "
        SELECT id, username, email, number, password, role
        FROM `users`
        WHERE email = :email
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ':email',
        $email
    );

    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        !$user ||
        !password_verify(
            $password,
            $user['password']
        )
    ) {

        header(
            'Location: login.php?status=error&message='
            . urlencode('Invalid email or password.')
        );

        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['number'] = $user['number'];
    $_SESSION['role'] = $user['role'];

    header(
        'Location: ' . ($user['role'] === 'admin' ? 'admin.php' : 'index.php')
    );

    exit;

} catch (PDOException $e) {

    redirectWithError(
        'login.php',
        'Login',
        $e,
        'Something went wrong while logging you in. Please try again.'
    );
}