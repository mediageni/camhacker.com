<?php
require 'db.php';
require 'includes/pagination.php'; // Include pagination logic
require 'queries.php'; // Include the query logic
require 'functions.php'; // Include the functions file
?>
<?php include 'includes/head.php'; 
$dynamicTitle = 'About CamHacker'; // Default title

?>
<title><?php echo htmlspecialchars($dynamicTitle); ?></title>
</head>
<body>

<main>
<?php include 'includes/nav.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-4">About CamHacker</h1>
        
            <h2 class="mb-4">Welcome to CamHacker</h2>
            <p>Welcome to CamHacker, the largest global directory of online surveillance and security cameras. Browse live webcams from streets, traffic, parking lots, offices, beaches, and more from around the world. You can explore live streams from cameras in various countries and regions, and view feeds from popular brands such as Axis, Panasonic, Linksys, Sony, TP-Link, Foscam, and many other network video cameras available online without password protection. For the best experience, we recommend using the Mozilla Firefox browser to view these cameras.</p>

			<p>We do not intentionally infringe upon anyone's privacy or rights. However, it is possible that some webcam owners did not intend for their cameras to be publicly accessible and may have inadvertently left them unsecured. Certain cameras are used for security purposes in businesses or semi-public areas, while others are public tourist attractions that willingly share their live video streams with the world.</p>

            <h3 class="mb-3">Privacy and Protection</h3>
            <p>At CamHacker, we take privacy seriously, and the following measures have been put in place to ensure the protection of individual privacy:</p>
            <ul>
                <li>We only display filtered cameras that do not infringe on personal privacy. None of the cameras on CamHacker invade anyone's private life.</li>
                <li>Any camera that violates privacy or is deemed unethical will be promptly removed upon request. Simply send us an email with the direct link to the camera, and we will take action immediately.</li>
                <li>If you prefer not to contact us directly, you can remove your camera from our directory by setting a password for your device.</li>
            </ul>

            <h3 class="mb-3">Camera Coordinates</h3>
            <p>Please note that the coordinates of the cameras are approximate. They reflect the general location of the ISP address and not the camera's exact physical address, offering accuracy only within a few hundred miles. The coordinates are provided to indicate the city of the camera, not its specific address or location.</p>

			<p>If you have any questions or concerns about the site, write to solidbunker@protonmail.com.</p>

            <p>Thank you for exploring the CamHacker directory.</p>

            <p>CamHacker Team.</p>
    </div>
</main>

<?php include 'includes/modal.php'; ?>
<?php include 'includes/footer.php'; ?>
</body>
</html>
