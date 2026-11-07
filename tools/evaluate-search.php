<?php

// Deliberately standalone: never runs against the application's configured database.
use App\Services\Search\CatalogIndex;
use App\Services\Search\ProductSearch;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';
foreach (['APP_ENV' => 'testing', 'APP_KEY' => 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=', 'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => ':memory:', 'DB_URL' => '', 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync'] as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
// Explicitly override any cached application configuration too.
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null,
    'cache.default' => 'array', 'search.ranking' => 'combined']);
DB::purge('sqlite');
if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') {
    throw new RuntimeException('Evaluation may only use in-memory SQLite.');
}
$catalogs = require __DIR__.'/../tests/Fixtures/search-catalogs.php';
$records = [];
foreach ($catalogs as $catalogName => $catalog) {
    // Fresh connection per catalog; no vocabulary/statistics shared across shops.
    DB::purge('sqlite');
    Schema::create('kategori', function (Blueprint $t) {
        $t->id('category_id');
        $t->string('nama_kategori');
    });
    Schema::create('produk', function (Blueprint $t) {
        $t->id('product_id');
        $t->string('nama_produk');
        $t->text('deskripsi');
        $t->unsignedBigInteger('category_id');
        $t->decimal('harga', 18, 2);
        $t->integer('stok');
    });
    (require __DIR__.'/../database/migrations/2026_11_05_000001_create_catalog_search_tables.php')->up();
    DB::table('kategori')->insert(['category_id' => 1, 'nama_kategori' => $catalog['category']]);
    foreach ($catalog['products'] as $id => [$name, $description]) {
        DB::table('produk')->insert(['product_id' => $id, 'nama_produk' => $name, 'deskripsi' => $description, 'category_id' => 1, 'harga' => 50000, 'stok' => 5]);
    }
    app(CatalogIndex::class)->rebuild();
    foreach (['none', 'distance', 'context'] as $mode) {
        foreach ($catalog['test'] as $case) {
            $latencies = [];
            for ($repeat = 0; $repeat < 5; $repeat++) {
                $start = hrtime(true);
                $result = app(ProductSearch::class)->search($case['q'], correction: $mode, perPage: 10);
                $latencies[] = (hrtime(true) - $start) / 1e6;
            }
            $ids = $result['results']->pluck('product_key')->all();
            $top1 = $result['corrected'] ? $result['effective'] : ($result['suggestions'][0] ?? mb_strtolower($case['q']));
            $relevance = $case['relevance'];
            $precision = count(array_filter(array_slice($ids, 0, 5), fn ($id) => ($relevance[$id] ?? 0) > 0)) / 5;
            $dcg = 0;
            foreach (array_slice($ids, 0, 10) as $position => $id) {
                $dcg += (2 ** ($relevance[$id] ?? 0) - 1) / log($position + 2, 2);
            }
            $grades = array_values($relevance);
            rsort($grades);
            $ideal = 0;
            foreach (array_slice($grades, 0, 10) as $position => $grade) {
                $ideal += (2 ** $grade - 1) / log($position + 2, 2);
            }
            $records[] = ['catalog' => $catalogName, 'mode' => $mode, 'query' => $case['q'], 'expected' => $case['expected'] ?? null,
                'top1' => $top1, 'automatic' => $result['corrected'], 'ids' => $ids, 'p5' => $precision,
                'ndcg10' => $ideal ? $dcg / $ideal : null, 'latency_ms' => $latencies];
        }
    }
}
$summary = [];
foreach (['none', 'distance', 'context'] as $mode) {
    $rows = array_values(array_filter($records, fn ($r) => $r['mode'] === $mode));
    $typos = array_values(array_filter($rows, fn ($r) => $r['expected'] !== null));
    $correct = array_values(array_filter($rows, fn ($r) => $r['expected'] === null));
    $rankable = array_values(array_filter($rows, fn ($r) => $r['ndcg10'] !== null));
    $times = array_merge(...array_column($rows, 'latency_ms'));
    sort($times);
    $summary[$mode] = [
        'queries' => count($rows), 'typo_queries' => count($typos),
        'correction_top1' => count(array_filter($typos, fn ($r) => $r['top1'] === $r['expected'])) / count($typos),
        'false_auto_correction' => count(array_filter($correct, fn ($r) => $r['automatic'])) / count($correct),
        'precision_at_5' => array_sum(array_column($rows, 'p5')) / count($rows),
        'ndcg_at_10' => array_sum(array_column($rankable, 'ndcg10')) / count($rankable),
        'p50_ms' => $times[(int) ceil(count($times) * .50) - 1], 'p95_ms' => $times[(int) ceil(count($times) * .95) - 1],
    ];
}
$report = ['dataset' => 'SYNTHETIC: 3 isolated catalogs, 8 products each, 36 test queries; development examples excluded',
    'database' => 'SQLite in memory; not production throughput', 'generated_at' => gmdate(DATE_ATOM),
    'protocol' => 'Top1 counts suggested or automatic corrections. Ranking metrics measure displayed results BEFORE clicking suggestions. P@5 divides by 5 including short/empty result sets; NDCG excludes no-relevant-product queries. Five sequential repetitions per query.',
    'config' => config('search'), 'summary' => $summary, 'records' => $records];
$directory = __DIR__.'/../docs/evaluation';
if (! is_dir($directory)) {
    mkdir($directory, 0777, true);
}
file_put_contents($directory.'/synthetic-results.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
$markdown = "# Evaluasi sintetis pencarian\n\n".$report['dataset']."\n\n".$report['protocol']."\n\n";
$markdown .= "| Mode | Top1 koreksi | Salah koreksi otomatis | P@5 | NDCG@10 | p50 ms | p95 ms |\n|---|---:|---:|---:|---:|---:|---:|\n";
foreach ($summary as $mode => $s) {
    $markdown .= sprintf("| %s | %.3f | %.3f | %.3f | %.3f | %.2f | %.2f |\n", $mode, $s['correction_top1'], $s['false_auto_correction'], $s['precision_at_5'], $s['ndcg_at_10'], $s['p50_ms'], $s['p95_ms']);
}
$markdown .= "\nHasil ini hanya pemeriksaan awal pada dataset kecil buatan pengembang. Bukan bukti keunggulan umum atau akurasi katalog UMKM nyata. Label ditetapkan sebelum eksekusi, bukan dari skor algoritma; belum dinilai penilai independen. Tidak ada penyetelan parameter terhadap hasil test ini.\n\nJalankan ulang: `php tools/evaluate-search.php`.\n";
file_put_contents($directory.'/synthetic-results.md', $markdown);
echo json_encode($summary, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
