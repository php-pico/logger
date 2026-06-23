<?php

namespace PhpPico\Logger\Tests;

use PhpPico\Logger\StderrLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StderrLoggerTest extends TestCase
{
    #[Test]
    public function can_log_to_stderr(): void
    {
        $message = 'test';

        $logger = new StderrLogger();

        $this->expectNotToPerformAssertions();
        $logger->info($message);
    }
}
