<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '6';
$h_menu = '9';
$_SUB_HEAD_TITLE = translate("설정", $userLang); // "설정" 번역
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";


if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', $userLang), './logout');
    }
}

if ($_SESSION['_mt_nickname'] == '') {
    alert(translate('추가정보 입력이 필요합니다.', $userLang), './setting_modify', ''); // "추가정보 입력이 필요합니다." 번역
}

$mem_row = get_member_t_info($_SESSION['_mt_idx']);
if ($mem_row['mt_level'] == '2') {
    $plan_use = translate('무료플랜 사용중', $userLang); // "무료플랜 사용중" 번역
} elseif ($mem_row['mt_level'] == '5') {
    $plan_use = translate('유료플랜 사용중', $userLang); // "유료플랜 사용중" 번역
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
                    <p class="fs_13 fc_mian_sec fw_500 mt-1"><?= format_phone($_SESSION['_mt_id']) ?></p>
                <?php } ?>
            </div>

            <div>
                <a href="./setting_list" onclick="sendBottomMenuEvent('setting_list')">
                    <i class="xi-angle-right-thin fs_24 fw_600"></i>
                </a>
            </div>
        </div>

        <div class="py_16">
            <div class="px_16">
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="./setting_list" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('account_settings')">
                        <p class="fs_16 fw_600"><?= translate('계정설정', $userLang) ?></p> <!-- "계정설정" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./manual" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('agreement_management')">
                        <p class="fs_16 fw_600"><?= translate('매뉴얼', $userLang) ?></p> <!-- "메뉴얼" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./setting_alarm" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('alarm_settings')">
                        <p class="fs_16 fw_600"><?= translate('알림설정', $userLang) ?></p> <!-- "알림설정" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./agree" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('agreement_management')">
                        <p class="fs_16 fw_600"><?= translate('약관 및 동의 관리', $userLang) ?></p> <!-- "약관 및 동의 관리" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="./inquiry" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('inquiry_history')">
                        <p class="fs_16 fw_600"><?= translate('1:1 문의 내역', $userLang) ?></p> <!-- "1:1 문의 내역" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./faq" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('faq')">
                        <p class="fs_16 fw_600"><?= translate('FAQ', $userLang) ?></p> <!-- "FAQ" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./notice" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('notice')">
                        <p class="fs_16 fw_600"><?= translate('공지사항', $userLang) ?></p> <!-- "공지사항" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <?php if (!$detect_mobile->isMobile()) { ?>
                        <a href="./coupon" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('coupon')">
                            <p class="fs_16 fw_600"><?= translate('쿠폰입력', $userLang) ?></p> <!-- "쿠폰입력" 번역 -->
                            <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                        </a>
                    <?php } ?>
                    <a href="./recomd_code" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('referral_code')">
                        <p class="fs_16 fw_600"><?= translate('추천인 입력', $userLang) ?></p> <!-- "추천인 입력" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./purchase_list" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('purchase_history')">
                        <p class="fs_16 fw_600"><?= translate('구매내역', $userLang) ?></p> <!-- "구매내역" 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./plan_information" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('subscription_management')">
                        <p class="fs_16 fw_600"><?= translate('구독관리', $userLang) ?></p> <!-- "구독관리" 번역 -->
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