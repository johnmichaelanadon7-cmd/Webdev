# Harvest Bread Co. — PHP Web Application

A full-stack PHP bakery e-commerce site built with PDO/MySQL.

## Setup

1. Import the database schema:
   ```bash
   mysql -u root -p < database/schema.sql
   ```

2. Edit `database/config.php` with your local MySQL credentials (host, db, user, password).

3. Serve from a PHP-enabled web server (XAMPP, Laragon, or `php -S localhost:8000`).

## Features

- Product browsing with live stock tracking
- Session-based shopping cart
- Checkout with transaction-safe stock locking (`FOR UPDATE`)
- Payment flow: cash, credit card, mobile pay (GCash / MariBank)
- User registration, login, logout (bcrypt password hashing)
- Admin dashboard: manage users, products, and orders
- Order status tracking

## GitHub — Commit Message Guide

Write commit messages that describe *what changed and why*:

| Type | Example |
|------|---------|
| `feat:` | `feat: add payment history page for users` |
| `fix:` | `fix: prevent session fixation with session_regenerate_id on login` |
| `style:` | `style: split style.css into per-page stylesheets` |
| `db:` | `db: add payment_transactions table via migration` |
| `refactor:` | `refactor: extract cart logic into cart_helpers.php` |
| `security:` | `security: protect success.php with session token check` |
| `docs:` | `docs: update ReadMe with setup instructions` |

Avoid vague messages like "update files", "fix stuff", or "push".
