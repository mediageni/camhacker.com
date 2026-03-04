<?php
require 'db.php';

// Disable STRICT_TRANS_TABLES for this session (optional, can be removed if not needed)
$conn->query("SET SESSION sql_mode = ''");

$inputFileName = 'mnt/data/cams.csv'; 

if (($handle = fopen($inputFileName, 'r')) !== FALSE) {
    $headers = fgetcsv($handle, 1000, ",");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $source_url = (string) $data[0];
        $cam_url = (string) $data[1];
        $cam_stream = (string) $data[2];
        $ip_with_port = (string) $data[3];
        $ip2location = (string) $data[4];
        $latitude = floatval($data[5]);
        $longitude = floatval($data[6]);
        $country_code = (string) $data[7];  // Explicitly cast to string
        $country = (string) $data[8];
        $state = (string) $data[9];
        $city = (string) $data[10];
        $zipcode = (string) $data[11];
        $timezone = trim((string) $data[12]);
        $cam_fixed_url = (string) $data[13];
        $cam_ip = (string) $data[14];
        $image_jpeg = (string) $data[15];
        $camera_model = (string) $data[16];
        $image_jpg_url = (string) $data[17];
        $image_url_full = (string) $data[18];
        $live_webcam_stream = (string) $data[19];
        $manufacturer = (string) $data[20];
        $tag = (string) $data[21];
        $title_seo = (string) $data[22];
        $description_seo = (string) $data[23];

        // Log the data to ensure it's being processed correctly
        error_log("Inserting row with country_code: $country_code, source_url: $source_url");

        // Manual SQL insert for debugging
        $sql_manual = "INSERT INTO webcams (
            source_url, cam_url, cam_stream, ipwithport, ip2location, latitude, longitude, country_code, country, 
            state, city, zipcode, timezone, cam_fixed_url, cam_ip, image_jpeg, camera_model, image_jpg_url, 
            image_url_full, live_webcam_stream, manufacturer, tag, title_seo, description_seo) 
            VALUES ('$source_url', '$cam_url', '$cam_stream', '$ip_with_port', '$ip2location', 
            $latitude, $longitude, '$country_code', '$country', '$state', '$city', '$zipcode', 
            '$timezone', '$cam_fixed_url', '$cam_ip', '$image_jpeg', '$camera_model', '$image_jpg_url', 
            '$image_url_full', '$live_webcam_stream', '$manufacturer', '$tag', '$title_seo', '$description_seo')";

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
