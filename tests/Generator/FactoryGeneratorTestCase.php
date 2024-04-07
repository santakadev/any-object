<?php

namespace Santakadev\AnyObject\Tests\Generator;

use Santakadev\AnyObject\Generator\FactoryGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;

class FactoryGeneratorTestCase extends AnyObjectTestCase
{
    protected const OUTPUT_DIR = __DIR__ . '/Generated';

    protected function setUp(): void
    {
        self::cleanGeneratedFiles();
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanGeneratedFiles();
    }

    private static function cleanGeneratedFiles(): void
    {
        if (file_exists(__DIR__ . '/Generated')) {
            $files = glob(__DIR__ . '/Generated/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir(__DIR__ . '/Generated');
        }
    }

    protected function readGeneratedAnyFileFor(string $fullyQualifiedClassName): string|false
    {
        return file_get_contents(self::OUTPUT_DIR . $this->getAnyObjectClassName($fullyQualifiedClassName));
    }

    private function getAnyObjectClassName(string $fullyQualifiedClassName): string
    {
        $shortClassName = substr($fullyQualifiedClassName, strrpos($fullyQualifiedClassName, '\\') + 1);

        return "/Any{$shortClassName}.php";
    }

    public function factoryGenerator(): FactoryGenerator
    {
        return new FactoryGenerator('Santakadev\\AnyObject\\Tests\\Generator\\Generated');
    }
}
