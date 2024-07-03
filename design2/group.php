<?php
$title = "그룹";
$b_menu = "2";
$_GET['hd_num'] = '3';
include_once("./inc/head.php");
?>
<style>
    .top_btn_wr.b_on.active {
        bottom:14rem
    }
</style>
<div class="container sub_pg bg_main">
    <div class="mt_20">
        <div class="fixed_top bg_main">
            <div class="py_20 px_16">
                <div class="group_mem bg_main d-flex align-items-center justify-content-between">
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
                                <!-- 남은기간 설정시에만 보여집니다. -->
                                <!-- <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1"> | 남은기간 : 10일</p> -->
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="bar_fluid"></div>
        </div>
        <div class="mt_90 pt_20 pb_100">
            <!-- 그룹원없을경우 -->
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">그룹원없을경우<span class="ml-2"></span></p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body">
                </div>
            </div>
            <!-- 그룹원없을경우(초대중) -->
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">그룹원없을경우(초대중)<span class="ml-2"></span></p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body">
                    <div class="px_16 py_08 border-top">
                        <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_08">
                            3명 초대중
                        </p>
                    </div>
                </div>
            </div>
            <!-- 오너화면 -->
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 cursor_pointer" onclick="location.href='group_info.php'">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">오너화면<span class="ml-2">(3)</span></p>
                    <i class="fs_15 text_gray xi-angle-right-min"></i>
                </div>
                <div class="group-body">
                    <div class=" px_16 py_08 border-top">
                        <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_08">
                            3명 초대중
                        </p>
                        <div class="d-flex align-items-center justify-content-between py_08">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">최지우</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py_08">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">최지우</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <!-- <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"></p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p> -->
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py_08">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">최지우</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <!-- <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"></p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p> -->
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 리더/그룹원 화면 -->
            <div class="border bg-white rounded-lg mb-3">
                <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom">
                    <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">리더화면<span class="ml-2">(3)</span></p>
                    <button type="button" class="btn h-auto w-auto p-0 fs_12 text_gray">그룹나가기</button>
                </div>
                <div class="group-body">
                    <div class=" px_16 py_08 border-top">
                        <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_08">
                            3명 초대중
                        </p>
                        <div class="d-flex align-items-center justify-content-between py_08">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">최지우</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">오너</p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py_08">
                            <a href="#" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">최지우</p>
                                    <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py_08">
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
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn w-100 floating_btn rounded" onclick="location.href='group_create.php'"><i class="xi-plus-min mr-3"></i> 그룹 추가하기</button>
</div>


<!-- 그룹만들기 플러팅 : 그룹만들기-->
<!-- <div class="floating_wrap on">
    <div class="flt_inner">
        <div class="flt_head">
            <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹만들기</span></p>
        </div>
        <div class="flt_body pb-5 pt-3">
            <p class="text_dynamic line_h1_3 fs_17 fw_700">함께할 친구들을
                <span class="text-primary">초대할 그룹이</span> 필요해요.
            </p>
            <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">그룹원을 추가하면 실시간 위치 조회를 할 수 있어요.</p>
        </div>
        <div class="flt_footer">
            <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='group_create.php'">다음</button>
        </div>
    </div>
</div> -->

<!-- 그룹만들기 플러팅 : 그룹 활동기한 설정-->
<div class="floating_wrap on">
    <div class="flt_inner">
        <div class="flt_head">
            <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹 활동기한 설정</span></p>
        </div>
        <div class="flt_body pb-5 pt-3">
            <p class="text_dynamic line_h1_3 fs_17 fw_700">반가워요!
                그룹 활동 기한 설정이 필요하신가요?
            </p>
            <p class="text_dynamic line_h1_3 text_gray fs_14 fw_500 mt-3">그룹 활동 기한은 개인 정보 보호를 위해
                설정한 시간 동안만 위치를 공유하게 해줍니다.
            </p>
        </div>
        <div class="flt_footer flt_footer_b">
            <div class="d-flex align-items-center w-100 mx-0 my-0">
                <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 flt_close" data-dismiss="modal" aria-label="Close">아니요</button>
                <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0">네</button>
            </div>
        </div>
    </div>
</div>

<!-- 멤버 초대 링크보내기 -->
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