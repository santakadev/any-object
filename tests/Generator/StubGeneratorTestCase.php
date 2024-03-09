<?php

namespace Santakadev\AnyObject\Tests\Generator;

use Santakadev\AnyObject\Tests\AnyObjectTestCase;

class StubGeneratorTestCase extends AnyObjectTestCase
{
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
}
