<?php
require 'db.php';
require 'includes/pagination.php'; // Include pagination logic
require 'queries.php'; // Include the query logic
require 'functions.php'; // Include the functions file
?>
<?php include 'includes/head.php'; ?>
<title><?php echo htmlspecialchars($dynamicTitle); ?></title>
</head>
<body>

<main>
<?php include 'includes/nav.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-4">Top Webcams</h1>


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


        <?php include 'includes/top3.php'; ?>
        


        <h2 class="mb-4">Random Webcams</h2>

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

        <?php include 'includes/random.php'; ?>





        <div class="mt-5">
            <h2 class="mb-4">Welcome to CamHacker</h2>
            <p>Welcome to CamHacker, the largest global directory of online surveillance and security cameras. Browse live webcams from streets, traffic, parking lots, offices, beaches, and more from around the world. You can explore live streams from cameras in various countries and regions, and view feeds from popular brands such as Axis, Panasonic, Linksys, Sony, TP-Link, Foscam, and many other network video cameras available online without password protection.</p>

            <h3 class="mb-3">Privacy and Protection</h3>
            <p>At CamHacker, we take privacy seriously, and the following measures have been put in place to ensure the protection of individual privacy:</p>
            <ul>
                <li>We only display filtered cameras that do not infringe on personal privacy. None of the cameras on CamHacker invade anyone's private life.</li>
                <li>Any camera that violates privacy or is deemed unethical will be promptly removed upon request. Simply send us an email with the direct link to the camera, and we will take action immediately.</li>
                <li>If you want to remove your camera from our directory, please set a password on your device.</li>
            </ul>

            <h3 class="mb-3">Camera Coordinates</h3>
            <p>Please note that the coordinates of the cameras are approximated by IP Geolocation. They reflect the general location of the IP address and not the camera's exact physical address, offering accuracy only within a few hundred miles. The coordinates are provided to indicate the city of the camera, not its specific address or location.</p>

            <p>Thank you for exploring the CamHacker directory.</p>

            <p>CamHacker Team.</p>
        </div>


    </div>


</main>

<?php include 'includes/modal.php'; ?>
<?php include 'includes/footer.php'; ?>
</body>
</html>
<?php
$conn->close();
?>