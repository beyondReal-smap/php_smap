<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "coupon_input") {
    if ($_POST['ct_code'] == "") {
        p_alert("잘못된 접근입니다.");
    }
    if ($_POST['mt_idx'] == "") {
        p_alert("잘못된 접근입니다.");
    }
    // 쿠폰 확인
    $DB->where('ct_code', $_POST['ct_code']);
    $DB->where('ct_show','Y');
    $ct_row = $DB->getone('coupon_t');    
    if ($ct_row['ct_idx']) { //쿠폰이 있을 경우
        $current_date = date("Y-m-d");
        if ($current_date > $ct_row['ct_edate']) {
            unset($arr_query);
            $arr_query = array(
                'ct_end' => 'Y'
            );
            $DB->where('ct_idx', $ct_row['ct_idx']);
            $DB->update('coupon_t', $arr_query);

            $DB->where('ct_code', $_POST['ct_code']);
            $ct_row = $DB->getone('coupon_t');
        }
        if ($ct_row['ct_use'] == 'Y') { //쿠폰을 이미 사용했을 경우
            echo json_encode(array('result' => 'use'));
            exit;
        } else if ($ct_row['ct_end'] == 'Y') { //쿠폰이 마감일 경우
            echo json_encode(array('result' => 'end'));
            exit;
        } else { //쿠폰 미사용이고 마감이 아닐경우
            $DB->where('mt_idx', $_POST['mt_idx']);
            $mem_row = $DB->getone('member_t');

            // 쿠폰 로그 업데이트
            unset($arr_query);
            $arr_query = array(
                'mt_idx' => $_POST['mt_idx'],
                'coupon_idx' => $ct_row['ct_idx'],
                'coupon_code' => $_POST['ct_code'],
                'clt_show' => 'Y',
                'clt_wdate' => $DB->now(),
            );
            $DB->insert('coupon_log_t', $arr_query);
            // 쿠폰 로그 업데이트 end

            // 플랜 일자 업데이트
            $ot_code = get_uid();
            $current_Date = date('Y-m-d H:i:s');
            if ($mem_row['mt_plan_date'] >= $current_Date) {
                $mt_plan_sdate = $mem_row['mt_plan_date'];
                $timestamp = strtotime($mem_row['mt_plan_date']);
                $mt_plan_date = date('Y-m-d H:i:s', strtotime('+' . $ct_row['ct_days'] . ' days', $timestamp)); // 쿠폰 가용일수 날짜 계산
            } else {
                $mt_plan_sdate = $current_Date;
                $timestamp = strtotime($current_Date);
                $mt_plan_date = date('Y-m-d H:i:s', strtotime('+' . $ct_row['ct_days'] . ' days', $timestamp)); // 쿠폰 가용일수 날짜 계산
            }
            unset($arr_query);
            $arr_query = array(
                'ot_code' => $ot_code,
                'mt_idx' => $mem_row['mt_idx'],
                'mt_id' => $mem_row['mt_id'],
                'ot_title' => '쿠폰사용 ' . $ct_row['ct_days'] . '일',
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
                'ot_edate' => $mt_plan_date,
            );
            $DB->insert('order_t', $arr_query);

            unset($arr_query);
            $arr_query = array(
                'mt_level' => '5',
                'mt_plan_date' => $mt_plan_date,
            );
            $DB->where('mt_idx', $mem_row['mt_idx']);
            $DB->update('member_t', $arr_query);

            unset($arr_query);
            $arr_query = array(
                'ct_use' => 'Y',
                'ct_member' => $mem_row['mt_idx']
            );
            $DB->where('ct_idx', $ct_row['ct_idx']);
            $DB->update('coupon_t', $arr_query);
            // 플랜 일자 업데이트 end

            $_mt_level = $_SESSION['_mt_level'] = 5;
            echo json_encode(array('result' => 'ok'));
            exit;
        }
    } else { // 쿠폰이 없을 경우
        echo json_encode(array('result' => 'none'));
        exit;
    }
}


include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
