<?php

declare(strict_types=1);

namespace PhpPico\Logger;

use Psr\Log\LoggerInterface;

abstract class AbstractLogger implements LoggerInterface
{
    use LoggerTrait;
}
