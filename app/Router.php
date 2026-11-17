<?php

declare(strict_types=1);

namespace App;

/**
 * Routeur HTTP minimaliste (GET / POST + gestion 404).
 */
final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /** @var callable|null */
    private $notFoundHandler = null;

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function setNotFound(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $method, string $requestUri): void
    {
        $method = strtoupper($method);
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        $path = parse_url($requestUri, PHP_URL_PATH);
        $normalized = $this->normalize(is_string($path) ? $path : '/');

        if (isset($this->routes[$method][$normalized])) {
            ($this->routes[$method][$normalized])();
            return;
        }

        http_response_code(404);
        if ($this->notFoundHandler !== null) {
            ($this->notFoundHandler)();
            return;
        }

        echo '404 — Page introuvable';
    }

    private function normalize(string $path): string
    {
        $trimmed = '/' . trim($path, '/');
        return $trimmed === '' ? '/' : $trimmed;
    }
}
