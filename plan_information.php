<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "플랜";
$h_menu = '2';
$_SUB_HEAD_TITLE = "플랜";
$sub_title = "plan";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
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
    <div class="mt-5">
        <p class=""><span class="plan_pay">Plus</span></p>
        <p class="tit_h1 text_dynamic line_h1_3 text_dynamic mt-4">SMAP의 모든 기능을
            제한없이 경험해 보세요.
        </p>
        <ul class="mt_20 mb_50">
            <li class="position-relative slash7 my-3 fs_16">내 장소를 마음껏 저장해보세요.</li>
            <li class="position-relative slash7 my-3 fs_16">2주 동안의 로그도 조회할 수있어요.</li>
            <li class="position-relative slash7 my-3 fs_16">광고 걱정 없이 쾌적하게 이용해보세요.</li>
            <li class="position-relative slash7 my-3 fs_16">하루에 최적경로 10회 조회도 가능해요!</li>
            <li class="position-relative slash7 my-3 fs_16">그룹원을 10명까지 관리 가능해요!</li>
        </ul>
        <form action="" class="">
            <div class="plan_info_wrap">
                <div class="checks ava_checks">
                    <label class="" onclick="change_price_text('month')">
                        <input type="radio" name="rd1">
                        <div class="w-100 chk_p rounded  ava_class ava_class_b mt-0">
                            <p class="fs_15 fw_700 text_dynamic">월간</p>
                            <div class="d-flex align-items-center">
                                <p class="fs_16 fw_700 text-nowrap mr-3">￦4,900/월</p>
                                <!-- <div class="ava_class_btn ava_class_btn_b" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','month','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')">선택</div> -->
                                <div class="ava_class_btn ava_class_btn_b select-btn" data-type="month" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','month','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')">선택</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            <div class="plan_info_wrap">
                <div class="checks ava_checks">
                    <label class="" onclick="change_price_text('year')">
                        <input type="radio" name="rd1">
                        <div class="w-100 chk_p rounded  ava_class ava_class_b mt-0">
                            <div class="d-block">
                                <p class="fs_15 fw_700 text_dynamic">연간</p>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs_12 fw_300 text_dynamic mt_08 mr-2"><del>￦58,800원</del></p>
                                    <!-- <p class="fs_12 fw_300 text_dynamic mt_08 fw_500">￦42,000원</p> -->
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <!-- <p class="fs_16 fw_700 mr-3 text-nowrap">3,500/월</p> -->
                                <p class="fs_16 fw_700 mr-3 text-nowrap">￦42,000/년</p>
                                <!-- <div class="ava_class_btn ava_class_btn_b" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','year','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')">선택</div> -->
                                <div class="ava_class_btn ava_class_btn_b select-btn" data-type="year" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','year','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')">선택</div>
                            </div>
                        </div>
                        <p class="plan_type"><span class="plan_comp">인기!!</span></p>
                    </label>
                </div>
            </div>
            <div class="plan_info_wrap d-none">
                <div class="checks ava_checks ord_ava_checks">
                    <label class="">
                        <input type="radio" name="rd2">
                        <div class="w-100 chk_p rounded  ava_class ava_class_b justify-content-center mt-0">
                            <div class="d-block">
                                <p class="fs_16 fw_700 text_dynamic">￦0에 사용해 보세요</p>
                            </div>
                        </div>
                    </label>
                </div>
                <p class="fs_15 fw_600 text-center text_dynamic line_h1_3 mt-3">1주 무료체험, 그 이후부터 매년 ￦42,000
                    App Store에서 언제든지 취소하세요.
                </p>
            </div>
            <p class="fs_15 fw_600 text-center text_dynamic line_h1_3 mt-3">1주 무료체험, 그 이후부터 <span id="change_price1">매년 ￦42,000</span>
                <span id="store">App Store</span>에서 언제든지 취소하세요.
            </p>
            <p class="fs_13 fc_gray_600 line_h1_3 mt-4">무료 체험판 사용 기간이 끝나면 <span id="change_price2">￦42,000/년</span> 요금이 <span id="id">Apple ID</span>계정에 청구됩니다. 현재 기간이 종료되기까지 최소 24시간 전에 구독이 취소되지 않으면 구독이 갱신됩니다. 귀하의 계정은 현 기간이 종료되기 전, 24시간 이내에 갱신되어 요금이 부과됩니다. 구매 후 <span id="store2">앱 스토어</span> 계정 설정으로 이동하여 구독을 관리하고 취소할 수 있습니다.</p>

            <button type="button" id="restore_btn" class="btn btn-sm d-none" onclick="f_restore_btn('<?= $_SESSION['_mt_idx'] ?>','restorePurchase')">복원하기</button>
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
    function sendGAEvent(event_name, event_category, event_label, value) {
        gtag('event', event_name, {
            'event_category': event_category,
            'event_label': event_label,
            'value': {
                'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                'platform': isAndroid() ? 'Android' : (isiOS() ? 'iOS' : 'Unknown')
            }
        });
    }

    $(document).ready(function() {
        $('.select-btn').on('click', function() {
            var type = $(this).data('type');
            if (type === 'month') {
                sendGAEvent('select_plan', 'plan', 'month', 1);
            } else if (type === 'year') {
                sendGAEvent('select_plan', 'plan', 'year', 1);
            }
        });
    });
    
    $(document).ready(function() {
        if (isAndroid()) {
            $('#restore_btn').addClass('d-none');
            $('#store').text('Google Store');
            $('#store2').text('Google Store');
            $('#id').text('Google ID');
        } else if (isiOS()) {
            $('#restore_btn').removeClass('d-none');
            $('#store').text('App Store');
            $('#store2').text('App Store');
            $('#id').text('Apple ID');
        }
    });

    function change_price_text(type) {
        if (type == 'month') {
            $('#change_price1').text('매월 ￦4,900');
            $('#change_price2').text('￦4,900/월');

        } else if (type == 'year') {
            $('#change_price1').text('매년 ￦42,000');
            $('#change_price2').text('￦42,000/년');
        }
    }

    function f_subscribe_btn(mt_idx, type, first_chk, mt_level) {
        if (mt_level == '2') {
            var message = {
                "type": "purchase",
                "param": type,
            };
            if (isAndroid()) {
                window.smapAndroid.purchase(type);
            } else if (isiOS()) {
                window.webkit.messageHandlers.smapIos.postMessage(message);
            }
        } else {
            jalert('이미 유료회원이세요!');
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
        return navigator.userAgent.match(/iPhone|iPad|iPod|Mac|Apple/i);
    }
</script>
<?
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>