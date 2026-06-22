<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Override;
use Stringable;
use Psr\Log\InvalidArgumentException;
use RuntimeException;

final class FileLogger extends AbstractLogger
{
    /**
     * @throws \InvalidArgumentException If $newLines is less than 1
     */
    public function __construct(
        public readonly string $path,
        public readonly string $file,
        public readonly int $newLines = 2,
    ) {
        if ($newLines < 1) {
            throw new \InvalidArgumentException('$newLines must be greater than 0');
        }
    }

    /**
     * Get the full path to the log file.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        $path = $this->path;

        if (str_ends_with($path, DIRECTORY_SEPARATOR)) {
            $path = substr($path, 0, -1);
        }

        return $path . DIRECTORY_SEPARATOR . $this->file;
    }

    /**
     * Logs with an arbitrary level. Appends to the log file. If the log file does not exist, it is created.
     *
     * @param mixed                   $level
     * @param string|Stringable       $message
     * @param array<array-key, mixed> $context
     *
     * @return void
     * @throws InvalidArgumentException If the provided $level is invalid
     * @throws RuntimeException If the log file could not be created/opened
     */
    #[Override]
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        if (!$this->isLevelValid($level)) {
            throw new InvalidArgumentException(sprintf('Invalid log level provided: %s', (string)$level));
        }

        $file = fopen($this->getFilePath(), 'a+');
        if (!$file) {
            throw new RuntimeException(sprintf('Failed to create/open log file at "%s"', $this->getFilePath()));
        }

        fseek($file, 0);
        if (trim((string)fread($file, 32))) {
            fwrite($file, str_repeat(PHP_EOL, $this->newLines));
        }

        fwrite($file, $this->format($level, $message, $context));
        fclose($file);
    }
}
