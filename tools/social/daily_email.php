<?php
// CLI: emails a ready-to-post CamHacker cam (image + caption) to the configured inbox.
// Picks the brightest daylight cam that already has SEO copy. Manual X posting stays free.
// Usage: php daily_email.php [envfile] [datafile] [optional_cam_id]
if (php_sapi_name() !== 'cli') { http_response_code(403); exit('CLI only'); }

$envFile  = $argv[1] ?? '/root/camhacker-mail.env';
$dataFile = $argv[2] ?? '/var/www/camhacker.com/data/webcams.json';
$forceId  = isset($argv[3]) ? (int)$argv[3] : 0;

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
    if ($l === '' || $l[0] === '#') continue;
    [$k, $v] = array_pad(explode('=', $l, 2), 2, '');
    $env[trim($k)] = trim($v);
}

$cams = json_decode(file_get_contents($dataFile), true);
$now = time();

function card($id) { return 'https://camhacker.com/cam-image/' . $id . '.jpg'; }
function fetchJpeg($url) {
    $ctx = stream_context_create(['http' => ['timeout' => 12, 'header' => "User-Agent: camhacker-mail/1.0\r\n"]]);
    $b = @file_get_contents($url, false, $ctx);
    return ($b !== false && strncmp($b, "\xFF\xD8\xFF", 3) === 0) ? $b : null;
}
function brightness($bytes) {
    $im = @imagecreatefromstring($bytes); if (!$im) return -1;
    $w = imagesx($im); $h = imagesy($im); $s = 0; $n = 0;
    for ($x = 0; $x < $w; $x += 12) for ($y = 0; $y < $h; $y += 12) {
        $c = imagecolorat($im, $x, $y);
        $s += ((($c >> 16) & 255) + (($c >> 8) & 255) + ($c & 255)) / 3; $n++;
    }
    return $n ? $s / $n : -1;
}

$pick = null; $img = null;

if ($forceId) {
    foreach ($cams as $c) if ((int)$c['id'] === $forceId) { $pick = $c; break; }
    if ($pick) $img = fetchJpeg(card($forceId));
} else {
    // daylight + has SEO copy
    $cand = [];
    foreach ($cams as $c) {
        if (empty($c['meta_title']) || empty($c['image_url_full'])) continue;
        if (!preg_match('/^([+-]?)(\d+):/', $c['timezone'] ?? '', $m)) continue;
        $off = ($m[1] === '-' ? -1 : 1) * (int)$m[2];
        $lh = (int)gmdate('G', $now + $off * 3600);
        if ($lh >= 8 && $lh <= 18) $cand[] = $c;
    }
    shuffle($cand);
    $best = -1;
    foreach (array_slice($cand, 0, 25) as $c) {
        $b = fetchJpeg(card($c['id'])); if (!$b) continue;
        $br = brightness($b);
        if ($br > $best && $br <= 235) { $best = $br; $pick = $c; $img = $b; }
        if ($best >= 150) break; // good enough, stop early
    }
}

if (!$pick || !$img) { fwrite(STDERR, "no suitable cam found\n"); exit(1); }

$id = (int)$pick['id'];
$url = 'https://camhacker.com/cam/' . $id;
$caption = $pick['h1'] . " \xF0\x9F\x91\x80\n" . $url . "\n\n#livecam #webcam #cctv";

$bodyText = "Today's CamHacker post for X (@camhacker)\n\n"
    . "CAPTION (copy & paste):\n"
    . "------------------------------\n" . $caption . "\n"
    . "------------------------------\n\n"
    . "Cam #$id  -  " . $pick['city'] . ", " . $pick['country'] . "\n"
    . "Title:       " . $pick['meta_title'] . "\n"
    . "Description: " . $pick['meta_description'] . "\n\n"
    . "Image attached. When you paste the link, X also auto-shows it as a card.\n"
    . "Compose here: https://x.com/compose/post\n";

$subject = "📷 CamHacker post today: " . $pick['city'] . ", " . $pick['country'];

// Build MIME
$b = '=_cam_' . bin2hex(random_bytes(8));
$enc = '=?UTF-8?B?' . base64_encode($subject) . '?=';
$msg  = "From: CamHacker <{$env['MAIL_FROM']}>\r\n";
$msg .= "To: {$env['MAIL_TO']}\r\n";
$msg .= "Subject: $enc\r\n";
$msg .= "MIME-Version: 1.0\r\n";
$msg .= "Content-Type: multipart/mixed; boundary=\"$b\"\r\n\r\n";
$msg .= "--$b\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
$msg .= chunk_split(base64_encode($bodyText)) . "\r\n";
$msg .= "--$b\r\nContent-Type: image/jpeg; name=\"cam{$id}.jpg\"\r\n";
$msg .= "Content-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"cam{$id}.jpg\"\r\n\r\n";
$msg .= chunk_split(base64_encode($img)) . "\r\n--$b--\r\n";

// SMTP (STARTTLS + AUTH LOGIN)
function smtpRead($fp) {
    $data = '';
    while ($line = fgets($fp, 515)) { $data .= $line; if (isset($line[3]) && $line[3] === ' ') break; }
    return $data;
}
function smtpCmd($fp, $cmd, $expect) {
    if ($cmd !== null) fwrite($fp, $cmd . "\r\n");
    $r = smtpRead($fp);
    if ((int)substr($r, 0, 3) !== $expect) { fwrite(STDERR, "SMTP error (want $expect): $r\n"); exit(1); }
    return $r;
}

$fp = stream_socket_client("tcp://{$env['MAIL_HOST']}:{$env['MAIL_PORT']}", $eno, $estr, 20);
if (!$fp) { fwrite(STDERR, "connect failed: $estr\n"); exit(1); }
smtpCmd($fp, null, 220);
smtpCmd($fp, "EHLO camhacker.com", 250);
smtpCmd($fp, "STARTTLS", 220);
stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
smtpCmd($fp, "EHLO camhacker.com", 250);
smtpCmd($fp, "AUTH LOGIN", 334);
smtpCmd($fp, base64_encode($env['MAIL_USER']), 334);
smtpCmd($fp, base64_encode($env['MAIL_PASS']), 235);
smtpCmd($fp, "MAIL FROM:<{$env['MAIL_FROM']}>", 250);
smtpCmd($fp, "RCPT TO:<{$env['MAIL_TO']}>", 250);
smtpCmd($fp, "DATA", 354);
fwrite($fp, $msg . "\r\n.\r\n");
smtpCmd($fp, null, 250);
fwrite($fp, "QUIT\r\n");
fclose($fp);

echo "sent: cam #$id {$pick['city']}, {$pick['country']} -> {$env['MAIL_TO']}\n";
