<?php
// api_chat.php

require_once $_SERVER['DOCUMENT_ROOT'] . 'config.inc.php'; // API 키와 기타 설정을 포함하는 파일
require_once $_SERVER['DOCUMENT_ROOT'] . 'anthropic.php'; // 이전에 만든 Anthropic 클래스 파일

header('Content-Type: application/json');

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = $input['message'] ?? '';

    if (empty($userMessage)) {
        http_response_code(400);
        echo json_encode(['error' => 'No message provided']);
        exit;
    }

    try {
        $anthropic = new Anthropic(ANTHROPIC_API_KEY);
        $response = $anthropic->createMessage(
            'claude-3-sonnet-20240320',
            1024,
            [['role' => 'user', 'content' => $userMessage]]
        );

        // API 응답에서 필요한 정보 추출
        $assistantMessage = $response['content'][0]['text'] ?? 'Sorry, I couldn\'t generate a response.';

        echo json_encode(['response' => $assistantMessage]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>