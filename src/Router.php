<?php declare(strict_types=1);

namespace StaticParts;

class Router
{
    private $routes;

    public function map($regex, callable $handler)
    {
        $this->routes[$regex] = $handler;
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
