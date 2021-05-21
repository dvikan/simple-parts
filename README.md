# Simple Parts

*PHP: The Simple Parts*

Surprisingly simple components!

These components are intentionally smaller and simpler than usual.

We advise you to actually read the code of a component you want to use because
they are so small and simple.

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

## Overview

* `Cache`
* `Clock`
* `Config`
* `Console`
* `Container`
* `ErrorHandler`
* `File`
* `Application` (web framework)  
* `HttpClient`
* `Json`
* `Logger`
* `Migrator` (database migrations)
* `Renderer` (template engine)
* `Router`  
* `Session`
* `Shell`
* `TestRunner` (unit testing)

All code resides under `dvikan\SimpleParts` and in case of failure throws `SimpleException`.

## Cache

```php
$cache = new FileCache(new TextFile('./cache.json'));

$cache->get('foo'); // NULL
$cache->get('foo', 'default'); // 'default'

$cache->set('foo');
$cache->set('foo', 'bar');
$cache->set('foo', 'bar', 60);

$cache->delete('foo');

$cache->clear();
```

The `FileCache` is a persistent key-value store.

Cache items have a ttl (time-to-live) specified in seconds from now. By default they never expire.

The underlying storage is a file. The cache is serialized as json.

The cache is written to file when it is garbage-collected by php.

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

print $config['env']; // prod
```

Config is an immutable wrapper around an array.

Keys in the custom config MUST have default values in the default config.

Trying to grab a non-existing key results in an exception.

The config values can be accessed with array syntax.

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

The console allows for some convenient printing of text, newlines and tables.

Also useful is colored text and exit code.

All methods support printf-style formatting.

## Container

```php
$container = new Container;

$container['http'] = function ($container) {
    return new HttpClient;
};

$http = $container['http'];
```

The container is a standard dependency container.

Values that are not closures are stored as is.

The stacktrace is passed to the logger in the context.

## ErrorHandler

```php
$logger = new Logger('default', [new CliHandler]);

ErrorHandler::create($logger);

print $foo;
```
```
[2021-05-10 02:18:56] default.INFO E_NOTICE: Undefined variable: foo at /home/u/simple-parts/test.php line 11 {
    "stacktrace": [
        "/home/u/simple-parts/test.php:11"
    ]
}
```

The error handler registers itself as php's error handler, exception handler and shutdown function.

A logger MUST be provided.

All php errors are converted to exceptions.

## TextFile

```php
$file = new TextFile('./application.log');

$file->write('hello ');

$file->append('world');

if ($file->exists()) {
    print $file->read();
}
```

TextFile is a standard file abstraction.

## Request

```php
$request = Request::fromGlobals();

print $request->method(); // 'GET'
print $request->uri(); // '/'
print $request->get('foo'); // NULL
print $request->get('id', '42'); // '42'
print $request->post('user', 'anon'); // 'anon'
```

Request is an abstraction over an http request.

## Response

```php
$response = new Response('Hello world', 200, ['Content-Type' => 'text/plain']);

$response->send();
```

Response is an abstraction over an http response.

## HttpClient

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
print Json::encode(['message' => 'hello']);
```

Json is wrapper that throws exception if the data fails to encode/decode as json.

## Logger

```php
$logger = new SimpleLogger('default', [new CliHandler()]);

$logger->info('hello');
$logger->warning('hello');
$logger->error('hello');
```
```
[2021-05-10 03:23:53] default.INFO hello []
[2021-05-10 03:23:53] default.WARNING hello []
[2021-05-10 03:23:53] default.ERROR hello []
```

The logger requires a name and an array of handlers.
It has three log levels: `INFO`, `WARNING` and `ERROR`.

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

The migrator is for database migrations.

Migrate migrations in `./migrations`:

## Renderer

```php
$renderer = new Renderer;

print $renderer->render('./welcome.php', ['user' => 'bob']);
```

```php
<?php namespace dvikan\SimpleParts; ?>

Hello <?= e($user) ?>
```

Renderer is a template engine. `e` is a function that escapes for html context.

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

The router maps http requests to handlers.

## Session

```php
$session = new Session();

$session->set('user', 'alice');

print $session->get('user');
```

Session is an abstraction over `$_SESSION`.

## Shell

```php
$shell = new Shell();

print $shell->execute('echo', ['hello', 'world']);
```

Shell is an abstraction over `exec()`.

The arguments are escaped but you still need to make sure they are not parsed as command options.

You can sometimes use `--`, otherwise validate the arguments manually.
```php
$shell = new Shell();

print $shell->execute('git clone --', [$userInput]);
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

The test runner is a tool for creating unit tests. Tests must inherit from `TestCase`.

Tests are assumed to be located in `./test`.

## Development

Install `composer create-project dvikan/simple-parts`

Run tests: `./bin/test`

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
