<?php

require 'database/config.php';
require 'validation.php';
require 'error_helpers.php';

if (!isset($_POST['signup'])) {
    header('Location: signup.php');
    exit;
}

$result = validateSignupInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {

    $message = implode(' ', $errors);

    header(
        'Location: signup.php?status=error&message='
        . urlencode($message)
    );

    exit;
}

try {

    $pdo = getConnection();

    /* Check if email already exists */
    $checkSql = "
        SELECT id
        FROM `users`
        WHERE email = :email
        LIMIT 1
    ";

    $checkStmt = $pdo->prepare($checkSql);

    $checkStmt->bindValue(
        ':email',
        $result['data']['email']
    );

    $checkStmt->execute();

    if ($checkStmt->fetch()) {

        header(
            'Location: signup.php?status=error&message='
            . urlencode('Email is already registered.')
        );

        exit;
    }

    /* Hash password */
    $hashedPassword = password_hash(
        $result['data']['password'],
        PASSWORD_DEFAULT
    );

    /* Insert new account */
    $sql = "
        INSERT INTO `users`
        (username, email, number, password)
        VALUES
        (:username, :email, :number, :password)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ':username',
        $result['data']['username']
    );

    $stmt->bindValue(
        ':email',
        $result['data']['email']
    );

    $stmt->bindValue(
        ':number',
        $result['data']['number']
    );

    $stmt->bindValue(
        ':password',
        $hashedPassword
    );

    $stmt->execute();

    $newId = (int) $pdo->lastInsertId();

    /* Allow success.php to display this one user's details once */
    session_start();
    $_SESSION['new_user_id'] = $newId;

    header(
        'Location: success.php?id=' . $newId
    );

    exit;

} catch (PDOException $e) {

    redirectWithError(
        'signup.php',
        'Signup',
        $e,
        'Something went wrong while creating your account. Please try again.'
    );
}