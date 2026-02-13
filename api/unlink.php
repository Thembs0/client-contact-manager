<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$clientId = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
$contactId = isset($_POST['contact_id']) ? (int)$_POST['contact_id'] : 0;

if (!$clientId || !$contactId) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("DELETE FROM client_contact WHERE client_id = ? AND contact_id = ?");
    $success = $stmt->execute([$clientId, $contactId]);
    
    echo json_encode(['success' => $success]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>