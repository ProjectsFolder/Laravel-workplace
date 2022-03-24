<?php


namespace App\Utils;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{
    public function toArray(): array
    {
        return [
            'items' => $this->items->toArray(),
            'meta' => [
                'current_page' => $this->currentPage(),
                'first_page_url' => $this->url(1),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'last_page_url' => $this->url($this->lastPage()),
                'next_page_url' => $this->nextPageUrl(),
                'path' => $this->path(),
                'per_page' => $this->perPage(),
                'prev_page_url' => $this->previousPageUrl(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
        ];
    }
}
