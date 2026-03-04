<?php
require '../db.php';

// Function to check IP and port using stream_socket_client
function checkPort($host, $port, $timeout = 5) {
    $connection = @stream_socket_client("tcp://$host:$port", $errno, $errstr, $timeout);
    if ($connection) {
        fclose($connection);
        return true; // Port is open
    } else {
        // Log any errors for debugging purposes (optional)
        // error_log("Port check failed for $host:$port - $errstr ($errno)");
        return false; // Port is closed or unreachable
    }
}

// Function to check HTTP response using a HEAD request via cURL
function checkHttpRequest($host, $port, $timeout = 5) {
    $url = "http://$host:$port"; // Assuming HTTP

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request (no body)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); // We only care about the headers

    // Execute cURL request
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        // If cURL error occurs, log it (optional) and return false
        // error_log('cURL error for ' . $url . ': ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    // Get the HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for valid HTTP status code (200 range)
    if ($httpCode >= 200 && $httpCode < 400) {
        return 'online (HTTP ' . $httpCode . ')'; // HTTP is responding with a valid status code
    } else {
        return false; // HTTP request failed or returned an error code
    }
}

// Main function to check the IP with HTTP request first and fallback to port check
function checkServiceStatus($ipwithport, $timeout = 5) {
    list($host, $port) = explode(':', $ipwithport);

    // First try HTTP HEAD request
    $httpStatus = checkHttpRequest($host, $port, $timeout);
    if ($httpStatus) {
        return $httpStatus; // HTTP request successful
    }

    // Fallback to checking if the port is open using stream_socket_client
    if (checkPort($host, $port, $timeout)) {
        return 'online (port open)'; // Port is open but HTTP check failed
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
    exit;
} else {
    // Handle cases where no IP was provided
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'No IP provided']);
    exit;
}
