<?php

namespace App\Utils\Mapper;

use Exception;

class TypeMapper
{
    private $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @param $source
     * @param string $targetClass
     *
     * @return object
     *
     * @throws Exception
     */
    public function convert($source, string $targetClass): object
    {
        if (false == class_exists($targetClass)) {
            throw new Exception('Class ' . $targetClass . ' not exists');
        }

        $sourceClass = get_class($source);
        /** @var TypeMapperInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($sourceClass, $targetClass)) {
                return $strategy->convert($source);
            }
        }

        throw new Exception('Converter from ' . $sourceClass . ' to ' . $targetClass . ' not found');
    }
}
