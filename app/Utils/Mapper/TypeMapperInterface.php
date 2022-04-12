<?php

namespace App\Utils\Mapper;

interface TypeMapperInterface
{
    public function supports(string $sourceClass, string $targetClass): bool;
    public function convert(object $source): object;
}
