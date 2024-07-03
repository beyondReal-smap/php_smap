<?php
$title = "설정";
$b_menu = "5";
$_GET['hd_num'] = '4';
include_once("./inc/head.php");
?>

<div class="container sub_pg px-0 bg_main">
    <div>
        <form action="">
            <div class="pt_24 pb-5 d-flex align-items-end justify-content-between border-bottom px_16 bg-white prd_setting">
                <div class="mb-3  mr-3">
                    <a href="setting_list.php" class="fs_20 fw_700 text_dynamic line_h1_3">루틴홍길동<i class="xi-angle-right-thin pl-2 fs_12 fw_600"></i></a>
                    <p class="fs_12 text_gray fw_500 mt-3">이메일 가입 회원</p>
                    <p class="fs_13 fc_mian_sec fw_500 mt-1">test@test.com</p>
                </div>

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
            </div>
        </form>
        <div class=" py_16">
            <div class="px_16">
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="setting_list.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">매뉴얼</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="setting_alarm.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">알림설정</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="https://schedulemap.notion.site/schedulemap/smap-4afc9e6b71a6434cbcbf9dc4ec6f9d9d" target="_blank" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">약관 및 동의 관리</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="inquiry.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">1:1 문의 내역</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="faq.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">FAQ</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="notice.php" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">공지사항</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>




<?php
    include_once("./inc/b_menu.php");
?>