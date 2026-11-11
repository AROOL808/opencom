<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Konfigurasi;
use App\Models\Produk;
use App\Services\Search\ProductSearch;
use App\Services\Search\SearchUnavailable;
use App\Http\Requests\CatalogSearchRequest;
use Illuminate\Pagination\LengthAwarePaginator;

final class SearchController extends Controller
{
    public function __invoke(CatalogSearchRequest $request, ProductSearch $search)
    {
        $data = $request->validated();
        $filters = $request->filters();
        $queryText = trim($data['q'] ?? '');
        $page = (int) ($data['page'] ?? 1);
        $perPage = config('search.per_page', 24);

        try {
            // JIKA KATA KUNCI KOSONG TAPI FILTER ADA: Bypass BM25
            if ($queryText === '' && !empty($filters)) {
                
                $query = Produk::query();

                // 1. Filter Kategori (Sesuai dengan tag name="category" di HTML)
                if (!empty($filters['category'])) {
                    $query->where('category_id', $filters['category']);
                }

                // 2. Filter Harga Minimum
                if (isset($filters['min_price']) && $filters['min_price'] !== '') {
                    $query->where('harga', '>=', $filters['min_price']);
                }

                // 3. Filter Harga Maksimum
                if (isset($filters['max_price']) && $filters['max_price'] !== '') {
                    $query->where('harga', '<=', $filters['max_price']);
                }

                // 4. Filter Stok Tersedia
                if (!empty($filters['in_stock'])) {
                    $query->where('stok', '>', 0);
                }

                // Ambil data (paginated) sesuai filter yang diterapkan
                $paginatedProducts = $query->paginate($perPage, ['*'], 'page', $page);
                
                // Ambil model Produk untuk ditampilkan di View
                $products = $paginatedProducts->keyBy('product_id');

                // MOCKING (Penyamaran) data agar bentuknya SAMA PERSIS dengan BM25
                $mockResults = $paginatedProducts->map(function ($prod) {
                    return (object) ['product_key' => $prod->product_id];
                });

                // Buat paginator tiruan yang membungkus mockResults
                $mockPaginator = new LengthAwarePaginator(
                    $mockResults,
                    $paginatedProducts->total(), // Sekarang pasti akurat (misal: 4 produk)
                    $paginatedProducts->perPage(),
                    $paginatedProducts->currentPage(),
                    ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                );

                $result = [
                    'original' => '',
                    'effective' => '',
                    'corrected' => false,
                    'suggestions' => [],
                    'results' => $mockPaginator,
                    'version' => 0,
                ];

            } else {
                // JIKA KATA KUNCI ADA: Gunakan mesin pencari BM25
                $result = $search->search($queryText, $filters, $request->boolean('original'), $page);
                
                $products = Produk::with('category')
                    ->whereIn('product_id', $result['results']->pluck('product_key'))
                    ->get()
                    ->keyBy('product_id');
            }
            
        } catch (SearchUnavailable) {
            return response()->view('search.unavailable', [], 503)->header('Retry-After', '60');
        }

        // Pertahankan parameter filter (seperti kategori) saat mengklik pagination
        $result['results']->appends(array_diff_key($data, ['page' => true]));

        return view('search.index', [
            'result' => $result, 
            'products' => $products, 
            'filters' => $filters,
            'categories' => Kategori::orderBy('nama_kategori')->get(),
            'store' => Konfigurasi::first(),
        ]);
    }
}