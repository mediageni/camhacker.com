<?php
$db = CamDatabase::getInstance();
$navCountries = $db->getCountryCounts();
$navManufacturers = $db->getDistinct('manufacturer');
$navTags = $db->getDistinct('tag');
?>
<nav class="navbar navbar-expand-lg sticky-top" id="main-nav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/">
      <i class="bi bi-webcam fs-3"></i>
      <span class="fw-bold"><?= SITE_NAME ?></span>
    </a>

    <div class="d-flex align-items-center gap-2 d-lg-none">
      <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
        <i class="bi bi-search"></i>
      </button>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navOffcanvas">
        <i class="bi bi-list fs-4"></i>
      </button>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="navOffcanvas">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title"><?= SITE_NAME ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-globe2 me-1"></i>Countries
            </a>
            <ul class="dropdown-menu dropdown-menu-scroll">
              <?php foreach ($navCountries as $name => $info): ?>
                <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="/country/<?= slugify($name) ?>">
                  <span><?= countryFlag($info['code']) ?><?= e($name) ?></span>
                  <span class="badge bg-secondary rounded-pill"><?= $info['count'] ?></span>
                </a></li>
              <?php endforeach; ?>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-cpu me-1"></i>Brands
            </a>
            <ul class="dropdown-menu dropdown-menu-scroll">
              <?php foreach ($navManufacturers as $m): ?>
                <li><a class="dropdown-item" href="/manufacturer/<?= slugify($m) ?>"><?= e($m) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>

          <?php if (!empty($navTags)): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-tag me-1"></i>Places
            </a>
            <ul class="dropdown-menu dropdown-menu-scroll">
              <?php foreach ($navTags as $t): ?>
                <li><a class="dropdown-item" href="/place/<?= slugify($t) ?>"><?= e($t) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
          <?php endif; ?>

          <li class="nav-item">
            <a class="nav-link" href="/map"><i class="bi bi-map me-1"></i>World Map</a>
          </li>
        </ul>

        <!-- Desktop search -->
        <div class="d-none d-lg-flex">
          <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" type="button" data-bs-toggle="modal" data-bs-target="#searchModal">
            <i class="bi bi-search"></i>
            <span class="d-none d-xl-inline">Search webcams...</span>
            <kbd class="ms-2 d-none d-xl-inline">Ctrl+K</kbd>
          </button>
        </div>

        <!-- Theme toggle -->
        <div class="ms-lg-3">
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" id="theme-toggle" aria-label="Toggle theme">
              <i class="bi bi-circle-half"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><button class="dropdown-item d-flex align-items-center gap-2" data-bs-theme-value="light"><i class="bi bi-sun-fill"></i>Light</button></li>
              <li><button class="dropdown-item d-flex align-items-center gap-2" data-bs-theme-value="dark"><i class="bi bi-moon-stars-fill"></i>Dark</button></li>
              <li><button class="dropdown-item d-flex align-items-center gap-2" data-bs-theme-value="auto"><i class="bi bi-circle-half"></i>Auto</button></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-label="Search webcams">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-4">
        <form action="/search" method="GET" class="d-flex gap-2">
          <div class="input-group input-group-lg">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="q" class="form-control" placeholder="Search country, city, brand..." autofocus>
          </div>
          <button type="submit" class="btn btn-primary btn-lg">Go</button>
        </form>
      </div>
    </div>
  </div>
</div>
