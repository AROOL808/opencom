<?php

namespace App\Services\Search;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

final class CatalogIndex
{
    public const FIELDS = ['all', 'name', 'category', 'description'];

    public function __construct(private Tokenizer $tokenizer) {}

    /** Called inside the catalog mutation transaction; work survives a crash. */
    public function changed(string $kind, string $key): void
    {
        // Before installation, existing catalog editing must continue to work.
        if (! Schema::hasTable('search_pending')) {
            return;
        }
        DB::transaction(function () use ($kind, $key) {
            DB::table('search_state')->where('id', 1)->lockForUpdate()->first();
            DB::table('search_pending')->insertOrIgnore(['kind' => $kind, 'entity_key' => $key]);
        });
        DB::afterCommit(function () {
            try {
                $this->retry();
            } catch (\Throwable $e) {
                // Do not expose SQL bindings or catalog/customer data in logs.
                Log::error('Catalog search synchronization pending; run search:retry.', ['exception_type' => $e::class]);
            }
        });
    }

    public function rebuild(): int
    {
        return DB::transaction(function () {
            $state = DB::table('search_state')->where('id', 1)->lockForUpdate()->first();
            foreach (['search_deletes', 'search_postings', 'search_terms', 'search_documents'] as $table) {
                DB::table($table)->delete();
            }
            $reset = ['documents' => 0];
            foreach (self::FIELDS as $field) {
                $reset['total_'.$field] = 0;
            }
            DB::table('search_state')->where('id', 1)->update($reset);
            DB::table('produk')->orderBy('product_id')->chunk(100, function ($products) {
                foreach ($products as $product) {
                    $this->replace((string) $product->product_id);
                }
            });
            DB::table('search_pending')->delete();
            DB::table('search_state')->where('id', 1)->update([
                'ready' => true,
                'version' => $state->version + 1,
                'index_version' => config('search.index_version'),
            ]);

            return (int) DB::table('search_state')->where('id', 1)->value('documents');
        });
    }

    public function retry(): int
    {
        return DB::transaction(function () {
            $state = DB::table('search_state')->where('id', 1)->lockForUpdate()->first();
            if (! $state || ! $state->ready) {
                return 0; // Initial population requires an explicit full rebuild.
            }
            $pending = DB::table('search_pending')->get();
            foreach ($pending as $entry) {
                if ($entry->kind === 'product') {
                    $this->replace($entry->entity_key);
                } else {
                    // Include stale indexed products when category deletion cascaded.
                    $keys = DB::table('produk')->where('category_id', $entry->entity_key)->pluck('product_id')
                        ->merge(DB::table('search_documents')->where('category_key', $entry->entity_key)->pluck('product_key'))->unique();
                    foreach ($keys as $key) {
                        $this->replace((string) $key);
                    }
                }
                DB::table('search_pending')->where('kind', $entry->kind)->where('entity_key', $entry->entity_key)->delete();
            }
            if ($pending->isNotEmpty()) {
                DB::table('search_state')->where('id', 1)->increment('version');
            }

            return $pending->count();
        });
    }

    private function replace(string $key): void
    {
        $old = DB::table('search_documents')->where('product_key', $key)->first();
        $delta = array_fill_keys(array_map(fn ($f) => 'total_'.$f, self::FIELDS), 0);
        $documentDelta = 0;
        if ($old) {
            $ids = DB::table('search_postings')->where('product_key', $key)->pluck('term_id');
            DB::table('search_documents')->where('product_key', $key)->delete();
            DB::table('search_terms')->whereIn('id', $ids)->decrement('df');
            DB::table('search_terms')->whereIn('id', $ids)->where('df', 0)->delete();
            $documentDelta--;
            foreach (self::FIELDS as $field) {
                $delta['total_'.$field] -= $old->{'length_'.$field};
            }
        }
        $product = DB::table('produk')->where('product_id', $key)->first();
        if ($product) {
            $category = DB::table('kategori')->where('category_id', $product->category_id)->value('nama_kategori');
            $tokens = [
                'name' => $this->tokenizer->tokens($product->nama_produk),
                'category' => $this->tokenizer->tokens($category),
                'description' => $this->tokenizer->tokens($product->deskripsi),
            ];
            $tokens['all'] = array_merge(...array_values($tokens));
            $document = ['product_key' => $key, 'category_key' => $product->category_id === null ? null : (string) $product->category_id,
                'price' => $product->harga, 'stock' => $product->stok];
            foreach ($tokens as $field => $words) {
                $document['length_'.$field] = count($words);
                $delta['total_'.$field] += count($words);
            }
            DB::table('search_documents')->insert($document);
            $documentDelta++;
            foreach ($tokens as $field => $words) {
                foreach (array_count_values($words) as $word => $tf) {
                    $word = (string) $word;
                    $term = DB::table('search_terms')->where('field', $field)->where('term', $word)->first();
                    if ($term) {
                        $id = $term->id;
                        DB::table('search_terms')->where('id', $id)->increment('df');
                    } else {
                        $id = DB::table('search_terms')->insertGetId(['field' => $field, 'term' => $word, 'df' => 1]);
                        if ($field === 'all' && $this->tokenizer->maxDistance($word) > 0) {
                            $rows = array_map(fn ($s) => ['signature' => $s, 'term_id' => $id], $this->tokenizer->signatures($word, 2));
                            foreach (array_chunk($rows, 200) as $chunk) {
                                DB::table('search_deletes')->insertOrIgnore($chunk);
                            }
                        }
                    }
                    DB::table('search_postings')->insert(['term_id' => $id, 'product_key' => $key, 'tf' => $tf]);
                }
            }
        }
        // A global state row lock serializes writers and keeps aggregate statistics exact.
        $state = DB::table('search_state')->where('id', 1)->first();
        $update = ['documents' => $state->documents + $documentDelta];
        foreach ($delta as $column => $value) {
            $update[$column] = $state->$column + $value;
        }
        DB::table('search_state')->where('id', 1)->update($update);
    }
}
