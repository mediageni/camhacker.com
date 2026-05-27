<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Auth guard
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: /admin/login.php');
    exit;
}

$cams = CamDatabase::getInstance()->getAll();
$total = count($cams);

$done = [];
$pending = [];
$byCountryDone = [];
$byCountryTotal = [];
foreach ($cams as $c) {
    $cc = $c['country'] ?: 'Unknown';
    $byCountryTotal[$cc] = ($byCountryTotal[$cc] ?? 0) + 1;
    if (!empty($c['meta_title'])) {
        $done[] = $c;
        $byCountryDone[$cc] = ($byCountryDone[$cc] ?? 0) + 1;
    } else {
        $pending[] = (int)$c['id'];
    }
}
$nDone = count($done);
$pct = $total ? round($nDone / $total * 100, 1) : 0;

// Pending IDs as plain text (for resuming in a future session)
if (isset($_GET['pending']) && $_GET['pending'] === 'txt') {
    header('Content-Type: text/plain');
    echo implode(',', $pending);
    exit;
}

ksort($byCountryTotal);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SEO Copy Progress - CamHacker Admin</title>
<meta name="robots" content="noindex, nofollow">
<style>
  body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 0; background: #0f1115; color: #e6e9ef; }
  .wrap { max-width: 1000px; margin: 0 auto; padding: 32px 20px 80px; }
  h1 { font-size: 22px; margin: 0 0 4px; }
  .sub { color: #8b93a7; margin-bottom: 24px; }
  a { color: #ff8a3d; }
  .bar { background: #1c2030; border-radius: 10px; height: 26px; overflow: hidden; margin: 10px 0 6px; }
  .bar > span { display: block; height: 100%; background: linear-gradient(90deg,#ff6300,#ff9d5c); }
  .nums { display: flex; gap: 28px; margin: 18px 0 26px; flex-wrap: wrap; }
  .nums div b { font-size: 26px; display: block; }
  .nums div span { color: #8b93a7; font-size: 13px; }
  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  th, td { text-align: left; padding: 7px 10px; border-bottom: 1px solid #232838; }
  th { color: #8b93a7; font-weight: 600; }
  td.r, th.r { text-align: right; }
  .mini { background: #1c2030; border-radius: 5px; height: 8px; width: 120px; display: inline-block; overflow: hidden; vertical-align: middle; }
  .mini > span { display: block; height: 100%; background: #ff6300; }
  .pill { background:#1c2030; padding:2px 8px; border-radius:20px; font-size:12px; color:#9fe0a0; }
  .actions { margin: 14px 0 30px; }
</style>
</head>
<body>
<div class="wrap">
  <h1>SEO Copy Progress</h1>
  <div class="sub">Image-based H1 / meta title / meta description rollout across all cams. <a href="/admin/index.php">&larr; Admin</a></div>

  <div class="bar"><span style="width: <?= $pct ?>%"></span></div>
  <div class="nums">
    <div><b><?= $nDone ?></b><span>done</span></div>
    <div><b><?= count($pending) ?></b><span>pending</span></div>
    <div><b><?= $total ?></b><span>total cams</span></div>
    <div><b><?= $pct ?>%</b><span>complete</span></div>
  </div>

  <div class="actions">
    <a class="pill" href="/admin/seo-progress.php?pending=txt">View pending cam IDs (plain text)</a>
  </div>

  <h3>By country</h3>
  <table>
    <tr><th>Country</th><th class="r">Done</th><th class="r">Total</th><th>Progress</th></tr>
    <?php foreach ($byCountryTotal as $cc => $tot):
        $d = $byCountryDone[$cc] ?? 0; $p = $tot ? round($d / $tot * 100) : 0; ?>
    <tr>
      <td><?= e($cc) ?></td>
      <td class="r"><?= $d ?></td>
      <td class="r"><?= $tot ?></td>
      <td><span class="mini"><span style="width: <?= $p ?>%"></span></span> <?= $p ?>%</td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>
