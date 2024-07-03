<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.ot_show', 'Y');

    //검색
    //날짜 조건
    if ($_POST['sel_search_sdate'] || $_POST['sel_search_edate']) {
            if ($_POST['sel_search_sdate'] && !$_POST['sel_search_edate']) {
                $DB->where("DATE(ot_pdate)", $_POST['sel_search_sdate'], ">=");
            }
            if (!$_POST['sel_search_sdate'] && $_POST['sel_search_edate']) {
                $DB->where("DATE(ot_pdate)", $_POST['sel_search_edate'], "<=");
            }
            if ($_POST['sel_search_sdate'] && $_POST['sel_search_edate']) {
                $DB->where("DATE(ot_pdate)", array($_POST['sel_search_sdate'], $_POST['sel_search_edate']), "BETWEEN");
            }
        
    }
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.mt_id, \'' . $_POST['obj_search_txt'] . '\') or instr(a2.mt_name, \'' . $_POST['obj_search_txt'] . '\') )');
        } else {
            $DB->Where('( instr(' . $_POST['obj_sel_search'] . ', \'' . $_POST['obj_search_txt'] . '\') )');
        }
    }
    if ($_POST['sel_ot_pay_type']) {
        $DB->where('a1.ot_pay_type', $_POST['sel_ot_pay_type']);
    }

    //Join
    $DB->join("member_t a2", "a1.mt_idx=a2.mt_idx", "LEFT");

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.ot_idx", "desc");
    } else {
        $DB->orderBy("a1.ot_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("order_t a1", $pg);

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
                    아이디(전화번호)
                </th>
                <th class="text-center" style="width:120px;">
                    이름(닉네임)
                </th>
                <th class="text-center" style="width:120px;">
                    결제일/완료일
                </th>
                <th class="text-center" style="width:120px;">
                    신규가입여부
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
                        <td data-title="아이디(전화번호)" class="text-center">
                            <?= format_phone($row['mt_id']) ?>
                        </td>
                        <td data-title="이름(닉네임)" class="text-center">
                            <?= $row['mt_name'] ?>(<?= $row['mt_nickname'] ?>)
                        </td>
                        <td data-title="결제일/완료일" class="text-center">
                            <?= DateType($row['ot_pdate'], 6) ?><br>
                            <?= DateType($row['ot_edate'], 6) ?>
                        </td>
                        <td data-title="신규가입여부" class="text-center">
                            <?= $arr_ot_pay_type[$row['ot_pay_type']] ?>
                        </td>
                    </tr>
                <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="5" class="text-center"><b>자료가 없습니다.</b></td>
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
