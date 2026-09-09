<?php

session_start();

require 'database/config.php';
require 'cart_helpers.php';

if (!isset($_SESSION['user_id'])) {
    header(
        'Location: login.php?status=error&message='
        . urlencode('Please log in to check out.')
    );
    exit;
}

$pdo = getConnection();
$cart = cartGet();

if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("
    SELECT id, name, price, image, stock
    FROM `products`
    WHERE id IN ($placeholders)
");

$stmt->execute($ids);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$items = [];
$total = 0.0;

foreach ($products as $product) {

    $quantity = min(
        $cart[$product['id']] ?? 0,
        (int) $product['stock']
    );

    if ($quantity < 1) {
        continue;
    }

    $lineTotal = $quantity * (float) $product['price'];
    $total += $lineTotal;

    $items[] = [
        'name'       => $product['name'],
        'price'      => (float) $product['price'],
        'quantity'   => $quantity,
        'line_total' => $lineTotal,
    ];
}

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Checkout | Harvest Bread Co.</title>

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
            <a href="cart.php">CART</a>
        </nav>

    </div>

</header>

<main class="shop-container">

    <h1 class="shop-heading">CHECKOUT</h1>

    <?php if ($status === 'error'): ?>
        <div class="auth-message error">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="checkout-summary">

        <h2>Order Summary</h2>

        <ul class="checkout-items">

            <?php foreach ($items as $item): ?>

                <li>
                    <span><?= (int) $item['quantity'] ?>&times; <?= htmlspecialchars($item['name']) ?></span>
                    <span>$<?= number_format($item['line_total'], 2) ?></span>
                </li>

            <?php endforeach; ?>

        </ul>

        <div class="checkout-total">
            Total: <strong>$<?= number_format($total, 2) ?></strong>
        </div>

    </div>

    <form method="POST" action="checkout_function.php" class="checkout-form">

        <div class="form-group">
            <label for="number">CONTACT NUMBER</label>
            <input
                type="tel"
                id="number"
                name="number"
                value="<?= htmlspecialchars($_SESSION['number'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="notes">DELIVERY / PICKUP NOTES (optional)</label>
            <textarea
                id="notes"
                name="notes"
                rows="3"
                placeholder="e.g. pickup time, delivery address, allergies"
            ></textarea>
        </div>

        <button type="submit" name="place_order" class="auth-button shop-button">
            PLACE ORDER
        </button>

    </form>

</main>

</body>
</html>
