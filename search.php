<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = CamDatabase::getInstance();

$filters = [
    'search' => $_GET['q'] ?? $_GET['search'] ?? '',
    'country' => $_GET['country'] ?? '',
    'city' => $_GET['city'] ?? '',
    'manufacturer' => $_GET['manufacturer'] ?? '',
    'tag' => $_GET['tag'] ?? '',
];

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = RESULTS_PER_PAGE;
$offset = ($page - 1) * $perPage;

$result = $db->search($filters, $perPage, $offset);
$webcams = $result['data'];
$totalResults = $result['total'];
$totalPages = ceil($totalResults / $perPage);

$dynamicTitle = buildDynamicTitle($filters);
$dynamicDescription = buildDynamicDescription($filters);

$pageTitle = $dynamicTitle . ' | ' . SITE_NAME;
$pageDescription = $dynamicDescription;

// Build pagination base URL
$queryParts = [];
foreach ($filters as $k => $v) {
    if (!empty($v)) $queryParts[] = urlencode($k) . '=' . urlencode($v);
}
$paginationBase = '/search?' . implode('&', $queryParts) . (!empty($queryParts) ? '&' : '');

$extraHead = '
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "' . addslashes($dynamicTitle) . '",
  "description": "' . addslashes($dynamicDescription) . '",
  "url": "' . SITE_URL . '/search' . '",
  "numberOfItems": ' . $totalResults . '
}
</script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <?php if (!empty($filters['country'])): ?>
        <li class="breadcrumb-item active"><?= e(ucwords_custom($filters['country'])) ?></li>
      <?php elseif (!empty($filters['city'])): ?>
        <li class="breadcrumb-item active"><?= e(ucwords_custom($filters['city'])) ?></li>
      <?php elseif (!empty($filters['manufacturer'])): ?>
        <li class="breadcrumb-item active"><?= e(ucwords_custom($filters['manufacturer'])) ?></li>
      <?php else: ?>
        <li class="breadcrumb-item active">Search</li>
      <?php endif; ?>
    </ol>
  </nav>

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="fw-bold mb-1"><?= e($dynamicTitle) ?></h1>
      <p class="text-body-secondary mb-0"><?= number_format($totalResults) ?> webcams found</p>
    </div>
    <!-- Inline search -->
    <form action="/search" method="GET" class="d-flex gap-2" style="max-width:400px;">
      <input type="search" name="q" value="<?= e($filters['search']) ?>" class="form-control" placeholder="Search webcams...">
      <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
  </div>

  <!-- Active Filters -->
  <?php
  $activeFilters = array_filter($filters);
  if (!empty($activeFilters)): ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
      <?php foreach ($activeFilters as $key => $val): ?>
        <span class="badge bg-primary d-flex align-items-center gap-1 px-3 py-2">
          <?= e(ucfirst($key)) ?>: <?= e(ucwords_custom($val)) ?>
          <a href="<?php
            $newFilters = $filters;
            unset($newFilters[$key]);
            $parts = [];
            foreach ($newFilters as $k => $v) { if (!empty($v)) $parts[] = "$k=" . urlencode($v); }
            echo '/search?' . implode('&', $parts);
          ?>" class="text-white ms-1"><i class="bi bi-x-circle"></i></a>
        </span>
      <?php endforeach; ?>
      <a href="/search" class="badge bg-body-secondary text-body-secondary text-decoration-none px-3 py-2">Clear all</a>
    </div>
  <?php endif; ?>

  <?= renderAdBlock() ?>

  <?php if (empty($webcams)): ?>
    <div class="text-center py-5">
      <i class="bi bi-camera-video-off display-1 text-body-secondary"></i>
      <h3 class="mt-3">No webcams found</h3>
      <p class="text-body-secondary">Try a different search or <a href="/search">browse all webcams</a>.</p>
    </div>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
      <?php foreach ($webcams as $cam): ?>
        <?php include __DIR__ . '/includes/cam-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <div class="mt-5">
      <?= renderPagination($totalPages, $page, $paginationBase) ?>
    </div>
  <?php endif; ?>

  <?= renderAdBlock() ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
