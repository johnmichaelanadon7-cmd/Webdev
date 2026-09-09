<?php

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL)
        ? null
        : "Enter a valid email address.";
}

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === ''
        ? "$label is required."
        : null;
}

function validatePassword(string $value): ?string
{
    if (trim($value) === '') {
        return "Password is required.";
    }

    if (strlen($value) < 8) {
        return "Password must be at least 8 characters.";
    }

    return null;
}

function validatePasswordConfirmation(
    string $password,
    string $confirmation
): ?string {
    return $password !== $confirmation
        ? "Passwords do not match."
        : null;
}

function validateSignupInput(array $post): array
{
    $username = trim($post['username'] ?? '');
    $email = trim($post['email'] ?? '');
    $number = trim($post['number'] ?? '');
    $password = $post['password'] ?? '';
    $confirmation = $post['confirmation_password'] ?? '';

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateEmailFormat($email),
        validateRequired($number, 'Number'),
        validatePassword($password),
        validatePasswordConfirmation($password, $confirmation),
    ]);

    $errors = array_values($errors);

    if (empty($errors)) {
        $username = htmlspecialchars($username);
        $email = htmlspecialchars($email);
        $number = htmlspecialchars($number);
    }

    return [
        'errors' => $errors,
        'data' => [
            'username' => $username,
            'email' => $email,
            'number' => $number,
            'password' => $password,
        ],
    ];
}