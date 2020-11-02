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
* Migrator (Database migrations)
* RssClient
* ErrorHandler
* JsonFile
* Console (todo)
* git wrapper (todo)
* irc client (todo)
* socket wrapper (todo)
* web framework (todo)
* Url (todo)
* Csv (todo)
* Collection (todo)
* ImapClient (todo)
* DataMapper (ORM, todo)

All classes reside under the `dvikan\SimpleParts` namespace.

## Router

The router accepts a regex and a handler. The handler MUST be
an array with a class and a method.

    <?php
    
    $router = new Router;
    
    $router->map('/user/([0-9]+)', [UserController::class, 'profile']);
    
    $routeInfo = $router->match('/user/42');
    
    $handler = $routeInfo[0];
    $args = $routeInfo[1];
    
    print $handler($args);
    
## Template engine

The template engine interprets the template as php code and the `e()`
function escapes for html context.
   
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

    <?php
        
    $request = Request::fromGlobals();

    $request->get('id') // '5'
    $request->post('message') // 'hello world'
   
## Response

    <?php

    $response = new Response('Hello world');
    
    $response->body() // 'Hello world'
    $response->code() // 200
    $response->headers() // array
    $response->json(['message' => 'hello'])

    $response->send();

## HttpClient

    <?php

    $client = new HttpClient;

    $response = $client->get('https://example.com/');
    $response2 = $client->post('https://example.com/', ['name' => 'val']);
    
## Session

    <?php
    
    use function session;
    
    session_start();
    
    session('user', 'root');
    
    print session('user');

## Logger

    <?php
    
    $logger = new Logger('./application.log');

    $logger->log('Something happened');

## Migrator (Database migrations)

Provide a dsn, folder and a cache.

    <?php

    $pdo = new PDO('sqlite:database.sqlite3');

    $migrator = Migrator(
        $pdo,
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

## RssClient

Fetch channel feed from url:

    $channel = Rss::fromUrl('https://classic.wowhead.com/news/rss/classic');

## ErrorHandler

    $errorHandler = new ErrorHandler;
    
    set_error_handler([$errorHandler, 'handleError']);
    set_exception_handler([$errorHandler, 'handleException']);

## JsonFile

Read and write arrays. They are persisted as json.

    $jsonFile = JsonFile::fromFile('./cache.json');
    
    $jsonFile->write([1,2,3]);
    
    $numbers = $jsonFile->read();
    
## Development

Run tests: `composer run test`
