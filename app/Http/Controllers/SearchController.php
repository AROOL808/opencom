<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Konfigurasi;
use App\Models\Produk;
use App\Services\Search\ProductSearch;
use App\Services\Search\SearchUnavailable;
use Illuminate\Http\Request;

final class SearchController extends Controller
{
    public function __invoke(Request $request, ProductSearch $search)
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
        if ($request->filled('min_price')) {
            $rules['max_price'][] = 'gte:min_price';
        }
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Ini akan menghentikan halaman dari redirect 
            // dan langsung mencetak error di layar (warna hitam)
            dd($validator->errors()->all()); 
        }

        $data = $validator->validated();
        
        $filters = array_intersect_key($data, array_flip(['category', 'min_price', 'max_price', 'in_stock']));
        try {
            $result = $search->search($data['q'] ?? '', $filters, $request->boolean('original'), (int) ($data['page'] ?? 1));
        } catch (SearchUnavailable) {
            return response()->view('search.unavailable', [], 503)->header('Retry-After', '60');
        }
        $products = Produk::with('category')->whereIn('product_id', $result['results']->pluck('product_key'))->get()->keyBy('product_id');
        $result['results']->appends(array_diff_key($data, ['page' => true]));

        return view('search.index', [
            'result' => $result, 'products' => $products, 'filters' => $filters,
            'categories' => Kategori::orderBy('nama_kategori')->get(),
            'store' => Konfigurasi::first(),
        ]);
    }
}
