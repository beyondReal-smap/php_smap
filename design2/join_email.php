<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic"> 이메일 주소를 입력해주세요.</p>
        <!-- <p class="fs_12 fc_gray_600 mt-3 line_h1_2">이메일 주소를 입력해주세요.</p> -->
        <form action="" class="">
            <div class="mt-5">
                <div class="ip_wr ip_valid">
                    <div class="ip_tit">
                        <h5 class="">이메일</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="test@test.com">
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

<!-- 존재하는 메일주소 -->
<div class="modal fade" id="dpl_email" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">이미 사용중인 메일주소에요.
                다른 메일주소를 입력해 주세요!</p>
            </div>
            <div class="modal-footer px-0 py-0">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close">확인하기</button>
            </div>
        </div>
    </div>
</div>