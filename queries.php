<?php

// Function to build the search query
function buildSearchQuery($conn, $filters, $results_per_page, $offset) {
    // Extract filters
    $search = $filters['search'] ?? '';
    $country = $filters['country'] ?? '';
    $city = $filters['city'] ?? '';
    $manufacturer = $filters['manufacturer'] ?? '';
    $tag = $filters['tag'] ?? '';  // Explicitly extract the 'tag' filter

    // Base SQL query
    $sql = "SELECT id, source_url, cam_url, cam_stream, ipwithport, latitude, longitude, country, 
            city, manufacturer, title_seo, state, zipcode, image_url_full, tag, view_count 
            FROM webcams 
            WHERE 1=1";

    $params = [];
    $types = '';

    // Free-text search query
    if ($search) {
        $sql .= " AND (title_seo LIKE ? OR country LIKE ? OR manufacturer LIKE ? OR city LIKE ? OR state LIKE ? OR tag LIKE ? OR zipcode LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sssssss';
    }

    // Dropdown filters
    if ($country) {
        $sql .= " AND country = ?";
        $params[] = $country;
        $types .= 's';
    }

    if ($city) {
        $sql .= " AND city = ?";
        $params[] = $city;
        $types .= 's';
    }

    if ($manufacturer) {
        $sql .= " AND manufacturer = ?";
        $params[] = $manufacturer;
        $types .= 's';
    }

    // Handle 'tag' as a filter if it's present
    if ($tag) {
        $sql .= " AND tag = ?";
        $params[] = $tag;
        $types .= 's';
    }

    // Count total results for pagination
    $count_sql = "SELECT COUNT(*) FROM (" . $sql . ") AS total_query";
    $count_stmt = $conn->prepare($count_sql);
    if ($params) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $count_stmt->bind_result($total_results);
    $count_stmt->fetch();
    $count_stmt->close();

    // Add limit for pagination
    $sql .= " LIMIT ?, ?";
    $types .= 'ii';
    $params[] = $offset;
    $params[] = $results_per_page;

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    return ['stmt' => $stmt, 'total_results' => $total_results];
}

// Function to fetch distinct values from a column for dropdowns
function getDistinctValues($conn, $field) {
    $sql = "SELECT DISTINCT $field FROM webcams WHERE $field IS NOT NULL AND $field != '' ORDER BY $field ASC";
    $result = $conn->query($sql);
    $values = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $values[] = $row[$field];
        }
    }
    return $values;
}

// Fetch cities by country (for dynamic city dropdowns)
function getCitiesByCountry($conn, $country) {
    $sql = "SELECT DISTINCT city FROM webcams WHERE country = ? AND city IS NOT NULL AND city != '' ORDER BY city ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $country);
    $stmt->execute();
    $result = $stmt->get_result();
    $cities = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cities[] = $row['city'];
        }
    }
    $stmt->close();
    return $cities;
}
?>
