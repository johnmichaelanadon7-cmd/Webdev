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

$pdo = getConnection();

$stmt = $pdo->prepare("
    SELECT id, total, payment_status
    FROM `orders`
    WHERE id = :id AND user_id = :user_id
    LIMIT 1
");

$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

/* Already paid? Nothing to do here anymore. */
if ($order['payment_status'] === 'paid') {
    header('Location: order_success.php?id=' . $id);
    exit;
}

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$method  = $_GET['method'] ?? 'cash';

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Payment | Harvest Bread Co.</title>

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
        </nav>

    </div>

</header>

<main class="shop-container">

    <h1 class="shop-heading">PAYMENT</h1>

    <?php if ($status === 'error'): ?>
        <div class="auth-message error">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="checkout-summary">
        <h2>Order #<?= (int) $order['id'] ?></h2>
        <div class="checkout-total">
            Amount due: <strong>$<?= number_format($order['total'], 2) ?></strong>
        </div>
    </div>

    <form method="POST" action="payment_function.php" class="checkout-form payment-form" id="paymentForm">

        <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">

        <div class="form-group">
            <label>PAYMENT METHOD</label>

            <div class="payment-method-options">

                <label class="payment-method-option">
                    <input
                        type="radio"
                        name="method"
                        value="cash"
                        <?= $method === 'cash' ? 'checked' : '' ?>
                    >
                    Cash
                </label>

                <label class="payment-method-option">
                    <input
                        type="radio"
                        name="method"
                        value="credit_card"
                        <?= $method === 'credit_card' ? 'checked' : '' ?>
                    >
                    Credit Card
                </label>

                <label class="payment-method-option">
                    <input
                        type="radio"
                        name="method"
                        value="mobile_pay"
                        <?= $method === 'mobile_pay' ? 'checked' : '' ?>
                    >
                    Mobile Pay
                </label>

            </div>
        </div>

        <!-- Cash: just an explicit confirmation before we mark it paid -->
        <div class="payment-fields" data-method="cash">
            <label class="checkbox-group-inline">
                <input type="checkbox" name="cash_confirm" value="1">
                I confirm I will pay <strong>$<?= number_format($order['total'], 2) ?> in cash</strong>
                at pickup/delivery.
            </label>
        </div>

        <!-- Credit Card: demo-only 4-digit verification code, never stored -->
        <div class="payment-fields" data-method="credit_card" hidden>
            <div class="form-group">
                <label for="pin">4-DIGIT VERIFICATION CODE</label>
                <input
                    type="password"
                    id="pin"
                    name="pin"
                    inputmode="numeric"
                    autocomplete="off"
                    maxlength="4"
                    pattern="\d{4}"
                    placeholder="e.g. 1234"
                >
                <p class="field-hint">
                    Demo only — this is a school-project verification code,
                    not a real card PIN. It is checked and then discarded;
                    it is never saved.
                </p>
            </div>
        </div>

        <!-- Mobile Pay: pick a provider -->
        <div class="payment-fields" data-method="mobile_pay" hidden>
            <div class="form-group">
                <label>CHOOSE A PROVIDER</label>
                <div class="payment-method-options">
                    <label class="payment-method-option">
                        <input type="radio" name="provider" value="GCash">
                        GCash
                    </label>
                    <label class="payment-method-option">
                        <input type="radio" name="provider" value="MariBank">
                        MariBank
                    </label>
                </div>
            </div>
        </div>

        <button type="submit" class="auth-button shop-button">
            CONFIRM &amp; PAY
        </button>

    </form>

</main>

<script>
(function () {
    'use strict';

    var form = document.getElementById('paymentForm');
    if (!form) return;

    var methodInputs = form.querySelectorAll('input[name="method"]');
    var panels = form.querySelectorAll('.payment-fields');

    function refresh() {
        var selected = form.querySelector('input[name="method"]:checked');
        var method = selected ? selected.value : 'cash';

        panels.forEach(function (panel) {
            var isActive = panel.getAttribute('data-method') === method;
            panel.hidden = !isActive;

            panel.querySelectorAll('input').forEach(function (input) {
                if (input.type === 'checkbox') {
                    input.required = isActive;
                    return;
                }
                input.disabled = !isActive;
                if (input.type === 'password' || input.type === 'radio') {
                    input.required = isActive;
                }
            });
        });
    }

    methodInputs.forEach(function (input) {
        input.addEventListener('change', refresh);
    });

    refresh();
})();
</script>

</body>
</html>
