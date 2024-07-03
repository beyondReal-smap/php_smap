<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['nt_title'] == "") {
        p_alert("잘못된 접근입니다. nt_title", 'back');
    }
    if($_POST['nt_content'] == "") {
        p_alert("잘못된 접근입니다. nt_content", 'back');
    }
    if($_POST['nt_show'] == "") {
        p_alert("잘못된 접근입니다. nt_show", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "mt_idx" => $_SESSION['_mt_idx'],
        "nt_title" => $_POST['nt_title'],
        "nt_content" => $_POST['nt_content'],
        "nt_show" => $_POST['nt_show'],
        "nt_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('notice_t', $arr_query);

    p_alert("등록되었습니다.", "./notice_list");
} elseif ($_POST['act'] == "update") {
    if($_POST['nt_title'] == "") {
        p_alert("잘못된 접근입니다. nt_title", 'back');
    }
    if($_POST['nt_content'] == "") {
        p_alert("잘못된 접근입니다. nt_content", 'back');
    }
    if($_POST['nt_show'] == "") {
        p_alert("잘못된 접근입니다. nt_show", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "nt_title" => $_POST['nt_title'],
        "nt_content" => $_POST['nt_content'],
        "nt_show" => $_POST['nt_show'],
        "nt_wdate" => $DB->now(),
    );

    $DB->where('nt_idx', $_POST['nt_idx']);

    $DB->update('notice_t', $arr_query);
    $_last_idx = $_POST['nt_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    $DB->where('nt_idx', $_POST['obj_idx']);
    $DB->delete('notice_t');

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
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
                제목
            </th>

            <th class="text-center" style="width:120px;">
                노출여부
            </th>
            <th class="text-center" style="width:120px;">
                조회수
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
                <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./notice_form?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./notice_update', '<?=$row['nt_idx']?>');" />
            </td>
            <td data-title="제목">
                <span class="line1_text"><?=$row['nt_title']?></span>
            </td>
            <td data-title="노출여부" class="text-center">
                <?=$row['nt_show']?>
            </td>
            <td data-title="조회수" class="text-center">
                <?=number_format($row['nt_hit'])?>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['nt_wdate'], 6)?>
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
