<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Router
{
    private $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function match(string $uri): array
    {
        foreach ($this->routes as $regex => $handler) {
            if (preg_match('#^'.$regex.'$#', $uri, $matches) === 1) {
                array_shift($matches);

                return [$handler, $matches];
            }
        }

        return [];
    }
}
