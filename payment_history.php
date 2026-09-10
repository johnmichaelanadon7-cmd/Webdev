<?php

session_start();

require 'database/config.php';

if (!isset($_SESSION['user_id'])) {
    header(
        'Location: login.php?status=error&message='
        . urlencode('Please log in to view your payment history.')
    );
    exit;
}

$pdo = getConnection();

$stmt = $pdo->prepare("
    SELECT
        pt.id,
        pt.order_id,
        pt.method,
        pt.provider,
        pt.amount,
        pt.status,
        pt.created_at
    FROM `payment_transactions` pt
    WHERE pt.user_id = :user_id
    ORDER BY pt.created_at DESC
");

$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$methodLabels = [
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

    <title>Payment History | Harvest Bread Co.</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="auth-page">

<header class="site-header">

    <div class="nav-container">

        <a href="index.php" class="brand">
            <img src="image/logo.svg" alt="Harvest Bread Co. Logo">
        </a>

        <nav class="main-nav">
            <a href="index.php">HOME</a>
            <a href="orders.php">MY ORDERS</a>
            <a href="index.php#products">PRODUCTS</a>
        </nav>

    </div>

</header>

<main class="shop-container">

    <h1 class="shop-heading">PAYMENT HISTORY</h1>

    <?php if (empty($transactions)): ?>

        <div class="empty-state">
            <p>You don't have any recorded payments yet.</p>
            <a href="index.php#products" class="auth-button shop-button">
                BROWSE PRODUCTS
            </a>
        </div>

    <?php else: ?>

        <div class="admin-table-wrap">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Transaction #</th>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($transactions as $txn): ?>

                        <tr>
                            <td class="payment-history-transaction">
                                #<?= (int) $txn['id'] ?>
                            </td>
                            <td>#<?= (int) $txn['order_id'] ?></td>
                            <td><?= htmlspecialchars($txn['created_at']) ?></td>
                            <td>
                                <?= htmlspecialchars($methodLabels[$txn['method']] ?? $txn['method']) ?>
                                <?php if ($txn['provider']): ?>
                                    (<?= htmlspecialchars($txn['provider']) ?>)
                                <?php endif; ?>
                            </td>
                            <td>$<?= number_format($txn['amount'], 2) ?></td>
                            <td>
                                <span class="role-badge status-completed">
                                    <?= htmlspecialchars(ucfirst($txn['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="order_view.php?id=<?= (int) $txn['order_id'] ?>" class="small-button">
                                    View Order
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</main>

</body>
</html>
