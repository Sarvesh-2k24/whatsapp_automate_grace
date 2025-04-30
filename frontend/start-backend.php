<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$batFile = realpath(__DIR__ . '/../start.bat');
if (!file_exists($batFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Start.bat not found']);
    exit;
}

$output = [];
$returnVar = 0;
exec("start /B /MIN \"\" $batFile", $output, $returnVar);

if ($returnVar !== 0) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to start backend']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Backend started successfully']);
?>