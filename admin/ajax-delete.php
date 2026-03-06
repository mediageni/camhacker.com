<?php
error_reporting(E_ALL & ~E_DEPRECATED);
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    exit(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');

$ids = json_decode($_POST['ids'] ?? '[]', true);
if (!is_array($ids) || empty($ids)) {
    echo json_encode(['ok' => false, 'message' => 'No IDs provided']);
    exit;
}

$db = CamDatabase::getInstance();
$deleted = 0;
foreach ($ids as $id) {
    $db->delete((int)$id);
    $deleted++;
}

echo json_encode(['ok' => true, 'deleted' => $deleted]);
