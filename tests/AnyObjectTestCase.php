<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use PHPUnit\Framework\TestCase;

class AnyObjectTestCase extends TestCase
{
    use AnyObjectDataProvider;

    /**
     * Keeps calling $callable until all $assertions are true.
     *
     * This is an alternative to:
     *  - mocking the random generator
     *  - asserting one or the other is true and relying on multiple runs
     *
     *  Cons:
     *  - It could be slow in some circumstances
     *  - It could be false positive if the random generator doesn't generate the value you need
     *
     *  Good use cases:
     *  - Few assertions with similar probability: is_string (50%) or is_null (50%)
     *
     *  Bad use cases:
     *  - Low probability of a single assertion: is_string (99%) or is_null (1%)
     *
     * @param callable $callable
     * @param callable[] $assertions
     * @param int $maxIterations
     * @return void
     */
    protected function assertAll(callable $callable, array $assertions, int $maxIterations = 100): void
    {
        $seen = array_fill(0, count($assertions), false);
        $iterations = 0;

        while (in_array(false, $seen) && $iterations < $maxIterations) {
            $value = $callable();
            foreach ($assertions as $index => $assertion) {
                if (!$seen[$index] && $assertion($value)) {
                    $this->assertTrue($assertion($value));
                    $seen[$index] = true;
                }
            }

            $iterations++;
        }

        if (in_array(false, $seen)) {
            // TODO: display all failed assertions
            $firstFail = array_search(false, $seen);
            $assertion = $assertions[$firstFail];
            $this->assertTrue($assertion($callable()), "Assertion '$assertion' never matched."); // TODO: assertion could be a closure, that could not be able to convert to string
        }
    }
}
