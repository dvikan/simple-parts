# Simple Parts

Surprisingly simple components!

These components are intentionally small and simple.

We advise you to actually read the code of a component you want to use because
they are short and sometimes fit in one page.

The name is inspired by Crockford's book *javascript the good parts*.

## Why?

Mostly as a personal learning exercise. The initial motivation was to create
a framework like Slim only slimmer.

Other reasons:

* We dislike depending on an endless amount of third-party packages
* We dislike the endless amount of upgrades for third-party packages
* We dislike monitoring third-party packages for vulnerabilities
* We dislike learning existing frameworks
* We dislike general-purpose frameworks because they are so large and complex
* We are less exposed to a [supply chain attack](https://blog.sonarsource.com/php-supply-chain-attack-on-composer)  
* Smaller and simpler components have less defects and vulnerabilities
* Creating components as if they were third-party packages makes for better components

Overview:

* `Cache`,`FileCache`,`MemoryCache`,`NullCache`
* `Clock`,`SystemClock`,`FrozenClock`
* `Config`
* `Console`
* `Container`
* `ErrorHandler`
* `File`,`TextFile`,`MemoryFile`
* `Application` (framework)  
* `HttpClient`,`Request`,`Response`
* `Json`
* `Logger`,`SimpleLogger`,`Handler`,`CliHandler`,`FileHandler`
* `Migrator` (database migrations)
* `Renderer` (template engine)
* `Router`  
* `Session`
* `Shell`
* `TestRunner` (unit testing tool)

All code resides under `dvikan\SimpleParts` and throws `SimpleException`
in case of failure.

## Cache

The `FileCache` is a persistent key-value store.

Cached items have a ttl (time-to-live) specified in seconds from now. By default they never expire.

The underlying storage is a file. The cache is serialized as json.

The cache is written to file when it is garbage-collected by php.

```php
$cache = new FileCache(new TextFile('./cache.json'));

$cache->set('foo'); // boolean true
$cache->set('foo', 'bar');
$cache->set('foo', 'bar', 60);
$cache->get('foo');

$cache->get('non_existing_key'); // NULL
$cache->get('non_existing_key', 'default'); // 'default'

$cache->delete('foo');
$cache->clear();
```

The `Cache` interface:
```php
interface Cache
{
    public function set(string $key, $value = true, int $ttl = 0): void;

    public function get(string $key, $default = null);

    public function delete(string $key): void;

    public function clear(): void;
}
```

## Clock

The clock component is useful in testing.

Clock interface:

```php
interface Clock
{
    public function now(): \DateTimeImmutable;
}
```

System clock:

```php
$clock = new SystemClock();

$now = $clock->now();

print $now->format('Y-m-d H:i:s'); // 2021-05-09 22:56:25
```

Frozen clock:

```php
$clock = new FrozenClock(new \DateTimeImmutable('1980-12-24'));

print $clock->now()->format('Y-m-d H:i:s'); // 1980-12-24 00:00:00

$clock->advance(new \DateInterval('P1Y'));

print $clock->now()->format('Y-m-d H:i:s'); // 1981-12-24 00:00:00
```

## Config

Config is an immutable wrapper around an array.

Keys in the custom config MUST have default values in the default config.

Trying to grab a non-existing key results in an exception.

The config values can be accessed with array syntax.

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

## Console

The console allows for some convenient printing of text, newlines and tables.

Also useful is colored text and exit code.

All methods support printf-style formatting.

```php
$console = new Console();

$console->print("hello %s\n", 'world');
$console->println('hello world');

$console->greenln('hello world');

$console->yellowln('hello world');

$console->redln('hello world');

$console->table(['id', 'user'], [
    [1, 'root'],
    [2, 'support'],
]);

$console->exit(1);
```
```
$ php test.php ; echo $?
hello world
hello world
hello world
hello world
hello world
+---------+---------+
| id      | user    |
+---------+---------+
| 1       | root    |
| 2       | support |
+---------+---------+
1
```

## Container

Container is a standard dependency container.

The resolved dependencies are cached.

Only closures are allowed as values.

```php
$container = new Container();

$container['http'] = function () {
    return new HttpClient();
};

$http = $container['http'];
```

## ErrorHandler

The error handler registers itself as php's error handler, exception handler and shutdown function.

A logger MUST be provided.

```php
$logger = new Logger('default', [new CliHandler()]);

$_ = ErrorHandler::create($logger);

print $foo;
```
```
[2021-05-10 02:18:56] default.INFO E_NOTICE: Undefined variable: foo at /home/u/simple-parts/test.php line 11 {
    "stacktrace": [
        "/home/u/simple-parts/test.php:11"
    ]
}
```

## TextFile

TextFile is a standard file abstraction.

```php
$file = new TextFile('./diary.txt');

$file->write('hello ');

$file->append('world');

if ($file->exists()) {
    print $file->read();
}
```

## Request

Request is an abstraction over an http request.

```php
$request = Request::fromGlobals();

print $request->method(); // 'GET'
print $request->uri(); // '/'
print $request->get('foo'); // NULL
print $request->get('id', '42'); // '42'
print $request->post('user', 'anon'); // 'anon'
```

## Response

Response is an abstraction over an http response.

```php
$response = new Response("Hello world\n", 200, ['Content-Type' => 'text/plain']);

$response->send();
```

It has a few utility methods:

```php
<?php

$response = new Response();

$response = $response
    ->withHeader('foo', 'bar')
    ->withCode(201)
    ->withJson(['message' => 'All is good'])
;

$response->send();
```

## HttpClient

The http client can be configured at construction and when doing requests.

Get request:

```php
$client = new HttpClient();

try {
    $response = $client->get('https://example.com/');
} catch (SimpleException $e) {
    print "Not 2xx\n";
}
```

Post request:

```php
$client = new HttpClient();

$response = $client->post('https://example.com/', [
    'body' => ['foo' => 'bar']]
);
```

Request:
```php
$client = new HttpClient();

$response = $client->request('GET', 'https://example.com/', [
    'useragent'         => 'firefox',
    'connect_timeout'   => 3,
    'timeout'           => 3,
]);
```

Config:
```json
[
    'useragent'         => 'HttpClient',
    'connect_timeout'   => 10,
    'timeout'           => 10,
    'follow_location'   => false,
    'max_redirs'        => 5,
    'auth_bearer'       => null,
    'client_id'         => null,
    'headers'           => [],
    'body'              => null
]
```

## Json

Json is wrapper that throws exception if the data fails to encode/decode as json.

```php
print Json::encode(['message' => 'hello']);
```

```
$ php test.php
{
    "message": "hello"
}
Array
(
    [message] => hello
)
```
## Logger

The logger requires a name and an array of handlers.
It has three log levels: `INFO`, `WARNING` and `ERROR`.

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


All handlers will receive a log item of the form:
```php
[
    'name'          => $this->name,
    'created_at'    => new DateTime(),
    'level'         => $level,
    'level_name'    => self::LEVEL_NAMES[$level],
    'message'       => $message,
    'context'       => $context,
]
```

## Migrator

The migrator is for database migrations.

Migrate migrations in `./migrations`:

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

Renderer is a template engine. `e` is a function that escapes for html context.

```php
$renderer = new Renderer();

print $renderer->render('./welcome.php', ['user' => 'bob']);
```

welcome.php:
```php
<?php namespace dvikan\SimpleParts; ?>

Hello <?= e($user) ?>
```

Configuration:
```php
$renderer = new Renderer([
    'context' => [], // Default context
]);
```

## Router

The router maps http requests to handlers.

```php
$router = new Router();

$router->addRoute('GET', '/', function() {
    return 'index';
});

$route = $router->dispatch('GET', '/');

if ($route[0] === Router::NOT_FOUND) {
    exit('404');
}

if ($route[0] === Router::METHOD_NOT_ALLOWED) {
    exit('Method not allowed');
}

$handler = $route[1];

print $handler();
```

## Session

Session is an abstraction over `$_SESSION`.
```php
$session = new Session();

$session->set('user', 'alice');

print $session->get('user');
```

## Shell

Shell is an abstraction over `exec()`.

```php
$shell = new Shell();

print $shell->execute('echo', ['hello', 'world']);
```

The arguments are escaped but you still need to make sure they are not parsed as command options.

You can sometimes use `--`, otherwise validate the arguments manually.
```php
$shell = new Shell();

print $shell->execute('git clone --', [$userInput]);
```
## TestRunner

The test runner is a tool for creating unit tests. Tests must inherit from `TestCase`.

Tests are assumed to be located in `./test`.

Example:

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
