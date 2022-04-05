<?php

namespace App\Domain\Interfaces\Output;

use App\Domain\Entity\Vat\VatData;

interface VatGetterInterface
{
    public function get(string $vat): ?VatData;
}
