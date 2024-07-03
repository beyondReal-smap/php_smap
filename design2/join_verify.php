<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>

<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">입력하신 휴대폰번호로
            인증번호를 보냈어요.</p>
        <form action="">
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5>인증번호</h5>
                    </div>
                    <div class="form-row mt_06 ip_valid">
                        <div class="col-12">
                            <input type="text" class="form-control input_time_input" placeholder="6자리 숫자">
                            <span class="fc_red fs_15 fw_300 bg_gray_100 input_time">02:59</span>
                        </div>
                    </div>
                </div>
                <div class="ip_wr mt-5 ip_valid">
                    <div class="ip_tit">
                        <h5 class="">입력하신 휴대전화번호로 인증번호가 발송됩니다.</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="010-0000-0000">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                    <button type="button" class="btn fs_12 fc_primary rounded-pill bg_secondary text-center px_12 py_07 text_dynamic w_fit h_fit_im d-flex align-items-center mt-3">인증번호가 안와요! <i class="xi-arrow-right ml-2"></i></button>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='join_email.php'">입력했어요!</button>
            </div>
        </form>
    </div>
</div>