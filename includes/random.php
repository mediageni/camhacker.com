<?php
// Fetch 20 random webcams
$sqlRandomWebcams = "SELECT id, image_url_full, title_seo, country, city, manufacturer, view_count
                     FROM webcams
                     ORDER BY RAND()
                     LIMIT 20";
$resultrandom = $conn->query($sqlRandomWebcams);

if ($resultrandom->num_rows === 0) {
    echo "<p>No webcams found in the database.</p>";
} else {
    // If results are found, proceed with displaying them
?>
    <!-- Grid Layout for Random 20 Webcams -->
    <div class="row row-cols-1 row-cols-md-4 g-4"> <!-- 4 columns per row on medium screens -->
        <?php while ($row = $resultrandom->fetch_assoc()): ?>
            <?php include 'includes/cam-item.php'; ?>
        <?php endwhile; ?>
    </div>
<?php
}
?>