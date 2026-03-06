<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$stats = CamDatabase::getInstance()->getStats();

$pageTitle = 'About CamHacker - Live Webcam Directory | ' . SITE_NAME;
$pageDescription = 'Learn about CamHacker, the largest global directory of online surveillance and security cameras with ' . number_format($stats['total_cams']) . ' cameras across ' . $stats['total_countries'] . ' countries.';
$canonicalUrl = SITE_URL . '/about';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width:800px;">
  <h1 class="fw-bold mb-4">About CamHacker</h1>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
      <p class="lead">CamHacker is the largest global directory of online surveillance and security cameras, tracking <strong><?= number_format($stats['total_cams']) ?></strong> cameras across <strong><?= $stats['total_countries'] ?></strong> countries.</p>

      <p>Browse live webcams from streets, traffic, parking lots, offices, beaches, and more from around the world. You can explore live streams from cameras in various countries and regions, and view feeds from popular brands such as Axis, Panasonic, Linksys, Sony, TP-Link, Foscam, and many other network video cameras available online without password protection.</p>

      <p>We do not intentionally infringe upon anyone's privacy or rights. However, it is possible that some webcam owners did not intend for their cameras to be publicly accessible and may have inadvertently left them unsecured.</p>

      <h2 class="h4 mt-4">Privacy and Protection</h2>
      <p>At CamHacker, we take privacy seriously:</p>
      <ul>
        <li>We only display filtered cameras that do not infringe on personal privacy.</li>
        <li>Any camera that violates privacy will be promptly removed upon request. Simply send us an email with the direct link.</li>
        <li>To remove your camera from our directory, simply set a password on your device.</li>
      </ul>

      <h2 class="h4 mt-4">Camera Coordinates</h2>
      <p>Camera coordinates are approximated by IP Geolocation. They reflect the general location of the IP address, not the camera's exact physical address, offering accuracy only within a few hundred miles.</p>

      <h2 class="h4 mt-4">Contact</h2>
      <p>If you have any questions or concerns, write to <a href="mailto:solidbunker@protonmail.com">solidbunker@protonmail.com</a>.</p>

      <p class="mt-4 text-body-secondary">Thank you for exploring CamHacker.<br>- The CamHacker Team</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
