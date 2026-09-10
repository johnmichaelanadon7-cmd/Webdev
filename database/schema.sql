-- Harvest Bread Co. — database schema
-- Run this once (e.g. `mysql -u root -p < database/schema.sql`,
-- or paste it into the phpMyAdmin SQL tab) to create the database
-- and table that database/config.php connects to.

CREATE DATABASE IF NOT EXISTS harvest_bread_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE harvest_bread_db;

CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL,
    email      VARCHAR(150) NOT NULL,
    number     VARCHAR(30)  NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- To make an account an admin (so it can reach admin.php):
-- 1. Sign up normally through signup.php first.
-- 2. Then run, replacing the email:
--
-- UPDATE users SET role = 'admin' WHERE email = 'you@example.com';
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)   NOT NULL,
    description VARCHAR(255)   NOT NULL DEFAULT '',
    price       DECIMAL(10,2)  NOT NULL,
    image       VARCHAR(150)   NOT NULL,
    stock       INT UNSIGNED   NOT NULL DEFAULT 0,
    is_active   TINYINT(1)     NOT NULL DEFAULT 1,
    created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED NOT NULL,
    total          DECIMAL(10,2) NOT NULL,
    contact_number VARCHAR(30)   NOT NULL,
    notes          VARCHAR(255)  NOT NULL DEFAULT '',
    status         ENUM('pending', 'preparing', 'ready', 'completed', 'cancelled')
                       NOT NULL DEFAULT 'pending',
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NULL,
    product_name VARCHAR(100) NOT NULL,
    price        DECIMAL(10,2) NOT NULL,
    quantity     INT UNSIGNED NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Starter products, matching the images already in /image
INSERT INTO products (name, description, price, image, stock, is_active) VALUES
    ('Gingerbread cookies',  'Warm spiced cookies, baked fresh daily.',      4.76, 'star1.jpg', 40, 1),
    ('Chocolate / strawberry pastry', 'Flaky pastry layered with chocolate and strawberry.', 8.40, 'star2.jpg', 30, 1),
    ('Choux pastry puffs',   'Light choux puffs with a creamy filling.',     8.40, 'star3.jpg', 30, 1),
    ('Butterscotch pie',     'Rich butterscotch custard in a buttery crust.',8.40, 'star4.jpg', 20, 1)
ON DUPLICATE KEY UPDATE name = name;
