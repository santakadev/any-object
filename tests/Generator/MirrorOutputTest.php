<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Generator\BuilderGenerator;
use Santakadev\AnyObject\Generator\FilesFinder;
use Santakadev\AnyObject\Generator\MirrorOutputResolver;

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

        $generator = new BuilderGenerator();

        // TODO: split responsibilities
        $files = (new FilesFinder())->find($includes, $excludes);
        foreach ($files as $file) {
            // One responsibility is to get class from file
            // Another one is to mirror that class
            $psr4MirrorToTest = new MirrorOutputResolver();
            $dest = $psr4MirrorToTest->mirrorFile($file);
            if ($dest) {
                $generator->generate($dest->class, $psr4MirrorToTest);
            }
        }

        $this->assertTrue(true);
    }
}
