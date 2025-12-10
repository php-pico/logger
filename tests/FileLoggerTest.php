<?php

declare(strict_types=1);

namespace PhpPico\Logger\Tests;

use PhpPico\Logger\FileLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileLoggerTest extends TestCase
{
    #[Test]
    public function path_is_normalized(): void
    {
        $fileLogger = new FileLogger(path: __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR, file: 'test.log',);

        $filePathHasMultipleSlashes = str_contains($fileLogger->getFilePath(), DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR);

        $this->assertFalse($filePathHasMultipleSlashes, 'File path should only contain a single directory separator');
    }

    #[Test]
    public function can_log(): void
    {
        $message    = 'test';
        $fileLogger = new FileLogger(path: __DIR__ . DIRECTORY_SEPARATOR . 'logs', file: 'test.log',);

        unlink($fileLogger->getFilePath());
        $this->assertFileDoesNotExist($fileLogger->getFilePath(), 'File should not exist before logging');

        $fileLogger->info(message: $message);
        $this->assertFileExists($fileLogger->getFilePath(), 'File should exist after logging');

        $logFileContents = file_get_contents($fileLogger->getFilePath());
        $this->assertStringContainsString($message, (string)$logFileContents, 'Log file should contain the message');
    }

    #[Test]
    public function interpolation(): void
    {
        $userId  = 1234;
        $message = 'User signed in: ${userId}';
        $context = compact('userId');

        $fileLogger = new FileLogger(path: __DIR__ . DIRECTORY_SEPARATOR . 'logs', file: 'test.log',);

        unlink($fileLogger->getFilePath());
        $this->assertFileDoesNotExist($fileLogger->getFilePath(), 'File should not exist before logging');

        $fileLogger->info(message: $message, context: $context);
        $this->assertFileExists($fileLogger->getFilePath(), 'File should exist after logging');

        $logFileContents = file_get_contents(filename: $fileLogger->getFilePath());
        $this->assertStringContainsString("User signed in: $userId", (string)$logFileContents, 'Log file should contain the interpolated message');
    }
}
