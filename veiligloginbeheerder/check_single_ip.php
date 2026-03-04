<?php
require '../db.php';


// Function to check IP and port
function checkPort($host, $port, $timeout = 4) {
    $connection = @stream_socket_client("tcp://$host:$port", $errno, $errstr, $timeout);
    if ($connection) {
        fclose($connection);
        return true; // Port is open
    } else {
        return false; // Port is closed or unreachable
    }
}

// Function to check HTTP response
function checkHttpRequest($host, $port, $timeout = 4) {
    $url = "http://$host:$port"; // Assuming HTTP, update to HTTPS if needed

    // Use cURL to make the HTTP request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, true); // We only care about the headers
    curl_setopt($ch, CURLOPT_NOBODY, true); // We don't need the body
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // If we receive a valid HTTP status code, the server is online
    if ($httpCode >= 200 && $httpCode < 400) {
        return 'online (HTTP ' . $httpCode . ')'; // HTTP is responding with a valid status code
    } else {
        return 'HTTP error (' . $httpCode . ')'; // HTTP request failed or returned an error code
    }
}

// Main function to check the IP with HTTP request first and fallback to port check
function checkServiceStatus($ipwithport, $timeout = 4) {
    list($host, $port) = explode(':', $ipwithport);

    // First try HTTP request
    $httpStatus = checkHttpRequest($host, $port, $timeout);
    if (strpos($httpStatus, 'online') !== false) {
        return $httpStatus; // HTTP request successful
    }

    // Fallback to checking if the port is open
    if (checkPort($host, $port, $timeout)) {
        return 'online (port open)'; // Port is open but HTTP failed
    } 
    
    return 'offline'; // Neither HTTP nor port check succeeded
}

// Handle the single IP check request
if (isset($_GET['ip'])) {
    // Ensure output is clean
    header('Content-Type: application/json');

    // Fetch the IP with port from the query string
    $ipwithport = $_GET['ip'];

    // Get the service status
    $status = checkServiceStatus($ipwithport);

    // Return JSON response with the status
    echo json_encode(['status' => $status]);

    // Exit script to prevent any other output
    exit;
} else {
    // Handle cases where no IP was provided
    echo json_encode(['error' => 'No IP provided']);
    exit;
}
