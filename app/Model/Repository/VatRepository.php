<?php

namespace App\Model\Repository;

use App\Domain\Entity\Vat\VatData;
use App\Domain\Interfaces\Output\VatSaverInterface;
use App\Http\Requests\VatRequest;
use App\Model\Entity\Vat;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VatRepository implements VatSaverInterface
{
    protected $guard;

    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    public function store(VatData $data): int
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
        $vat->user()->associate($this->guard->user());
        $vat->save();

        return $vat->id;
    }

    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Vat::query()->paginate($perPage);
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
