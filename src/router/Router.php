<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Router
{
    public const FOUND              = 10;
    public const METHOD_NOT_ALLOWED = 20;
    public const NOT_FOUND          = 30;

    private $routes = [];

    public function get(string $pattern, $handler)
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, $handler)
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    public function map(array $methods, string $pattern, $handler)
    {
        $this->addRoute($methods, $pattern, $handler);
    }

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
                return [self::METHOD_NOT_ALLOWED];
            }

            array_shift($matches);

            return [self::FOUND, $route['handler'], $matches];
        }

        return [self::NOT_FOUND];
    }
}
