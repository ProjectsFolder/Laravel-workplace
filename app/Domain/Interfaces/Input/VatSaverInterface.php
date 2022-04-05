<?php

namespace App\Domain\Interfaces\Input;

interface VatSaverInterface
{
    public function saveVat(string $vat): ?string;
}
