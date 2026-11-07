<?php

namespace App\Services\Search;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

final class ProductSearch
{
    public function __construct(private Tokenizer $tokenizer, private Bm25 $bm25, private SpellingCorrector $spelling) {}

    public function search(string $query, array $filters = [], bool $originalOnly = false, int $page = 1, ?string $correction = null, ?int $perPage = null): array
    {
        if (mb_strlen($query) > config('search.max_query_length')) {
            throw ValidationException::withMessages(['q' => 'Kata pencarian terlalu panjang.']);
        }
        $tokens = $this->tokenizer->tokens($query, true);
        if (count($tokens) > config('search.max_tokens')) {
            throw ValidationException::withMessages(['q' => 'Gunakan paling banyak '.config('search.max_tokens').' kata pencarian.']);
        }
        if (! Schema::hasTable('search_state')) {
            throw new SearchUnavailable('Search tables are not installed.');
        }

        return DB::transaction(function () use ($query, $tokens, $filters, $originalOnly, $page, $correction, $perPage) {
            // Readers see one complete version; no partial update/rebuild and no result cache.
            $state = DB::table('search_state')->where('id', 1)->sharedLock()->first();
            if (! $state || ! $state->ready || $state->index_version !== config('search.index_version')
                || DB::table('search_pending')->exists()) {
                throw new SearchUnavailable('Search index requires rebuild/retry.');
            }
            $original = $this->bm25->query($tokens, $state, $filters);
            // Preserve existing matches. A scarce result is not a spelling error.
            $hasResults = DB::query()->fromSub(clone $original, 'original_results')->exists();
            $suggestion = ['automatic' => null, 'suggestions' => []];
            if (! $originalOnly && ! $hasResults) {
                $suggestion = $this->spelling->suggest($tokens, $state->documents, $correction ?? config('search.correction'));
            }
            $effective = $suggestion['automatic'] ?? implode(' ', $tokens);
            $resultsQuery = $suggestion['automatic'] === null ? $original : $this->bm25->query($this->tokenizer->tokens($effective), $state, $filters);
            // A correction must not automatically replace the query with zero filtered matches.
            if ($suggestion['automatic'] !== null && ! DB::query()->fromSub(clone $resultsQuery, 'corrected_results')->exists()) {
                $suggestion['automatic'] = null;
                $effective = implode(' ', $tokens);
                $resultsQuery = $original;
            }

            return [
                'original' => $query, 'effective' => $effective,
                'corrected' => $suggestion['automatic'] !== null,
                'suggestions' => $originalOnly ? [] : $suggestion['suggestions'],
                'results' => $resultsQuery->paginate(min(100, max(1, $perPage ?? config('search.per_page'))), ['*'], 'page', max(1, $page)),
                'version' => $state->version,
            ];
        });
    }
}
