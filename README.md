# Simple Parts

Intentionally simple components for building applications.

Components:

* Router
* Template engine
* Container
* Request
* Response
* HttpClient
* Session
* Logger
* Database migrations

## Router

The router accepts a regex and a handler. The handler MUST be
an array with a class and a method.

    <?php
    
    $router = new \dvikan\SimpleParts\Router;
    
    $router->map('/user/([0-9]+)', [UserController::class, 'profile']);
    
    $routeInfo = $router->match('/user/42');
    
    $handler = $routeInfo[0];
    $args = $routeInfo[1];
    
    print $handler($args);
    
## Template engine

The template engine interprets the template as php code and the `e()`
function escapes for html context.
   
    <?php
    
    use function \dvikan\SimpleParts\render;
    
    print render('index.tpl', [
        'message' => 'Hello world <3',
    ]);

The template:

    <?php use function \dvikan\SimpleParts\e; ?>
    
    <p>
        Message of the day: <?= e($message) ?>
    </p>
    
## Container

The container stores arrays and callables. Callables are invoked once and then 
shared.
    
    <?php
    
    $container = new \dvikan\SimpleParts\Container;
    
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

    <?php
        
    $request = \dvikan\SimpleParts\Request::fromGlobals();
    
## Response

    <?php
        
    $response = new \dvikan\SimpleParts\Response('Hello world');
    
    $response->send();

## HttpClient

    <?php
        
    $client = new \dvikan\SimpleParts\HttpClient;
    
    $response = $client->get('https://example.com/');
    $response2 = $client->post('https://example.com/', ['name' => 'val']);
    
    print $response->code;
    print_r($response->headers);
    print $response->body;
    
## Session

    <?php
    
    use function \dvikan\SimpleParts\session;
    
    session_start();
    
    session('user', 'root');
    
    print session('user');

## Logger

    <?php
    
    $logger = new \dvikan\SimpleParts\Logger('./application.log');

    $logger->log('Something happened');

## Database migrations

Provide a dsn, folder and a cache.

    <?php

    $migrator = new \dvikan\SimpleParts\Migrator(
        'sqlite:database.sqlite3',
        './migrations',
        './migrations-cache'
    );

    $migrator->run();

Place your migrations as .sql files in the folder:

    $ ls  migrations
    001-init.sql

Place sql inside the migration:

    $ cat migrations/001-init.sql
    create table user (
        id integer primary key,
        name text,
        mobile text,
        email text,
        created_at text
    );

## Development

Run tests: `composer run test`
