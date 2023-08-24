<?php

namespace Santakadev\AnyStub\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyStub\AnyStub;

class AnyStubTest extends TestCase
{
    public function test(): void
    {
        $any = new AnyStub();

        $product = $any->of(Product::class);

        $this->assertIsString($product->name);
        $this->assertGreaterThan(0, strlen($product->name));
        $this->assertIsString($product->description);
        $this->assertGreaterThan(0, strlen($product->description));
        $this->assertIsInt($product->price);
        $this->assertIsFloat($product->tax);
        $this->assertIsBool($product->available);
    }
}
