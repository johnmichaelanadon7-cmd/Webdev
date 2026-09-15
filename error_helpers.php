<?php

/*
 * error_helpers.php
 *
 * Centralizes what happens when a caught exception needs to reach the
 * user: the real message is written to the server log (never the
 * browser), and every caller shows the same friendly, generic text.
 * Keeping this in one place means a redirect+exit sequence used in a
 * dozen files no longer has to be retyped (or forgotten) in each one.
 */

function logAppError(string $context, Throwable $e): void
{
    error_log('[' . $context . '] ' . $e->getMessage());
}

/**
 * Log a caught error and immediately redirect back with a safe,
 * user-friendly message. Always terminates the script (exit).
 */
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
