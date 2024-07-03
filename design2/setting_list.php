<?php
$title = "계정설정";
$b_menu = "5";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>

<div class="container sub_pg px-0 bg_main">
    <div>
        <form action="">
            <div class="pt-5 pb_20 px_16 bg-white">
                <div class="upload_img_wrap profile_upolad">
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
                <div class="mb-3 mr-2">
                    <div class="d-flex align-items-center justify-content-center flex-wrap " >
                        <a class="fs_15 fw_600 line_h1_3 text-center text_dynamic mt-2 mr-2">루틴홍길동루틴홍길동루틴홍길동</a>
                        <p class="fs_13 fc_mian_sec fw_600 line_h1_3 text-center text_dynamic mt-2">루틴홍길동</p>
                    </div>
                    <p class="fs_14 text_light_gray fw_500 text-center text_dynamic mt-2">test@test.com</p>
                </div>
            </div>
        </form>
        <div class=" py_16">
            <div class="px_16">
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="setting_modify.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">기본 정보 수정</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="current_password.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">비밀번호 변경</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a class="d-flex align-items-center justify-content-between cursor_pointer px_16 py_16" data-toggle="modal" data-target="#logout_modal">
                        <p class="fs_16 fw_600">로그아웃</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="withdraw.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 text_gray fw_600">회원탈퇴</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
            </div>
        </div>
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

<?php 
    include_once("./inc/b_menu.php");
    include_once("./inc/tail.php");
?>

