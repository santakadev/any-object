<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfStringObjectBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyMoneyBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNamedConstructorWithPrivateConstructObjectBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyProductBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyProductPriceBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyQuantityBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\AnySharedTypesInConstructorObjectBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfStringObjectBuilder;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\NamedConstructorWithPrivateConstructObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\SharedTypesInConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\Money\Amount;
use Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\Product;
use Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\ProductId;
use Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\ProductName;
use Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\ProductPrice;
use Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\Quantity;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfStringObject;

class BuilderGeneratorTest extends BuilderGeneratorTestCase
{
    public function test_builder(): void
    {
        $this->generateBuilderFor(Quantity::class);
        $text = $this->readGeneratedAnyFileFor(Quantity::class);
        Approvals::verifyString($text);

        $quantity = AnyQuantityBuilder::create()
            ->withValue(3)
            ->build();

        $this->assertEquals(3, $quantity->value);
    }

    public function test_generator_from_named_constructor(): void
    {
        $this->generateBuilderFor(NamedConstructorWithPrivateConstructObject::class);

        $text = $this->readGeneratedAnyFileFor(NamedConstructorWithPrivateConstructObject::class);
        Approvals::verifyString($text);
        $test = AnyNamedConstructorWithPrivateConstructObjectBuilder::create()
            ->withValue("test")
            ->build();
        $this->assertEquals("test", $test->value);
    }

    public function test_complex_builder(): void
    {
        $this->generateBuilderFor(Product::class);
        $text = $this->readGeneratedAnyFileFor(Product::class);
        Approvals::verifyString($text);

        $money = AnyMoneyBuilder::create()
            ->withAmount(new Amount(5))
            ->build();
        $productPrice = new ProductPrice($money);

        $product = AnyProductBuilder::create()
            ->withId(new ProductId('id'))
            ->withName(new ProductName('name'))
            ->withPrice($productPrice)
            ->build();

        $this->assertEquals('id', $product->id()->value);
        $this->assertEquals('name', $product->name()->value);
        $this->assertEquals(5, $product->price()->value->amount->value);
    }

    public function test_array(): void
    {
        $this->generateBuilderFor(GenericArrayOfStringObject::class);
        $text = $this->readGeneratedAnyFileFor(GenericArrayOfStringObject::class);
        Approvals::verifyString($text);

        AnyGenericArrayOfStringObjectBuilder::create()
            ->withValue(["a", "b"])
            ->build();
    }

    public function test_variadic_constructor(): void
    {
        $this->generateBuilderFor(VariadicOfStringObject::class);
        $text = $this->readGeneratedAnyFileFor(VariadicOfStringObject::class);
        Approvals::verifyString($text);

        AnyVariadicOfStringObjectBuilder::create()
            ->withValue(["a", "b"])
            ->build();
    }
    public function test_shared_types_in_constructor(): void
    {
        $this->generateBuilderFor(SharedTypesInConstructorObject::class);
        $text = $this->readGeneratedAnyFileFor(SharedTypesInConstructorObject::class);
        Approvals::verifyString($text);
        AnySharedTypesInConstructorObjectBuilder::create()
            ->withValue1(new StringObject("value1"))
            ->withValue1(new StringObject("value2"))
            ->withValue1(new StringObject("value3"))
            ->build();
    }
}
