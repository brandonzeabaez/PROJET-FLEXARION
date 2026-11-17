<?php

declare(strict_types=1);

namespace App;

/**
 * Couche de sécurité : en-têtes HTTP (CSP avec nonce), session durcie,
 * jeton CSRF, anti-flood et assainissement des entrées.
 */
final class Security
{
    private static ?string $nonce = null;

    public static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        if (($_SERVER['SERVER_PORT'] ?? null) === '443') {
            return true;
        }
        if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
            return true;
        }
        if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '')) === 'on') {
            return true;
        }
        return false;
    }

    public static function isLocal(): bool
    {
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
        $hostOnly = explode(':', $host)[0];
        return in_array($hostOnly, ['localhost', '127.0.0.1', '[::1]'], true);
    }

    /**
     * Redirige toute requête HTTP vers HTTPS (301) en production (Alwaysdata, etc.).
     * En local (localhost / 127.0.0.1), laisse fonctionner php -S sans bloquer.
     */
    public static function enforceHttps(): void
    {
        if (PHP_SAPI === 'cli' || self::isHttps() || self::isLocal()) {
            return;
        }

        $host = (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
        if ($host === '') {
            return;
        }

        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        header('Location: https://' . $host . $uri, true, 301);
        exit;
    }

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function getNonce(): string
    {
        if (self::$nonce === null) {
            self::$nonce = base64_encode(random_bytes(16));
        }
        return self::$nonce;
    }

    public static function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        $nonce = self::getNonce();
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self'",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src 'self'",
            "frame-src https://www.youtube-nocookie.com",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "object-src 'none'",
        ];
        if (self::isHttps()) {
            $directives[] = 'upgrade-insecure-requests';
        }
        $csp = implode('; ', $directives);

        header('Content-Security-Policy: ' . $csp);
        if (self::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-origin');
    }

    public static function csrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        self::startSession();
        $expected = $_SESSION['_csrf_token'] ?? '';
        if (!is_string($expected) || $expected === '' || !is_string($token) || $token === '') {
            return false;
        }
        return hash_equals($expected, $token);
    }

    /**
     * Limite la fréquence d'envoi d'un formulaire en session.
     * Retourne true si la requête est autorisée, false si trop rapide.
     */
    public static function checkRateLimit(string $key = 'contact_last_submit', int $cooldownSeconds = 10): bool
    {
        self::startSession();
        $now = time();
        $last = $_SESSION[$key] ?? 0;
        if (is_int($last) && ($now - $last) < $cooldownSeconds) {
            return false;
        }
        return true;
    }

    public static function markRateLimit(string $key = 'contact_last_submit'): void
    {
        self::startSession();
        $_SESSION[$key] = time();
    }

    public static function sanitizeText(string $value, int $maxLength = 2000): string
    {
        // Supprime les octets nuls et caractères de contrôle (sauf \n, \r, \t)
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        $clean = trim(is_string($clean) ? $clean : '');
        if (mb_strlen($clean, 'UTF-8') > $maxLength) {
            $clean = mb_substr($clean, 0, $maxLength, 'UTF-8');
        }
        return $clean;
    }

    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
