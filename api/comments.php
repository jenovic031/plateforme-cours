<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$courseId = (int)($_POST['course_id'] ?? 0);
$text = cleanInput($_POST['text'] ?? '');

if ($courseId <= 0 || empty($text)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

if (addComment($courseId, $_SESSION['user_id'], $text)) {
    echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add comment']);
}
?>
