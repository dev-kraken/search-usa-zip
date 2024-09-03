<?php

declare(strict_types=1);

namespace DevKraken\Router;

use InvalidArgumentException;

class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    public function addRoute(string $path, callable $handler): void
    {
        $this->routes[$path] = $handler;
    }

    public function routeRequest(string $path): array
    {
        try {
            $parsedUrl = $this->parseUrl($path);
            $routePath = $parsedUrl['path'];
            $queryParams = $this->extractQueryParams($parsedUrl);

            return $this->executeHandler($routePath, $queryParams);
        } catch (InvalidArgumentException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function parseUrl(string $path): array
    {
        $parsedUrl = parse_url($path);
        if ($parsedUrl === false) {
            throw new InvalidArgumentException('Invalid URL provided');
        }

        $parsedUrl['path'] ??= '/';
        return $parsedUrl;
    }

    private function extractQueryParams(array $parsedUrl): array
    {
        if (!isset($parsedUrl['query'])) {
            throw new InvalidArgumentException('Query string is required. Please provide a search parameter (e.g., /?search=10001)');
        }

        if (is_array($parsedUrl['query'])) {
            throw new InvalidArgumentException('Invalid query string format');
        }

        parse_str($parsedUrl['query'], $queryParams);

        if (!isset($queryParams['search']) || $queryParams['search'] === '') {
            throw new InvalidArgumentException('Search parameter is required and cannot be empty');
        }

        return $queryParams;
    }


    private function executeHandler(string $routePath, array $queryParams): array
    {
        if (isset($queryParams['error'])) {
            return $queryParams; // This is the error from extractQueryParams
        }

        $handler = $this->routes[$routePath] ?? null;

        if ($handler === null) {
            return ['error' => '404 Not Found'];
        }
        return $handler($queryParams);
    }
}