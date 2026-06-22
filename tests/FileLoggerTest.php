<?php

declare(strict_types=1);

namespace PhpPico\Logger\Tests;

use PhpPico\Logger\FileLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FileLoggerTest extends TestCase
{
    #[Test]
    public function path_is_normalized(): void
    {
        $fileLogger = new FileLogger(__DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR, 'test.log');

        $filePathHasMultipleSlashes = str_contains($fileLogger->getFilePath(), DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR);

        $this->assertFalse($filePathHasMultipleSlashes, 'File path should only contain a single directory separator');
    }

    #[Test]
    public function can_log(): void
    {
        $message    = 'test';
        $fileLogger = new FileLogger(__DIR__ . DIRECTORY_SEPARATOR . 'logs', 'test.log');

        unlink($fileLogger->getFilePath());
        $this->assertFileDoesNotExist($fileLogger->getFilePath(), 'File should not exist before logging');

        $fileLogger->info($message);
        $this->assertFileExists($fileLogger->getFilePath(), 'File should exist after logging');

        $logFileContents = file_get_contents($fileLogger->getFilePath());
        $this->assertStringContainsString($message, (string)$logFileContents, 'Log file should contain the message');
    }

    #[Test]
    public function interpolation(): void
    {
        $userId  = 1234;
        $message = 'User signed in: {userId}';
        $context = compact('userId');

        $fileLogger = new FileLogger(__DIR__ . DIRECTORY_SEPARATOR . 'logs', 'test.log');

        unlink($fileLogger->getFilePath());
        $this->assertFileDoesNotExist($fileLogger->getFilePath(), 'File should not exist before logging');

        $fileLogger->info($message, $context);
        $this->assertFileExists($fileLogger->getFilePath(), 'File should exist after logging');

        $logFileContents = file_get_contents($fileLogger->getFilePath());
        $this->assertStringContainsString("User signed in: $userId", (string)$logFileContents, 'Log file should contain the interpolated message');
    }

    #[Test]
    public function non_stringable_context_value_is_skipped(): void
    {
        $message    = 'User: {user}';
        $fileLogger = new FileLogger(__DIR__ . DIRECTORY_SEPARATOR . 'logs', 'test.log');

        unlink($fileLogger->getFilePath());

        $fileLogger->info($message, ['user' => ['id' => 1]]);

        $logFileContents = file_get_contents($fileLogger->getFilePath());
        $this->assertStringContainsString('{user}', (string)$logFileContents, 'Non-stringable values should leave their placeholder intact');
    }

    #[Test]
    public function exception_context_key_is_interpolated_with_its_message(): void
    {
        $exceptionMessage = 'something broke';
        $message          = 'Failed: {exception}';
        $fileLogger       = new FileLogger(__DIR__ . DIRECTORY_SEPARATOR . 'logs', 'test.log');

        unlink($fileLogger->getFilePath());

        $fileLogger->error($message, ['exception' => new RuntimeException($exceptionMessage)]);

        $logFileContents = file_get_contents($fileLogger->getFilePath());
        $this->assertStringContainsString("Failed: $exceptionMessage", (string)$logFileContents, 'Throwable in the exception key should render its message');
    }

    #[Test]
    public function new_lines_between_log_entries(): void
    {
        $messages = [
            'one',
            'two',
            'three',
            'four',
        ];

        $fileLogger       = new FileLogger(__DIR__ . DIRECTORY_SEPARATOR . 'logs', 'test.log', 4);

        unlink($fileLogger->getFilePath());

        foreach ($messages as $message) {
            $fileLogger->info($message);
        }

        $logFileContents = file_get_contents($fileLogger->getFilePath());
        foreach ($messages as $message) {
            $this->assertStringContainsString($message, (string)$logFileContents, sprintf('The log file should contain the message: "%s"', $message));
        }

        $this->assertStringContainsString(str_repeat(PHP_EOL, $fileLogger->newLines), (string)$logFileContents);
    }
}
