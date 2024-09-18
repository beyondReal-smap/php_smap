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
        <p class="text_dynamic fs_24 fw_700 text-left line_h1_3 pt_60"><?= translate("안전한 일상,", $userLang); ?>
            <?= translate("smap에 오신 것을", $userLang); ?>
            <?= translate("환영합니다!", $userLang); ?></p> <!-- "안전한 일상, smap에 오신 것을 환영합니다!" 번역 -->
        <p class="text_dynamic fs_12 text_light_gray mt-3 line_h1_2"><?= translate("빠른 회원가입으로 바로 사용해보세요!", $userLang); ?></p> <!-- "빠른 회원가입으로 바로 사용해보세요!" 번역 -->
        <div class="b_botton">
            <button type="button" class="btn btn-bg_gray btn-md fw_600 btn-block rounded-sm fs_14" onclick="location.href='./form_agree'"><?= translate("이메일로 가입하기", $userLang); ?></button> <!-- "이메일로 가입하기" 번역 -->
            <button type="button" class="btn btn-md btn-block fs_14 text-center line_h1_3" onclick="location.href='./login'"><?= translate("이미 가입하셨나요? 로그인 하기", $userLang); ?></button> <!-- "이미 가입하셨나요? 로그인 하기" 번역 -->
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>