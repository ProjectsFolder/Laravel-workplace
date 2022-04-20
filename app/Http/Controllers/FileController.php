<?php

namespace App\Http\Controllers;

use App\Infrastructure\Storage\FileStorageInterface;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FileController extends Controller
{
    protected $storage;
    protected $translator;

    public function __construct(FileStorageInterface $storage, Translator $translator)
    {
        $this->storage = $storage;
        $this->translator = $translator;
    }

    public function upload(Request $request): Response
    {
        $this->validateData($request->all(), ['file' => 'required']);
        $file = $request->file('file');
        $path = $this->storage->store($file->get(), $file->getClientOriginalName(), 'uploads');

        return \response()->success(['path' => $path]);
    }

    public function delete(Request $request): Response
    {
        $this->validateData($request->all(), ['file' => 'required']);
        $fileName = $request->get('file');
        $success = $this->storage->delete($fileName, 'uploads');
        if ($success) {
            return \response()->success();
        }

        throw new HttpException(404, $this->translator->get('messages.not_found'));
    }

    public function download(Request $request): Response
    {
        $this->validateData($request->all(), ['file' => 'required']);
        $fileName = $request->get('file');
        $resource = $this->storage->get($fileName, 'uploads');
        if ($resource) {
            //return Storage::download($path);
            return \response()->attachment($resource, $fileName);
        }

        throw new HttpException(404, $this->translator->get('messages.not_found'));
    }
}
