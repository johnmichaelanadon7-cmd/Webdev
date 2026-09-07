<?php
// Harvest Bread Co. — PHP-ready single-page replica
$products = [
    ["name" => "Gingerbread cookies", "price" => "$4.76 USD", "reviews" => "324 reviews"],
    ["name" => "Chocolate / strawberry", "price" => "$8.40 USD", "reviews" => "124 reviews"],
    ["name" => "Choux pastry puffs", "price" => "$8.40 USD", "reviews" => "212 reviews"],
    ["name" => "Butterscotch pie", "price" => "$8.40 USD", "reviews" => "126 reviews"],
];

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harvest Bread Co. | Dumaguete</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="nav-container">
        <a href="#home" class="brand" aria-label="Harvest Bread Co. home">
          <img src="image/logo.svg" alt="Harvest Bread Co. Logo">
        </a>

        <button class="menu-toggle" aria-label="Open menu" aria-expanded="false">☰</button>

        <nav class="main-nav" id="mainNav">
            <a href="#home">HOME</a>
            <a href="#about">ABOUT</a>
            <a href="#products">PRODUCTS</a>
            <a href="#location">LOCATION</a>
            <a href="#blog">BLOG</a>
        </nav>
    </div>
</header>

<main>
<section class="hero" id="home">
    <img src="image/hero.png" alt="hero" class="real-image">

<div class="hero-overlay">
    <h1>NOW OPEN</h1>
    <p>DUMAGUETE CITY ROBINSON</p>
</div>
</section>

<section class="featured section-white" id="products">
    <div class="section-intro">
        <h2>Featured Products</h2>
        <p>
            We believe in doing things the way they should be done, with principles
            that run deep, from our bakery to every table we reach in Ireland.
        </p>
    </div>

    <h3 class="display-title">CHECK OUR<br>BEST MENU</h3>

    <div class="featured-grid">
        <?php foreach ($featured as $item): ?>
            <article class="featured-card">

                <img 
                    src="image/<?= htmlspecialchars($item['image']) ?>" 
                    alt="<?= htmlspecialchars($item['name']) ?>"
                    class="product-placeholder"
                >

                <button class="pill-button">VIEW ITEM</button>

            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="values" id="about">
    <div class="values-grid">

        <?php foreach ($features as $i => $feature): ?>
        <article class="value-card">
            <img 
                src="image/mage<?= $i + 1 ?>.jpg"
                alt="<?= htmlspecialchars($feature["title"]) ?>"
                class="value-placeholder"
            >
            <h3><?= htmlspecialchars($feature["title"]) ?></h3>
            <p><?= htmlspecialchars($feature["text"]) ?></p>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="service-strip">
    <div class="service-title">FRESH, FAST &amp; ALWAYS<br>HERE FOR YOU</div>
    <div>Crafted with love &amp;<br>quality products</div>
    <div>Free shipping on all<br>orders above $50</div>
    <div>Freshly baked, every<br>single day</div>
</section>

<section class="product-showcase">
    <div class="product-grid">
        <?php foreach ($products as $i => $product): ?>

        <article class="product-card">

            <img 
                src="image/star<?= $i + 1 ?>.jpg"
                alt="<?= htmlspecialchars($product["name"]) ?>"
                class="shop-placeholder"
            >

            <h3><?= htmlspecialchars($product["name"]) ?></h3>

            <div class="stars">★★★★★</div>

            <p><?= htmlspecialchars($product["reviews"]) ?></p>

            <strong><?= htmlspecialchars($product["price"]) ?></strong>

            <button class="order-button">ORDER NOW</button>

        </article>

        <?php endforeach; ?>
    </div>
</section>

<section class="story-grid" id="blog">
    <!-- LEFT SIDE -->
    <article class="story-text light-panel">
        <div class="breadbag"><img 
            src="image/mockup1.svg"
            alt="Harvest Bread Co."
            class="mini-logo"
        >
        </div>
        <p>
            Bread is at the heart of what we do, but it’s not the only thing. 
            From our well-known bagels to pastries and buttery croissants, the 
            same commitment to craft runs through everything we bake. Each product 
            is carefully prepared using quality ingredients and thoughtful techniques. 
            We take pride in creating baked goods that are fresh, delicious, and made 
            with care. Whether you are enjoying a quick breakfast or sharing a treat with 
            family and friends, we want every bite to feel special. Our goal is to bring 
            people together through the simple joy of freshly baked food. Everything we 
            bake is made with passion, care, and love for good food.
        </p>
    </article>


    <!-- CENTER IMAGE -->
    <div class="story-photo">
        <img
            src="image/mockup2.jpg"
            alt="Bakery"
            class="story-photo-image"
        >
    </div>
    <!-- RIGHT SIDE -->
    <article class="story-text orange-panel">
        <p>
            From matches to race days, gatherings to celebrations, our bread shows 
            up where people come together. Large format, sliced and ready to serve, 
            but made with slow-fermented dough just like our original. Every loaf is 
            carefully prepared to bring fresh flavor and quality to every occasion. 
            We believe good bread can make simple moments feel more special. Whether 
            shared with family, friends, or loved ones, our bread is made to be enjoyed 
            together. From the first bite to the last slice, we make sure every loaf is 
            baked with care and passion. It is more than just bread—it is made to be part 
            of the moments that bring people together.
        </p>
    </article>
</section>


<section class="newsletter" id="location">
    <div>
        <h2>Sunshine in your inbox</h2>
        <p>Get 25% off your starter kit when you sign up</p>
    </div>
    <form id="newsletterForm">
        <label for="email">Enter your email</label>
        <div class="email-row">
            <input id="email" type="email" placeholder="Enter your email" required>
            <button type="submit">SIGN UP</button>
        </div>
        <small>You can unsubscribe by using the unsubscribe link.<br>Our Privacy Policy applies and sets out your rights.</small>
    </form>
</section>
</main>

<footer class="footer">
    <div class="footer-col">
        <h3>Contact us</h3>
        <p>info@harvestbreadco</p>
        <p>+3210-777-3411</p>
        <p>Robinson, Dumaguete City</p>
        <a href="#location">Get directions</a>
    </div>

    <div class="footer-col">
        <h3>Useful link</h3>
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#products">Products</a>
        <a href="#location">Contact</a>
        <a href="#blog">Blog</a>
    </div>

    <div class="footer-col">
        <h3>Our utilities</h3>
        <a href="#">Style guide</a>
        <a href="#">Change log</a>
        <p>Facebook · Twitter · Instagram</p>
    </div>

    <div class="footer-brand">
        <img src="image/logos.svg" alt="Harvest Bread Co. Logo" class="footer-logo">
        <p>2026 HarvestBreadCo<br>made to introduce the breads</p>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>