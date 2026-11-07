<?php

namespace App\Services\Search;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class Bm25
{
    public function query(array $tokens, object $state, array $filters = [], ?string $mode = null): Builder
    {
        $tokens = array_values(array_unique($tokens));
        $query = DB::table('search_documents as d');
        foreach (['category' => 'd.category_key', 'min_price' => 'd.price', 'max_price' => 'd.price'] as $key => $column) {
            if (isset($filters[$key]) && $filters[$key] !== '') {
                $query->where($column, match ($key) {
                    'min_price' => '>=', 'max_price' => '<=', default => '='
                }, $filters[$key]);
            }
        }
        if (! empty($filters['in_stock'])) {
            $query->where('d.stock', '>', 0);
        }
        if ($tokens === [] || $state->documents === 0) {
            return $query->selectRaw('d.product_key, 0.0 as score')->whereRaw('1 = 0');
        }
        $fields = ($mode ?? config('search.ranking')) === 'weighted' ? config('search.weights') : ['all' => 1.0];
        $query->join('search_postings as p', 'p.product_key', '=', 'd.product_key')
            ->join('search_terms as t', 't.id', '=', 'p.term_id')
            ->whereIn('t.term', $tokens)->whereIn('t.field', array_keys($fields));
        $k1 = (float) config('search.k1');
        $b = (float) config('search.b');
        if ($k1 <= 0 || $b < 0 || $b > 1) {
            throw new \InvalidArgumentException('BM25 requires k1 > 0 and 0 <= b <= 1.');
        }
        $parts = [];
        $bindings = [];
        // Compute IDF only for the bounded query vocabulary. SQLite builds may lack LN().
        $idfParts = [];
        $idfBindings = [];
        foreach (DB::table('search_terms')->whereIn('term', $tokens)->whereIn('field', array_keys($fields))->get() as $term) {
            $idfParts[] = 'WHEN t.id = ? THEN CAST(? AS DOUBLE PRECISION)';
            array_push($idfBindings, $term->id, log(1 + ($state->documents - $term->df + 0.5) / ($term->df + 0.5)));
        }
        $idfSql = $idfParts === [] ? '0.0' : 'CASE '.implode(' ', $idfParts).' ELSE 0.0 END';
        foreach ($fields as $field => $weight) {
            if (! in_array($field, CatalogIndex::FIELDS, true) || ! is_numeric($weight) || $weight < 0) {
                throw new \InvalidArgumentException('Invalid BM25 field/weight.');
            }
            $average = max(1e-9, $state->{'total_'.$field} / $state->documents);
            // Explicit numeric casts also avoid ambiguous parameter types in PostgreSQL.
            $parts[] = "WHEN t.field = ? THEN CAST(? AS DOUBLE PRECISION) * (p.tf * CAST(? AS DOUBLE PRECISION)) / (p.tf + CAST(? AS DOUBLE PRECISION) * (1.0 - CAST(? AS DOUBLE PRECISION) + CAST(? AS DOUBLE PRECISION) * d.length_{$field} / CAST(? AS DOUBLE PRECISION)))";
            array_push($bindings, $field, (float) $weight, $k1 + 1, $k1, $b, $b, $average);
        }

        return $query->select('d.product_key')
            ->selectRaw('SUM(('.$idfSql.') * (CASE '.implode(' ', $parts).' ELSE 0 END)) as score', array_merge($idfBindings, $bindings))
            ->groupBy('d.product_key')
            // AND semantics: do not silently ignore an unknown or unavailable attribute.
            ->havingRaw('COUNT(DISTINCT t.term) = ?', [count($tokens)])
            ->orderByDesc('score')->orderBy('d.product_key');
    }
}
