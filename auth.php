<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if($chk_mobile) {
    if ($_GET['mt_lat'] == '') {
        $_GET['mt_lat'] = '37.5666805';
    }
    if ($_GET['mt_long'] == '') {
        $_GET['mt_long'] = '126.9784147';
    }
    $_SESSION['_auth_chk'] = 'Y';
    $_SESSION['_mt_token_id'] = $_GET['mt_token_id'];
    $_SESSION['_mt_lat'] = $_GET['mt_lat'];
    $_SESSION['_mt_long'] = $_GET['mt_long'];
    $_SESSION['_event_url'] = $_GET['event_url'];

    if ($_GET['mt_token_id'] == '') { //PC 접근
        gotourl('./');
    } else { //앱접근
        if ($_GET['mt_token_id']) {
            $DB->where('mt_token_id', $_GET['mt_token_id']);
            $row = $DB->getone('member_t');

            if ($row['mt_idx']) {
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
                unset($arr_query);
                $arr_query = array(
                    "mt_lat" => $_GET['mt_lat'],
                    "mt_long" => $_GET['mt_long'],
                    "mt_os_check" => $mt_os_check
                );

                $DB->where('mt_idx', $row['mt_idx']);
                $DB->update('member_t', $arr_query);

                $_mt_idx   = $_SESSION['_mt_idx']   = $row['mt_idx'];
                $_mt_id    = $_SESSION['_mt_id']    = $row['mt_id'];
                $_mt_name  = $_SESSION['_mt_name']  = $row['mt_name'];
                $_mt_nickname  = $_SESSION['_mt_nickname']  = $row['mt_nickname'];
                $_mt_level = $_SESSION['_mt_level'] = $row['mt_level'];
                $_mt_file1 = $_SESSION['_mt_file1'] = CDN_HTTP . "/img/uploads/" . $row['mt_file1'] . "?v=" . time();
                setcookie('_mt_token_id', $_SESSION['_mt_token_id']);
                $_COOKIE['_mt_token_id'] = $_SESSION['_mt_token_id'];


                unset($arr_query);
                $arr_query = array(
                    "mt_token_id" => $_SESSION['_mt_token_id'],
                    "mt_token_id_cookie" => $_COOKIE['_mt_token_id'],
                    "mt_lat" => $_SESSION['_mt_lat'],
                    "mt_long" => $_SESSION['_mt_long'],
                    "event_url" => $_SESSION['_event_url'],
                    "referer_url" => $_SERVER['HTTP_REFERER'],
                    "now_url" => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    "agent" => $_SERVER['HTTP_USER_AGENT'],
                    "ip" => $_SERVER['REMOTE_ADDR'],
                    "auth_chk" => $_SESSION['_auth_chk'],
                    "wdate" => $DB->now(),
                );
                $DB->insert('page_log_t', $arr_query);

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
                if ($_SESSION['_event_url']) {
                    $eventUrl = $_SESSION['_event_url'];
                    // 정규표현식 패턴
                    $pattern = '/^SMAP/';
                    // sit_code의 값을 얻음
                    if (isset($eventUrl)) {
                        if (preg_match($pattern, $eventUrl)) {
                            $sitCode = $_SESSION['_event_url'];
                            $DB->where('sit_code', $sitCode);
                            $DB->where('sit_status', '2');
                            $sit_row = $DB->getone('smap_invite_t');
                            if ($sit_row['sit_idx']) {
                                $DB->where('sgt_idx', $sit_row['sgt_idx']);
                                $DB->where('sgt_show', 'Y');
                                $sgt_row = $DB->getone('smap_group_t');
                                if ($sgt_row['sgt_idx']) {
                                    // 다른그룹에 속해있는지 확인
                                    $DB->where('mt_idx', $_SESSION['_mt_idx']);
                                    $DB->where('sgdt_discharge', 'N');
                                    $DB->where('sgdt_exit', 'N');
                                    $DB->where('sgdt_show', 'Y');
                                    $sgdt_list = $DB->get('smap_group_detail_t');
                                    $sgdt_count = count($sgdt_list);
                                    if ($sgdt_count > 0) {
                                        //이미 다른그룹에 속해있음
                                        gotourl('./');
                                    } else {
                                        // 그룹오너 등급 확인하기
                                        $DB->where('mt_idx', $sgt_row['mt_idx']);
                                        $owner_row = $DB->getone('member_t');
                                        if ($owner_row['mt_level'] == '5') { //오너가 유료회원이면
                                            $group_count = 10;
                                        } else {
                                            $group_count = 4;
                                        }
                                        // 해당 그룹 인원 확인
                                        $DB->where('sgt_idx', $sit_row['sgt_idx']);
                                        $DB->where('sgdt_discharge', 'N');
                                        $DB->where('sgdt_exit', 'N');
                                        $DB->where('sgdt_show', 'Y');
                                        $sgdt_list = $DB->get('smap_group_detail_t');
                                        $sgdt_count = count($sgdt_list);
                                        if ($sgdt_count > $group_count) {
                                            //이미 인원마감
                                            gotourl('./');
                                        } else {
                                            unset($arr_query);
                                            $arr_query = array(
                                                "sit_status" => '3',
                                                "sit_adate" => $DB->now(),
                                            );
                                            $DB->where('sit_idx', $sit_row['sit_idx']);
                                            $DB->update('smap_invite_t', $arr_query);

                                            unset($arr_query);
                                            $arr_query = array(
                                                "sgt_idx" => $sit_row['sgt_idx'],
                                                "mt_idx" => $_SESSION['_mt_idx'],
                                                "sgdt_owner_chk" => 'N',
                                                "sgdt_leader_chk" => 'N',
                                                "sgdt_discharge" => 'N',
                                                "sgdt_group_chk" => 'D',
                                                "sgdt_exit" => 'N',
                                                "sgdt_show" => 'Y',
                                                "sgdt_push_chk" => 'Y',
                                                "sgdt_wdate" => $DB->now(),
                                            );
                                            $_last_idx = $DB->insert('smap_group_detail_t', $arr_query);

                                            unset($arr_query);
                                            $arr_query = array(
                                                "mt_idx" => $sgt_row['mt_idx'],
                                                "sgdt_idx" => $_last_idx,
                                                "sgdt_mt_idx" => $_SESSION['_mt_idx'],
                                                "slmt_wdate" => $DB->now(),
                                            );
                                            $_last_idx = $DB->insert('smap_location_member_t', $arr_query);
                                            gotourl('./group');
                                        }
                                    }
                                } else {
                                    gotourl('./');
                                }
                            } else {
                                gotourl('./');
                            }
                        } else {
                            gotourl($_SESSION['_event_url']);                            
                        }
                    } else {
                        gotourl($_SESSION['_event_url']);
                    }
                } else {
                    gotourl('./');
                }
            } else {

                setcookie('_mt_token_id', $_SESSION['_mt_token_id']);
                $_COOKIE['_mt_token_id'] = $_SESSION['_mt_token_id'];


                unset($arr_query);
                $arr_query = array(
                    "mt_token_id" => $_SESSION['_mt_token_id'],
                    "mt_token_id_cookie" => $_COOKIE['_mt_token_id'],
                    "mt_lat" => $_SESSION['_mt_lat'],
                    "mt_long" => $_SESSION['_mt_long'],
                    "event_url" => $_SESSION['_event_url'],
                    "referer_url" => $_SERVER['HTTP_REFERER'],
                    "now_url" => $_SERVER['REQUEST_URI'],
                    "agent" => $_SERVER['HTTP_USER_AGENT'],
                    "ip" => $_SERVER['REMOTE_ADDR'],
                    "wdate" => $DB->now(),
                );
                $DB->insert('page_log_t', $arr_query);
                gotourl('./join_entry');
            }
        } else {
            gotourl('./join_entry');
        }
    }
} else {
    gotourl('./frame');
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
