<?php
$title = "플랜";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 text_dynamic line_h1_3 text_dynamic">SMAP 플랜 안내</p>
        <p class="fs_14 fw_500 text_gray mt-2 text_dynamic">SMAP의 서비스를 더욱 풍성하게 만나보세요 :)</p>
        <form action="" class="">
            <div class="plan_info_wrap">
                <div class="planinfo_in">
                    <p class="plan_type"><span class="plan_free">무료 플랜</span></p>
                    <ul class="planinfo_ul">
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">최적경로 사용 횟수 (하루/월)</p>
                            <p class="text-right line_h1_3">2<span>/</span>60</p>
                        </li>
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">내 장소 저장</p>
                            <p class="text-right line_h1_3">2개</p>
                        </li>
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">로그 조회 기간</p>
                            <p class="text-right line_h1_3">2일</p>
                        </li>
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">광고</p>
                            <p class="text-right line_h1_3">노출</p>
                        </li>
                    </ul>
                </div>
                <div class="planinfo_in">
                    <p class="plan_type"><span class="plan_pay">유료 플랜</span></p>
                    <ul class="planinfo_ul">
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">최적경로 사용 횟수 (하루/월)</p>
                            <p class="text-right line_h1_3">10<span>/</span>300</p>
                        </li>
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">내 장소 저장</p>
                            <p class="text-right line_h1_3">무제한</p>
                        </li>
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">로그 조회 기간</p>
                            <p class="text-right line_h1_3">2주</p>
                        </li>
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">광고</p>
                            <p class="text-right line_h1_3">없음</p>
                        </li>
                    </ul>
                    <div class="plan_ft">
                        <button type="button" class="btn plan_pay_btn">유료 플랜 신청하기</button>
                    </div>
                </div>
                <div class="planinfo_in">
                    <p class="plan_type"><span class="plan_comp">기업형 플랜</span></p>
                    <ul class="planinfo_ul">
                        <li class="text-center">
                            <p class="text_dynamic fs_13 fw_500 line_h1_8 text_gray">현재는 기업형 플랜이 제공되지 않습니다.
                                그룹원을 10명 이상 등록하려면
                                고객 센터로 문의해주시기 바랍니다.</p>
                        </li>
                    </ul>
                    <div class="plan_ft">
                        <button type="button" class="btn plan_comp_btn" onclick="href='inquiry.php'">1:1문의 바로가기</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- 존재하는 메일주소 -->
<div class="modal fade" id="dpl_email" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">이미 사용중인 메일주소에요.
                    다른 메일주소를 입력해 주세요!</p>
            </div>
            <div class="modal-footer px-0 py-0">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close">확인하기</button>
            </div>
        </div>
    </div>
</div>