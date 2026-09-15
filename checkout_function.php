<?php

session_start();

require 'database/config.php';
require 'cart_helpers.php';
require 'error_helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_POST['place_order'])) {
    header('Location: checkout.php');
    exit;
}

$cart = cartGet();

if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$contactNumber = trim($_POST['number'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if ($contactNumber === '') {
    header(
        'Location: checkout.php?status=error&message='
        . urlencode('A contact number is required.')
    );
    exit;
}

$pdo = getConnection();

try {

    $pdo->beginTransaction();

    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("
        SELECT id, name, price, stock
        FROM `products`
        WHERE id IN ($placeholders)
        FOR UPDATE
    ");

    $stmt->execute($ids);

    $productsById = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $product) {
        $productsById[$product['id']] = $product;
    }

    $orderItems = [];
    $total = 0.0;

    foreach ($cart as $productId => $quantity) {

        if (!isset($productsById[$productId])) {
            continue;
        }

        $product = $productsById[$productId];
        $quantity = min($quantity, (int) $product['stock']);

        if ($quantity < 1) {
            continue;
        }

        $lineTotal = $quantity * (float) $product['price'];
        $total += $lineTotal;

        $orderItems[] = [
            'product_id' => $product['id'],
            'name'       => $product['name'],
            'price'      => $product['price'],
            'quantity'   => $quantity,
        ];
    }

    if (empty($orderItems)) {
        $pdo->rollBack();

        header(
            'Location: cart.php?status=error&message='
            . urlencode('Sorry, those items are no longer available.')
        );
        exit;
    }

    $orderStmt = $pdo->prepare("
        INSERT INTO `orders`
        (user_id, total, contact_number, notes)
        VALUES
        (:user_id, :total, :contact_number, :notes)
    ");

    $orderStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $orderStmt->bindValue(':total', $total);
    $orderStmt->bindValue(':contact_number', $contactNumber);
    $orderStmt->bindValue(':notes', $notes);
    $orderStmt->execute();

    $orderId = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("
        INSERT INTO `order_items`
        (order_id, product_id, product_name, price, quantity)
        VALUES
        (:order_id, :product_id, :product_name, :price, :quantity)
    ");

    $stockStmt = $pdo->prepare("
        UPDATE `products`
        SET stock = stock - :quantity
        WHERE id = :id
    ");

    foreach ($orderItems as $item) {

        $itemStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $itemStmt->bindValue(':product_id', $item['product_id'], PDO::PARAM_INT);
        $itemStmt->bindValue(':product_name', $item['name']);
        $itemStmt->bindValue(':price', $item['price']);
        $itemStmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
        $itemStmt->execute();

        $stockStmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
        $stockStmt->bindValue(':id', $item['product_id'], PDO::PARAM_INT);
        $stockStmt->execute();
    }

    $pdo->commit();

    cartClear();

    header('Location: payment.php?id=' . $orderId);
    exit;

} catch (PDOException $e) {

    $pdo->rollBack();

    redirectWithError(
        'checkout.php',
        'Checkout',
        $e,
        'Could not place your order. Please try again.'
    );
}
