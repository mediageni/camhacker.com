<?php
require 'db.php';
require 'includes/pagination.php'; 
require 'queries.php';
require 'functions.php';

$results_per_page = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $results_per_page;

$countries = getDistinctValues($conn, 'country');
$cities = getDistinctValues($conn, 'city');
$manufacturers = getDistinctValues($conn, 'manufacturer');

// Extract the 'tag' variable explicitly
$filters = [
    'search' => isset($_GET['search']) ? $_GET['search'] : '',
    'country' => isset($_GET['country']) ? $_GET['country'] : '',
    'city' => isset($_GET['city']) ? $_GET['city'] : '',
    'manufacturer' => isset($_GET['manufacturer']) ? $_GET['manufacturer'] : '',
    'tag' => isset($_GET['tag']) ? $_GET['tag'] : ''
];

// Build the dynamic H1 text based on search parameters
$dynamicH1 = 'Live Webcams'; // Default H1

$elements = [];

if ($filters['tag']) {
    $elements[] = ucwords_custom($filters['tag']);
}
if ($filters['search']) {
    $elements[] = ucwords_custom($filters['search']);
}
if ($filters['manufacturer']) {
    $elements[] = ucwords_custom($filters['manufacturer']);
}

if (!empty($elements)) {
    $dynamicH1 = 'Live ' . implode(' ', $elements) . ' Webcams';
}

// Only add "in" if city or country is present
if ($filters['city'] || $filters['country']) {
    $dynamicH1 .= ' in ';
    if ($filters['city']) {
        $dynamicH1 .= ucwords_custom($filters['city']);
    }
    if ($filters['city'] && $filters['country']) {
        $dynamicH1 .= ', ';
    }
    if ($filters['country']) {
        $dynamicH1 .= ucwords_custom($filters['country']);
    }
}

// Execute search query
$search_query = buildSearchQuery($conn, $filters, $results_per_page, $offset);
$stmt = $search_query['stmt'];
$total_results = $search_query['total_results'];
$stmt->execute();
$result = $stmt->get_result();
$total_pages = ceil($total_results / $results_per_page);

// Include head.php and pass the filters for dynamic title/description
include 'includes/head.php'; // head.php will now have access to $filters
?>
<title><?php echo htmlspecialchars($dynamicTitle); ?></title>
</head>
<body>
<main>
<?php include 'includes/nav.php'; ?>

<div class="container mt-4">
    <!-- SEO-optimized H1 -->
    <h1 class="mb-4"><?php echo htmlspecialchars($dynamicH1); ?></h1>
    
    <!-- Include the card layout for webcams -->
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php include 'includes/cam-item.php'; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No webcams found.</p>
        <?php endif; ?>
    </div>

    <div class="row mt-5"> 
        <ul class="pagination justify-content-center">
            <?php paginate($total_pages, $page, '?search=' . urlencode($filters['search']) . '&country=' . urlencode($filters['country']) . '&city=' . urlencode($filters['city']) . '&manufacturer=' . urlencode($filters['manufacturer']) . '&tag=' . urlencode($filters['tag']) . '&'); ?>
        </ul>
    </div>
</div>
</main>

<?php include 'includes/modal.php'; ?>
<?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
