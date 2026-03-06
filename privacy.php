<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Privacy Policy - ' . SITE_NAME;
$pageDescription = 'CamHacker privacy policy. Learn how we handle data, cookies, and camera information.';
$canonicalUrl = SITE_URL . '/privacy';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width:800px;">
  <h1 class="fw-bold mb-4">Privacy Policy</h1>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <p>Last updated: <?= date('F j, Y') ?></p>

      <h2 class="h5 mt-4">Information We Collect</h2>
      <p>CamHacker does not collect personal information from visitors directly. We use analytics services to understand traffic patterns and improve our service.</p>

      <h2 class="h5 mt-4">Camera Data</h2>
      <p>All cameras listed on CamHacker are publicly accessible IP cameras that do not require authentication. We do not access password-protected cameras or private networks. Camera location data is approximated using IP geolocation.</p>

      <h2 class="h5 mt-4">Google Analytics & AdSense</h2>
      <p>We use Google AdSense to display advertisements. Google may use cookies to serve ads based on your previous visits. Users may opt out of personalized advertising by visiting <a href="https://www.google.com/settings/ads" target="_blank" rel="noopener">Google Ads Settings</a>.</p>

      <h2 class="h5 mt-4">Cookies</h2>
      <p>We use cookies for:</p>
      <ul>
        <li>Theme preference (light/dark mode)</li>
        <li>View count tracking (session-based)</li>
        <li>Analytics (Statcounter)</li>
        <li>Advertising (Google AdSense)</li>
      </ul>
      <p>You can choose to disable cookies through your browser settings.</p>

      <h2 class="h5 mt-4">Third-Party Services</h2>
      <ul>
        <li><strong>Google AdSense</strong> - for advertising</li>
        <li><strong>Statcounter</strong> - for anonymous analytics</li>
        <li><strong>wsrv.nl</strong> - for image proxying</li>
        <li><strong>OpenStreetMap</strong> - for map tiles</li>
      </ul>

      <h2 class="h5 mt-4">Third-Party Links</h2>
      <p>Our website may contain links to other websites. We are not responsible for the privacy practices or content of these third-party sites.</p>

      <h2 class="h5 mt-4">Camera Removal</h2>
      <p>If you find your camera listed and wish to have it removed, please <a href="/contact">contact us</a> or set a password on your camera device.</p>

      <h2 class="h5 mt-4">Changes</h2>
      <p>We may update this policy from time to time. Changes will be posted on this page.</p>

      <h2 class="h5 mt-4">Contact</h2>
      <p>Questions about this Privacy Policy? <a href="/contact">Contact us</a>.</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
