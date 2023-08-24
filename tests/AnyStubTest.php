<?php

namespace Santakadev\AnyStub\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyStub\AnyStub;

class AnyStubTest extends TestCase
{
    public function test(): void
    {
        $any = new AnyStub();
        $this->assertEquals(AnyStubTest::class, $any->of(AnyStubTest::class));
    }
}
