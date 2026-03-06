<?php $stats = CamDatabase::getInstance()->getStats(); ?>
</main>

<footer class="border-top mt-5">
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-lg-4 mb-3">
        <h5 class="d-flex align-items-center gap-2"><i class="bi bi-webcam"></i> <?= SITE_NAME ?></h5>
        <p class="text-body-secondary">The largest global directory of live streaming webcams. Currently tracking <strong><?= number_format($stats['total_cams']) ?></strong> cameras across <strong><?= $stats['total_countries'] ?></strong> countries.</p>
        <div class="d-flex gap-3">
          <a href="https://twitter.com/camhacker" class="text-body-secondary" target="_blank" rel="noopener"><i class="bi bi-twitter-x fs-5"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="fw-semibold">Navigate</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="/" class="text-body-secondary text-decoration-none">Home</a></li>
          <li class="mb-2"><a href="/map" class="text-body-secondary text-decoration-none">World Map</a></li>
          <li class="mb-2"><a href="/search" class="text-body-secondary text-decoration-none">Browse All</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="fw-semibold">Info</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="/about" class="text-body-secondary text-decoration-none">About</a></li>
          <li class="mb-2"><a href="/contact" class="text-body-secondary text-decoration-none">Contact</a></li>
          <li class="mb-2"><a href="/privacy" class="text-body-secondary text-decoration-none">Privacy Policy</a></li>
        </ul>
      </div>
      <div class="col-lg-4">
        <h6 class="fw-semibold">Top Countries</h6>
        <div class="d-flex flex-wrap gap-2">
          <?php
          $topCountries = CamDatabase::getInstance()->getCountryCounts();
          uasort($topCountries, fn($a, $b) => $b['count'] - $a['count']);
          $i = 0;
          foreach ($topCountries as $name => $info):
            if ($i++ >= 10) break;
          ?>
            <a href="/country/<?= slugify($name) ?>" class="badge text-decoration-none bg-body-secondary text-body-secondary">
              <?= countryFlag($info['code']) ?><?= e($name) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <hr>
    <div class="text-center text-body-secondary small">
      &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. All cameras displayed are publicly accessible.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
