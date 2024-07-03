<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "chk_mt_id") {
    if ($_POST['mt_email'] == '') {
        p_alert("잘못된 접근입니다. mt_email");
    }

    $DB->where('mt_email', $_POST['mt_email']);
    $DB->where('mt_level != 1');
    $row = $DB->getone('member_t');

    if ($row['mt_idx']) {
        //중복일 경우
        echo json_encode(false);
    } else {
        //중복X
        echo json_encode(true);
    }
}
if ($_POST['act'] == "check_hp") {
    if ($_POST['mt_hp'] == '') {
        p_alert("잘못된 접근입니다. mt_hp");
    }
    // 휴대폰 번호에서 하이픈 제거
    $mt_hp = str_replace('-', '', $_POST['mt_hp']);
    $DB->where('mt_hp', $mt_hp);
    $DB->where('mt_status', '1');
    $DB->where('mt_level != 1');
    $row = $DB->getone('member_t');
    if ($row['mt_idx']) {
        echo json_encode(array('result' => 'login', 'mt_idx' => $row['mt_idx'], 'mt_hp' => $row['mt_hp']));
    } else {
        echo json_encode(array('result' => 'join', 'mt_hp' => $_POST['mt_hp']));
    }
    exit;
}
if ($_POST['act'] == "send_hp") {
    if ($_POST['mt_hp'] == '') {
        p_alert("잘못된 접근입니다. mt_hp");
    }
    if ($_POST['mt_hp']) {
        // 휴대폰 번호에서 하이픈 제거
        $mt_hp = str_replace('-', '', $_POST['mt_hp']);
        $DB->where('mt_hp', $mt_hp);
        $DB->where('mt_status', '1');
        $DB->where('mt_level != 1');
        $mem_row = $DB->getone('member_t');
        if ($mem_row['mt_idx'] && $_POST['mt_state'] == 'join') {
            echo json_encode(array('result' => '_login', 'msg' => '등록되어 있는 휴대폰번호 입니다.'));
            exit;
        } else {
            $sms_num = mt_sms_make();
            $msg = '[SMAP]
인증번호 [' . $sms_num . '] 입니다.
인증번호를 정확히 입력해주세요.';
            // 네이버
            // $rtn = f_naver_sms_send($mt_hp, $msg);
            // $rtn_de = json_decode($rtn, true);

            // 알리고
            $rtn = f_aligo_sms_send($mt_hp, $msg);
            // if($rtn['message'] == 'test') {
            //     $sms_num = '123456';
            //     $rtn_de['result_code'] = $rtn['result_code'];
            //     $rtn_de['message'] = $rtn['message'];
            // }else {
            $rtn_de = json_decode($rtn, true);
            // }
            if ($rtn_de === null && json_last_error() !== JSON_ERROR_NONE) {
                // JSON 디코딩 중 오류가 발생한 경우
                echo json_encode(array('result' => '_json_error', 'msg' => 'JSON decoding error: ' . json_last_error_msg()));
                // echo json_encode(array('result' => '_json_error', 'msg' => 'JSON decoding error: ' . $rtn_de['errorMessage']));
                exit;
            } else {
                // 정상적으로 JSON 디코딩된 경우
                // 알리고
                $slt_code = $rtn_de['result_code'];
                $slt_result = $rtn_de['message'];
                // 네이버
                // $slt_code = $rtn_de['statusCode'];
                // $slt_result = $rtn_de['statusName'];

                // 여기에서 $slt_code 및 $slt_result를 사용하여 계속 로직을 처리할 수 있습니다.
            }
            $time = time() + (60 * 5);
            unset($arr_query);
            $arr_query = array(
                "slt_hp" => $_POST['mt_hp'],
                "slt_msg" => $msg,
                "slt_number" => $sms_num,
                "slt_code" => $slt_code,
                "slt_result" => $slt_result,
                "slt_request_time" => $time,
                "slt_wdate" => $DB->now(),
            );
            $DB->insert('sms_log_t', $arr_query);
            $_SESSION['_number_chk'] = 1;
            echo json_encode(array('result' => '_ok', 'msg' => '발송되었습니다.'));
        }
    } else {
        echo json_encode(array('result' => '_false', 'msg' => '휴대폰 번호를 입력해 주세요.'));
    }
    exit;
}
if ($_POST['act'] == 'auth_hp') { //인증번호 확인
    if ($_POST['mt_hp'] == '') {
        p_alert("잘못된 접근입니다. mt_hp");
    }
    if ($_POST['mt_num'] == '') {
        p_alert("잘못된 접근입니다. mt_num");
    }
    if ($_POST['mt_num']) {
        $DB->where('slt_hp', $_POST['mt_hp']);
        $DB->where('slt_number', $_POST['mt_num']);
        $DB->orderBy('slt_wdate', 'DESC');
        $row = $DB->getone('sms_log_t');
        if (!$row['slt_idx']) {
            echo json_encode(array('result' => '_false', 'msg' => '인증번호가 맞지않습니다.'));
            exit;
        }
        if (time() > $row['slt_request_time']) {
            echo json_encode(array('result' => '_false', 'msg' => '인증번호가 만료되었습니다. 재전송하여 다시 인증해주세요.'));
            exit;
        }
    } else {
        echo json_encode(array('result' => '_false', 'msg' => '인증번호를 입력해 주세요.'));
        exit;
    }
    $_SESSION['_number_chk'] = 0;
    echo json_encode(array('result' => '_ok', 'msg' => '인증되었습니다.'));
    exit;
}
if ($_POST['act'] == 'join') {
    if ($_POST['mt_pass'] == "") {
        p_alert("잘못된 접근입니다. mt_pass");
    }
    if ($_POST['mt_pass_confirm'] == "") {
        p_alert("잘못된 접근입니다. mt_pass_confirm");
    }
    if ($_POST['mt_pass'] != $_POST['mt_pass_confirm']) {
        p_alert("잘못된 접근입니다. mt_pass mt_pass_confirm" . $_POST['mt_pass'] . " " . $_POST['mt_pass_confirm']);
    }
    if ($_POST['mt_email'] == "") {
        p_alert("잘못된 접근입니다. mt_email");
    }
    if ($_POST['mt_hp'] == "") {
        p_alert("잘못된 접근입니다. mt_hp");
    }
    // if ($_POST['mt_token_id'] == "") {
    //     p_alert("잘못된 접근입니다. mt_token_id");
    // }
    // 휴대폰 번호에서 하이픈 제거
    $mt_hp = str_replace('-', '', $_POST['mt_hp']);

    // and, ios 구분
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $patternMobile = '/(iPhone|iPod|iPad|Android|Windows Phone)/i';

    if (preg_match($patternMobile, $userAgent)) {
        // 운영체제 확인
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $mt_os_check = 1;
        } else {
            $mt_os_check = 0;
        }
    }

    if ($_SESSION['_mt_lat'] == '') {
        $_SESSION['_mt_lat'] = '37.5666805';
    }

    if ($_SESSION['_mt_long'] == '') {
        $_SESSION['_mt_long'] = '126.9784147';
    }
    $DB->where('mt_id', $mt_hp);
    $mem_chk_row = $DB->getone('member_t');
    if ($mem_chk_row['mt_idx']) {
        echo json_encode(array('result' => '_already'));
        exit;
    } else {
        unset($arr_query);
        $arr_query = array(
            "mt_type" => 1,
            "mt_level" => 2,
            "mt_status" => 1,
            "mt_id" => $mt_hp,
            "mt_hp" => $mt_hp,
            "mt_email" => $_POST['mt_email'],
            "mt_pwd" =>  password_hash($_POST['mt_pass'], PASSWORD_DEFAULT),
            "mt_token_id" => $_POST['mt_token_id'] ? $_POST['mt_token_id'] : $_SESSION['_mt_token_id'],
            "mt_os_check" => $mt_os_check,
            "mt_onboarding" => "N",
            "mt_show" => "Y",
            "mt_push1" => "Y",
            "mt_plan_check" => "N",
            "mt_lat" => $_SESSION['_mt_lat'] ? $_SESSION['_mt_lat'] : '37.5666805',
            "mt_long" => $_SESSION['_mt_long'] ? $_SESSION['_mt_long'] : '126.9784147',
            "mt_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('member_t', $arr_query);

        if ($_last_idx) {
            $_mt_idx   = $_SESSION['_mt_idx'] = $_last_idx;
            $_mt_level = $_SESSION['_mt_level'] = '2';

            echo json_encode(array('result' => '_ok', 'mt_idx' => $_last_idx));
            exit;
        } else {
            p_alert("잘못된 접근입니다. _last_idx");
        }
    }
    
}
if ($_POST['act'] == 'join_add_info') {
    if ($_POST['mt_name'] == '') {
        p_alert("잘못된 접근입니다. mt_name");
    }
    if ($_POST['mt_gender'] == '') {
        p_alert("잘못된 접근입니다. mt_gender");
    }
    $my_birth = $_POST['pick_year'] . '-' . $_POST['pick_month'] . '-' . $_POST['pick_day'];

    unset($arr_query);
    $arr_query = array(
        "mt_name" => $_POST['mt_name'],
        "mt_birth" => $my_birth,
        "mt_gender" => $_POST['mt_gender'],
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->update('member_t', $arr_query);

    $_mt_name  = $_SESSION['_mt_name']  = $_POST['mt_name'];
    p_gotourl("./join_agree");
}
if ($_POST['act'] == "join_agree") {
    if ($_POST['mt_agree']) {
        foreach ($_POST['mt_agree'] as $key => $val) {
            if ($val) {
                $_SESSION['_mt_agree' . $val] = 'Y';
            }
        }
        unset($arr_query);
        $arr_query = array(
            "mt_agree1" => $_SESSION['_mt_agree1'],
            "mt_agree2" => $_SESSION['_mt_agree2'],
            "mt_agree3" => $_SESSION['_mt_agree3'],
            "mt_agree4" => $_SESSION['_mt_agree4'],
            "mt_agree5" => $_SESSION['_mt_agree5'],
        );

        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->update('member_t', $arr_query);
    } else {
        p_alert("잘못된 접근입니다. mt_agree");
    }

    p_gotourl("./join_done");
}
if ($_POST['act'] == "change_password") {
    if ($_POST['mt_hp'] == '') {
        p_alert("잘못된 접근입니다. mt_hp");
    }
    if ($_POST['mt_pass'] == "") {
        p_alert("잘못된 접근입니다. mt_pass");
    }
    if ($_POST['mt_pass_confirm'] == "") {
        p_alert("잘못된 접근입니다. mt_pass_confirm");
    }
    if ($_POST['mt_pass'] != $_POST['mt_pass_confirm']) {
        p_alert("잘못된 접근입니다. mt_pass mt_pass_confirm" . $_POST['mt_pass'] . " " . $_POST['mt_pass_confirm']);
    }

    $mt_hp = str_replace('-', '', $_POST['mt_hp']);
    unset($arr_query);
    $arr_query = array(
        "mt_pwd" =>  password_hash($_POST['mt_pass'], PASSWORD_DEFAULT),
    );
    $DB->where('mt_id', $mt_hp);

    $DB->update('member_t', $arr_query);

    p_gotourl("./login?phoneNumber=" . $_POST['mt_hp']);
}
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
