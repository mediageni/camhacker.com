<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact Us - ' . SITE_NAME;
$pageDescription = 'Contact the CamHacker team for camera removal requests, privacy concerns, or general inquiries.';
$canonicalUrl = SITE_URL . '/contact';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width:800px;">
  <h1 class="fw-bold mb-4">Contact CamHacker</h1>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <p class="lead">Have a question, privacy concern, or camera removal request? We'd love to hear from you.</p>

      <h2 class="h5 mt-4">Camera Removal</h2>
      <p>If you want your camera removed from our directory, you have two options:</p>
      <ul>
        <li><strong>Set a password</strong> on your camera device - it will automatically be removed from our listings.</li>
        <li><strong>Email us</strong> with the direct link to the camera page, and we will remove it promptly.</li>
      </ul>

      <h2 class="h5 mt-4">Get in Touch</h2>
      <p>Email: <a href="mailto:solidbunker@protonmail.com">solidbunker@protonmail.com</a></p>
      <p>Twitter: <a href="https://twitter.com/camhacker" target="_blank" rel="noopener">@camhacker</a></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
