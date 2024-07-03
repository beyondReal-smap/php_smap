<?php
$title = "그룹편집";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>
<?php include_once("./inc/modal.php");?>
<style>
.top_btn_wr.b_on.active {
    bottom:14rem
}
</style>
<div class="container sub_pg">
    <div class="mt_20">
        <div class="fixed_top">
            <div class="bg-secondary px_16 py-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <p class="fs_16 fw_800 mr-2">오너<span class="ml-2">(1)</span></p>
                    <!-- 오너일때 필요 -->
                    <button type="button" class="btn h_fit_im px-0 py-0" data-toggle="modal" data-target="#name_edit_modal"><img src="./img/ico_edit.png" width="19px" alt="그룹편집" onclick="locarion.href='./'"/></button>
                </div>
                <!--맴버 / 그룹리더 -->
                <button type="button" class="btn fs_14 fw_500 text_gray h_fit_im px-0 py-0 mx-0 my-0 text-right" data-toggle="modal" data-target="#group_out_modal">그룹삭제</button>
                <!--그룹오너 -->
                <!-- <button type="button" class="btn fs_14 fw_500 text_gray h_fit_im px-0 text-right" data-toggle="modal" data-target="#group_delete_modal">그룹삭제</button>  -->
            </div>
            <div class="py_20 bg-white px_16">
                <div class="group_mem d-flex align-items-center">
                    <div class="w_fit">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_12 mine">
                                <div class="rect_square rounded_14">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div>
                                <p class="fs_14 fw_500 text_dynamic mr-2">나</p>
                                <div class="d-flex align-items-center flex-wrap ">
                                        <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">오너</p>
                                        <!-- <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p> -->
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
        <div class="bargray_fluid"></div>
        </div>
        <div class="mt_145">
            <!-- 그룹원 없을 때 -->
            <!-- <div class="">
                <div class="pt-5 text-center">
                    <img src="./img/warring.png" width="82px" alt="그룹원추가"/>
                    <p class="mt_20 fc_gray_900 text-center">그룹원을 추가해주세요!</p>
                </div>
            </div> -->
            <div class="pt-2">
                <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_08">
                    3명 초대중
                </p>
            </div>
            <div>
                <div class="py_16 d-flex align-items-center justify-content-between border-bottom">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">리더이름</p>
                            <div class="d-flex align-items-center flex-wrap ">
                                <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p>
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                            </div>
                        </div>
                    </a>
                    <button type="button" class="btn h-auto w-auto p-3 fc_gray" data-toggle="modal" data-target="#more_madal"><i class="xi-ellipsis-v"></i></button>
                </div>
                <div class="py_16 d-flex align-items-center justify-content-between border-bottom">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">리더이름</p>
                            <div class="d-flex align-items-center flex-wrap ">
                                <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p>
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                            </div>
                        </div>
                    </a>
                    <button type="button" class="btn h-auto w-auto p-3 fc_gray" data-toggle="modal" data-target="#more_madal"><i class="xi-ellipsis-v"></i></button>
                </div>
                <div class="py_16 d-flex align-items-center justify-content-between border-bottom">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">그룹원이름</p>
                            <div class="d-flex align-items-center flex-wrap ">
                                <!-- <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p> 
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p> -->
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                            </div>
                        </div>
                    </a>
                    <button type="button" class="btn h-auto w-auto p-3 fc_gray" data-toggle="modal" data-target="#more_madal"><i class="xi-ellipsis-v"></i></button>
                </div>
                <div class="py_16 d-flex align-items-center justify-content-between border-bottom">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">그룹원이름</p>
                            <div class="d-flex align-items-center flex-wrap ">
                                <!-- <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p> 
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p> -->
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                            </div>
                        </div>
                    </a>
                    <button type="button" class="btn h-auto w-auto p-3 fc_gray" data-toggle="modal" data-target="#more_madal"><i class="xi-ellipsis-v"></i></button>
                </div>
                <div class="py_16 d-flex align-items-center justify-content-between border-bottom">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">그룹원이름</p>
                            <div class="d-flex align-items-center flex-wrap ">
                                <!-- <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p> 
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p> -->
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                            </div>
                        </div>
                    </a>
                    <button type="button" class="btn h-auto w-auto p-3 fc_gray" data-toggle="modal" data-target="#more_madal"><i class="xi-ellipsis-v"></i></button>
                </div>
                <div class="py_16 d-flex align-items-center justify-content-between border-bottom">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">그룹원이름</p>
                            <div class="d-flex align-items-center flex-wrap ">
                                <!-- <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">리더</p> 
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p> -->
                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : 10일</p>
                            </div>
                        </div>
                    </a>
                    <button type="button" class="btn h-auto w-auto p-3 fc_gray" data-toggle="modal" data-target="#more_madal"><i class="xi-ellipsis-v"></i></button>
                </div>
            </div>
        </div>
        <div class="b_botton">
            <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " data-toggle="modal" data-target="#link_modal"><i class="xi-plus-min mr-3"></i>그룹원 추가하기</button>
        </div>
    </div>
</div>


<div class="modal fade bottom_modal" id="more_madal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
            </div>
            <div class="modal-body">
                <div class="rounded-lg overflow-hidden ">
                    <button type="button" class="btn btn-block mt-0 rounded-0 bottom_mdl_btn">리더 추가하기</button>
                    <button type="button" class="btn btn-block mt-0 rounded-0 bottom_mdl_btn">리더 해제하기</button>
                    <button type="button" class="btn btn-block mt-0 rounded-0 bottom_mdl_btn">그룹원 내보내기</button>
                </div>
            </div>
            <div class="modal-footer mt-3 mb-3">
            <button type="button" class="btn text-black btn-block fs_15 fw_600 bg-white mx-0 my-0" data-dismiss="modal">취소</button>
            </div>
        </div>
    </div>
</div>

<!-- E-4 그룹 나가기 -->
<div class="modal fade" id="group_out_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">그룹에서 나가시겠어요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" >나가기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- E-8 리더 추가 -->
<div class="modal fade" id="leader_add_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">다연님을 [아가들] 그룹의 리더로
                    추가하시겠어요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" >추가하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- E-8 리더 삭제 -->
<div class="modal fade" id="leader_delete_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">다연님을 [아가들] 그룹의 리더에서
                    제외하시겠어요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" >제외하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- E-8 그룹원 삭제 -->
<div class="modal fade" id="mem_out_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">다연님을 [아가들] 그룹에서
                    제외하시겠어요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" >제외하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- E-11 그룹명 수정  -->
<div class="modal fade" id="name_edit_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
				<p class="modal-title line1_text fs_20 fw_700">그룹명 수정</p>
                <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/modal_close.png"></button></div>
            </div>
            <div class="modal-body scroll_bar_y">
				<form class="">
                    <div class="ip_wr">
                        <div class="ip_tit d-flex align-items-center justify-content-between">
                            <h5 class="">변경전</h5>
                        </div>
                        <input type="text" class="form-control" placeholder="" value="아가들">
                        <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                        <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                    </div>
                    <div class="ip_wr mt_25">
                        <div class="ip_tit d-flex align-items-center justify-content-between">
                            <h5 class="">변경후</h5>
                            <p class="text_num fs_12 fc_gray_600">(0/15)</p>
                        </div>
                        <input type="text" class="form-control" placeholder="변경할 이름">
                        <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                        <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                    </div>
				</form>
            </div>
            <div class="modal-footer border-0 p-0">
                <button type="button" class="btn btn-lg btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close">그룹명 수정하기</button>
            </div>
        </div>
    </div>
</div>

<!-- E-12 그룹 삭제 -->
<div class="modal fade" id="group_delete_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">[아가들] 그룹을 삭제할까요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">나중에</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" >삭제하기</button>
                </div>
            </div>
        </div>
    </div>
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

