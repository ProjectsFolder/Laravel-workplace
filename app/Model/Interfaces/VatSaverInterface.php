<?php

namespace App\Model\Interfaces;

use App\Model\Dto\VatData;
use App\Model\Entity\Vat;

interface VatSaverInterface
{
    public function store(VatData $data): Vat;
}
