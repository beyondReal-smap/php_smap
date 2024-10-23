<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

$logger->write("로그인 시도 시작");

if ($_POST['mt_id']=="") {
    $logger->write("mt_id가 비어 있음");
    p_alert("잘못된 접근입니다.");
}
if ($_POST['mt_pass']=="") {
    $logger->write("mt_pass가 비어 있음");
    p_alert("잘못된 접근입니다.");
}

$DB->where('mt_id', $_POST['mt_id']);
$DB->where('mt_level', '9');
$DB->where('mt_status', '1');

if ($DB->totalCount > 0) {
    $logger->write("관리자 계정으로 잘못된 접근 시도");
    p_alert("잘못된 접근입니다.");
} else {
    $logger->write("일반 사용자 로그인 시도");
    $row = $DB->getone('member_t');

    if (password_verify($_POST['mt_pass'], $row['mt_pwd'])) {
        $logger->write("비밀번호 검증 성공");
        unset($arr_query);
        $arr_query = array(
            'mt_ldate' => "now()",
        );

        $DB->where('mt_idx', $row['mt_idx']);

        $DB->update('member_t', $arr_query);
        $logger->write("로그인 날짜 업데이트 성공");

        $_mt_idx   = $_SESSION['_mt_idx']   = $row['mt_idx'];
        $_mt_id    = $_SESSION['_mt_id']    = $row['mt_id'];
        $_mt_name  = $_SESSION['_mt_name']  = $row['mt_name'];
        $_mt_level = $_SESSION['_mt_level'] = $row['mt_level'];

        $logger->write("세션 설정 완료");
        p_gotourl("./");
    } else {
        $logger->write("비밀번호 검증 실패");
        p_alert("아이디 및 비밀번호가 올바르지 않습니다.<br/>아이디, 비밀번호는 대문자, 소문자를 구분합니다.<br/><Caps Lock>키가 켜져 있는지 확인하시고 다시 입력하십시오.");
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";