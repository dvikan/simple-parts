<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Application
{
    private $container;
    private $routes = [];
    private $middleware = [];
    private $controllerMiddleware = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(string $pattern, $handler, $middleware = [])
    {
        $this->addRoute('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, $handler, $middleware = [])
    {
        $this->addRoute('POST', $pattern, $handler, $middleware);
    }

    public function map(array $methods, string $pattern, $handler, $middleware = [])
    {
        $this->addRoute($methods, $pattern, $handler, $middleware);
    }

    protected function addRoute($methods, string $pattern, $handler, $middleware = []): void
    {
        $this->routes[$pattern] = [
            'methods' => (array) $methods,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => is_array($middleware) ? $middleware : [$middleware],
        ];
    }

    public function addMiddleware(callable $middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function addControllerMiddleware(string $controller, callable $middleware)
    {
        $this->controllerMiddleware[$controller][] = $middleware;
    }

    public function run(): void
    {
        $response = $this->_run();

        if (!$response instanceof Response) {
            $response = new Response($response);
        }

        $response->send();
    }

    public function _run()
    {
        $router = new Router();
        $request = Request::fromGlobals();

        foreach ($this->routes as $route) {
            $router->addRoute($route['methods'], $route['pattern'], $route['handler']);
        }

        $route = $router->dispatch($request->method(), $request->uri());

        foreach ($this->middleware as $mw) {
            $mw($request);
        }

        if ($route[0] === Router::NOT_FOUND) {
            return new Response('Page not found', Http::NOT_FOUND);
        }

        if ($route[0] === Router::METHOD_NOT_ALLOWED) {
            return new Response('Method not allowed', Http::METHOD_NOT_ALLOWED);
        }

        foreach ($this->routes as $_route) {
            if (preg_match('#' . $_route['pattern'] . '#', $request->uri())) {
                foreach ($_route['middleware'] as $mw) {
                    $mw($request);
                }
            }
        }

        $handler    = $route[1];
        $args       = $route[2];

        if ($handler instanceof \Closure) {
            return $handler($request, $args);
        }

        $controllerClass = $handler[0];
        $controllerMethod = $handler[1];
        $controller = $this->container[$controllerClass];

        foreach ($this->controllerMiddleware as $c => $mw) {
            if ($controllerClass === $c) {
                foreach ($mw as $m) {
                    $m($request);
                }
            }
        }

        return $controller->$controllerMethod($request, $args);
    }
}