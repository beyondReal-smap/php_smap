<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.rlt_show', 'Y');

    //검색
    //날짜 조건
    if ($_POST['sel_search_sdate'] || $_POST['sel_search_edate']) {
        if ($_POST['sel_search_sdate'] && !$_POST['sel_search_edate']) {
            $DB->where("DATE(rlt_wdate)", $_POST['sel_search_sdate'], ">=");
        }
        if (!$_POST['sel_search_sdate'] && $_POST['sel_search_edate']) {
            $DB->where("DATE(rlt_wdate)", $_POST['sel_search_edate'], "<=");
        }
        if ($_POST['sel_search_sdate'] && $_POST['sel_search_edate']) {
            $DB->where("DATE(rlt_wdate)", array($_POST['sel_search_sdate'], $_POST['sel_search_edate']), "BETWEEN");
        }
    }
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.mt_id, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.mt_name, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.rlt_mt_id, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.rlt_mt_name, \'' . $_POST['obj_search_txt'] . '\') )');
        } else {
            $DB->Where('( instr(' . $_POST['obj_sel_search'] . ', \'' . $_POST['obj_search_txt'] . '\') )');
        }
    }
    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.rlt_idx", "desc");
    } else {
        $DB->orderBy("a1.rlt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("recommend_log_t a1", $pg);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);
?>
    <table class="table inx-table inx-table-card">
        <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:100px;">
                    번호
                </th>
                <th class="text-center" style="width:160px;">
                    추천자
                </th>
                <th class="text-center" style="width:120px;">
                    추천인
                </th>
                <th class="text-center" style="width:120px;">
                    추천코드
                </th>
                <th class="text-center" style="width:120px;">
                    추천일시
                </th>
                <th class="text-center" style="width:120px;">
                    가용일자
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
            ?>
                    <tr>
                        <td data-title="번호" class="text-center">
                            <?= $counts ?>
                        </td>
                        <td data-title="추천자" class="text-center">
                            <?= format_phone($row['mt_id']) ?><br>
                            <?= $row['mt_name'] ?>(<?= $row['mt_nickname'] ?>)
                        </td>
                        <td data-title="추천인" class="text-center">
                            <?= format_phone($row['rlt_mt_id']) ?><br>
                            <?= $row['rlt_mt_name'] ?>(<?= $row['rlt_mt_nickname'] ?>)
                        </td>
                        <td data-title="추천코드" class="text-center">
                            <?= $row['rlt_code'] ?>
                        </td>
                        <td data-title="추천일시" class="text-center">
                            <?= DateType($row['rlt_wdate'], 6) ?>
                        </td>
                        <td data-title="가용일자" class="text-center">
                            <?= $row['rlt_days'] ?>
                        </td>
                    </tr>
                <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="6" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php
    if ($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
