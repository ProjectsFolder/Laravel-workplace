<?php

namespace App\Domain;

use App\Domain\Interfaces\Output\VatGetterInterface;
use App\Domain\Interfaces\Output\VatSaverInterface;

class VatSaver implements Interfaces\Input\VatSaverInterface
{
    protected $vatGetter;
    protected $vatSaver;

    public function __construct(VatGetterInterface $vatGetter, VatSaverInterface $vatSaver)
    {
        $this->vatGetter = $vatGetter;
        $this->vatSaver = $vatSaver;
    }

    public function saveVat(string $vat): ?string
    {
        $vatData = $this->vatGetter->get($vat);
        if (!empty($vatData) && $vatData->getValid()) {
            return $this->vatSaver->store($vatData);
        }

        return null;
    }
}
