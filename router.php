<?php

declare(strict_types=1);

/**
 * Script de routage sécurisé pour le serveur PHP intégré :
 *   php -S 127.0.0.1:8765 router.php
 */
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if (is_string($uriPath) && str_starts_with($uriPath, '/assets/')) {
    $assetsRoot = realpath(__DIR__ . '/assets');
    $candidate = realpath(__DIR__ . $uriPath);

    if ($assetsRoot !== false && $candidate !== false && str_starts_with($candidate, $assetsRoot . DIRECTORY_SEPARATOR) && is_file($candidate)) {
        return false;
    }
}

require __DIR__ . '/index.php';
