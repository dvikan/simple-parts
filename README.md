# Simple Parts

Intentionally simple components for building applications.

Components:

* Router
* Template engine
* Dependency container
* Request
* Response
* HttpClient
* Session
* Logger
* Migrator (database migrations)
* Rss
* ErrorHandler
* Json
* JsonFile
* FileCache
* Console
* git wrapper (todo)
* irc client (todo)
* socket wrapper (todo)
* web framework (todo)
* Url (todo)
* Csv (todo)
* Collection (todo)
* ImapClient (todo)
* DataMapper (ORM, todo)
* Dotenv (todo)
* EventDispatcher (todo)
* Validator (todo)
* Random (todo)
* Guid (todo)
* Shell command

All classes reside under the `dvikan\SimpleParts` namespace.

## Router

The router accepts a regex and a handler. The handler MUST be
an array with a class and a method.

```php
$router = new Router();

$router->map('/user/([0-9]+)', [UserController::class, 'profile']);

$routeInfo = $router->match('/user/42');

$handler = $routeInfo[0];
$args = $routeInfo[1];

print $handler($args);
```
    
## Template engine

The template engine interprets the template as php code and the `e()`
function escapes for html context.
   
```php
print render('index.tpl', [
    'message' => 'Hello world <3',
]);
```

The template:

```php
<?php use function \dvikan\SimpleParts\e; ?>

<p>
    Message of the day: <?= e($message) ?>
</p>
```
    
## Dependency container

The container stores reusable dependencies.

```php
$container = new Container();

$container['http_client_options'] = [
    'connect_timeout' => 3,
];

$container['httpClient'] = function($c) {
    return new CurlHttpClient($c['http_client_options']);
};

$httpClient = $container['httpClient'];
```

## Request

```php
$request = Request::fromGlobals();

if ($request->isGet()) {
    print $request->get('id');
}
```
   
## Response

```php
$response = new Response('Hello world', 200, ['Content-type' => 'text/plain']);

$response->send();
```

## HttpClient

```php
$client = new CurlHttpClient();

$response = $client->get('https://example.com/');
$response = $client->post('https://example.com/', ['foo' => 'bar']);
```

## Session

```php
use function session;

session_start();

session('user', 'root');

print session('user');
```

## Logger

The logger has three log levels `INFO`, `WARNING` and `ERROR` and accepts a name and an array of handlers in its constructor.

```php
$logger = new SimpleLogger('default', [
    new PrintHandler(),
    new FileHandler('./error.log'),
    new LibNotifyHandler()
]);

$logger->info('hello');
$logger->warning('hello');
$logger->error('hello');
```

## Migrator (database migrations)

The migrator looks for `.sql` files in the provided folder.

```php
<?php

use dvikan\SimpleParts\Migrator;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

$migrator = new Migrator(
    new PDO('sqlite:' . __DIR__ . '/var/app.db'),
    __DIR__ . '/var/migrations/'
);

try {
    $messages = $migrator->migrate();

    if ($messages === []) {
        printf("No pending migrations\n");
    } else {
        printf("%s\n", implode("\n", $messages));
    }
} catch (SimpleException $e) {
    printf("Migration failure: %s\n", $e->getMessage());
}

```
## Rss

The rss client parses rss 2.0 and atom feeds.

```php
$rss = new Rss();

$feed = $rss->fromUrl('https://www.reddit.com/r/php/.rss');

foreach ($feed['items'] as $item) {
    printf("%s %s %s\n", $item['date'], $item['title'], $item['link']);
}
```

## ErrorHandler

The error handler registers itself for errors, exceptions and the shutdown function.
All php errors and exceptions are passed off to the logger with severity `error`.

```php
$logger = new Logger([new PrintHandler()]);

ErrorHandler::initialize($logger);
```

## Json

The `Json` class encodes and decodes json and will always throw exception on failure.

```php
print Json::encode(['hello']);
print Json::decode('{"foo":"bar"}');
```

## JsonFile

Read/write to a json file.

```php
$storage = new JsonFile('./numbers.json');

$storage->putContents([1,2,3]);

print_r($storage->getContents());
```

## FileCache

```php
$cache = new FileCache('/cache.json');

$cache->set('foo', 'bar');

print $cache->get('foo');
```

## Console

The `Console` component writes text to stdout. It can also render a table.

```php
<?php

use dvikan\SimpleParts\Console;

require __DIR__ . '/../vendor/autoload.php';

$console = new Console();

$console->write('Hello');
$console->writeln(' world!');

$console->greenln('Success');
$console->redln('Failure');

$headers = ['Id', 'User', 'Created'];

$rows = [
    ['1', 'root', '2020-11-01'],
    ['1000', 'joe', '2020-11-02'],
    ['1001', 'bob', '2020-11-03'],
];

$console->table($headers, $rows);
```

```
Hello world!
Success
Failure
+------------+------------+------------+
| Id         | User       | Created    |
+------------+------------+------------+
| 1          | root       | 2020-11-01 |
| 1000       | joe        | 2020-11-02 |
| 1001       | bob        | 2020-11-03 |
+------------+------------+------------+
```
