<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
// $h_menu = '2';
$_SUB_HEAD_TITLE = "";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <div class="done_wrap">
            <lottie-player src="<?=CDN_HTTP?>/img/smap.json" background="transparent" class="mx-auto mt-lg-5 " speed="1" style="width: 120px; height: 120px;" loop autoplay></lottie-player>
            <p class="tit_h3 text-center mt_20 line_h1_3 text_dynamic"><?=$translations['txt_welcome']?></p>
            <p class="tit_h3 text-center text-primary"><?=$translations['txt_schedule_map']?></p>
            <p class="tit_h3 text-center text_dynamic mt-3 line_h1_5"><?=$translations['txt_start_now']?></p>
        </div>
        <div class="b_botton">
            <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='./permission'"><?=$translations['txt_start']?></button>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>