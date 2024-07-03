<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">로그인 계정 정보를 입력하세요.</p>
        <form action="">
            <div class="mt-5">
                <div class="ip_wr mt-5 ip_valid">
                    <div class="ip_tit">
                        <h5 class="">이메일</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="test@test.com">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5>비밀번호</h5>
                    </div>
                    <input type="password" class="form-control" placeholder="비밀번호를 입력해주세요.">
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2">비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.</div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 비밀번호를 다시 확인해주세요</div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn fs_14 text_gray mx-auto btn-block" onclick="location.href='form_email.php'">비밀번호가 기억나지 않나요?</button>
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='index.php'">로그인하기</button>
            </div>
        </form>
    </div>
</div>