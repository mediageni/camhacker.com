<?php
// Same-domain Open Graph card image for a cam.
// Social scrapers (Reddit/Embedly, X, Telegram) are unreliable with third-party
// query-string image URLs, so we serve the resized snapshot from our own domain.
ini_set('display_errors', '0'); // never let a notice corrupt the binary body
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cam = CamDatabase::getInstance()->getById($id);

if (!$cam || empty($cam['image_url_full'])) {
    http_response_code(404);
    exit;
}

$cacheDir  = __DIR__ . '/cache/og';
$cacheFile = $cacheDir . '/' . $id . '.jpg';
$ttl       = 1800; // 30 min

function serveJpeg($bytes, $maxAge) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=' . $maxAge);
    header('Content-Length: ' . strlen($bytes));
    echo $bytes;
    exit;
}

if (is_file($cacheFile) && (time() - filemtime($cacheFile) < $ttl)) {
    serveJpeg(file_get_contents($cacheFile), $ttl);
}

$src = 'https://wsrv.nl/?url=' . urlencode($cam['image_url_full']) . '&w=1200&h=630&fit=cover&output=jpg';
$ctx = stream_context_create(['http' => [
    'method'          => 'GET',
    'timeout'         => 8,
    'follow_location' => 1,
    'header'          => "User-Agent: CamHacker-OG/1.0\r\n",
]]);
$data = @file_get_contents($src, false, $ctx);

// Valid JPEG starts with the FF D8 FF magic bytes; anything else means the
// source was unreachable or wsrv returned an error response.
$isJpeg = ($data !== false && strncmp($data, "\xFF\xD8\xFF", 3) === 0);

if ($isJpeg) {
    if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
    @file_put_contents($cacheFile, $data, LOCK_EX);
    serveJpeg($data, $ttl);
}

// Source unreachable: serve stale cache if we have it, else send the scraper to wsrv.
if (is_file($cacheFile)) {
    serveJpeg(file_get_contents($cacheFile), 300);
}

header('Location: ' . $src, true, 302);
