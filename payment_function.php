<?php

session_start();

require 'database/config.php';
require 'error_helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$method  = $_POST['method'] ?? '';

$allowedMethods = ['cash', 'credit_card', 'mobile_pay'];

if (!$orderId || !in_array($method, $allowedMethods, true)) {
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

$stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

if ($order['payment_status'] === 'paid') {
    header('Location: order_success.php?id=' . $orderId);
    exit;
}

$redirectBack = static function (string $message) use ($orderId, $method): void {
    header(
        'Location: payment.php?id=' . $orderId
        . '&method=' . urlencode($method)
        . '&status=error&message=' . urlencode($message)
    );
    exit;
};

$provider = null;

switch ($method) {

    case 'cash':
        if (empty($_POST['cash_confirm'])) {
            $redirectBack('Please confirm you will pay in cash to continue.');
        }
        break;

    case 'credit_card':
        $pin = $_POST['pin'] ?? '';

        if (!ctype_digit($pin) || strlen($pin) !== 4) {
            $redirectBack('Enter the 4-digit verification code to continue.');
        }

        unset($pin);
        break;

    case 'mobile_pay':
        $provider = $_POST['provider'] ?? '';

        if (!in_array($provider, ['GCash', 'MariBank'], true)) {
            $redirectBack('Choose GCash or MariBank to continue.');
        }
        break;
}

try {

    $pdo->beginTransaction();

    $updateStmt = $pdo->prepare("
        UPDATE `orders`
        SET payment_method = :method,
            payment_status = 'paid'
        WHERE id = :id AND user_id = :user_id AND payment_status = 'unpaid'
    ");

    $updateStmt->bindValue(':method', $method);
    $updateStmt->bindValue(':id', $orderId, PDO::PARAM_INT);
    $updateStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $updateStmt->execute();

    if ($updateStmt->rowCount() < 1) {
        $pdo->rollBack();
        header('Location: order_success.php?id=' . $orderId);
        exit;
    }

    $txnStmt = $pdo->prepare("
        INSERT INTO `payment_transactions`
        (order_id, user_id, method, provider, amount, status)
        VALUES
        (:order_id, :user_id, :method, :provider, :amount, 'paid')
    ");

    $txnStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
    $txnStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $txnStmt->bindValue(':method', $method);
    $txnStmt->bindValue(':provider', $provider, $provider === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $txnStmt->bindValue(':amount', $order['total']);
    $txnStmt->execute();

    $pdo->commit();

    header('Location: order_success.php?id=' . $orderId . '&paid=1');
    exit;

} catch (PDOException $e) {

    $pdo->rollBack();
    logAppError('Payment', $e);
    $redirectBack('Payment could not be completed. Please try again.');
}
