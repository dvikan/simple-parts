# Simple Parts

Intentionally simple components for building applications.

* `Cache`
* `Console`
* `Container`
* `ErrorHandler`
* `File`
* `HttpClient`
* `Json`
* `Request`/`Response`
* `Router`
* `Logger`
* `Migrator`
* `Rss`
* Session
* `Shell`
* Template engine
* `TwitchClient`
* `YahooFinanceClient`

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

All classes reside under the `dvikan\SimpleParts` namespace.

## Cache

```php
<?php

use dvikan\SimpleParts\FileCache;
use dvikan\SimpleParts\StreamFile;

require __DIR__ . '/../vendor/autoload.php';

$cache = new FileCache(new StreamFile('cache.json'));

$cache->set('foo', 'bar');

if ($cache->has('foo')) {
    print $cache->get('foo') . "\n";
}

$cache->delete('foo');

print $cache->get('foo', 'default') . "\n";

$cache->clear();
```

## Console

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
$console->exit();
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

## Container

```php
<?php

use dvikan\SimpleParts\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$container['database'] = function($c) {
    return new PDO($c['database_options']['dsn']);
};

$container['database_options'] = [
    'dsn' => 'sqlite::memory:',
];

/** @var PDO $database */
$database = $container['database'];
```

## ErrorHandler

```php
<?php

use dvikan\SimpleParts\ErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

$errorHandler = ErrorHandler::create();

print $foo;
```

## File

```php
<?php

use dvikan\SimpleParts\StreamFile;

require __DIR__ . '/../vendor/autoload.php';

$file = new StreamFile('test.txt');

$file->write('hello ');
$file->append('world');

if ($file->exists()) {
    print $file->read() . "\n";
}
```

## HttpClient

```php
<?php

use dvikan\SimpleParts\CurlHttpClient;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

$client = new CurlHttpClient();

try {
    $response1 = $client->get('http://example.com/');
    $response2 = $client->post('http://example.com/', ['foo' => 'bar']);

    print $response1->body();
    print $response2->body();
} catch (SimpleException $e) {
    print "Didn't get http 200\n";
}
```

## Json

```php
<?php

use dvikan\SimpleParts\Json;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

try {
    $json = Json::encode(['message' => 'hello']);
    print_r(Json::decode($json));
} catch (SimpleException $e) {
    printf("Unable to encode/decode json\n");
}
```

## Request/Response

```php
<?php

use dvikan\SimpleParts\Request;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::fromGlobals();

$uri = $request->uri();
$isGet = $request->isGet();
$id = $request->get('id') ?? -1;
$user = $request->post('user') ?? 'anon';

var_dump($uri, $isGet, $id, $user);
```

```php
<?php

use dvikan\SimpleParts\Response;

require __DIR__ . '/../vendor/autoload.php';

$response = new Response('hello world', 200, ['Content-Type' => 'text/html']);

$response->send();
```

## Router

```php
<?php

use dvikan\SimpleParts\Router;

require __DIR__ . '/../vendor/autoload.php';

class HttpController
{
    public function profile(array $args)
    {
        return 'The profile id is '. $args[0];
    }
}

$router = new Router();

$router->map('/user/([0-9]+)', [HttpController::class, 'profile']);

[$handler, $args] = $router->match('/user/42');

$class = $handler[0];
$method = $handler[1];
$controller = new $class();

print $controller->{$method}($args) . "\n";
```
    
## Logger

```php
<?php

use dvikan\SimpleParts\SimpleLogger;

require __DIR__ . '/../vendor/autoload.php';

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
<?php

use dvikan\SimpleParts\Migrator;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

$pdo = new PDO('sqlite:' . __DIR__ . '/var/app.db');
$folder = __DIR__ . '/var/migrations/';
$migrator = new Migrator($pdo, $folder);

try {
    $messages = $migrator->migrate();

    if ($messages === []) {
        print "No pending migrations\n";
    } else {
        print implode("\n", $messages) . "\n";
    }
} catch (SimpleException $e) {
    print $e->getMessage() . "\n";
}
```

## Rss

```php
<?php

use dvikan\SimpleParts\Rss;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

$rss = new Rss();

$feed = 'https://github.com/sebastianbergmann/phpunit/releases.atom';

try {
    $feed = $rss->fromUrl($feed);
} catch (SimpleException $e) {
    printf("Unable to fetch feed: %s\n", $e->getMessage());
    exit(0);
}

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
<?php

use function dvikan\SimpleParts\session;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$counter = session('counter') ?? 0;

$counter++;

session('counter', $counter);

print $counter;
```

## Shell

```php
<?php

use dvikan\SimpleParts\Shell;

require __DIR__ . '/../vendor/autoload.php';

$shell = new Shell();

print $shell->execute('echo', ['hello', 'world']);
```

## Template engine

```php
<?php

use function dvikan\SimpleParts\render;

require __DIR__ . '/../vendor/autoload.php';

$name = $_GET['name'] ?? 'anon';

print render('z.php', ['user' => $name]);
```

z.php:
```php
<?php use function \dvikan\SimpleParts\e; ?>

<p>
    Welcome <?= e($user) ?>
</p>
```

## TwitchClient

```php
<?php

use dvikan\SimpleParts\TwitchClient;

require __DIR__ . '/../vendor/autoload.php';

$accessToken = `secret twitch_access_token`;
$clientId = `secret twitch_client_id`;
$clientSecret = `secret twitch_client_secret`;

$client = new TwitchClient($clientId, $clientSecret, $accessToken);

/**
 * Save this for reuse
 */
var_dump($client->accessToken());

$streams = $client->streams();

print_r($streams);
```

## Yahoo finance client

```php
<?php

use dvikan\SimpleParts\SimpleException;
use dvikan\SimpleParts\YahooFinanceClient;

require __DIR__ . '/../vendor/autoload.php';

$client = new YahooFinanceClient();

try {
    $quote = $client->quote(['AAPL', '^GSPC']);

    foreach ($quote as $result) {
        printf(
            "%s %s (%s%%)\n",
            $result['symbol'],
            $result['regularMarketPrice'],
            $result['regularMarketChangePercent'],
        );
    }
} catch (SimpleException $e) {
    printf("Unable to fetch quotes: %s\n", $e->getMessage());
}
```
