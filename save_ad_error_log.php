<?php
// 에러 로그를 저장할 디렉토리 설정
$logDir = __DIR__ . '/error_logs';

// 디렉토리가 없으면 생성
if (!file_exists($logDir)) {
    if (!mkdir($logDir, 0755, true)) {
        error_log("Failed to create directory: $logDir");
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to create log directory']);
        exit;
    }
}

// POST 데이터 받기
$postData = json_decode(file_get_contents('php://input'), true);

// 로그 파일명 설정 (예: error_log_2024-06-28.txt)
$logFile = $logDir . '/error_log_' . date('Y-m-d') . '.txt';

// 로그 메시지 생성
$logMessage = date('Y-m-d H:i:s') . " - ";
$logMessage .= "mt_idx: " . ($postData['mt_idx'] ?? 'N/A') . ", ";
$logMessage .= "Error: " . ($postData['error_message'] ?? 'N/A') . ", ";
$logMessage .= "Stack: " . ($postData['error_stack'] ?? 'N/A') . ", ";
$logMessage .= "User Agent: " . ($postData['user_agent'] ?? 'N/A') . ", ";
$logMessage .= "Platform: " . ($postData['platform'] ?? 'N/A') . "\n";

// 파일에 로그 추가
$result = file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

if ($result !== false) {
    echo json_encode(['status' => 'success', 'message' => 'Error log saved successfully']);
} else {
    error_log("Failed to write to log file: $logFile");
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save error log']);
}
?>