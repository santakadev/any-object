<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use Exception;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Generator\AutoloadEntries;
use Santakadev\AnyObject\Generator\MirrorOutputResolver;

class MirrorOutputResolverTest extends TestCase
{
    public function test_mirror_output(): void
    {
        $autoload = AutoloadEntries::fromArray([
            'Org\\Package\\' => "src/",
            'Org\\Package\\Tests\\' => "tests/",
        ]);
        $mirror = new MirrorOutputResolver($autoload);

        $class = 'Org\\Package\\Module\\SubModule\\ClassName';
        $this->defineClass($class);
        $output = $mirror->resolve($class);

        $this->assertSame('tests/Module/SubModule', $output->dir);
        $this->assertSame('Org\\Package\\Tests\\Module\\SubModule', $output->namespace);
    }

    public function test_mirror_output_missing_path_slash(): void
    {
        $autoload = AutoloadEntries::fromArray([
            'Org\\Package\\' => "src",
            'Org\\Package\\Tests\\' => "tests",
        ]);
        $mirror = new MirrorOutputResolver($autoload);

        $class = 'Org\\Package\\Module\\SubModule\\ClassName';
        $this->defineClass($class);
        $output = $mirror->resolve($class);

        $this->assertSame('tests/Module/SubModule', $output->dir);
        $this->assertSame('Org\\Package\\Tests\\Module\\SubModule', $output->namespace);
    }

    public function test_mirror_an_enum_should_throw_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Trying to determine the output of an enum');

        $autoload = AutoloadEntries::fromArray([
            'Org\\Package\\' => "src",
            'Org\\Package\\Tests\\' => "tests",
        ]);
        $mirror = new MirrorOutputResolver($autoload);

        $enum = 'Org\\Package\\Module\\SubModule\\InterfaceName';
        $this->defineEnum($enum);
        $mirror->resolve($enum);
    }

    public function test_mirror_an_interface_should_throw_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Trying to determine the output of an interface');

        $autoload = AutoloadEntries::fromArray([
            'Org\\Package\\' => "src",
            'Org\\Package\\Tests\\' => "tests",
        ]);
        $mirror = new MirrorOutputResolver($autoload);

        $enum = 'Org\\Package\\Module\\SubModule\\EnumName';
        $this->defineInterface($enum);
        $mirror->resolve($enum);
    }

    private function defineClass(string $class): void
    {
        if (!class_exists($class)) {
            $lastBackslash = strrpos($class, '\\');
            $namespace = substr($class, 0, $lastBackslash);
            $shortClassName = substr($class, $lastBackslash + 1);

            $class = "
                namespace $namespace;
                class $shortClassName {}
            ";

            eval($class);
        }
    }

    private function defineEnum(string $enum): void
    {
        if (!enum_exists($enum)) {
            $lastBackslash = strrpos($enum, '\\');
            $namespace = substr($enum, 0, $lastBackslash);
            $shortEnumName = substr($enum, $lastBackslash + 1);

            $enum = "
                namespace $namespace;
                enum $shortEnumName {}
            ";

            eval($enum);
        }
    }

    private function defineInterface(string $interface): void
    {
        if (!enum_exists($interface)) {
            $lastBackslash = strrpos($interface, '\\');
            $namespace = substr($interface, 0, $lastBackslash);
            $shortInterfaceName = substr($interface, $lastBackslash + 1);

            $interface = "
                namespace $namespace;
                interface $shortInterfaceName {}
            ";

            eval($interface);
        }
    }
}
