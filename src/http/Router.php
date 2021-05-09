<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Router
{
    private $routes = [];

    public function get(string $pattern, $handler)
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, $handler)
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    public function addRoute(string $method, string $pattern, $handler)
    {
        $this->routes[] = [
            'method'        => $method,
            'pattern'       => $pattern,
            'handler'       => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): array
    {
        foreach ($this->routes as $route) {
            if (
                $method === $route['method']
                && preg_match('#^' . $route['pattern'] . '$#', $uri, $vars) === 1
            ) {
                array_shift($vars);

                return [$route['handler'], $vars];
            }
        }

        return [];
    }
}
