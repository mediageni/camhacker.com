<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
<script src="/assets/js/color-modes.js"></script>
<?php

$baseUrl = getBaseUrl();
$currentUrl = getCurrentUrl();

// Initialize dynamic variables from filters passed by search.php
$search = isset($filters['search']) ? $filters['search'] : '';
$country = isset($filters['country']) ? $filters['country'] : '';
$city = isset($filters['city']) ? $filters['city'] : '';
$manufacturer = isset($filters['manufacturer']) ? $filters['manufacturer'] : '';
$tag = isset($filters['tag']) ? $filters['tag'] : '';

// Build dynamic title based on search filters
$dynamicTitle = 'Unsecured Live Streaming Public IP Webcams'; // Default title

$elements = [];

if ($tag) {
    $elements[] = ucwords_custom($tag);
}
if ($search) {
    $elements[] = ucwords_custom($search);
}
if ($manufacturer) {
    $elements[] = ucwords_custom($manufacturer);
}

if (!empty($elements)) {
    $dynamicTitle = 'Live ' . implode(' ', $elements) . ' Webcams';
}

// Only add "in" if city or country is present
if ($city || $country) {
    $dynamicTitle .= ' in ';
    if ($city) {
        $dynamicTitle .= ucwords_custom($city);
    }
    if ($city && $country) {
        $dynamicTitle .= ', ';
    }
    if ($country) {
        $dynamicTitle .= ucwords_custom($country);
    }
}

// Build dynamic description similarly
$dynamicDescription = 'Unprotected hidden live streaming webcams found';

if ($city || $country) {
    $dynamicDescription .= ' in ';
    if ($city) {
        $dynamicDescription .= ucwords_custom($city);
    }
    if ($city && $country) {
        $dynamicDescription .= ', ';
    }
    if ($country) {
        $dynamicDescription .= ucwords_custom($country);
    }
}
$dynamicDescription .= '.';

$currentUrl = isset($currentUrl) ? $currentUrl : ''; 
?>

<link rel="canonical" href="<?php echo $currentUrl; ?>">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo htmlspecialchars($dynamicDescription); ?>">
<meta name="robots" content="index, follow">

<meta property="og:title" content="<?php echo htmlspecialchars($dynamicTitle); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($dynamicDescription); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($currentUrl); ?>">
<meta property="og:image" content="path_to_default_image_if_any.jpg"> 

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($dynamicTitle); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($dynamicDescription); ?>">
<meta name="theme-color" content="#ff9100">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link href="/assets/css/app.css" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<?php include 'google.php'; ?>