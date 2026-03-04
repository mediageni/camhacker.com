<?php
// PhantomJS Cloud API Key (replace with your own)
$apiKey = "ak-09d62-gsmvf-9thmk-ky154-xxjtw";

// The URL of the page to fetch
$url = "http://212.67.236.61:80";

// PhantomJS Cloud API request URL
$requestUrl = "https://phantomjscloud.com/api/browser/v2/$apiKey/";


// JSON payload to configure the request
$payload = json_encode([
    "url" => $url,
    "renderType" => "html",  // You can request "html" or "plainText"
    "requestSettings" => [
        "waitInterval" => 6000,  // Wait for 6 seconds to allow JavaScript to execute
        "renderSettings" => [
            "outputAsJson" => false  // Return the HTML content
        ]
    ]
]);

// Make the POST request to PhantomJS Cloud API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $requestUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// Get the response from PhantomJS Cloud
$response = curl_exec($ch);
curl_close($ch);

// Check if we received a valid response
if ($response === false) {
    echo "Failed to fetch the webpage.";
    exit;
}

// Debugging: Print the full HTML response to see if the title is there
echo "Full HTML Response:<br><pre>" . htmlspecialchars($response) . "</pre>";

// Extract the title from the returned HTML
if (preg_match('/<title>(.*?)<\/title>/', $response, $matches)) {
    $title = $matches[1];
    echo "The title is: " . $title;
} else {
    echo "Title not found.";
}
?>
