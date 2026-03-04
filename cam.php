<?php
session_start(); // Start a session to track user views

require 'db.php';
require 'includes/pagination.php'; // Include pagination logic
require 'queries.php'; // Include the query logic
require 'functions.php'; // Include the functions file

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

?>

<?php include 'includes/head-detail.php'; ?>

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
<title><?php echo htmlspecialchars($webcam['title_seo']); ?></title>


<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@graph": [
		{
			"@type": "ImageObject",
			"@id": "<?php echo $currentUrl; ?>",
			"contentUrl": "https://wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>"
		},

		{
			"@type": "Place",
			"@id": "<?php echo $currentUrl; ?>",
			"url": "<?php echo $currentUrl; ?>",
			"name": "<?php echo htmlspecialchars($webcam['city']) . ', ' . htmlspecialchars($webcam['country']); ?>",
			"description": "Watch live cam located in <?php echo htmlspecialchars($webcam['city']) . ', ' . htmlspecialchars($webcam['state']) . ', ' . htmlspecialchars($webcam['country']); ?>",
			"image": {
				"@id": "https://wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>"
			}
		},

		{
			"@type":"BroadcastEvent",
			"name": "Live camera in <?php echo htmlspecialchars($webcam['city']) . ', ' . htmlspecialchars($webcam['country']); ?>",
			"description": "Watch live cam located in <?php echo htmlspecialchars($webcam['city']) . ', ' . htmlspecialchars($webcam['state']) . ', ' . htmlspecialchars($webcam['country']); ?>",
			"isLiveBroadcast": true,
			"videoFormat": "SD",
			"url": "<?php echo $currentUrl; ?>",
			"broadcastOfEvent": {
				"name": "Live camera in <?php echo htmlspecialchars($webcam['city']) . ', ' . htmlspecialchars($webcam['country']); ?>",
				"description": "Watch live cam located in <?php echo htmlspecialchars($webcam['city']) . ', ' . htmlspecialchars($webcam['state']) . ', ' . htmlspecialchars($webcam['country']); ?>",
				"isAccessibleForFree": "true",
				"startDate": "<?php echo date('Y-m-d'); ?>",
				"eventStatus": "https://schema.org/EventMovedOnline",
				"eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",
				"image": {
					"@id": "<?php echo $currentUrl; ?>"
				},
				"location": {
					"@id": "<?php echo $currentUrl; ?>",
					"address": {
						"@type": "PostalAddress",
						"addressLocality": "<?php echo htmlspecialchars($webcam['city']) . ', ' . htmlspecialchars($webcam['state']) . ', ' . htmlspecialchars($webcam['country']); ?>",
						"addressRegion": "<?php echo htmlspecialchars($webcam['country_code']); ?>",
						"postalCode": "<?php echo htmlspecialchars($webcam['zipcode']); ?>"
					}
				}
			}
		}
	]
}
</script>



</head>
<body>
<main>
<?php include 'includes/nav.php'; ?>
    <div class="container mt-4">


    <h1><?php echo htmlspecialchars($webcam['title_seo']); ?></h1>

<div class="mt-3 mb-4">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6630109012927307"
     crossorigin="anonymous"></script>
<!-- Cam hacker Home -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6630109012927307"
     data-ad-slot="4229941586"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>
    <!-- View Counter -->
    <p><?php echo number_format($webcam['view_count']); ?> views</p>

    <!-- Display the larger thumbnail with ID to target it in JavaScript -->
    <?php if (!empty($webcam['image_url_full'])): ?>
        <img id="webcam-img" src="//wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>&w=640&h=480"
             alt="<?php echo htmlspecialchars($webcam['title_seo']); ?> Webcam Thumbnail"
             class="img-fluid rounded" style="width: 100%; max-width: 640px; height: auto;">
    <?php else: ?>
        <img src="/assets/img/loading.gif" alt="No Thumbnail Available" class="img-fluid rounded" style="width: 100%; max-width: 640px; height: auto;">
    <?php endif; ?>

<div class="mt-3 mb-4">
<h3 class="mt-5">More live stream webcams</h3>
<a href="https://livewebcamstream.com/"><img src="/assets/cams.gif" class="img-fluid" alt="Live Stream Webcams"></a>
</div>

<div class="mt-3 mb-4">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6630109012927307"
     crossorigin="anonymous"></script>
<!-- Cam hacker Home -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6630109012927307"
     data-ad-slot="4229941586"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>

    <!-- Display the webcam details in a table -->
    <h2 class="mt-5">Webcam Details</h2>
    <p><?php echo htmlspecialchars($webcam['description_seo']); ?></p>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr><th>Country</th><td><a href="<?php echo generateSeoUrl('country', $webcam['country']); ?>"><?php echo htmlspecialchars($webcam['country']); ?></a></td></tr>
            <tr><th>Country Code</th><td><?php echo htmlspecialchars($webcam['country_code']); ?></td></tr>

            <tr><th>State</th><td><?php echo htmlspecialchars($webcam['state']); ?></td></tr>
            <tr><th>City</th><td><a href="<?php echo generateSeoUrl('city', $webcam['city']); ?>"><?php echo htmlspecialchars($webcam['city']); ?></a></td></tr>
            <tr><th>Zipcode</th><td><?php echo htmlspecialchars($webcam['zipcode']); ?></td></tr>
            <tr><th>Timezone</th><td><?php echo htmlspecialchars($webcam['timezone']); ?></td></tr>
            <tr><th>IP with Port</th><td><?php echo htmlspecialchars($webcam['ipwithport']); ?></td></tr>
            <tr><th>Latitude</th><td><?php echo htmlspecialchars($webcam['latitude']); ?></td></tr>
            <tr><th>Longitude</th><td><?php echo htmlspecialchars($webcam['longitude']); ?></td></tr>
            <tr><th>Cam Stream</th><td><a href="<?php echo htmlspecialchars($webcam['cam_stream']); ?>" target="_blank">View Stream</a></td></tr>
            <tr><th>Manufacturer</th><td><a href="<?php echo generateSeoUrl('manufacturer', $webcam['manufacturer']); ?>"><?php echo htmlspecialchars($webcam['manufacturer']); ?></a></td></tr>
            <tr><th>Tag</th><td><a href="<?php echo generateSeoUrl('place', $webcam['tag']); ?>"><?php echo htmlspecialchars($webcam['tag']); ?></a></td></tr>
            <tr><th>Camera Model</th><td><?php echo htmlspecialchars($webcam['camera_model']); ?></td></tr>
        </table>
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