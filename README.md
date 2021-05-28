# Simple Parts

*PHP: The Simple Parts*

Surprisingly simple components!

These components are intentionally smaller and simpler than usual.

We advise you to actually read the code of a component you want to use because
they are so small and simple.

There are some code samples below on basic usage. The code itself is the documentation.

The name is inspired by Crockford's book *Javascript: The Good Parts*.

## Why?

Mostly as a personal learning exercise. The initial motivation was to create
a framework like Slim, only slimmer.

Other reasons:

* We dislike depending on an endless amount of third-party packages
* We dislike the endless amount of upgrades for third-party packages
* We dislike monitoring third-party packages for vulnerabilities
* We dislike learning existing frameworks
* We dislike general-purpose frameworks
* We are less exposed to a [supply chain attack](https://blog.sonarsource.com/php-supply-chain-attack-on-composer)  
* Smaller and simpler components have less defects and vulnerabilities
* We suffer from the NIH-syndrome (not-invented-here)

## File tree

```bash
$ tree src
src
├── cache
│   ├── Cache.php
│   ├── FileCache.php
│   ├── MemoryCache.php
│   ├── NullCache.php
│   └── SqliteCache.php
├── clock
│   ├── Clock.php
│   ├── FrozenClock.php
│   └── SystemClock.php
├── common
│   └── common.php
├── config
│   └── Config.php
├── console
│   └── Console.php
├── container
│   └── Container.php
├── error-handler
│   └── ErrorHandler.php
├── file
│   ├── File.php
│   ├── MemoryFile.php
│   └── TextFile.php
├── framework
│   └── Application.php
├── http
│   ├── CurlHttpClient.php
│   ├── HttpClient.php
│   ├── Http.php
│   ├── Request.php
│   └── Response.php
├── json
│   └── Json.php
├── logger
│   ├── CliHandler.php
│   ├── FileHandler.php
│   ├── Handler.php
│   ├── Logger.php
│   └── SimpleLogger.php
├── migrator
│   └── Migrator.php
├── renderer
│   └── Renderer.php
├── router
│   └── Router.php
├── session
│   └── Session.php
├── shell
│   └── Shell.php
└── test-runner
    ├── AssertionFailure.php
    ├── TestCase.php
    └── TestRunner.php

18 directories, 36 files
```

All code resides under `dvikan\SimpleParts` and in case of failure throws `SimpleException`.

## FileCache

```php
$cache = new FileCache(new TextFile('./cache.json'));

$cache->set('foo', 'bar');
$cache->get('foo');
$cache->delete('foo');
$cache->clear();
```

## Clock

```php
$clock = new SystemClock;

$now = $clock->now();

print $now->format('Y-m-d H:i:s');
```

## Config

```php
$defaultConfig = [
    'env' => 'dev',
];

$customConfig = [
    'env' => 'prod',
];

$config = Config::fromArray($defaultConfig, $customConfig);

print $config['env'];
```

## Console

```php
$console = new Console;

$console->println('hello %s', 'world');

$console->greenln('hello world');

$console->table(['id', 'user'], [
    [1, 'root'],
    [2, 'support'],
]);

$console->exit(1);
```

```
+---------+---------+
| id      | user    |
+---------+---------+
| 1       | root    |
| 2       | support |
+---------+---------+
```

## Container

```php
$container = new Container;

$container['foo'] = function($c) {
    return new Foo($c['bar']);
};

$container['bar'] = function() {
    return new Bar;
};

$foo = $container['foo'];
```

## ErrorHandler

```php
ErrorHandler::create();

print $foo;
```
```
default.ERROR Uncaught Exception ErrorException: Undefined variable: foo at /home/u/repos/simple-parts/test2.php line 13 {
    "stacktrace": [
        "/home/u/repos/simple-parts/test2.php:13",
        "/home/u/repos/simple-parts/test2.php:13"
    ]
}
```

## TextFile

```php
$file = new TextFile('./application.log');

$file->write('hello ');

$file->append('world');

if ($file->exists()) {
    print $file->read();
}
```

## Request

```php
$request = Request::fromGlobals();

print $request->method();
print $request->uri();
print $request->get('foo');
print $request->post('user');
```

## Response

```php
$response = new Response('Hello world', 200, ['Content-Type' => 'text/plain']);

$response->send();
```

## CurlHttpClient

```php
$client = new CurlHttpClient;

try {
    $response = $client->get('https://example.com/');
} catch (SimpleException $e) {
    print "Not 2XX\n";
}
```

## Json

```php
print Json::encode(['foo' => 'bar']);
```

## Logger

```php
$logger = new SimpleLogger('default', [new CliHandler()]);

$logger->info('foo');
$logger->warning('foo');
$logger->error('bar');
```
```
default.INFO foo []
default.WARNING foo []
default.ERROR bar []
```

## Migrator

```php
$pdo = new \PDO('sqlite:application.db');

$migrator = new Migrator($pdo, './migrations');

$result = $migrator->migrate();

if ($result === []) {
    exit;
}

print implode("\n", $result) . "\n";
```

## Renderer

```php
$renderer = new Renderer;

print $renderer->render('./welcome.php', ['user' => 'bob']);
```

```php
<?php use function dvikan\SimpleParts\e; ?>

Hello <?= e($user) ?>
```

## Router

```php
$router = new Router;

$router->addRoute('GET', '/', function() {
    return 'Hello world!';
});

$route = $router->dispatch('GET', '/');

$handler = $route[1];

print $handler();
```

## Session

```php
$session = new Session();

$session->set('user', 'alice');

print $session->get('user');
```

## Shell

```php
$shell = new Shell();

print $shell->execute('echo', ['hello', 'world']);
```

## TestRunner

```php
class FooTest extends TestCase
{
    function test()
    {
        $this->assert(true);
        $this->assertSame(1, 2);
    }
}
```
```
./vendor/bin/test
```

## Todo

Some more ideas.

* git wrapper
* irc client
* socket wrapper
* web framework
* Url
* Uri
* Csv
* Collection
* ImapClient
* DataMapper
* ORM
* Dotenv
* EventDispatcher
* Validator
* Random
* Guid
* Flat file database
* vardumper
* throttling
* captcha
* i18n
* String
* html form, csrf
* browser ua lib
* ipv4 address to location lib
* autoloader
