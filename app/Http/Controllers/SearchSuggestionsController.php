<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogSearchRequest;
use App\Models\Produk;
use App\Services\Search\ProductSearch;
use App\Services\Search\SearchUnavailable;
use App\Services\Search\Tokenizer;
use Illuminate\Support\Facades\DB;

final class SearchSuggestionsController extends Controller
{
    public function __invoke(CatalogSearchRequest $request, ProductSearch $search, Tokenizer $tokenizer)
    {
        $query = trim($request->validated('q') ?? '');
        $filters = $request->filters();
        $parameters = array_merge($filters, ['q' => $query]);
        if ($request->boolean('original')) {
            $parameters['original'] = 1;
        }
        $payload = [
            'query' => $query, 'corrections' => [], 'products' => [],
            'total' => 0, 'preview_query' => $query, 'incomplete' => false,
            'results_url' => route('search', $parameters),
        ];
        if (mb_strlen($query) < 3) {
            return response()->json($payload)->header('Cache-Control', 'no-store');
        }
        try {
            $result = $search->search($query, $filters, originalOnly: true, perPage: 5);
            if ($result['results']->total() === 0 && ! $request->boolean('original')) {
                $tokens = $tokenizer->tokens($query, true);
                $last = end($tokens);
                // A known prefix can still be a word being typed. Do not suggest a
                // spelling replacement for it; full submission retains normal IR behavior.
                $prefix = $last && DB::table('search_terms')->where('field', 'all')
                    ->where('term', '<>', $last)
                    ->where('term', 'like', $last.'%')->exists();
                if ($prefix) {
                    $payload['incomplete'] = true;
                } else {
                    $result = $search->search($query, $filters, perPage: 5);
                }
            }
        } catch (SearchUnavailable) {
            return response()->json(['message' => 'Saran sementara tidak tersedia. Tekan Enter untuk mencoba pencarian.'], 503)
                ->header('Cache-Control', 'no-store')->header('Retry-After', '60');
        }
        $payload['corrections'] = array_map(fn ($term) => [
            'label' => $term,
            'url' => route('search', array_merge($filters, ['q' => $term, 'original' => 1])),
        ], array_slice($result['suggestions'], 0, 3));
        $products = Produk::with('category')->whereIn('product_id', $result['results']->pluck('product_key'))->get()->keyBy('product_id');
        foreach ($result['results'] as $hit) {
            if (! $product = $products->get($hit->product_key)) {
                continue;
            }
            $payload['products'][] = [
                'name' => $product->nama_produk,
                'price' => 'Rp '.number_format($product->harga, 0, ',', '.'),
                'category' => $product->category?->nama_kategori,
                'in_stock' => $product->stok > 0,
                'image' => $product->alamat_foto ? asset('storage/products/'.rawurlencode(basename($product->alamat_foto))) : null,
                'url' => route('product.show', $product->product_id),
            ];
        }
        $payload['preview_query'] = $result['effective'];
        $payload['total'] = $result['results']->total();

        return response()->json($payload)->header('Cache-Control', 'no-store');
    }
}
