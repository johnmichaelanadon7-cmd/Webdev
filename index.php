<?php
session_start();

require 'database/config.php';
require 'cart_helpers.php';

// Harvest Bread Co. — PHP-ready single-page replica

$pdo = getConnection();

$products = $pdo->query("
    SELECT id, name, price, image, stock
    FROM `products`
    WHERE is_active = 1
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

$featured = [
    [
        'name' => 'Picture 1',
        'image' => 'picture1.jpg'
    ],
    [
        'name' => 'Picture 2',
        'image' => 'picture2.jpg'
    ],
    [
        'name' => 'Picture 3',
        'image' => 'picture3.jpg'
    ],
    [
        'name' => 'Picture 4',
        'image' => 'picture4.jpg'
    ],
    [
        'name' => 'Picture 5',
        'image' => 'picture5.jpg'
    ],
    [
        'name' => 'Picture 6',
        'image' => 'picture6.jpg'
    ],
];

$features = [
    [
        "title" => "Choicest Natural Ingredients",
        "text" => "Sourcing globally to handpick and select premium natural ingredients such as Japanese-milled flour and pure butter from New Zealand. The ingredients are especially prepared from its raw state to create pastes or fillings within our Central Kitchen, ensuring greater quality control of the finished items."
    ],
    [
        "title" => "Craftsmanship",
        "text" => "Focusing on creating perfect harmony among textures, flavors and ingredients, the recipes are inspired from each Master Chef’s personal take on classic simplicity."
    ],
    [
        "title" => "Wholesome goodness",
        "text" => "Every bite’s a joy as we continue to create products that bring an abundance of joy and goodness in order to satisfy the evolving needs of consumers."
    ]
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

    <title>Harvest Bread Co. | Dumaguete</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="site-header">

    <div class="nav-container">

        <a
            href="#home"
            class="brand"
            aria-label="Harvest Bread Co. home"
        >
            <img
                src="image/logo.svg"
                alt="Harvest Bread Co. Logo"
            >
        </a>

        <button
            class="menu-toggle"
            type="button"
            aria-label="Open menu"
            aria-expanded="false"
        >
            ☰
        </button>

        <nav class="main-nav" id="mainNav">

            <a href="#home">HOME</a>
            <a href="#about">ABOUT</a>
            <a href="#products">PRODUCTS</a>
            <a href="#location">LOCATION</a>
            <a href="#blog">BLOG</a>
            <a href="cart.php">CART<?= cartCount() > 0 ? ' (' . cartCount() . ')' : '' ?></a>

            <?php if (isset($_SESSION['user_id'])): ?>

                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="admin.php">ADMIN</a>
                <?php else: ?>
                    <a href="orders.php">MY ORDERS</a>
                <?php endif; ?>

                <a href="logout.php">LOGOUT</a>

            <?php else: ?>

                <a href="login.php">LOGIN</a>
                <a href="signup.php" class="signup-nav">SIGN UP</a>

            <?php endif; ?>

        </nav>

    </div>

</header>

<main>

    <?php if ($status === 'error' || $status === 'success'): ?>
        <div class="auth-message <?= $status ?> site-message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- HERO -->
    <section class="hero" id="home">

        <img
            src="image/hero.png"
            alt="Harvest Bread Co. bakery"
            class="real-image"
        >

        <div class="hero-overlay">

            <h1>NOW OPEN</h1>

            <p>DUMAGUETE CITY ROBINSON</p>

        </div>

    </section>


    <!-- FEATURED PRODUCTS -->
    <section
        class="featured section-white"
        id="products"
    >

        <div class="section-intro">

            <h2>Featured Products</h2>

            <p>
                We believe in doing things the way they should be done,
                with principles that run deep, from our bakery to every
                table we reach in Ireland.
            </p>

        </div>

        <h3 class="display-title">
            CHECK OUR<br>
            BEST MENU
        </h3>

        <div class="featured-grid">

            <?php foreach ($featured as $item): ?>

                <article class="featured-card">

                    <img
                        src="image/<?= htmlspecialchars($item['image']) ?>"
                        alt="<?= htmlspecialchars($item['name']) ?>"
                        class="product-placeholder"
                    >

                    <button
                        type="button"
                        class="pill-button"
                    >
                        VIEW ITEM
                    </button>

                </article>

            <?php endforeach; ?>

        </div>

    </section>


    <!-- VALUES / ABOUT -->
    <section
        class="values"
        id="about"
    >

        <div class="values-grid">

            <?php foreach ($features as $i => $feature): ?>

                <article class="value-card">

                    <img
                        src="image/mage<?= $i + 1 ?>.jpg"
                        alt="<?= htmlspecialchars($feature["title"]) ?>"
                        class="value-placeholder"
                    >

                    <h3>
                        <?= htmlspecialchars($feature["title"]) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($feature["text"]) ?>
                    </p>

                </article>

            <?php endforeach; ?>

        </div>

    </section>


    <!-- SERVICE STRIP -->
    <section class="service-strip">

        <div class="service-title">
            FRESH, FAST &amp; ALWAYS<br>
            HERE FOR YOU
        </div>

        <div>
            Crafted with love &amp;<br>
            quality products
        </div>

        <div>
            Free shipping on all<br>
            orders above $50
        </div>

        <div>
            Freshly baked, every<br>
            single day
        </div>

    </section>


    <!-- PRODUCTS -->
    <section class="product-showcase">

        <div class="product-grid">

            <?php foreach ($products as $product): ?>

                <article class="product-card">

                    <img
                        src="image/<?= htmlspecialchars($product["image"]) ?>"
                        alt="<?= htmlspecialchars($product["name"]) ?>"
                        class="shop-placeholder"
                    >

                    <h3>
                        <?= htmlspecialchars($product["name"]) ?>
                    </h3>

                    <div class="stars">
                        ★★★★★
                    </div>

                    <p>
                        <?= (int) $product["stock"] > 0
                            ? (int) $product["stock"] . ' in stock'
                            : 'Sold out' ?>
                    </p>

                    <strong>
                        $<?= number_format((float) $product["price"], 2) ?> USD
                    </strong>

                    <?php if ((int) $product['stock'] > 0): ?>

                        <form method="POST" action="cart_add.php" class="order-form">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input
                                type="number"
                                name="quantity"
                                value="0"
                                min="1"
                                max="<?= (int) $product['stock'] ?>"
                                class="order-qty"
                            >
                            <button type="submit" class="order-button">
                                ORDER NOW
                            </button>
                        </form>

                    <?php else: ?>

                        <button type="button" class="order-button" disabled>
                            SOLD OUT
                        </button>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

            <?php if (empty($products)): ?>
                <p class="admin-empty">No products available right now.</p>
            <?php endif; ?>

        </div>

    </section>


    <!-- STORY / BLOG -->
    <section
        class="story-grid"
        id="blog"
    >

        <article class="story-text light-panel">

            <div class="breadbag">

                <img
                    src="image/mockup1.svg"
                    alt="Harvest Bread Co."
                    class="mini-logo"
                >

            </div>

            <p>
                Bread is at the heart of what we do, but it’s not the only thing.
                From our well-known bagels to pastries and buttery croissants,
                the same commitment to craft runs through everything we bake.
                Each product is carefully prepared using quality ingredients and
                thoughtful techniques. We take pride in creating baked goods
                that are fresh, delicious, and made with care. Whether you are
                enjoying a quick breakfast or sharing a treat with family and
                friends, we want every bite to feel special. Our goal is to
                bring people together through the simple joy of freshly baked
                food. Everything we bake is made with passion, care, and love
                for good food.
            </p>

        </article>


        <div class="story-photo">

            <img
                src="image/mockup2.jpg"
                alt="Bakery"
                class="story-photo-image"
            >

        </div>


        <article class="story-text orange-panel">

            <p>
                From matches to race days, gatherings to celebrations, our
                bread shows up where people come together. Large format,
                sliced and ready to serve, but made with slow-fermented dough
                just like our original. Every loaf is carefully prepared to
                bring fresh flavor and quality to every occasion. We believe
                good bread can make simple moments feel more special. Whether
                shared with family, friends, or loved ones, our bread is made
                to be enjoyed together. From the first bite to the last slice,
                we make sure every loaf is baked with care and passion. It is
                more than just bread—it is made to be part of the moments that
                bring people together.
            </p>

        </article>

    </section>


    <!-- LOCATION -->
    <section
        class="location-section"
        id="location"
    >

        <div class="location-content">

            <h2>VISIT US</h2>

            <p>
                Come and enjoy freshly baked bread and pastries
                at Harvest Bread Co.
            </p>

            <h3>Robinsons Dumaguete City</h3>

            <p>
                Dumaguete City, Negros Oriental, Philippines
            </p>

            <a
                href="https://www.google.com/maps"
                target="_blank"
                rel="noopener"
                class="location-button"
            >
                GET DIRECTIONS
            </a>

        </div>

    </section>


</main>


<!-- FOOTER -->
<footer class="footer">

    <div class="footer-col">

        <h3>Contact us</h3>

        <p>info@harvestbreadco</p>

        <p>+3210-777-3411</p>

        <p>Robinson, Dumaguete City</p>

        <a href="#location">
            Get directions
        </a>

    </div>


    <div class="footer-col">

        <h3>Useful link</h3>
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#products">Products</a>
        <a href="#location">Location</a>
        <a href="#blog">Blog</a>

    </div>


    <div class="footer-col">

        <h3>Our utilities</h3>

        <a href="#">Style guide</a>

        <a href="#">Change log</a>

        <p>
            Facebook · Twitter · Instagram
        </p>

    </div>


    <div class="footer-brand">

        <img
            src="image/logos.svg"
            alt="Harvest Bread Co. Logo"
            class="footer-logo"
        >

        <p>
            2026 HarvestBreadCo<br>
            made to introduce the breads
        </p>

    </div>

</footer>

<script src="script.js"></script>

</body>
</html>