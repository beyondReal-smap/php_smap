<?php
include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
define("DEBURG", true);

use Google\Client;
use Google\Service\AndroidPublisher;

function verifySubscription($keyFilePath, $product_id, $package_name, $purchaseToken)
{
    // 클라이언트 생성 및 서비스 객체 생성
    $client = new Client();
    $client->setAuthConfig($keyFilePath);
    $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

    $service = new AndroidPublisher($client);

    // API 요청 수행
    $result = $service->purchases_subscriptions->get($package_name, $product_id, $purchaseToken);
    $arr = array();
    $arr['kind']                        = $result->kind;
    $arr['startTimeMillis']             = $result->startTimeMillis;
    $arr['expiryTimeMillis']            = $result->expiryTimeMillis;
    $arr['autoResumeTimeMillis']        = $result->autoResumeTimeMillis;
    $arr['autoRenewing']                = $result->autoRenewing;
    $arr['priceCurrencyCode']           = $result->priceCurrencyCode;
    $arr['priceAmountMicros']           = $result->priceAmountMicros;
    $arr['introductoryPriceInfo']       = $result->getIntroductoryPriceInfo();
    $arr['countryCode']                 = $result->countryCode;
    $arr['developerPayload']            = $result->developerPayload;
    $arr['paymentState']                = $result->getPaymentState();
    $arr['cancelReason']                = $result->cancelReason;
    $arr['userCancellationTimeMillis']  = $result->userCancellationTimeMillis;
    $arr['cancelSurveyResult']          = $result->getCancelSurveyResult();
    $arr['orderId']                     = $result->orderId;
    $arr['linkedPurchaseToken']         = $result->linkedPurchaseToken;
    $arr['purchaseType']                = $result->purchaseType; // 0. 테스트, 1. 프로모션 코드를 사용하여 구매
    $arr['priceChange']                 = $result->getPriceChange();
    $arr['profileName']                 = $result->profileName;
    $arr['emailAddress']                = $result->emailAddress; // 구글 이메일
    $arr['givenName']                   = $result->givenName;
    $arr['familyName']                  = $result->familyName;
    $arr['profileId']                   = $result->profileId;
    $arr['acknowledgementState']        = $result->acknowledgementState;
    $arr['promotionType']               = $result->promotionType;
    $arr['promotionCode']               = $result->promotionCode;
    return $arr;
}
// JSON 키 파일의 경로
$keyFilePath = $_SERVER['DOCUMENT_ROOT'] . '/com-dmonster-smap-2fe1ba79102e.json'; // 서비스 계정의 JSON 키 파일 경로를 설정합니다

if ($_POST['act'] == 'member_receipt_done') { // 구독결제등록
    // 영수증 데이터
    $mt_idx = $_POST['mt_idx'];
    $product_id = $_POST['product_id'];
    $package_name = $_POST['package_name'];
    $purchaseToken = $_POST['purchaseToken']; // 클라이언트에서 받은 purchaseToken을 사용합니다.

    // 영수증 검증
    $verificationResult = verifySubscription($keyFilePath, $product_id, $package_name, $purchaseToken);
    // 응답 처리
    if ($verificationResult['kind'] == 'androidpublisher#subscriptionPurchase') { // 정기 구독일 경우
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_POST['mt_idx'],
            "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
            "product_id" => $_POST['product_id'], // 상품명
            "type" => $_POST['act'],
            "status" => $verificationResult['autoRenewing'],
            "rsp_txt" => $_POST['JsonString'], // JSON 정보
            "wdate" => $DB->now(),
        );
        $DB->insert('order_log_t', $arr_query);

        $DB->where('mt_idx', $mt_idx);
        $mem_row = $DB->getone('member_t');

        if ($product_id == 'smap-sub-year') {
            $ot_title = '구독결제(연간)';
        } else {
            $ot_title = '구독결제(월간)';
        }
        $bprice = '0';
        $sprice = $verificationResult['priceAmountMicros'] / 1000000;
        $price = $verificationResult['priceAmountMicros'] / 1000000;
            
        // purchase_date_ms 값
        $purchase_date_ms = $verificationResult['startTimeMillis'];
        $expires_date_ms = $verificationResult['expiryTimeMillis'];
        // purchase_date_ms를 밀리초 단위에서 초 단위로 변환 (밀리초를 1000으로 나누어 초로 변환)
        $purchase_date_seconds = $purchase_date_ms / 1000;
        $expires_date_seconds = $expires_date_ms / 1000;
        // UTC로부터의 경과 시간을 기준으로 PHP의 DateTime 객체 생성
        $date = new DateTime("@$purchase_date_seconds");
        $edate = new DateTime("@$expires_date_seconds");
        // KST(Korea Standard Time, 한국 표준시)로 변경
        $date->setTimezone(new DateTimeZone('Asia/Seoul'));
        $edate->setTimezone(new DateTimeZone('Asia/Seoul'));
        // 변환된 시간을 문자열로 출력
        $ot_pdate = $date->format('Y-m-d H:i:s');
        $ot_edate = $edate->format('Y-m-d H:i:s');

        // 플랜 일자 업데이트
        unset($arr_query);
        $arr_query = array(
            "ot_code" => $verificationResult['orderId'],
            "ot_ori_code" => $_POST['purchaseToken'], // 영수증정보
            'mt_idx' => $mt_idx,
            'mt_id' => $mem_row['mt_id'],
            'ot_title' => $ot_title,
            'ot_pay_type' => '2',
            'ot_status' => '2',
            'ot_sprice' => $sprice,
            'ot_use_coupon' => '0',
            'ot_price' => $price,
            'ot_price_b' => $bprice,
            'ot_show' => 'Y',
            'ot_wdate' => $DB->now(),
            'ot_pdate' => $DB->now(),
            'ot_sdate' => $ot_pdate,
            'ot_edate' => $ot_edate,
        );
        $DB->insert('order_t', $arr_query);

        unset($arr_query);
        $arr_query = array(
            'mt_level' => '5',
            'mt_plan_date' => $ot_edate,
        );
        $DB->where('mt_idx', $mem_row['mt_idx']);
        $DB->update('member_t', $arr_query);

        $_mt_level = $_SESSION['_mt_level'] = 5;
        echo 'Y';
        exit;
        // 추가 처리
    } else {
        // 유효하지 않은 구독
        // 여기서는 간단히 실패 메시지를 반환합니다.
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_POST['mt_idx'],
            "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
            "product_id" => $_POST['product_id'], // 상품명
            "type" => $_POST['act'],
            "status" => 'fail',
            "rsp_txt" => $_POST['JsonString'], // JSON 정보
            "wdate" => $DB->now(),
        );
        $DB->insert('order_log_t', $arr_query);
        echo 'N';
        exit;
    }
}
if ($_POST['act'] == 'member_receipt_check') { // 구독확인하기
    // 영수증 데이터
    $mt_idx = $_POST['mt_idx'];
    $product_id = $_POST['product_id'];
    $package_name = $_POST['package_name'];
    $purchaseToken = $_POST['purchaseToken']; // 클라이언트에서 받은 purchaseToken을 사용합니다.

    // 마지막 결제 로그 정보 들고오기
    $DB->where('mt_idx', $_POST['mt_idx']);
    $DB->where('ot_pay_type', 2);
    $DB->orderby('ot_edate', 'desc');
    $order_row = $DB->getone('order_t');

    $DB->where('mt_idx', $_POST['mt_idx']);
    $mem_row = $DB->getone('member_t');
    // 영수증 검증
    $verificationResult = verifySubscription($keyFilePath, $product_id, $package_name, $purchaseToken);
    // 응답 처리
    if ($verificationResult['kind'] == 'androidpublisher#subscriptionPurchase') { // 정기 구독 정보가 있을 경우
        // purchase_date_ms 값
        $purchase_date_ms = $verificationResult['startTimeMillis'];
        $expires_date_ms = $verificationResult['expiryTimeMillis'];
        // purchase_date_ms를 밀리초 단위에서 초 단위로 변환 (밀리초를 1000으로 나누어 초로 변환)
        $purchase_date_seconds = $purchase_date_ms / 1000;
        $expires_date_seconds = $expires_date_ms / 1000;
        // UTC로부터의 경과 시간을 기준으로 PHP의 DateTime 객체 생성
        $date = new DateTime("@$purchase_date_seconds");
        $edate = new DateTime("@$expires_date_seconds");
        // KST(Korea Standard Time, 한국 표준시)로 변경
        $date->setTimezone(new DateTimeZone('Asia/Seoul'));
        $edate->setTimezone(new DateTimeZone('Asia/Seoul'));
        // 변환된 시간을 문자열로 출력
        $ot_pdate = $date->format('Y-m-d H:i:s');
        $ot_edate = $edate->format('Y-m-d H:i:s');
        $current_date = date('Y-m-d H:i:s');

        if ($ot_edate <= $current_date && $verificationResult['autoRenewing'] == 0 && $verificationResult['cancelReason']) { // 구독기간 구독만료, 자동 결제 취소, 취소 사유있을 경우
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
                "product_id" => $_POST['product_id'], // 상품명
                "type" => $_POST['act'],
                "status" => $verificationResult['autoRenewing'],
                "rsp_txt" => $_POST['JsonString'], // JSON 정보
                "wdate" => $DB->now(),
            );
            $DB->insert('order_log_t', $arr_query);

            unset($arr_query);
            $arr_query = array(
                "ct_cancel_reson" => $verificationResult['cancelReason'],
            );
            $DB->where('ot_code',$verificationResult['orderId']);
            $DB->where('ot_ori_code',$verificationResult['linkedPurchaseToken']);
            $DB->update('order_t',$arr_query);
            
            unset($arr_query);
            $arr_query = array(
                'mt_level' => '2',
            );
            $DB->where('mt_idx', $_POST['mt_idx']);
            $DB->update('member_t', $arr_query);

            $_mt_level = $_SESSION['_mt_level'] = 2;
            
            echo 'Y';
            exit;
        }else if($ot_edate > $current_date && $verificationResult['autoRenewing'] == 1 && $order_row['ot_code'] != $verificationResult['orderId']) { // 구독기간 중 + 자동 결제
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
                "product_id" => $_POST['product_id'], // 상품명
                "type" => $_POST['act'],
                "status" => $verificationResult['autoRenewing'],
                "rsp_txt" => $_POST['JsonString'], // JSON 정보
                "wdate" => $DB->now(),
            );
            $DB->insert('order_log_t', $arr_query);

            if ($product_id == 'smap-sub-year') {
                $ot_title = '구독결제(연간)';
            } else {
                $ot_title = '구독결제(월간)';
            }
            $bprice = '0';
            $sprice = $verificationResult['priceAmountMicros'] / 1000000;
            $price = $verificationResult['priceAmountMicros'] / 1000000;

            // 플랜 일자 업데이트
            unset($arr_query);
            $arr_query = array(
                "ot_code" => $verificationResult['orderId'],
                "ot_ori_code" => $verificationResult['linkedPurchaseToken'], // 영수증정보
                'mt_idx' => $mem_row['mt_idx'],
                'mt_id' => $mem_row['mt_id'],
                'ot_title' => $ot_title,
                'ot_pay_type' => '2',
                'ot_status' => '2',
                'ot_sprice' => $sprice,
                'ot_use_coupon' => '0',
                'ot_price' => $price,
                'ot_price_b' => $bprice,
                'ot_show' => 'Y',
                'ot_wdate' => $DB->now(),
                'ot_pdate' => $DB->now(),
                'ot_sdate' => $ot_pdate,
                'ot_edate' => $ot_edate,
            );
            $DB->insert('order_t', $arr_query);

            unset($arr_query); 
            $arr_query = array(
                'mt_level' => '5',
                'mt_plan_date' => $ot_edate,
            );
            $DB->where('mt_idx', $mem_row['mt_idx']);
            $DB->update('member_t', $arr_query);

            $_mt_level = $_SESSION['_mt_level'] = 5;
            echo 'Y';
            exit;
        }
    }else{
        $current_date = date('Y-m-d H:i:s');
        if (($order_row['ot_pay_type'] == '3' || $order_row['ot_pay_type'] == '4') && $order_row['ot_edate'] > $current_date) { // 결제정보 없고, 구독기간이 끝남, 쿠폰 또는 추천인 일 경우 무료로 변경
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
                "product_id" => $_POST['product_id'], // 상품명
                "type" => $_POST['act'],
                "status" => 0,
                "rsp_txt" => $_POST['JsonString'], // JSON 정보
                "wdate" => $DB->now(),
            );
            $DB->insert('order_log_t', $arr_query);
            unset($arr_query);
            $arr_query = array(
                'mt_level' => '2',
            );
            $DB->where('mt_idx', $_POST['mt_idx']);
            $DB->update('member_t', $arr_query);

            $_mt_level = $_SESSION['_mt_level'] = 2;
            // 검증 결과가 유효하지 않은 경우
        }
        echo 'Y';
        exit;
    }
}

if (DEBURG) {
    $product_id = "smap_sub";
    $package_name = "com.dmonster.smap";
    $purchaseToken = "mhcoojgcefaljgdeogfcenea.AO-J1OyXC9NYztK6hxwNs6JyUjcNzYMenNu4-J1YseYX1kZ_mjaRUrNvsGR1ZJhrVRJpvN639jEa50fidJ-THDq_lILw51H3Xg";

    // 마지막 결제 로그 정보 들고오기
    $DB->where('mt_idx', 98);
    $DB->where('ot_pay_type', 2);
    $DB->orderby('ot_edate', 'desc');
    $order_row = $DB->getone('order_t');
    // 영수증 검증
    $verificationResult = verifySubscription($keyFilePath, $product_id, $package_name, $purchaseToken);
    printr($verificationResult);
    // 응답 처리
    if ($verificationResult['kind'] == 'androidpublisher#subscriptionPurchase') { // 정기 구독 정보가 있을 경우
        // purchase_date_ms 값
        $purchase_date_ms = $verificationResult['startTimeMillis'];
        $expires_date_ms = $verificationResult['expiryTimeMillis'];
        // purchase_date_ms를 밀리초 단위에서 초 단위로 변환 (밀리초를 1000으로 나누어 초로 변환)
        $purchase_date_seconds = $purchase_date_ms / 1000;
        $expires_date_seconds = $expires_date_ms / 1000;
        // UTC로부터의 경과 시간을 기준으로 PHP의 DateTime 객체 생성
        $date = new DateTime("@$purchase_date_seconds");
        $edate = new DateTime("@$expires_date_seconds");
        // KST(Korea Standard Time, 한국 표준시)로 변경
        $date->setTimezone(new DateTimeZone('Asia/Seoul'));
        $edate->setTimezone(new DateTimeZone('Asia/Seoul'));
        // 변환된 시간을 문자열로 출력
        $ot_pdate = $date->format('Y-m-d H:i:s');
        $ot_edate = $edate->format('Y-m-d H:i:s');
        $current_date = date('Y-m-d H:i:s');
        echo  $ot_pdate . "<br>";
        echo  $ot_edate. "<br>";
        echo  $order_row['ot_ori_code'] . "<br>";
        echo   $verificationResult['linkedPurchaseToken'];
     if ($ot_edate > $current_date && $verificationResult['autoRenewing'] == 1 && $order_row['ot_code'] != $verificationResult['orderId']) { // 구독기간 중 + 자동 결제
            echo  $order_row['ot_ori_code'] . "<br>";
            echo   $verificationResult['linkedPurchaseToken'];
        }
    } else {
        // 유효하지 않은 구독
        echo "유효하지 않은 구독입니다.";
        // 추가 처리
    }
    exit;
} 
include $_SERVER['DOCUMENT_ROOT']."/tail_inc.php";
