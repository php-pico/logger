<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Override;
use Stringable;
use Psr\Log\InvalidArgumentException;

final class FileLogger extends AbstractLogger
{
    public function __construct(
        public readonly string $path,
        public readonly string $file,
    ) {
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
     */
    #[Override]
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        if (!$this->isLevelValid($level)) {
            throw new InvalidArgumentException(sprintf('Invalid log level provided: %s', (string)$level));
        }

        $message = $this->format($level, $message, $context);
        file_put_contents($this->getFilePath(), $this->format($level, $message, $context), FILE_APPEND);
    }
}
