<?php

// Fetch top 3 webcams based on view_count
$sqltop3view = "SELECT id, image_url_full, title_seo, country, city, manufacturer, view_count
        FROM webcams 
        ORDER BY view_count DESC LIMIT 3";
$result = $conn->query($sqltop3view);

if ($result->num_rows === 0) {
    echo "<p>No webcams found in the database.</p>";
} else {
    // If results are found, proceed with displaying them
?>
    <!-- Grid Layout Top 3 Webcams -->
    <div class="row">
        <!-- First column (single card) -->
        <div class="col-md-8 mb-4">
            <?php if ($row = $result->fetch_assoc()): ?>
                <div class="card bg-dark text-white h-100 card-custom">
                    <!-- Add an inline style for a max-height -->
                    <img src="//wsrv.nl/?url=<?php echo urlencode($row['image_url_full']); ?>" class="card-img-top img-fluid" style="max-height: 400px; object-fit: cover;" alt="<?php echo htmlspecialchars($row['title_seo']); ?> Thumbnail">
                    <div class="card-img-overlay card-body-bottom">
                        <h5 class="card-title fs-6 fs-md-5"><a href="<?php echo generateSeoUrl('cam', $row['id']); ?>" class="link-light"><?php echo htmlspecialchars($row['title_seo']); ?></a></h5>
                        <p class="card-text">
                            <span class="badge bg-success"><?php echo htmlspecialchars($row['city']); ?>, <?php echo htmlspecialchars($row['country']); ?></span>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Second column (two cards stacked) -->
        <div class="col-md-4">
            <div class="row row-equal">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-12 mb-4">
                        <div class="card bg-dark text-white h-100 card-custom">
                            <!-- Add an inline style for a max-height -->
                            <img src="//wsrv.nl/?url=<?php echo urlencode($row['image_url_full']); ?>" class="card-img-top img-fluid" style="max-height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($row['title_seo']); ?> Thumbnail">
                            <div class="card-img-overlay card-body-bottom">
                                <h5 class="card-title fs-6 fs-md-5"><a href="<?php echo generateSeoUrl('cam', $row['id']); ?>" class="link-light"><?php echo htmlspecialchars($row['title_seo']); ?></a></h5>
                                <p class="card-text">
                                    <span class="badge bg-success"><?php echo htmlspecialchars($row['city']); ?>, <?php echo htmlspecialchars($row['country']); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

<?php
}
?>
