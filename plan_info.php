<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "구독관리";
$h_menu = '2';
$_SUB_HEAD_TITLE = "구독관리";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_attempt_other_device'], './logout');
    }
    // 첫 결제를 했는지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('ot_pay_type', '1');
    $first_chk = $DB->getone('order_t');
    if ($first_chk['ot_idx']) {
        $first_check = false;
    } else {
        $first_check = true;
    }
}
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 text_dynamic line_h1_3 text_dynamic">SMAP 플랜 안내</p>
        <p class="fs_14 fw_500 text_gray mt-2 text_dynamic">SMAP의 서비스를 더욱 풍성하게 만나보세요 :)</p>
        <form action="" class="">
            <div class="plan_info_wrap  mt-5">
                <div class="planinfo_in">
                    <p class="plan_type"><span class="plan_free">무료 플랜</span></p>
                    <ul class="planinfo_ul">
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">최적경로 사용 횟수 (하루/월)</p>
                            <p class="text-right line_h1_3">2<span>/</span>60</p>
                        </li>
                        <li class="plantext_wp">
                            <p class="text_dynamic line_h1_3">일정 장소 저장</p>
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
                            <p class="text_dynamic line_h1_3">일정 장소 저장</p>
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
                        <button type="button" class="btn plan_pay_btn" onclick="location.href='./plan_information'">유료 플랜 신청하기</button>
                    </div>
                    <? if ($_SERVER['REMOTE_ADDR'] == '115.93.39.5') { ?>
                        <!-- <button type="button" class="btn btn-sm" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','month','<?= $first_check ?>')">유료 플랜 월간 신청하기</button>
                        <button type="button" class="btn btn-sm" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','year','<?= $first_check ?>')">유료 플랜 연간 신청하기</button>
                        <button type="button" class="btn btn-sm" onclick="f_restore_btn('<?= $_SESSION['_mt_idx'] ?>','restorePurchase')">복원하기</button> 
                        <button type="button" class="btn btn-sm" onclick="f_purchase_check_btn('<?= $_SESSION['_mt_idx'] ?>','purchaseCheck')">구독상태 확인</button>-->
                    <? } ?>
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
                        <button type="button" class="btn plan_comp_btn" onclick="location.href='./inquiry'">1:1문의 바로가기</button>
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
<script>
    function f_subscribe_btn(mt_idx, type, first_chk) {
        var message = {
            "type": "purchase",
            "param": type,
        };
        if (isAndroid()) {
            window.smapAndroid.purchase(type);
        } else if (isiOS()) {
            window.webkit.messageHandlers.smapIos.postMessage(message);
        }
    }

    function f_restore_btn(mt_idx, type) {
        var message = {
            "type": "restorePurchase",
        };
        if (isAndroid()) {
            window.smapAndroid.restorePurchase();
        } else if (isiOS()) {
            window.webkit.messageHandlers.smapIos.postMessage(message);
        }
    }


    function f_purchase_check_btn(mt_idx, type) {
        var message = {
            "type": "purchaseCheck",
        };
        if (isAndroid()) {
            window.smapAndroid.purchaseCheck();
        } else if (isiOS()) {
            window.webkit.messageHandlers.smapIos.postMessage(message);
        }
    }

    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    }
</script>