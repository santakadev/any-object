<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class AnyObjectTest extends TestCase
{
    use AnyObjectDataProvider;

    /** @dataProvider anyProvider */
    public function test_with_fixed_data(AnyObject $any): void
    {
        $object = $any->of(class: StringObject::class, with: ['value' => 'foo']);
        $this->assertEquals('foo', $object->value);
    }

    // TODO: find the best way to test promoted and non-promoted properties
}
