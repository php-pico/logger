<?php

namespace PhpPico\Logger\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PhpPico\Logger\StdoutLogger;

final class StdoutLoggerTest extends TestCase
{
    #[Test]
    public function can_log_to_stdout(): void
    {
        $message = 'test';

        $logger = new StdoutLogger();

        $this->expectNotToPerformAssertions();
        $logger->info($message);
    }
}
