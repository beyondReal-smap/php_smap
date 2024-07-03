<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['mt_id'] == "") {
        p_alert("잘못된 접근입니다. mt_id", 'back');
    }
    if($_POST['mt_pwd'] == "") {
        p_alert("잘못된 접근입니다. mt_pwd", 'back');
    }
    if($_POST['mt_pwd_re'] == "") {
        p_alert("잘못된 접근입니다. mt_pwd_re", 'back');
    }
    if($_POST['mt_name'] == "") {
        p_alert("잘못된 접근입니다. mt_name", 'back');
    }
    if($_POST['mt_pwd'] != $_POST['mt_pwd_re']) {
        p_alert("비밀번호가 동일하지 않습니다. 확인바랍니다.", './member_admin_form');
    }

    $DB->where('mt_id', $_POST['mt_id']);
    $row = $DB->getone('member_t');

    if($row['mt_idx']) {
        p_alert("중복된 아이디가 존재합니다.");
    } else {
        unset($arr_query);
        $arr_query = array(
            "mt_type" => '1',
            "mt_id" => $_POST['mt_id'],
            'mt_pwd' => password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT),
            "mt_name" => $_POST['mt_name'],
            "mt_hp" => $_POST['mt_hp'],
            "mt_level" => 9,
            "mt_status" => $_POST['mt_status'],
            "mt_show" => 'Y',
            "mt_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('member_t', $arr_query);

        p_alert("등록되었습니다.", "./member_admin_list");
    }
} elseif ($_POST['act'] == "update") {
    if($_POST['mt_name'] == "") {
        p_alert("잘못된 접근입니다. mt_name", 'back');
    }
    if($_POST['mt_pwd'] != "" && $_POST['mt_pwd_re'] != "") {
        if($_POST['mt_pwd'] != $_POST['mt_pwd_re']) {
            p_alert("비밀번호가 동일하지 않습니다. 확인바랍니다.", './');
            exit;
        }
    }

    unset($arr_query);
    $arr_query = array(
        "mt_name" => $_POST['mt_name'],
        "mt_status" => $_POST['mt_status'],
    );

    if($_POST['mt_pwd'] && $_POST['mt_pwd_re']) {
        if($_POST['mt_pwd'] == $_POST['mt_pwd_re']) {
            $arr_query['mt_pwd'] = password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT);
        }
    }

    $DB->where('mt_idx', $_POST['mt_idx']);

    $DB->update('member_t', $arr_query);
    $_last_idx = $_POST['mt_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    unset($arr_query);
    $arr_query = array(
        "mt_show" => 'N',
    );

    $DB->where('mt_idx', $_POST['obj_idx']);

    $DB->update('member_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "status_chg") {
    unset($arr_query);
    $arr_query = array(
        $_POST['mt_obj'] => $_POST['mt_val'],
    );

    $DB->where('mt_idx', $_POST['mt_idx']);

    $DB->update('member_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "mt_id_chk") {
    $DB->where('mt_id', $_POST['mt_id']);
    $row = $DB->getone('member_t');

    if($row['mt_idx']) {
        echo "N";
    } else {
        echo "Y";
    }
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.mt_show', 'Y');
    $DB->where('a1.mt_level', '9');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\') )');
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
                로그인
            </th>
            <th class="text-center">
                아이디
            </th>
            <th class="text-center">
                이름
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
                <input type="button" class="btn btn-outline-primary btn-sm" value="상세" onclick="location.href='./member_admin_form?act=update&mt_idx=<?=$row['mt_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del_admin_member('./member_admin_update', '<?=$row['mt_idx']?>');" />
            </td>
            <td data-title="로그인" class="text-center">
                <?=$arr_mt_type[$row['mt_type']]?>
            </td>
            <td data-title="아이디" class="text-center">
                <?=$row['mt_id']?>
            </td>
            <td data-title="이름" class="text-center">
                <?=$row['mt_name']?>
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
