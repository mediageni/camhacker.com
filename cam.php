<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$db = CamDatabase::getInstance();
$webcam = $db->getById($id);

if (!$webcam) {
    http_response_code(404);
    $pageTitle = 'Webcam Not Found - ' . SITE_NAME;
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h1>Webcam Not Found</h1><p>The requested webcam could not be found.</p><a href="/" class="btn btn-primary">Back to Home</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Increment views
if (!isset($_SESSION['viewed_webcams']) || !in_array($id, $_SESSION['viewed_webcams'])) {
    $db->incrementViews($id);
    $_SESSION['viewed_webcams'][] = $id;
    $webcam['view_count'] = ($webcam['view_count'] ?? 0) + 1;
}

// Get related cams from same country
$related = $db->search(['country' => $webcam['country']], 8);
$relatedCams = array_filter($related['data'], fn($c) => (int)$c['id'] !== $id);
$relatedCams = array_slice($relatedCams, 0, 4);

$pageTitle = e($webcam['title_seo']) . ' | ' . SITE_NAME;
$pageDescription = e($webcam['description_seo']);
$canonicalUrl = SITE_URL . '/cam/' . $id;
$ogImage = 'https://wsrv.nl/?url=' . urlencode($webcam['image_url_full'] ?? '');

$extraHead = '
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "ImageObject",
      "@id": "' . $canonicalUrl . '#image",
      "contentUrl": "https://wsrv.nl/?url=' . urlencode($webcam['image_url_full'] ?? '') . '"
    },
    {
      "@type": "Place",
      "@id": "' . $canonicalUrl . '#place",
      "url": "' . $canonicalUrl . '",
      "name": "' . addslashes(e($webcam['city'] . ', ' . $webcam['country'])) . '",
      "description": "' . addslashes(e($webcam['description_seo'])) . '",
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "' . ($webcam['latitude'] ?? 0) . '",
        "longitude": "' . ($webcam['longitude'] ?? 0) . '"
      },
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "' . addslashes(e($webcam['city'])) . '",
        "addressRegion": "' . addslashes(e($webcam['state'])) . '",
        "addressCountry": "' . e($webcam['country_code']) . '",
        "postalCode": "' . e($webcam['zipcode']) . '"
      }
    },
    {
      "@type": "BroadcastEvent",
      "name": "Live camera in ' . addslashes(e($webcam['city'] . ', ' . $webcam['country'])) . '",
      "description": "' . addslashes(e($webcam['description_seo'])) . '",
      "isLiveBroadcast": true,
      "videoFormat": "SD",
      "url": "' . $canonicalUrl . '",
      "startDate": "' . date('Y-m-d') . '",
      "location": { "@id": "' . $canonicalUrl . '#place" }
    }
  ]
}
</script>';

require_once __DIR__ . '/includes/header.php';

$lat = (float)($webcam['latitude'] ?? 0);
$lng = (float)($webcam['longitude'] ?? 0);
$hasCoords = abs($lat) <= 90 && abs($lng) <= 180 && ($lat != 0 || $lng != 0);
?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <?php if (!empty($webcam['country'])): ?>
        <li class="breadcrumb-item"><a href="/country/<?= slugify($webcam['country']) ?>"><?= countryFlag($webcam['country_code'] ?? '') ?><?= e($webcam['country']) ?></a></li>
      <?php endif; ?>
      <?php if (!empty($webcam['city'])): ?>
        <li class="breadcrumb-item"><a href="/city/<?= slugify($webcam['city']) ?>"><?= e($webcam['city']) ?></a></li>
      <?php endif; ?>
      <li class="breadcrumb-item active" aria-current="page">Camera #<?= $id ?></li>
    </ol>
  </nav>

  <h1 class="fw-bold mb-2"><?= e($webcam['title_seo']) ?></h1>
  <p class="text-body-secondary mb-4">
    <i class="bi bi-eye me-1"></i><?= number_format($webcam['view_count'] ?? 0) ?> views
    <span class="mx-2">|</span>
    <span class="badge bg-danger"><i class="bi bi-record-circle me-1"></i>LIVE</span>
  </p>

  <?= renderAdBlock() ?>

  <div class="row g-4">
    <!-- Live Feed -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm overflow-hidden">
        <div class="cam-live-feed bg-dark position-relative" style="min-height:400px;">
          <?php if (!empty($webcam['image_url_full'])): ?>
            <img id="webcam-img"
                 src="//wsrv.nl/?url=<?= urlencode($webcam['image_url_full']) ?>&w=640&h=480"
                 alt="<?= e($webcam['title_seo']) ?>"
                 class="w-100" style="object-fit:contain;max-height:600px;">
            <div class="position-absolute top-0 end-0 p-3">
              <span class="badge bg-danger bg-opacity-75 fs-6"><i class="bi bi-record-circle me-1"></i>LIVE</span>
            </div>
          <?php else: ?>
            <div class="d-flex align-items-center justify-content-center h-100 text-white-50" style="min-height:400px;">
              <div class="text-center">
                <i class="bi bi-camera-video-off fs-1"></i>
                <p class="mt-2">No preview available</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="card-body d-flex justify-content-between align-items-center py-2">
          <small class="text-body-secondary">Auto-refreshes every 3 seconds</small>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="btn-pause" title="Pause"><i class="bi bi-pause-fill"></i></button>
            <button class="btn btn-sm btn-outline-secondary" id="btn-refresh" title="Refresh now"><i class="bi bi-arrow-clockwise"></i></button>
            <?php if (!empty($webcam['cam_stream'])): ?>
              <a href="<?= e($webcam['cam_stream']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Open stream"><i class="bi bi-box-arrow-up-right"></i> Stream</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Details Sidebar -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">
          <i class="bi bi-info-circle me-2"></i>Camera Details
        </div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <tbody>
              <tr><th class="ps-3" style="width:40%">Country</th><td><a href="/country/<?= slugify($webcam['country'] ?? '') ?>"><?= countryFlag($webcam['country_code'] ?? '') ?><?= e($webcam['country']) ?></a></td></tr>
              <tr><th class="ps-3">State</th><td><?= e($webcam['state']) ?></td></tr>
              <tr><th class="ps-3">City</th><td><a href="/city/<?= slugify($webcam['city'] ?? '') ?>"><?= e($webcam['city']) ?></a></td></tr>
              <tr><th class="ps-3">Zipcode</th><td><?= e($webcam['zipcode']) ?></td></tr>
              <tr><th class="ps-3">Timezone</th><td><?= e($webcam['timezone']) ?></td></tr>
              <tr><th class="ps-3">Brand</th><td><a href="/manufacturer/<?= slugify($webcam['manufacturer'] ?? '') ?>"><?= e($webcam['manufacturer']) ?></a></td></tr>
              <tr><th class="ps-3">Model</th><td><?= e($webcam['camera_model']) ?></td></tr>
              <?php if (!empty($webcam['tag'])): ?>
                <tr><th class="ps-3">Tag</th><td><a href="/place/<?= slugify($webcam['tag']) ?>"><?= e($webcam['tag']) ?></a></td></tr>
              <?php endif; ?>
              <tr><th class="ps-3">Coordinates</th><td><?= round($lat, 4) ?>, <?= round($lng, 4) ?></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <?php if ($hasCoords): ?>
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">
          <i class="bi bi-geo-alt me-2"></i>Location
        </div>
        <div class="card-body p-0">
          <div id="cam-map" style="height:250px;"></div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?= renderAdBlock() ?>

  <!-- SEO Description -->
  <div class="card border-0 shadow-sm my-4">
    <div class="card-body p-4">
      <h2 class="h5 fw-bold"><?= e($webcam['title_seo']) ?></h2>
      <p class="text-body-secondary mb-0"><?= e($webcam['description_seo']) ?></p>
    </div>
  </div>

  <!-- Related Cameras -->
  <?php if (!empty($relatedCams)): ?>
  <section class="mb-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-collection me-2"></i>More Webcams in <?= e($webcam['country']) ?></h3>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
      <?php foreach ($relatedCams as $cam): ?>
        <?php include __DIR__ . '/includes/cam-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</div>

<?php if ($hasCoords): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
const map = L.map('cam-map').setView([<?= $lat ?>, <?= $lng ?>], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap'
}).addTo(map);
L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map)
 .bindPopup('<?= addslashes(e($webcam['city'] . ', ' . $webcam['country'])) ?>');
</script>
<?php endif; ?>

<script>
(function() {
  const img = document.getElementById('webcam-img');
  if (!img) return;
  let paused = false;
  let timer = setInterval(refreshImage, 3000);

  function refreshImage() {
    if (paused) return;
    const rand = Math.floor(Math.random() * 100000);
    img.src = '//wsrv.nl/?url=<?= urlencode($webcam['image_url_full']) ?>&w=640&h=480&rand=' + rand;
  }

  document.getElementById('btn-pause')?.addEventListener('click', function() {
    paused = !paused;
    this.innerHTML = paused ? '<i class="bi bi-play-fill"></i>' : '<i class="bi bi-pause-fill"></i>';
  });
  document.getElementById('btn-refresh')?.addEventListener('click', refreshImage);
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
