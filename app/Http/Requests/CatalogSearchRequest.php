<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CatalogSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'q' => ['nullable', 'string', 'max:'.config('search.max_query_length')],
            'category' => ['nullable', 'string', 'max:64'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'in_stock' => ['sometimes', 'boolean'],
            'original' => ['sometimes', 'boolean'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:10000'],
        ];
        if ($this->filled('min_price')) {
            $rules['max_price'][] = 'gte:min_price';
        }

        return $rules;
    }

    public function filters(): array
    {
        return array_intersect_key($this->validated(), array_flip(['category', 'min_price', 'max_price', 'in_stock']));
    }
}
