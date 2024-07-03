<!--D-6 멤버 스케줄 미참석 팝업-->
<div class="modal fade" id="push_modal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body px_16 pt_44_im pb_26_im">
                <div class="prd_img mx-auto d-flex">
                    <div class="rect_square rounded_14 flex-shrink-0">
                        <img src="<?= CDN_HTTP ?>/img/sample01.png" alt="이미지" />
                    </div>
                </div>
                <p class="fs_16 fw_700 wh_pre mt-4 line_h1_3 text-center">다연님이 '피아노 학원'일정에
                    도착하지 않았습니다.</p>
            </div>
            <div class="modal-footer px_16 py-0 bg-white">
                <button type="button" class="btn btn-block btn-md btn-primary mx-0 my-0 rounded-sm open_contact_modal_btn">전화하기</button>
                <div class="w-100 d-flex align-items-center">
                    <button type="button" class="btn btn-lg w-50 fw_400 fs_14 text_gray mx-0 mr-0 mb-3">알림 해제하기</button>
                    <button type="button" class="btn btn-lg w-50 fw_400 fs_14 text-primary mx-0 mr-0 mb-3">알림 유지하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--D-7 연락처 목록-->
<div class="modal ad_ad fade second_modal " id="contact_modal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header flex-column px-0 py-0">
                <div class="w-100 pt_20 px_20 d-flex align-items-center justify-content-between ">
                    <div class="tit_h2">연락처</div>
                    <div></div>
                    <button type="button" class="btn h-auto p-0" data-dismiss="modal" aria-label="Close"><i class="xi-close fs_20"></i></button>
                </div>
                <div class="w-100 px_20 py_20">
                    <div class="w_fit">
                        <div class="d-flex align-items-center">
                            <div class="prd_img_50 mx-auto d-flex flex-shrink-0">
                                <div class="rect_square rounded_14 flex-shrink-0">
                                    <img src="<?= CDN_HTTP ?>/img/sample01.png" alt="이미지" />
                                </div>
                            </div>
                            <div class="pl-3">
                                <p class="fs_14 fw_500 text_dynamic mr-2 mb-2">최지우최지우최지우최지우최지우</p>
                                <a href="tel:010-2222-333" class="fs_13 text_gray text_dynamic line_h1_2"><u>010-2222-3333</u></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body px-0 py-0 ">
                <div class="">
                    <div id="accordion" class="accordion_1">
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <div class="card-header border-bottom bg_f8faff" id="headingOne">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20 ">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="<?= CDN_HTTP ?>/img/ico_tel.png" width="20px" alt="전화" /></span>학교</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <!-- 오픈할때 .collapse 클래스에 .show 추가-->
                            <div id="collapseOne" class="collapse " aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <div class="card-header border-bottom bg_f8faff" id="headingTwo">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20 ">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="<?= CDN_HTTP ?>/img/ico_tel.png" width="20px" alt="전화" /></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <div class="card-header border-bottom bg_f8faff" id="headingThree">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="<?= CDN_HTTP ?>/img/ico_tel.png" width="20px" alt="전화" /></span>학교</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <!-- 오픈할때 .collapse 클래스에 .show 추가-->
                            <div id="collapseThree" class="collapse " aria-labelledby="headingThree" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            <div class="card-header border-bottom bg_f8faff" id="headingFour">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="<?= CDN_HTTP ?>/img/ico_tel.png" width="20px" alt="전화" /></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            <div class="card-header border-bottom bg_f8faff" id="headingFive">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="<?= CDN_HTTP ?>/img/ico_tel.png" width="20px" alt="전화" /></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            <div class="card-header border-bottom bg_f8faff" id="headingSix">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20 ">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="<?= CDN_HTTP ?>/img/ico_tel.png" width="20px" alt="전화" /></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            <div class="card-header border-bottom bg_f8faff" id="headingSeven">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="<?= CDN_HTTP ?>/img/ico_tel.png" width="20px" alt="전화" /></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<iframe src="about:blank" name="hidden_ifrm" id="hidden_ifrm" style="display:none;"></iframe>
<div class="modal" id="splinner_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="top: 40%;">
        <div class=" modal-content">
            <div class="text-center">
                <div class="spinner-border m-5" role="status"></div>
            </div>
            <div class="text-center mb-5">
                <span class="text-primary">처리중입니다. 잠시만 기다려주세요.</span>
            </div>
        </div>
    </div>
</div>
</body>

</html>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>