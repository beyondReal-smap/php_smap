<?php
$title = "초대코드입력";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic">빨리 친구와 함께 해볼까요?</p>
        <p class="fs_12 fc_gray_600 mt-3 text_dynamic line_h1_2">앱을 닫고 친구가 보낸 링크를 누르면, 
        코드 입력은 자동으로 해결돼요.
        </p>
        <form action="" class="">
            <div class="mt-5">
                <div class="ip_wr ip_valid">
                    <div class="ip_tit">
                        <h5 class="">초대코드</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="초대코드를 입력해주세요.">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 이메일형식에 맞게 입력해주세요.</div>
                </div>
            </div>
            <div class="b_botton">
                <!-- 이메일 중복일 경우 data-toggle="modal" data-target="#dpl_email" 버튼태기에 넣어주세요 -->
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='join_psd.php'">입력했어요!</button>
            </div>
        </form>
    </div>
</div>

