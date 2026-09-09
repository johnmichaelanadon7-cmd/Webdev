<?php

session_start();

require 'database/config.php';

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header('Location: login.php');
    exit;
}

$id        = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$newStatus = $_POST['status'] ?? '';

$validStatuses = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];

if (!$id || !in_array($newStatus, $validStatuses, true)) {
    header('Location: admin_orders.php');
    exit;
}

$pdo = getConnection();

try {

    $stmt = $pdo->prepare("
        UPDATE `orders`
        SET status = :status
        WHERE id = :id
    ");

    $stmt->bindValue(':status', $newStatus);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header(
        'Location: admin_orders.php?status=success&message='
        . urlencode('Order status updated.')
    );
    exit;

} catch (PDOException $e) {

    header(
        'Location: admin_orders.php?status=error&message='
        . urlencode($e->getMessage())
    );

    exit;
}
