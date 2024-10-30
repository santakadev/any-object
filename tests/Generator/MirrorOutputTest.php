<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Generator\Generator;
use Santakadev\AnyObject\Generator\MirrorOutputResolver;
use Santakadev\AnyObject\Generator\WrapNameResolver;

class MirrorOutputTest extends TestCase
{
    public function testFind(): void
    {
        $finder = new MirrorOutputResolver();
        $includes = [
            'tests/TestData/ScalarTypes/BoolObject.php',
            'tests/TestData/CustomTypes/CustomObject.php',
        ];
        $excludes = [
            'tests/TestData/ScalarTypes/Any*.php',
            'tests/TestData/ScalarTypes/None.php',
            'tests/TestData/CustomTypes/Any*.php',
            'tests/TestData/CustomTypes/None.php',
        ];

        // TODO: handler this patterns
        //$finder->find('./src/Generator/*.php');
        //$finder->find(__DIR__ . '/src/Generator/*.php');


        $generator = new Generator();
        $generator->generate(
            'factory',
            $includes,
            $excludes,
            new WrapNameResolver('Any', 'Builer'),
            //new FixedOutputResolver('tests/Tests', 'Test'),
            new MirrorOutputResolver()
        );


        $this->assertTrue(true);
    }
}
