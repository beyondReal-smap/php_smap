<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "return") {
    $DB->where('mt_idx', $_POST['mt_idx_t']);
    $row = $DB->getone('member_t');

    unset($arr_query);
    $arr_query = array(
        "mt_show" => 'Y',
        "mt_status" => '1',
        "mt_level" => '2',
    );

    $DB->where('mt_idx', $_POST['mt_idx_t']);

    $DB->update('member_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.mt_show', 'N');
    $DB->where('a1.mt_level', '1');
    $DB->where('a1.mt_retire_level', '2');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_hp, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.mt_idx", "desc");
    } else {
        $DB->orderBy("a1.mt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("member_t a1", $pg);

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
            <th class="text-center" style="width:140px;">
                관리
            </th>
            <th class="text-center">
                아이디
            </th>
            <th class="text-center">
                이름
            </th>

            <th class="text-center">
                연락처
            </th>
            <th class="text-center">
                상태
            </th>
            <th class="text-center" style="width:140px;">
                등록일시
            </th>

            <th class="text-center" style="width:140px;">
                접속일시
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
                <?=$counts?>
            </td>
            <td data-title="관리" class="text-center">
                <input type="button" class="btn btn-outline-primary btn-sm" value="상세" onclick="location.href='./member_form?act=update&mt_idx=<?=$row['mt_idx']?>'" />
                <!-- <input type="button" class="btn btn-outline-success btn-sm" value="복구" onclick="f_return_mem('<?=$row['mt_idx']?>');" /> -->
            </td>
            <td data-title="아이디" class="text-center">
                <?=$row['mt_id_retire']?>
            </td>
            <td data-title="성명" class="text-center">
                <?=$row['mt_name']?>
            </td>

            <td data-title="연락처" class="text-center">
                <?=$row['mt_id_retire']?>
            </td>
            <td data-title="상태" class="text-center">
                <?=$arr_mt_status[$row['mt_status']]?>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['mt_wdate'], 6)?>
            </td>

            <td data-title="접속일시" class="text-center">
                <?=DateType($row['mt_ldate'], 6)?>
            </td>
        </tr>
        <?php
            $counts--;
        }
    } else {
        ?>
        <tr>
            <td colspan="8" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<?php
    if($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
