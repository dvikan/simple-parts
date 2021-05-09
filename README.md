# Simple Parts

Simple components for building web applications.

These components are intentionally simple with minimal configurability.

There are no interfaces here.

The name is inspired by Crockford's book javascript the good parts.

Run tests: `composer run test`

* `Cache`
* `Clock`,`SystemClock`  
* `Config`
* `Console`
* `Container`
* `ErrorHandler`
* `File`,`TextFile`,`MemoryFile`
* `HttpClient`,`Request`,`Response`,`Router`
* `Json`
* `Logger`,`CliHandler`,`FileHandler`
* `Migrator`
* `Renderer`
* `Session`
* `Shell`
* `Test`

## Cache

```php
<?php

$cache = new Cache('./cache.json');

$cache->set('foo');
$cache->set('foo', 'bar');
$cache->set('foo', 'bar', 60);

$cache->get('foo');
$cache->get('foo', 'default');

$cache->delete('foo');

$cache->clear();
```

## Clock

## Config

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

$container['config'] = function($c) {
    return ['env' => 'dev'];
};

print $container['config'];
```

## ErrorHandler

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\ErrorHandler;

require __DIR__ . '/vendor/autoload.php';

ErrorHandler::create();

print foo();
```
```
default.ERROR Uncaught Exception Error: Call to undefined function foo() in test.php:9
```

## TextFile

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\TextFile;

require __DIR__ . '/vendor/autoload.php';

$file = new TextFile('./diary.txt');

$file->write('hello ');
$file->append('world');

if ($file->exists()) {
    print $file->read();
}
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

$response = new Response('Hello world', 200, ['Content-Type' => 'text/plain']);

$response->send();
```

## HttpClient

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\HttpClient;

require __DIR__ . '/vendor/autoload.php';

$client = new HttpClient();

$response = $client->get('https://example.com');

print $response->body();
```

## Json

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Json;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/vendor/autoload.php';

try {
    print Json::encode(['message' => 'hello']);
} catch (SimpleException $e) {
    print $e->getMessage();
}
```

## Logger

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Logger;
use dvikan\SimpleParts\CliHandler;

require __DIR__ . '/vendor/autoload.php';

$logger = new Logger('default', [new CliHandler()]);

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

$pdo = new PDO('sqlite:application.db');

$migrator = new Migrator($pdo);

$result = $migrator->migrate();

if ($result === []) {
    exit;
}

print implode("\n", $result) . "\n";
```

## Renderer

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Renderer;

require __DIR__ . '/vendor/autoload.php';

$renderer = new Renderer();

$name = $_GET['name'] ?? 'anon';

print $renderer->render('welcome.php', ['name' => $name]);
```

welcome.php:
```php
<?php namespace dvikan\SimpleParts; ?>

<p>
    Welcome <?= e($name) ?>
</p>
```

## Router

```php
<?php declare(strict_types=1);

use dvikan\SimpleParts\Router;

require 'vendor/autoload.php';

$router = new Router();

$router->get('/', function() { return 'index'; });

$router->post('/delete', function() { return 'delete'; });

$router->map(['GET', 'POST'], '/update', function() { return 'update'; });

$router->post('/delete/(\d+)', function(array $vars) {
    $id = (int) $vars[0];
    return 'id: ' . $id;
});

$method = 'GET';
$uri = '/update';

$route = $router->dispatch($method, $uri);

if ($route[0] === Router::NOT_FOUND) {
    exit('404');
}

if ($route[0] === Router::METHOD_NOT_ALLOWED) {
    exit('Method not allowed');
}

$handler = $route[1];
$args = $route[2];

print $handler($args);
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

## Test

## Todo

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
