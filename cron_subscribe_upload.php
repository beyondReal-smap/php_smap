<?php
$_SERVER['DOCUMENT_ROOT'] = "/data/wwwroot/app2.smap.site";
$cron_chk=true;
include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

use Google\Client;
use Google\Service\AndroidPublisher;

$package_name = 'com.dmonster.smap';
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
    } catch (\Exception $e) {
        // 오류 처리
        return false;
    }
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
// $DB->where('mt_level', '5');        // 유료구독회원
$DB->where('mt_level > 1');            // 탈퇴회원제외
$DB->where('mt_plan_check', 'Y');
$DB->where('mt_os_check', '0');
$DB->where('mt_show', 'Y');
$mem_list = $DB->get('member_t');
if ($mem_list) {
    foreach ($mem_list as $mem_row) {
        if ($mem_row['mt_plan_check'] == 'Y') { // 구독여부 확인
            // 마지막 결제 로그 정보 들고오기
            $DB->where('mt_idx', $mem_row['mt_idx']);
            $DB->where('ot_pay_type', 2);
            $DB->orderby('ot_edate', 'desc');
            $order_row = $DB->getone('order_t');
            $purchaseToken = $mem_row['mt_last_receipt_token'];
            $purchaseToken = 'fgaefpnhgbiicklhapgapnce.AO-J1OxOSIz7MBPBcIjZXCbbOKkifd7At5cSspGS8_fjx2GlSC6oy7SRf5KyBJ4aIPkaBmbP2KJSjZaJhbA42Zpchi7or7EPOw';
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
                if ($verificationResult['SubscriptionState'] != 'SUBSCRIPTION_STATE_ACTIVE' && $verificationResult['cancelTime']) { // 구독기간 구독만료, 자동 결제 취소

                    $cancel_date = $verificationResult['cancelTime'];
                    $ot_cdate = convertTimeFormat($cancel_date);
                    unset($arr_query);
                    $arr_query = array(
                        "mt_idx" => $mem_row['mt_idx'],
                        "imp_uid" => $purchaseToken, // 영수증 정보
                        "product_id" => $verificationResult['basePlanId'], // 상품명
                        "type" => 'member_receipt_check_cancel_cron',
                        "status" => $verificationResult['SubscriptionState'], // 구독정보
                        "wdate" => $DB->now(),
                    );
                    $DB->insert('order_log_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "ot_ccdate" => $ot_cdate,
                        //"ot_status" => '99',
                        "ct_cancel_reson" => $verificationResult['canceledStateContext'],
                    );

                    // $DB->where('ot_code', $verificationResult['latestOrderId']);
                    $DB->where('ot_ori_code',$purchaseToken);
                    $DB->update('order_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        'mt_plan_check' => 'N',
                        'mt_plan_date' => $ot_edate,
                        "mt_last_receipt_token" =>  $purchaseToken, // 영수증 정보
                    );
                    $DB->where('mt_idx', $mem_row['mt_idx']);
                    $DB->update('member_t', $arr_query);
                } else if ($ot_edate > $current_date && $verificationResult['SubscriptionState'] == 'SUBSCRIPTION_STATE_ACTIVE' && $order_row['ot_code'] != $verificationResult['latestOrderId']) { // 구독기간 중 + 자동 결제
                    
                    unset($arr_query);
                    $arr_query = array(
                        "mt_idx" => $mem_row['mt_idx'],
                        "imp_uid" => $purchaseToken, // 영수증 정보
                        "product_id" => $verificationResult['basePlanId'], // 상품명
                        "type" => 'member_receipt_check_cron',
                        "status" => $verificationResult['SubscriptionState'], // 구독정보
                        "wdate" => $DB->now(),
                    );
                    $DB->insert('order_log_t', $arr_query);

                    // 지역별 가격 정보 가져오기
                    $priceInfo = getPriceByRegion();

                    if ($verificationResult['basePlanId'] == 'smap-sub-year') {
                        $ot_title = $translations['txt_yearly_subscription'];
                        $sprice = $priceInfo['monthly'] * 12;
                        $bprice = $priceInfo['monthly'] * 12 - $priceInfo['yearly'];
                        $price = $priceInfo['yearly'];
                    } else {
                        $ot_title = $translations['txt_monthly_subscription'];
                        $sprice = $priceInfo['monthly'];
                        $bprice = '0';
                        $price = $priceInfo['monthly'];
                    }

                    // 플랜 일자 업데이트
                    unset($arr_query);
                    $arr_query = array(
                        "ot_code" => $verificationResult['latestOrderId'],
                        "ot_ori_code" => $purchaseToken, // 영수증정보
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
                        'mt_plan_check' => 'Y',
                        "mt_last_receipt_token" => $purchaseToken, // 영수증 정보
                        'mt_plan_date' => $ot_edate,
                    );
                    $DB->where('mt_idx', $mem_row['mt_idx']);
                    $DB->update('member_t', $arr_query);
                }
            }
        }else{
            $current_date = date('Y-m-d H:i:s');
            if ($mem_row['mt_plan_date'] < $current_date) { // 결제정보 없고, 구독기간이 끝남, 쿠폰 또는 추천인 일 경우 무료로 변경
                unset($arr_query);
                $arr_query = array(
                    "mt_idx" => $mem_row['mt_idx'],
                    "type" => 'member_receipt_check_cron',
                    "status" => 'plan_edate_end', // 구독정보
                    "wdate" => $DB->now(),
                );
                $DB->insert('order_log_t', $arr_query);

                unset($arr_query);
                $arr_query = array(
                    'mt_level' => '2',
                    'mt_plan_check' => 'N',
                );
                $DB->where('mt_idx', $mem_row['mt_idx']);
                $DB->update('member_t', $arr_query);

            }
        }
    }
}
