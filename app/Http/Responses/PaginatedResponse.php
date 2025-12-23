<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedResponse extends JsonResource
{
    protected string $tableId = 'table_id';
    protected string $searchKey = 'search';
    protected string $sortFieldKey = 'sort_field';
    protected string $sortDirectionKey = 'sort_direction';

    public function toArray($request): array
    {
        return [
            'data' => $this->resource->items(),
            'current_page' => $this->resource->currentPage(),
            'per_page' => $this->resource->perPage(),
            'last_page' => $this->resource->lastPage(),
            'total' => $this->resource->total(),
            'table_id' => $request->input($this->tableId ?? 'table_id', ''),
            'search' => $request->input($this->searchKey ?? 'search', ''),
            'sort_field' => $request->input($this->sortFieldKey ?? 'sort_field', 'created_at'),
            'sort_direction' => $request->input($this->sortDirectionKey ?? 'sort_direction', 'desc'),
            'filter' => $this->resource->filters ?? [],
            'filter_type' => $this->resource->filterType ?? '',
            'filter_by' => $request->input('filter_by')?? '',
            'filter_value' => $request->input('filter_value') ?? '',

        ];
    }

    public function withCustomKeys(string $searchKey, string $sortFieldKey, string $sortDirectionKey): self
    {
        $this->searchKey = $searchKey;
        $this->sortFieldKey = $sortFieldKey;
        $this->sortDirectionKey = $sortDirectionKey;
        return $this;
    }
}
