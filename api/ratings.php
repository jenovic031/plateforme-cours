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
$rating = (int)($_POST['rating'] ?? 0);

if ($courseId <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

if (addRating($courseId, $_SESSION['user_id'], $rating)) {
    echo json_encode(['success' => true, 'message' => 'Rating added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add rating']);
}
?>
