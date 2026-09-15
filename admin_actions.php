<?php

session_start();

require 'database/config.php';
require 'error_helpers.php';

/* Admin-only guard */
if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header(
        'Location: login.php?status=error&message='
        . urlencode('Admin access only.')
    );
    exit;
}

$action = $_POST['action'] ?? '';

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header('Location: admin.php');
    exit;
}

/* Admins can't act on their own account through this page */
if ($id === (int) $_SESSION['user_id']) {
    header(
        'Location: admin.php?status=error&message='
        . urlencode('You cannot change your own account here.')
    );
    exit;
}

try {

    $pdo = getConnection();

    if ($action === 'delete') {

        /* This project allows exactly one fixed admin account —
           it can never be deleted through this panel. */
        $roleStmt = $pdo->prepare("SELECT role FROM `users` WHERE id = :id LIMIT 1");
        $roleStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $roleStmt->execute();
        $target = $roleStmt->fetch(PDO::FETCH_ASSOC);

        if ($target && $target['role'] === 'admin') {
            header(
                'Location: admin.php?status=error&message='
                . urlencode('The admin account cannot be deleted.')
            );
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM `users` WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header(
            'Location: admin.php?status=success&message='
            . urlencode('User deleted.')
        );
        exit;
    }

    header('Location: admin.php');
    exit;

} catch (PDOException $e) {

    redirectWithError(
        'admin.php',
        'AdminUserAction',
        $e,
        'Something went wrong. Please try again.'
    );
}
