<?php
include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
define("DEBURG", true);

// JSON 키 파일의 경로
$keyFilePath = $_SERVER['DOCUMENT_ROOT'] . '/com-dmonster-smap-2fe1ba79102e.json'; // 서비스 계정의 JSON 키 파일 경로를 설정합니다.


if (DEBURG) {
    $product_id = "smap_sub";
    $package_name = "com.dmonster.smap";
    $purchaseToken = "oldkfkmafhokdfkmplalopak.AO-J1OzwlBgLlMrBHlzFvVMrs0Y8P41J2wGeL6Y78BcgcDsFIoXllLOpTNa7NpcNsD_PLf6Xj7pBeSXbueiHtnkxFIh1GazDsA";
}else{
    // 영수증 데이터
    $product_id = $_POST['product_id'];
    $package_name = $_POST['package_name'];
    $purchaseToken = $_POST['purchaseToken']; // 클라이언트에서 받은 purchaseToken을 사용합니다.

    unset($arr_query);
    $arr_query = array(
        "mt_idx" => $_POST['mt_idx'],
        "imp_uid" => $_POST['purchaseToken'], // 영수증번호
        "type" => $_POST['act'],
        "rsp_txt" => $_POST['JsonString'], // jsonoriginal
        "wdate" => $DB->now(),
    );
    $DB->insert('order_log_t', $arr_query);
}


use Google\Client;
use Google\Service\AndroidPublisher;

// 클라이언트 생성 및 서비스 객체 생성
$client = new Client();
$client->setAuthConfig($keyFilePath);

// Google Play Developer API에 액세스할 범위 설정
$client->addScope('https://www.googleapis.com/auth/androidpublisher');

// Google API 클라이언트를 사용하여 API 요청 수행
// 예를 들어, Google Play Developer API의 purchases.subscriptions.get 메서드를 호출하는 경우:
$service = new \Google\Service\AndroidPublisher($client);
$result = $service->purchases_subscriptions->get($package_name, $product_id, $purchaseToken);
// printr($service);
printr($result);
exit;
try {
    // 인앱 결제를 확인하고 결과를 반환합니다.
    $response = $service->purchases_subscriptions->get($package_name, $product_id, $purchaseToken);

    // 결제가 유효하면 처리합니다.
    if ($response->purchaseState == 0 && $response->autoRenewing) {
        // 결제가 유효하고 자동 갱신 중인 경우
        // 정기 결제가 성공적으로 이루어졌으며, 자동 갱신됩니다.
        // 추가적인 로직을 추가하여 사용자에게 프리미엄 기능을 제공하거나 데이터베이스에 구독 정보를 기록합니다.
        echo "정기 결제가 성공적으로 확인되었습니다.";
    } else {
        // 결제가 유효하지 않거나 자동 갱신이 비활성화된 경우
        echo "정기 결제가 유효하지 않습니다.";
    }
} catch (Exception $e) {
    // 예외가 발생하면 예외를 처리합니다.
    echo '구글 API 오류: ' . $e->getMessage();
}
include $_SERVER['DOCUMENT_ROOT']."/tail_inc.php";
