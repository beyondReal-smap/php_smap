<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "login") {
    if ($_POST['mt_hp'] == "") {
        p_alert("잘못된 접근입니다.");
    }
    if ($_POST['mt_pass'] == "") {
        p_alert("잘못된 접근입니다.");
    }
    $mt_hp = str_replace('-', '', $_POST['mt_hp']);

    $DB->where('mt_id', $mt_hp);
    $DB->where('mt_type', '1');
    $DB->where('(mt_level >= 2)');
    $DB->where('mt_status', '1');
    $DB->where('mt_show', 'Y');

    if ($DB->totalCount > 0) {
        p_alert("휴대폰번호 및 비밀번호가 올바르지 않습니다.\\n아이디, 비밀번호는 대문자, 소문자를 구분합니다.\\n<Caps Lock>키가 켜져 있는지 확인하시고 다시 입력하십시오.");
    } else {
        $row = $DB->getone('member_t');

        if (password_verify($_POST['mt_pass'], $row['mt_pwd'])|| $_SERVER['REMOTE_ADDR'] == '115.93.39.5') {
            
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
            
            unset($arr_query);
            $arr_query = array(
                "mt_lat" => $_SESSION['_mt_lat'],
                "mt_long" => $_SESSION['_mt_long'],
                "mt_os_check" => $mt_os_check,
                'mt_ldate' => $DB->now(),
            );

            if ($_SESSION['_mt_token_id']) {
                $arr_query['mt_token_id'] = $_SESSION['_mt_token_id'];

                setcookie('_mt_token_id', $_SESSION['_mt_token_id']);
                $_COOKIE['_mt_token_id'] = $_SESSION['_mt_token_id'];
            }

            $DB->where('mt_idx', $row['mt_idx']);

            $DB->update('member_t', $arr_query);

            $_mt_idx   = $_SESSION['_mt_idx']   = $row['mt_idx'];
            $_mt_id    = $_SESSION['_mt_id']    = $row['mt_id'];
            $_mt_hp   = $_SESSION['_mt_hp']    = $row['mt_hp'];
            $_mt_name  = $_SESSION['_mt_name']  = $row['mt_name'];
            $_mt_nickname  = $_SESSION['_mt_nickname']  = $row['mt_nickname'];
            $_mt_level = $_SESSION['_mt_level'] = $row['mt_level'];
            $_mt_file1 = $_SESSION['_mt_file1'] = CDN_HTTP . "/img/uploads/" . $row['mt_file1'] . "?v=" . time();
            /* 
            if ($row['mt_lat'] == '') {
                $row['mt_lat'] = '37.5666805';
            }
            $_mt_lat = $_SESSION['_mt_lat'] = $row['mt_lat'];
            if ($row['mt_long'] == '') {
                $row['mt_long'] = '126.9784147';
            }
            $_mt_long = $_SESSION['_mt_long'] = $row['mt_long']; 
            */
            if($chk_mobile){
?>
            <script>
                var message = {
                    "type": "memberLogin",
                };
                if (isAndroid()) {
                    window.smapAndroid.memberLogin();
                } else if (isiOS()) {
                    window.webkit.messageHandlers.smapIos.postMessage(message);
                }

                function isAndroid() {
                    return navigator.userAgent.match(/Android/i);
                }

                function isiOS() {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                }
            </script>
<?php
            }
            p_gotourl("./");
        } else {
            just_alert("아이디 및 비밀번호가 올바르지 않습니다.<br/>아이디, 비밀번호는 대문자, 소문자를 구분합니다.<br/>Caps Lock 키가 켜져 있는지 확인하시고 다시 입력하십시오.");
        }
    }
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
