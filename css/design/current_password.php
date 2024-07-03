<?php
$title = "비밀번호 변경";
$b_menu = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>

<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">본인 확인을 위해
            현재 비밀번호를 입력해 주세요.
        </p>
        <form action="">
            <div class="mt-5">
                <div class="ip_wr">
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
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='change_password.php'">비밀번호 변경하기</button>
            </div>
        </form>
    </div>
</div>