<?php
include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

// JSON 키 파일의 경로
$serviceAccountEmail = 'beyondrealsmap@gmail.com';
$serviceAccountKeyFile = $_SERVER['DOCUMENT_ROOT'] . '/com-dmonster-smap-firebase-adminsdk-2zx5p-2610556cf5.json'; // 서비스 계정의 JSON 키 파일 경로를 설정합니다.

// 서비스 객체 생성
$client = new Google_Client();
$client->setApplicationName('SMAP');
$client->setAuthConfig($serviceAccountKeyFile);
$client->setScopes(['https://www.googleapis.com/auth/androidpublisher']);

// Google Play Developer API에 연결
$androidPublisher = new Google_Service_AndroidPublisher($client);

// 결제 영수증 검증 함수
function verifyReceipt($packageName, $productId, $purchaseToken)
{
    global $androidPublisher;

    try {
        // Google Play Developer API를 통해 결제 확인 요청 생성
        $url = "https://androidpublisher.googleapis.com/androidpublisher/v3/applications/{$packageName}/purchases/subscriptions/{$productId}/tokens/{$purchaseToken}";

        // $url = "https://www.googleapis.com/androidpublisher/v3/applications/{$packageName}/purchases/subscriptions/{$productId}/tokens/{$purchaseToken}";

        // cURL 초기화
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 요청 실행
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 응답 확인
        if ($httpStatus == 200) {
            // 결제 성공
            return true;
        } else {
            // 결제 실패
            return false;
        }
    } catch (Exception $e) {
        // 오류 처리
        error_log('Error verifying receipt: ' . $e->getMessage());
        return false;
    }
}

// 사용 예시
$packageName = 'com.dmonster.smap';
$productId = 'smap_sub';
$purchaseToken = 'ngmlihiaeomeeeaebidndkkk.AO-J1OxXvVjz3KTgsTNv8dkYSLdgtTLKEU4dvxMtJrf2_rWC9DP9OWOy8_DocXyW191_1f2gzCHCMCQXSLI0cnhXCqP76nSyeg';

if (verifyReceipt($packageName, $productId, $purchaseToken)) {
    echo 'Payment verified.';
} else {
    echo 'Payment verification failed.';
}
?>