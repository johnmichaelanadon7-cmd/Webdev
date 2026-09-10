<?php

session_start();

require 'database/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$pdo = getConnection();

$stmt = $pdo->prepare("
    SELECT id, total, status, contact_number, notes, created_at,
           payment_method, payment_status
    FROM `orders`
    WHERE id = :id AND user_id = :user_id
    LIMIT 1
");

$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

if ($order['payment_status'] !== 'paid') {
    header('Location: payment.php?id=' . $id);
    exit;
}

$paymentMethodLabels = [
    'cash'        => 'Cash',
    'credit_card' => 'Credit Card',
    'mobile_pay'  => 'Mobile Pay',
];

$paymentMethodLabel = $paymentMethodLabels[$order['payment_method']] ?? 'Unknown';

$itemsStmt = $pdo->prepare("
    SELECT product_name, price, quantity
    FROM `order_items`
    WHERE order_id = :order_id
");

$itemsStmt->bindValue(':order_id', $id, PDO::PARAM_INT);
$itemsStmt->execute();

$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Placed | Harvest Bread Co.</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="auth-page">

<header class="site-header">

    <div class="nav-container">

        <a href="index.php" class="brand">
            <img src="image/logo.svg" alt="Harvest Bread Co. Logo">
        </a>

    </div>

</header>

<main class="auth-container">

    <section class="auth-box success-box">

        <span class="payment-done-badge">✓ PAYMENT DONE</span>

        <h1>THANK YOU!</h1>

        <p class="success-text">
            Order #<?= (int) $order['id'] ?> has been placed and paid via
            <strong><?= htmlspecialchars($paymentMethodLabel) ?></strong>.
        </p>

        <ul class="checkout-items">

            <?php foreach ($items as $item): ?>

                <li>
                    <span><?= (int) $item['quantity'] ?>&times; <?= htmlspecialchars($item['product_name']) ?></span>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </li>

            <?php endforeach; ?>

        </ul>

        <div class="checkout-total">
            Total: <strong>$<?= number_format($order['total'], 2) ?></strong>
        </div>

        <p class="order-status">
            Status:
            <span class="role-badge status-<?= htmlspecialchars($order['status']) ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span>
        </p>

        <a href="orders.php" class="auth-button success-button">
            VIEW MY ORDERS
        </a>

        <a href="payment_history.php" class="auth-switch">
            View Payment History
        </a>

        <a href="index.php#products" class="auth-switch">
            Continue shopping
        </a>

    </section>

</main>

</body>
</html>
