<?php

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;

trait AnyObjectDataProvider
{
    public static function anyProvider(): array
    {
        return [
            'build from constructor' => [new AnyObject(true)],
            'build from properties' => [new AnyObject(false)],
        ];
    }
}
