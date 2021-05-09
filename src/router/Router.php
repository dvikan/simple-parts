<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Router
{
    public const FOUND              = 'FOUND';
    public const METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';
    public const NOT_FOUND          = 'NOT_FOUND';

    private const METHODS = ['GET', 'POST', 'DELETE'];

    private $routes = [];

    public function get(string $pattern, $handler)
    {
        $this->addRoute(['GET'], $pattern, $handler);
    }

    public function post(string $pattern, $handler)
    {
        $this->addRoute(['POST'], $pattern, $handler);
    }

    public function map(array $methods, $pattern, $handler)
    {
        $this->addRoute($methods, $pattern, $handler);
    }

    private function addRoute(array $methods, string $pattern, $handler): void
    {
        foreach ($methods as $method) {
            if (! in_array($method, self::METHODS)) {
                throw new SimpleException(sprintf('Illegal route method: "%s"', $method));
            }
        }

        if (isset($this->routes[$pattern])) {
            throw new SimpleException(sprintf('Refusing to overwrite existing route: "%s"', $pattern));
        }

        $this->routes[$pattern] = [
            'methods' => $methods,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): array
    {
        if (! in_array($method, self::METHODS)) {
            throw new SimpleException(sprintf('Illegal route method: "%s"', $method));
        }

        foreach ($this->routes as $route) {
            $result = preg_match('#^' . $route['pattern'] . '$#', $uri, $matches);

            if ($result === false) {
                throw new SimpleException('Regex error in route pattern');
            }

            if ($result === 0) {
                continue;
            }

            if (! in_array($method, $route['methods'])) {
                return [self::METHOD_NOT_ALLOWED];
            }

            // Drop the first full match
            array_shift($matches);

            return [self::FOUND, $route['handler'], $matches];
        }

        return [self::NOT_FOUND];
    }
}
