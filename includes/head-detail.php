<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
<script src="/assets/js/color-modes.js"></script>
<?php

$baseUrl = getBaseUrl();
$currentUrl = getCurrentUrl();

// Initialize dynamic variables from filters passed by search
$search = isset($filters['search']) ? $filters['search'] : '';
$country = isset($filters['country']) ? $filters['country'] : '';
$city = isset($filters['city']) ? $filters['city'] : '';
$manufacturer = isset($filters['manufacturer']) ? $filters['manufacturer'] : '';
$tag = isset($filters['tag']) ? $filters['tag'] : '';


$currentUrl = isset($currentUrl) ? $currentUrl : '';
?>

<link rel="canonical" href="<?php echo $currentUrl; ?>">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo htmlspecialchars($webcam['description_seo']); ?>">
<meta name="robots" content="index, follow">

<meta property="og:title" content="<?php echo htmlspecialchars($webcam['title_seo']); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($webcam['description_seo']); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($currentUrl); ?>">
<meta property="og:image" content="https://wsrv.nl/?url=<?php echo urlencode($webcam['image_url_full']); ?>">
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:video:type" content="text/html"/>
<meta property="og:site_name" content="Camhacker" />

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($webcam['title_seo']); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($webcam['description_seo']); ?>">
<meta name="theme-color" content="#ff9100">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link href="/assets/css/app.css" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<?php include 'google.php'; ?>