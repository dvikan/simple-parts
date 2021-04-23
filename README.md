# Simple Parts

Intentionally simple components for building applications.

* `Cache`, `FileCache`, `MemoryCache`, `NullCache`
* `Console`
* `Container`
* `ErrorHandler`
* `File`, `TextFile`, `MemoryFile`, `NullFile`
* `HttpClient`, `CurlHttpClient`, `NullHttpClient`
* `Json`
* `Request`, `Response`
* `Router`
* `Logger`, `SimpleLogger`, `NullLogger`, `Handler`, `PrintHandler`, `FileHandler`
* `Migrator`
* `Rss`
* `Session`
* `Shell`
* `Template`

TODO:

* git wrapper (todo)
* irc client (todo)
* socket wrapper (todo)
* web framework (todo)
* Url,Uri, (todo)
* Csv (todo)
* Collection (todo)
* ImapClient (todo)
* DataMapper (ORM, todo)
* Dotenv (todo)
* EventDispatcher (todo)
* Validator, validate values, validate array structure (todo)
* Random (todo)
* Guid (todo)
* Flat file database
* vardumper
* throttling
* captcha

All classes reside under the `dvikan\SimpleParts` namespace.

## Cache

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\FileCache;
use dvikan\SimpleParts\TextFile;

require __DIR__ . '/vendor/autoload.php';

$cache = new FileCache(new TextFile('cache.json'));

print $cache->get('foo', 'default') . "\n";

$cache->set('foo', 'bar');

if ($cache->has('foo')) {
    print $cache->get('foo') . "\n";
}

// $cache->delete('foo');
// $cache->clear();
```

## Console

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Console;

require __DIR__ . '/vendor/autoload.php';

$console = new Console();

$console->write('Hello');
$console->writeln(' world!');

$console->greenln('Success');
```

```
Hello world!
Success
```

## Container

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Container;

require __DIR__ . '/vendor/autoload.php';

$container = new Container();

$container['database'] = function($container) {
    return new PDO($container['dsn']);
};

$container['dsn'] = 'sqlite::memory:';

/** @var PDO $database */
$database = $container['database'];
```

## ErrorHandler

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\ErrorHandler;

require __DIR__ . '/vendor/autoload.php';

$errorHandler = ErrorHandler::create();

print foo();
```
```
default.ERROR Uncaught Exception Error: Call to undefined function foo() in test.php:9
```

## File

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\TextFile;

require __DIR__ . '/vendor/autoload.php';

$file = new TextFile('./application.log');

$file->write('hello ');
$file->append('world');

if ($file->exists()) {
    print $file->read();
}
```

## HttpClient

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\CurlHttpClient;

require __DIR__ . '/vendor/autoload.php';

$client = new CurlHttpClient();

$response = $client->get('https://example.com');

print $response->body();
```

## Json

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Json;

require __DIR__ . '/vendor/autoload.php';

print Json::encode(['message' => 'hello']);
```

## Request

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Request;

require __DIR__ . '/vendor/autoload.php';

$request = new Request([
    'id' => '6',
], [
    'user' => 'bob',
], [
    'REQUEST_METHOD' => 'POST',
    'REQUEST_URI' => '/about',
]);

$uri = $request->uri();
$isGet = $request->isGet();
$id = $request->get('id');
$user = $request->post('user');
```

## Response

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Response;

require __DIR__ . '/vendor/autoload.php';

$response = new Response();

$response = new Response("Hello\nworld", 200, ['Content-Type' => 'text/plain']);

$response->send();
```

## Router

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Router;

require __DIR__ . '/vendor/autoload.php';

class Controller
{
    public function profile(array $vars)
    {
        $id = $vars[0];
        return "The user id is {$id}";
    }
}

$routes = [
    '/profile/([0-9]+)' => [Controller::class, 'profile'],
];

$router = new Router($routes);

$route = $router->match('/profile/3');

if ($route === []) {
    exit("404");
}

$handler = $route[0];
$args = $route[1];

$handlerClass = $handler[0];
$handlerMethod = $handler[1];
$handlerObject = new $handlerClass();

$result = $handlerObject->{$handlerMethod}($args);

print $result;
```
    
## Logger

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\SimpleLogger;

require __DIR__ . '/vendor/autoload.php';

$logger = new SimpleLogger();

$logger->info('hello');
$logger->warning('hello');
$logger->error('hello');
```
```
[2020-11-22 21:59:14] default.INFO hello
[2020-11-22 21:59:14] default.WARNING hello
[2020-11-22 21:59:14] default.ERROR hello
```

## Migrator

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Migrator;

require __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('sqlite:' . __DIR__ . '/application.db');

$migrator = new Migrator($pdo);

$result = $migrator->migrate();

if ($result === []) {
    exit;
}

print implode("\n", $result) . "\n";
```

## Rss

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Rss;

require __DIR__ . '/vendor/autoload.php';

$rss = new Rss();

$url = 'https://github.com/sebastianbergmann/phpunit/releases.atom';

$feed = $rss->fromUrl($url);

foreach ($feed['items'] as $item) {
    printf(
        "%s %s %s\n",
        $item['date'],
        $item['title'],
        $item['link'] ?? '(no link)'
    );
}
```

## Session

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Session;

require __DIR__ . '/vendor/autoload.php';

$session = new Session();

$session->set('user', 'alice');

print 'Welcome, ' . $session->get('user', 'anon');
```

## Shell

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Shell;

require __DIR__ . '/vendor/autoload.php';

$shell = new Shell();

print $shell->execute('echo', ['-n', 'hello', 'world']);
```

## Template engine

```php
<?php declare(strict_types=1);

use function dvikan\SimpleParts\render;

require __DIR__ . '/vendor/autoload.php';

$name = $_GET['name'] ?? 'anon';

print render('z.php', ['user' => $name]);
```

```php
<?php use function \dvikan\SimpleParts\e; ?>

<p>
    Welcome <?= e($user) ?>
</p>
```