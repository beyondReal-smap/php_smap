<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

class CloudMailer
{
    private $accessKey;
    private $secretKey;
    private $apiEndpoint;

    public function __construct($accessKey, $secretKey, $region = 'KR')
    {
        global $logger;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;

        $logger->write("accessKey : " . $this->accessKey);
        $logger->write("secretKey : " . $this->secretKey);

        $endpoints = [
            'KR' => 'https://mail.apigw.ntruss.com/api/v1',
            'SGN' => 'https://mail.apigw.ntruss.com/api/v1-sgn',
            'JPN' => 'https://mail.apigw.ntruss.com/api/v1-jpn'
        ];
        $this->apiEndpoint = $endpoints[$region] ?? $endpoints['KR'];
    }

    private function makeSignature($timestamp, $method, $uri)
    {
        $space = " ";
        $newLine = "\n";
        $message = $method . $space . $uri . $newLine . $timestamp . $newLine . $this->accessKey;
        return base64_encode(hash_hmac('sha256', $message, $this->secretKey, true));
    }

    public function createMailRequest($params)
    {
        global $logger; // $logger 객체가 전역으로 선언되어 있다고 가정합니다.

        $logger->write("Step 1: 메일 요청 생성 시작");

        $timestamp = round(microtime(true) * 1000);
        $logger->write("Step 2: 타임스탬프 생성 완료 - {$timestamp}");

        $method = "POST";
        $uri = "/api/v1/mails";

        $logger->write("Step 3: 서명 생성 시작");
        $signature = $this->makeSignature($timestamp, $method, $uri);
        $logger->write("Step 4: 서명 생성 완료 - {$signature}");

        $headers = [
            "Content-Type: application/json",
            "x-ncp-apigw-timestamp: {$timestamp}",
            "x-ncp-iam-access-key: {$this->accessKey}",
            "x-ncp-apigw-signature-v2: {$signature}"
        ];

        $logger->write("Request Headers: " . print_r($headers, true));
        $logger->write("Request Body: " . json_encode($params));

        $logger->write("Step 5: cURL 초기화 시작");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiEndpoint . "/mails");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //원격 서버의 인증서가 유효한지 검사 안함
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //요청 결과를 문자열로 반환
        curl_setopt($ch, CURLOPT_POST, true); //true시 post 전송
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params)); //POST data
        $logger->write("Step 6: cURL 설정 완료");
        

        $logger->write("Step 7: 메일 요청 전송 시작");
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $logger->write("Step 8: 메일 요청 전송 완료 - HTTP 코드: {$httpCode}");

        curl_close($ch);
        $logger->write("Step 9: cURL 종료");

        $logger->write("Full Response: " . $response);

        echo json_encode($httpCode);
    }
}

function generateResetPasswordToken($userId) {
    $randomString = bin2hex(random_bytes(32)); // 랜덤 문자열 생성
    $token = hash('sha256', $randomString . $userId); // 해싱하여 토큰 생성
    return $token;
}

function saveResetPasswordToken($userId, $token) {
    global $DB; // 데이터베이스 연결 객체

    $expirationTime = date('Y-m-d H:i:s', strtotime('+24 hours')); // 24시간 후 만료 시간

    $data = [
        'mt_reset_token' => $token,
        'mt_token_edate' => $expirationTime
    ];

    // member_t 테이블에서 mt_email을 키로 찾아서 데이터 업데이트
    $DB->where('mt_email', $userId);
    $DB->update('member_t', $data); 
}

function getUserNameOrNickname($email) {
    global $DB; // 데이터베이스 연결 객체

    // member_t 테이블에서 mt_email을 키로 mt_name과 mt_nickname을 가져옴
    $DB->where('mt_email', $email);
    $user = $DB->getOne('member_t', ['mt_name', 'mt_nickname']);

    // mt_name이 비어있으면 mt_nickname을 반환, 그렇지 않으면 mt_name을 반환
    if (!empty($user['mt_name'])) {
        return $user['mt_name'];
    } else {
        return $user['mt_nickname'];
    }
}

// 사용 예시
$accessKey = NCP_ACCESS_KEY;
$secretKey = NCP_SECRET_KEY;

// 비밀번호 재설정 토큰 생성 및 저장
$token = generateResetPasswordToken($_POST['mt_email']);
saveResetPasswordToken($_POST['mt_email'], $token);


$userName = getUserNameOrNickname($_POST['mt_email']);
$head = "<!DOCTYPE html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SMAP 비밀번호 재설정</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0046FE;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: #0046FE;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0035C9;
        }
        .note {
            font-size: 0.9em;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>";
$title = $translations['txt_reset_password_title'];
$body = $translations['txt_reset_password_content'];
$body = str_replace('{domain}', APP_DOMAIN, $body);
$body = str_replace('{token}', $token, $body);


$mailer = new CloudMailer($accessKey, $secretKey);
$mailParams = [
    "senderAddress" => "admin@smap.site",
    "senderName" => "SMAP Admin",
    "title" => $title,
    "body" => $head . $body, 
    "recipients" => [
        [
            "address" => $_POST['mt_email'],
            "name" => $userName,
            "type" => "R"
        ]
    ],
    "isHtml" => true // HTML 메일임을 명시
];

$result = $mailer->createMailRequest($mailParams);