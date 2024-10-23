<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "mt_push_chg") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
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
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['mt_name'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_name");
    }
    if ($_POST['mt_nickname'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_nickname");
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
        p_alert($translations['login_required']  . "");
    }
    if ($_POST['mt_pass'] == "") {
        p_alert($translations['txt_invalid_access']  . " mt_pass");
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    if ($DB->totalCount > 0) {
        p_alert($translations['txt_invalid_access']  . " totalCount");
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
        p_alert($translations['txt_login_required'], './login', '');
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

    unset($arr_query);
    $arr_query = array(
        "mt_pwd" =>  password_hash($_POST['mt_pass'], PASSWORD_DEFAULT),
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    $DB->update('member_t', $arr_query);

    p_gotourl("./setting_list");
} elseif ($_POST['act'] == "withdraw_on") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['mt_retire_chk'] == "") {
        p_alert($translations['txt_invalid_access']  . " mt_retire_chk");
    }

    $mt_info = get_member_t_info();
    $sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']);

    $sgdt_info = get_group_detail_info($_SESSION['_mt_idx'], $sgt_cnt);

    update_member_info($_SESSION['_mt_idx'], $mt_info);

    delete_member_related_data($_SESSION['_mt_idx']);

    if ($sgt_cnt > 0) {
        transfer_ownership($sgdt_info);
    }

    function get_group_detail_info($mt_idx, $sgt_cnt) {
        global $DB;
        if ($sgt_cnt > 0) {
            $DB->where('mt_idx', $mt_idx);
            $DB->where('sgdt_owner_chk', 'Y');
        } else {
            $DB->where('mt_idx', $mt_idx);
        }
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        return $DB->getone('smap_group_detail_t');
    }

    function update_member_info($mt_idx, $mt_info) {
        global $DB;
        $arr_query = array(
            "mt_id" => '',
            "mt_hp" => '',
            "mt_token_id" => '',
            "mt_id_retire" => $mt_info['mt_id'],
            "mt_retire_chk" => $_POST['mt_retire_chk'],
            "mt_retire_etc" => $_POST['mt_retire_etc'],
            "mt_level" => '1',
            "mt_status" => '2',
            "mt_show" => 'N',
            "mt_retire_level" => $mt_info['mt_level'],
            "mt_rdate" => $DB->now(),
        );
        $DB->where('mt_idx', $mt_idx);
        $DB->update('member_t', $arr_query);
    }

    function delete_member_related_data($mt_idx) {
        global $DB;
        $tables = [
            'member_location_log_t', 'push_log_t', 'qna_t', 'smap_contact_t',
            'smap_invite_t', 'smap_loadpath_log_t', 'smap_location_member_t',
            'smap_location_t', 'smap_schedule_t'
        ];
        foreach ($tables as $table) {
            $DB->where('mt_idx', $mt_idx);
            $DB->delete($table);
        }
        $DB->where('insert_mt_idx', $mt_idx);
        $DB->delete('smap_location_t');
    }

    function transfer_ownership($sgdt_info) {
        global $DB;
        $new_owner = get_new_owner($sgdt_info['sgt_idx']);
        if ($new_owner) {
            update_group_detail($new_owner['sgdt_idx']);
            update_group($new_owner['sgt_idx'], $new_owner['mt_idx']);
        }
    }

    function get_new_owner($sgt_idx) {
        global $DB;
        $DB->where('sgt_idx', $sgt_idx);
        $DB->where('sgdt_leader_chk', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        $DB->orderby('sgdt_wdate', 'asc');
        $new_owner = $DB->getone('smap_group_detail_t');

        if (!$new_owner) {
            $DB->where('sgt_idx', $sgt_idx);
            $DB->where('sgdt_owner_chk', 'N');
            $DB->where('sgdt_leader_chk', 'N');
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
            $DB->where('sgdt_show', 'Y');
            $DB->orderby('sgdt_wdate', 'asc');
            $new_owner = $DB->getone('smap_group_detail_t');
        }

        return $new_owner;
    }

    function update_group_detail($sgdt_idx) {
        global $DB;
        $arr_query = array(
            "sgdt_owner_chk" => 'Y',
            "sgdt_leader_chk" => 'N',
            "sgdt_group_chk" => 'Y',
            "sgdt_udate" => $DB->now()
        );
        $DB->where('sgdt_idx', $sgdt_idx);
        $DB->update('smap_group_detail_t', $arr_query);
    }

    function update_group($sgt_idx, $mt_idx) {
        global $DB;
        $arr_query = array(
            "mt_idx" => $mt_idx,
            "sgt_udate" => $DB->now()
        );
        $DB->where('sgt_idx', $sgt_idx);
        $DB->update('smap_group_t', $arr_query);
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
} elseif ($_POST['act'] == "update_map") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['mt_map'] == "") {
        p_alert($translations['txt_invalid_access']  . " mt_map");
    }

    unset($arr_query);
    $arr_query = array(
        "mt_map" =>  $_POST['mt_map'],
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->update('member_t', $arr_query);

    echo "Y";
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
