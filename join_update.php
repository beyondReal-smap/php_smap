<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "chk_mt_id") {
    if ($_POST['mt_email'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_email");
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
        p_alert($translations['txt_invalid_access']  . " mt_hp");
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


if ($_POST['act'] == 'check_email') {
    if ($_POST['mt_email'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_email");
    }
    $mt_email = trim($_POST['mt_email']);
    $DB->where('mt_email', $mt_email);
    $DB->where('mt_status', '1');
    $DB->where('mt_level != 1');
    $row = $DB->getone('member_t');
    if ($row['mt_idx']) {
        echo json_encode(array('result' => 'login', 'mt_idx' => $row['mt_idx'], 'mt_email' => $row['mt_email']));
    } else {
        echo json_encode(array('result' => 'join', 'mt_email' => $_POST['mt_email']));
    }
    exit;
}

// 이메일 전송 함수
function sendVerificationEmail($email, $code, $access_token)
{
    $url = "https://www.worksapis.com/v1.0/me/admin/mail";
    $headers = array(
        "Content-Type: application/json",
        "Authorization: Bearer $access_token",
    );
    $data = array(
        "to" => $email, // 수신자 이메일 주소
        "subject" => "[SMAP] 이메일 인증 코드",
        "body" => "인증 코드: $code",
        "userName" => "admin@smap.site",
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 202) {
        return true;
    } else {
        error_log("Failed to send verification email: $response");
        return false;
    }
}

if ($_POST['act'] == "send_hp") {
    if ($_POST['mt_hp'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_hp");
    }
    if ($_POST['mt_hp']) {
        // 휴대폰 번호에서 하이픈 제거
        $mt_hp = str_replace('-', '', $_POST['mt_hp']);
        $DB->where('mt_hp', $mt_hp);
        $DB->where('mt_status', '1');
        $DB->where('mt_level != 1');
        $mem_row = $DB->getone('member_t');
        if ($mem_row['mt_idx'] && $_POST['mt_state'] == 'join') {
            echo json_encode(array('result' => '_login', 'msg' => $translations['txt_registered_phone_number']));
            exit;
        } else {
            $sms_num = mt_sms_make();
            $msg = $translations['txt_smap_verification_code_message'];
            $msg = str_replace('{code}', $sms_num, $msg);
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
            echo json_encode(array('result' => '_ok', 'msg' => $translations['txt_sent_successfully']));
        }
    } else {
        echo json_encode(array('result' => '_false', 'msg' => $translations['txt_enter_phone_number']));
    }
    exit;
}
if ($_POST['act'] == 'auth_hp') { //인증번호 확인
    if ($_POST['mt_hp'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_hp");
    }
    if ($_POST['mt_num'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_num");
    }
    if ($_POST['mt_num']) {
        $DB->where('slt_hp', $_POST['mt_hp']);
        $DB->where('slt_number', $_POST['mt_num']);
        $DB->orderBy('slt_wdate', 'DESC');
        $row = $DB->getone('sms_log_t');
        if (!$row['slt_idx']) {
            echo json_encode(array('result' => '_false', 'msg' => $translations['txt_incorrect_verification_code']));
            exit;
        }
        if (time() > $row['slt_request_time']) {
            echo json_encode(array('result' => '_false', 'msg' => $translations['txt_verification_code_expired']));
            exit;
        }
    } else {
        echo json_encode(array('result' => '_false', 'msg' => $translations['txt_enter_verification_code']));
        exit;
    }
    $_SESSION['_number_chk'] = 0;
    echo json_encode(array('result' => '_ok', 'msg' => $translations['txt_verification_successful']));
    exit;
}
if ($_POST['act'] == 'join') {
    global $userLang;
    if ($_POST['mt_pass'] == "") {
        $logger->write('Password is empty');
        p_alert($translations['txt_invalid_access']  . " mt_pass");
    }
    if ($_POST['mt_pass_confirm'] == "") {
        $logger->write('Password confirmation is empty');
        p_alert($translations['txt_invalid_access']  . " mt_pass_confirm");
    }
    if ($_POST['mt_pass'] != $_POST['mt_pass_confirm']) {
        $logger->write('Password and confirmation do not match');
        p_alert($translations['txt_invalid_access']  . " mt_pass mt_pass_confirm" . $_POST['mt_pass'] . " " . $_POST['mt_pass_confirm']);
    }
    
    if ($userLang == 'ko') {
        if ($_POST['mt_hp'] == "") {
            $logger->write('Phone number is empty');
            p_alert($translations['txt_invalid_access']  . " mt_hp");
        }
    } else {
        if ($_POST['mt_email'] == "") {
            $logger->write('Email is empty');
            p_alert($translations['txt_invalid_access']  . " mt_email");
        }
    }
    // if ($_POST['mt_token_id'] == "") {
    //     p_alert($translations['txt_invalid_access']  . " mt_token_id");
    // }
    // 휴대폰 번호에서 하이픈 제거
    $mt_hp = str_replace('-', '', $_POST['mt_hp']);
    $logger->write('Hyphens removed from phone number');

    // and, ios 구분
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $patternMobile = '/(iPhone|iPod|iPad|Android|Windows Phone)/i';

    if (preg_match($patternMobile, $userAgent)) {
        // 운영체제 확인
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $mt_os_check = 1;
            $logger->write('Operating system is iOS');
        } else {
            $mt_os_check = 0;
            $logger->write('Operating system is Android or other');
        }
    }
    
    $defaultCoordinates = [
        'ko' => ['lat' => '37.5666805', 'long' => '126.9784147'], // 서울
        'en' => ['lat' => '40.712776', 'long' => '-74.005974'],   // 뉴욕
        'es' => ['lat' => '40.416775', 'long' => '-3.703790'],    // 마드리드
        'ja' => ['lat' => '35.689487', 'long' => '139.691706'],   // 도쿄
        'vi' => ['lat' => '10.775845', 'long' => '106.692234'],   // 호치민
        'id' => ['lat' => '-6.208763', 'long' => '106.892149'],   // 자카르타
        'th' => ['lat' => '13.756331', 'long' => '100.501765'],   // 방콕
        'hi' => ['lat' => '28.613939', 'long' => '77.209021'],   // 뉴델리
    ];
    
    if (isset($userLang) && array_key_exists($userLang, $defaultCoordinates)) {
        $_SESSION['_mt_lat'] = $defaultCoordinates[$userLang]['lat'];
        $_SESSION['_mt_long'] = $defaultCoordinates[$userLang]['long'];
    } else {
        // 기본값 (예: 한국어) 설정 또는 오류 처리
        $_SESSION['_mt_lat'] = $defaultCoordinates['ko']['lat'];
        $_SESSION['_mt_long'] = $defaultCoordinates['ko']['long'];
    }

    if ($userLang == 'ko') {
        $DB->where('mt_id', $mt_hp);
        $logger->write('Searching for member by phone number');
    } else {
        $DB->where('mt_id', $_POST['mt_email']);
        $logger->write('Searching for member by email');
    }
    $mem_chk_row = $DB->getone('member_t');

    if ($mem_chk_row['mt_idx']) {
        $logger->write('Member already exists');
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
            "mt_lang" => $userLang,
            "mt_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('member_t', $arr_query);
        $logger->write('New member inserted into database');

        if ($_last_idx) {
            $_mt_idx   = $_SESSION['_mt_idx'] = $_last_idx;
            $_mt_level = $_SESSION['_mt_level'] = '2';
            $logger->write('New member session variables set');

            echo json_encode(array('result' => '_ok', 'mt_idx' => $_last_idx));
            exit;
        } else {
            $logger->write('Failed to insert new member');
            echo json_encode(array('result' => '_false', 'msg' => $translations['txt_enter_verification_code']));
        }
    }
}
if ($_POST['act'] == 'join_add_info') {
    if ($_POST['mt_name'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_name");
    }
    if ($_POST['mt_gender'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_gender");
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
    // p_gotourl("./join_agree");
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
        p_alert($translations['txt_invalid_access']  . " mt_agree");
    }

    p_gotourl("./join_done");
}
if ($_POST['act'] == "change_password") {
    if ($_POST['mt_hp'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_hp");
    }
    if ($_POST['mt_pass'] == "") {
        p_alert($translations['txt_invalid_access']  . " mt_pass");
    }
    if ($_POST['mt_pass_confirm'] == "") {
        p_alert($translations['txt_invalid_access']  . " mt_pass_confirm");
    }
    if ($_POST['mt_pass'] != $_POST['mt_pass_confirm']) {
        p_alert($translations['txt_invalid_access']  . " mt_pass mt_pass_confirm" . $_POST['mt_pass'] . " " . $_POST['mt_pass_confirm']);
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
if ($_POST['act'] == "join_delete") {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('member_t');
    p_gotourl("./join_entry");
}
if ($_POST['act'] == 'reset_pwd') {
    global $userLang;
    $logger->write("Step 1: 비밀번호 재설정 요청 시작");

    if ($_POST['mt_pass'] == "") {
        $logger->write("Error: 비밀번호가 입력되지 않음");
        p_alert($translations['txt_invalid_access']  . " mt_pass");
    }
    if ($_POST['mt_pass_confirm'] == "") {
        $logger->write("Error: 비밀번호 확인이 입력되지 않음");
        p_alert($translations['txt_invalid_access']  . " mt_pass_confirm");
    }
    if ($_POST['mt_pass'] != $_POST['mt_pass_confirm']) {
        $logger->write("Error: 비밀번호와 비밀번호 확인이 일치하지 않음");
        p_alert($translations['txt_invalid_access']  . " mt_pass mt_pass_confirm" . $_POST['mt_pass'] . " " . $_POST['mt_pass_confirm']);
    }

    $logger->write("Step 2: 회원 정보 확인 시작");
    $DB->where('mt_reset_token', $_POST['mt_reset_token']);
    $mem_chk_row = $DB->getone('member_t');

    $logger->write("Step 3: 회원 정보 업데이트 시작");
    unset($arr_query);
    $arr_query = array(
        "mt_pwd" =>  password_hash($_POST['mt_pass'], PASSWORD_DEFAULT),
        "mt_token_id" => $_POST['mt_token_id'] ? $_POST['mt_token_id'] : $_SESSION['_mt_token_id'],
        "mt_reset_token" => "",
        "mt_token_edate" => "",
    );

    $DB->update('member_t', $arr_query);

    $logger->write("Step 4: 비밀번호 재설정 완료");
    echo json_encode(array('result' => '_ok'));
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
