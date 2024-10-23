<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '1';
$h_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";
// 앱 체크(auth를 탓는지 체크)
if (!$_SESSION['_auth_chk']) {
    // 로그인 체크
    if (!isset($_SESSION['_mt_idx'])) {
        // frame 탔는지 체크
        if ($_SESSION['frame_chk'] == true && !isset($_SESSION['_mt_idx'])) {
            // frame 탔을 경우
            $_SESSION['frame_chk'] = false;
            alert($translations['txt_login_required'], './login', '');
        } else if(!isset($_SESSION['_mt_idx']) && $chk_mobile){ // mt_idx 값이 없고 모바일일 경우
            $_SESSION['frame_chk'] = false;
            alert($translations['txt_login_required'], './login', '');
        }else {
            // frame 안탔을 경우
            $_SESSION['frame_chk'] = true;
            header('Location: ./frame');
            exit;
        }
    } else { // 이미 로그인을 했을 경우
        // frame 탔을 경우
        if ($_SESSION['frame_chk'] == true) {
            $_SESSION['frame_chk'] = false;
        } else {
            // frame 안탔을 경우
            $_SESSION['frame_chk'] = true;
            header('Location: ./frame');
            exit;
        }
    }
}

if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_attempt_other_device'], './logout');
    }
}
?>

<script>
  // 새 창에서 열릴 URL
  const newWindowUrl = 'https://blog.naver.com/smapofficial/223539608474'; 

  // 페이지 로딩 시 새 창 열기
  window.onload = function() {
    window.open(newWindowUrl, '_blank'); 

    // 팝업 페이지 닫기 (선택 사항)
    window.close(); 
  };
</script>