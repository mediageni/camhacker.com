<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=UTF-8');

$db = CamDatabase::getInstance();
$allCams = $db->getAll();
$countries = $db->getDistinct('country');
$cities = $db->getDistinct('city');
$manufacturers = $db->getDistinct('manufacturer');
$tags = $db->getDistinct('tag');

$today = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

  <!-- Homepage -->
  <url>
    <loc><?= SITE_URL ?>/</loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>

  <!-- Map -->
  <url>
    <loc><?= SITE_URL ?>/map</loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>

  <!-- Search -->
  <url>
    <loc><?= SITE_URL ?>/search</loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>

  <!-- Static Pages -->
  <url><loc><?= SITE_URL ?>/about</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
  <url><loc><?= SITE_URL ?>/contact</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
  <url><loc><?= SITE_URL ?>/privacy</loc><changefreq>monthly</changefreq><priority>0.3</priority></url>

  <!-- Country Pages -->
  <?php foreach ($countries as $country): ?>
  <url>
    <loc><?= SITE_URL ?>/country/<?= slugify($country) ?></loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>

  <!-- City Pages -->
  <?php foreach ($cities as $city): ?>
  <url>
    <loc><?= SITE_URL ?>/city/<?= slugify($city) ?></loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endforeach; ?>

  <!-- Manufacturer Pages -->
  <?php foreach ($manufacturers as $mfg): ?>
  <url>
    <loc><?= SITE_URL ?>/manufacturer/<?= slugify($mfg) ?></loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>

  <!-- Tag/Place Pages -->
  <?php foreach ($tags as $tag): ?>
  <url>
    <loc><?= SITE_URL ?>/place/<?= slugify($tag) ?></loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>

  <!-- Individual Camera Pages -->
  <?php foreach ($allCams as $cam): ?>
  <url>
    <loc><?= SITE_URL ?>/cam/<?= (int)$cam['id'] ?></loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq>daily</changefreq>
    <priority>0.6</priority>
    <?php if (!empty($cam['image_url_full'])): ?>
    <image:image>
      <image:loc>https://wsrv.nl/?url=<?= urlencode($cam['image_url_full']) ?></image:loc>
      <image:title><?= htmlspecialchars($cam['title_seo'] ?? '', ENT_XML1) ?></image:title>
    </image:image>
    <?php endif; ?>
  </url>
  <?php endforeach; ?>

</urlset>
