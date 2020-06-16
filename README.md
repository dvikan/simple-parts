# StaticParts - simple components for building web apps

Features:

* Router
* Template engine
* Container
* Request
* Response
* HttpClient
* Session
* Logger

## Router

The router accepts a regex and a handler.

    <?php require 'vendor/autoload.php';
    
    $router = new StaticParts\Router;
    
    $router->map('/user/([0-9]+)', function ($args) {
        return 'User id is: ' . $args[0];
    });
    
    $routeInfo = $router->match('/user/42');
    
    $handler = $routeInfo[0];
    $args = $routeInfo[1];
    
    print $handler($args);
    
## Template engine

The template engine interprets the template as php code and the `e()`
function escapes for html context.
   
    <?php require 'vendor/autoload.php';
    
    use function StaticParts\render;
    
    print render('index.tpl', [
        'message' => 'Hello world <3',
    ]);

The template:

    <?php use function StaticParts\e; ?>
    
    <p>
        Message of the day: <?= e($message) ?>
    </p>
    
## Container

The container stores arrays and callables. Callables are invoked once and then 
shared.
    
    <?php require 'vendor/autoload.php';
    
    use StaticParts\Container;
    
    $container = new Container;
    
    $container['options'] = [
        'title' => 'weeee',
    ];
    
    $container['random_number'] = function () {
        return rand(1, 1000);
    };
    
    print $container['random_number'] . PHP_EOL;
    print $container['random_number'] . PHP_EOL;
    
    print_r($container['options']);
    
    691
    691
    Array
    (
        [title] => weeee
    )

## Request

    <?php require 'vendor/autoload.php';
    
    use StaticParts\Request;
    
    $request = Request::fromGlobals();
    
## Response

    <?php require 'vendor/autoload.php';
    
    use StaticParts\Response;
    
    $response = new Response('Hello world');
    
    $response->send();

## HttpClient

    <?php require 'vendor/autoload.php';
    
    use StaticParts\HttpClient;
    
    $client = new HttpClient;
    
    $response = $client->get('https://example.com/');
    $response2 = $client->post('https://example.com/', ['name' => 'val']);
    
    print $response->code;
    print_r($response->headers);
    print $response->body;
    
## Session

    <?php require 'vendor/autoload.php';
    
    use function StaticParts\session;
    
    session_start();
    
    session('user', 'root');
    
    print session('user');

## Logger

    <?php require 'vendor/autoload.php';

    use StaticParts\Logger;

    $logger = new Logger('./application.log');

    $logger->log('Something happened');
