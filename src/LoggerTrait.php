<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Psr\Log\LoggerTrait as PsrLoggerTrait;
use Stringable;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;

trait LoggerTrait
{
    use PsrLoggerTrait;

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string|Stringable       $message
     * @param array<array-key, mixed> $context
     *
     * @return string
     */
    protected function interpolate(string|Stringable $message, array $context = []): string
    {
        $result = (string)$message;

        foreach ($context as $key => $value) {
            $result = str_replace("{{$key}}", (string)$value, $result);
        }

        return $result;
    }

    /**
     * Returns if a log level is valid.
     * @see LogLevel
     * 
     * @param mixed $level
     * 
     * @return bool
     */
    public function isLevelValid(mixed $level): bool
    {
        $validLevels = [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ];

        return in_array($level, $validLevels, true);
    }

    /**
     * Format a message according to the given log level.
     *
     * @param mixed                   $level
     * @param string|Stringable       $message
     * @param array<array-key, mixed> $context
     *
     * @return string
     * @throws InvalidArgumentException If the provided $level is invalid
     */
    public function format(mixed $level, string|Stringable $message, array $context = []): string
    {
        if (!$this->isLevelValid($level)) {
            throw new InvalidArgumentException(sprintf('Invalid log level provided: %s', (string)$level));
        }

        return vsprintf("%s [%s] %s", [
            date('Y-m-d H:i:s'),
            (string)$level,
            $this->interpolate($message, $context),
        ]);
    }
}
