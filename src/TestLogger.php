<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Override;
use Stringable;
use Psr\Log\InvalidArgumentException;

final class TestLogger extends AbstractLogger
{
    /** @var list<array{level: mixed, formatted_message: string, message: string|Stringable, context: array<array-key, mixed>}> */
    protected array $logs;

    public function __construct()
    {
        $this->logs = [];
    }

    /**
     * Logs to in-memory array with an arbitrary level.
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

        $this->logs[] = [
            'level'             => $level,
            'formatted_message' => $this->format($level, $message, $context),
            'message'           => $message,
            'context'           => $context,
        ];
    }

    /**
     * Get logs.
     * 
     * @return list<array{level: mixed, formatted_message: string, message: string|Stringable, context: array<array-key, mixed>}>
     */
    public function getLogs(): array
    {
        return $this->logs;
    }
}
