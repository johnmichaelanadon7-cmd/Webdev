<?php

session_start();

require 'database/config.php';
require 'cart_helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$action    = $_POST['action'] ?? 'update';

if (!$productId) {
    header('Location: cart.php');
    exit;
}

if ($action === 'remove') {
    cartRemove($productId);
    header('Location: cart.php');
    exit;
}

$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if ($quantity === false || $quantity === null) {
    header('Location: cart.php');
    exit;
}

$pdo = getConnection();

$stmt = $pdo->prepare("
    SELECT stock
    FROM `products`
    WHERE id = :id
    LIMIT 1
");

$stmt->bindValue(':id', $productId, PDO::PARAM_INT);
$stmt->execute();

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    cartRemove($productId);
    header('Location: cart.php');
    exit;
}

cartSetQuantity($productId, $quantity, (int) $product['stock']);

header('Location: cart.php');
exit;
