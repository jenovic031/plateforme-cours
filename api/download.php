<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$courseId = (int)($_GET['id'] ?? 0);

if ($courseId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid course ID']);
    exit;
}

$course = getCourseById($courseId);

if (!$course || $course['status'] !== 'approved') {
    http_response_code(404);
    echo json_encode(['error' => 'Course not found']);
    exit;
}

// Vérifier si le fichier existe
if (!file_exists($course['file_path'])) {
    http_response_code(404);
    echo json_encode(['error' => 'File not found']);
    exit;
}

// Incrémenter les téléchargements
incrementDownloads($courseId);
recordDownload($courseId, $_SESSION['user_id']);

// Télécharger le fichier
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($course['file_path']) . '"');
header('Content-Length: ' . filesize($course['file_path']));
readfile($course['file_path']);
exit;
?>
