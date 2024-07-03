<?php
$title = "새 그룹";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <form action="">
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">그룹명</h5>
                        <p class="text_num fs_12 fc_gray_600">(0/15)</p>
                    </div>
                    <input type="text" class="form-control" placeholder="그룹명 입력">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='group.php'">새 그룹명 저장</button>
            </div>
        </form>
    </div>
</div>