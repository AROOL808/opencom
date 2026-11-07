<?php

namespace App\Services\Search;

use Illuminate\Support\Facades\DB;

final class SpellingCorrector
{
    public function __construct(private Tokenizer $tokenizer, private DamerauLevenshtein $distance) {}

    public function suggest(array $tokens, int $documents, string $mode): array
    {
        $empty = ['automatic' => null, 'suggestions' => []];
        if ($mode === 'none' || $tokens === [] || $documents === 0) {
            return $empty;
        }
        $known = DB::table('search_terms')->where('field', 'all')->whereIn('term', $tokens)->pluck('term')->all();
        $beam = [['tokens' => [], 'score' => 0.0, 'changes' => []]];
        $truncated = false;
        foreach ($tokens as $word) {
            $options = [['term' => $word, 'score' => 0, 'distance' => 0, 'similarity' => 1, 'primary' => true]];
            if (! in_array($word, $known, true) && $this->tokenizer->maxDistance($word) > 0) {
                [$candidates, $limited] = $this->candidates($word, $documents);
                $truncated = $truncated || $limited;
                $options = array_merge($candidates, $options);
            }
            $expanded = [];
            foreach ($beam as $variant) {
                foreach ($options as $option) {
                    $copy = $variant;
                    $copy['tokens'][] = $option['term'];
                    $copy['score'] += $option['score'];
                    if ($option['term'] !== $word) {
                        $copy['changes'][] = $option;
                    }
                    $expanded[] = $copy;
                }
            }
            usort($expanded, fn ($a, $b) => ($b['score'] <=> $a['score']) ?: strcmp(implode(' ', $a['tokens']), implode(' ', $b['tokens'])));
            $truncated = $truncated || count($expanded) > config('search.beam_width');
            $beam = array_slice($expanded, 0, config('search.beam_width'));
        }
        $variants = [];
        foreach ($beam as $variant) {
            if ($variant['changes'] === []) {
                continue;
            }
            $support = $this->support($variant['tokens']);
            if ($mode === 'context' && $support === 0) {
                continue;
            }
            if ($mode === 'context') {
                $variant['score'] += 1.0 + min(0.25, log(1 + $support) / 10);
                $variant['score'] += $this->support($variant['tokens'], true) > 0 ? 0.5 : 0;
            }
            $variant['support'] = $support;
            $variant['query'] = implode(' ', $variant['tokens']);
            $variants[$variant['query']] = $variant;
        }
        $variants = array_values($variants);
        usort($variants, fn ($a, $b) => ($b['score'] <=> $a['score']) ?: strcmp($a['query'], $b['query']));
        if ($variants === []) {
            return $empty;
        }
        $best = $variants[0];
        $margin = isset($variants[1]) ? $best['score'] - $variants[1]['score'] : INF;
        $automatic = ! $truncated && $best['support'] > 0 && $margin >= config('search.auto_margin');
        foreach ($best['changes'] as $change) {
            $automatic = $automatic && $change['distance'] <= config('search.auto_max_distance')
                && $change['similarity'] >= config('search.auto_min_similarity') && $change['primary']
                && $change['input_length'] >= config('search.auto_min_token_length');
        }

        return [
            'automatic' => $automatic ? $best['query'] : null,
            'suggestions' => array_column(array_slice($variants, 0, config('search.suggestions')), 'query'),
        ];
    }

    private function candidates(string $word, int $documents): array
    {
        $max = $this->tokenizer->maxDistance($word);
        $pool = config('search.candidate_pool');
        $rows = DB::table('search_terms as t')
            ->whereIn('t.id', DB::table('search_deletes')->select('term_id')->whereIn('signature', $this->tokenizer->signatures($word, $max)))
            ->orderByDesc('t.df')->orderBy('t.term')->limit($pool + 1)->get(['t.term', 't.df']);
        $limited = $rows->count() > $pool;
        $rows = $rows->take($pool);
        $primary = DB::table('search_terms')->whereIn('field', ['name', 'category'])->whereIn('term', $rows->pluck('term'))->pluck('term')->all();
        $candidates = [];
        foreach ($rows as $row) {
            if (in_array($row->term, config('search.protected_terms', []), true)) {
                continue;
            }
            $distance = $this->distance->distance($word, $row->term);
            if ($distance > $max) {
                continue;
            }
            $similarity = 1 - $distance / max(mb_strlen($word), mb_strlen($row->term));
            $isPrimary = in_array($row->term, $primary, true);
            $candidates[] = ['term' => $row->term, 'distance' => $distance, 'similarity' => $similarity, 'primary' => $isPrimary, 'input_length' => mb_strlen($word),
                'score' => $similarity + 0.10 * log(1 + $row->df) / log(1 + $documents) + ($isPrimary ? 0.10 : 0)];
        }
        usort($candidates, fn ($a, $b) => ($b['score'] <=> $a['score']) ?: strcmp($a['term'], $b['term']));

        return [array_slice($candidates, 0, config('search.max_candidates')), $limited || count($candidates) > config('search.max_candidates')];
    }

    private function support(array $tokens, bool $primaryOnly = false): int
    {
        $tokens = array_unique($tokens);
        $query = DB::table('search_postings as p')->join('search_terms as t', 't.id', '=', 'p.term_id')
            ->whereIn('t.term', $tokens)->whereIn('t.field', $primaryOnly ? ['name', 'category'] : ['all'])
            ->select('p.product_key')->groupBy('p.product_key')->havingRaw('COUNT(DISTINCT t.term) = ?', [count($tokens)]);

        return DB::query()->fromSub($query, 'matches')->count();
    }
}
