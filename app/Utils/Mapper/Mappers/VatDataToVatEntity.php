<?php

namespace App\Utils\Mapper\Mappers;

use App\Domain\Entity\Vat\VatData;
use App\Model\Entity\Vat;
use App\Utils\Mapper\TypeMapperInterface;
use Illuminate\Contracts\Auth\Guard;

class VatDataToVatEntity implements TypeMapperInterface
{
    protected $guard;

    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    public function supports(string $sourceClass, string $targetClass): bool
    {
        return $sourceClass == VatData::class && $targetClass == Vat::class;
    }

    public function convert(object $source): object
    {
        /** @var VatData $source */
        $entity = new Vat();
        $entity->fill([
            'country_code' => $source->getCountryCode(),
            'vat_number' => $source->getVatNumber(),
            'request_date' => $source->getRequestDate(),
            'valid' => $source->getValid(),
            'name' => $source->getName(),
            'address' => $source->getAddress(),
        ]);
        $entity->user()->associate($this->guard->user());

        return $entity;
    }
}
