<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = CamDatabase::getInstance();
$markers = $db->getMapData();
$stats = $db->getStats();

$pageTitle = 'World Map of Live Webcams - ' . number_format($stats['total_cams']) . ' Cameras | ' . SITE_NAME;
$pageDescription = 'Interactive world map showing ' . number_format($stats['total_cams']) . ' live webcam locations across ' . $stats['total_countries'] . ' countries. Click a marker to view the live camera feed.';
$canonicalUrl = SITE_URL . '/map';

$extraHead = '
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" crossorigin="">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" crossorigin="">
<style>#world-map{height:calc(100vh - 200px);min-height:500px;border-radius:var(--bs-border-radius-lg);}</style>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "World Map of Live Webcams",
  "description": "' . addslashes($pageDescription) . '",
  "url": "' . $canonicalUrl . '"
}
</script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
  <h1 class="fw-bold mb-2"><i class="bi bi-map me-2"></i>World Map of Live Webcams</h1>
  <p class="text-body-secondary mb-4">Explore <?= number_format($stats['total_cams']) ?> cameras across <?= $stats['total_countries'] ?> countries. Click a marker to view the camera.</p>

  <div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div id="world-map"></div>
  </div>

  <?= renderAdBlock() ?>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js" crossorigin=""></script>
<script>
const map = L.map('world-map').setView([30, 0], 3);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  maxZoom: 18
}).addTo(map);

const markers = L.markerClusterGroup({ maxClusterRadius: 50, spiderfyOnMaxZoom: true });
const cams = <?= json_encode($markers) ?>;

cams.forEach(c => {
  const m = L.marker([c.lat, c.lng]);
  m.bindPopup(`
    <div style="min-width:200px">
      <strong>${c.title}</strong><br>
      <span class="fi fi-${c.country_code} me-1"></span>${c.city}, ${c.country}<br>
      <a href="/cam/${c.id}" class="btn btn-sm btn-primary mt-2">View Camera</a>
    </div>
  `);
  markers.addLayer(m);
});

map.addLayer(markers);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
