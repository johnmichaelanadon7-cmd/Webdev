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
$editId  = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

$pdo = getConnection();

$products = $pdo->query("
    SELECT *
    FROM `products`
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$editing = null;

if ($editId) {
    foreach ($products as $product) {
        if ((int) $product['id'] === $editId) {
            $editing = $product;
            break;
        }
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

    <title>Manage Products | Harvest Bread Co.</title>

    <link rel="stylesheet" href="style.css">

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
        <h1>PRODUCTS</h1>
    </div>

    <div class="admin-subnav">
        <a href="admin.php">Users</a>
        <a href="admin_products.php" class="active">Products</a>
        <a href="admin_orders.php">Orders</a>
    </div>

    <?php if ($status === 'error'): ?>
        <div class="auth-message error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($status === 'success'): ?>
        <div class="auth-message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="admin-form-card">

        <h2><?= $editing ? 'Edit Product' : 'Add Product' ?></h2>

        <form method="POST" action="admin_product_actions.php">

            <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">

            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">NAME</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars($editing['name'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description">DESCRIPTION</label>
                <input
                    type="text"
                    id="description"
                    name="description"
                    value="<?= htmlspecialchars($editing['description'] ?? '') ?>"
                >
            </div>

            <div class="admin-form-row">

                <div class="form-group">
                    <label for="price">PRICE (USD)</label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars($editing['price'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="stock">STOCK</label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        min="0"
                        value="<?= htmlspecialchars($editing['stock'] ?? 0) ?>"
                        required
                    >
                </div>

            </div>

            <div class="form-group">
                <label for="image">IMAGE FILENAME (inside /image)</label>
                <input
                    type="text"
                    id="image"
                    name="image"
                    placeholder="e.g. star1.jpg"
                    value="<?= htmlspecialchars($editing['image'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input
                        type="checkbox"
                        name="is_active"
                        <?= (!$editing || $editing['is_active']) ? 'checked' : '' ?>
                    >
                    Visible on the site
                </label>
            </div>

            <button type="submit" class="auth-button shop-button">
                <?= $editing ? 'SAVE CHANGES' : 'ADD PRODUCT' ?>
            </button>

            <?php if ($editing): ?>
                <a href="admin_products.php" class="auth-switch">Cancel edit</a>
            <?php endif; ?>

        </form>

    </div>

    <div class="admin-table-wrap">

        <table class="admin-table">

            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Visible</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($products as $product): ?>

                    <tr>
                        <td>
                            <img
                                src="image/<?= htmlspecialchars($product['image']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="cart-thumb"
                            >
                        </td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td><?= (int) $product['stock'] ?></td>
                        <td>
                            <span class="role-badge role-<?= $product['is_active'] ? 'admin' : 'user' ?>">
                                <?= $product['is_active'] ? 'Yes' : 'Hidden' ?>
                            </span>
                        </td>
                        <td class="admin-actions">

                            <a href="admin_products.php?edit=<?= (int) $product['id'] ?>" class="small-button">
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="admin_product_actions.php"
                                onsubmit="return confirm('Delete this product? This cannot be undone.');"
                            >
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                <button type="submit" class="small-button danger">Delete</button>
                            </form>

                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="admin-empty">No products yet.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>
