<?php
$title = "회원탈퇴";
$b_menu = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>

<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">본인 확인을 위해
            현재 비밀번호를 입력해 주세요.
        </p>
        <form action="">
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5>비밀번호</h5>
                    </div>
                    <input type="password" class="form-control" placeholder="비밀번호를 입력해주세요.">
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2">비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.</div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 비밀번호를 다시 확인해주세요</div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" data-toggle="modal" data-target="#withdraw_modal">회원탈퇴하기</button>
            </div>
        </form>
    </div>
</div>

<!-- H-7 회원탈퇴 안내 -->
<div class="modal fade" id="withdraw_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-5 pt-5 pb-4">
				<p class="text_dynamic fs_16 fw_800 line_h1_3">우리 서비스를 떠나시려는 이유를 
                알려주실 수 있나요? 
                귀하의 소중한 의견을 통해 
                더 나은 서비스를 제공하려 노력합니다.
                </p>
            </div>
            <div class="modal-body px-5 pt-0 pb-5">
				<form class="">
                    <div class="ip_wr mb-4">
                        <div class="checks_wr flex-column">
                            <div class="checks pb-2">
                                <label class="chk_left">
                                    <input type="checkbox" name="chk2">
                                    <span class="ic_box"><i class="xi-check-min"></i></span>
                                    <div class="chk_p text_gray">
                                        <p class="text_dynamic">서비스가 복잡해요.</p>
                                    </div>
                                </label>
                            </div>
                            <div class="checks pb-2">
                                <label class="chk_left">
                                    <input type="checkbox" name="chk2">
                                    <span class="ic_box"><i class="xi-check-min"></i></span>
                                    <div class="chk_p text_gray">
                                        <p class="text_dynamic">필요한 기능이 없어요.</p>
                                    </div>
                                </label>
                            </div>
                            <div class="checks pb-2">
                                <label class="chk_left">
                                    <input type="checkbox" name="chk2">
                                    <span class="ic_box"><i class="xi-check-min"></i></span>
                                    <div class="chk_p text_gray">
                                        <p class="text_dynamic">다른 서비스를 이용할래요.</p>
                                    </div>
                                </label>
                            </div>
                            <div class="checks pb-2">
                                <label class="chk_left">
                                    <input type="checkbox" name="chk2">
                                    <span class="ic_box"><i class="xi-check-min"></i></span>
                                    <div class="chk_p text_gray">
                                        <p class="text_dynamic">기타 이유.</p>
                                    </div>
                                </label>
                            </div>
                            <!-- 탈퇴이유 -->
                            <div class="ip_wr pb-5">
                                <div class="ip_tit d-flex align-items-center justify-content-between">
                                    <h5 class="fs_15 fw_500 text-text">탈퇴하는 이유를 알려주세요.</h5>
                                </div>
                                <textarea class="form-control" placeholder="입력해주세요" rows="3"></textarea>
                                <p class="fc_gray_600 fs_12 text-right mt-2">(0/1000)</p>
                                <div class="invalid-feedback">1000자까지만 써주세요</div>
                            </div>  
                        </div>
                    </div>
				</form>
                <div class="bg_gray px_16 pt_16 pb-2 rounded_12">
                    <div class="d-flex align-items-center">
                        <img src="./img/ico_warring_chk.png" width="14px" alt="확인해주세요" class="mr_08"/>
                        <p class="fs_16 fw_800 line_h1_2">아래 사항을 확인해주세요.</p>
                    </div>
                    <ul class="py_07">
                        <li class="position-relative slash5 text_dynamic pl_22 fs_14 py_06 line_h1_2">모든 데이터와 기록이 삭제돼요.</li>
                        <li class="position-relative slash5 text_dynamic pl_22 fs_14 py_06 line_h1_2">한 번 탈퇴하시면 복구가 불가능해요.</li>
                        <li class="position-relative slash5 text_dynamic pl_22 fs_14 py_06 line_h1_2">재가입 시 이전 정보와 데이터는 복원되지 않아요.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer w-100 p-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" >탈퇴하기</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close">나중에</button>
                </div>
            </div>
        </div>
    </div>
</div>