<?php

session_start();

require 'database/config.php';
require 'cart_helpers.php';

$pdo = getConnection();
$cart = cartGet();

$items = [];
$total = 0.0;

if (!empty($cart)) {

    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("
        SELECT id, name, price, image, stock
        FROM `products`
        WHERE id IN ($placeholders)
    ");

    $stmt->execute($ids);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            'id'         => $product['id'],
            'name'       => $product['name'],
            'price'      => (float) $product['price'],
            'image'      => $product['image'],
            'stock'      => (int) $product['stock'],
            'quantity'   => $quantity,
            'line_total' => $lineTotal,
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Your Cart | Harvest Bread Co.</title>

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

    <h1 class="shop-heading">YOUR CART</h1>

    <?php if (empty($items)): ?>

        <div class="empty-state">
            <p>Your cart is empty.</p>
            <a href="index.php#products" class="auth-button shop-button">
                BROWSE PRODUCTS
            </a>
        </div>

    <?php else: ?>

        <div class="cart-table-wrap">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th></th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($items as $item): ?>

                        <tr>
                            <td>
                                <img
                                    src="image/<?= htmlspecialchars($item['image']) ?>"
                                    alt="<?= htmlspecialchars($item['name']) ?>"
                                    class="cart-thumb"
                                >
                            </td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <form method="POST" action="cart_update.php" class="cart-qty-form">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input
                                        type="number"
                                        name="quantity"
                                        value="<?= (int) $item['quantity'] ?>"
                                        min="1"
                                        max="<?= (int) $item['stock'] ?>"
                                    >
                                    <button type="submit" class="small-button">Update</button>
                                </form>
                            </td>
                            <td>$<?= number_format($item['line_total'], 2) ?></td>
                            <td>
                                <form method="POST" action="cart_update.php">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="small-button danger">Remove</button>
                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <div class="cart-summary">

            <div class="cart-total">
                Total: <strong>$<?= number_format($total, 2) ?></strong>
            </div>

            <a href="checkout.php" class="auth-button shop-button">
                PROCEED TO CHECKOUT
            </a>

        </div>

    <?php endif; ?>

</main>

</body>
</html>
