<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use Santakadev\AnyObject\Generator\FactoryGenerator;
use Santakadev\AnyObject\Generator\FixedOutputResolver;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;

class FactoryGeneratorTestCase extends AnyObjectTestCase
{
    protected const OUTPUT_DIR = __DIR__ . '/Generated';
    const OUTPUT_NAMESPACE = 'Santakadev\\AnyObject\\Tests\\Generator\\Generated';

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

    protected function generateFactoryFor(string $class): void
    {
        $generator = new FactoryGenerator();
        $generator->generate(
            $class,
            new FixedOutputResolver(self::OUTPUT_DIR, self::OUTPUT_NAMESPACE),
        );
    }

}
