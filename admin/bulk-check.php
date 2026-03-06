<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: /admin/login.php');
    exit;
}

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'bulk_delete') {
    $ids = json_decode($_POST['ids'] ?? '[]', true);
    if (is_array($ids) && count($ids)) {
        $db = CamDatabase::getInstance();
        foreach ($ids as $id) {
            $db->delete((int)$id);
        }
        header('Location: /admin/bulk-check.php?deleted=' . count($ids) . '&page=' . ($_POST['page'] ?? 1));
        exit;
    }
}

$db = CamDatabase::getInstance();
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 250;
$offset = ($page - 1) * $perPage;

$result = $db->search([], $perPage, $offset);
$webcams = $result['data'];
$total = $result['total'];
$totalPages = ceil($total / $perPage);
$deleted = (int)($_GET['deleted'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Bulk Health Check - <?= SITE_NAME ?> Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" rel="stylesheet">
<style>
  .cam-cell {
    width: 160px;
    display: inline-block;
    vertical-align: top;
    margin: 4px;
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: #1a1a2e;
    border: 2px solid transparent;
    transition: border-color 0.3s;
  }
  .cam-cell.is-online { border-color: #22c55e; }
  .cam-cell.is-offline { border-color: #ef4444; }
  .cam-cell.is-error { border-color: #f59e0b; }
  .cam-cell.is-checking { border-color: #6b7280; }
  .cam-cell.selected { border-color: #ff6300 !important; box-shadow: 0 0 0 2px rgba(255,99,0,0.3); }
  .cam-cell .cam-thumb {
    width: 100%;
    height: 100px;
    object-fit: cover;
    display: block;
    background: #111;
  }
  .cam-cell .cam-info {
    padding: 4px 6px;
    font-size: 0.7rem;
    line-height: 1.3;
  }
  .cam-cell .cam-info .cam-id { color: #999; }
  .cam-cell .cam-info .cam-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
  }
  .cam-cell .status-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    font-size: 0.65rem;
    padding: 1px 5px;
    border-radius: 4px;
  }
  .cam-cell .select-cb {
    position: absolute;
    top: 4px;
    left: 4px;
    z-index: 2;
  }
  .cam-cell .del-btn {
    position: absolute;
    bottom: 52px;
    right: 4px;
    z-index: 3;
    padding: 1px 5px;
    font-size: 0.65rem;
    opacity: 0;
    transition: opacity 0.2s;
  }
  .cam-cell:hover .del-btn { opacity: 1; }
  .cam-cell .cam-overlay {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.6);
    color: #ef4444;
    font-size: 1.5rem;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
  }
  .cam-cell.is-offline .cam-overlay { opacity: 1; }
  .cam-cell.is-offline .cam-thumb { filter: grayscale(1) brightness(0.4); }
  .cam-cell .cached-label {
    display: none;
    position: absolute;
    top: 40px;
    left: 0; right: 0;
    text-align: center;
    font-size: 0.6rem;
    color: #fca5a5;
    pointer-events: none;
    z-index: 2;
  }
  .cam-cell.is-offline .cached-label { display: block; }
  .progress-bar-health { height: 8px; }
  .sticky-toolbar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: var(--bs-body-bg);
    border-bottom: 1px solid var(--bs-border-color);
    padding: 12px 0;
  }
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
      <a href="/admin/" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
      <a href="/" class="btn btn-outline-light btn-sm" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View Site</a>
    </div>
  </div>
</nav>

<div class="container-fluid py-3">
  <?php if ($deleted): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <i class="bi bi-check-circle me-2"></i><?= $deleted ?> cameras deleted successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="sticky-toolbar">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
      <h5 class="mb-0 me-3"><i class="bi bi-heart-pulse me-2"></i>Bulk Health Check</h5>

      <button class="btn btn-primary btn-sm" id="btn-start"><i class="bi bi-play-fill me-1"></i>Start Check</button>
      <button class="btn btn-outline-secondary btn-sm" id="btn-stop" disabled><i class="bi bi-stop-fill me-1"></i>Stop</button>

      <div class="vr mx-1"></div>

      <button class="btn btn-outline-warning btn-sm" id="btn-select-dead"><i class="bi bi-check2-square me-1"></i>Select Dead</button>
      <button class="btn btn-outline-secondary btn-sm" id="btn-select-none"><i class="bi bi-square me-1"></i>Deselect All</button>

      <div class="vr mx-1"></div>

      <button class="btn btn-danger btn-sm" id="btn-delete-selected" disabled>
        <i class="bi bi-trash me-1"></i>Delete Selected (<span id="sel-count">0</span>)
      </button>

      <div class="ms-auto d-flex gap-3 align-items-center">
        <span class="badge bg-body-secondary text-body-secondary">Page <?= $page ?>/<?= $totalPages ?> (<?= number_format($total) ?> total)</span>
        <span class="badge bg-success" id="stat-online">0 online</span>
        <span class="badge bg-danger" id="stat-offline">0 dead</span>
        <span class="badge bg-warning text-dark" id="stat-error">0 errors</span>
        <span class="badge bg-secondary" id="stat-checked">0/<?= count($webcams) ?></span>
      </div>
    </div>

    <div class="progress progress-bar-health">
      <div class="progress-bar bg-info" id="progress-bar" style="width:0%"></div>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="mt-2">
      <nav>
        <ul class="pagination pagination-sm mb-0 flex-wrap">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="/admin/bulk-check.php?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
    <?php endif; ?>
  </div>

  <div class="py-3" id="cam-grid">
    <?php foreach ($webcams as $cam): ?>
    <div class="cam-cell" data-id="<?= $cam['id'] ?>" data-url="<?= e($cam['image_url_full'] ?? '') ?>">
      <input type="checkbox" class="form-check-input select-cb cam-cb" value="<?= $cam['id'] ?>">
      <span class="status-badge badge bg-secondary"><i class="bi bi-dash"></i></span>
      <div class="cam-overlay"><i class="bi bi-x-circle-fill"></i></div>
      <div class="cached-label">cached image - cam is dead</div>
      <button class="btn btn-sm btn-danger del-btn" title="Delete"><i class="bi bi-trash-fill"></i></button>
      <img class="cam-thumb"
           src="//wsrv.nl/?url=<?= urlencode($cam['image_url_full'] ?? '') ?>"
           alt=""
           loading="lazy"
           onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22160%22 height=%22100%22><rect fill=%22%23111%22 width=%22160%22 height=%22100%22/><text x=%2250%%25%22 y=%2250%%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23555%22 font-size=%2212%22>No Image</text></svg>';">
      <div class="cam-info">
        <span class="cam-id">#<?= $cam['id'] ?></span>
        <a href="/cam/<?= $cam['id'] ?>" target="_blank" class="cam-title text-decoration-none"><?= e($cam['title_seo'] ?? '') ?></a>
        <small class="text-body-secondary"><?= e($cam['city'] ?? '') ?>, <?= e($cam['country'] ?? '') ?></small>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  let running = false;
  let online = 0, offline = 0, errors = 0, checked = 0, deleted = 0;
  let totalCells = document.querySelectorAll('.cam-cell').length;

  const btnStart = document.getElementById('btn-start');
  const btnStop = document.getElementById('btn-stop');
  const btnSelectDead = document.getElementById('btn-select-dead');
  const btnSelectNone = document.getElementById('btn-select-none');
  const btnDelete = document.getElementById('btn-delete-selected');
  const progressBar = document.getElementById('progress-bar');

  function getLiveCells() {
    return document.querySelectorAll('.cam-cell:not(.is-deleted)');
  }

  function updateStats() {
    document.getElementById('stat-online').textContent = online + ' online';
    document.getElementById('stat-offline').textContent = offline + ' dead';
    document.getElementById('stat-error').textContent = errors + ' errors';
    document.getElementById('stat-checked').textContent = checked + '/' + totalCells;
    progressBar.style.width = totalCells ? Math.round((checked / totalCells) * 100) + '%' : '0%';
  }

  function updateSelCount() {
    const count = document.querySelectorAll('.cam-cb:checked').length;
    document.getElementById('sel-count').textContent = count;
    btnDelete.disabled = count === 0;
  }

  // AJAX delete: removes cells from DOM, doesn't interrupt health check
  async function ajaxDelete(ids) {
    const fd = new FormData();
    fd.append('ids', JSON.stringify(ids));
    try {
      const resp = await fetch('/admin/ajax-delete.php', { method: 'POST', body: fd, credentials: 'same-origin' });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      const data = await resp.json();
      if (data.ok) {
        ids.forEach(id => {
          const cell = document.querySelector('.cam-cell[data-id="' + id + '"]');
          if (cell) {
            // Adjust counters
            if (cell.classList.contains('is-online')) online--;
            if (cell.classList.contains('is-offline')) offline--;
            if (cell.classList.contains('is-error')) errors--;
            if (cell.classList.contains('is-online') || cell.classList.contains('is-offline') || cell.classList.contains('is-error')) {
              checked--;
            }
            totalCells--;
            deleted++;
            cell.classList.add('is-deleted');
            cell.style.transition = 'opacity 0.3s, transform 0.3s';
            cell.style.opacity = '0';
            cell.style.transform = 'scale(0.8)';
            setTimeout(() => cell.remove(), 300);
          }
        });
        updateStats();
        updateSelCount();
      }
      return data;
    } catch (e) {
      alert('Delete failed: ' + e.message);
      return null;
    }
  }

  // Click cell to toggle select (but not on buttons/links/checkboxes)
  document.getElementById('cam-grid').addEventListener('click', (e) => {
    const cell = e.target.closest('.cam-cell');
    if (!cell || cell.classList.contains('is-deleted')) return;

    // Handle inline delete button
    if (e.target.closest('.del-btn')) {
      e.stopPropagation();
      const id = parseInt(cell.dataset.id);
      ajaxDelete([id]);
      return;
    }

    if (e.target.closest('a') || e.target.closest('input')) return;

    const cb = cell.querySelector('.cam-cb');
    cb.checked = !cb.checked;
    cell.classList.toggle('selected', cb.checked);
    updateSelCount();
  });

  document.getElementById('cam-grid').addEventListener('change', (e) => {
    if (e.target.classList.contains('cam-cb')) {
      const cell = e.target.closest('.cam-cell');
      cell.classList.toggle('selected', e.target.checked);
      updateSelCount();
    }
  });

  async function checkOne(cell) {
    if (!running || cell.classList.contains('is-deleted')) return;
    const url = cell.dataset.url;
    const badge = cell.querySelector('.status-badge');

    cell.classList.add('is-checking');
    badge.className = 'status-badge badge bg-secondary';
    badge.innerHTML = '<div class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></div>';

    try {
      const fd = new FormData();
      fd.append('url', url);
      const resp = await fetch('/admin/check-health.php', { method: 'POST', body: fd, credentials: 'same-origin' });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      const data = await resp.json();

      if (cell.classList.contains('is-deleted')) return;
      cell.classList.remove('is-checking');
      checked++;

      if (data.status === 'online') {
        online++;
        cell.classList.add('is-online');
        badge.className = 'status-badge badge bg-success';
        badge.innerHTML = '<i class="bi bi-check"></i> ' + data.time + 'ms';
      } else if (data.status === 'offline') {
        offline++;
        cell.classList.add('is-offline');
        badge.className = 'status-badge badge bg-danger';
        badge.innerHTML = '<i class="bi bi-x"></i> DEAD';
      } else {
        errors++;
        cell.classList.add('is-error');
        badge.className = 'status-badge badge bg-warning text-dark';
        badge.innerHTML = '<i class="bi bi-exclamation"></i> ' + data.code;
      }
    } catch (e) {
      if (cell.classList.contains('is-deleted')) return;
      checked++;
      errors++;
      cell.classList.remove('is-checking');
      cell.classList.add('is-error');
      badge.className = 'status-badge badge bg-warning text-dark';
      badge.innerHTML = '<i class="bi bi-question"></i>';
    }
    updateStats();
  }

  btnStart.addEventListener('click', async () => {
    running = true;
    btnStart.disabled = true;
    btnStop.disabled = false;
    online = 0; offline = 0; errors = 0; checked = 0;
    totalCells = getLiveCells().length;
    updateStats();

    const queue = Array.from(getLiveCells());
    for (const cell of queue) {
      if (!running) break;
      if (cell.classList.contains('is-deleted')) continue;
      await checkOne(cell);
    }

    running = false;
    btnStart.disabled = false;
    btnStop.disabled = true;
  });

  btnStop.addEventListener('click', () => {
    running = false;
    btnStop.disabled = true;
    btnStart.disabled = false;
  });

  btnSelectDead.addEventListener('click', () => {
    getLiveCells().forEach(cell => {
      const isDead = cell.classList.contains('is-offline');
      const cb = cell.querySelector('.cam-cb');
      cb.checked = isDead;
      cell.classList.toggle('selected', isDead);
    });
    updateSelCount();
  });

  btnSelectNone.addEventListener('click', () => {
    getLiveCells().forEach(cell => {
      cell.querySelector('.cam-cb').checked = false;
      cell.classList.remove('selected');
    });
    updateSelCount();
  });

  btnDelete.addEventListener('click', () => {
    const ids = Array.from(document.querySelectorAll('.cam-cb:checked')).map(cb => parseInt(cb.value));
    if (!ids.length) return;
    if (!confirm('Delete ' + ids.length + ' cameras permanently?')) return;
    ajaxDelete(ids);
  });
});
</script>
</body>
</html>
