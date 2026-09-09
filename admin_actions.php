<?php

session_start();

require 'database/config.php';

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

        $stmt = $pdo->prepare("DELETE FROM `users` WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header(
            'Location: admin.php?status=success&message='
            . urlencode('User deleted.')
        );
        exit;
    }

    if ($action === 'make_admin' || $action === 'make_user') {

        $newRole = $action === 'make_admin' ? 'admin' : 'user';

        $stmt = $pdo->prepare("
            UPDATE `users`
            SET role = :role
            WHERE id = :id
        ");

        $stmt->bindValue(':role', $newRole);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header(
            'Location: admin.php?status=success&message='
            . urlencode('User role updated.')
        );
        exit;
    }

    header('Location: admin.php');
    exit;

} catch (PDOException $e) {

    header(
        'Location: admin.php?status=error&message='
        . urlencode($e->getMessage())
    );

    exit;
}
