<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['pft_send_type'] == "") {
        p_alert("잘못된 접근입니다. pft_send_type", 'back');
    }
    if($_POST['pft_title'] == "") {
        p_alert("잘못된 접근입니다. pft_title", 'back');
    }
    if($_POST['pft_content'] == "") {
        p_alert("잘못된 접근입니다. pft_content", 'back');
    }
    
    if ($_POST['pft_gt_idx']) {
        $_POST['pft_send_mt_idx'] = json_encode($_POST['pft_gt_idx'], JSON_UNESCAPED_UNICODE);
    }
    if ($_POST['pft_mt_idx']) {
        $_POST['pft_send_mt_idx'] = json_encode($_POST['pft_mt_idx'], JSON_UNESCAPED_UNICODE);
    }


    unset($arr_query);
    $arr_query = array(
        "pft_code" => $_POST['pft_code'],
        "pft_title" => $_POST['pft_title'],
        "pft_content" => $_POST['pft_content'],
        "pft_send_type" => $_POST['pft_send_type'],
        "pft_send_mt_idx" => $_POST['pft_send_mt_idx'],
        "pft_rdate" => $_POST['pft_rdate'],
        "pft_url" => $_POST['pft_url'],
        "pft_status" => '1',
        "pft_show" => "Y",
        "pft_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('push_fcm_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "update") {
    if($_POST['pft_send_type'] == "") {
        p_alert("잘못된 접근입니다. pft_send_type", 'back');
    }
    if($_POST['pft_title'] == "") {
        p_alert("잘못된 접근입니다. pft_title", 'back');
    }
    if($_POST['pft_content'] == "") {
        p_alert("잘못된 접근입니다. pft_content", 'back');
    }

    if($_POST['pft_gt_idx']) {
        $_POST['pft_send_mt_idx'] = json_encode($_POST['pft_gt_idx'], JSON_UNESCAPED_UNICODE);
    }
    if($_POST['pft_mt_idx']) {
        $_POST['pft_send_mt_idx'] = json_encode($_POST['pft_mt_idx'], JSON_UNESCAPED_UNICODE);
    }

    unset($arr_query);
    $arr_query = array(
        "pft_title" => $_POST['pft_title'],
        "pft_content" => $_POST['pft_content'],
        "pft_send_type" => $_POST['pft_send_type'],
        "pft_send_mt_idx" => $_POST['pft_send_mt_idx'],
        "pft_rdate" => $_POST['pft_rdate'],
        "pft_url" => $_POST['pft_url'],
        "pft_wdate" => $DB->now(),
    );

    $DB->where('pft_idx', $_POST['pft_idx']);

    $DB->update('push_fcm_t', $arr_query);
    $_last_idx = $_POST['pft_idx'];

    echo "Y";
} elseif ($_POST['act'] == "delete") {
    unset($arr_query);
    $arr_query = array(
        "pft_show" => 'N',
    );

    $DB->where('pft_idx', $_POST['obj_idx']);

    $DB->update('push_fcm_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.pft_show', 'Y');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.pft_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.pft_content, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['obj_sel_pft_send_type']) {
        $DB->Where('a1.pft_send_type = \''.$_POST['obj_sel_pft_send_type'].'\'');
    }

    if ($_POST['obj_sel_pft_status']) {
        $DB->Where('a1.pft_status = \''.$_POST['obj_sel_pft_status'].'\'');
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.pft_idx", "desc");
    } else {
        $DB->orderBy("a1.pft_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("push_fcm_t a1", $pg);

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
            <th class="text-center" style="width:120px;">
                대상
            </th>
            <th class="text-center">
                제목
            </th>

            <th class="text-center" style="width:120px;">
                상태
            </th>
            <th class="text-center" style="width:140px;">
                발송예약일시
            </th>
            <th class="text-center" style="width:140px;">
                발송시작일시
            </th>
            <th class="text-center" style="width:140px;">
                발송종료일시
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
                <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./push_fcm_form?act=update&pft_idx=<?=$row['pft_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./push_fcm_update', '<?=$row['pft_idx']?>');" />
            </td>
            <td data-title="대상" class="text-center">
                <?=$arr_pft_send_type[$row['pft_send_type']]?>
            </td>
            <td data-title="제목">
                <span class="line1_text"><?=$row['pft_title']?></span>
            </td>

            <td data-title="상태" class="text-center">
                <?=$arr_pft_status[$row['pft_status']]?>
            </td>
            <td data-title="발송예약일시" class="text-center">
                <?=DateType($row['pft_rdate'], 6)?>
            </td>
            <td data-title="발송시작일시" class="text-center">
                <?=DateType($row['pft_sdate'], 6)?>
            </td>
            <td data-title="발송종료일시" class="text-center">
                <?=DateType($row['pft_edate'], 6)?>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['pft_wdate'], 6)?>
            </td>
        </tr>
        <?php
            $counts--;
        }
    } else {
        ?>
        <tr>
            <td colspan="9" class="text-center"><b>자료가 없습니다.</b></td>
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
