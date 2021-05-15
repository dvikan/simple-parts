<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Router
{
    public const FOUND              = 'FOUND';
    public const METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';
    public const NOT_FOUND          = 'NOT_FOUND';

    private $routes = [];

    /**
     * @param string|array $methods
     */
    public function addRoute($methods, string $pattern, $handler): void
    {
        $this->routes[$pattern] = [
            'methods' => (array) $methods,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): array
    {
        foreach ($this->routes as $route) {
            if (!preg_match('#^' . $route['pattern'] . '$#', $uri, $matches)) {
                continue;
            }

            if (! in_array($method, $route['methods'])) {
                return [self::METHOD_NOT_ALLOWED, null, []];
            }

            array_shift($matches);

            return [self::FOUND, $route['handler'], $matches];
        }

        return [self::NOT_FOUND, null, []];
    }
}
