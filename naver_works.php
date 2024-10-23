<?php
session_start();
require_once 'vendor/autoload.php';
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Configuration
$client_id = 'CdQ7PtdKO9q6O0lntuHn';
$client_secret = 'H1H_p3J18N';
$service_account = 'ueff3.serviceaccount@smap.site';
$private_key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/key/naver_works_private.key');

// Create JWT
function createJWT($client_id, $service_account, $private_key)
{
    $current_time = time();
    $expire_time = $current_time + 3600; // Token expires in 1 hour

    $payload = [
        "iss" => $client_id,
        "sub" => $service_account,
        "iat" => $current_time,
        "exp" => $expire_time
    ];

    return JWT::encode($payload, $private_key, 'RS256');
}

// Request access token
function requestAccessToken($client_id, $client_secret, $jwt, $scope)
{
    $client = new Client(['base_uri' => 'https://auth.worksmobile.com']);

    $response = $client->request('POST', '/oauth2/v2.0/token', [
        'form_params' => [
            'assertion' => $jwt,
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'scope' => $scope
        ]
    ]);

    return json_decode($response->getBody(), true);
}
function sendMail($access_token, $to, $subject, $body, $attachments = [])
{
    $client = new Client(['base_uri' => 'https://www.worksapis.com']);

    $data = [
        'to' => $to,
        'subject' => $subject,
        'body' => $body,
        'contentType' => 'html',
        'userName' => 'Sender Name',
        'isSaveSentMail' => true,
        'isSaveTracking' => true,
        'isSendSeparately' => false,
    ];

    if (!empty($attachments)) {
        $data['attachments'] = $attachments;
    }

    try {
        $response = $client->request('POST', '/v1.0/users/me/mail', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        return [
            'success' => true,
            'status' => $response->getStatusCode(),
            'message' => 'Mail sent successfully',
        ];
    } catch (RequestException $e) {
        return [
            'success' => false,
            'status' => $e->getCode(),
            'message' => $e->getMessage(),
        ];
    }
}

// Main execution
try {
    $logger->write("Step 1: JWT 생성 시작");
    // Generate JWT
    $jwt = createJWT($client_id, $service_account, $private_key);
    $logger->write("Step 1: JWT 생성 완료");

    $logger->write("Step 2: 액세스 토큰 요청 시작");
    // Request access token
    $scope = 'mail email'; // Adjust scope as needed
    $token_response = requestAccessToken($client_id, $client_secret, $jwt, $scope);
    $logger->write("Step 2: 액세스 토큰 요청 완료");
    // Use the access token
    $access_token = $token_response['access_token'];
    $logger->write("Step 3: 액세스 토큰 사용 시작");
    echo "Access Token: " . $access_token;
    $logger->write("Step 3: 액세스 토큰 사용 완료" . $access_token);

    // 사용 예시
    $to = 'bluemusk@gmail.com';
    $subject = '테스트 메일';
    $body = '<h1>안녕하세요!</h1><p>이것은 NAVER WORKS API를 통해 보낸 테스트 메일입니다.</p>';
    $logger->write("메일 정보 설정 완료: 수신자 - $to, 제목 - $subject");

    // 첨부 파일 예시 (선택사항)
    $attachments = [
        [
            'filename' => 'test.txt',
            'fileType' => 'text/plain',
            'data' => base64_encode('This is a test file content.'),
        ],
    ];
    $logger->write("첨부 파일 설정 완료: " . json_encode($attachments));

    $result = sendMail($access_token, $to, $subject, $body, $attachments);
    $logger->write("메일 전송 시도 완료");

    if ($result['success']) {
        echo "Mail sent successfully. Status code: " . $result['status'];
        $logger->write("메일 전송 성공: 상태 코드 - " . $result['status']);
    } else {
        echo "Failed to send mail. Error: " . $result['message'];
        $logger->write("메일 전송 실패: 오류 메시지 - " . $result['message']);
    }
} catch (Exception $e) {
    $logger->write("Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
