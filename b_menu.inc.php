<?php if ($b_menu) { ?>
<style>
    .b_menu ul {
        display: flex;
        justify-content: space-around;
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .b_menu li {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 20%; /* 5개의 메뉴 아이템이 있으므로 각각 20%의 너비를 가집니다 */
    }
    .b_menu a {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        width: 100%;
    }
    .b_menu_img {
        position: relative;
        width: 24px;
        height: 24px;
    }
    .b_menu_img img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .b_menu_img .img_on {
        display: none;
    }
    .b_menu li.on .b_menu_img .img_off {
        display: none;
    }
    .b_menu li.on .b_menu_img .img_on {
        display: block;
    }
    .b_menu p {
        margin: 4px 0 0;
        font-size: 12px;
        line-height: 1.2;
        text-align: center;
        height: 14px; /* 텍스트의 고정 높이 */
        overflow: hidden; /* 텍스트가 넘치지 않도록 */
    }
</style>
<?php $translations = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $userLang . '.php'; ?>
<div class="b_menu" style="z-index:1054">
    <ul>
        <li <?php if ($b_menu === '1') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/" onclick="sendBottomMenuEvent('home');">
                <div class="b_menu_img">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu1_off.png" alt="<?=$translations['txt_home'] ?>" class="img_off">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu1_on.png" alt="<?=$translations['txt_home'] ?>" class="img_on">
                </div>
                <p><?=$translations['txt_home'] ?></p>
            </a>
        </li>
        <li <?php if ($b_menu === '2') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/group" onclick="sendBottomMenuEvent('group');">
                <div class="b_menu_img">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu2_off.png" alt="<?=$translations['txt_group'] ?>" class="img_off">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu2_on.png" alt="<?=$translations['txt_group'] ?>" class="img_on">
                </div>
                <p><?=$translations['txt_group'] ?></p>
            </a>
        </li>
        <li <?php if ($b_menu === '3') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/schedule" onclick="sendBottomMenuEvent('schedule');">
                <div class="b_menu_img">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu3_off.png" alt="<?=$translations['txt_schedule'] ?>" class="img_off">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu3_on.png" alt="<?=$translations['txt_schedule'] ?>" class="img_on">
                </div>
                <p><?=$translations['txt_schedule'] ?></p>
            </a>
        </li>
        <li <?php if ($b_menu === '4') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/location" onclick="sendBottomMenuEvent('location');">
                <div class="b_menu_img">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu4_off.png" alt="<?=$translations['txt_my_places'] ?>" class="img_off">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu4_on.png" alt="<?=$translations['txt_my_places'] ?>" class="img_on">
                </div>
                <p><?=$translations['txt_my_places'] ?></p>
            </a>
        </li>
        <li <?php if ($b_menu === '5') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/log" onclick="sendBottomMenuEvent('log');">
                <div class="b_menu_img">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu5_off.png" alt="<?=$translations['txt_log'] ?>" class="img_off">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu5_on.png" alt="<?=$translations['txt_log'] ?>" class="img_on">
                </div>
                <p><?=$translations['txt_log'] ?></p>
            </a>
        </li>
    </ul>
</div>

<script>
// 기존 JavaScript 코드는 그대로 유지
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
</script>
<?php } ?>