<?php
$pageTitle = $pageTitle ?? SITE_NAME . ' - Live Webcams from Around the World';
$pageDescription = $pageDescription ?? SITE_DESCRIPTION;
$canonicalUrl = $canonicalUrl ?? SITE_URL . $_SERVER['REQUEST_URI'];
$ogImage = $ogImage ?? SITE_URL . '/assets/img/og-default.jpg';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
<script>
(()=>{const t=localStorage.getItem('theme');document.documentElement.setAttribute('data-bs-theme',t||(window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))})();
</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDescription) ?>">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?= e($canonicalUrl) ?>">

<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDescription) ?>">
<meta property="og:url" content="<?= e($canonicalUrl) ?>">
<meta property="og:site_name" content="<?= SITE_NAME ?>">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($pageTitle) ?>">
<meta name="twitter:description" content="<?= e($pageDescription) ?>">
<meta name="theme-color" content="#ff6300">

<!-- Bootstrap 5.3 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- Flag Icons -->
<link href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" rel="stylesheet">
<!-- Custom -->
<link href="/assets/css/style.css" rel="stylesheet">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

<?php if (isset($extraHead)) echo $extraHead; ?>

<!-- Google Analytics / Statcounter -->
<script type="text/javascript">var sc_project=2047147;var sc_invisible=1;var sc_security="b29b251f";</script>
<script type="text/javascript" src="https://www.statcounter.com/counter/counter.js" async></script>
</head>
<body>

<?php include __DIR__ . '/navbar.php'; ?>

<main>
