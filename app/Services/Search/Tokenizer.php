<?php

namespace App\Services\Search;

use Illuminate\Validation\ValidationException;

final class Tokenizer
{
    public function tokens(?string $text, bool $strict = false): array
    {
        $text = preg_replace('/<[^>]*>/u', ' ', $text ?? '') ?? '';
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (class_exists(\Normalizer::class)) {
            $text = \Normalizer::normalize($text, \Normalizer::FORM_KC) ?: $text;
        }
        $text = mb_strtolower($text, 'UTF-8');
        preg_match_all('/[\p{L}\p{N}]+(?:[.\-\x{2019}\x{0027}][\p{L}\p{N}]+)*/u', $text, $matches);
        if ($strict && count(array_filter($matches[0], fn ($term) => mb_strlen($term) > config('search.max_token_length', 64)))) {
            throw ValidationException::withMessages(['q' => 'Salah satu kata pencarian terlalu panjang.']);
        }

        return array_values(array_filter($matches[0], fn ($term) => mb_strlen($term) <= config('search.max_token_length', 64)));
    }

    public function maxDistance(string $term): int
    {
        $length = mb_strlen($term);
        if ($length < 3 || $length > config('search.max_correction_length', 24)
            || ! preg_match('/^\p{L}+$/u', $term)
            || in_array($term, config('search.protected_terms', []), true)) {
            return 0;
        }

        return $length >= 7 ? 2 : 1;
    }

    /** Symmetric-delete candidate index; acceptance always uses full DL. */
    public function signatures(string $term, int $distance): array
    {
        $seen = [$term => true];
        $frontier = [$term];
        for ($depth = 0; $depth < $distance; $depth++) {
            $next = [];
            foreach ($frontier as $word) {
                $chars = mb_str_split($word);
                foreach (array_keys($chars) as $i) {
                    $copy = $chars;
                    array_splice($copy, $i, 1);
                    $deleted = implode('', $copy);
                    if ($deleted !== '' && ! isset($seen[$deleted])) {
                        $seen[$deleted] = true;
                        $next[] = $deleted;
                    }
                }
            }
            $frontier = $next;
        }

        return array_keys($seen);
    }
}
