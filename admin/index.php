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

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}

$db = CamDatabase::getInstance();
$stats = $db->getStats();
$countryCounts = $db->getCountryCounts();

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete' && isset($_POST['id'])) {
        $db->delete((int)$_POST['id']);
        $message = 'Camera #' . (int)$_POST['id'] . ' deleted successfully.';
    }

    if ($postAction === 'bulk_delete' && isset($_POST['ids'])) {
        $ids = json_decode($_POST['ids'], true);
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $db->delete((int)$id);
            }
            $message = count($ids) . ' cameras deleted successfully.';
        }
    }

    if ($postAction === 'add') {
        $newCam = [
            'source_url' => $_POST['source_url'] ?? '',
            'cam_url' => $_POST['cam_url'] ?? '',
            'cam_stream' => $_POST['cam_stream'] ?? '',
            'ipwithport' => $_POST['ipwithport'] ?? '',
            'ip2location' => $_POST['ip2location'] ?? '',
            'latitude' => (float)($_POST['latitude'] ?? 0),
            'longitude' => (float)($_POST['longitude'] ?? 0),
            'country_code' => strtoupper($_POST['country_code'] ?? ''),
            'country' => $_POST['country'] ?? '',
            'state' => $_POST['state'] ?? '',
            'city' => $_POST['city'] ?? '',
            'zipcode' => $_POST['zipcode'] ?? '',
            'timezone' => $_POST['timezone'] ?? '',
            'cam_fixed_url' => '',
            'cam_ip' => $_POST['cam_ip'] ?? '',
            'image_jpeg' => '',
            'camera_model' => $_POST['camera_model'] ?? '',
            'image_jpg_url' => '',
            'image_url_full' => $_POST['image_url_full'] ?? '',
            'live_webcam_stream' => $_POST['cam_stream'] ?? '',
            'manufacturer' => $_POST['manufacturer'] ?? '',
            'tag' => $_POST['tag'] ?? '',
            'title_seo' => $_POST['title_seo'] ?? '',
            'description_seo' => $_POST['description_seo'] ?? '',
        ];
        $newId = $db->add($newCam);
        $message = 'Camera #' . $newId . ' added successfully.';
    }

    if ($postAction === 'edit' && isset($_POST['id'])) {
        $editData = [
            'title_seo' => $_POST['title_seo'] ?? '',
            'description_seo' => $_POST['description_seo'] ?? '',
            'country' => $_POST['country'] ?? '',
            'country_code' => strtoupper($_POST['country_code'] ?? ''),
            'state' => $_POST['state'] ?? '',
            'city' => $_POST['city'] ?? '',
            'manufacturer' => $_POST['manufacturer'] ?? '',
            'camera_model' => $_POST['camera_model'] ?? '',
            'tag' => $_POST['tag'] ?? '',
            'image_url_full' => $_POST['image_url_full'] ?? '',
            'cam_stream' => $_POST['cam_stream'] ?? '',
            'latitude' => (float)($_POST['latitude'] ?? 0),
            'longitude' => (float)($_POST['longitude'] ?? 0),
        ];
        $db->update((int)$_POST['id'], $editData);
        $message = 'Camera #' . (int)$_POST['id'] . ' updated successfully.';
    }
}

// Pagination & Search
$searchTerm = $_GET['q'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

$filters = [];
if (!empty($searchTerm)) $filters['search'] = $searchTerm;
if (!empty($_GET['country'])) $filters['country'] = $_GET['country'];

$result = $db->search($filters, $perPage, $offset);
$webcams = $result['data'];
$totalResults = $result['total'];
$totalPages = ceil($totalResults / $perPage);

// Edit mode
$editCam = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editCam = $db->getById((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Dashboard - <?= SITE_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" rel="stylesheet">
<style>
  .admin-stat { border-left: 4px solid var(--bs-primary); }
  .cam-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; }
  .table td { vertical-align: middle; }
  .selected-row { background-color: rgba(255,99,0,0.1) !important; }
  .health-online { color: #22c55e; }
  .health-offline { color: #ef4444; }
  .health-error { color: #f59e0b; }
  .health-checking { color: #6b7280; }
  .health-progress { height: 6px; }
  tr.dead-cam { opacity: 0.5; background-color: rgba(239,68,68,0.05) !important; }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/admin/">
      <i class="bi bi-webcam text-warning"></i>
      <span class="fw-bold"><?= SITE_NAME ?> Admin</span>
    </a>
    <div class="d-flex gap-3 align-items-center">
      <span class="text-light small d-none d-md-inline"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?></span>
      <a href="/" class="btn btn-outline-light btn-sm" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View Site</a>
      <a href="/admin/bulk-check.php" class="btn btn-outline-info btn-sm"><i class="bi bi-heart-pulse me-1"></i>Bulk Health Check</a>
      <a href="/admin/?action=add" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Camera</a>
      <a href="/admin/?logout=1" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid py-4">
  <?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i><?= e($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if ($action === 'add' || $editCam): ?>
    <!-- Add/Edit Form -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-transparent">
        <h5 class="mb-0"><?= $editCam ? 'Edit Camera #' . $editCam['id'] : 'Add New Camera' ?></h5>
      </div>
      <div class="card-body">
        <form method="POST" action="/admin/">
          <input type="hidden" name="action" value="<?= $editCam ? 'edit' : 'add' ?>">
          <?php if ($editCam): ?><input type="hidden" name="id" value="<?= $editCam['id'] ?>"><?php endif; ?>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">SEO Title</label>
              <input type="text" name="title_seo" class="form-control" value="<?= e($editCam['title_seo'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">SEO Description</label>
              <input type="text" name="description_seo" class="form-control" value="<?= e($editCam['description_seo'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Country</label>
              <input type="text" name="country" class="form-control" value="<?= e($editCam['country'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Country Code</label>
              <input type="text" name="country_code" class="form-control" value="<?= e($editCam['country_code'] ?? '') ?>" maxlength="2" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">State</label>
              <input type="text" name="state" class="form-control" value="<?= e($editCam['state'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" value="<?= e($editCam['city'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Manufacturer</label>
              <input type="text" name="manufacturer" class="form-control" value="<?= e($editCam['manufacturer'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Camera Model</label>
              <input type="text" name="camera_model" class="form-control" value="<?= e($editCam['camera_model'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tag</label>
              <input type="text" name="tag" class="form-control" value="<?= e($editCam['tag'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Image URL (full path to .jpg)</label>
              <input type="url" name="image_url_full" class="form-control" value="<?= e($editCam['image_url_full'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Stream URL (MJPG)</label>
              <input type="url" name="cam_stream" class="form-control" value="<?= e($editCam['cam_stream'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Latitude</label>
              <input type="number" step="any" name="latitude" class="form-control" value="<?= e($editCam['latitude'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Longitude</label>
              <input type="number" step="any" name="longitude" class="form-control" value="<?= e($editCam['longitude'] ?? '') ?>">
            </div>
            <?php if (!$editCam): ?>
            <div class="col-md-3">
              <label class="form-label">IP with Port</label>
              <input type="text" name="ipwithport" class="form-control" value="">
            </div>
            <div class="col-md-3">
              <label class="form-label">IP2Location</label>
              <input type="text" name="ip2location" class="form-control" value="">
            </div>
            <?php endif; ?>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i><?= $editCam ? 'Save Changes' : 'Add Camera' ?></button>
            <a href="/admin/" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="admin-stat card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3"><i class="bi bi-webcam fs-4"></i></div>
          <div><div class="fs-4 fw-bold"><?= number_format($stats['total_cams']) ?></div><small class="text-body-secondary">Total Cameras</small></div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="admin-stat card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-success bg-opacity-10 text-success rounded-3 p-3"><i class="bi bi-globe2 fs-4"></i></div>
          <div><div class="fs-4 fw-bold"><?= $stats['total_countries'] ?></div><small class="text-body-secondary">Countries</small></div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="admin-stat card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-info bg-opacity-10 text-info rounded-3 p-3"><i class="bi bi-buildings fs-4"></i></div>
          <div><div class="fs-4 fw-bold"><?= $stats['total_cities'] ?></div><small class="text-body-secondary">Cities</small></div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="admin-stat card border-0 shadow-sm p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3"><i class="bi bi-eye fs-4"></i></div>
          <div><div class="fs-4 fw-bold"><?= number_format($stats['total_views']) ?></div><small class="text-body-secondary">Total Views</small></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search & Filter -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" action="/admin/" class="row g-2 align-items-end">
        <div class="col-md-4">
          <input type="search" name="q" value="<?= e($searchTerm) ?>" class="form-control" placeholder="Search cameras...">
        </div>
        <div class="col-md-3">
          <select name="country" class="form-select">
            <option value="">All Countries</option>
            <?php foreach ($countryCounts as $name => $info): ?>
              <option value="<?= e($name) ?>" <?= (($_GET['country'] ?? '') === $name) ? 'selected' : '' ?>><?= e($name) ?> (<?= $info['count'] ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
        </div>
        <div class="col-auto">
          <a href="/admin/" class="btn btn-outline-secondary">Reset</a>
        </div>
        <div class="col-auto ms-auto">
          <button type="button" class="btn btn-danger" id="bulk-delete-btn" disabled>
            <i class="bi bi-trash me-1"></i>Delete Selected (<span id="selected-count">0</span>)
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Camera List -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
      <span>Showing <?= number_format(count($webcams)) ?> of <?= number_format($totalResults) ?> cameras</span>
    </div>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th><input type="checkbox" id="select-all" class="form-check-input"></th>
            <th>ID</th>
            <th>Preview</th>
            <th>Title</th>
            <th>Location</th>
            <th>Brand</th>
            <th>Views</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($webcams as $cam): ?>
          <tr data-id="<?= $cam['id'] ?>">
            <td><input type="checkbox" class="form-check-input row-select" value="<?= $cam['id'] ?>"></td>
            <td><small class="text-body-secondary">#<?= $cam['id'] ?></small></td>
            <td>
              <img src="<?= proxyImage($cam['image_url_full'] ?? '', 120, 90) ?>"
                   class="cam-thumb" alt="" loading="lazy"
                   onerror="this.src='/assets/img/loading.gif';">
            </td>
            <td>
              <a href="/cam/<?= $cam['id'] ?>" target="_blank" class="text-decoration-none"><?= e(mb_strimwidth($cam['title_seo'] ?? '', 0, 50, '...')) ?></a>
            </td>
            <td>
              <?= countryFlag($cam['country_code'] ?? '') ?>
              <?= e($cam['city'] ?? '') ?>, <?= e($cam['country'] ?? '') ?>
            </td>
            <td><small><?= e($cam['manufacturer'] ?? '-') ?></small></td>
            <td><?= number_format($cam['view_count'] ?? 0) ?></td>
            <td class="health-status" data-url="<?= e($cam['image_url_full'] ?? '') ?>">
              <span class="health-checking"><i class="bi bi-dash-circle"></i></span>
            </td>
            <td>
              <div class="btn-group btn-group-sm">
                <a href="/cam/<?= $cam['id'] ?>" target="_blank" class="btn btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                <a href="/admin/?action=edit&id=<?= $cam['id'] ?>" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="/admin/" class="d-inline" onsubmit="return confirm('Delete camera #<?= $cam['id'] ?>?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $cam['id'] ?>">
                  <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-transparent">
      <nav>
        <ul class="pagination pagination-sm justify-content-center mb-0">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="/admin/?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Bulk Delete Form -->
<form method="POST" action="/admin/" id="bulk-delete-form" style="display:none;">
  <input type="hidden" name="action" value="bulk_delete">
  <input type="hidden" name="ids" id="bulk-delete-ids">
</form>

<!-- Health Check Modal -->
<div class="modal fade" id="healthModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-heart-pulse me-2"></i>Camera Health Checker</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-body-secondary mb-3">Checks if camera image URLs respond. Tests the <?= number_format(count($webcams)) ?> cameras on this page.</p>

        <div class="d-flex gap-2 mb-3">
          <button class="btn btn-primary" id="health-start"><i class="bi bi-play-fill me-1"></i>Start Check</button>
          <button class="btn btn-outline-secondary" id="health-stop" disabled><i class="bi bi-stop-fill me-1"></i>Stop</button>
          <button class="btn btn-danger ms-auto" id="health-delete-dead" disabled><i class="bi bi-trash me-1"></i>Delete All Dead (<span id="dead-count">0</span>)</button>
        </div>

        <div class="progress health-progress mb-3">
          <div class="progress-bar bg-info" id="health-progress-bar" style="width:0%"></div>
        </div>

        <div class="row text-center mb-3">
          <div class="col-3">
            <div class="fs-4 fw-bold" id="stat-total">0</div>
            <small class="text-body-secondary">Checked</small>
          </div>
          <div class="col-3">
            <div class="fs-4 fw-bold health-online" id="stat-online">0</div>
            <small class="text-body-secondary">Online</small>
          </div>
          <div class="col-3">
            <div class="fs-4 fw-bold health-offline" id="stat-offline">0</div>
            <small class="text-body-secondary">Offline/Dead</small>
          </div>
          <div class="col-3">
            <div class="fs-4 fw-bold health-error" id="stat-error">0</div>
            <small class="text-body-secondary">Errors</small>
          </div>
        </div>

        <div id="health-log" style="max-height:300px;overflow-y:auto;font-size:0.85rem;" class="border rounded p-2 bg-body-tertiary">
          <div class="text-body-secondary">Click "Start Check" to begin scanning...</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const selectAll = document.getElementById('select-all');
  const checkboxes = document.querySelectorAll('.row-select');
  const bulkBtn = document.getElementById('bulk-delete-btn');
  const countSpan = document.getElementById('selected-count');
  let lastChecked = null;

  function updateCount() {
    const checked = document.querySelectorAll('.row-select:checked');
    countSpan.textContent = checked.length;
    bulkBtn.disabled = checked.length === 0;
  }

  selectAll.addEventListener('change', () => {
    checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
    updateCount();
  });

  checkboxes.forEach(cb => {
    cb.addEventListener('click', (e) => {
      if (e.shiftKey && lastChecked) {
        const boxes = Array.from(checkboxes);
        const start = boxes.indexOf(lastChecked);
        const end = boxes.indexOf(cb);
        boxes.slice(Math.min(start, end), Math.max(start, end) + 1)
          .forEach(b => b.checked = lastChecked.checked);
      }
      lastChecked = cb;
      updateCount();
    });
  });

  bulkBtn.addEventListener('click', () => {
    const selected = Array.from(document.querySelectorAll('.row-select:checked')).map(c => parseInt(c.value));
    if (selected.length && confirm('Delete ' + selected.length + ' cameras?')) {
      document.getElementById('bulk-delete-ids').value = JSON.stringify(selected);
      document.getElementById('bulk-delete-form').submit();
    }
  });

  // --- Health Checker ---
  const healthStart = document.getElementById('health-start');
  const healthStop = document.getElementById('health-stop');
  const healthDeleteDead = document.getElementById('health-delete-dead');
  const healthLog = document.getElementById('health-log');
  const progressBar = document.getElementById('health-progress-bar');
  let healthRunning = false;
  let deadCamIds = [];

  healthStart?.addEventListener('click', async () => {
    const rows = document.querySelectorAll('tbody tr[data-id]');
    if (!rows.length) return;

    healthRunning = true;
    healthStart.disabled = true;
    healthStop.disabled = false;
    healthDeleteDead.disabled = true;
    deadCamIds = [];
    healthLog.innerHTML = '';

    let checked = 0, online = 0, offline = 0, errors = 0;
    const total = rows.length;
    // Use 1 concurrent request for PHP dev server (single-threaded), increase on production
    const concurrency = 1;
    let queue = Array.from(rows);

    const updateStats = () => {
      document.getElementById('stat-total').textContent = checked;
      document.getElementById('stat-online').textContent = online;
      document.getElementById('stat-offline').textContent = offline;
      document.getElementById('stat-error').textContent = errors;
      document.getElementById('dead-count').textContent = deadCamIds.length;
      progressBar.style.width = Math.round((checked / total) * 100) + '%';
    };

    const checkOne = async (row) => {
      if (!healthRunning) return;
      const id = row.dataset.id;
      const statusCell = row.querySelector('.health-status');
      const url = statusCell?.dataset.url;
      if (!url) { checked++; updateStats(); return; }

      statusCell.innerHTML = '<span class="health-checking"><div class="spinner-border spinner-border-sm" role="status"></div></span>';

      try {
        const fd = new FormData();
        fd.append('url', url);
        const resp = await fetch('/admin/check-health.php', { method: 'POST', body: fd, credentials: 'same-origin' });
        if (!resp.ok) { throw new Error('HTTP ' + resp.status); }
        const data = await resp.json();
        checked++;

        if (data.status === 'online') {
          online++;
          statusCell.innerHTML = '<span class="health-online" title="' + data.time + 'ms"><i class="bi bi-check-circle-fill"></i></span>';
          addLog('online', '#' + id + ' — Online (' + data.time + 'ms)');
        } else if (data.status === 'offline') {
          offline++;
          deadCamIds.push(parseInt(id));
          row.classList.add('dead-cam');
          statusCell.innerHTML = '<span class="health-offline" title="No response"><i class="bi bi-x-circle-fill"></i></span>';
          addLog('offline', '#' + id + ' — DEAD (no response)');
        } else {
          errors++;
          statusCell.innerHTML = '<span class="health-error" title="HTTP ' + data.code + '"><i class="bi bi-exclamation-circle-fill"></i></span>';
          addLog('error', '#' + id + ' — HTTP ' + data.code);
        }
      } catch (e) {
        checked++;
        errors++;
        statusCell.innerHTML = '<span class="health-error"><i class="bi bi-question-circle"></i></span>';
        addLog('error', '#' + id + ' — Check failed: ' + e.message);
      }
      updateStats();
    };

    // Process in batches of `concurrency`
    const workers = [];
    for (let i = 0; i < concurrency; i++) {
      workers.push((async () => {
        while (queue.length && healthRunning) {
          await checkOne(queue.shift());
        }
      })());
    }
    await Promise.all(workers);

    healthRunning = false;
    healthStart.disabled = false;
    healthStop.disabled = true;
    if (deadCamIds.length > 0) {
      healthDeleteDead.disabled = false;
    }
    addLog('info', 'Done! ' + online + ' online, ' + offline + ' dead, ' + errors + ' errors.');
  });

  healthStop?.addEventListener('click', () => {
    healthRunning = false;
    healthStop.disabled = true;
    healthStart.disabled = false;
    addLog('info', 'Stopped by user.');
    if (deadCamIds.length > 0) healthDeleteDead.disabled = false;
  });

  healthDeleteDead?.addEventListener('click', () => {
    if (!deadCamIds.length) return;
    if (!confirm('Delete ' + deadCamIds.length + ' dead cameras? This cannot be undone.')) return;
    document.getElementById('bulk-delete-ids').value = JSON.stringify(deadCamIds);
    document.getElementById('bulk-delete-form').submit();
  });

  function addLog(type, msg) {
    const colors = { online: 'health-online', offline: 'health-offline', error: 'health-error', info: 'text-info' };
    const div = document.createElement('div');
    div.className = colors[type] || '';
    div.textContent = msg;
    healthLog.appendChild(div);
    healthLog.scrollTop = healthLog.scrollHeight;
  }
});
</script>
</body>
</html>
