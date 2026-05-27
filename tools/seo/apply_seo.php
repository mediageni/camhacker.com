<?php
// CLI tool: merge SEO fields (h1, meta_title, meta_description) into webcams.json in place, by id.
// Preserves all other fields (incl. live view_count). Idempotent / resumable.
// Usage:  php apply_seo.php /path/to/data/webcams.json /path/to/batch.json
// Batch JSON: [ {"id":N,"h1":"...","meta_title":"...","meta_description":"..."}, ... ]
if (php_sapi_name() !== 'cli') { http_response_code(403); exit('CLI only'); }

$dataFile  = $argv[1] ?? '';
$batchFile = $argv[2] ?? '';
$d = json_decode(@file_get_contents($dataFile), true);
$batch = json_decode(@file_get_contents($batchFile), true);
if (!is_array($d) || !is_array($batch)) { fwrite(STDERR, "bad input\n"); exit(1); }

$map = [];
foreach ($batch as $b) { $map[(int)$b['id']] = $b; }

$applied = 0;
foreach ($d as &$c) {
    $id = (int)($c['id'] ?? 0);
    if (isset($map[$id])) {
        $c['h1']               = $map[$id]['h1'];
        $c['meta_title']       = $map[$id]['meta_title'];
        $c['meta_description'] = $map[$id]['meta_description'];
        $applied++;
    }
}
unset($c);

file_put_contents($dataFile, json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);

$done = 0;
foreach ($d as $c) { if (!empty($c['meta_title'])) $done++; }
echo "applied $applied of " . count($batch) . "; total with SEO copy: $done / " . count($d) . "\n";
