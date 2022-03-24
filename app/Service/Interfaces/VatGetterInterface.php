<?php

namespace App\Service\Interfaces;

use App\Model\Dto\VatData;

interface VatGetterInterface
{
    public function get(string $vat): VatData;
}
