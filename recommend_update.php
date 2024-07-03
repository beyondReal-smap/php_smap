<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

// 로그 파일 경로 설정
// $log_file = './recommend_update_log_' . date('Ymd_His') . '.txt';

// 로그 함수 정의
function write_log($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$message}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

if ($_POST['act'] == "recommend_input") {
    if ($_POST['mt_hp'] == "") {
        p_alert("잘못된 접근입니다.");
    }
    if ($_POST['mt_idx'] == "") {
        p_alert("잘못된 접근입니다.");
    }
    // 추천인사용자가 추천인입력을 사용했는지 확인
    $DB->where('mt_idx',$_POST['mt_idx']);
    $mem_row = $DB->getone('member_t');
    if($mem_row['mt_recommend_chk']=='Y'){ //추천인 코드를 사용했을 경우
        echo json_encode(array('result' => 'use'));
        exit;
    }else{ //추천인 코드를 사용하지 않았을 경우
        $mt_hp = str_replace('-', '', $_POST['mt_hp']);
        $DB->where('mt_id', $mt_hp);
        $DB->where('mt_type', '1');
        $DB->where('mt_status', '1');
        $DB->where('mt_show', 'Y');
        $recommend_row = $DB->getone('member_t');
        if($recommend_row['mt_idx']){ // 추천인이 있을 경우
            // 추천인 로그 업데이트
            unset($arr_query);
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
                'rlt_days' => '30일',
                'rlt_show' => 'Y',
                'rlt_wdate' => $DB->now(),
            );
            $DB->insert('recommend_log_t', $arr_query);
            // 추천인로그 업데이트 end

            // 추천인사용자 업데이트
            $ot_code = get_uid();
            $current_Date = date('Y-m-d H:i:s');
            if($mem_row['mt_plan_date'] >= $current_Date){
                $mt_plan_sdate = $mem_row['mt_plan_date'];
                $timestamp = strtotime($mem_row['mt_plan_date']);
                $mt_plan_date = date('Y-m-d H:i:s', strtotime('+1 months', $timestamp)); // 한달 뒤의 날짜 계산
            }else {
                $mt_plan_sdate = $current_Date;
                $timestamp = strtotime($current_Date);
                $mt_plan_date = date('Y-m-d H:i:s', strtotime('+1 months', $timestamp)); // 한달 뒤의 날짜 계산
            }
            unset($arr_query);
            $arr_query = array(
                'ot_code' => $ot_code,
                'mt_idx' => $mem_row['mt_idx'],
                'mt_id' => $mem_row['mt_id'],
                'ot_title' => '추천인 입력 30일',
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

            unset($arr_query);
            $arr_query = array(
                'mt_level' => '5',
                'mt_recommend_chk' => 'Y',
                'mt_plan_date' => $mt_plan_date,
                'mt_rec_date' => $DB->now(),
            );
            $DB->where('mt_idx', $mem_row['mt_idx']);
            $DB->update('member_t', $arr_query);
            // 추천인사용자 업데이트 end

            // 추천인 업데이트

            $re_ot_code = get_uid();
            if($recommend_row['mt_plan_date'] >= $current_Date) {
                $mt_plan_sdate = $recommend_row['mt_plan_date'];
                $timestamp = strtotime($recommend_row['mt_plan_date']);
                $mt_plan_date = date('Y-m-d H:i:s', strtotime('+1 months', $timestamp)); // 한달 뒤의 날짜 계산
            }else {
                $mt_plan_sdate = $current_Date;
                $timestamp = strtotime($current_Date);
                $mt_plan_date = date('Y-m-d H:i:s', strtotime('+1 months', $timestamp)); // 한달 뒤의 날짜 계산
            }

            unset($arr_query);
            $arr_query = array(
                'ot_code' => $re_ot_code,
                'mt_idx' => $recommend_row['mt_idx'],
                'mt_id' => $recommend_row['mt_id'],
                'ot_title' => '추천인 입력 30일',
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
            
            unset($arr_query);
            $arr_query = array(
                'mt_level' => '5',
                'mt_plan_date' => $mt_plan_date,
                'mt_rec_date' => $DB->now(),
            );            
            $DB->where('mt_idx', $recommend_row['mt_idx']);
            $DB->update('member_t', $arr_query);
            // 추천인 업데이트 end

            $_mt_level = $_SESSION['_mt_level'] = 5;
            echo json_encode(array('result' => 'ok'));
            exit;
        }else{ // 추천인이 없을 경우
            echo json_encode(array('result' => 'none'));
            exit;
        }
    }    
} elseif ($_POST['act'] == "admin_input") {
    if ($_POST['mt_hp'] == "" || $_POST['mt_idx'] == "") {
        p_alert("잘못된 접근입니다.");
    }
    // write_log("mt_hp: {$_POST['mt_hp']}, mt_idx: {$_POST['mt_idx']}, mt_level: {$_POST['mt_level']}"); 
    
    // Check if the user is recommended
    $DB->where('mt_idx', $_POST['mt_idx']);
    $mem_row = $DB->getone('member_t');
    // write_log("mem_row: " . print_r($mem_row, true));
    
    if (!$mem_row) {
        p_alert("회원 정보를 찾을 수 없습니다.");
    }
    
    $mt_hp = str_replace('-', '', $_POST['mt_hp']);
    // write_log("mt_hp: {$mt_hp}");
    
    // 추천인사용자 업데이트
    $ot_code = get_uid();
    $current_Date = date('Y-m-d H:i:s');
    // write_log("ot_code: {$ot_code}, current_Date: {$current_Date}");
    
    // Convert the date to a DateTime object
    $mt_plan_date = new DateTime($_POST['mt_plan_date']);
    // Add one day to the date
    $mt_plan_date->modify('+1 day');
    // Subtract one minute from the date
    $mt_plan_date->modify('-1 minute');
    // Format the date as required (YYYY-MM-DD HH:MM:SS)
    $_POST['mt_plan_date'] = $mt_plan_date->format('Y-m-d H:i:s');
    // write_log("mt_plan_date: {$_POST['mt_plan_date']}");
    
    // Check if mt_plan_date is earlier than current date
    if ($_POST['mt_plan_date'] < $current_Date) {
        $_POST['mt_plan_date'] = $current_Date;
        // write_log("Updated mt_plan_date: {$_POST['mt_plan_date']}");
    }
    
    $mt_plan_sdate = $current_Date;
    $mt_plan_edate = $_POST['mt_plan_date'];
    // write_log("mt_plan_sdate: {$mt_plan_sdate}, mt_plan_edate: {$mt_plan_edate}");
    
    // Check if the user exists in 'order_t' based on ot_sdate and ot_edate
    $ordr_query = "select *
                    from order_t
                    where mt_idx = " . $_POST['mt_idx'] . "
                    and SYSDATE() between ot_sdate and ot_edate";    
    $order_row = $DB->Query($ordr_query);
    // write_log("order_row: " . count($order_row));
    
    if ($order_row) {
        if ($_POST['mt_level'] == '2') {
            // Update ot_edate to current date if mt_level is 2
            $arr_query = array(
                'ot_edate' => $current_Date,
            );
        } elseif ($_POST['mt_level'] == '5') {
            // Update ot_edate to $mt_plan_edate if mt_level is 5
            $arr_query = array(
                'ot_edate' => $mt_plan_edate,
            );
        } else {
            // Handle other mt_level values or set a default value
            $arr_query = array();
        }
        // write_log("arr_query: " . print_r($arr_query, true));
        
        if (!empty($arr_query)) { 
            $current_datetime = date('Y-m-d H:i:s');  // 현재 시각을 'YYYY-MM-DD HH:MM:SS' 형식으로 가져옴

            $DB->where('mt_idx', $_POST['mt_idx']);
            $DB->where('ot_sdate', $current_datetime, '<=');
            $DB->where('ot_edate', $current_datetime, '>=');
            $DB->update('order_t', $arr_query);
            // write_log("Updated order_t");
        }
    } else {
        // If the user does not exist in 'order_t' and mt_level is 5, insert a new row
        if ($_POST['mt_level'] == '5') {
            $arr_query = array(
                'ot_code' => $ot_code,
                'mt_idx' => $mem_row['mt_idx'],
                'mt_id' => $mem_row['mt_id'],
                'ot_title' => '관리자 입력',
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

    $arr_query = array(
        'mt_plan_date' => $mt_plan_date,
    );            
    $DB->where('mt_idx', $mem_row['mt_idx']);
    $DB->update('member_t', $arr_query);
    
    echo json_encode(array('result' => 'ok'));
    exit;
}
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";


