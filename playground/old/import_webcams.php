<?php
require 'db.php';

// Disable STRICT_TRANS_TABLES for this session
$conn->query("SET SESSION sql_mode = ''");

$inputFileName = 'mnt/data/cams.csv'; 

if (($handle = fopen($inputFileName, 'r')) !== FALSE) {
    $headers = fgetcsv($handle, 1000, ",");

    $sql = "INSERT INTO webcams (
        source_url, cam_url, cam_stream, ipwithport, ip2location, latitude, longitude, country_code, country, 
        state, city, zipcode, timezone, cam_fixed_url, cam_ip, image_jpeg, camera_model, image_jpg_url, 
        image_url_full, live_webcam_stream, manufacturer, tag, title_seo, description_seo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Statement preparation failed: ' . $conn->error);
    }

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $source_url = $data[0];
        $cam_url = $data[1];
        $cam_stream = $data[2];
        $ip_with_port = $data[3];
        $ip2location = $data[4];
        $latitude = floatval($data[5]);
        $longitude = floatval($data[6]);
        $country_code = (string) $data[7];  // Explicitly cast country_code to string
        $country = $data[8];
        $state = $data[9];
        $city = $data[10];
        $zipcode = $data[11];
        $timezone = trim($data[12]);
        $cam_fixed_url = $data[13];
        $cam_ip = $data[14];
        $image_jpeg = $data[15];
        $camera_model = $data[16];
        $image_jpg_url = $data[17];
        $image_url_full = $data[18];
        $live_webcam_stream = $data[19];
        $manufacturer = $data[20];
        $tag = $data[21];
        $title_seo = $data[22];
        $description_seo = $data[23];

        // Ensure that all string fields are correctly identified in bind_param()
        $stmt->bind_param(
            'ssssssddssssssssssssssss', 
            $source_url,
            $cam_url,
            $cam_stream,
            $ip_with_port,
            $ip2location,
            $latitude,
            $longitude,
            $country_code,  // Correctly bind this as a string
            $country,
            $state,
            $city,
            $zipcode,
            $timezone,
            $cam_fixed_url,
            $cam_ip,
            $image_jpeg,
            $camera_model,
            $image_jpg_url,
            $image_url_full,
            $live_webcam_stream,
            $manufacturer,
            $tag,
            $title_seo,
            $description_seo
        );

        if (!$stmt->execute()) {
            die('Failed to insert row: ' . $conn->error);
        }
    }

    fclose($handle);
    echo "Data imported successfully.";
} else {
    echo "Failed to open the CSV file.";
}

$stmt->close();
$conn->close();
?>
