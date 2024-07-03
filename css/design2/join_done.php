<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <div class="done_wrap">
            <lottie-player src="https://lottie.host/38e156d9-ac35-4f73-aa81-5717db995156/sWT27c6Jt5.json" background="transparent" class="mx-auto mt-lg-5 " speed="1"  style="width: 120px; height: 120px;" loop autoplay></lottie-player>
            <p class="tit_h3 text-center mt_20 line_h1_3 text-primary">반가워요. 이름 님</p>
            <p class="tit_h3 text-center text_dynamic  mt-3 line_h1_5">일정과 위치를 한번에!
                <span class="text-primary">Schdule + MAP!</span></p>
            <p class="tit_h3 text-center text_dynamic mt-3 line_h1_5">지금 시작해 볼까요?</p>
        </div>
        <div class="b_botton">
            <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='index.php'">시작하기!</button>
        </div>
    </div>
</div>