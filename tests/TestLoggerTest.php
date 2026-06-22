<?php

declare(strict_types=1);

namespace PhpPico\Logger\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PhpPico\Logger\TestLogger;

final class TestLoggerTest extends TestCase
{
    #[Test]
    public function it_logs(): void
    {
        $message = 'test';

        $logger = new TestLogger();

        $this->assertCount(0, $logger->getLogs());

        $logger->info($message);

        $this->assertCount(1, $logger->getLogs());
        $this->assertEquals($message, $logger->getLogs()[0]['message']);
    }
}
