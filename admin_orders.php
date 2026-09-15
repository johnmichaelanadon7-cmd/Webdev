<?php

session_start();

require 'database/config.php';

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

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

$pdo = getConnection();

$orders = $pdo->query("
    SELECT o.id, o.total, o.status, o.created_at, o.payment_method,
           o.payment_status, u.username, u.email
    FROM `orders` o
    JOIN `users` u ON u.id = o.user_id
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$statuses = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];

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

    <title>Manage Orders | Harvest Bread Co.</title>

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/admin.css">

</head>

<body class="auth-page">

<header class="site-header">

    <div class="nav-container">

        <a href="index.php" class="brand">
            <img src="image/logo.svg" alt="Harvest Bread Co. Logo">
        </a>

        <nav class="main-nav">
            <a href="index.php">SITE</a>
            <a href="logout.php">LOGOUT</a>
        </nav>

    </div>

</header>

<main class="admin-container">

    <div class="admin-heading">
        <h1>ORDERS</h1>
    </div>

    <div class="admin-subnav">
        <a href="admin.php">Users</a>
        <a href="admin_products.php">Products</a>
        <a href="admin_orders.php" class="active">Orders</a>
    </div>

    <?php if ($status === 'error'): ?>
        <div class="auth-message error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($status === 'success'): ?>
        <div class="auth-message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="admin-table-wrap">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Placed</th>
                    <th>Update</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($orders as $order): ?>

                    <tr>
                        <td>#<?= (int) $order['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($order['username']) ?><br>
                            <span class="admin-you"><?= htmlspecialchars($order['email']) ?></span>
                        </td>
                        <td>$<?= number_format($order['total'], 2) ?></td>
                        <td>
                            <?php if ($order['payment_status'] === 'paid'): ?>
                                <span class="role-badge status-completed">
                                    <?= htmlspecialchars($paymentMethodLabels[$order['payment_method']] ?? 'Paid') ?>
                                </span>
                            <?php else: ?>
                                <span class="role-badge status-cancelled">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="role-badge status-<?= htmlspecialchars($order['status']) ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td>
                            <form method="POST" action="admin_order_actions.php" class="cart-qty-form">
                                <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                                <select name="status">
                                    <?php foreach ($statuses as $statusOption): ?>
                                        <option
                                            value="<?= $statusOption ?>"
                                            <?= $statusOption === $order['status'] ? 'selected' : '' ?>
                                        >
                                            <?= ucfirst($statusOption) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="small-button">Update</button>
                            </form>
                        </td>
                        <td>
                            <a href="order_view.php?id=<?= (int) $order['id'] ?>" class="small-button">
                                View
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="admin-empty">No orders yet.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>
