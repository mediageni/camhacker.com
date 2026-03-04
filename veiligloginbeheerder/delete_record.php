<?php
require '../db.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Prepare SQL to delete the record by ID
    $stmt = $conn->prepare("DELETE FROM webcams WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // If deletion is successful, return success response
        echo json_encode(['success' => true]);
    } else {
        // If deletion fails, return failure response
        echo json_encode(['success' => false]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
