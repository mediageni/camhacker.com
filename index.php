<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = CamDatabase::getInstance();
$stats = $db->getStats();
$topCams = $db->getTopViewed(6);
$randomCams = $db->getRandom(24);
$countryCounts = $db->getCountryCounts();

$pageTitle = 'CamHacker - Live Webcams from Around the World | ' . number_format($stats['total_cams']) . ' Cameras';
$pageDescription = SITE_DESCRIPTION;
$canonicalUrl = SITE_URL;

$extraHead = '
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "' . SITE_NAME . '",
  "url": "' . SITE_URL . '",
  "description": "' . addslashes(SITE_DESCRIPTION) . '",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "' . SITE_URL . '/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden">
  <div class="container py-5">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="d-flex align-items-center gap-2 mb-3">
          <span class="badge bg-danger d-flex align-items-center gap-1"><i class="bi bi-record-circle"></i> LIVE</span>
          <span class="text-body-secondary"><?= number_format($stats['total_cams']) ?> cameras worldwide</span>
        </div>
        <h1 class="display-4 fw-bold mb-3">Explore <span class="text-gradient">Live Webcams</span> from Around the World</h1>
        <p class="lead text-body-secondary mb-4">Browse <?= number_format($stats['total_cams']) ?> live streaming cameras across <?= $stats['total_countries'] ?> countries. Discover streets, traffic, offices, nature and more.</p>
        <div class="d-flex flex-wrap gap-3">
          <a href="/search" class="btn btn-primary btn-lg px-4"><i class="bi bi-grid-3x3-gap me-2"></i>Browse All</a>
          <a href="/map" class="btn btn-outline-secondary btn-lg px-4"><i class="bi bi-map me-2"></i>World Map</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <div class="col-4"><div class="stat-card text-center p-3 rounded-4"><div class="fs-2 fw-bold text-primary"><?= number_format($stats['total_cams']) ?></div><small class="text-body-secondary">Cameras</small></div></div>
          <div class="col-4"><div class="stat-card text-center p-3 rounded-4"><div class="fs-2 fw-bold text-primary"><?= $stats['total_countries'] ?></div><small class="text-body-secondary">Countries</small></div></div>
          <div class="col-4"><div class="stat-card text-center p-3 rounded-4"><div class="fs-2 fw-bold text-primary"><?= $stats['total_cities'] ?></div><small class="text-body-secondary">Cities</small></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<?= renderAdBlock() ?>

<!-- Top Viewed Cameras -->
<section class="container mb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-fire me-2 text-danger"></i>Most Viewed Webcams</h2>
    <a href="/search?sort=views" class="btn btn-sm btn-outline-primary">View All</a>
  </div>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
    <?php foreach ($topCams as $cam): ?>
      <?php include __DIR__ . '/includes/cam-card.php'; ?>
    <?php endforeach; ?>
  </div>
</section>

<!-- Countries Grid -->
<section class="container mb-5">
  <h2 class="fw-bold mb-4"><i class="bi bi-globe2 me-2 text-primary"></i>Browse by Country</h2>
  <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
    <?php
    uasort($countryCounts, fn($a, $b) => $b['count'] - $a['count']);
    foreach ($countryCounts as $name => $info): ?>
      <div class="col">
        <a href="/country/<?= slugify($name) ?>" class="card border-0 shadow-sm text-decoration-none h-100 country-card">
          <div class="card-body text-center p-3">
            <span class="fi fi-<?= e($info['code']) ?> fis" style="font-size:2rem;"></span>
            <h6 class="mt-2 mb-1 text-truncate"><?= e($name) ?></h6>
            <small class="text-body-secondary"><?= $info['count'] ?> cams</small>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?= renderAdBlock() ?>

<!-- Random Cameras -->
<section class="container mb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-shuffle me-2 text-success"></i>Discover Random Webcams</h2>
    <a href="/" class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-clockwise me-1"></i>Refresh</a>
  </div>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
    <?php foreach ($randomCams as $cam): ?>
      <?php include __DIR__ . '/includes/cam-card.php'; ?>
    <?php endforeach; ?>
  </div>
</section>

<!-- About Section (SEO content) -->
<section class="container mb-5">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-lg-5">
      <h2 class="fw-bold mb-3">Welcome to CamHacker</h2>
      <p>Welcome to CamHacker, the largest global directory of online surveillance and security cameras. Browse live webcams from streets, traffic, parking lots, offices, beaches, and more from around the world. You can explore live streams from cameras in various countries and regions, and view feeds from popular brands such as Axis, Panasonic, Linksys, Sony, TP-Link, Foscam, and many other network video cameras available online without password protection.</p>

      <h3 class="mt-4">Privacy and Protection</h3>
      <ul>
        <li>We only display filtered cameras that do not infringe on personal privacy. None of the cameras on CamHacker invade anyone's private life.</li>
        <li>Any camera that violates privacy or is deemed unethical will be promptly removed upon request. Simply send us an email with the direct link to the camera, and we will take action immediately.</li>
        <li>If you want to remove your camera from our directory, please set a password on your device.</li>
      </ul>

      <h3 class="mt-4">Camera Coordinates</h3>
      <p>The coordinates of the cameras are approximated by IP Geolocation. They reflect the general location of the IP address and not the camera's exact physical address, offering accuracy only within a few hundred miles.</p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
