<?php
// CLI tool: pick N cams that are in daylight now + bright enough to analyse, diverse by country,
// skipping cams that already have SEO copy. Downloads chosen frames to /tmp/sample_<id>.jpg.
// Usage:  php pick_cams.php /path/to/data/webcams.json 12
// Output (stdout): id|city|state|country|brightness|url   (one per line)
if (php_sapi_name() !== 'cli') { http_response_code(403); exit('CLI only'); }

$file = $argv[1] ?? '';
$want = (int)($argv[2] ?? 10);
$d = json_decode(@file_get_contents($file), true);
if (!is_array($d)) { fwrite(STDERR, "bad input\n"); exit(1); }
$now = time();

// Known mislabeled cams (view does not match stored location) — need a geo data fix, skip for now.
$skip = [123 => 1, 125 => 1, 142 => 1, 250 => 1, 3065 => 1];

$cand = [];
foreach ($d as $c) {
    if (empty($c['image_url_full'])) continue;
    if (!empty($c['meta_title'])) continue;                 // skip already-processed
    if (isset($skip[(int)$c['id']])) continue;              // skip known bad-geo cams
    if (!preg_match('/^([+-]?)(\d+):/', $c['timezone'] ?? '', $m)) continue;
    $off = ($m[1] === '-' ? -1 : 1) * (int)$m[2];
    $lh = (int)gmdate('G', $now + $off * 3600);
    if ($lh >= 8 && $lh <= 18) { $cand[] = $c; }            // daylight window
}
shuffle($cand);

function brightness_bytes($url, $w, $h) {
    $t = 'https://wsrv.nl/?url=' . urlencode($url) . "&w=$w&h=$h&fit=cover&output=jpg";
    $ctx = stream_context_create(['http' => ['timeout' => 7, 'header' => "User-Agent: s/1.0\r\n"]]);
    $b = @file_get_contents($t, false, $ctx);
    if ($b === false || strncmp($b, "\xFF\xD8\xFF", 3) !== 0) return [null, -1];
    $im = @imagecreatefromstring($b); if (!$im) return [$b, -1];
    $iw = imagesx($im); $ih = imagesy($im); $s = 0; $n = 0;
    for ($x = 0; $x < $iw; $x += 6) for ($y = 0; $y < $ih; $y += 6) {
        $col = imagecolorat($im, $x, $y);
        $s += ((($col >> 16) & 255) + (($col >> 8) & 255) + ($col & 255)) / 3; $n++;
    }
    return [$b, $n ? $s / $n : -1];
}

$picked = 0; $perCountry = []; $tried = 0;
foreach ($cand as $c) {
    if ($picked >= $want) break;
    if (++$tried > 150) break;
    $cc = $c['country_code'] ?? '?';
    if (($perCountry[$cc] ?? 0) >= 2) continue;             // diversity: max 2 per country
    [$bytes, $br] = brightness_bytes($c['image_url_full'], 1200, 630);
    if ($br < 55 || $br > 235) continue;                    // bright, not blank/overexposed
    file_put_contents("/tmp/sample_{$c['id']}.jpg", $bytes);
    $perCountry[$cc] = ($perCountry[$cc] ?? 0) + 1;
    $picked++;
    printf("%d|%s|%s|%s|%d|%s\n", $c['id'], $c['city'], $c['state'], $c['country'], round($br),
        'https://camhacker.com/cam/' . $c['id']);
}
fwrite(STDERR, "picked $picked of $want (tried $tried)\n");
