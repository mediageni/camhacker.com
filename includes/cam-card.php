<div class="col">
  <div class="card cam-card h-100 border-0 shadow-sm overflow-hidden">
    <div class="cam-card-img position-relative bg-dark" style="height:200px;">
      <div class="spinner-overlay position-absolute top-50 start-50 translate-middle">
        <div class="spinner-border spinner-border-sm text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <a href="/cam/<?= (int)$cam['id'] ?>">
        <img src="//wsrv.nl/?url=<?= urlencode($cam['image_url_full'] ?? '') ?>"
             alt="<?= e($cam['title_seo'] ?? 'Webcam') ?>"
             class="card-img-top"
             loading="lazy"
             style="object-fit:cover;height:100%;width:100%;opacity:0;transition:opacity .3s;"
             onload="this.style.opacity='1';var s=this.closest('.cam-card-img').querySelector('.spinner-overlay');if(s)s.remove();"
             onerror="this.style.display='none';var s=this.closest('.cam-card-img').querySelector('.spinner-overlay');if(s){s.innerHTML='<i class=\'bi bi-camera-video-off text-light fs-3\'></i><div class=\'text-light-emphasis small mt-1\'>Offline</div>';s.classList.add('text-center');}">
      </a>
      <div class="position-absolute bottom-0 start-0 end-0 p-2 cam-card-overlay">
        <div class="d-flex justify-content-between align-items-center">
          <span class="badge bg-dark bg-opacity-75">
            <?= countryFlag($cam['country_code'] ?? '') ?>
            <?= e($cam['city'] ?? '') ?>
          </span>
          <span class="badge bg-danger bg-opacity-75"><i class="bi bi-record-circle me-1"></i>LIVE</span>
        </div>
      </div>
    </div>
    <div class="card-body p-3">
      <a href="/cam/<?= (int)$cam['id'] ?>" class="text-decoration-none">
        <h6 class="card-title text-truncate mb-1"><?= e($cam['title_seo'] ?? 'Unknown Webcam') ?></h6>
      </a>
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-body-secondary">
          <i class="bi bi-eye me-1"></i><?= number_format($cam['view_count'] ?? 0) ?>
        </small>
        <?php if (!empty($cam['manufacturer'])): ?>
          <a href="/manufacturer/<?= slugify($cam['manufacturer']) ?>" class="badge bg-body-secondary text-body-secondary text-decoration-none">
            <?= e($cam['manufacturer']) ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
