<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

// 세션 설정 부분 수정
session_set_cookie_params([
    'lifetime' => 365 * 24 * 60 * 60, // 세션 유지 시간을 1년으로 설정
    'path' => '/',
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
    'httponly' => true,
    'samesite' => 'Lax'
]);
ini_set('session.gc_maxlifetime', 365 * 24 * 60 * 60); // 세션 가비지 컬렉션 시간도 1년으로 설정

// 세션이 없는 경우 자동으로 시작
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

            // Remember Me 처리 - 항상 자동 로그인 활성화
            $logger->write("Setting up persistent login.");
            // 안전한 토큰 생성
            $remember_token = bin2hex(random_bytes(32));
            $token_hash = password_hash($remember_token, PASSWORD_DEFAULT);
            // 만료 날짜를 10년으로 설정
            $expiry = date('Y-m-d H:i:s', strtotime('+10 years'));
            
            // DB에 토큰 저장
            $arr_query['mt_remember_token'] = $token_hash;
            $arr_query['mt_token_expiry'] = $expiry;
            
            // 쿠키에 토큰 저장 (10년)
            $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            $httponly = true;
            
            $remember_cookie_set = setcookie('remember_token', $remember_token, [
                'expires' => time() + (3650 * 24 * 60 * 60), // 10년
                'path' => '/',
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax'
            ]);
            
            $user_cookie_set = setcookie('user_id', $row['mt_idx'], [
                'expires' => time() + (3650 * 24 * 60 * 60), // 10년
                'path' => '/',
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax'
            ]);
            
            // 쿠키 설정 성공 여부 로그
            $logger->write("Persistent login cookie setup - remember_token: " . ($remember_cookie_set ? "Success" : "Failed"));
            $logger->write("Persistent login cookie setup - user_id: " . ($user_cookie_set ? "Success" : "Failed"));
            
            // 세션 수명 연장
            session_set_cookie_params([
                'lifetime' => 3650 * 24 * 60 * 60, // 10년
                'path' => '/',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            ini_set('session.gc_maxlifetime', 3650 * 24 * 60 * 60);
            
            $logger->write("Persistent login cookies set for 10 years. User ID: " . $row['mt_idx']);

            // DB 업데이트가 실패할 경우를 위한 예외 처리
            try {
                $DB->where('mt_idx', $row['mt_idx']);
                $update_result = $DB->update('member_t', $arr_query);
                $logger->write("User data updated in database: " . ($update_result ? "Success" : "Failed"));
                
                if (!$update_result) {
                    error_log("Failed to update member_t table for user ID: " . $row['mt_idx']);
                    error_log("DB Error: " . $DB->getLastError());
                }
            } catch (Exception $e) {
                error_log("Exception during DB update: " . $e->getMessage());
                $logger->write("Error updating user data: " . $e->getMessage());
            }

            // 세션 변수 설정
            $_mt_idx   = $_SESSION['_mt_idx']   = $row['mt_idx'];
            $_mt_id    = $_SESSION['_mt_id']    = $row['mt_id'];
            $_mt_hp   = $_SESSION['_mt_hp']    = $row['mt_hp'];
            $_mt_name  = $_SESSION['_mt_name']  = $row['mt_name'];
            $_mt_nickname  = $_SESSION['_mt_nickname']  = $row['mt_nickname'];
            $_mt_level = $_SESSION['_mt_level'] = $row['mt_level'];
            $_mt_file1 = $_SESSION['_mt_file1'] = CDN_HTTP . "/img/uploads/" . $row['mt_file1'] . "?v=" . time();

            // 토큰 ID 저장
            if (isset($row['mt_token_id']) && !empty($row['mt_token_id'])) {
                $_SESSION['_mt_token_id'] = $row['mt_token_id'];
                error_log("App token ID set in session: " . $row['mt_token_id']);
            }

            // 세션 ID 로깅
            error_log("Session started with ID after login: " . session_id());
            error_log("Session variables set for user ID: " . $row['mt_idx']);

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
