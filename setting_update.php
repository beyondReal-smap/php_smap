<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "mt_push_chg") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }

    if ($_POST['mt_push_chg'] == 'Y') {
        $mt_push_chg_t = 'N';
    } else {
        $mt_push_chg_t = 'Y';
    }

    unset($arr_query);
    $arr_query = array(
        "mt_push1" => $mt_push_chg_t,
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    $DB->update('member_t', $arr_query);

    echo $mt_push_chg_t;
} elseif ($_POST['act'] == "setting_modify") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['mt_name'] == '') {
        p_alert("잘못된 접근입니다. mt_name");
    }
    if ($_POST['mt_nickname'] == '') {
        p_alert("잘못된 접근입니다. mt_nickname");
    }

    unset($arr_query);
    $arr_query = array(
        "mt_nickname" => $_POST['mt_nickname'],
        "mt_birth" => $_POST['mt_birth'],
        "mt_gender" => $_POST['mt_gender'],
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    $DB->update('member_t', $arr_query);

    $_mt_nickname  = $_SESSION['_mt_nickname']  = $_POST['mt_nickname'];

    p_gotourl("./setting_list");
} elseif ($_POST['act'] == "current_password" || $_POST['act'] == "withdraw") {
    if ($_SESSION['_mt_idx'] == '') {
        palert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['mt_pass'] == "") {
        p_alert("잘못된 접근입니다. mt_pass");
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    if ($DB->totalCount > 0) {
        p_alert("잘못된 접근입니다. totalCount");
    } else {
        $row = $DB->getone('member_t');

        if (password_verify($_POST['mt_pass'], $row['mt_pwd'])) {
            if ($_POST['act'] == 'withdraw') {
                echo "Y";
            } else {
                p_gotourl("./change_password");
            }
        } else {
            p_alert("아이디 및 비밀번호가 올바르지 않습니다.<br/>아이디, 비밀번호는 대문자, 소문자를 구분합니다.<br/><Caps Lock>키가 켜져 있는지 확인하시고 다시 입력하십시오.");
        }
    }
} elseif ($_POST['act'] == "change_password") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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

    unset($arr_query);
    $arr_query = array(
        "mt_pwd" =>  password_hash($_POST['mt_pass'], PASSWORD_DEFAULT),
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    $DB->update('member_t', $arr_query);

    p_gotourl("./setting_list");
} elseif ($_POST['act'] == "withdraw_on") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['mt_retire_chk'] == "") {
        p_alert("잘못된 접근입니다. mt_retire_chk");
    }

    $mt_info = get_member_t_info();
    $sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
    if ($sgt_cnt > 0) {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where('sgdt_owner_chk', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        $sgdt_info = $DB->getone('smap_group_detail_t');
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        $sgdt_info = $DB->getone('smap_group_detail_t');
    }

    unset($arr_query);
    $arr_query = array(
        "mt_id" =>  '',
        "mt_hp" =>  '',
        "mt_token_id" => '',
        "mt_id_retire" =>  $mt_info['mt_id'],
        "mt_retire_chk" =>  $_POST['mt_retire_chk'],
        "mt_retire_etc" =>  $_POST['mt_retire_etc'],
        "mt_level" =>  '1',
        "mt_status" =>  '2',
        "mt_show" =>  'N',
        "mt_retire_level" =>  $mt_info['mt_level'],
        "mt_rdate" =>  $DB->now(),
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->update('member_t', $arr_query);


    /*     unset($arr_query);
    $arr_query = array(
        "mt_idx" => $mt_info['mt_idx'],
        "mt_type" => $mt_info['mt_type'],
        "mt_level" =>  '1',
        "mt_recommend_chk" => $mt_info['mt_recommend_chk'],
        "mt_plan_date" => $mt_info['mt_plan_date'],
        "mt_rec_date" => $mt_info['mt_rec_date'],
        "mt_status" =>  '2',
        "mt_id" => $mt_info['mt_id'],
        "mt_pwd" => $mt_info['mt_pwd'],
        "mt_pwd_cnt" => $mt_info['mt_pwd_cnt'],
        "mt_token_id" => $mt_info['mt_token_id'],
        "mt_name" => $mt_info['mt_name'],
        "mt_nickname" => $mt_info['mt_nickname'],
        "mt_hp" => $mt_info['mt_hp'],
        "mt_email" => $mt_info['mt_email'],
        "mt_birth" => $mt_info['mt_birth'],
        "mt_gender" => $mt_info['mt_gender'],
        "mt_file1" => $mt_info['mt_file1'],
        "mt_show" =>  'N',
        "mt_agree1" => $mt_info['mt_agree1'],
        "mt_agree2" => $mt_info['mt_agree2'],
        "mt_agree3" => $mt_info['mt_agree3'],
        "mt_agree4" => $mt_info['mt_agree4'],
        "mt_agree5" => $mt_info['mt_agree5'],
        "mt_push1" => $mt_info['mt_push1'],
        "mt_lat" => $mt_info['mt_lat'],
        "mt_long" => $mt_info['mt_long'],
        "mt_sido" => $mt_info['mt_sido'],
        "mt_gu" => $mt_info['mt_gu'],
        "mt_dong" => $mt_info['mt_dong'],
        "mt_onboarding" => $mt_info['mt_onboarding'],
        "mt_weather_pop" => $mt_info['mt_weather_pop'],
        "mt_weather_sky" => $mt_info['mt_weather_sky'],
        "mt_weather_tmn" => $mt_info['mt_weather_tmn'],
        "mt_weather_tmx" => $mt_info['mt_weather_tmx'],
        "mt_weather_date" => $mt_info['mt_weather_date'],
        "mt_wdate" => $mt_info['mt_wdate'],
        "mt_ldate" => $mt_info['mt_ldate'],
        "mt_lgdate" => $mt_info['mt_lgdate'],
        "mt_retire_chk" =>  $_POST['mt_retire_chk'],
        "mt_retire_etc" =>  $_POST['mt_retire_etc'],
        "mt_retire_level" =>  $mt_info['mt_level'],
        "mt_rdate" =>  $DB->now(),
    );
    $DB->insert('retire_member_t',$arr_query); 
*/

    //회원 장소로그 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('member_location_log_t');
    //회원 푸쉬 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('push_log_t');
    //회원 QNA DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('qna_t');
    //회원 연락처 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_contact_t');
    //회원 그룹초대 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_invite_t');
    //회원 최적경로 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_loadpath_log_t');
    //회원 장소정보 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_location_member_t');
    //회원 내장소 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_location_t');
    //회원 내장소 내가 입력해준 DB 삭제
    $DB->where('insert_mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_location_t');
    //회원 일정 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_schedule_t');
    //회원 일정 입력받은 DB 삭제
    $DB->where('sgdt_idx', $sgdt_info['sgdt_idx']);
    $DB->delete('smap_schedule_t');


    if ($sgt_cnt > 0) { // 내가 오너일 경우 리더가 있다면 리더에게 오너 권한 부여
        $DB->where('sgt_idx', $sgdt_info['sgt_idx']);
        $DB->where('sgdt_leader_chk', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        $DB->orderby('sgdt_wdate', 'asc');
        $sgdt_leader_info = $DB->getone('smap_group_detail_t');

        if ($sgdt_leader_info['sgdt_idx']) { // 리더가 있다면 제일 처음 들어온 리더에게 오너 권한 부여
            unset($arr_query);
            $arr_query = array(
                "sgdt_owner_chk" =>  'Y',
                "sgdt_leader_chk" =>  'N',
                "sgdt_group_chk" =>  'Y',
                "sgdt_udate" => $DB->now()
            );
            $DB->where('sgdt_idx', $sgdt_leader_info['sgdt_idx']);
            $DB->update('smap_group_detail_t', $arr_query);

            unset($arr_query);
            $arr_query = array(
                "mt_idx" =>  $sgdt_leader_info['mt_idx'],
                "sgt_udate" => $DB->now()
            );
            $DB->where('sgt_idx', $sgdt_leader_info['sgt_idx']);
            $DB->update('smap_group_t', $arr_query);
        } else { // 리더가 없다면 제일 처음 들어온 그룹원에게 오너 권한 부여
            $DB->where('sgt_idx', $sgdt_info['sgt_idx']);
            $DB->where('sgdt_owner_chk', 'N');
            $DB->where('sgdt_leader_chk', 'N');
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
            $DB->where('sgdt_show', 'Y');
            $DB->orderby('sgdt_wdate', 'asc');
            $sgdt_group_info = $DB->getone('smap_group_detail_t');

            if ($sgdt_group_info['sgdt_idx']) {
                unset($arr_query);
                $arr_query = array(
                    "sgdt_owner_chk" =>  'Y',
                    "sgdt_leader_chk" =>  'N',
                    "sgdt_group_chk" =>  'Y',
                    "sgdt_udate" => $DB->now()
                );
                $DB->where('sgdt_idx', $sgdt_group_info['sgdt_idx']);
                $DB->update('smap_group_detail_t', $arr_query);

                unset($arr_query);
                $arr_query = array(
                    "mt_idx" =>  $sgdt_group_info['mt_idx'],
                    "sgt_udate" => $DB->now()
                );
                $DB->where('sgt_idx', $sgdt_group_info['sgt_idx']);
                $DB->update('smap_group_t', $arr_query);
            }
        }
    }
    //회원 그룹 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_group_t');
    //회원 그룹상세 DB 삭제
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->delete('smap_group_detail_t');

    unset($_SESSION['_mt_idx']);
    unset($_SESSION['_mt_id']);
    unset($_SESSION['_mt_hp']);
    unset($_SESSION['_mt_name']);
    unset($_SESSION['_mt_nickname']);
    unset($_SESSION['_mt_level']);
    unset($_SESSION['_mt_file1']);
    if (!$chk_mobile) {
        unset($_COOKIE);
    }
    echo "Y";
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
