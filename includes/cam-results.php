<?php if ($result->num_rows > 0): ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card bg-dark text-white h-100 card-custom" id="card-<?php echo $row['id']; ?>" style="min-height: 300px;"> <!-- Set a min-height here inline -->
                    <!-- Webcam Thumbnail -->
                    <?php if (!empty($row['image_url_full'])): ?>
                        <div class="position-relative">
                            <!-- Loading Spinner in Top-Left Corner -->
                            <div class="spinner-border text-light position-absolute top-0 start-0 m-2" role="status" id="spinner-<?php echo $row['id']; ?>">
                                <span class="visually-hidden">Loading...</span>
                            </div>

                            <!-- Image with onload to hide spinner and remove min-height -->
                            <img src="//wsrv.nl/?url=<?php echo urlencode($row['image_url_full']); ?>" 
                                 class="card-img" 
                                 alt="<?php echo htmlspecialchars($row['title_seo']); ?> Thumbnail"
                                 onload="document.getElementById('spinner-<?php echo $row['id']; ?>').style.display='none'; 
                                          document.getElementById('card-<?php echo $row['id']; ?>').style.minHeight='auto';">
                        </div>
                    <?php endif; ?>

                    <!-- Card Overlay -->
                    <div class="card-img-overlay card-body-bottom">
                        <!-- Webcam Title -->
                        <h5 class="card-title fs-6 fs-md-5">
                            <a href="cam.php?id=<?php echo $row['id']; ?>" class="link-light"><?php echo htmlspecialchars($row['title_seo']); ?></a>
                        </h5>

                        <!-- Webcam Location -->
                        <p class="card-text">
                            <span class="badge bg-success"><?php echo htmlspecialchars($row['city']); ?>, <?php echo htmlspecialchars($row['country']); ?></span>
                        </p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No webcams found.</p>
<?php endif; ?>
