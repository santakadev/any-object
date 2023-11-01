<?php

namespace Santakadev\AnyObject\Generator;

use ReflectionClass;
use Santakadev\AnyObject\Parser\Parser;

class StubGenerator
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function generate(string $class): string
    {
        $root = $this->parser->parseThroughConstructor($class);

        $reflectionClass = new ReflectionClass($root->type->class);
        $name = $reflectionClass->getShortName();
        $namespace = $reflectionClass->getNamespaceName();
        $stubName = $name . 'Factory';
        $currentNamespace = __NAMESPACE__;

        $file = "<?php

namespace $currentNamespace\Tests\Generated;

use Faker\Factory;
use $namespace\\$name;

final class $stubName
{
    public function with(string|ValueNotProvided \$value = new ValueNotProvided): $name
    {
        if (\$value instanceof ValueNotProvided) {
            \$value = (Factory::create())->text();
        }

        return new StringObject(\$value);
    }

    public function build(): $name
    {
        return self::with();
    }
}
";
        if (!is_dir(__DIR__ . "/../tests/Generated/")) {
            mkdir(__DIR__ . "/../tests/Generated/");
        }

        file_put_contents(__DIR__ . "/../tests/Generated/$stubName.php", $file);

        $this->generateAnyFile();
        $this->generateValueNotProvidedFile();

        return $file;
    }

    public function generateAnyFile(): void
    {
        $any = '<?php

namespace Santakadev\AnyObject\Tests\Generated;

use Santakadev\AnyObject\Tests\Generator\Generated\StringObjectFactory;use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class Any
{
    public static function of(string $class): StringObjectFactory
    {
        return match ($class) {
            StringObject::class => new StringObjectFactory()
        };
    }
}
';

        file_put_contents(__DIR__ . "/../tests/Generated/Any.php", $any);
    }

    public function generateValueNotProvidedFile(): void
    {
        $any = '<?php

namespace Santakadev\AnyObject\Tests\Generated;

class ValueNotProvided
{
}
';

        file_put_contents(__DIR__ . "/../tests/Generated/ValueNotProvided.php", $any);
    }
}
