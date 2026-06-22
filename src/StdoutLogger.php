<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Override;
use Stringable;
use Psr\Log\InvalidArgumentException;
use RuntimeException;

final class StdoutLogger extends AbstractLogger
{
    /**
     * Logs to stdout with an arbitrary level.
     *
     * @param mixed                   $level
     * @param string|Stringable       $message
     * @param array<array-key, mixed> $context
     *
     * @return void
     * @throws InvalidArgumentException If the provided $level is invalid
     * @throws RuntimeException If the stream could not be created
     */
    #[Override]
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        if (!$this->isLevelValid($level)) {
            throw new InvalidArgumentException(sprintf('Invalid log level provided: %s', (string)$level));
        }

        $message = $this->format($level, $message, $context);
        $success = (bool)file_put_contents('php://stdout', $message);

        if (!$success) {
            throw new RuntimeException('Failed to write message to stdout');
        }
    }
}
