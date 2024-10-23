<?php

$_SERVER['DOCUMENT_ROOT'] = "/data/wwwroot/app2.smap.site";
$cron_chk = true;

include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

// 애플 서버 URL을 상수로 정의
define('APPLE_PRODUCTION_VERIFY_RECEIPT_URL', 'https://buy.itunes.apple.com/verifyReceipt');
define('APPLE_SANDBOX_VERIFY_RECEIPT_URL', 'https://sandbox.itunes.apple.com/verifyReceipt');

// 영수증 검증 함수
function verifyReceipt($receiptData, $isSandbox = false)
{
    $verifyReceiptUrl = $isSandbox ? APPLE_SANDBOX_VERIFY_RECEIPT_URL : APPLE_PRODUCTION_VERIFY_RECEIPT_URL;

    $postData = json_encode([
        'receipt-data' => $receiptData,
        'password' => '18aee4ac68604ece94f2685c9c5a3a88', // 실제 비밀번호로 변경
        'exclude-old-transactions' => true
    ]);

    $ch = curl_init($verifyReceiptUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpStatus != 200) {
        // 오류 로깅 추가
        error_log("Error verifying receipt. HTTP Status: $httpStatus, Response: $response");
        return false;
    }

    return json_decode($response, true);
}

// 지역별 가격 정보 가져오기
$priceInfo = getPriceByRegion();
// 상품 정보 배열
$productInfo = [
    'com.dmonster.smap.sub_month' => [
        'ot_title' => $translations['txt_monthly_subscription'],
        'sprice' => $priceInfo['monthly'],
        'bprice' => '0',
        'price' => $priceInfo['monthly'],
        'currency' => $priceInfo['currency']
    ],
    'com.dmonster.smap.sub_year' => [
        'ot_title' => $translations['txt_yearly_subscription'],
        'sprice' => $priceInfo['monthly'] * 12,
        'bprice' => $priceInfo['monthly'] * 12 - $priceInfo['yearly'],
        'price' => $priceInfo['yearly'],
        'currency' => $priceInfo['currency']
    ]
];

// DB 조회 
$DB->where('mt_level > 1')
    ->where('mt_plan_check', 'Y')
    ->where('mt_os_check', '1')
    ->where('mt_show', 'Y');
$mem_list = $DB->get('member_t');

if ($mem_list) {
    foreach ($mem_list as $mem_row) {
        $memberId = $mem_row['mt_idx'];
        $isSubscribed = $mem_row['mt_plan_check'] == 'Y';

        if ($isSubscribed) {
            $receiptToken = $mem_row['mt_last_receipt_token'];

            // 마지막 결제 로그 정보
            $order_row = $DB->where('mt_idx', $memberId)
                ->where('ot_pay_type', 2)
                ->orderby('ot_edate', 'desc')
                ->getOne('order_t');

            // 샌드박스 환경 여부 설정 (필요에 따라 변경)
            $isSandbox = false; 
            $verificationResult = verifyReceipt($receiptToken, $isSandbox);

            if ($verificationResult && $verificationResult['status'] == 0) {
                $receiptInfo = $verificationResult['latest_receipt_info'][0];
                $receiptRenewalinfo = $verificationResult['pending_renewal_info'][0];

                $ot_pdate = convertTimestampToKst($receiptInfo['purchase_date_ms']);
                $ot_edate = convertTimestampToKst($receiptInfo['expires_date_ms']);
                $current_date = date('Y-m-d H:i:s');

                if ($receiptRenewalinfo['auto_renew_status'] == 0) {
                    // 구독 만료 또는 취소 처리

                    // 로그 기록 
                    $logData = [
                        "mt_idx" => $memberId,
                        "type" => 'member_receipt_check_cancel_ios_cron',
                        "status" => '99',
                        "imp_uid" => $receiptRenewalinfo['original_transaction_id'],
                        "product_id" => $receiptRenewalinfo['product_id'],
                        "ot_code" => $receiptRenewalinfo['auto_renew_status'] . '-' . $receiptRenewalinfo['expiration_intent'],
                        "rsp_txt" => $receiptToken,
                        "wdate" => $DB->now()
                    ];
                    $DB->insert('order_log_t', $logData);

                    // 주문 상태 업데이트
                    $DB->where('ot_ori_code', $receiptRenewalinfo['original_transaction_id'])
                        ->update('order_t', [
                            "ot_ccdate" => $ot_edate,
                            "ct_cancel_reson" => $receiptRenewalinfo['auto_renew_status'] . '-' . $receiptRenewalinfo['expiration_intent']
                        ]);

                    // 회원 정보 업데이트
                    $DB->where('mt_idx', $memberId)
                        ->update('member_t', [
                            'mt_plan_check' => 'N',
                            'mt_plan_date' => $ot_edate,
                            "mt_last_receipt_token" => $receiptToken
                        ]);

                } else if ($ot_edate > $current_date && $receiptRenewalinfo['auto_renew_status'] == 1 && $order_row['ot_code'] != $receiptInfo['transaction_id']) { 
                    // 구독 기간 중 영수증 갱신 처리

                    // 로그 기록
                    $DB->insert('order_log_t', [
                        "mt_idx" => $memberId,
                        "type" => 'member_receipt_check_ios_cron',
                        "imp_uid" => $receiptInfo['transaction_id'],
                        "product_id" => $receiptRenewalinfo['product_id'],
                        "ot_code" => $verificationResult['status'],
                        "rsp_txt" => $receiptToken,
                        "wdate" => $DB->now()
                    ]);

                    // 상품 정보 가져오기
                    $product = $productInfo[$receiptInfo['product_id']]; // receiptList -> receiptInfo
                    $ot_title = $product['ot_title'];
                    $sprice = $product['sprice'];
                    $bprice = $product['bprice'];
                    $price = $product['price'];

                    // 플랜 일자 업데이트
                    $DB->insert('order_t', [
                        "ot_code" => $receiptInfo['transaction_id'],
                        "ot_ori_code" => $receiptInfo['original_transaction_id'],
                        'mt_idx' => $memberId,
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
                        'ot_pdate' => $ot_pdate,
                        'ot_sdate' => $ot_pdate,
                        'ot_edate' => $ot_edate
                    ]);

                }
            } else {
                // 영수증 검증 실패 또는 오류 처리
                handleVerificationFailure($order_row, $memberId, $receiptToken); 
            }
        } else {
            // 구독이 아닌 경우 처리
            handleNonSubscription($mem_row);
        }
    }
}

// 밀리초 단위 timestamp를 KST DateTime으로 변환하는 함수
function convertTimestampToKst($timestamp)
{
    $seconds = $timestamp / 1000;
    $date = new DateTime("@$seconds", new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Asia/Seoul'));
    return $date->format('Y-m-d H:i:s');
}

// 영수증 검증 실패 시 처리 함수
function handleVerificationFailure($order_row, $memberId, $receiptToken)
{
    global $DB; // $DB 객체를 함수 내에서 사용하기 위해 global로 선언
    $current_date = date('Y-m-d H:i:s');
    if (($order_row['ot_pay_type'] == '3' || $order_row['ot_pay_type'] == '4') && $order_row['ot_edate'] > $current_date) {
        // 결제 정보 없고, 구독 기간이 끝남, 쿠폰 또는 추천인 일 경우 무료로 변경
        $DB->insert('order_log_t', [
            "mt_idx" => $memberId,
            "type" => 'plan_edate_end',
            "product_id" => $order_row['ot_pay_type'],
            "ot_code" => $order_row['ot_code'],
            "rsp_txt" => $receiptToken,
            "wdate" => $DB->now()
        ]);

        $DB->where('mt_idx', $memberId) 
            ->update('member_t', [
                'mt_level' => '2',
                'mt_plan_check' => 'N'
            ]);
    }
}

// 구독이 아닌 경우 처리 함수
function handleNonSubscription($mem_row)
{
    global $DB; // $DB 객체를 함수 내에서 사용하기 위해 global로 선언
    $memberId = $mem_row['mt_idx'];
    $current_date = date('Y-m-d H:i:s');

    if ($mem_row['mt_plan_date'] < $current_date) {
        $DB->insert('order_log_t', [
            "mt_idx" => $memberId,
            "type" => 'member_receipt_check_ios_cron',
            "status" => 'plan_edate_end', 
            "wdate" => $DB->now()
        ]);

        $DB->where('mt_idx', $memberId)
            ->update('member_t', [
                'mt_level' => '2',
                'mt_plan_check' => 'N'
            ]);
    }
}
?>