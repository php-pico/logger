<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

abstract class AbstractLogger implements LoggerInterface
{
    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string|Stringable                   $message
     * @param array<array-key, string|Stringable> $context
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
     * @see LoggerInterface::emergency()
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::EMERGENCY, message: $message, context: $context);
    }

    /**
     * @see LoggerInterface::alert()
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::ALERT, message: $message, context: $context);
    }

    /**
     * @see LoggerInterface::critical()
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::CRITICAL, message: $message, context: $context);
    }

    /**
     * @see LoggerInterface::error()
     */
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::ERROR, message: $message, context: $context);
    }

    /**
     * @see LoggerInterface::warning()
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::WARNING, message: $message, context: $context);
    }

    /**
     * @see LoggerInterface::notice()
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::NOTICE, message: $message, context: $context);
    }

    /**
     * @see LoggerInterface::info()
     */
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::INFO, message: $message, context: $context);
    }

    /**
     * @see LoggerInterface::debug()
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(level: LogLevel::DEBUG, message: $message, context: $context);
    }

    /**
     * Format a message according to the given log level.
     *
     * @param string                           $level
     * @param string|Stringable                $message
     * @param array<string, string|Stringable> $context
     *
     * @return string
     */
    public function format(string $level, string|Stringable $message, array $context = []): string
    {
        return vsprintf("%s [%s] %s\n", [
            date('Y-m-d H:i:s'),
            $level,
            $this->interpolate($message, $context),
        ]);
    }
}
