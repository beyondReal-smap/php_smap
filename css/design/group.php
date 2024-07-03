<?php
$title = "그룹";
$b_menu = "2";
$_GET['hd_num'] = '5';
include_once("./inc/head.php");
?>
<style>
.top_btn_wr.b_on.active {
    bottom:14rem
}
</style>
<div class="container sub_pg bg_main">
    <div class="mt_20">
        <!-- 내용 없을 때 박스 -->
        <!-- <div class="border rounded-lg px_16 py_16 none_box">
            <div class="text-center">
                <p class="fs_14 text_gray text_dynamic">그룹을 생성새주세요!</p>
                <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="location.href='group_create.php'">그룹 생성 하러가기<i class="xi-angle-right-min ml_19"></i></button>
            </div>
        </div> -->
        <div class="fixed_top bg_main">
            <div class="py_20 px_16">
                <div class="group_mem bg_main d-flex align-items-center justify-content-between">
                    <div class="w_fit">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_12 mine">
                                <div class="rect_square rounded_14">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div>
                                <p class="fs_14 fw_500 text_dynamic mr-2">나</p>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">오너</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <p class="fs_14 fw_500 text_gray">3,621 <span>걸음</span></p>
                </div>
            </div>
            <div class="bar_fluid"></div>
        </div>
        <div class="mt_90 pt_20 pb_100">
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">아가들😃</p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body px_16 py_08">
                    <div class="d-flex align-items-center justify-content-between py_08">
                        <div class="w_fit">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">최지우</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <p class="fs_14 fw_500 text_light_gray flex-shrink-0">3,621 <span>걸음</span></p>
                    </div>
                    <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_08">
                        3명 초대중
                    </p>
                </div>
            </div>
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">새로운그룹추가됨</p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body px_16 py_08">
                    <div class="text-center py-3">
                        <p class="fs_14 text_gray text_dynamic">그룹원을 추가해주세요!</p>
                        <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" data-toggle="modal" data-target="#link_modal">그룹원 추가하기<i class="xi-angle-right-min ml_19"></i></button>
                    </div>
                </div>
            </div>
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">부모님</p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body px_16 py_08">
                    <div class="d-flex align-items-center justify-content-between py_08">
                        <div class="w_fit">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">아버지아버지아버지아버지아버지아버지아버지</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">오너</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <p class="fs_14 fw_500 text_light_gray flex-shrink-0">3,621 <span>걸음</span></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py_08">
                        <div class="w_fit">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">어머니 어머니 어머니 어머니 어머니 어머니</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <p class="fs_14 fw_500 text_light_gray flex-shrink-0">3,621 <span>걸음</span></p>
                    </div>
                </div>
            </div>
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">부모님</p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body px_16 py_08">
                    <div class="d-flex align-items-center justify-content-between py_08">
                        <div class="w_fit">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">아버지</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">오너</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <p class="fs_14 fw_500 text_light_gray flex-shrink-0">3,621 <span>걸음</span></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py_08">
                        <div class="w_fit">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">어머니</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <p class="fs_14 fw_500 text_light_gray flex-shrink-0">3,621 <span>걸음</span></p>
                    </div>
                </div>
            </div>
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">부모님</p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body px_16 py_08">
                    <div class="d-flex align-items-center justify-content-between py_08">
                        <div class="w_fit">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">아버지</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">오너</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <p class="fs_14 fw_500 text_light_gray flex-shrink-0">3,621 <span>걸음</span></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py_08">
                        <div class="w_fit">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">어머니</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <p class="fs_14 fw_500 text_light_gray flex-shrink-0">3,621 <span>걸음</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn w-100 floating_btn rounded" onclick="location.href='group_create.php'"><i class="xi-plus-min mr-3"></i> 그룹 추가하기</button>
</div>

<!-- E-13 멤버 초대 -->
<div class="modal btn_sheeet_wrap fade" id="link_modal" tabindex="-1">
    <div class="modal-dialog btm_sheet">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div class="d-inline-block w-100 text-right">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/modal_close.png" width="24px"></button>
                </div>
                <p class="fs_18 fw_700 text_dynamic line_h1_2">초대장은 어떻게 보낼까요?</p>
            </div>
            <div class="modal-body">
                <ul>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="./img/ico_kakao.png" alt="카카오톡 열기" width="40px" class="mr_12"/>
                                <p class="fs_15 fw_500 gray_900">카카오톡 열기</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="./img/ico_link.png" alt="초대 링크 복사" width="40px" class="mr_12"/>
                                <p class="fs_15 fw_500 gray_900">초대 링크 복사</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="./img/ico_address.png" alt="연락처 열기" width="40px" class="mr_12"/>
                                <p class="fs_15 fw_500 gray_900">연락처 열기</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<?php
include_once("./inc/b_menu.php");
include_once("./inc/tail.php");
?>