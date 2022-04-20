<?php

namespace App\Infrastructure\Storage;

interface FileStorageInterface
{
    public function store(string $content, string $name, string $area = ''): string;
    public function delete(string $name, string $area = ''): bool;
    public function get(string $name, string $area = '');
}
