<?php

namespace App\Model\Repository;

use App\Domain\Entity\Vat\VatData;
use App\Domain\Interfaces\Output\VatSaverInterface;
use App\Http\Requests\VatRequest;
use App\Model\Entity\Vat;
use App\Utils\Mapper\TypeMapper;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VatRepository implements VatSaverInterface
{
    protected $guard;
    protected $typeMapper;

    public function __construct(Guard $guard, TypeMapper $typeMapper)
    {
        $this->guard = $guard;
        $this->typeMapper = $typeMapper;
    }

    public function store(VatData $data): int
    {
        $vat = $this->typeMapper->convert($data, Vat::class);
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
