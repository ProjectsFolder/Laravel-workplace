<?php

namespace App\Http\Controllers;

use App\Http\Requests\VatRequest;
use App\Model\Interfaces\VatSaverInterface;
use App\Model\Repository\VatRepository;
use App\Service\Interfaces\VatGetterInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VatController extends Controller
{
    private $vatRepository;

    public function __construct(VatRepository $vatRepository)
    {
        $this->vatRepository = $vatRepository;
    }

    public function check(
        Request $request,
        VatGetterInterface $vatGetter,
        VatSaverInterface $vatSaver
    ): Response {
        $this->validateData($request->all(), ['vat' => 'required|vat']);

        $vatData = $vatGetter->get($request->query('vat'));
        if (!$vatData->getValid()) {
            throw new HttpException(400, 'Vat is not valid');
        }
        $vat = $vatSaver->store($vatData);

        return response()->success($vat);
    }

    public function list(): Response
    {
        $list = $this->vatRepository->list();

        return response()->success($list);
    }

    public function get(int $id): Response
    {
        $vat = $this->vatRepository->get($id);
        if (empty($vat)) {
            throw new HttpException(404, 'Not found');
        }

        return response()->success($vat);
    }

    public function update(int $id, VatRequest $request): Response
    {
        $vat = $this->vatRepository->update($id, $request);
        if (empty($vat)) {
            throw new HttpException(404, 'Not found');
        }

        return response()->success($vat);
    }

    public function delete(int $id): Response
    {
        $deleted = $this->vatRepository->delete($id);
        if (!$deleted) {
            throw new HttpException(404, 'Not found');
        }

        return response()->success();
    }
}
