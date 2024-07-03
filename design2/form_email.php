<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">이메일을 입력해주세요.</p>
        <p class="fs_12 fc_gray_600 mt-3 line_h1_2">이메일 확인을 위한 인증메일이 발송될 예정입니다.</p>
        <form action="" class="">
            <div class="mt-5">
                <div class="ip_wr ip_valid">
                    <div class="ip_tit">
                        <h5 class="">이메일</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="test@test.com">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='form_verify.php'">입력했어요!</button>
            </div>
        </form>
    </div>
</div>