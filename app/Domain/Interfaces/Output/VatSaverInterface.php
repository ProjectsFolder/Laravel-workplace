<?php

namespace App\Domain\Interfaces\Output;

use App\Domain\Entity\Vat\VatData;

interface VatSaverInterface
{
    public function store(VatData $data): int;
}
