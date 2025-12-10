<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Override;
use Psr\Log\LoggerInterface;
use Stringable;

final class FileLogger extends AbstractLogger implements LoggerInterface
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
     * @param string                           $level
     * @param string|Stringable                $message
     * @param array<string, string|Stringable> $context
     *
     * @return void
     */
    #[Override]
    public function log($level, $message, array $context = []): void
    {
        file_put_contents($this->getFilePath(), $this->format(level: $level, message: $message, context: $context), FILE_APPEND);
    }
}
