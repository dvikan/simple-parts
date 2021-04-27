<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Router
{
    private $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * @param string $regex
     * @param string|array $handler Class name and method
     * @param array $middlewares
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
