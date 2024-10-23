<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    // 고정 검색값
    $DB->where('a1.qt_show', 'Y');
    $DB->where('a1.mt_idx', $_SESSION['_mt_idx']);

    // 검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_qtitle, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_qcontent, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_atitle, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_acontent, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['sel_qt_status']) {
        $DB->where('a1.qt_status', $_POST['sel_qt_status']);
    }

    // 정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.qt_idx", "desc");
    } else {
        $DB->orderBy("a1.qt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("qna_t a1", $pg);

    // 페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);
    ?>
<div id="accordion" class="accordion_1">
    <?php
    if ($list) {
        foreach ($list as $row) {
            $qt_status_cls = $row['qt_status'] == '1' ? 'text_gray' : 'text-primary';
            ?>
    <div class="card aco_list collapsed" data-toggle="collapse" data-target="#collapse_box<?=$row['qt_idx']?>" aria-expanded="false" aria-controls="collapse_box<?=$row['qt_idx']?>">
        <div class="card-header border-0" id="heading_box<?=$row['qt_idx']?>">
            <div class="d-flex justify-content-between align-items-center">
                <div class="">
                    <p class="fs_13 fw_500 <?=$qt_status_cls?>"><?= $arr_qt_status[$row['qt_status']] ?></p>
                    <p class="fs_15 fw_700 text_dynamic mt_08 line2_text line_h1_3"><?= $row['qt_qtitle'] ?></p>
                    <p class="text_light_gray fs_13 fw_300 mt_08"><?= DateType($row['qt_qdate'], 6) ?></p>
                </div>
                <button type="button" class="btn btn-link position-relative aco_btn"></button>
            </div>
        </div>
        <!-- 오픈할때 .collapse 클래스에 .show 추가-->
        <div id="collapse_box<?=$row['qt_idx']?>" class="collapse" aria-labelledby="heading_box<?=$row['qt_idx']?>" data-parent="#accordion">
            <div class="card-body">
                <div class="bg_f9f9f9 rounded_06 p-4">
                    <div class="border-bottom pb-3 mb-3">
                        <p class="fs_15 fw_600 text_dynamic text_gray line_h1_5"><?= $translations['txt_inquiry_content']; ?></p>
                        <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5 mt-3"><?=nl2br($row['qt_qcontent'])?></p>
                    </div>
                    <?php
                        if($row['qt_status']=='1') {
                    ?>
                    <div>
                        <p class="text-primary fw_600 fs_14 fw_300"><?= $translations['txt_answer']; ?></p>
                        <div class="text-center py-4">
                            <img src="<?=CDN_HTTP?>/img/warring.png" width="62px" alt="<?= $translations['txt_no_data']; ?>">
                            <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5 mt-3"><?= $translations['txt_waiting_for_answer']; ?></p>
                        </div>
                    </div>
                    <?php
                        } else {
                    ?>
                    <div>
                        <p class="text-primary fw_600 fs_14 fw_300"><?= $translations['txt_answer']; ?></p>
                        <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5 mt-3"><?=nl2br($row['qt_acontent'])?></p>
                        <p class="text_light_gray fs_13 fw_300 mt_08"><?=DateType($row['qt_adate'], 6)?></p>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
            $counts--;
        }
    } else {
        ?>
    <p class="text-center mt-3 mb-3"><b><?= $translations['txt_no_data']; ?></b></p>
    <?php
    }
    ?>
</div>
<?php
    if($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_list');
    }
} else if ($_POST['act'] == "input") {
    if(empty($_POST['qt_qtitle']) || empty($_POST['qt_qcontent'])) {
        p_alert($translations['txt_invalid_access'], 'back');
        exit;
    }

    $arr_query = array(
        "mt_idx" => $_SESSION['_mt_idx'],
        "mt_id" => $_SESSION['_mt_id'],
        "mt_name" => $_SESSION['_mt_name'],
        "qt_qtitle" => $_POST['qt_qtitle'],
        "qt_qcontent" => $_POST['qt_qcontent'],
        "qt_status" => 1,
        "qt_show" => "Y",
        "qt_qdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('qna_t', $arr_query);

    p_alert($translations['txt_registered'], "./inquiry");
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>