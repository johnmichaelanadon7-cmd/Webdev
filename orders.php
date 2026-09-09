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
    SELECT id, total, status, created_at
    FROM `orders`
    WHERE user_id = :user_id
    ORDER BY created_at DESC
");

$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <a href="index.php#products">PRODUCTS</a>
        </nav>

    </div>

</header>

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

</body>
</html>
