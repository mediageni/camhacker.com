<?php
// Function to get the base URL dynamically
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // Remove any trailing slash
    return $protocol . $host . $script;
}

// Function to generate the full URL of the current page
function getCurrentUrl() {
    $baseUrl = getBaseUrl();
    $requestUri = $_SERVER['REQUEST_URI'];
    return $baseUrl . $requestUri;
}

// Function to capitalize first letter of each word
function ucwords_custom($string) {
    return ucwords(strtolower($string));
}

// Handle AJAX request for updating city dropdown
if (isset($_GET['ajax'])) {
    if ($_GET['ajax'] === 'get_cities' && isset($_GET['country'])) {
        $country = $_GET['country'];
        $cities = getCitiesByCountry($conn, $country);
        echo json_encode($cities);
        exit;
    } elseif ($_GET['ajax'] === 'get_all_cities') {
        $cities = getDistinctValues($conn, 'city');
        echo json_encode($cities);
        exit;
    }
}
// Function to generate SEO-friendly URLs
function generateSeoUrl($type, $value) {
    $baseUrl = getBaseUrl();
    $value = urlencode(strtolower($value)); // Make the value lowercase and URL-encoded

    switch ($type) {
        case 'country':
            return $baseUrl . '/country/' . $value;
        case 'city':
            return $baseUrl . '/city/' . $value;
        case 'place':
            return $baseUrl . '/place/' . $value;
        case 'manufacturer':
            return $baseUrl . '/manufacturer/' . $value;
        case 'cam':
            return $baseUrl . '/view/' . $value; // Generate URL for cam detail page
        default:
            return $baseUrl; // Fallback to base URL if type is unknown
    }
}

