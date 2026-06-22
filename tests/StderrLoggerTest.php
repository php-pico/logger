<?php

namespace PhpPico\Logger\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PhpPico\Logger\StderrLogger;

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
