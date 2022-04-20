<?php

namespace App\Http\Controllers;

use App\Domain\Interfaces\Input\VatSaverInterface;
use App\Http\Requests\VatRequest;
use App\Model\Repository\VatRepository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VatController extends Controller
{
    protected $vatRepository;
    protected $translator;

    public function __construct(VatRepository $vatRepository, Translator $translator)
    {
        $this->vatRepository = $vatRepository;
        $this->translator = $translator;
    }

    public function check(
        Request $request,
        VatSaverInterface $vatSaver
    ): Response {
        $this->validateData($request->all(), ['vat' => 'required|vat']);

        $id = $vatSaver->saveVat($request->query('vat'));
        if (empty($id)) {
            throw new HttpException(400, $this->translator->get('messages.vat.invalid'));
        }

        return response()->success($this->vatRepository->get($id));
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
            throw new HttpException(404, $this->translator->get('messages.not_found'));
        }

        return response()->success($vat);
    }

    public function update(int $id, VatRequest $request): Response
    {
        $vat = $this->vatRepository->update($id, $request);
        if (empty($vat)) {
            throw new HttpException(404, $this->translator->get('messages.not_found'));
        }

        return response()->success($vat);
    }

    public function delete(int $id): Response
    {
        $deleted = $this->vatRepository->delete($id);
        if (!$deleted) {
            throw new HttpException(404, $this->translator->get('messages.not_found'));
        }

        return response()->success();
    }
}
