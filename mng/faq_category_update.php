<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['fct_name'] == "") {
        p_alert("잘못된 접근입니다. fct_name", 'back');
    }
    if($_POST['fct_show'] == "") {
        p_alert("잘못된 접근입니다. fct_show", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "fct_name" => $_POST['fct_name'],
        "fct_rank" => $_POST['fct_rank'],
        "fct_show" => $_POST['fct_show'],
        "fct_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('faq_category_t', $arr_query);

    p_alert("등록되었습니다.", "./faq_category_list");
} elseif ($_POST['act'] == "update") {
    if($_POST['fct_name'] == "") {
        p_alert("잘못된 접근입니다. fct_name", 'back');
    }
    if($_POST['fct_show'] == "") {
        p_alert("잘못된 접근입니다. fct_show", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "fct_name" => $_POST['fct_name'],
        "fct_rank" => $_POST['fct_rank'],
        "fct_show" => $_POST['fct_show'],
        "fct_wdate" => $DB->now(),
    );

    $DB->where('fct_idx', $_POST['fct_idx']);

    $DB->update('faq_category_t', $arr_query);
    $_last_idx = $_POST['fct_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    $DB->where('fct_idx', $_POST['obj_idx']);
    $DB->delete('faq_category_t');

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.fct_name, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.fct_idx", "desc");
    } else {
        $DB->orderBy("a1.fct_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("faq_category_t a1", $pg);

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
                관리
            </th>
            <th class="text-center">
                카테고리명
            </th>
            <th class="text-center" style="width:120px;">
                노출순서
            </th>
            <th class="text-center" style="width:120px;">
                노출여부
            </th>
            <th class="text-center" style="width:140px;">
                등록일시
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
                <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./faq_category_form?act=update&fct_idx=<?=$row['fct_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./faq_category_update', '<?=$row['fct_idx']?>');" />
            </td>
            <td data-title="카테고리명">
                <span class="line1_text"><?=$row['fct_name']?></span>
            </td>
            <td data-title="노출순서" class="text-center">
                <?=$row['fct_rank']?>
            </td>
            <td data-title="노출여부" class="text-center">
                <?=$row['fct_show']?>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['fct_wdate'], 6)?>
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
    if($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
