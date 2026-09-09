<?php

session_start();

require 'database/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: orders.php');
    exit;
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$pdo = getConnection();

$sql = "
    SELECT o.*, u.username, u.email
    FROM `orders` o
    JOIN `users` u ON u.id = o.user_id
    WHERE o.id = :id
";

if (!$isAdmin) {
    $sql .= " AND o.user_id = :user_id";
}

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

if (!$isAdmin) {
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
}

$stmt->execute();

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: ' . ($isAdmin ? 'admin_orders.php' : 'orders.php'));
    exit;
}

$itemsStmt = $pdo->prepare("
    SELECT product_name, price, quantity
    FROM `order_items`
    WHERE order_id = :order_id
");

$itemsStmt->bindValue(':order_id', $id, PDO::PARAM_INT);
$itemsStmt->execute();

$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

$statuses = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order #<?= (int) $order['id'] ?> | Harvest Bread Co.</title>

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
            <a href="<?= $isAdmin ? 'admin_orders.php' : 'orders.php' ?>">
                <?= $isAdmin ? 'ALL ORDERS' : 'MY ORDERS' ?>
            </a>
        </nav>

    </div>

</header>

<main class="shop-container">

    <h1 class="shop-heading">ORDER #<?= (int) $order['id'] ?></h1>

    <div class="checkout-summary">

        <?php if ($isAdmin): ?>
            <p>
                <strong>Customer:</strong>
                <?= htmlspecialchars($order['username']) ?>
                (<?= htmlspecialchars($order['email']) ?>)
            </p>
        <?php endif; ?>

        <p><strong>Placed:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
        <p><strong>Contact number:</strong> <?= htmlspecialchars($order['contact_number']) ?></p>

        <?php if ($order['notes'] !== ''): ?>
            <p><strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?></p>
        <?php endif; ?>

        <p>
            <strong>Status:</strong>
            <span class="role-badge status-<?= htmlspecialchars($order['status']) ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span>
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

    </div>

    <?php if ($isAdmin): ?>

        <form method="POST" action="admin_order_actions.php" class="status-form">

            <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">

            <div class="form-group">
                <label for="status">UPDATE STATUS</label>
                <select id="status" name="status">
                    <?php foreach ($statuses as $statusOption): ?>
                        <option
                            value="<?= $statusOption ?>"
                            <?= $statusOption === $order['status'] ? 'selected' : '' ?>
                        >
                            <?= ucfirst($statusOption) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="auth-button shop-button">
                UPDATE STATUS
            </button>

        </form>

    <?php endif; ?>

</main>

</body>
</html>
