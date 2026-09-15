<?php

session_start();

require 'database/config.php';

if (!isset($_SESSION['user_id'])) {
    header(
        'Location: login.php?status=error&message='
        . urlencode('Please log in to view your orders.')
    );
    exit;
}

$pdo = getConnection();

$stmt = $pdo->prepare("
    SELECT id, total, status, created_at, payment_method, payment_status
    FROM `orders`
    WHERE user_id = :user_id
    ORDER BY created_at DESC
");

$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$paymentMethodLabels = [
    'cash'        => 'Cash',
    'credit_card' => 'Credit Card',
    'mobile_pay'  => 'Mobile Pay',
];

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders | Harvest Bread Co.</title>

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/shop.css">

</head>

<body class="auth-page">

<?php $page = 'orders'; include 'partials/header.php'; ?>

<main class="shop-container">

    <h1 class="shop-heading">MY ORDERS</h1>

    <?php if (empty($orders)): ?>

        <div class="empty-state">
            <p>You haven't placed any orders yet.</p>
            <a href="index.php#products" class="auth-button shop-button">
                BROWSE PRODUCTS
            </a>
        </div>

    <?php else: ?>

        <div class="admin-table-wrap">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($orders as $order): ?>

                        <tr>
                            <td>#<?= (int) $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td>$<?= number_format($order['total'], 2) ?></td>
                            <td>
                                <?php if ($order['payment_status'] === 'paid'): ?>
                                    <span class="role-badge status-completed">
                                        <?= htmlspecialchars($paymentMethodLabels[$order['payment_method']] ?? 'Paid') ?>
                                    </span>
                                <?php else: ?>
                                    <a href="payment.php?id=<?= (int) $order['id'] ?>" class="small-button">
                                        Pay Now
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="role-badge status-<?= htmlspecialchars($order['status']) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="order_view.php?id=<?= (int) $order['id'] ?>" class="small-button">
                                    View
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</main>

<script src="script.js"></script>

</body>
</html>
