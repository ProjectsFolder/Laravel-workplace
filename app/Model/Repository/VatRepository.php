<?php

namespace App\Model\Repository;

use App\Http\Requests\VatRequest;
use App\Model\Dto\VatData;
use App\Model\Entity\Vat;
use App\Model\Interfaces\VatSaverInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VatRepository implements VatSaverInterface
{
    public function store(VatData $data): Vat
    {
        $vat = new Vat();
        $vat->fill([
            'country_code' => $data->getCountryCode(),
            'vat_number' => $data->getVatNumber(),
            'request_date' => $data->getRequestDate(),
            'valid' => $data->getValid(),
            'name' => $data->getName(),
            'address' => $data->getAddress(),
        ]);
        $vat->save();

        return $vat;
    }

    public function list(int $perPage = 10): LengthAwarePaginator
    {
        $pager = Vat::query()->paginate($perPage);

        return $pager;
    }

    public function get(int $id): ?Vat
    {
        /** @var Vat $vat */
        $vat = Vat::query()->find($id);

        return $vat;
    }

    public function update(int $id, VatRequest $data): ?Vat
    {
        /** @var Vat $vat */
        $vat = Vat::query()->find($id);
        if (empty($vat)) {
            return null;
        }

        $validated = $data->validated();
        $vat->update($validated);

        return $vat;
    }

    public function delete(int $id): bool
    {
        /** @var Vat $vat */
        $vat = Vat::query()->find($id);
        if (empty($vat)) {
            return false;
        }
        $vat->delete();

        return true;
    }
}
