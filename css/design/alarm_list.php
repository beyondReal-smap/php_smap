<?php
$title = "알림";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>

<div class="container sub_pg">
    <div class="">
        <form action="">
            <div class="text-right mt-3 mb-4">
                <button type="button" class="btn h_fit_im fs_13 fc_gray_600 px-0"  data-toggle="modal" data-target="#arm_delete_modal"><i class="xi-trash-o mr_04"></i>전체삭제</button>
            </div>
            <div class="mb_24">
                <p class="fs_16 fw_600">2023.09.06(수) <span class="text-primary ml-2">오늘 알림</span></p>
                <div class="mt-4">
                    <div class="py_09">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_16">
                                <div class="rect_square border_opacity_50 rounded-pill">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_14 text_dynamic line_h1_2 mr_08"><span class="fw_700">다연</span> 님이 피아노학원 일정 시작 20분 전 입니다.</p>
                                <p class="fs_14 text_light_gray line_h1_2">오전 08:37</p>
                            </div>
                        </a>
                    </div>
                    <div class="py_09">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_16">
                                <div class="rect_square border_opacity_50 rounded-pill">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_14 text_dynamic line_h1_2 mr_08"><span class="fw_700">다연</span> 님이 학교에 도착하였습니다.</p>
                                <p class="fs_14 text_light_gray line_h1_2">오전 08:37</p>
                            </div>
                        </a>
                    </div>
                    <div class="py_09">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_16">
                                <div class="rect_square border_opacity_50 rounded-pill">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_14 text_dynamic line_h1_2 mr_08"><span class="fw_700">다연</span> 님이 집에서 출발하였습니다.</p>
                                <p class="fs_14 text_light_gray line_h1_2">오전 08:37</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mb_24">
                <p class="fs_16 fw_600">2023.09.05(화)</p>
                <div class="mt-4">
                    <div class="py_09">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_16">
                                <div class="rect_square border_opacity_50 rounded-pill">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_14 text_dynamic line_h1_2 mr_08"><span class="fw_700">다연</span> 님이 피아노학원 일정 시작 20분 전 입니다.</p>
                                <p class="fs_14 text_light_gray line_h1_2">오전 08:37</p>
                            </div>
                        </a>
                    </div>
                    <div class="py_09">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_16">
                                <div class="rect_square border_opacity_50 rounded-pill">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_14 text_dynamic line_h1_2 mr_08"><span class="fw_700">다연</span> 님이 학교에 도착하였습니다.</p>
                                <p class="fs_14 text_light_gray line_h1_2">오전 08:37</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- D-5 알림 목록 -->
<div class="modal fade" id="arm_delete_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">전체 삭제하시겠습니까?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" id="ToastBtn" data-dismiss="modal" aria-label="Close" >네</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close">아니요</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>삭제되었습니다.</p> <!-- 성공메시지 -->
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>