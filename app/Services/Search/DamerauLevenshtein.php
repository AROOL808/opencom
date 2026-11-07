<?php

namespace App\Services\Search;

/** Unrestricted Damerau–Levenshtein, unit costs, Unicode code points. Not OSA. */
final class DamerauLevenshtein
{
    public function distance(string $left, string $right): int
    {
        $a = mb_str_split($left);
        $b = mb_str_split($right);
        $n = count($a);
        $m = count($b);
        $limit = $n + $m;
        $d = array_fill(0, $n + 2, array_fill(0, $m + 2, 0));
        $d[0][0] = $limit;
        for ($i = 0; $i <= $n; $i++) {
            $d[$i + 1][0] = $limit;
            $d[$i + 1][1] = $i;
        }
        for ($j = 0; $j <= $m; $j++) {
            $d[0][$j + 1] = $limit;
            $d[1][$j + 1] = $j;
        }
        $last = [];
        for ($i = 1; $i <= $n; $i++) {
            $matchedColumn = 0;
            for ($j = 1; $j <= $m; $j++) {
                $i1 = $last[$b[$j - 1]] ?? 0;
                $j1 = $matchedColumn;
                $cost = 1;
                if ($a[$i - 1] === $b[$j - 1]) {
                    $cost = 0;
                    $matchedColumn = $j;
                }
                $d[$i + 1][$j + 1] = min(
                    $d[$i][$j] + $cost,
                    $d[$i + 1][$j] + 1,
                    $d[$i][$j + 1] + 1,
                    $d[$i1][$j1] + ($i - $i1 - 1) + 1 + ($j - $j1 - 1),
                );
            }
            $last[$a[$i - 1]] = $i;
        }

        return $d[$n + 1][$m + 1];
    }
}
