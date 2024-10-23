<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

// 로그 파일 경로 설정 (필요에 따라 수정)
$log_file = './recommend_update_log_' . date('Ymd_His') . '.txt';

// 로그 함수 정의
function write_log($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$message}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

if ($_POST['act'] == "recommend_input") {
    $logger->write('Recommend input action started.');
    if (empty($_POST['mt_hp']) && empty($_POST['mt_email']) || empty($_POST['mt_idx'])) {
        $logger->write('Invalid access: Missing required fields.');
        p_alert($translations['txt_invalid_access']); // "잘못된 접근입니다." 번역
        exit;
    }

    // 추천인 사용자가 추천인 입력을 사용했는지 확인
    $DB->where('mt_idx',$_POST['mt_idx']);
    $mem_row = $DB->getone('member_t');

    if ($mem_row['mt_recommend_chk'] == 'Y') { 
        // 추천인 코드를 이미 사용했을 경우
        $logger->write('Recommend code already used.');
        echo json_encode(array('result' => 'use'));
        exit;
    } else { 
        // 추천인 코드를 사용하지 않았을 경우
        $mt_hp = str_replace('-', '', $_POST['mt_hp']);
        $mt_email = $_POST['mt_email'];
        if (!empty($mt_hp)) {
            $DB->where('mt_id', $mt_hp)
                ->where('mt_type', '1')
                ->where('mt_status', '1')
                ->where('mt_show', 'Y');
        } else {
            $DB->where('mt_email', $mt_email)
                ->where('mt_type', '1')
                ->where('mt_status', '1')
                ->where('mt_show', 'Y');
        }
        $recommend_row = $DB->getone('member_t');

        if ($recommend_row['mt_idx']) { 
            // 추천인이 있을 경우
            $logger->write('Referrer found.');
            // 추천인 로그 업데이트
            $arr_query = array(
                'mt_idx' => $mem_row['mt_idx'],
                'mt_id' => $mem_row['mt_id'],
                'mt_name' => $mem_row['mt_name'],
                'mt_nickname' => $mem_row['mt_nickname'],
                'rlt_mt_idx' => $recommend_row['mt_idx'],
                'rlt_mt_id' => $recommend_row['mt_id'],
                'rlt_mt_name' => $recommend_row['mt_name'],
                'rlt_mt_nickname' => $recommend_row['mt_nickname'],
                'rlt_code' => $_POST['mt_hp'],
                'rlt_days' => $translations['txt_30_days'], // "30일" 번역
                'rlt_show' => 'Y',
                'rlt_wdate' => $DB->now(),
            );
            $DB->insert('recommend_log_t', $arr_query);

            // 추천인 사용자 업데이트
            $ot_code = get_uid();
            $current_Date = date('Y-m-d H:i:s');
            $mt_plan_sdate = $mem_row['mt_plan_date'] >= $current_Date ? $mem_row['mt_plan_date'] : $current_Date;
            $mt_plan_date = date('Y-m-d H:i:s', strtotime('+1 months', strtotime($mt_plan_sdate))); // 한달 뒤의 날짜 계산

            $arr_query = array(
                'ot_code' => $ot_code,
                'mt_idx' => $mem_row['mt_idx'],
                'mt_id' => $mem_row['mt_id'],
                'ot_title' => $translations['txt_recommend_input_30_days'], // "추천인 입력 30일" 번역
                'ot_pay_type' => '4',
                'ot_status' => '2',
                'ot_sprice' => '0',
                'ot_use_coupon' => '0',
                'ot_price' => '0',
                'ot_price_b' => '0',
                'ot_show' => 'Y',
                'ot_wdate' => $DB->now(),
                'ot_pdate' => $DB->now(),
                'ot_sdate' => $mt_plan_sdate,
                'ot_edate' => $mt_plan_date,
            );
            $DB->insert('order_t', $arr_query);

            $DB->where('mt_idx', $mem_row['mt_idx'])
                ->update('member_t', [
                    'mt_level' => '5',
                    'mt_recommend_chk' => 'Y',
                    'mt_plan_date' => $mt_plan_date,
                    'mt_rec_date' => $DB->now(),
                ]);

            // 추천인 업데이트
            $re_ot_code = get_uid();
            $mt_plan_sdate = $recommend_row['mt_plan_date'] >= $current_Date ? $recommend_row['mt_plan_date'] : $current_Date;
            $mt_plan_date = date('Y-m-d H:i:s', strtotime('+1 months', strtotime($mt_plan_sdate))); // 한달 뒤의 날짜 계산

            $arr_query = array(
                'ot_code' => $re_ot_code,
                'mt_idx' => $recommend_row['mt_idx'],
                'mt_id' => $recommend_row['mt_id'],
                'ot_title' => $translations['txt_recommend_input_30_days'], // "추천인 입력 30일" 번역
                'ot_pay_type' => '4',
                'ot_status' => '2',
                'ot_sprice' => '0',
                'ot_use_coupon' => '0',
                'ot_price' => '0',
                'ot_price_b' => '0',
                'ot_show' => 'Y',
                'ot_wdate' => $DB->now(),
                'ot_pdate' => $DB->now(),
                'ot_sdate' => $mt_plan_sdate,
                'ot_edate' => $mt_plan_date,
            );
            $DB->insert('order_t', $arr_query);

            $DB->where('mt_idx', $recommend_row['mt_idx'])
                ->update('member_t', [
                    'mt_level' => '5',
                    'mt_plan_date' => $mt_plan_date,
                    'mt_rec_date' => $DB->now(),
                ]);

            $_mt_level = $_SESSION['_mt_level'] = 5;
            $logger->write('Recommend input action completed successfully.');
            echo json_encode(array('result' => 'ok'));
            exit;

        } else { 
            // 추천인이 없을 경우
            $logger->write('No referrer found.');
            echo json_encode(array('result' => 'none'));
            exit;
        }
    }    
} elseif ($_POST['act'] == "admin_input") {
    if (empty($_POST['mt_hp']) || empty($_POST['mt_idx'])) {
        p_alert($translations['txt_invalid_access']); // "잘못된 접근입니다." 번역
        exit;
    }

    // write_log("mt_hp: {$_POST['mt_hp']}, mt_idx: {$_POST['mt_idx']}, mt_level: {$_POST['mt_level']}"); 

    // 회원 정보 확인
    $DB->where('mt_idx', $_POST['mt_idx']);
    $mem_row = $DB->getone('member_t');
    // write_log("mem_row: " . print_r($mem_row, true));

    if (!$mem_row) {
        p_alert($translations['txt_member_not_found']); // "회원 정보를 찾을 수 없습니다." 번역
        exit;
    }

    $mt_hp = str_replace('-', '', $_POST['mt_hp']);
    // write_log("mt_hp: {$mt_hp}");

    // 관리자 입력 처리
    $ot_code = get_uid();
    $current_Date = date('Y-m-d H:i:s');
    // write_log("ot_code: {$ot_code}, current_Date: {$current_Date}");

    // mt_plan_date 처리
    $mt_plan_date = new DateTime($_POST['mt_plan_date']);
    $mt_plan_date->modify('+1 day');
    $mt_plan_date->modify('-1 minute');
    $_POST['mt_plan_date'] = $mt_plan_date->format('Y-m-d H:i:s');
    // write_log("mt_plan_date: {$_POST['mt_plan_date']}");

    if ($_POST['mt_plan_date'] < $current_Date) {
        $_POST['mt_plan_date'] = $current_Date;
        // write_log("Updated mt_plan_date: {$_POST['mt_plan_date']}");
    }

    $mt_plan_sdate = $current_Date;
    $mt_plan_edate = $_POST['mt_plan_date'];
    // write_log("mt_plan_sdate: {$mt_plan_sdate}, mt_plan_edate: {$mt_plan_edate}");

    // 기존 주문 정보 확인
    $order_row = $DB->where('mt_idx', $_POST['mt_idx'])
                     ->where('SYSDATE() BETWEEN ot_sdate AND ot_edate')
                     ->getOne('order_t'); 
    // write_log("order_row: " . print_r($order_row, true)); 

    if ($order_row) {
        // 기존 주문 정보가 있을 경우 업데이트
        if ($_POST['mt_level'] == '2') {
            $arr_query = array('ot_edate' => $current_Date);
        } elseif ($_POST['mt_level'] == '5') {
            $arr_query = array('ot_edate' => $mt_plan_edate);
        } else {
            $arr_query = array();
        }

        // write_log("arr_query: " . print_r($arr_query, true));

        if (!empty($arr_query)) {
            $DB->where('mt_idx', $_POST['mt_idx'])
               ->where('SYSDATE() BETWEEN ot_sdate AND ot_edate')
               ->update('order_t', $arr_query);
            // write_log("Updated order_t");
        }
    } else {
        // 기존 주문 정보가 없고, 레벨이 5일 경우 새 주문 정보 추가
        if ($_POST['mt_level'] == '5') {
            $arr_query = array(
                'ot_code' => $ot_code,
                'mt_idx' => $mem_row['mt_idx'],
                'mt_id' => $mem_row['mt_id'],
                'ot_title' => $translations['txt_admin_input'], // "관리자 입력" 번역
                'ot_pay_type' => '3',
                'ot_status' => '2',
                'ot_sprice' => '0',
                'ot_use_coupon' => '0',
                'ot_price' => '0',
                'ot_price_b' => '0',
                'ot_show' => 'Y',
                'ot_wdate' => $DB->now(),
                'ot_pdate' => $DB->now(),
                'ot_sdate' => $mt_plan_sdate,
                'ot_edate' => $mt_plan_edate,
            );
            $DB->insert('order_t', $arr_query);
            // write_log("Inserted into order_t: " . print_r($arr_query, true));
        }
    }

    $_mt_level = $_SESSION['_mt_level'] = $_POST['mt_level'];
    // write_log("mt_level: {$_mt_level}");

    $DB->where('mt_idx', $mem_row['mt_idx'])
       ->update('member_t', ['mt_plan_date' => $mt_plan_date]);
    
    echo json_encode(array('result' => 'ok'));
    exit;
}
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>