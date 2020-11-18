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
* Migrator
* Rss
* ErrorHandler
* Json
* JsonFile
* FileCache
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
* Dotenv (todo)
* EventDispatcher (todo)

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
    
## Container

The container stores reusable dependencies.

```php
$container = new Container();

$container['http_client_options'] = [
    'connect_timeout' => 3,
];

$container['httpClient'] = function($c) {
    return new HttpClient($c['http_client_options']);
};

$httpClient = $container['httpClient'];
```

## Request

```php
$request = Request::fromGlobals();

print $request->get('id');
print $request->post('message');
print $request->body();
print $request->json();
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

The logger has three severity levels and accepts an array of handlers in its constructor.

```php
$logger = new Logger([
    new PrintHandler(),
    new FileHandler('./error.log'),
    new LibNotifyHandler()
]);

$logger->info('hello');
$logger->warning('hello');
$logger->error('hello');
```

## Migrator (database migrations)

The migrator assumes that your migrations are stored as `.sql` files in `./migrations`
and that your persistent folder is at `./var`.

```php
$migrator = new Migrator(new PDO('sqlite:db.sqlite'));

$migrator->migrate();
```

Example:

```console
$ cat migrations/001-init.sql
create table user (
    id integer primary key,
    name text,
    mobile text,
    email text,
    created_at text
);

$ ./bin/migrate.php
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
