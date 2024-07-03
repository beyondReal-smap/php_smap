<div class="b_menu">
    <ul>
        <li <?php if ($b_menu === '1') { ?> class="on" <?php } ?>>
            <a href="./index.php">
                <div class="b_menu_img d-flex flex-column">
                    <img src="./img/ic_b_menu1_off.png" alt="홈 아이콘" class="img_off flex-shrink-0" >
                    <img src="./img/ic_b_menu1_on.png" alt="홈 아이콘" class="img_on flex-shrink-0">
                </div>
                <p class="">홈</p>
            </a>
        </li>
        <li <?php if ($b_menu === '2') { ?> class="on" <?php } ?>>
            <a href="./group.php">
                <div class="b_menu_img d-flex flex-column">
                    <img src="./img/ic_b_menu2_off.png" alt="그룹 아이콘" class="img_off flex-shrink-0">
                    <img src="./img/ic_b_menu2_on.png" alt="그룹 아이콘" class="img_on flex-shrink-0">
                </div>
                <p class="">그룹</p>
            </a>
        </li>
        <li <?php if ($b_menu === '3') { ?> class="on" <?php } ?>>
            <a href="./schedule.php">
                <div class="b_menu_img d-flex flex-column">
                    <img src="./img/ic_b_menu3_off.png" alt="일정 아이콘" class="img_off flex-shrink-0">
                    <img src="./img/ic_b_menu3_on.png" alt="일정 아이콘" class="img_on flex-shrink-0">
                </div>
                <p class="">일정</p>
            </a>
        </li>
        <li <?php if ($b_menu === '4') { ?> class="on" <?php } ?>>
            <a href="./location.php">
                <div class="b_menu_img d-flex flex-column">
                    <img src="./img/ic_b_menu4_off.png" alt="내장소" class="img_off flex-shrink-0">
                    <img src="./img/ic_b_menu4_on.png" alt="내장소" class="img_on flex-shrink-0">
                </div>
                <p class="">내장소</p>
            </a>
        </li>
        <li <?php if ($b_menu === '5') { ?> class="on" <?php } ?>>
            <a href="./setting.php">
                <div class="b_menu_img d-flex flex-column">
                    <img src="./img/ic_b_menu5_off.png" alt="로그" class="img_off flex-shrink-0">
                    <img src="./img/ic_b_menu5_on.png" alt="로그" class="img_on flex-shrink-0">
                </div>
                <p class="">로그</p>
            </a>
        </li>
    </ul>
</div>