<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '7';
$h_func = "location.replace('./setting')";

$_SUB_HEAD_TITLE = $translations['txt_purchase_history_label']; 
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
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic"><?=$translations['txt_smap_paid_plan'] ?>
        
        <?=$translations['txt_purchase_history_info'] ?></p> 
        <div class="pt-4">
            <?
            if ($ot_list) {
                foreach ($ot_list as $ot_row) {
            ?>
                    <div class="d-flex align-items-center justify-content-between border-bottom py-4">
                        <div>
                            <p class="fs_14 fw_700 line_h1_3"><?=$translations['txt_plan_label'] ?></p>
                            <p class="fs_14 text_dynamic line_h1_3 mt-2"><?= dateType($ot_row['ot_sdate'], 1) ?> ~ <?= dateType($ot_row['ot_edate'], 1) ?></p>
                            <?php if ($ot_row['ot_ccdate']) { ?>
                                <p class="fs_14 text_dynamic line_h1_3 mt-2"><?= dateType($ot_row['ot_edate'], 1) ?> <?=$translations['txt_expired'] ?></p>
                            <?php } ?>
                        </div>
                        <div class="text-right">
                            <p class="fs_14 text_dynamic text_dynamic line_h1_3"><?= dateType($ot_row['ot_pdate'], 1) ?> <?=$translations['txt_purchased'] ?></p> 
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
                        <p class=" fs_14 fw_700 line_h1_3 align-text-center"><?=$translations['txt_no_data'] ?></p>
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