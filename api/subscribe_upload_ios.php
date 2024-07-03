<?php
include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
define("DEBURG", true);

// 영수증 검증 함수
function verifyReceipt($receiptData)
{
    // 애플의 검증 URL
    $url = 'https://buy.itunes.apple.com/verifyReceipt'; // 실제로는 https://sandbox.itunes.apple.com/verifyReceipt를 사용하여 테스트할 수도 있습니다.
    // $url = "https://sandbox.itunes.azpple.com/verifyReceipt"; // 테스트

    // POST 요청에 포함될 데이터
    $postData = json_encode(array('receipt-data' => $receiptData, 'password' => '18aee4ac68604ece94f2685c9c5a3a88', 'exclude-old-transactions'=> true));
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

// 결제 검증
if ($_POST['act'] == "member_receipt_done_ios") {
    // 받은 데이터 확인
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $token = $_POST['token'];
    $mt_idx = $_POST['mt_idx'];

    // 영수증 검증
    $verificationResult = verifyReceipt($token);

    // 영수증 검증 결과 확인
    if ($verificationResult && $verificationResult['status'] == 0) {
        // 영수증 검증이 성공하면 결제를 처리합니다.
        // 여기서는 간단히 성공 메시지를 반환합니다.
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_POST['mt_idx'],
            "imp_uid" => $_POST['order_id'], // 영수증번호
            "product_id" => $_POST['product_id'],
            "type" => $_POST['act'],
            "ot_code" => $order_id,
            "status" => $verificationResult['status'],
            "rsp_txt" => $_POST['token'], // jsonoriginal
            "wdate" => $DB->now(),
        );
        $DB->insert('order_log_t', $arr_query);

        $DB->where('mt_idx', $mt_idx);
        $mem_row = $DB->getone('member_t');
        $receiptInfo = $verificationResult['latest_receipt_info'][0];

        if($receiptInfo['is_trial_period'] == true){ // 7일 체험판일 경우
            $ot_title = '구독결제(7일체험)';
            $sprice = '0';
            $bprice = '0';
            $price = '0';
        // } else if($product_id == 'com.dmonster.smap.sub_month'){
        } else if($receiptInfo['product_id'] == 'com.dmonster.smap.sub_month'){ // 24.06.07 
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
        // 플랜 일자 업데이트
        unset($arr_query);
        $arr_query = array(
            "ot_code" => $receiptInfo['transaction_id'],
            "ot_ori_code" => $receiptInfo['original_transaction_id'],
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
            'ot_pdate' => $ot_pdate,
            'ot_sdate' => $ot_pdate,
            'ot_edate' => $ot_edate,
        );
        $DB->insert('order_t', $arr_query);

        unset($arr_query);
        $arr_query = array(
            'mt_level' => '5',
            'mt_plan_check' => 'Y',
            'mt_last_receipt_token' => $_POST['token'],
            'mt_plan_date' => $ot_edate,
        );
        $DB->where('mt_idx', $mem_row['mt_idx']);
        $DB->update('member_t', $arr_query);

        $_mt_level = $_SESSION['_mt_level'] = 5;
        echo 'Y';
        exit;
    } else {
        // 영수증 검증이 실패하거나 상태가 유효하지 않으면 결제를 거부합니다.
        // 여기서는 간단히 실패 메시지를 반환합니다.
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_POST['mt_idx'],
            "imp_uid" => $_POST['order_id'], // order_id
            "product_id" => $_POST['product_id'],
            "type" => $_POST['act'],
            "ot_code" => $order_id,
            "status" => $verificationResult['status'],
            "rsp_txt" => $_POST['token'], // 영수증번호
            "wdate" => $DB->now(),
        );
        $DB->insert('order_log_t', $arr_query);
        echo 'N';
        exit;
    }
}
// 결제 확인
if ($_POST['act'] == "member_receipt_check_ios") {
    // 받은 데이터 확인
    $token = $_POST['token'];
    $mt_idx = $_POST['mt_idx'];

    // 마지막 결제 로그 정보 들고오기
    $DB->where('mt_idx', $_POST['mt_idx']);
    $DB->where('ot_pay_type',2);
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
        if($ot_edate <= $current_date && $receiptRenewalinfo['auto_renew_status'] == 0){ // 구독기간 구독만료, 고객 취소 시
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "type" => $_POST['act'],
                "imp_uid" => $receiptRenewalinfo['original_transaction_id'],
                "product_id" => $receiptRenewalinfo['product_id'],
                "ot_code" => $receiptRenewalinfo['auto_renew_status'].'-'. $receiptRenewalinfo['expiration_intent'],
                "rsp_txt" => $_POST['token'], // 영수증번호
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

            $_mt_level = $_SESSION['_mt_level'] = 2;
            
            echo json_encode(array('status' => 'end'));
        }else if($ot_edate > $current_date && $receiptRenewalinfo['auto_renew_status'] == 1 && $order_row['ot_code'] != $receiptInfo['transaction_id']){  // 구독기간 중 + 고객 영수증 갱신 시
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "type" => $_POST['act'],
                "ot_code" => $verificationResult['status'],
                "rsp_txt" => $_POST['token'], // 영수증번호
                "wdate" => $DB->now(),
            );
            $DB->insert('order_log_t', $arr_query);
            $DB->where('mt_idx', $mt_idx);
            $mem_row = $DB->getone('member_t');

            $receiptListInfo = $verificationResult['receipt']['in_app'];
            // printr($receiptListInfo);
            foreach ($receiptListInfo as $receiptList) {
                $DB->where('ot_code', $receiptList['transaction_id']);
                $ot_chk = $DB->getone('order_t');
                if (!$ot_chk['ot_idx']) {
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
                    $ot_pdate = $date->format('Y-m-d H:i:s');
                    $ot_edate = $edate->format('Y-m-d H:i:s');
                    // 플랜 일자 업데이트
                    unset($arr_query);
                    $arr_query = array(
                        "ot_code" => $receiptList['transaction_id'],
                        "ot_ori_code" => $receiptList['original_transaction_id'],
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
                        'ot_pdate' => $ot_pdate,
                        'ot_sdate' => $ot_pdate,
                        'ot_edate' => $ot_edate,
                    );
                    $DB->insert('order_t', $arr_query);
                }
            }

            unset($arr_query);
            $arr_query = array(
                'mt_level' => '5',
                'mt_plan_check' => 'Y',
                'mt_last_receipt_token' => $_POST['token'],
                'mt_plan_date' => $ot_edate,
            );
            $DB->where('mt_idx', $mem_row['mt_idx']);
            $DB->update('member_t', $arr_query);

            $_mt_level = $_SESSION['_mt_level'] = 5;
            echo json_encode(array('status' => 'renew'));
        }
    } else {
        $current_date = date('Y-m-d H:i:s');
        if( ($order_row['ot_pay_type'] == '3' || $order_row['ot_pay_type'] == '4') && $order_row['ot_edate'] > $current_date){ // 결제정보 없고, 구독기간이 끝남, 쿠폰 또는 추천인 일 경우 무료로 변경
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_POST['mt_idx'],
                "type" => $_POST['act'],
                "product_id" => $order_row['ot_pay_type'],
                "ot_code" => $order_row['ot_code'],
                "rsp_txt" => $_POST['token'], // 영수증번호
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

            $_mt_level = $_SESSION['_mt_level'] = 2;
            // 검증 결과가 유효하지 않은 경우
        }
        echo json_encode(array('status' => 'failure'));
    }
    exit;
}
// 복원
if ($_POST['act'] == "member_receipt_restore_ios") {
    // 받은 데이터 확인
    $token = $_POST['token'];
    $mt_idx = $_POST['mt_idx'];

    // 영수증 검증
    $verificationResult = verifyReceipt($token);

    // 영수증 검증 결과 확인
    if ($verificationResult && $verificationResult['status'] == 0) {
        // 영수증 검증이 성공하면 결제를 처리합니다.
        // 여기서는 간단히 성공 메시지를 반환합니다.
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_POST['mt_idx'],
            "imp_uid" => $_POST['order_id'], // order_id
            "product_id" => $_POST['product_id'],
            "type" => $_POST['act'],
            "ot_code" => $verificationResult['status'],
            "rsp_txt" => $_POST['token'], // 영수증번호
            "wdate" => $DB->now(),
        );
        $DB->insert('order_log_t', $arr_query);

        echo 'Y';
        exit;
    } else {
        // 영수증 검증이 실패하거나 상태가 유효하지 않으면 결제를 거부합니다.
        // 여기서는 간단히 실패 메시지를 반환합니다.
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_POST['mt_idx'],
            "imp_uid" => $_POST['order_id'], // order_id
            "product_id" => $_POST['product_id'],
            "type" => $_POST['act'],
            "ot_code" => $verificationResult['status'],
            "rsp_txt" => $_POST['token'], // 영수증번호
            "wdate" => $DB->now(),
        );
        $DB->insert('order_log_t', $arr_query);
        echo 'N';
        exit;
    }
}

if (DEBURG) {
    $product_id = "smap_sub";
    $order_id = "2000000579030387";
    $token = "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";

}
// 영수증 검증
$verificationResult = verifyReceipt($token);
// printr($verificationResult);
// printr($verificationResult);
// 영수증이 유효한 경우
$receiptInfo = $verificationResult['latest_receipt_info'][0];
$receiptRenewalinfo = $verificationResult['pending_renewal_info'][0];
$receiptListInfo = $verificationResult['receipt']['in_app'];
printr($receiptListInfo);
foreach($receiptListInfo as $receiptList){
    $DB->where('ot_code', $receiptList['transaction_id']);
    $ot_chk = $DB->getone('order_t');
    if(!$ot_chk['ot_idx']){
        echo $receiptList['transaction_id'].'<br>';
    }
}
// printr($receiptInfo);
// printr($receiptRenewalinfo);
// expires_date_ms 값
$expires_date_ms = $receiptInfo['expires_date_ms'];
// expires_date_ms를 밀리초 단위에서 초 단위로 변환 (밀리초를 1000으로 나누어 초로 변환)
$expires_date_seconds = $expires_date_ms / 1000;
// UTC로부터의 경과 시간을 기준으로 PHP의 DateTime 객체 생성
$edate = new DateTime("@$expires_date_seconds");
// DateTimeZone 객체를 생성하고 LA 시간대로 설정
$LA_timezone = new DateTimeZone('America/Los_Angeles');
$edate->setTimezone($LA_timezone);
// LA 시간에서 KST(Korea Standard Time, 한국 표준시)로 변경
$edate->setTimezone(new DateTimeZone('Asia/Seoul'));
// 변환된 시간을 문자열로 출력
$edate = $edate->format('Y-m-d H:i:s');
printr($edate);

// 클라이언트로 영수증 정보 반환
// echo json_encode(array('status' => 'success', 'receipt_info' => $receiptInfo));
exit;
include $_SERVER['DOCUMENT_ROOT'] . "/tail_inc.php";
