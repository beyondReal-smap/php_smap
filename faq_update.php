<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.ft_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.ft_content, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['sel_fct_idx']) {
        $DB->where('a1.fct_idx', $_POST['sel_fct_idx']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.ft_idx", "desc");
    } else {
        $DB->orderBy("a1.ft_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("faq_t a1", $pg);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);
    ?>
<div id="accordion" class="accordion_1">
    <?php
    if ($list) {
        foreach ($list as $row) {
            ?>
    <div class="card aco_list collapsed " data-toggle="collapse" data-target="#collapse_box<?=$row['ft_idx']?>" aria-expanded="false" aria-controls="collapse_box<?=$row['ft_idx']?>">
        <div class="card-header border-0" id="heading_box<?=$row['ft_idx']?>">
            <p class="fs_13 fw_500 text-primary"><?=$row['fct_name']?></p>
            <div class="d-flex justify-content-between align-items-center">
                <p class="fs_15 fw_700 text_dynamic mt_08 line_h1_3"><?=$row['ft_title']?></p>
                <button type="button" class="btn btn-link position-relative aco_btn"></button>
            </div>
        </div>
        <!-- 오픈할때 .collapse 클래스에 .show 추가-->
        <div id="collapse_box<?=$row['ft_idx']?>" class="collapse" aria-labelledby="heading_box<?=$row['ft_idx']?>" data-parent="#accordion">
            <div class="card-body">
                <div class="bg_f9f9f9 rounded_06 p-4">
                    <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5"><?=$row['ft_content']?></p>
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
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";