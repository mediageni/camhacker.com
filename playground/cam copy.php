<?php
session_start(); // Start a session to track user views

require 'db.php';
require 'includes/pagination.php'; // Include pagination logic
require 'queries.php'; // Include the query logic
require 'functions.php'; // Include the functions file
?>
<?php include 'includes/head.php'; ?>
</head>
<body>



// Get the ID of the webcam from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die('Invalid webcam ID: ' . htmlspecialchars($id)); // Show the ID for debugging purposes
}

// Fetch webcam details from the database
$sql = "SELECT * FROM webcams WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Webcam not found.');
}

// Get the webcam data
$webcam = $result->fetch_assoc();

// Increment the view count only if this webcam hasn't been viewed in the session
if (!isset($_SESSION['viewed_webcams']) || !in_array($id, $_SESSION['viewed_webcams'])) {
    $updateSql = "UPDATE webcams SET view_count = view_count + 1 WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('i', $id);
    $updateStmt->execute();
    $updateStmt->close();

    // Mark this webcam as viewed in the session
    $_SESSION['viewed_webcams'][] = $id;
}

$stmt->close();
$conn->close();

// Get the dynamic base URL and current URL
$baseUrl = getBaseUrl();
$currentUrl = getCurrentUrl();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Title and Meta Description -->
    <title><?php echo htmlspecialchars($webcam['title_seo']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($webcam['description_seo']); ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $currentUrl; ?>">
    
    <!-- Open Graph Meta Tags for Social Sharing -->
    <meta property="og:title" content="<?php echo htmlspecialchars($webcam['title_seo']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($webcam['description_seo']); ?>">
    <meta property="og:image" content="//wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>&w=640&h=480">
    <meta property="og:url" content="<?php echo $currentUrl; ?>">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($webcam['title_seo']); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($webcam['description_seo']); ?>">
    <meta name="twitter:image" content="//wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>&w=640&h=480">

    <script>
        function refreshImage() {
            const webcamImg = document.getElementById('webcam-img');
            const randomNum = Math.floor(Math.random() * 10000); // Generate random number
            const url = "//wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>&w=640&h=480&rand=" + randomNum;
            webcamImg.src = url; // Update the image src with the new URL
        }

        // Refresh the image every 3 seconds (3000 milliseconds)
        setInterval(refreshImage, 3000);
    </script>
</head>
<body>
<main>

    <h1><?php echo htmlspecialchars($webcam['title_seo']); ?></h1>

    <!-- View Counter -->
    <p><?php echo number_format($webcam['view_count']); ?> views</p>

    <!-- Display the larger thumbnail with ID to target it in JavaScript -->
    <?php if (!empty($webcam['image_url_full'])): ?>
        <img id="webcam-img" src="//wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>&w=640&h=480" 
             alt="<?php echo htmlspecialchars($webcam['title_seo']); ?> Webcam Thumbnail" 
             style="width: 640px; height: 480px;">
    <?php else: ?>
        <img src="/assets/img/loading.gif" alt="No Thumbnail Available" style="width: 640px; height: 480px;">
    <?php endif; ?>

    <!-- Display the webcam details in a table -->
    <h2>Webcam Details</h2>
	<p><?php echo htmlspecialchars($webcam['description_seo']); ?></p>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr><th>Country</th><td><?php echo htmlspecialchars($webcam['country']); ?></td></tr>
        <tr><th>State</th><td><?php echo htmlspecialchars($webcam['state']); ?></td></tr>
        <tr><th>City</th><td><?php echo htmlspecialchars($webcam['city']); ?></td></tr>
        <tr><th>Zipcode</th><td><?php echo htmlspecialchars($webcam['zipcode']); ?></td></tr>
        <tr><th>Timezone</th><td><?php echo htmlspecialchars($webcam['timezone']); ?></td></tr>
        <tr><th>IP with Port</th><td><?php echo htmlspecialchars($webcam['ipwithport']); ?></td></tr>
        <tr><th>Latitude</th><td><?php echo htmlspecialchars($webcam['latitude']); ?></td></tr>
        <tr><th>Longitude</th><td><?php echo htmlspecialchars($webcam['longitude']); ?></td></tr>
        <tr><th>Cam Stream</th><td><a href="<?php echo htmlspecialchars($webcam['cam_stream']); ?>" target="_blank">View Stream</a></td></tr>
        <tr><th>Manufacturer</th><td><?php echo htmlspecialchars($webcam['manufacturer']); ?></td></tr>
        <tr><th>Tag</th><td><?php echo htmlspecialchars($webcam['tag']); ?></td></tr>
        <tr><th>Camera Model</th><td><?php echo htmlspecialchars($webcam['camera_model']); ?></td></tr> <!-- Added camera model -->
    </table>
</main>
</body>
</html>
