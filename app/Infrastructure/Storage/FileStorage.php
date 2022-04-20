<?php

namespace App\Infrastructure\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;

class FileStorage implements FileStorageInterface
{
    protected $storage;

    public function __construct(Filesystem $filesystem)
    {
        $this->storage = $filesystem;
    }

    public function store(string $content, string $name, string $area = ''): string
    {
        $path = $this->getPath($name, $area);
        $this->storage->put($path, $content);

        return $path;
    }

    public function delete(string $name, string $area = ''): bool
    {
        $path = $this->getPath($name, $area);

        return $this->storage->delete($path);
    }

    public function get(string $name, string $area = '')
    {
        $path = $this->getPath($name, $area);

        return $this->storage->exists($path) ? $this->storage->readStream($path) : null;
    }

    protected function getPath(string $name, string $area = ''): string
    {
        $fileId = hash('sha256', $area.$name);
        $path = [
            $fileId[0] ?? 'a',
            $fileId[1] ?? 'a',
            $fileId[2] ?? 'a',
            $name,
        ];
        if (!empty($area)) {
            array_unshift($path, $area);
        }

        return implode(DIRECTORY_SEPARATOR, $path);
    }
}
