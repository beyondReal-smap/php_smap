<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = translate("플랜", $userLang); 
$h_menu = '2';
$_SUB_HEAD_TITLE = translate("플랜", $userLang); 
$sub_title = "plan";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', '');
} else {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', $userLang), './logout');
    }
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('ot_pay_type', '1');
    $first_chk = $DB->getone('order_t');
    $first_check = $first_chk['ot_idx'] ? false : true;
}
?>

<div class="container sub_pg">
    <div class="mt-5">
        <p class=""><span class="plan_pay"><?= translate('Plus', $userLang) ?></span></p>
        <p class="tit_h1 text_dynamic line_h1_3 text_dynamic mt-4"><?= translate('SMAP의 모든 기능을', $userLang) ?> 
        <?= translate('제한없이 경험해 보세요.', $userLang) ?></p>
        <ul class="mt_20 mb_50">
            <li class="position-relative slash7 my-3 fs_16"><?= translate('내 장소를 마음껏 저장해보세요.', $userLang) ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= translate('2주 동안의 로그도 조회할 수있어요.', $userLang) ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= translate('광고 걱정 없이 쾌적하게 이용해보세요.', $userLang) ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= translate('하루에 최적경로 10회 조회도 가능해요!', $userLang) ?></li>
            <li class="position-relative slash7 my-3 fs_16"><?= translate('그룹원을 10명까지 관리 가능해요!', $userLang) ?></li>
        </ul>
        <form action="" class="">
            <div class="plan_info_wrap">
                <div class="checks ava_checks">
                    <label class="" onclick="change_price_text('month')">
                        <input type="radio" name="rd1">
                        <div class="w-100 chk_p rounded  ava_class ava_class_b mt-0">
                            <p class="fs_15 fw_700 text_dynamic"><?= translate('월간', $userLang) ?></p>
                            <div class="d-flex align-items-center">
                                <p class="fs_16 fw_700 text-nowrap mr-3"><?= translate('￦4,900', $userLang) ?>/<?= translate('월', $userLang) ?></p>
                                <div class="ava_class_btn ava_class_btn_b select-btn" data-type="month" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','month','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')"><?= translate('선택', $userLang) ?></div>
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
                                <p class="fs_15 fw_700 text_dynamic"><?= translate('연간', $userLang) ?></p>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs_12 fw_300 text_dynamic mt_08 mr-2"><del><?= translate('￦58,800', $userLang) ?></del></p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <p class="fs_16 fw_700 mr-3 text-nowrap"><?= translate('￦42,000', $userLang) ?>/<?= translate('년', $userLang) ?></p>
                                <div class="ava_class_btn ava_class_btn_b select-btn" data-type="year" onclick="f_subscribe_btn('<?= $_SESSION['_mt_idx'] ?>','year','<?= $first_check ?>', '<?= $mem_row['mt_level'] ?>')"><?= translate('선택', $userLang) ?></div>
                            </div>
                        </div>
                        <p class="plan_type"><span class="plan_comp"><?= translate('인기!!', $userLang) ?></span></p>
                    </label>
                </div>
            </div>
            <div class="plan_info_wrap d-none">
                <div class="checks ava_checks ord_ava_checks">
                    <label class="">
                        <input type="radio" name="rd2">
                        <div class="w-100 chk_p rounded  ava_class ava_class_b justify-content-center mt-0">
                            <div class="d-block">
                                <p class="fs_16 fw_700 text_dynamic">￦0에 <?= translate('사용해 보세요', $userLang) ?></p>
                            </div>
                        </div>
                    </label>
                </div>
                <p class="fs_15 fw_600 text-center text_dynamic line_h1_3 mt-3"><?= translate('1주 무료체험, 그 이후부터 매년 ', $userLang) ?><?= translate('￦42,000', $userLang) ?></p>
            </div>
            <p class="fs_15 fw_600 text-center text_dynamic line_h1_3 mt-3"><?= translate('1주 무료체험, 그 이후부터', $userLang) ?> <span id="change_price1"><?= translate('매년', $userLang) ?> <?= translate('￦42,000', $userLang) ?></span>
                <span id="store"><?= translate('App Store', $userLang) ?></span><?= translate('에서 언제든지 취소하세요.', $userLang) ?>
            </p>
            <p class="fs_13 fc_gray_600 line_h1_3 mt-4"><?= translate('무료 체험판 사용 기간이 끝나면', $userLang) ?> <span id="change_price2"><?= translate('￦42,000', $userLang) ?>/<?= translate('년', $userLang) ?></span> <?= translate('요금이', $userLang) ?> <span id="id"><?= translate('Apple ID', $userLang) ?></span><?= translate('계정에 청구됩니다. 현재 기간이 종료되기까지 최소 24시간 전에 구독이 취소되지 않으면 구독이 갱신됩니다.', $userLang) ?> 
             <?= translate('귀하의 계정은 현 기간이 종료되기 전, 24시간 이내에 갱신되어 요금이 부과됩니다.', $userLang) ?> <?= translate('구매 후', $userLang) ?><span id="store2"><?= translate('앱 스토어', $userLang) ?></span> <?= translate('계정 설정으로 이동하여 구독을 관리하고 취소할 수 있습니다.', $userLang) ?></p>

            <button type="button" id="restore_btn" class="btn btn-sm d-none" onclick="f_restore_btn('<?= $_SESSION['_mt_idx'] ?>','restorePurchase')"><?= translate('복원하기', $userLang) ?></button>
        </form>
    </div>
</div>

<!-- 존재하는 메일주소 -->
<div class="modal fade" id="dpl_email" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= translate('이미 사용중인 메일주소에요. 다른 메일주소를 입력해 주세요!', $userLang) ?></p>
            </div>
            <div class="modal-footer px-0 py-0">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close"><?= translate('확인하기', $userLang) ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        change_price_text('year'); // '연간' 플랜 가격 정보 표시
    });
</script>
<?
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>