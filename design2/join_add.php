<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">회원 정보를 입력해 주세요</p>
        <form action="">
            <div class="mt-5">
                <div class="upload_img_wrap profile_upolad mt-5">
                    <div class="form-group upload_img_item profile_add_btn">
                        <label for="file_upload" class="file_upload fs_12 fw_700 square border"><i class="xi-camera"></i></label>
                        <input type="file" class="form-control-file d-none" id="file_upload">
                    </div>       
                    <div class="form-group upload_img_item profile_view_img">
                        <label for="file_upload" class="file_upload square d-none"><i class="xi-plus"></i></label>
                        <input type="file" class="form-control-file d-none" id="file_upload">
                        <div class="rect_square">
                            <!-- 이미지 없을 때 -->
                            <img src="./img/no_profile.png" alt="프로필이미지">
                            <div class="dimmed"></div>
                        </div>
                    </div>
                </div>
                <div class="ip_wr mt_25 ip_valid">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">이름</h5>
                        <p class="text_num fs_12 fc_gray_600">(0/30)</p>
                    </div>
                    <input type="text" class="form-control" placeholder="이름을 입력해주세요">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5 class="">생년월일</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="생년월일 8자리를 입력해주세요.">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5>성별</h5>
                    </div>
                    <select class="form-control custom-select">
                        <option selected>성별</option>
                        <option value="1">남자</option>
                        <option value="2">여자</option>
                    </select>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='join_agree.php'">입력했어요!</button>
            </div>
        </form>
    </div>
</div>