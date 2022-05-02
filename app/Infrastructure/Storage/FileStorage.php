<?php

namespace App\Infrastructure\Storage;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;

class FileStorage implements FileStorageInterface
{
    protected $storage;
    protected $cache;

    public function __construct(Filesystem $filesystem, Repository $cache)
    {
        $this->storage = $filesystem;
        $this->cache = $cache;
    }

    public function store(string $content, string $name, string $area = ''): string
    {
        $path = $this->getPath($name, $area);
        $this->cache->lock(self::class.$area, 60)->block(30, function () use ($path, $content) {
            $this->storage->put($path, $content);
            sleep(15);
        });

        return $path;
    }

    public function delete(string $name, string $area = ''): bool
    {
        $path = $this->getPath($name, $area);

        return $this->storage->delete($path);
    }

    public function deleteArea(string $area): bool
    {
        return $this->storage->deleteDirectory($area);
    }

    public function get(string $name, string $area = '')
    {
        $path = $this->getPath($name, $area);

        return $this->storage->exists($path) ? $this->storage->readStream($path) : null;
    }

    protected function getPath(string $name, string $area = ''): string
    {
        return $this->cache->remember(self::class.$area.$name, 60 * 10, function () use ($area, $name) {
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
        });
    }
}
