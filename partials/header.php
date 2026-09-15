<?php

require_once __DIR__ . '/../cart_helpers.php';

$page          = $page ?? '';
$minimalHeader = $minimalHeader ?? false;

$homeHref   = ($page === 'index') ? '#home' : 'index.php';
$anchorBase = ($page === 'index') ? '' : 'index.php';

$cartItemCount = cartCount();
?>
<header class="site-header">

    <div class="nav-container">

        <a href="<?= $page === 'index' ? '#home' : 'index.php' ?>" class="brand" aria-label="Harvest Bread Co. home">
            <img src="image/logo.svg" alt="Harvest Bread Co. Logo">
        </a>

        <?php if (!$minimalHeader): ?>

            <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">☰</button>

            <nav class="main-nav" id="mainNav">

                <a href="<?= $homeHref ?>">HOME</a>
                <a href="<?= $anchorBase ?>#about">ABOUT</a>
                <a href="<?= $anchorBase ?>#products">PRODUCTS</a>
                <a href="<?= $anchorBase ?>#location">LOCATION</a>
                <a href="<?= $anchorBase ?>#blog">BLOG</a>

                <a href="cart.php" class="cart-link" aria-label="View cart<?= $cartItemCount > 0 ? " ($cartItemCount items)" : '' ?>">
                    <svg class="cart-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M2.5 3H4.8L5.3 5.2M5.3 5.2H21L18.6 14H8M5.3 5.2L7.2 14M7.2 14L5 16.1C4.3 16.8 4.8 18 5.8 18H18.6M9.2 21C10 21 10.6 20.4 10.6 19.6C10.6 18.8 10 18.2 9.2 18.2C8.4 18.2 7.8 18.8 7.8 19.6C7.8 20.4 8.4 21 9.2 21ZM17.6 21C18.4 21 19 20.4 19 19.6C19 18.8 18.4 18.2 17.6 18.2C16.8 18.2 16.2 18.8 16.2 19.6C16.2 20.4 16.8 21 17.6 21Z"
                              stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="cart-label">Cart</span>
                    <span class="cart-badge<?= $cartItemCount > 0 ? '' : ' is-empty' ?>" id="cartBadge">
                        <?= $cartItemCount > 99 ? '99+' : $cartItemCount ?>
                    </span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <a href="admin.php">ADMIN</a>
                    <?php else: ?>
                        <a href="orders.php">MY ORDERS</a>
                        <a href="payment_history.php">PAYMENTS</a>
                    <?php endif; ?>

                    <a href="logout.php">LOGOUT</a>

                <?php else: ?>

                    <a href="login.php" class="login-nav">LOGIN</a>
                    <a href="signup.php" class="signup-nav">SIGN UP</a>

                <?php endif; ?>

            </nav>

        <?php endif; ?>

    </div>

</header>
