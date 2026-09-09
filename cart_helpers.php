<?php

/*
 * Session cart helpers.
 * The cart is stored as $_SESSION['cart'] = [ product_id => quantity ].
 * Every file that uses these must call session_start() itself first.
 */

function cartGet(): array
{
    return $_SESSION['cart'] ?? [];
}

function cartSave(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

function cartCount(): int
{
    return array_sum(cartGet());
}

function cartAdd(int $productId, int $quantity, int $maxStock): void
{
    $cart = cartGet();

    $newQuantity = ($cart[$productId] ?? 0) + $quantity;
    $newQuantity = max(0, min($newQuantity, $maxStock));

    if ($newQuantity < 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $newQuantity;
    }

    cartSave($cart);
}

function cartSetQuantity(int $productId, int $quantity, int $maxStock): void
{
    $cart = cartGet();

    $quantity = max(0, min($quantity, $maxStock));

    if ($quantity < 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $quantity;
    }

    cartSave($cart);
}

function cartRemove(int $productId): void
{
    $cart = cartGet();
    unset($cart[$productId]);
    cartSave($cart);
}

function cartClear(): void
{
    unset($_SESSION['cart']);
}
