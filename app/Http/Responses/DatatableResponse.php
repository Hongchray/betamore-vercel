<?php

namespace App\Http\Responses;

use Illuminate\Pagination\LengthAwarePaginator;

class DatatableResponse
{
    public $data;

    public function __construct(LengthAwarePaginator $data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return [
            'data' => $this->data->items(),
            'current_page' => $this->data->currentPage(),
            'per_page' => $this->data->perPage(),
            'total' => $this->data->total(),
        ];
    }
}
