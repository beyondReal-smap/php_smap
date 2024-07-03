<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.ct_title, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.ct_code, \'' . $_POST['obj_search_txt'] . '\') )');
        } else {
            $DB->Where('( instr(' . $_POST['obj_sel_search'] . ', \'' . $_POST['obj_search_txt'] . '\') )');
        }
    }
    if ($_POST['sel_ct_days']) {
        $DB->where('a1.ct_days', $_POST['sel_ct_days']);
    }
    if ($_POST['sel_ct_show']) {
        $DB->where('a1.ct_show', $_POST['sel_ct_show']);
    }
    if ($_POST['sel_ct_use']) {
        $DB->where('a1.ct_use', $_POST['sel_ct_use']);
    }
    if ($_POST['sel_ct_end']) {
        $DB->where('a1.ct_end', $_POST['sel_ct_end']);
    }
    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.ct_idx", "desc");
    } else {
        $DB->orderBy("a1.ct_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("coupon_t a1", $pg);

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
                    가용일
                </th>
                <th class="text-center" style="width:120px;">
                    쿠폰번호
                </th>
                <th class="text-center" style="width:120px;">
                    쿠폰명/비고
                </th>
                <th class="text-center" style="width:120px;">
                    노출여부/사용여부
                </th>
                <th class="text-center" style="width:120px;">
                    만료일시/만료여부
                </th>
                <th class="text-center" style="width:120px;">
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
                            <?= $counts ?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./coupon_form?act=update&ct_idx=<?= $row['ct_idx'] ?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./coupon_update', '<?= $row['ct_idx'] ?>');" />
                        </td>
                        <td data-title="가용일" class="text-center">
                            <?= $arr_ct_days[$row['ct_days']] ?>
                        </td>
                        <td data-title="쿠폰번호" class="text-center">
                            <?= $row['ct_code'] ?>
                        </td>
                        <td data-title="쿠폰명" class="text-center">
                            <?= $row['ct_title'] ?><br>
                            <?= $row['ct_subtitle'] ?>
                        </td>
                        <td data-title="노출여부" class="text-center">
                            <?= $arr_ct_show[$row['ct_show']] ?><br>
                            <?= $arr_ct_use[$row['ct_use']] ?>
                        </td>
                        <td data-title="만료일시" class="text-center">
                            <?= DateType($row['ct_edate'], 6) ?><br>
                            <?= $arr_ct_end[$row['ct_end']] ?>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?= DateType($row['ct_wdate'], 6) ?>
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
    if ($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
} else if ($_POST['act'] == 'input') {
    if ($_POST['ct_title'] == "") {
        p_alert("잘못된 접근입니다. ct_title", 'back');
    }
    if ($_POST['sel_search_sdate'] == "") {
        p_alert("잘못된 접근입니다. sel_search_sdate", 'back');
    }
    if ($_POST['sel_search_edate'] == "") {
        p_alert("잘못된 접근입니다. sel_search_edate", 'back');
    }

    if ($_POST['ct_idx']) {
        $DB->where('ct_idx', $_POST['ct_idx']);
        $row = $DB->getone('coupon_t');
    }

    if ($row['ct_idx']) {
        //p_alert("중복된 정보가 존재합니다.", './coupon_list.php');
        $_POST['act'] = 'update';
    } else {
        unset($arr_query);
        $ct_code = get_coupon_code();
        $arr_query = array(
            "ct_title" => $_POST['ct_title'],
            "ct_subtitle" => $_POST['ct_subtitle'],
            'ct_code' => $ct_code,
            "ct_type1" => 1,
            "ct_sdate" => $_POST['sel_search_sdate'],
            "ct_edate" => $_POST['sel_search_edate'],
            "ct_days" => $_POST['ct_days'],
            "ct_use" => 'N',
            "ct_end" => 'N',
            "ct_show" => $_POST['ct_show'],
            "ct_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('coupon_t', $arr_query);

        p_alert("등록되었습니다.", "./coupon_list");
        exit;
    }
} else if ($_POST['act'] == "delete") {
    $DB->where('ct_idx', $_POST['obj_idx']);
    $DB->delete('coupon_t');

    echo "Y";
}
if ($_POST['act'] == "update") {
    if ($_POST['ct_title'] == "") {
        p_alert("잘못된 접근입니다. ct_title", 'back');
    }
    unset($arr_query);
    $arr_query = array(
        "ct_title" => $_POST['ct_title'],
        "ct_subtitle" => $_POST['ct_subtitle'],
        "ct_sdate" => $_POST['sel_search_sdate'],
        "ct_edate" => $_POST['sel_search_edate'],
        "ct_days" => $_POST['ct_days'],
        "ct_use" => $_POST['ct_use'],
        "ct_show" => $_POST['ct_show'],
    );

    $DB->where('ct_idx', $_POST['ct_idx']);

    $DB->update('coupon_t', $arr_query);
    $_last_idx = $_POST['ct_idx'];

    p_alert("수정되었습니다.");
    exit;
}
if ($_POST['act'] == "coupon_use_list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    //날짜 조건
    if ($_POST['sel_search_sdate'] || $_POST['sel_search_edate']) {
        if ($_POST['sel_search_sdate'] && !$_POST['sel_search_edate']) {
            $DB->where("DATE(clt_wdate)", $_POST['sel_search_sdate'], ">=");
        }
        if (!$_POST['sel_search_sdate'] && $_POST['sel_search_edate']) {
            $DB->where("DATE(clt_wdate)", $_POST['sel_search_edate'], "<=");
        }
        if ($_POST['sel_search_sdate'] && $_POST['sel_search_edate']) {
            $DB->where("DATE(clt_wdate)", array($_POST['sel_search_sdate'], $_POST['sel_search_edate']), "BETWEEN");
        }
    }
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.coupon_code, \'' . $_POST['obj_search_txt'] . '\') or instr(a2.mt_id, \'' . $_POST['obj_search_txt'] . '\') or instr(a2.mt_name, \'' . $_POST['obj_search_txt'] . '\') )');
        } else {
            $DB->Where('( instr(' . $_POST['obj_sel_search'] . ', \'' . $_POST['obj_search_txt'] . '\') )');
        }
    }

    //Join
    $DB->join("member_t a2", "a1.mt_idx=a2.mt_idx", "LEFT");
    $DB->join("coupon_t a3", "a1.coupon_idx=a3.ct_idx", "LEFT");

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.clt_idx", "desc");
    } else {
        $DB->orderBy("a1.clt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("coupon_log_t a1", $pg);

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
                    사용회원
                </th>
                <th class="text-center" style="width:120px;">
                    쿠폰번호
                </th>
                <th class="text-center" style="width:120px;">
                    가용일자
                </th>
                <th class="text-center" style="width:120px;">
                    사용일자
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
                        <td data-title="사용회원" class="text-center">
                            <?= format_phone($row['mt_id']) ?><br>
                            <?= $row['mt_name'] ?>(<?= $row['mt_nickname'] ?>)
                        </td>
                        <td data-title="쿠폰번호" class="text-center">
                            <?= $row['ct_code'] ?>
                        </td>
                        <td data-title="가용일자" class="text-center">
                            <?= $arr_ct_days[$row['ct_days']] ?>
                        </td>
                        <td data-title="사용일자" class="text-center">
                            <?= DateType($row['clt_wdate'], 6) ?>
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
