<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '7';
$h_func = "location.replace('./setting')";
$_SUB_HEAD_TITLE = translate("구매내역", $userLang); // "구매내역" 번역
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', ''); // "로그인이 필요합니다." 번역
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', $userLang), './logout'); // "다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다." 번역
    }
}

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('ot_status', '2');
$DB->where('ot_show', 'Y');
$DB->orderby('ot_edate', 'desc');
$DB->orderby('ot_wdate', 'desc');
$ot_list = $DB->get('order_t');
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic"><?= translate("SMAP 유료플랜", $userLang); ?>
        
        <?= translate("구매내역입니다.", $userLang); ?></p> 
        <!-- "SMAP 유료플랜\n구매내역입니다." 번역 (줄바꿈 유지) -->
        <div class="pt-4">
            <?
            if ($ot_list) {
                foreach ($ot_list as $ot_row) {
            ?>
                    <div class="d-flex align-items-center justify-content-between border-bottom py-4">
                        <div>
                            <p class="fs_14 fw_700 line_h1_3"><?= $ot_row['ot_title'] ?></p>
                            <p class="fs_14 text_dynamic line_h1_3 mt-2"><?= dateType($ot_row['ot_sdate'], 1) ?> ~ <?= dateType($ot_row['ot_edate'], 1) ?></p>
                            <?php if ($ot_row['ot_ccdate']) { ?>
                                <p class="fs_14 text_dynamic line_h1_3 mt-2"><?= dateType($ot_row['ot_edate'], 1) ?> <?= translate('종료', $userLang); ?></p> <!-- "종료" 번역 -->
                            <?php } ?>
                        </div>
                        <div class="text-right">
                            <p class="fs_14 text_dynamic text_dynamic line_h1_3"><?= dateType($ot_row['ot_pdate'], 1) ?> <?= translate('구매됨', $userLang); ?></p> <!-- "구매됨" 번역 -->
                            <p class="fs_14 fw_700 text-primary line_h1_3 mt-2">₩<?= number_format($ot_row['ot_price']) ?></p>
                        </div>
                    </div>
                <?
                }
            } else {
                ?>
                <div class="d-flex align-items-center justify-content-between border-bottom border-top py-4">
                    <div>
                    </div>
                    <div>
                        <p class=" fs_14 fw_700 line_h1_3 align-text-center"><?= translate('자료가 없습니다.', $userLang); ?></p> <!-- "자료가 없습니다." 번역 -->
                    </div>
                    <div>
                    </div>
                </div>
            <?
            }
            ?>
        </div>
    </div>
</div>


<?php
include_once("./inc/b_menu.php");
include_once("./inc/tail.php");
?>