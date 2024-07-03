<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '6';
$h_menu = '9';
$_SUB_HEAD_TITLE = "설정";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";


if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
    }
}

if ($_SESSION['_mt_nickname'] == '') {
    alert('추가정보 입력이 필요합니다.', './setting_modify', '');
}

$mem_row = get_member_t_info($_SESSION['_mt_idx']);
if ($mem_row['mt_level'] == '2') {
    $plan_use = '무료플랜 사용중';
} elseif ($mem_row['mt_level'] == '5') {
    $plan_use = '유료플랜 사용중';
}

$st_info = get_setup_t_info();
?>
<script>
    function openurl(t) {
        var message = {
            "type": "openUrlBlank",
            "param": t
        };
        if (isAndroid()) {
            window.smapAndroid.openUrlBlank(t);
        } else if (isiOS()) {
            window.webkit.messageHandlers.smapIos.postMessage(message);
        }
    }

    function sendBottomMenuEvent(label) {
        gtag('event', 'click_bottom_menu', {
            'event_category': 'engagement',
            'event_label': label,
            'user_id': '<?= $_SESSION['_mt_idx'] ?>',
            'platform': isAndroid() ? 'Android' : (isiOS() ? 'iOS' : 'Unknown')
        });
    }

    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    }

    function isAndroidDevice() {
    return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
    }

    function isiOSDevice() {
        return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
    }
</script>
<div class="container sub_pg px-0 bg_main">
    <div>
        <div class="pt_24 pb-5 d-flex align-items-end justify-content-between border-bottom px_20 bg-white prd_setting">
            <div class="mb-3  mr-3">
                <style>
                    .center-align-wrapper {
                        display: flex;
                        align-items: center;
                    }
                    .center-align-wrapper i {
                        margin-left: 8px; /* 아이콘과 텍스트 사이에 여백을 주기 위해 */
                    }
                </style>
                <a href="./setting_list" class="fs_20 fw_700 text_dynamic line_h1_3 center-align-wrapper" onclick="sendBottomMenuEvent('setting_list')">
                    <?= $_SESSION['_mt_nickname'] ?>(<?= $_SESSION['_mt_name'] ?>)
                    <i class="xi-angle-right-thin fs_12 fw_600"></i>
                </a>
                <p class="fs_12 text_gray fw_500 mt-3"><?= $plan_use ?></p>
                <?php if ($mem_row['mt_level'] == '5') { ?>
                    <p class="fs_13 fc_mian_sec fw_500 mt-1"><?= format_phone($_SESSION['_mt_id']) ?></p>
                <?php } ?>
            </div>

            <?php
            //프로필 사진 등록/수정
            include $_SERVER['DOCUMENT_ROOT'] . "/profile.inc.php";
            ?>
        </div>

        <div class="py_16">
            <div class="px_16">
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="./setting_list" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('account_settings')">
                        <p class="fs_16 fw_600">계정설정</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a onclick="sendBottomMenuEvent('manual'); openurl('<?= $st_info['st_agree6'] ?>')" target="_blank" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">메뉴얼</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./setting_alarm" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('alarm_settings')">
                        <p class="fs_16 fw_600">알림설정</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./agree" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('agreement_management')">
                        <p class="fs_16 fw_600">약관 및 동의 관리</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="./inquiry" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('inquiry_history')">
                        <p class="fs_16 fw_600">1:1 문의 내역</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./faq" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('faq')">
                        <p class="fs_16 fw_600">FAQ</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./notice" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('notice')">
                        <p class="fs_16 fw_600">공지사항</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <?php if (!$detect_mobile->isMobile()) { ?>
                        <a href="./coupon" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('coupon')">
                            <p class="fs_16 fw_600">쿠폰입력</p>
                            <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                        </a>
                    <?php } ?>
                    <a href="./recomd_code" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('referral_code')">
                        <p class="fs_16 fw_600">추천인 입력</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./purchase_list" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('purchase_history')">
                        <p class="fs_16 fw_600">구매내역</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./plan_information" class="d-flex align-items-center justify-content-between px_16 py_16" onclick="sendBottomMenuEvent('subscription_management')">
                        <p class="fs_16 fw_600">구독관리</p>
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