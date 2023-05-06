<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
            'items_per_page' => $this->perPage(),
            'total_items' => $this->total(),
            'items' => $this->items()            
        ];
    }
}
