<?php

namespace Santakadev\AnyObject\RandomGenerator;

interface RandomGenerator
{
    public function string(): string;

    public function int(): int;

    public function float(): float;

    public function bool(): bool;

    public function array(): bool;
}
