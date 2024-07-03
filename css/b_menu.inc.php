<?php if ($b_menu) { ?>
<div class="b_menu" style="z-index:1054">
    <ul>
        <li <?php if ($b_menu === '1') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/" onclick="sendBottomMenuEvent('home');">
                <div class="b_menu_img d-flex flex-column">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu1_off.png" alt="홈 아이콘" class="img_off flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu1_on.png" alt="홈 아이콘" class="img_on flex-shrink-0" style="width:24px">
                </div>
                <p class="">홈</p>
            </a>
        </li>
        <li <?php if ($b_menu === '2') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/group" onclick="sendBottomMenuEvent('group');">
                <div class="b_menu_img d-flex flex-column">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu2_off.png" alt="그룹 아이콘" class="img_off flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu2_on.png" alt="그룹 아이콘" class="img_on flex-shrink-0" style="width:24px">
                </div>
                <p class="">그룹</p>
            </a>
        </li>
        <li <?php if ($b_menu === '3') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/schedule" onclick="sendBottomMenuEvent('schedule');">
                <div class="b_menu_img d-flex flex-column">
                    <img src="<?= CDN_HTTP ?>//img/ic_b_menu3_off.png" alt="일정 아이콘" class="img_off flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>//img/ic_b_menu3_on.png" alt="일정 아이콘" class="img_on flex-shrink-0" style="width:24px">
                </div>
                <p class="">일정</p>
            </a>
        </li>
        <li <?php if ($b_menu === '4') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/location" onclick="sendBottomMenuEvent('location');">
                <div class="b_menu_img d-flex flex-column">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu4_off.png" alt="내장소" class="img_off flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu4_on.png" alt="내장소" class="img_on flex-shrink-0" style="width:24px">
                </div>
                <p class="">내장소</p>
            </a>
        </li>
        <li <?php if ($b_menu === '5') { ?> class="on" <?php } ?>>
            <a href="<?= CDN_HTTP ?>/log" onclick="sendBottomMenuEvent('log');">
                <div class="b_menu_img d-flex flex-column">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu5_off.png" alt="로그" class="img_off flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ic_b_menu5_on.png" alt="로그" class="img_on flex-shrink-0" style="width:24px">
                </div>
                <p class="">로그</p>
            </a>
        </li>
    </ul>
</div>

<script>
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