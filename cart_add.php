<?php

session_start();

require 'database/config.php';
require 'cart_helpers.php';

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity  = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$quantity || $quantity < 0) {
    $quantity = 0;
}

if (!$productId) {
    header('Location: index.php#products');
    exit;
}

$pdo = getConnection();

$stmt = $pdo->prepare("
    SELECT id, stock, is_active
    FROM `products`
    WHERE id = :id
    LIMIT 1
");

$stmt->bindValue(':id', $productId, PDO::PARAM_INT);
$stmt->execute();

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product || !$product['is_active'] || (int) $product['stock'] < 1) {
    header(
        'Location: index.php?status=error&message='
        . urlencode('That item is currently unavailable.')
        . '#products'
    );
    exit;
}

cartAdd($productId, $quantity, (int) $product['stock']);

header(
    'Location: index.php?status=success&message='
    . urlencode('Added to cart.')
    . '#products'
);

exit;
