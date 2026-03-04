<div class="col">
    <div class="card shadow-sm h-100" id="card-<?php echo $row['id']; ?>" style="min-height: 300px;">
        <!-- Spinner for loading image -->
        <div class="position-absolute top-50 start-50 translate-middle" id="spinner-<?php echo $row['id']; ?>">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Webcam Thumbnail -->
        <?php if (!empty($row['image_url_full'])): ?>
            <div class="bg-black position-relative" style="height: 225px;">
                <a href="<?php echo generateSeoUrl('cam', $row['id']); ?>" id="image-link-<?php echo $row['id']; ?>">
                    <img src="//wsrv.nl/?url=<?php echo urlencode($row['image_url_full']); ?>"
                         class="card-img-top"
                         alt="<?php echo htmlspecialchars($row['title_seo']); ?> Thumbnail"
                         onload="document.getElementById('spinner-<?php echo $row['id']; ?>').style.display='none';
                                  document.getElementById('image-<?php echo $row['id']; ?>').style.display='block';
                                  document.getElementById('card-<?php echo $row['id']; ?>').style.minHeight='auto';"
                         id="image-<?php echo $row['id']; ?>"
                         style="display: none; object-fit: cover; height: 100%; width: 100%;">
                </a>

                <!-- Country and City Links - Positioned in the lower right corner of the image -->
                <div class="position-absolute bottom-0 end-0 text-end p-2" style="background: rgba(0, 0, 0, 0.5);">
                    <a href="<?php echo generateSeoUrl('country', $row['country']); ?>" class="text-white small me-2">
                        <?php echo htmlspecialchars($row['country']); ?>
                    </a>
                    <a href="<?php echo generateSeoUrl('city', $row['city']); ?>" class="text-white small">
                        <?php echo htmlspecialchars($row['city']); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Card Body with Bottom Alignment -->
        <div class="card-body d-flex flex-column">
            <!-- Webcam Title linked to the cam detail page -->
            <p class="card-text">
                <a href="<?php echo generateSeoUrl('cam', $row['id']); ?>" class="text-muted">
                    <?php echo htmlspecialchars($row['title_seo']); ?>
                </a>
            </p>

            <!-- Align this section to the bottom -->
            <div class="mt-auto d-flex justify-content-between align-items-center">
                <!-- Total view count -->
                <small class="text-body-secondary">
                    <?php echo htmlspecialchars($row['view_count']) . ' views'; ?>
                </small>
            </div>
        </div>
    </div>
</div>
