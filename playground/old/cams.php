<?php if ($result->num_rows > 0): ?>
    <h2>Search Results</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Thumbnail</th><th>Title</th><th>Country</th><th>City</th><th>Manufacturer</th><th>Stream</th><th>IP</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <!-- Display the ID of the record -->
                <td><?php echo htmlspecialchars($row['id']); ?></td>

                <!-- Display the thumbnail -->
                <td>
                    <?php if (!empty($row['image_url_full'])): ?>
                        <img src="//wsrv.nl/?url=<?php echo urlencode($row['image_url_full']); ?>" alt="Thumbnail" style="width: 100px; height: auto;">
                    <?php else: ?>
                        <img src="placeholder.jpg" alt="No Thumbnail" style="width: 100px; height: auto;">
                    <?php endif; ?>
                </td>

                <!-- Display the title with a link to cam.php -->
                <td>
                    <a href="cam.php?id=<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['title_seo']); ?>
                    </a>
                </td>

                <!-- Display other details -->
                <td><?php echo htmlspecialchars($row['country']); ?></td>
                <td><?php echo htmlspecialchars($row['city']); ?></td>
                <td><?php echo htmlspecialchars($row['manufacturer']); ?></td>
                <td><a href="<?php echo htmlspecialchars($row['cam_url']); ?>" target="_blank">View Stream</a></td>
                <td><?php echo htmlspecialchars($row['ipwithport']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No webcams found matching your search criteria.</p>
<?php endif; ?>
