<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $nt_n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.nt_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.nt_content, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.nt_idx", "desc");
    } else {
        $DB->orderBy("a1.nt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("notice_t a1", $pg);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $nt_n_limit_num);
    ?>
<ul>
        <?php
    if ($list) {
        foreach ($list as $row) {
            ?>
    <li>
        <a href="./notice_detail?act=view&nt_idx=<?=$row['nt_idx']?>" class="d-flex justify-content-between align-items-center py_23 border-bottom">
            <div>
                <p class="fs_15 fw_700 text_dynamic line1_text mr-3"><?=$row['nt_title']?></p>
                <p class="text_light_gray fs_13 fw_300 mt_08"><?=DateType($row['nt_wdate'], 6)?></p>
            </div>
            <i class="xi-angle-right-min text_light_gray"></i>
        </a>
    </li>
        <?php
            $counts--;
        }
    } else {
        ?>
    <li>
        <p class="text-center mt-3 mb-3"><b><?= translate('자료가 없습니다.', $userLang); ?></b></p>
    </li>
        <?php
    }
    ?>
    </ul>
<?php
    if($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_list');
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
