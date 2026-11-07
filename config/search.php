<?php

return [
    'k1' => 1.2,
    'b' => 0.75,
    'ranking' => 'combined', // combined baseline or weighted; not BM25F.
    'weights' => ['name' => 3.0, 'category' => 2.0, 'description' => 1.0],
    'correction' => 'context', // none, distance, context
    'max_query_length' => 200,
    'max_tokens' => 8,
    'max_token_length' => 64,
    'max_correction_length' => 24,
    'max_candidates' => 5,
    'candidate_pool' => 128,
    'beam_width' => 12,
    'suggestions' => 3,
    'auto_margin' => 0.20,
    'auto_max_distance' => 1,
    'auto_min_similarity' => 0.75,
    'auto_min_token_length' => 4,
    'protected_terms' => [],
    'per_page' => 12,
    // No stemming or stopword removal: units and local names survive.
    'index_version' => 1,
];
