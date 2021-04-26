<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use Closure;

final class Router
{
    private $routes;

    // todo: expand with method too
    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * @param string $regex
     * @param string|array $handler Class name and method
     */
    public function get(string $regex, $handler, array $middlewares = [])
    {
        $this->routes[] = [
            'method'        => 'get',
            'regex'         => $regex,
            'handler'       => $handler,
            'middlewares'   => $middlewares,
        ];
    }

    public function match(Request $request): array
    {
        foreach ($this->routes as $route) {
            if (preg_match('#^'. $route['regex'] . '$#', $request->uri(), $matches) === 1) {
                array_shift($matches);

                $route['args'] = $matches;

                return $route;
            }
        }

        return [];
    }
}
