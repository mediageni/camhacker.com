<?php
define('SITE_NAME', 'CamHacker');

// Dynamic base URL - works on localhost and production
$_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host = $_SERVER['HTTP_HOST'] ?? 'camhacker.com';
define('SITE_URL', "{$_protocol}://{$_host}");
define('SITE_DESCRIPTION', 'The largest global directory of online surveillance and security cameras. Browse live webcams from streets, traffic, parking lots, offices, beaches and more from around the world.');
define('DATA_FILE', __DIR__ . '/../data/webcams.json');
define('RESULTS_PER_PAGE', 24);
define('IMAGE_PROXY', '//wsrv.nl/?url=');
define('ADSENSE_CLIENT', 'ca-pub-6630109012927307');
define('ADSENSE_SLOT', '4229941586');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'hit2bits!');
