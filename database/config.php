<?php

function getConnection(): PDO
{
    $host = 'localhost';
    $db   = 'harvest_bread_db';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8mb4",
            $user,
            $pass
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (PDOException $e) {
        /* Log the real error server-side; never expose DB details to the browser */
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(503);
        die("The site is temporarily unavailable. Please try again later.");
    }
}