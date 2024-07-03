<?php
$title = "";
$_GET['hd_num'] = '';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 test_dynamic">비욘드리얼과 함께라면, 
        소중한 사람들과의 거리가     
        훨씬 가까워집니다.</p>
        <p class="fc_gray_600 test_dynamic mt-3">위치 기반 일정 관리로 더 빠르고 정확하게 소통해요.</p>
        <form action="">
            <div class="mt-5">
                <div class="ip_wr mt-5 ip_valid">
                    <div class="ip_tit">
                        <h5 class="">휴대전화번호</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="010-0000-0000">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
            </div>
            <div class="mt-5">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='index.php'">입력했어요!</button>
            </div>
        </form>
    </div>
</div>