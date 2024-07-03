<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '';
$_SUB_HEAD_TITLE = "INTRO";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<style>
    .h_menu {
        display: none;
    }

    .container {
        height: 100vh;
    }
</style>

<div class="container">
    <div class="position-relative h-100">
        <p class="text_dynamic fs_24 fw_700 text-left line_h1_3 pt_60">안전한 일상,
            smap에 오신 것을
            환영합니다!
        </p>
        <p class="text_dynamic fs_12 text_light_gray mt-3 line_h1_2">빠른 회원가입으로 바로 사용해보세요!</p>
        <div class="b_botton">
            <button type="button" class="btn btn-bg_gray btn-md fw_600 btn-block rounded-sm fs_14" onclick="location.href='./form_agree'">이메일로 가입하기</button>
            <button type="button" class="btn btn-md btn-block fs_14 text-center line_h1_3" onclick="location.href='./login'">이미 가입하셨나요? 로그인 하기</button>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>