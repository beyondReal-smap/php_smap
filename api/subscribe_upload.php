<?php
include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
define("DEBURG", true);

use Google\Client;
use Google\Service\AndroidPublisher;

// JSON 키 파일의 경로
$keyFilePath = $_SERVER['DOCUMENT_ROOT'] . '/com-dmonster-smap-2fe1ba79102e.json'; // 서비스 계정의 JSON 키 파일 경로를 설정합니다

function verifySubscription($keyFilePath, $package_name, $purchaseToken)
{
    // 클라이언트 생성 및 서비스 객체 생성
    $client = new Client();
    $client->setAuthConfig($keyFilePath);
    $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

    $service = new AndroidPublisher($client);
    try {
        // 구독 정보 가져오기
        $result = $service->purchases_subscriptionsv2->get($package_name, $purchaseToken); // 정기결제 최신

        $arr = array();
        $arr['kind']                        = $result->kind;
        $arr['startTime']                   = $result->startTime;
        $arr['expiryTime']                  = $result->lineItems[0]->expiryTime;
        $arr['latestOrderId']               = $result->latestOrderId;
        $arr['basePlanId']                  = $result->lineItems[0]->offerDetails->basePlanId; // 현재구매정보
        $arr['offerId']                     = $result->lineItems[0]->offerDetails->offerId; // 프로모션 정보
        $arr['SubscriptionState']           = $result->subscriptionState; // 구독의 현재 상태
        $arr['acknowledgementState']        = $result->acknowledgementState; // 구독의 현재 상태
        $arr['linkedPurchaseToken']        = $result->linkedPurchaseToken;
        $arr['cancelTime']                  = $result->canceledStateContext->userInitiatedCancellation->cancelTime; // 취소시간
        $arr['canceledStateContext']        = $result->canceledStateContext->userInitiatedCancellation->cancelSurveyResult->reason; // 취소사유
        return $arr;


        // $arr['autoResumeTimeMillis']        = $result->autoResumeTimeMillis;
        // $arr['autoRenewing']                = $result->autoRenewing;
        // $arr['priceCurrencyCode']           = $result->priceCurrencyCode;
        // $arr['priceAmountMicros']           = $result->priceAmountMicros;
        // $arr['introductoryPriceInfo']       = $result->getIntroductoryPriceInfo();
        // $arr['countryCode']                 = $result->countryCode;
        // $arr['developerPayload']            = $result->developerPayload;
        // $arr['paymentState']                = $result->getPaymentState();
        // $arr['userCancellationTimeMillis']  = $result->userCancellationTimeMillis;
        // $arr['cancelSurveyResult']          = $result->getCancelSurveyResult();
        // $arr['orderId']                     = $result->orderId;
        // $arr['linkedPurchaseToken']         = $result->linkedPurchaseToken;
        // $arr['purchaseType']                = $result->purchaseType; // 0. 테스트, 1. 프로모션 코드를 사용하여 구매
        // $arr['priceChange']                 = $result->getPriceChange();
        // $arr['profileName']                 = $result->profileName;
        // $arr['emailAddress']                = $result->emailAddress; // 구글 이메일
        // $arr['givenName']                   = $result->givenName;
        // $arr['familyName']                  = $result->familyName;
        // $arr['profileId']                   = $result->profileId;
        // $arr['acknowledgementState']        = $result->acknowledgementState;
        // $arr['promotionType']               = $result->promotionType;
        // $arr['promotionCode']               = $result->promotionCode;
        // 가져온 결과 반환
        // return $result;
    } catch (\Exception $e) {
        // 오류 처리
        return false;
    }
    // API 요청 수행
    // $result = $service->purchases_subscriptions->get($package_name, $product_id, $purchaseToken);
    // $result = $service->purchases_subscriptionsv2->get($package_name, $purchaseToken); // 정기결제 최신
    // return $result;
}
function convertTimeFormat($timeString)
{
    // 시간 문자열을 DateTime 객체로 파싱
    $dateTime = DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $timeString);
    // 시간 추가
    $dateTime->modify('+9 hours');

    // 포맷 변경
    $formattedTime = $dateTime->format('Y-m-d H:i:s');

    return $formattedTime;
}

if ($_POST['act'] == 'member_receipt_done') { // 구독결제등록
    // 영수증 데이터
    $mt_idx = $_POST['mt_idx'];
    $product_id = $_POST['product_id'];
    $package_name = $_POST['package_name'];
    $purchaseToken = $_POST['purchaseToken']; // 클라이언트에서 받은 purchaseToken을 사용합니다.

    // 영수증 검증
    $verificationResult = verifySubscription($keyFilePath, $package_name, $purchaseToken);
    // 응답 처리
    if ($verificationResult['kind'] == 'androidpublisher#subscriptionPurchaseV2' && $verificationResult['acknowledgementState'] == 'ACKNOWLEDGEMENT_STATE_ACKNOWLEDGED') { // 정기 구독일 경우
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_POST['mt_idx'],
            "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
            "product_id" => $verificationResult['basePlanId'], // 상품명
            "type" => $_POST['act'],
            "status" => $verificationResult['SubscriptionState'], // 구독정보
            "rsp_txt" => $_POST['JsonString'], // JSON 정보
            "wdate" => $DB->now(),
        );
        $DB->insert('order_log_t', $arr_query);

        $DB->where('mt_idx', $mt_idx);
        $mem_row = $DB->getone('member_t');

        if($verificationResult['offerId']) { // 7일 체험판일 경우
            $ot_title = '구독결제(7일체험)';
            $sprice = '0';
            $bprice = '0';
            $price = '0';
        }else if ($verificationResult['basePlanId'] == 'smap-sub-year') {
            $ot_title = '구독결제(연간)';
            $sprice = '58800';
            $bprice = '16800';
            $price = '42000';
        } else {
            $ot_title = '구독결제(월간)';
            $sprice = '4900';
            $bprice = '0';
            $price = '4900';
        }

        $purchase_date = $verificationResult['startTime'];
        $expires_date = $verificationResult['expiryTime'];
        // 변환된 시간을 문자열로 출력
        $ot_pdate = convertTimeFormat($purchase_date);
        $ot_edate = convertTimeFormat($expires_date);

        // 플랜 일자 업데이트
        unset($arr_query);
        $arr_query = array(
            "ot_code" => $verificationResult['latestOrderId'],
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
            'mt_plan_check' => 'Y',
            'mt_last_receipt_token' => $_POST['purchaseToken'],
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
    $verificationResult = verifySubscription($keyFilePath, $package_name, $purchaseToken);
    // 응답 처리
    if ($verificationResult['kind'] == 'androidpublisher#subscriptionPurchaseV2') { // 정기 구독 정보가 있을 경우
        $purchase_date = $verificationResult['startTime'];
        $expires_date = $verificationResult['expiryTime'];
        // 변환된 시간을 문자열로 출력
        $ot_pdate = convertTimeFormat($purchase_date);
        $ot_edate = convertTimeFormat($expires_date);
        $current_date = date('Y-m-d H:i:s');

        // if ($ot_edate <= $current_date && $verificationResult['SubscriptionState'] != 'SUBSCRIPTION_STATE_ACTIVE' && $verificationResult['cancelTime']) { // 구독기간 구독만료, 자동 결제 취소
        if ($verificationResult['SubscriptionState'] != 'SUBSCRIPTION_STATE_ACTIVE' && $verificationResult['cancelTime']) { // 구독기간 구독만료, 자동 결제 취소

            $cancel_date = $verificationResult['cancelTime'];
            $ot_cdate = convertTimeFormat($cancel_date);
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
                "product_id" => $verificationResult['basePlanId'], // 상품명
                "type" => $_POST['act'],
                "status" => $verificationResult['SubscriptionState'], // 구독정보
                "rsp_txt" => $_POST['JsonString'], // JSON 정보
                "wdate" => $DB->now(),
            );
            $DB->insert('order_log_t', $arr_query);

            unset($arr_query);
            $arr_query = array(
                "ot_ccdate" => $ot_cdate,
                "ct_cancel_reson" => $verificationResult['SubscriptionState'],
            );

            $DB->where('ot_code', $verificationResult['latestOrderId']);
            $DB->where('ot_ori_code', $verificationResult['linkedPurchaseToken']);
            $DB->update('order_t', $arr_query);
            
            unset($arr_query);
            $arr_query = array(
                'mt_plan_check' => 'N',
                'mt_plan_date' => $expires_date,
                "mt_last_receipt_token" => $verificationResult['linkedPurchaseToken'], // 영수증 정보
            );
            $DB->where('mt_idx', $_POST['mt_idx']);
            $DB->update('member_t', $arr_query);

            // $_mt_level = $_SESSION['_mt_level'] = 2;

            echo 'Y';
            exit;
        } else if ($ot_edate > $current_date && $verificationResult['SubscriptionState'] == 'SUBSCRIPTION_STATE_ACTIVE' && $order_row['ot_code'] != $verificationResult['latestOrderId']) { // 구독기간 중 + 자동 결제
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "imp_uid" => $_POST['purchaseToken'], // 영수증 정보
                "product_id" => $verificationResult['basePlanId'], // 상품명
                "type" => $_POST['act'],
                "status" => $verificationResult['SubscriptionState'], // 구독정보
                "rsp_txt" => $_POST['JsonString'], // JSON 정보
                "wdate" => $DB->now(),
            );
            $DB->insert('order_log_t', $arr_query);

            if ($verificationResult['basePlanId'] == 'smap-sub-year') {
                $ot_title = '구독결제(연간)';
                $sprice = '58800';
                $bprice = '16800';
                $price = '42000';
            } else {
                $ot_title = '구독결제(월간)';
                $sprice = '4900';
                $bprice = '0';
                $price = '4900';
            }

            // 플랜 일자 업데이트
            unset($arr_query);
            $arr_query = array(
                "ot_code" => $verificationResult['latestOrderId'],
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
                'mt_plan_check' => 'Y',
                "mt_last_receipt_token" => $_POST['purchaseToken'], // 영수증 정보
                'mt_plan_date' => $ot_edate,
            );
            $DB->where('mt_idx', $mem_row['mt_idx']);
            $DB->update('member_t', $arr_query);

            $_mt_level = $_SESSION['_mt_level'] = 5;
            echo 'Y';
            exit;
        }
    } else {
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
    $purchaseToken = "npeapmlaolpmdabmafnohlmj.AO-J1OxO9Kcx9cf1-OtZumG454qlYSzB4FFNwCnrF1-vY-MURhs_wF7n9nF0bRRrJf5t91Jl9vOUmCeKFLBTEsUqRDWi26MnOQ";

    // 마지막 결제 로그 정보 들고오기
    $DB->where('mt_idx', 98);
    $DB->where('ot_pay_type', 2);
    $DB->orderby('ot_edate', 'desc');
    $order_row = $DB->getone('order_t');
    // 영수증 검증
    $verificationResult = verifySubscription($keyFilePath, $package_name, $purchaseToken);
    printr($verificationResult);
    // exit;
    // 응답 처리
    if ($verificationResult['kind'] == 'androidpublisher#subscriptionPurchaseV2') { // 정기 구독 정보가 있을 경우
        // purchase_date_ms 값
        $purchase_date = $verificationResult['startTime'];
        $expires_date = $verificationResult['expiryTime'];
        // 변환된 시간을 문자열로 출력
        $ot_pdate = convertTimeFormat($purchase_date);
        $ot_edate = convertTimeFormat($expires_date);
        $current_date = date('Y-m-d H:i:s');
        echo  $ot_pdate . "<br>";
        echo  $ot_edate . "<br>";
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
include $_SERVER['DOCUMENT_ROOT'] . "/tail_inc.php";
