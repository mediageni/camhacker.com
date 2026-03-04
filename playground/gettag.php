<?php

$image_url = 'https://wsrv.nl/?url=http://62.40.153.122:8000/jpg/image.jpg';
$api_credentials = array(
    'key' => 'acc_a696e62e536e7ea',
    'secret' => 'aa3db6e2410b0f413902585efe9a1d7e'
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.imagga.com/v2/tags?image_url='.$image_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_USERPWD, $api_credentials['key'].':'.$api_credentials['secret']);

$response = curl_exec($ch);
curl_close($ch);

$json_response = json_decode($response, true); // Decode as an associative array

// Get the image data as a blob (binary data)
$image_blob = file_get_contents($image_url);
$base64_blob = base64_encode($image_blob); // Convert binary data to base64
$mime_type = 'image/jpeg'; // You can adjust this if you know the exact type of the image

?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Recognition Tags</title>
</head>
<body>

<!-- Display the image from the URL -->
<h2>Image from URL:</h2>
<img src="<?php echo $image_url; ?>" alt="Recognized Image" style="max-width: 640px; max-height: 480px;">

<!-- Display the image as a blob -->
<h2>Image Blob:</h2>
<img src="data:<?php echo $mime_type; ?>;base64,<?php echo $base64_blob; ?>" alt="Blob Image" style="max-width: 640px; max-height: 480px;">

<!-- Output the tags -->
<h2>Top 5 Tags:</h2>
<?php
// Check if the response contains the 'tags' array
if (isset($json_response['result']['tags'])) {
    $tags = $json_response['result']['tags'];
    
    // Sort the tags by confidence in descending order
    usort($tags, function($a, $b) {
        return $b['confidence'] <=> $a['confidence'];
    });
    
    // Slice the top 5 tags
    $top_tags = array_slice($tags, 0, 5);
    
    // Display the top 5 tags
    echo "<ul>";
    foreach ($top_tags as $tag) {
        echo "<li>Tag: " . $tag['tag']['en'] . " - Confidence: " . $tag['confidence'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No tags found in the response.</p>";
}
?>

</body>
</html>
