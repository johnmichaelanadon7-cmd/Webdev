<?php

function logAppError(string $context, Throwable $e): void
{
    error_log('[' . $context . '] ' . $e->getMessage());
}

function redirectWithError(string $location, string $context, Throwable $e, string $userMessage): never
{
    logAppError($context, $e);

    header(
        'Location: ' . $location
        . (str_contains($location, '?') ? '&' : '?')
        . 'status=error&message=' . urlencode($userMessage)
    );

    exit;
}
