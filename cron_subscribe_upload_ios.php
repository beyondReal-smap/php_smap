<?php

$_SERVER['DOCUMENT_ROOT'] = "/data/wwwroot/app.smap.site";

$cron_chk = true;

include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";



// 영수증 검증 함수

function verifyReceipt($receiptData)

{

    // 애플의 검증 URL

    $url = 'https://buy.itunes.apple.com/verifyReceipt'; // 실제로는 https://sandbox.itunes.apple.com/verifyReceipt를 사용하여 테스트할 수도 있습니다.

    // $url = "https://sandbox.itunes.apple.com/verifyReceipt"; // 테스트



    // POST 요청에 포함될 데이터

    $postData = json_encode(array('receipt-data' => $receiptData, 'password' => '18aee4ac68604ece94f2685c9c5a3a88', 'exclude-old-transactions' => true));

    // cURL 초기화

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));



    // 요청 실행

    $response = curl_exec($ch);

    $errno    = curl_errno($ch);

    $errmsg   = curl_error($ch);

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);



    // 응답 확인

    if ($httpStatus != 200) {

        // 오류 발생

        return false;

    }

    // JSON 형태로 응답 반환

    return json_decode($response, true);

}

// $DB->where('mt_level', '5');        // 유료구독회원

$DB->where('mt_level > 1');            // 탈퇴회원제외

$DB->where('mt_plan_check', 'Y');

$DB->where('mt_os_check','1');

$DB->where('mt_show', 'Y');

$mem_list = $DB->get('member_t');



if ($mem_list) {

    foreach ($mem_list as $mem_row) {

        if ($mem_row['mt_plan_check'] == 'Y') { // 구독여부 확인

            // 받은 데이터 확인

            $token = $mem_row['mt_last_receipt_token'];



            // 마지막 결제 로그 정보 들고오기

            $DB->where('mt_idx', $mem_row['mt_idx']);

            $DB->where('ot_pay_type', 2);

            $DB->orderby('ot_edate', 'desc');

            $order_row = $DB->getone('order_t');

            // 영수증 검증

            $verificationResult = verifyReceipt($token);



            // 영수증 검증 결과 확인

            if ($verificationResult && $verificationResult['status'] == 0) {

                // 영수증이 유효한 경우

                $receiptInfo = $verificationResult['latest_receipt_info'][0];

                $receiptRenewalinfo = $verificationResult['pending_renewal_info'][0];

                // purchase_date_ms 값

                $purchase_date_ms = $receiptInfo['purchase_date_ms'];

                $expires_date_ms = $receiptInfo['expires_date_ms'];

                // purchase_date_ms를 밀리초 단위에서 초 단위로 변환 (밀리초를 1000으로 나누어 초로 변환)

                $purchase_date_seconds = $purchase_date_ms / 1000;

                $expires_date_seconds = $expires_date_ms / 1000;

                // UTC로부터의 경과 시간을 기준으로 PHP의 DateTime 객체 생성

                $date = new DateTime("@$purchase_date_seconds");

                $edate = new DateTime("@$expires_date_seconds");

                // DateTimeZone 객체를 생성하고 LA 시간대로 설정

                $LA_timezone = new DateTimeZone('America/Los_Angeles');

                $date->setTimezone($LA_timezone);

                $edate->setTimezone($LA_timezone);

                // LA 시간에서 KST(Korea Standard Time, 한국 표준시)로 변경

                $date->setTimezone(new DateTimeZone('Asia/Seoul'));

                $edate->setTimezone(new DateTimeZone('Asia/Seoul'));

                // 변환된 시간을 문자열로 출력

                $ot_pdate = $date->format('Y-m-d H:i:s');

                $ot_edate = $edate->format('Y-m-d H:i:s');

                $current_date = date('Y-m-d H:i:s');

                if ($receiptRenewalinfo['auto_renew_status'] == 0) { // 구독기간 구독만료, 고객 취소 시

                    unset($arr_query);

                    $arr_query = array(

                        "mt_idx" => $mem_row['mt_idx'],

                        "type" => 'member_receipt_check_cancel_ios_cron',

                        "status" => '99',

                        "imp_uid" => $receiptRenewalinfo['original_transaction_id'],

                        "product_id" => $receiptRenewalinfo['product_id'],

                        "ot_code" => $receiptRenewalinfo['auto_renew_status'] . '-' . $receiptRenewalinfo['expiration_intent'],

                        "rsp_txt" => $token, // 영수증번호

                        "wdate" => $DB->now(),

                    );

                    $DB->insert('order_log_t', $arr_query);



                    unset($arr_query);

                    $arr_query = array(

                        "ot_ccdate" => $ot_edate,

                        // "ot_status" => '99',

                        "ct_cancel_reson" =>  $receiptRenewalinfo['auto_renew_status'] . '-' . $receiptRenewalinfo['expiration_intent'],

                    );



                    // $DB->where('ot_code', $verificationResult['latestOrderId']);

                    $DB->where('ot_ori_code', $receiptRenewalinfo['original_transaction_id']);

                    $DB->update('order_t', $arr_query);



                    unset($arr_query);

                    $arr_query = array(

                        'mt_plan_check' => 'N',

                        'mt_plan_date' => $ot_edate,

                        "mt_last_receipt_token" => $token, // 영수증 정보

                    );

                    $DB->where('mt_idx', $mem_row['mt_idx']);

                    $DB->update('member_t', $arr_query);



                } else if ($ot_edate > $current_date && $receiptRenewalinfo['auto_renew_status'] == 1 && $order_row['ot_code'] != $receiptInfo['transaction_id']) {  // 구독기간 중 + 고객 영수증 갱신 시

                    unset($arr_query);

                    $arr_query = array(

                        "mt_idx" => $mem_row['mt_idx'],

                        "type" => 'member_receipt_check_ios_cron',

                        "imp_uid" => $receiptInfo['transaction_id'],

                        "product_id" => $receiptRenewalinfo['product_id'],

                        "ot_code" => $verificationResult['status'],

                        "rsp_txt" => $token, // 영수증번호

                        "wdate" => $DB->now(),

                    );

                    $DB->insert('order_log_t', $arr_query);



                    if ($receiptList['product_id'] == 'com.dmonster.smap.sub_month') {

                        $ot_title = '구독결제(월간)';

                        $sprice = '4900';

                        $bprice = '0';

                        $price = '4900';

                    } else {

                        $ot_title = '구독결제(연간)';

                        $sprice = '58800';

                        $bprice = '16800';

                        $price = '42000';

                    }

                    // 플랜 일자 업데이트

                    unset($arr_query);

                    $arr_query = array(

                            "ot_code" => $receiptInfo['transaction_id'],

                            "ot_ori_code" => $receiptInfo['original_transaction_id'],

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

                            'ot_pdate' => $ot_pdate,

                            'ot_sdate' => $ot_pdate,

                            'ot_edate' => $ot_edate,

                        );

                    $DB->insert('order_t', $arr_query);



                    // 이전에 입력안한거 있으면 확인하기

                    $receiptListInfo = $verificationResult['receipt']['in_app'];

                    foreach ($receiptListInfo as $receiptList) {

                        $DB->where('ot_code', $receiptList['transaction_id']);

                        $ot_chk = $DB->get('order_t');

                        $ot_count = count($ot_chk);

                        if ($ot_count < 1) {

                            if ($receiptList['product_id'] == 'com.dmonster.smap.sub_month') {

                                $ot_title = '구독결제(월간)';

                                $sprice = '4900';

                                $bprice = '0';

                                $price = '4900';

                            } else {

                                $ot_title = '구독결제(연간)';

                                $sprice = '58800';

                                $bprice = '16800';

                                $price = '42000';

                            }

                            // purchase_date_ms 값

                            $purchase_date_ms = $receiptList['purchase_date_ms'];

                            $expires_date_ms = $receiptList['expires_date_ms'];

                            // purchase_date_ms를 밀리초 단위에서 초 단위로 변환 (밀리초를 1000으로 나누어 초로 변환)

                            $purchase_date_seconds = $purchase_date_ms / 1000;

                            $expires_date_seconds = $expires_date_ms / 1000;

                            // UTC로부터의 경과 시간을 기준으로 PHP의 DateTime 객체 생성

                            $date = new DateTime("@$purchase_date_seconds");

                            $edate = new DateTime("@$expires_date_seconds");

                            // DateTimeZone 객체를 생성하고 LA 시간대로 설정

                            $LA_timezone = new DateTimeZone('America/Los_Angeles');

                            $date->setTimezone($LA_timezone);

                            $edate->setTimezone($LA_timezone);

                            // LA 시간에서 KST(Korea Standard Time, 한국 표준시)로 변경

                            $date->setTimezone(new DateTimeZone('Asia/Seoul'));

                            $edate->setTimezone(new DateTimeZone('Asia/Seoul'));

                            // 변환된 시간을 문자열로 출력

                            $ott_pdate = $date->format('Y-m-d H:i:s');

                            $ott_edate = $edate->format('Y-m-d H:i:s');

                            // 플랜 일자 업데이트

                            unset($arr_query);

                            $arr_query = array(

                                "ot_code" => $receiptList['transaction_id'],

                                "ot_ori_code" => $receiptList['original_transaction_id'],

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

                                'ot_pdate' => $ott_pdate,

                                'ot_sdate' => $ott_pdate,

                                'ot_edate' => $ott_edate,

                            );

                            $DB->insert('order_t', $arr_query);

                        }

                    }



                    unset($arr_query);

                    $arr_query = array(

                        'mt_level' => '5',

                        'mt_plan_check' => 'Y',

                        'mt_plan_date' => $ot_edate,

                        "mt_last_receipt_token" => $verificationResult['latest_receipt'], // 영수증 정보

                    );

                    $DB->where('mt_idx', $mem_row['mt_idx']);

                    $DB->update('member_t', $arr_query);

                }

            } else {

                $current_date = date('Y-m-d H:i:s');

                if (($order_row['ot_pay_type'] == '3' || $order_row['ot_pay_type'] == '4') && $order_row['ot_edate'] > $current_date) { // 결제정보 없고, 구독기간이 끝남, 쿠폰 또는 추천인 일 경우 무료로 변경

                    unset($arr_query);

                    $arr_query = array(

                        "mt_idx" => $mem_row['mt_idx'],

                        "type" => 'plan_edate_end',

                        "product_id" => $order_row['ot_pay_type'],

                        "ot_code" => $order_row['ot_code'],

                        "rsp_txt" => $token, // 영수증번호

                        "wdate" => $DB->now(),

                    );

                    $DB->insert('order_log_t', $arr_query);

                    unset($arr_query);

                    $arr_query = array(

                        'mt_level' => '2',

                        'mt_plan_check' => 'N',

                    );

                    $DB->where('mt_idx', $_POST['mt_idx']);

                    $DB->update('member_t', $arr_query);

                }

            }

        }else{

            $current_date = date('Y-m-d H:i:s');

            if ($mem_row['mt_plan_date'] < $current_date) { // 결제정보 없고, 구독기간이 끝남, 쿠폰 또는 추천인 일 경우 무료로 변경

                unset($arr_query);

                $arr_query = array(

                    "mt_idx" => $mem_row['mt_idx'],

                    "type" => 'member_receipt_check_ios_cron',

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