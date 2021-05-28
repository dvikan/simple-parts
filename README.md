# Simple Parts

Simpler components for building applications

These components are intentionally simpler than usual.

I suggest that you read each component in its entirety.

### Why?

Mostly as a personal learning exercise. The initial motivation was to create
a framework like Slim, only slimmer.

Other reasons:

* I dislike depending on an endless amount of third-party packages
* I dislike the endless amount of upgrades from third-party packages
* I dislike monitoring third-party packages for vulnerabilities
* I dislike learning existing frameworks
* I dislike general-purpose frameworks
* I am less exposed to a [supply chain attack](https://blog.sonarsource.com/php-supply-chain-attack-on-composer)  
* Smaller and simpler components have less defects and vulnerabilities
* I suffer from the NIH-syndrome (not-invented-here)

All code resides under the `dvikan\SimpleParts` namespace
and in case of failure throws `SimpleException`.

### Requirements

* `php 7.3`
* `ext-json`
* `ext-mbstring`
* `ext-curl`
* `ext-pdo`

### Installation

`composer require dvikan/simple-parts`

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

Create `./test/FooTest.php`:
```php
final class FooTest extends TestCase
{
    public function test()
    {
        $this->assert(true);
        $this->assertSame(1, 1);
    }
}
```

Run tests: `./vendor/bin/test`

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
