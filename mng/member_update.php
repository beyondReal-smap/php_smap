<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "update") {
    if ($_POST['mt_name'] == "") {
        p_alert("잘못된 접근입니다. mt_name", 'back');
    }
    if ($_POST['mt_nickname'] == "") {
        p_alert("잘못된 접근입니다. mt_nickname", 'back');
    }
    if ($_POST['mt_nickname_chk'] != "Y") {
        p_alert("잘못된 접근입니다. mt_nickname_chk", 'back');
    }
    if ($_POST['mt_name'] == "" && $_POST['mt_pwd_re'] == "") {
        if ($_POST['mt_pwd'] != $_POST['mt_pwd_re']) {
            p_alert("비밀번호가 동일하지 않습니다. 확인바랍니다.", './');
            exit;
        }
    }

    unset($arr_query);
    $arr_query = array(
        "mt_name" => $_POST['mt_name'],
        "mt_nickname" => $_POST['mt_nickname'],
        "mt_email" => $_POST['mt_email'],
        "mt_status" => $_POST['mt_status'],
        "mt_birth" => $_POST['mt_birth'],
        "mt_gender" => $_POST['mt_gender'],
        "mt_level" => $_POST['mt_level'],
        "mt_plan_date" => $_POST['mt_plan_date'],
    );

    if ($_POST['mt_pwd'] && $_POST['mt_pwd_re']) {
        if ($_POST['mt_pwd'] == $_POST['mt_pwd_re']) {
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
} elseif ($_POST['act'] == "retire") {
    $DB->where('mt_idx', $_POST['mt_idx_t']);
    $row = $DB->getone('member_t');

    unset($arr_query);
    $arr_query = array(
        "mt_id" => '',
        "mt_hp" => '',
        "mt_id_retire" => $row['mt_id'],
        "mt_token_id" => '',
        "mt_show" => 'N',
        "mt_status" => '2',
        "mt_level" => '1',
        "mt_retire_level" => $row['mt_level'],
        "mt_retire_chk" => '4',
        "mt_retire_etc" => '관리자 탈퇴',
    );

    $DB->where('mt_idx', $_POST['mt_idx_t']);

    $DB->update('member_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "mt_nickname_chk") {
    $DB->where('mt_nickname', $_POST['mt_nickname']);
    $row = $DB->getone('member_t');

    if ($row['mt_idx']) {
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
    $DB->where('(a1.mt_level >= 2 and a1.mt_level < 9)');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.mt_id, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.mt_name, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.mt_nickname, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.mt_hp, \'' . $_POST['obj_search_txt'] . '\') )');
        } else {
            $DB->Where('( instr(' . $_POST['obj_sel_search'] . ', \'' . $_POST['obj_search_txt'] . '\') )');
        }
    }

    //로그인 구분
    if ($_POST['obj_sel_mt_type'] != 'all') {
        $DB->where('a1.mt_type', $_POST['obj_sel_mt_type']);
    }

    //회원상태
    if ($_POST['obj_sel_mt_status'] != '') {
        $DB->where('a1.mt_status', $_POST['obj_sel_mt_status']);
    }

    //회원구분
    if ($_POST['obj_sel_mt_level'] != '') {
        $DB->where('a1.mt_level', $_POST['obj_sel_mt_level']);
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
                <!-- <th class="text-center">
                로그인
            </th> -->
                <th class="text-center">
                    아이디(전화번호)
                </th>
                <th class="text-center">
                    이름(닉네임)
                </th>
                <th class="text-center">
                    이메일
                </th>
                <th class="text-center">
                    상태
                </th>
                <th class="text-center">
                    등급
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
                            <?= $counts ?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-primary btn-sm" value="상세" onclick="location.href='./member_form?act=update&mt_idx=<?= $row['mt_idx'] ?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="탈퇴" onclick="f_retire_mem('<?= $row['mt_idx'] ?>');" />
                        </td>
                        <!-- <td data-title="로그인" class="text-center">
                <?= $arr_mt_type[$row['mt_type']] ?>
            </td> -->
                        <td data-title="아이디(전화번호)" class="text-center">
                            <?= format_phone($row['mt_id']) ?>
                        </td>
                        <td data-title="이름(닉네임)" class="text-center">
                            <?= $row['mt_name'] ?> <?= $row['mt_nickname'] ? '(' . $row['mt_nickname'] .')' : '' ?>
                        </td>

                        <td data-title="이메일" class="text-center">
                            <?= $row['mt_email'] ?>
                        </td>
                        <td data-title="상태" class="text-center">
                            <?= $arr_mt_status[$row['mt_status']] ?>
                        </td>
                        <td data-title="등급" class="text-center">
                            <?= $arr_mt_level[$row['mt_level']] ?><br>
                            <? if ($row['mt_plan_date'] && $row['mt_level'] == 5) echo '~' . DateType($row['mt_plan_date'], 6); ?>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?= DateType($row['mt_wdate'], 6) ?>
                        </td>

                        <td data-title="접속일시" class="text-center">
                            <?= DateType($row['mt_ldate'], 6) ?>
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
    if ($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
