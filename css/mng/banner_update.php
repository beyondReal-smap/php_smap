<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "input") {
    if ($_POST['bt_type'] == "") {
        p_alert("잘못된 접근입니다. bt_type", 'back');
    }
    if ($_POST['bt_title'] == "") {
        p_alert("잘못된 접근입니다. bt_title", 'back');
    }
    if ($_POST['bt_url'] == "") {
        p_alert("잘못된 접근입니다. bt_url", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "bt_type" => $_POST['bt_type'],
        "bt_title" => $_POST['bt_title'],
        "bt_url" => $_POST['bt_url'],
        "bt_target" => $_POST['bt_target'],
        "bt_rank" => $_POST['bt_rank'],
        "bt_show" => "Y",
        "bt_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('banner_t', $arr_query);

    $c = 1;
    $file_nm_t = 'file_arr' . $c;
    if ($_FILES[$file_nm_t]) {
        foreach ($_FILES[$file_nm_t]['name'] as $key => $val) {
            if ($val) {
                $bt_file = $_FILES[$file_nm_t]['tmp_name'][$key];
                $bt_file_name = $_FILES[$file_nm_t]['name'][$key];
                $bt_file_size = $_FILES[$file_nm_t]['size'][$key];
                $bt_file_type = $_FILES[$file_nm_t]['type'][$key];

                $temp_img_txt = "bt_file";
                $temp_img_on_txt = $temp_img_txt . "_on";
                $temp_img_ori_txt = $temp_img_txt . "_ori";
                $temp_img_size_txt = $temp_img_txt . "_size";

                if (!empty($bt_file_name)) {
                    $_POST[$temp_img_on_txt] = $temp_img_txt . "_" . $_last_idx . "_" . $c . "_" . time() . "." . get_file_ext($bt_file_name);
                    upload_file($bt_file, $_POST[$temp_img_on_txt], $ct_img_dir . "/");
                    $_POST[$temp_img_size_txt] = $bt_file_size;
                    $_POST[$temp_img_ori_txt] = $bt_file_name;

                    if ($arr_bt_type_thumb[$_POST['bt_type']]) {
                        $img_width_t = $arr_bt_type_thumb[$_POST['bt_type']][0];
                        $img_height_t = $arr_bt_type_thumb[$_POST['bt_type']][1];
                        thumnail_width($ct_img_dir . "/" . $_POST[$temp_img_on_txt], $_POST[$temp_img_on_txt], $ct_img_dir . "/", $img_width_t, $img_height_t);
                    }

                    unset($arr_query_img);
                    $arr_query_img[$temp_img_txt] = $_POST[$temp_img_on_txt];
                    $arr_query_img[$temp_img_txt . '_ori'] = $_POST[$temp_img_ori_txt];
                    $arr_query_img[$temp_img_txt . '_size'] = $_POST[$temp_img_size_txt];

                    $DB->where('bt_idx', $_last_idx);

                    $DB->update('banner_t', $arr_query_img);
                }
            }
        }
    }

    echo "Y";
} elseif ($_POST['act'] == "update") {
    if ($_POST['bt_type'] == "") {
        p_alert("잘못된 접근입니다. bt_type", 'back');
    }
    if ($_POST['bt_title'] == "") {
        p_alert("잘못된 접근입니다. bt_title", 'back');
    }
    if ($_POST['bt_url'] == "") {
        p_alert("잘못된 접근입니다. bt_url", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "bt_type" => $_POST['bt_type'],
        "bt_title" => $_POST['bt_title'],
        "bt_url" => $_POST['bt_url'],
        "bt_target" => $_POST['bt_target'],
        "bt_rank" => $_POST['bt_rank'],
        "bt_show" => $_POST['bt_show'],
        "bt_wdate" => $DB->now(),
    );

    $DB->where('bt_idx', $_POST['bt_idx']);

    $DB->update('banner_t', $arr_query);
    $_last_idx = $_POST['bt_idx'];

    $c = 1;
    $file_nm_t = 'file_arr' . $c;
    if ($_FILES[$file_nm_t]) {
        foreach ($_FILES[$file_nm_t]['name'] as $key => $val) {
            if ($val) {
                $bt_file = $_FILES[$file_nm_t]['tmp_name'][$key];
                $bt_file_name = $_FILES[$file_nm_t]['name'][$key];
                $bt_file_size = $_FILES[$file_nm_t]['size'][$key];
                $bt_file_type = $_FILES[$file_nm_t]['type'][$key];

                $temp_img_txt = "bt_file";
                $temp_img_on_txt = $temp_img_txt . "_on";
                $temp_img_ori_txt = $temp_img_txt . "_ori";
                $temp_img_size_txt = $temp_img_txt . "_size";

                if (!empty($bt_file_name)) {
                    $_POST[$temp_img_on_txt] = $temp_img_txt . "_" . $_last_idx . "_" . $c . "_" . time() . "." . get_file_ext($bt_file_name);
                    upload_file($bt_file, $_POST[$temp_img_on_txt], $ct_img_dir . "/");
                    $_POST[$temp_img_size_txt] = $bt_file_size;
                    $_POST[$temp_img_ori_txt] = $bt_file_name;

                    if ($arr_bt_type_thumb[$_POST['bt_type']]) {
                        $img_width_t = $arr_bt_type_thumb[$_POST['bt_type']][0];
                        $img_height_t = $arr_bt_type_thumb[$_POST['bt_type']][1];
                        thumnail_width($ct_img_dir . "/" . $_POST[$temp_img_on_txt], $_POST[$temp_img_on_txt], $ct_img_dir . "/", $img_width_t, $img_height_t);
                    }

                    unset($arr_query_img);
                    $arr_query_img[$temp_img_txt] = $_POST[$temp_img_on_txt];
                    $arr_query_img[$temp_img_txt . '_ori'] = $_POST[$temp_img_ori_txt];
                    $arr_query_img[$temp_img_txt . '_size'] = $_POST[$temp_img_size_txt];

                    $DB->where('bt_idx', $_last_idx);

                    $DB->update('banner_t', $arr_query_img);
                }
            }
        }
    }

    echo "Y";
} elseif ($_POST['act'] == "delete") {
    $DB->where('bt_idx', $_POST['obj_idx']);
    $row = $DB->getone('banner_t');

    if ($row['bt_file']) {
        @unlink($ct_img_dir . "/" . $row['bt_file']);
    }

    $DB->where('bt_idx', $_POST['obj_idx']);
    $DB->delete('banner_t');

    echo "Y";
} elseif ($_POST['act'] == "delete_img") {
    $DB->where('bt_idx', $_POST['bt_idx']);
    $row = $DB->getone('banner_t');

    if ($row['bt_file']) {
        @unlink($ct_img_dir . "/" . $row['bt_file']);
    }

    unset($arr_query);
    $arr_query = array(
        "bt_file" => '',
        "bt_file_ori" => '',
        "bt_file_size" => '',
    );

    $DB->where('bt_idx', $_POST['bt_idx']);

    $DB->update('banner_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.bt_title, \'' . $_POST['obj_search_txt'] . '\') )');
        } else {
            $DB->Where('( instr(' . $_POST['obj_sel_search'] . ', \'' . $_POST['obj_search_txt'] . '\') )');
        }
    }

    if ($_POST['sel_bt_type']) {
        $DB->where('a1.bt_type', $_POST['sel_bt_type']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.bt_idx", "desc");
    } else {
        $DB->orderBy("a1.bt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("banner_t a1", $pg);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);
    ?>
    <table class="table inx-table inx-table-card">
        <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:80px;">
                    번호
                </th>
                <th class="text-center" style="width:140px;">
                    관리
                </th>
                <th class="text-center" style="width:80px;">
                    구분
                </th>
                <th class="text-center">
                    이미지
                </th>
                <th class="text-center">
                    내용
                </th>
                <th class="text-center" style="width:100px;">
                    노출순서 / 노출여부
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
                            <?= $counts ?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./banner_form?act=update&bt_idx=<?= $row['bt_idx'] ?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./banner_update', '<?= $row['bt_idx'] ?>');" />
                        </td>
                        <td data-title="구분" class="text-center">
                            <?= $arr_bt_type[$row['bt_type']] ?>
                        </td>
                        <td data-title="이미지" class="text-center">
                            <img src='../img/uploads/<?= $row['bt_file'] ?>' onerror="this.src='<?= $ct_no_img_url ?>'" style="border-radius:0%; width:100%;height:100px">
                        </td>
                        <td data-title="제목">
                            <span class="line1_text"><?= $row['bt_title'] ?></span>
                        </td>
                        <td data-title="노출순서/노출여부" class="text-center">
                            <?= $row['bt_rank'] ?><br>
                            <?= $row['bt_show'] ?>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?= DateType($row['bt_wdate'], 6) ?>
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
    if ($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
