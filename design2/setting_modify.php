<?php
$title = "기본정보 수정";
$b_menu = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>

<div class="container sub_pg">
    <div>
        <form action="">
            <div class="mt-5">
                <div class="upload_img_wrap profile_upolad mt-5">
                    <div class="form-group upload_img_item profile_add_btn">
                        <label for="file_upload" class="file_upload fs_12 fw_700 square"><i class="xi-camera"></i></label>
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
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">닉네임</h5>
                        <p class="text_num fs_12 fc_gray_600">(0/12)</p>
                    </div>
                    <input type="text" class="form-control" placeholder="사용하실 닉네임을 입력해주세요.">
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div>
                            <div class="form_arm_text fs_13 fw_600 fc_gray_600 px-4 line_h1_2">한글/영문/숫자만 입력가능해요!</div>
                            <div class="form-text ip_valid mt-0"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                            <div class="form-text ip_invalid mt-0"><i class="xi-error-o"></i> 사용중인 닉네임입니다.</div>
                        </div>
                        <button type="button" class="btn rounded_12 h_fit_im py-3 px_12 db_chk_btn" disabled ><i class="xi-check-circle-o mr-2"></i>중복확인하기</button>
                    </div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='setting_list.php'">기본정보 수정하기</button>
            </div>
        </form>
    </div>
</div>

<!-- H-5 로그아웃 -->
<div class="modal fade" id="logout_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">로그아웃 하시겠습니까?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='.php'">로그아웃</button>
                </div>
            </div>
        </div>
        
    </div>
</div>
