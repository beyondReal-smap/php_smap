<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['pot_title'] == "") {
        p_alert("잘못된 접근입니다. pot_title", 'back');
    }
    if($_POST['pot_sdate'] == "") {
        p_alert("잘못된 접근입니다. pot_sdate", 'back');
    }
    if($_POST['pot_edate'] == "") {
        p_alert("잘못된 접근입니다. pot_edate", 'back');
    }
    if($_POST['pot_url'] == "") {
        p_alert("잘못된 접근입니다. pot_url", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "pot_title" => $_POST['pot_title'],
        "pot_sdate" => $_POST['pot_sdate'],
        "pot_edate" => $_POST['pot_edate'],
        "pot_url" => $_POST['pot_url'],
        "pot_target" => $_POST['pot_target'],
        "pot_close" => $_POST['pot_close'],
        "pot_show" => 'Y',
        "pot_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('popup_t', $arr_query);

    $c = 1;
    $file_nm_t = 'file_arr'.$c;
    if($_FILES[$file_nm_t]) {
        foreach($_FILES[$file_nm_t]['name'] as $key => $val) {
            if($val) {
                $bt_file = $_FILES[$file_nm_t]['tmp_name'][$key];
                $bt_file_name = $_FILES[$file_nm_t]['name'][$key];
                $bt_file_size = $_FILES[$file_nm_t]['size'][$key];
                $bt_file_type = $_FILES[$file_nm_t]['type'][$key];

                $temp_img_txt = "pot_file";
                $temp_img_on_txt = $temp_img_txt."_on";
                $temp_img_ori_txt = $temp_img_txt."_ori";
                $temp_img_size_txt = $temp_img_txt."_size";

                if (!empty($bt_file_name)) {
                    $_POST[$temp_img_on_txt] = $temp_img_txt."_".$_last_idx."_".$c."_".time().".".get_file_ext($bt_file_name);
                    upload_file($bt_file, $_POST[$temp_img_on_txt], $ct_img_dir."/");
                    $_POST[$temp_img_size_txt] = $bt_file_size;
                    $_POST[$temp_img_ori_txt] = $bt_file_name;

                    // if($arr_bt_type_thumb[$_POST['bt_type']]) {
                    //     $img_width_t = $arr_bt_type_thumb[$_POST['bt_type']][0];
                    //     $img_height_t = $arr_bt_type_thumb[$_POST['bt_type']][1];
                    //     thumnail_width($ct_img_dir."/".$_POST[$temp_img_on_txt], $_POST[$temp_img_on_txt], $ct_img_dir."/", $img_width_t, $img_height_t);
                    // }

                    unset($arr_query_img);
                    $arr_query_img[$temp_img_txt] = $_POST[$temp_img_on_txt];
                    $arr_query_img[$temp_img_txt.'_ori'] = $_POST[$temp_img_ori_txt];
                    $arr_query_img[$temp_img_txt.'_size'] = $_POST[$temp_img_size_txt];

                    $DB->where('pot_idx', $_last_idx);

                    $DB->update('popup_t', $arr_query_img);
                }
            }
        }
    }

    echo "Y";
} elseif ($_POST['act'] == "update") {
    if($_POST['pot_title'] == "") {
        p_alert("잘못된 접근입니다. pot_title", 'back');
    }
    if($_POST['pot_sdate'] == "") {
        p_alert("잘못된 접근입니다. pot_sdate", 'back');
    }
    if($_POST['pot_edate'] == "") {
        p_alert("잘못된 접근입니다. pot_edate", 'back');
    }
    if($_POST['pot_url'] == "") {
        p_alert("잘못된 접근입니다. pot_url", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "pot_title" => $_POST['pot_title'],
        "pot_sdate" => $_POST['pot_sdate'],
        "pot_edate" => $_POST['pot_edate'],
        "pot_url" => $_POST['pot_url'],
        "pot_target" => $_POST['pot_target'],
        "pot_close" => $_POST['pot_close'],
        "pot_show" => 'Y',
        "pot_wdate" => $DB->now(),
    );

    $DB->where('pot_idx', $_POST['pot_idx']);

    $DB->update('popup_t', $arr_query);
    $_last_idx = $_POST['pot_idx'];

    $c = 1;
    $file_nm_t = 'file_arr'.$c;
    if($_FILES[$file_nm_t]) {
        foreach($_FILES[$file_nm_t]['name'] as $key => $val) {
            if($val) {
                $bt_file = $_FILES[$file_nm_t]['tmp_name'][$key];
                $bt_file_name = $_FILES[$file_nm_t]['name'][$key];
                $bt_file_size = $_FILES[$file_nm_t]['size'][$key];
                $bt_file_type = $_FILES[$file_nm_t]['type'][$key];

                $temp_img_txt = "pot_file";
                $temp_img_on_txt = $temp_img_txt."_on";
                $temp_img_ori_txt = $temp_img_txt."_ori";
                $temp_img_size_txt = $temp_img_txt."_size";

                if (!empty($bt_file_name)) {
                    $_POST[$temp_img_on_txt] = $temp_img_txt."_".$_last_idx."_".$c."_".time().".".get_file_ext($bt_file_name);
                    upload_file($bt_file, $_POST[$temp_img_on_txt], $ct_img_dir."/");
                    $_POST[$temp_img_size_txt] = $bt_file_size;
                    $_POST[$temp_img_ori_txt] = $bt_file_name;

                    // if($arr_bt_type_thumb[$_POST['bt_type']]) {
                    //     $img_width_t = $arr_bt_type_thumb[$_POST['bt_type']][0];
                    //     $img_height_t = $arr_bt_type_thumb[$_POST['bt_type']][1];
                    //     thumnail_width($ct_img_dir."/".$_POST[$temp_img_on_txt], $_POST[$temp_img_on_txt], $ct_img_dir."/", $img_width_t, $img_height_t);
                    // }

                    unset($arr_query_img);
                    $arr_query_img[$temp_img_txt] = $_POST[$temp_img_on_txt];
                    $arr_query_img[$temp_img_txt.'_ori'] = $_POST[$temp_img_ori_txt];
                    $arr_query_img[$temp_img_txt.'_size'] = $_POST[$temp_img_size_txt];

                    $DB->where('pot_idx', $_last_idx);

                    $DB->update('popup_t', $arr_query_img);
                }
            }
        }
    }

    echo "Y";
} elseif ($_POST['act'] == "delete") {
    $DB->where('pot_idx', $_POST['obj_idx']);
    $row = $DB->getone('popup_t');

    if($row['pot_file']) {
        @unlink($ct_img_dir."/".$row['pot_file']);
    }

    $DB->where('pot_idx', $_POST['obj_idx']);
    $DB->delete('popup_t');

    echo "Y";
} elseif ($_POST['act'] == "delete_img") {
    $DB->where('pot_idx', $_POST['pot_idx']);
    $row = $DB->getone('popup_t');

    if($row['pot_file']) {
        @unlink($ct_img_dir."/".$row['pot_file']);
    }

    unset($arr_query);
    $arr_query = array(
        "pot_file" => '',
        "pot_file_ori" => '',
        "pot_file_size" => '',
    );

    $DB->where('pot_idx', $_POST['pot_idx']);

    $DB->update('popup_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.pot_title, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.pot_idx", "desc");
    } else {
        $DB->orderBy("a1.pot_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("popup_t a1", $pg);

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
            <th class="text-center">
                노출기간
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
                <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./popup_form?act=update&pot_idx=<?=$row['pot_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./popup_update', '<?=$row['pot_idx']?>');" />
            </td>
            <td data-title="제목">
                <span class="line1_text"><?=$row['pot_title']?></span>
            </td>
            <td data-title="노출기간" class="text-center">
                <?=DateType($row['pot_sdate'], 6)?> ~ <?=DateType($row['pot_edate'], 6)?>
            </td>
            <td data-title="노출여부" class="text-center">
                <?=$row['pot_show']?>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['pot_wdate'], 6)?>
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
