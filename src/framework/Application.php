<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Application
{
    private $container;
    private $router;
    private $routes = [];
    private $notFoundHandler = null;

    public function __construct(Container $container = null)
    {
        $this->container = $container ?? new Container();
        $this->router = new Router();
    }

    public function get(string $pattern, array $handler, $middlewares = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middlewares);
    }

    public function post(string $pattern, array $handler, $middlewares = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middlewares);
    }

    public function map(array $methods, string $pattern, array $handler, $middlewares = []): void
    {
        $this->addRoute($methods, $pattern, $handler, $middlewares);
    }

    public function addRoute($methods, string $pattern, array $handler, $middlewares = []): void
    {
        $handler[] =  is_array($middlewares) ? $middlewares : [$middlewares];

        $this->routes[] = [
            'methods' => (array) $methods,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function setNotFoundHandler(array $handler): void
    {
        $handler[] = [];
        $this->notFoundHandler = $handler;
    }

    public function setMethodNotAllowedHandler(array $handler): void
    {
        $handler[] = [];
        $this->methodNotAllowedHandler = $handler;
    }

    public function run(): void
    {
        $this->container[NotFound::class] = function () {
            return new NotFound;
        };

        $this->container[MethodNotAllowed::class] = function () {
            return new MethodNotAllowed;
        };

        foreach ($this->routes as $route) {
            $this->router->addRoute(
                $route['methods'],
                $route['pattern'],
                $route['handler']
            );
        }

        $request = Request::fromGlobals();

        [$result, $handler, $args] = $this->router->dispatch($request->method(), $request->uri());

        if ($result === Router::NOT_FOUND) {
            $handler = $this->notFoundHandler ?? [NotFound::class, '__invoke', []];
        }

        if ($result === Router::METHOD_NOT_ALLOWED) {
            $handler = $this->methodNotAllowedHandler ?? [MethodNotAllowed::class, '__invoke', []];
        }

        $handler[0] = $this->container[$handler[0]];

        foreach (array_pop($handler) as $middleware) {
            $middleware($request);
        }

        $response = $handler($request, $args);

        if ($response instanceof Response) {
            $response->send();
        } else {
            print $response;
        }
    }
}

final class NotFound
{
    public function __invoke($request, $args)
    {
        return new Response('Page not found', Http::NOT_FOUND);
    }
}

final class MethodNotAllowed
{
    public function __invoke($request, $args)
    {
        return new Response('Method not allowed bro', Http::METHOD_NOT_ALLOWED);
    }
}
