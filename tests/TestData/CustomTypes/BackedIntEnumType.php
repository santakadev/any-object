<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

enum BackedIntEnumType: int
{
    case A = 1;
    case B = 2;
    case C = 3;
}
