<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Application
{
    private $container;
    private $router;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->router = new Router();

        $this->addRoute(['GET'], '/404', [NotFound::class, '__invoke']);
        $this->addRoute(['GET'], '/405', [MethodNotAllowed::class, '__invoke']);
    }

    public function addRoute($methods, string $pattern, $handler, $middleware = []): void
    {
        if (! is_array($middleware)) {
            $middleware = [$middleware];
        }

        $handler[] = $middleware;

        $this->router->addRoute((array) $methods, $pattern, $handler);
    }

    public function run(): void
    {
        $this->container[NotFound::class] = new NotFound();
        $this->container[MethodNotAllowed::class] = MethodNotAllowed::class;

        $request = Request::fromGlobals();

        [$result, $handler, $args] = $this->router->dispatch($request->method(), $request->uri());

        if ($result === Router::NOT_FOUND) {
            [$_, $handler, $_] = $this->router->dispatch('GET', '/404');
        }

        if ($result === Router::METHOD_NOT_ALLOWED) {
            [$_, $handler, $_] = $this->router->dispatch('GET', '/405');
        }

        $handler[0] = $this->container[$handler[0]];

        foreach (array_pop($handler) as $middleware) {
            $request = $middleware($request);
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
        return new Response('Method not allowed', Http::METHOD_NOT_ALLOWED);
    }
}