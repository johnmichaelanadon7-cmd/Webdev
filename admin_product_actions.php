<?php

session_start();

require 'database/config.php';

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header('Location: login.php');
    exit;
}

$action = $_POST['action'] ?? '';

$pdo = getConnection();

try {

    if ($action === 'create' || $action === 'update') {

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $image       = trim($_POST['image'] ?? '');
        $stock       = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if ($price === false || $price === null || $price < 0) {
            $errors[] = 'A valid price is required.';
        }

        if ($image === '') {
            $errors[] = 'An image filename is required.';
        }

        if ($stock === false || $stock === null || $stock < 0) {
            $errors[] = 'A valid stock amount is required.';
        }

        if (!empty($errors)) {
            header(
                'Location: admin_products.php?status=error&message='
                . urlencode(implode(' ', $errors))
            );
            exit;
        }

        if ($action === 'create') {

            $stmt = $pdo->prepare("
                INSERT INTO `products`
                (name, description, price, image, stock, is_active)
                VALUES
                (:name, :description, :price, :image, :stock, :is_active)
            ");

            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':price', $price);
            $stmt->bindValue(':image', $image);
            $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindValue(':is_active', $isActive, PDO::PARAM_INT);
            $stmt->execute();

            header(
                'Location: admin_products.php?status=success&message='
                . urlencode('Product added.')
            );
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: admin_products.php');
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE `products`
            SET
                name = :name,
                description = :description,
                price = :price,
                image = :image,
                stock = :stock,
                is_active = :is_active
            WHERE id = :id
        ");

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':image', $image);
        $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $isActive, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header(
            'Location: admin_products.php?status=success&message='
            . urlencode('Product updated.')
        );
        exit;
    }

    if ($action === 'delete') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM `products` WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }

        header(
            'Location: admin_products.php?status=success&message='
            . urlencode('Product deleted.')
        );
        exit;
    }

    header('Location: admin_products.php');
    exit;

} catch (PDOException $e) {

    header(
        'Location: admin_products.php?status=error&message='
        . urlencode($e->getMessage())
    );

    exit;
}
