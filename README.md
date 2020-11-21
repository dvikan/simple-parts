# Simple Parts

Intentionally simple components for building applications.

* `Cache`
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
* File
* Console
* Twitch api client
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
* Shell command
* File

All classes reside under the `dvikan\SimpleParts` namespace.

Here is the directory tree for `src/`:

```shell
$ tree src/
src/
├── cache
│   ├── Cache.php
│   ├── FileCache.php
│   └── NullCache.php
├── Console.php
├── Container.php
├── ErrorHandler.php
├── functions.php
├── http
│   ├── CurlHttpClient.php
│   ├── HttpClient.php
│   ├── NullHttpClient.php
│   ├── Request.php
│   └── Response.php
├── json
│   ├── JsonFile.php
│   └── Json.php
├── logger
│   ├── FileHandler.php
│   ├── Handler.php
│   ├── LibNotifyHandler.php
│   ├── Logger.php
│   ├── NullLogger.php
│   ├── PrintHandler.php
│   └── SimpleLogger.php
├── Migrator.php
├── Router.php
├── Rss.php
└── SimpleException.php

5 directories, 26 files

```
## Router

The router accepts a regex and a handler. The handler MUST be
an array.

```php
<?php

use dvikan\SimpleParts\Router;

require __DIR__ . '/../vendor/autoload.php';

class HttpController
{
    public function profile(array $args)
    {
        $id = $args[0];
        return 'The profile id is '. $id;
    }
}

$router = new Router();

$router->map('/user/([0-9]+)', [HttpController::class, 'profile']);

[$handler, $args] = $router->match('/user/42');

$class = $handler[0];
$method = $handler[1];
$controller = new $class();

print $controller->{$method}($args);
```
    
## Template engine

The template engine is a function which accepts a template and a context. The `e()`
function escapes for html context.
   
```php
<?php

use function dvikan\SimpleParts\render;

require __DIR__ . '/../vendor/autoload.php';

print render('index.php', [
    'message' => 'Welcome ' . ($_GET['name'] ?? 'anon'),
]);
```

```php
<?php use function \dvikan\SimpleParts\e; ?>

<p>
    <?= e($message) ?>
</p>
```
## Dependency container

The container stores reusable dependencies.

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

## Request

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
   
## Response

```php
<?php

use dvikan\SimpleParts\Response;
use function dvikan\SimpleParts\response;

require __DIR__ . '/../vendor/autoload.php';

$response = new Response();
$response = response();
$response = new Response('Hello world', 200, ['Content-type' => 'text/plain']);

// Text response
$response->code(); // 200
$response->body(); // 'Hello world'
$response->ok(); // true
//$response->send();

// Json response
$response = response()->withJson(['id' => 42]);
$response->json(); //  ['id' => 42]
$response->send(); // {"id": 42}
```

## HttpClient

```php
<?php

use dvikan\SimpleParts\CurlHttpClient;
use dvikan\SimpleParts\HttpClient;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

$client = new CurlHttpClient([
    HttpClient::CONNECT_TIMEOUT => 3,
    HttpClient::USERAGENT => 'Curl',
]);

try {
    $response1 = $client->get('http://example.com/');
    $response2 = $client->post('http://example.com/', ['foo' => 'bar']);

    print $response1->body();
    print $response2->body();
} catch (SimpleException $e) {
    print "Didn't get http 200\n";
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

print "Status $counter\n";
```

## Logger

The logger has three log levels `INFO`, `WARNING` and `ERROR` and accepts a name and an array of handlers in its constructor.

```php
<?php

use dvikan\SimpleParts\FileHandler;
use dvikan\SimpleParts\PrintHandler;
use dvikan\SimpleParts\SimpleLogger;

require __DIR__ . '/../vendor/autoload.php';

$fileHandler = new FileHandler('./error.log');
$printHandler = new PrintHandler();

$logger = new SimpleLogger('default', [$printHandler, $fileHandler]);

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
<?php

use dvikan\SimpleParts\Rss;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

$rss = new Rss();

try {
    $feed = $rss->fromUrl('https://www.reddit.com/r/php/');
} catch (SimpleException $e) {
    printf("Unable to fetch feed: %s\n", $e->getMessage());
    exit(0);
}

foreach ($feed['items'] as $item) {
    printf(
        "%s %s %s\n",
        $item['date'],
        $item['title'],
        $item['link']
    );
}
```

## ErrorHandler

The error handler registers itself for errors, exceptions and the shutdown function.
All php errors and exceptions are passed off to the logger with severity `error`.

```php
<?php

use dvikan\SimpleParts\ErrorHandler;
use dvikan\SimpleParts\PrintHandler;
use dvikan\SimpleParts\SimpleLogger;

require __DIR__ . '/../vendor/autoload.php';

$logger = new SimpleLogger('default', [new PrintHandler()]);

ErrorHandler::create($logger);

print $foo;
```

## Json

The `Json` class encodes and decodes json and will always throw exception on failure.

```php
<?php

use dvikan\SimpleParts\Json;
use dvikan\SimpleParts\SimpleException;

require __DIR__ . '/../vendor/autoload.php';

try {
    $json = Json::encode(['message' => 'hello']);
    print Json::decode($json)['message'] . "\n";
} catch (SimpleException $e) {
    printf("Unable to encode/decode json\n");
}
```

## File

The `JsonFile` follows the `File` contract.

```php
<?php

use dvikan\SimpleParts\JsonFile;

require __DIR__ . '/../vendor/autoload.php';

$storage = new JsonFile('./var/numbers.json');

$storage->write([1,2,3]);

print_r($storage->read());
```

## Cache

The `FileCache`, `ArrayCache` and `NullCache` follow the same contract.

```php
<?php

use dvikan\SimpleParts\FileCache;
use dvikan\SimpleParts\JsonFile;

require __DIR__ . '/../vendor/autoload.php';

$cache = new FileCache(new JsonFile('cache.json'));

$cache->set('foo', 'bar');

print $cache->get('aaaa', 'default');

$hasFoo = $cache->has('foo');

$cache->delete('foo');

$cache->clear();
```

## Console

The `Console` component writes text to stdout. It can also render tables.

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

$console->exit(1);
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

## Twitch api client

https://dev.twitch.tv/docs/api/reference

```php
<?php

use dvikan\SimpleParts\TwitchClient;

require __DIR__ . '/../vendor/autoload.php';

$accessToken = '';
$clientId = '';
$clientSecret = '';

$client = new TwitchClient($clientId, $clientSecret, $accessToken);

/**
 * Persist this for reuse
 */
//$accessToken = $twitchClient->accessToken();

$result = $client->streams();

print_r($result);
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

Example response for AAPL:
```
[language] => en-US
[region] => US
[quoteType] => EQUITY
[quoteSourceName] => Nasdaq Real Time Price
[triggerable] => 1
[currency] => USD
[exchange] => NMS
[shortName] => Apple Inc.
[longName] => Apple Inc.
[messageBoardId] => finmb_24937
[exchangeTimezoneName] => America/New_York
[exchangeTimezoneShortName] => EST
[gmtOffSetMilliseconds] => -18000000
[market] => us_market
[esgPopulated] => 
[marketState] => REGULAR
[priceHint] => 2
[firstTradeDateMilliseconds] => 345479400000
[regularMarketChange] => -0.6791992
[regularMarketChangePercent] => -0.57248753
[regularMarketTime] => 1605891653
[regularMarketPrice] => 117.9608
[regularMarketDayHigh] => 118.77
[regularMarketDayRange] => 117.84 - 118.77
[regularMarketDayLow] => 117.84
[regularMarketVolume] => 32115496
[regularMarketPreviousClose] => 118.64
[bid] => 118.12
[ask] => 118.13
[bidSize] => 10
[askSize] => 14
[fullExchangeName] => NasdaqGS
[financialCurrency] => USD
[regularMarketOpen] => 118.64
[averageDailyVolume3Month] => 154613424
[averageDailyVolume10Day] => 93837362
[fiftyTwoWeekLowChange] => 64.808304
[fiftyTwoWeekLowChangePercent] => 1.2192899
[fiftyTwoWeekRange] => 53.1525 - 137.98
[fiftyTwoWeekHighChange] => -20.019196
[fiftyTwoWeekHighChangePercent] => -0.14508766
[fiftyTwoWeekLow] => 53.1525
[fiftyTwoWeekHigh] => 137.98
[dividendDate] => 1605139200
[earningsTimestamp] => 1603989000
[earningsTimestampStart] => 1611658740
[earningsTimestampEnd] => 1612180800
[trailingAnnualDividendRate] => 0.795
[trailingPE] => 35.963657
[trailingAnnualDividendYield] => 0.006700944
[epsTrailingTwelveMonths] => 3.28
[epsForward] => 4.33
[epsCurrentYear] => 3.96
[priceEpsCurrentYear] => 29.78808
[sharesOutstanding] => 17102499840
[bookValue] => 3.849
[fiftyDayAverage] => 116.655
[fiftyDayAverageChange] => 1.3058014
[fiftyDayAverageChangePercent] => 0.011193703
[twoHundredDayAverage] => 103.28055
[twoHundredDayAverageChange] => 14.680252
[twoHundredDayAverageChangePercent] => 0.14213957
[marketCap] => 2005545910272
[forwardPE] => 27.24268
[priceToBook] => 30.64713
[sourceInterval] => 15
[exchangeDataDelayedBy] => 0
[tradeable] => 
[displayName] => Apple
[symbol] => AAPL
```
