<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '6';
$h_menu = '9';

$_SUB_HEAD_TITLE = $translations['txt_settings'];
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";


if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_attempt_other_device'], './logout');
    }
}

if ($_SESSION['_mt_nickname'] == '') {
    alert($translations['txt_additional_info_required'], './setting_modify', '');
}

$mem_row = get_member_t_info($_SESSION['_mt_idx']);
if ($mem_row['mt_level'] == '2') {
    $plan_use = $translations['txt_using_free_plan'];
} elseif ($mem_row['mt_level'] == '5') {
    $plan_use = $translations['txt_using_paid_plan'];
}

$st_info = get_setup_t_info();
?>
<script>
    // ... (기존 스크립트 코드) ...
</script>
<div class="container sub_pg px-0 bg_main">
    <div>
        <div class="pt_24 pb-5 d-flex align-items-center justify-content-between border-bottom px_20 bg-white prd_setting">
            <div>
                <?php
                //프로필 사진 등록/수정
                include $_SERVER['DOCUMENT_ROOT'] . "/profile.inc.php";
                ?>
            </div>

            <div class="mx-3">
                <style>
                    .center-align-wrapper {
                        display: flex;
                        align-items: center;
                    }

                    .center-align-wrapper i {
                        margin-left: 8px;
                        /* 아이콘과 텍스트 사이에 여백을 주기 위해 */
                    }
                </style>
                <a href="./setting_list" class="fs_20 fw_700 text_dynamic line_h1_3 center-align-wrapper"
                    onclick="sendBottomMenuEvent('setting_list')">
                    <?= $_SESSION['_mt_nickname'] ?>(<?= $_SESSION['_mt_name'] ?>)
                </a>
                <p class="fs_12 text_gray fw_500 mt-2"><?= $plan_use ?></p>
                <?php if ($mem_row['mt_level'] == '5') { ?>
                    <p class="fs_13 fc_mian_sec fw_500 mt-1">
                        <?= $mem_row['mt_lang'] == 'ko' ? format_phone($_SESSION['_mt_id']) : $mem_row['mt_email'] ?>
                    </p>
                <?php } ?>
            </div>

            <!-- <div>
                <a href="./setting_list" onclick="sendBottomMenuEvent('setting_list')">
                    <i class="xi-angle-right-thin fs_24 fw_600"></i>
                </a>
            </div> -->
        </div>

        <div class="py_16">
            <div class="px_16">
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="./setting_list" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('account_settings')">
                        <p class="fs_16 fw_600"><?= $translations['txt_account_settings'] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./manual" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('agreement_management')">
                        <p class="fs_16 fw_600"><?= $translations['txt_manual'] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./setting_alarm" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('alarm_settings')">
                        <p class="fs_16 fw_600"><?= $translations['txt_notification_settings'] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./agree" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('agreement_management')">
                        <p class="fs_16 fw_600"><?= $translations['txt_terms_and_conditions'] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
                <?php if ($userLang == "ko") { ?>
                    <div class="border rounded-lg py_16 bg-white mb-3">
                        <a href="./setting_map" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('setting_map')">
                            <p class="fs_16 fw_600"><?= $translations['txt_map_select'] ?></p>
                            <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                        </a>
                        <a href="./inquiry" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('inquiry_history')">
                            <p class="fs_16 fw_600"><?= $translations['txt_contact_history'] ?></p>
                            <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                        </a>
                        <a href="./notice" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('notice')">
                            <p class="fs_16 fw_600"><?= $translations['txt_notice'] ?></p>
                            <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                        </a>
                        <a href="./faq" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('faq')">
                            <p class="fs_16 fw_600">FAQ</p>
                            <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                        </a>
                    </div>
                <?php } ?>
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <?php if (!$detect_mobile->isMobile()) { ?>
                        <a href="./coupon" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('coupon')">
                            <p class="fs_16 fw_600"><?= $translations['txt_enter_coupon'] ?></p>
                            <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                        </a>
                    <?php } ?>
                    <a href="./recomd_code" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('referral_code')">
                        <p class="fs_16 fw_600"><?= $translations['txt_referrer_input'] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./purchase_list" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('purchase_history')">
                        <p class="fs_16 fw_600"><?= $translations['txt_purchase_history'] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./plan_information" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('subscription_management')">
                        <p class="fs_16 fw_600"><?= $translations['txt_subscription_management'] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>