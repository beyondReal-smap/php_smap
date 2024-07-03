<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="">
        <div class="done_wrap text-center">
            <img src="./img/warring.png" width="82px" alt="자료없음"/>
            <p class="tit_h3 mt_20 line_h1_3">해당 메일로 가입된 계정이 없습니다.</p>
            <p class="fs_14 fc_gray_600 mt-3 line_h1_3 text_dynamic">인증하신 이메일로 가입된 계정이 없습니다.
            아래 회원가입 버튼을 눌러 현재 인증한 이메일로
            회원가입을 진행할 수 있습니다.</p>
        </div>
        <div class="b_botton">
            <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='form_agree.php'">회원가입하러 가기</button>
        </div>
    </div>
</div>