<?php
require 'db.php';

// Disable STRICT_TRANS_TABLES for this session (optional, can be removed if not needed)
$conn->query("SET SESSION sql_mode = ''");

$inputFileName = 'mnt/data/cams.csv'; 

if (($handle = fopen($inputFileName, 'r')) !== FALSE) {
    $headers = fgetcsv($handle, 1000, ","); // Skip header row

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $id = (string) $data[0];  // New id column (first column in the CSV)
        $source_url = (string) $data[1];
        $cam_url = (string) $data[2];
        $cam_stream = (string) $data[3];
        $ip_with_port = (string) $data[4];
        $ip2location = (string) $data[5];
        $latitude = floatval($data[6]);
        $longitude = floatval($data[7]);
        $country_code = (string) $data[8];  // Explicitly cast to string
        $country = (string) $data[9];
        $state = (string) $data[10];
        $city = (string) $data[11];
        $zipcode = (string) $data[12];
        $timezone = trim((string) $data[13]);
        $cam_fixed_url = (string) $data[14];
        $cam_ip = (string) $data[15];
        $image_jpeg = (string) $data[16];
        $camera_model = (string) $data[17];
        $image_jpg_url = (string) $data[18];
        $image_url_full = (string) $data[19];
        $live_webcam_stream = (string) $data[20];
        $manufacturer = (string) $data[21];
        $tag = (string) $data[22];
        $title_seo = (string) $data[23];
        $description_seo = (string) $data[24];
        $view_count = (int) $data[25];  // New column for view count

        // Log the data to ensure it's being processed correctly
        error_log("Inserting row with ID: $id, country_code: $country_code, source_url: $source_url, view_count: $view_count");

        // Manual SQL insert for debugging
        $sql_manual = "INSERT INTO webcams (
            id, source_url, cam_url, cam_stream, ipwithport, ip2location, latitude, longitude, country_code, country, 
            state, city, zipcode, timezone, cam_fixed_url, cam_ip, image_jpeg, camera_model, image_jpg_url, 
            image_url_full, live_webcam_stream, manufacturer, tag, title_seo, description_seo, view_count) 
            VALUES ('$id', '$source_url', '$cam_url', '$cam_stream', '$ip_with_port', '$ip2location', 
            $latitude, $longitude, '$country_code', '$country', '$state', '$city', '$zipcode', 
            '$timezone', '$cam_fixed_url', '$cam_ip', '$image_jpeg', '$camera_model', '$image_jpg_url', 
            '$image_url_full', '$live_webcam_stream', '$manufacturer', '$tag', '$title_seo', '$description_seo', '$view_count')";

        if ($conn->query($sql_manual) === TRUE) {
            echo "Row inserted successfully<br>";
        } else {
            echo "Error: " . $conn->error . "<br>";
        }
    }

    fclose($handle);
    echo "Data imported successfully.";
} else {
    echo "Failed to open the CSV file.";
}

$conn->close();
?>
