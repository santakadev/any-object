<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

enum BackedStringEnumType: string
{
    case A = 'a';
    case B = 'b';
    case C = 'c';
}
