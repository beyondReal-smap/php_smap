<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "member_profile_get") {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $row = $DB->getone('member_t');

    if($row['mt_file1']) {
        $_SESSION['_mt_file1'] = CDN_HTTP."/img/uploads/".$row['mt_file1']."?v=".time();
        echo $_SESSION['_mt_file1'];
    } else {
        echo '';
    }
} elseif ($_POST['act'] == "form_agree") {
    if($_POST['mt_agree_all']) {
        $_SESSION['_mt_agree_all'] = 'Y';
    }

    if($_POST['mt_agree']) {
        foreach($_POST['mt_agree'] as $key => $val) {
            if($val) {
                $_SESSION['_mt_agree'.$val] = 'Y';
            }
        }
    } else {
        p_alert($translations['txt_invalid_access']  . " mt_agree");
    }

    p_gotourl("./form_email");
} elseif ($_POST['act'] == "chk_mt_id") {
    if($_POST['mt_id'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_id");
    }

    $DB->where('mt_id', $_POST['mt_id']);
    $row = $DB->getone('member_t');

    if($row['mt_idx']) {
        echo json_encode(false);
    } else {
        echo json_encode(true);
    }
} elseif ($_POST['act'] == "form_email") {
    if($_POST['mt_id']) {
        $DB->where('mt_id', $_POST['mt_id']);
        $row = $DB->getone('member_t');

        if($row['mt_idx']) {
            p_alert($translations['txt_invalid_access']  . " mt_idx");
        } else {
            $_SESSION['_mt_id_join'] = $_POST['mt_id'];
            $_SESSION['_m_confirm_num'] = mt_sms_make();

            //이메일 인증번호 전송
            $subject = '['.APP_TITLE.']'.$translations['txt_email_verification_subject'];
            $content = $translations['txt_email_verification_content'].$_SESSION['_m_confirm_num'];

            // $rtn1 = mailer_new(FNAME, FMAIL, $_POST['mt_id'], $_POST['mt_id'], $subject, $content);
        }
    } else {
        p_alert($translations['txt_invalid_access']  . " mt_id");
    }

    p_gotourl("./form_verify");
} elseif ($_POST['act'] == "re_send_mail") {
    if($_POST['mt_id']) {
        $DB->where('mt_id', $_POST['mt_id']);
        $row = $DB->getone('member_t');

        if($row['mt_idx']) {
            echo "N";
        } else {
            $_SESSION['_mt_id_join'] = $_POST['mt_id'];
            $_SESSION['_m_confirm_num'] = mt_sms_make();

            //이메일 인증번호 전송
            $subject = '['.APP_TITLE.']'.$translations['txt_email_verification_subject'];
            $content = $translations['txt_email_verification_content'].$_SESSION['_m_confirm_num'];

            // $rtn1 = mailer_new(FNAME, FMAIL, $_POST['mt_id'], $_POST['mt_id'], $subject, $content);

            echo "Y";
        }
    } else {
        echo "N";
    }
} elseif ($_POST['act'] == "chk_m_confirm") {
    if($_POST['m_confirm_num'] == '') {
        p_alert($translations['txt_invalid_access']  . " m_confirm_num");
    }

    // if($_POST['m_confirm_num'] == $_SESSION['_m_confirm_num']) {
    if($_POST['m_confirm_num'] == '123456') {
        echo json_encode(true);
    } else {
        echo json_encode(false);
    }
} elseif ($_POST['act'] == "form_verify") {
    if($_SESSION['_mt_id_join'] == '') {
        p_alert($translations['txt_invalid_access']  . " _mt_id_join");
    }
    // if($_POST['m_confirm_num'] != $_SESSION['_m_confirm_num']) {
    if($_POST['m_confirm_num'] != '123456') {
        p_alert($translations['txt_invalid_access']  . " m_confirm_num");
    }

    p_gotourl("./form_password");
} elseif ($_POST['act'] == "form_password") {
    if($_SESSION['_mt_id_join'] == '') {
        p_alert($translations['txt_invalid_access']  . " _mt_id_join");
    }
    if($_POST['mt_pass'] == "") {
        p_alert($translations['txt_invalid_access']  . " mt_pass");
    }
    if($_POST['mt_pass_confirm'] == "") {
        p_alert($translations['txt_invalid_access']  . " mt_pass_confirm");
    }
    if($_POST['mt_pass'] != $_POST['mt_pass_confirm']) {
        p_alert($translations['txt_invalid_access']  . " mt_pass mt_pass_confirm".$_POST['mt_pass']." ".$_POST['mt_pass_confirm']);
    }

    unset($arr_query);
    $arr_query = array(
        "mt_type" => 1,
        "mt_level" => 2,
        "mt_status" => 1,
        "mt_id" => $_SESSION['_mt_id_join'],
        "mt_pwd" =>  password_hash($_POST['mt_pass'], PASSWORD_DEFAULT),
        "mt_token_id" => $_SESSION['_mt_token_id'],
        "mt_onboarding" => "N",
        "mt_show" => "Y",
        "mt_agree1" => $_SESSION['_mt_agree1'],
        "mt_agree2" => $_SESSION['_mt_agree2'],
        "mt_agree3" => $_SESSION['_mt_agree3'],
        "mt_agree4" => $_SESSION['_mt_agree4'],
        "mt_agree5" => $_SESSION['_mt_agree5'],
        "mt_push1" => "Y",
        "mt_lat" => $_SESSION['_mt_lat'],
        "mt_long" => $_SESSION['_mt_long'],
        "mt_wdate" => "now()",
    );

    $_last_idx = $DB->insert('member_t', $arr_query);

    if($_last_idx) {
        $_mt_idx   = $_SESSION['_mt_idx']   = $_last_idx;
        $_mt_level = $_SESSION['_mt_level'] = '2';

        $_SESSION['_mt_id_join'] = '';
        $_SESSION['_mt_agree_all'] = '';
        $_SESSION['_mt_agree1'] = '';
        $_SESSION['_mt_agree2'] = '';
        $_SESSION['_mt_agree3'] = '';
        $_SESSION['_mt_agree4'] = '';
        $_SESSION['_mt_agree5'] = '';
        $_SESSION['_m_confirm_num'] = '';

        p_gotourl("./form_add_info");
    } else {
        p_alert($translations['txt_invalid_access']  . " _last_idx");
    }
} elseif ($_POST['act'] == "chk_mt_nick") {
    if($_POST['mt_nickname'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_nickname");
    }

    $DB->where('mt_nickname', $_POST['mt_nickname']);
    $row = $DB->getone('member_t');

    if($row['mt_idx']) {
        if($row['mt_idx'] == $_SESSION['_mt_idx']){
            echo json_encode(true);
        }else{
        echo json_encode(false);
        }
    } else {
        echo json_encode(true);
    }
} elseif ($_POST['act'] == "form_add_info") {
    if($_POST['mt_name'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_name");
    }
    if($_POST['mt_nickname'] == '') {
        p_alert($translations['txt_invalid_access']  . " mt_nickname");
    }

    unset($arr_query);
    $arr_query = array(
        "mt_name" => $_POST['mt_name'],
        "mt_nickname" => $_POST['mt_nickname'],
        "mt_hp" => $_POST['mt_hp'],
        "mt_birth" => $_POST['mt_birth'],
        "mt_gender" => $_POST['mt_gender'],
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    $DB->update('member_t', $arr_query);

    $_mt_name  = $_SESSION['_mt_name']  = $_POST['mt_name'];
    $_mt_nickname  = $_SESSION['_mt_nickname']  = $_POST['mt_nickname'];

    p_gotourl("./join_done");
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
