<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = "";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <div class="done_wrap">
            <lottie-player src="<?=CDN_HTTP?>/img/smap.json" background="transparent" class="mx-auto mt-lg-5 " speed="1" style="width: 120px; height: 120px;" loop autoplay></lottie-player>
            <p class="tit_h3 text-center mt_20 line_h1_3 text-primary">반가워요. <?=$_SESSION['_mt_name']?> 님</p>
            <p class="fs_16 text-center fw_700 text_dynamic  mt-3 line_h1_5">일정과 위치를 한번에!
                <span class=" tit_h3 text-primary">Schdule + MAP!</span>
            </p>
            <p class="tit_h3 text-center text_dynamic mt-3 line_h1_5">지금 시작해 볼까요?</p>
        </div>
        <div class="b_botton">
            <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='./permission'">시작하기!</button>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>