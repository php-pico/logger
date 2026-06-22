# php-pico/logger

PSR-3 compliant logging package.

## Message format

Messages are formatted as `Y-m-d H:i:s [level] message`, where `level` is the
raw lowercase PSR-3 level:

```
2026-06-22 14:30:45 [info] User signed in: 1234
```

## Context interpolation

When logging, you can pass a `$context` key-value map to the `log()` method. This will be interpolated into the message replacing the matching placeholders in the message:

```php
$level   = \Psr\Log\LogLevel::DEBUG;
$message = 'User {userId} signed in from {userIp}';
$context = [
    'userId' => 1,
    'userIp' => '127.0.0.1',
];

// Logs: "User 1 signed in from 127.0.0.1"
$logger->log($level, $message, $context);
```

## Logging engines

Every engine is PSR-3 compliant and implements the `Psr\Log\LoggerInterface`.

| Engine          | Production | Notes                                     |
| --------------- | ---------- | ----------------------------------------- |
| `FileLogger`    | Yes        | Logs/append to a file.                    |
| `StderrLogger`  | Yes        | Logs to `stderr`.                         |
| `TestLogger`    | No         | Keeps logs in-memory. Useful for testing. |

**Note:**

### FileLogger

Appends entries to a file, creating it if it does not exist. Entries are
separated by `$newLines` blank lines (default `2`; a value below `1` throws
`InvalidArgumentException`).

```php
use PhpPico\Logger\FileLogger;

$path     = __DIR__ . '/logs';
$file     = 'app.log';
$newLines = 2;

$logger = new FileLogger($path, $file, $newLines);
```

Constructor: `__construct(string $path, string $file, int $newLines = 2)`

### StderrLogger

Writes formatted messages to `php://stderr`. Takes no constructor arguments.

```php
use PhpPico\Logger\StderrLogger;

$logger = new StderrLogger();
```

### TestLogger

Stores entries in memory instead of writing them anywhere. Intended for use in
tests, where you assert against what was logged. Takes no constructor arguments.

```php
use PhpPico\Logger\TestLogger;

$logger = new TestLogger();

// Get logs stored
$logger->getLogs();

// Flush the logs (down the toilet)
$logger->flush();
```
