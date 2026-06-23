<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerTrait as PsrLoggerTrait;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

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
        $replacements = [];

        foreach (array_keys($context) as $key) {
            $replacement = $this->stringifyContextValue($key, $context[$key]);

            if ($replacement !== null) {
                $replacements["{{$key}}"] = $replacement;
            }
        }

        return strtr((string) $message, $replacements);
    }

    /**
     * Casts a context value to string for interpolation, or null when it
     * cannot be represented as a string (per PSR-3, such values are skipped).
     *
     * @param array-key $key
     * @param mixed     $value
     *
     * @return string|null
     */
    protected function stringifyContextValue(string|int $key, mixed $value): ?string
    {
        if ($key === 'exception' && $value instanceof Throwable) {
            return $value->getMessage();
        }

        if (is_scalar($value) || $value instanceof Stringable || $value === null) {
            return (string) $value;
        }

        return null;
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
            throw new InvalidArgumentException(sprintf('Invalid log level provided: %s', (string) $level));
        }

        return vsprintf('%s [%s] %s', [
            date('Y-m-d H:i:s'),
            (string) $level,
            $this->interpolate($message, $context),
        ]);
    }
}
