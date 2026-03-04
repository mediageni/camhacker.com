<?php
require 'db.php';
require 'includes/pagination.php'; // Include pagination logic
require 'queries.php'; // Include the query logic
require 'functions.php'; // Include the functions file

// Handle AJAX request for updating city dropdown
if (isset($_GET['ajax'])) {
    if ($_GET['ajax'] === 'get_cities' && isset($_GET['country'])) {
        $country = $_GET['country'];
        $cities = getCitiesByCountry($conn, $country);

        // Return cities as a JSON response
        echo json_encode($cities);
        exit;
    } elseif ($_GET['ajax'] === 'get_all_cities') {
        // Fetch all cities if no country is selected
        $cities = getDistinctValues($conn, 'city');
        
        // Return cities as a JSON response
        echo json_encode($cities);
        exit;
    }
}
// Define results per page
$results_per_page = 25;

// Determine current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $results_per_page;

// Get values for dropdown fields (only country, city, manufacturer)
$countries = getDistinctValues($conn, 'country');
$cities = getDistinctValues($conn, 'city');  // Fetch all cities by default
$manufacturers = getDistinctValues($conn, 'manufacturer');

// Initialize search queries for each filter
$filters = [
    'search' => isset($_GET['search']) ? $_GET['search'] : '',
    'country' => isset($_GET['country']) ? $_GET['country'] : '',
    'city' => isset($_GET['city']) ? $_GET['city'] : '',
    'manufacturer' => isset($_GET['manufacturer']) ? $_GET['manufacturer'] : ''
];

// Call the query function from queries.php
$search_query = buildSearchQuery($conn, $filters, $results_per_page, $offset);
$stmt = $search_query['stmt'];
$total_results = $search_query['total_results'];

// Execute the search query
$stmt->execute();
$result = $stmt->get_result();

// Calculate total pages
$total_pages = ceil($total_results / $results_per_page);

// Get the dynamic base URL and current URL
$baseUrl = getBaseUrl();
$currentUrl = getCurrentUrl();
?>
<?php include 'includes/head.php'; ?> 
</head>
<body>
<main>
<?php include 'includes/nav.php'; ?>
<div class="container mt-4">
    <h1 class="mb-4">Search Webcams</h1>
    
    <?php include 'includes/searchform.php'; ?> <!-- Include the search form -->


   <!-- Display total number of webcams -->
    <p><?php echo $total_webcams; ?> hidden webcams are available as of the last scan.</p>

    <!-- Webcam Results Table -->
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Manufacturer</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td>
                            <?php if (!empty($row['image_url_full'])): ?>
                                <img src="//wsrv.nl/?url=<?php echo urlencode($row['image_url_full']); ?>" alt="Thumbnail" style="width: 100px; height: auto;">
                            <?php else: ?>
                                <img src="/assets/img/loading.gif" alt="No Thumbnail" style="width: 100px; height: auto;">
                            <?php endif; ?>
                        </td>
                        <td><a href="cam.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title_seo']); ?></a></td>
                        <td><?php echo htmlspecialchars($row['country']); ?></td>
                        <td><?php echo htmlspecialchars($row['city']); ?></td>
                        <td><?php echo htmlspecialchars($row['manufacturer']); ?></td>
                        <td><?php echo htmlspecialchars($row['ipwithport']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No webcams found matching your search criteria.</p>
    <?php endif; ?>

    <!-- Pagination Links -->
        <ul class="pagination justify-content-center">
            <?php paginate($total_pages, $page, '?search=' . urlencode($filters['search']) . '&country=' . urlencode($filters['country']) . '&city=' . urlencode($filters['city']) . '&manufacturer=' . urlencode($filters['manufacturer']) . '&'); ?>
        </ul>


</main>
    <?php
    $stmt->close();
    $conn->close();
    ?>
</div>

<?php include 'includes/modal.php'; ?>
<?php include 'includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>