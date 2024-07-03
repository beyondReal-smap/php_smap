<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['fct_idx'] == "") {
        p_alert("잘못된 접근입니다. fct_idx", 'back');
    }
    if($_POST['ft_title'] == "") {
        p_alert("잘못된 접근입니다. ft_title", 'back');
    }
    if($_POST['ft_content'] == "") {
        p_alert("잘못된 접근입니다. ft_content", 'back');
    }
    if($_POST['ft_show'] == "") {
        p_alert("잘못된 접근입니다. ft_show", 'back');
    }

    $DB->where('fct_show', 'Y');
    $DB->where('fct_idx', $_POST['fct_idx']);
    $row_fct = $DB->getone('faq_category_t');

    if($row_fct['fct_name']) {
        $_POST['fct_name'] = $row_fct['fct_name'];
    }

    unset($arr_query);
    $arr_query = array(
        "fct_idx" => $_POST['fct_idx'],
        "fct_name" => $_POST['fct_name'],
        "ft_title" => $_POST['ft_title'],
        "ft_content" => $_POST['ft_content'],
        "ft_rank" => $_POST['ft_rank'],
        "ft_show" => $_POST['ft_show'],
        "ft_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('faq_t', $arr_query);

    p_alert("등록되었습니다.", "./faq_list");
} elseif ($_POST['act'] == "update") {
    if($_POST['fct_idx'] == "") {
        p_alert("잘못된 접근입니다. fct_idx", 'back');
    }
    if($_POST['ft_title'] == "") {
        p_alert("잘못된 접근입니다. ft_title", 'back');
    }
    if($_POST['ft_content'] == "") {
        p_alert("잘못된 접근입니다. ft_content", 'back');
    }
    if($_POST['ft_show'] == "") {
        p_alert("잘못된 접근입니다. ft_show", 'back');
    }

    $DB->where('fct_show', 'Y');
    $DB->where('fct_idx', $_POST['fct_idx']);
    $row_fct = $DB->getone('faq_category_t');

    if($row_fct['fct_name']) {
        $_POST['fct_name'] = $row_fct['fct_name'];
    }

    unset($arr_query);
    $arr_query = array(
        "fct_idx" => $_POST['fct_idx'],
        "fct_name" => $_POST['fct_name'],
        "ft_title" => $_POST['ft_title'],
        "ft_content" => $_POST['ft_content'],
        "ft_rank" => $_POST['ft_rank'],
        "ft_show" => $_POST['ft_show'],
        "ft_wdate" => $DB->now(),
    );

    $DB->where('ft_idx', $_POST['ft_idx']);

    $DB->update('faq_t', $arr_query);
    $_last_idx = $_POST['ft_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    $DB->where('ft_idx', $_POST['obj_idx']);
    $DB->delete('faq_t');

    echo "Y";
} elseif ($_POST['act'] == "list") {
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
<table class="table inx-table inx-table-card">
    <thead class="thead-dark">
        <tr>
            <th class="text-center" style="width:100px;">
                번호
            </th>
            <th class="text-center" style="width:160px;">
                관리
            </th>
            <th class="text-center" style="width:180px;">
                카테고리
            </th>
            <th class="text-center">
                제목
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
                <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./faq_form?act=update&ft_idx=<?=$row['ft_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./faq_update', '<?=$row['ft_idx']?>');" />
            </td>
            <td data-title="카테고리" class="text-center">
                <?=$row['fct_name']?>
            </td>
            <td data-title="제목">
                <span class="line1_text"><?=$row['ft_title']?></span>
            </td>
            <td data-title="노출순서" class="text-center">
                <?=$row['ft_rank']?>
            </td>
            <td data-title="노출여부" class="text-center">
                <?=$row['ft_show']?>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['ft_wdate'], 6)?>
            </td>
        </tr>
        <?php
            $counts--;
        }
    } else {
        ?>
        <tr>
            <td colspan="7" class="text-center"><b>자료가 없습니다.</b></td>
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
