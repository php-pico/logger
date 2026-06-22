# php-pico/logger

PSR-3 compliant logging package.

## Logging engines

Every engine is PSR-3 compliant (`Psr\Log\LoggerInterface`) and lives in the
`PhpPico\Logger` namespace, so they are interchangeable anywhere a PSR-3 logger
is expected. Each exposes the standard level shortcuts (`emergency`, `alert`,
`critical`, `error`, `warning`, `notice`, `info`, `debug`) plus the generic
`log($level, $message, $context)`.

Messages are formatted as `Y-m-d H:i:s [level] message`, where `level` is the
raw lowercase PSR-3 level:

```
2026-06-22 14:30:45 [info] User signed in: 1234
```

`{placeholder}` tokens in the message are replaced from `$context`. A `Throwable`
passed under the `exception` key interpolates to its message; context values that
cannot be cast to a string are skipped (per PSR-3). An invalid level throws
`Psr\Log\InvalidArgumentException`.

### FileLogger

Appends entries to a file, creating it if it does not exist. Entries are
separated by `$newLines` blank lines (default `2`; a value below `1` throws
`InvalidArgumentException`).

```php
use PhpPico\Logger\FileLogger;

$logger = new FileLogger(__DIR__ . '/logs', 'app.log');
$logger->info('User signed in: {userId}', ['userId' => 1234]);

$logger->getFilePath(); // /path/to/logs/app.log
```

Constructor: `__construct(string $path, string $file, int $newLines = 2)`

### StderrLogger

Writes formatted messages to `php://stderr`. Takes no constructor arguments.

```php
use PhpPico\Logger\StderrLogger;

$logger = new StderrLogger();
$logger->error('Failed: {exception}', ['exception' => new RuntimeException('boom')]);
```

### TestLogger

Stores entries in memory instead of writing them anywhere. Intended for use in
tests, where you assert against what was logged. Takes no constructor arguments.

```php
use PhpPico\Logger\TestLogger;

$logger = new TestLogger();
$logger->info('test message');

$logger->getLogs(); // [['level' => 'info', 'formatted_message' => '...', 'message' => '...', 'context' => []]]
$logger->flush();   // clear stored entries
```
