<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = $translations['txt_plan'];
$h_menu = '2';

$_SUB_HEAD_TITLE = $translations['txt_plan'];
$sub_title = "plan";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_attempt_other_device'], './logout');
    }
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('ot_pay_type', '1');
    $first_chk = $DB->getone('order_t');
    $first_check = $first_chk['ot_idx'] ? false : true;
}
// 지역에 따른 가격과 화폐 단위 가져오기
$priceInfo = getPriceByRegion();

?>

<div class="container sub_pg">
    <div class="mt-5">
        <p class=""><span class="plan_pay"><?= $translations['txt_plus'] ?></span></p>
        <p class="tit_h1 text_dynamic line_h1_3 text_dynamic mt-4"><?= $translations['txt_all_smap_features'] ?>
            <?= $translations['txt_experience_without_limits'] ?></p>
        <ul class="mt_20 mb_50">
            <li class="position-relative slash7 my-3 fs_16"><?= $translations['txt_save_locations_freely'] ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= $translations['txt_view_2_weeks_logs'] ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= $translations['txt_ad_free_experience'] ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= $translations['txt_10_optimal_routes_day'] ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= $translations['txt_manage_10_members'] ?></li>
        </ul>
        <form action="" class="">
            <div class="plan_info_wrap">
                <div class="checks ava_checks">
                    <label class="" onclick="change_price_text('month')">
                        <input type="radio" name="rd1">
                        <div class="w-100 chk_p rounded  ava_class ava_class_b mt-0">
                            <p class="fs_15 fw_700 text_dynamic"><?= $translations['txt_monthly_plan'] ?></p>
                            <div class="d-flex align-items-center">
                                <p class="fs_16 fw_700 text-nowrap mr-3"><?= $priceInfo['currency'] ?><?= $priceInfo['monthly'] ?><?= $translations['txt_per_month'] ?></p>
                                <div class="ava_class_btn ava_class_btn_b select-btn" data-type="month" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','month','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')"><?= $translations['txt_select_plan'] ?></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            <div class="plan_info_wrap">
                <div class="checks ava_checks">
                    <label class="" onclick="change_price_text('year')">
                        <input type="radio" name="rd1" checked>
                        <div class="w-100 chk_p rounded  ava_class ava_class_b mt-0">
                            <div class="d-block">
                                <p class="fs_15 fw_700 text_dynamic"><?= $translations['txt_yearly_plan'] ?></p>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs_12 fw_300 text_dynamic mt_08 mr-2"><del><?= $priceInfo['currency'] ?><?= $priceInfo['yearly'] ?></del></p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <p class="fs_16 fw_700 mr-3 text-nowrap"><?= $priceInfo['currency'] ?><?= $priceInfo['yearly'] ?><?= $translations['txt_per_year'] ?></p>
                                <div class="ava_class_btn ava_class_btn_b select-btn" data-type="year" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','year','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')"><?= $translations['txt_select_plan'] ?></div>
                            </div>
                        </div>
                        <p class="plan_type"><span class="plan_comp"><?= $translations['txt_popular'] ?></span></p>
                    </label>
                </div>
            </div>
            <div class="plan_info_wrap d-none">
                <div class="checks ava_checks ord_ava_checks">
                    <label class="">
                        <input type="radio" name="rd2">
                        <div class="w-100 chk_p rounded  ava_class ava_class_b justify-content-center mt-0">
                            <div class="d-block">
                                <p class="fs_16 fw_700 text_dynamic">￦0에 <?= $translations['txt_try_it'] ?></p>
                            </div>
                        </div>
                    </label>
                </div>
                <p class="fs_15 fw_600 text-center text_dynamic line_h1_3 mt-3"><?= $translations['txt_free_trial_info'] ?> <?= $priceInfo['yearly'] ?></p>
            </div>
            <p class="fs_15 fw_600 text-center text_dynamic line_h1_3 mt-3"><?= $translations['txt_free_trial_info'] ?> <span id="change_price1"><?= $translations['txt_per_year'] ?> <?= $priceInfo['yearly'] ?></span>
                <span id="store"><?= $translations['txt_app_store'] ?></span><?= $translations['txt_cancel_anytime'] ?>
            </p>
            <p class="fs_13 fc_gray_600 line_h1_3 mt-4"><?= $translations['txt_after_free_trial'] ?> <span id="change_price2"><?= $priceInfo['yearly'] ?><?= $translations['txt_per_year'] ?></span> <?= $translations['txt_fee'] ?> <span id="id"><?= $translations['txt_apple_id'] ?></span><?= $translations['txt_charged_to_account'] ?>
                <?= $translations['txt_renewed_and_charged'] ?> <?= $translations['txt_after_purchase'] ?><span id="store2"><?= $translations['txt_app_store'] ?></span> <?= $translations['txt_manage_cancel_subscription'] ?></p>

            <button type="button" id="restore_btn" class="btn btn-sm d-none" onclick="f_restore_btn('<?= $_SESSION['_mt_idx'] ?>','restorePurchase')"><?= $translations['txt_restore'] ?></button>
        </form>
    </div>
</div>

<!-- 존재하는 메일주소 -->
<div class="modal fade" id="dpl_email" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= $translations['txt_no_registered_info_email'] ?></p>
            </div>
            <div class="modal-footer px-0 py-0">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close"><?= $translations['txt_confirmed'] ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        change_price_text('year');
    });

    function change_price_text(planType) {
        // 가격 정보 엘리먼트를 가져옴
        const changePrice1 = document.getElementById('change_price1');
        const changePrice2 = document.getElementById('change_price2');

        // 플랜 종류에 따른 가격 업데이트
        if (planType === 'month') {
            changePrice1.innerHTML = '<?= $translations['txt_monthly'] ?> <?= $priceInfo['currency'] ?><?= $priceInfo['monthly'] ?>';
            changePrice2.innerHTML = '<?= $priceInfo['currency'] ?><?= $priceInfo['monthly'] ?><?= $translations['txt_per_month'] ?>';
        } else if (planType === 'year') {
            changePrice1.innerHTML = '<?= $translations['txt_yearly'] ?> <?= $priceInfo['currency'] ?><?= $priceInfo['yearly'] ?>';
            changePrice2.innerHTML = '<?= $priceInfo['currency'] ?><?= $priceInfo['yearly'] ?><?= $translations['txt_per_year'] ?>';
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
            jalert('<?= $translations['txt_already_paid_member'] ?>');
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