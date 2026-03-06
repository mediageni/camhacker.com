<?php
error_reporting(E_ALL & ~E_DEPRECATED);
session_start();
require_once __DIR__ . '/../includes/config.php';

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    exit(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');

$url = $_POST['url'] ?? '';
if (empty($url)) {
    echo json_encode(['status' => 'error', 'message' => 'No URL']);
    exit;
}

// Try to fetch just the headers with a short timeout
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_NOBODY => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_CONNECTTIMEOUT => 4,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 3,
    CURLOPT_USERAGENT => 'Mozilla/5.0',
]);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$totalTime = round(curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000);

if ($httpCode === 200) {
    echo json_encode(['status' => 'online', 'code' => $httpCode, 'time' => $totalTime]);
} elseif ($httpCode > 0) {
    echo json_encode(['status' => 'error', 'code' => $httpCode, 'time' => $totalTime]);
} else {
    echo json_encode(['status' => 'offline', 'code' => 0, 'error' => $error, 'time' => $totalTime]);
}
