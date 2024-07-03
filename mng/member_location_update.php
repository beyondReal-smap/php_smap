<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "input") {
    if ($_POST['rlt_cate'] == "") {
        p_alert("잘못된 접근입니다. rlt_cate", 'back');
    }
    if ($_POST['rlt_title'] == "") {
        p_alert("잘못된 접근입니다. rlt_title", 'back');
    }
    if ($_POST['rlt_add1'] == "") {
        p_alert("잘못된 접근입니다. rlt_add1", 'back');
    }
    if ($_POST['rlt_add2'] == "") {
        p_alert("잘못된 접근입니다. rlt_add2", 'back');
    }
    if ($_POST['rlt_tel1'] == "") {
        p_alert("잘못된 접근입니다. rlt_tel1", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "rlt_cate" => $_POST['rlt_cate'],
        "rlt_title" => $_POST['rlt_title'],
        "rlt_student" => $_POST['rlt_student'],
        "rlt_psnby_thcc_cntnt" => $_POST['rlt_psnby_thcc_cntnt'],
        "rlt_url" => $_POST['rlt_url'],
        "rlt_zip" => $_POST['rlt_zip'],
        "rlt_add1" => $_POST['rlt_add1'],
        "rlt_add2" => $_POST['rlt_add2'],
        "rlt_tel1" => $_POST['rlt_tel1'],
        "rlt_tel2" => $_POST['rlt_tel2'],
        "rlt_udate" => $_POST['rlt_udate'],
        "rlt_lat" => $_POST['rlt_lat'],
        "rlt_long" => $_POST['rlt_long'],
        "rlt_show" => "Y",
        "rlt_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('recomand_location_t', $arr_query);

    p_alert("등록되었습니다.", "./recomand_location_list");
} elseif ($_POST['act'] == "update") {
    if ($_POST['rlt_cate'] == "") {
        p_alert("잘못된 접근입니다. rlt_cate", 'back');
    }
    if ($_POST['rlt_title'] == "") {
        p_alert("잘못된 접근입니다. rlt_title", 'back');
    }
    if ($_POST['rlt_add1'] == "") {
        p_alert("잘못된 접근입니다. rlt_add1", 'back');
    }
    if ($_POST['rlt_add2'] == "") {
        p_alert("잘못된 접근입니다. rlt_add2", 'back');
    }
    if ($_POST['rlt_tel1'] == "") {
        p_alert("잘못된 접근입니다. rlt_tel1", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "rlt_cate" => $_POST['rlt_cate'],
        "rlt_title" => $_POST['rlt_title'],
        "rlt_student" => $_POST['rlt_student'],
        "rlt_psnby_thcc_cntnt" => $_POST['rlt_psnby_thcc_cntnt'],
        "rlt_url" => $_POST['rlt_url'],
        "rlt_zip" => $_POST['rlt_zip'],
        "rlt_add1" => $_POST['rlt_add1'],
        "rlt_add2" => $_POST['rlt_add2'],
        "rlt_tel1" => $_POST['rlt_tel1'],
        "rlt_tel2" => $_POST['rlt_tel2'],
        "rlt_udate" => $_POST['rlt_udate'],
        "rlt_lat" => $_POST['rlt_lat'],
        "rlt_long" => $_POST['rlt_long'],
        "rlt_wdate" => $DB->now(),
    );

    $DB->where('rlt_idx', $_POST['rlt_idx']);

    $DB->update('recomand_location_t', $arr_query);
    $_last_idx = $_POST['rlt_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    unset($arr_query);
    $arr_query = array(
        "rlt_show" => 'N',
    );

    $DB->where('rlt_idx', $_POST['obj_idx']);

    $DB->update('recomand_location_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.slt_show', 'Y');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.slt_title, \'' . $_POST['obj_search_txt'] . '\') or instr(a1.slt_add, \'' . $_POST['obj_search_txt'] . '\') )');
        } else {
            $DB->Where('( instr(' . $_POST['obj_sel_search'] . ', \'' . $_POST['obj_search_txt'] . '\') )');
        }
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.slt_idx", "desc");
    } else {
        $DB->orderBy("a1.slt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("smap_location_t a1", $pg);

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
                    그룹명 / 그룹원
                </th>
                <th class="text-center" style="width:600px;">
                    장소명 / 주소
                </th>

                <th class="text-center">
                    등록일시 / 등록 그룹원
                </th>
                <th class="text-center">
                    위치알림 여부
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    $DB->where('sgdt_idx', $row['sgdt_idx']);
                    $sgdt_row = $DB->getone('smap_group_detail_t');

                    $DB->where('mt_idx', $row['mt_idx']);
                    $sgdt_mem_row = $DB->getone('member_t');

                    $DB->where('sgt_idx', $sgdt_row['sgt_idx']);
                    $sgt_row = $DB->getone('smap_group_t');

                    $DB->where('mt_idx', $row['insert_mt_idx']);
                    $insert_mem_row = $DB->getone('member_t');

            ?>
                    <tr>
                        <td data-title="번호" class="text-center">
                            <?= $counts ?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-primary btn-sm" value="상세" onclick="location.href='./member_location_form?act=update&slt_idx=<?= $row['slt_idx'] ?>'" />
                            <!-- <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./recomand_location_update', '<?= $row['slt_idx'] ?>');" /> -->
                        </td>
                        <td data-title="그룹명 / 그룹원" class="text-center">
                            <?= $sgt_row['sgt_title'] ?><br>
                            <?= $sgdt_mem_row['mt_name'] ?><?= $sgdt_mem_row['mt_nickname'] ? '(' . $sgdt_mem_row['mt_nickname'] . ')' : '' ?>
                        </td>
                        <td data-title="장소명 / 주소" class="text-center">
                            <span class="line1_text"><?= $row['slt_title'] ?></span>
                            <span class="line1_text"><?= $row['slt_add'] ?></span>
                        </td>
                        <td data-title="등록일시 / 등록 그룹원" class="text-center">
                            <?= DateType($row['slt_wdate'], 6) ?><br>
                            <?= $insert_mem_row['mt_name'] ?><?= $insert_mem_row['mt_nickname'] ? '(' . $insert_mem_row['mt_nickname'] . ')' : '' ?>
                        </td>
                        <td data-title="위치알림 여부" class="text-center">
                            <?= $arr_slt_enter_chk[$row['slt_enter_alarm']] ?>
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
    if ($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
} elseif ($_POST['act'] == "excel_upload") {
    $file_nm_t = 'excel_file';
    $xls_file = $_FILES[$file_nm_t]['tmp_name'];
    $xls_file_name = $_FILES[$file_nm_t]['name'];
    $xls_file_size = $_FILES[$file_nm_t]['size'];

    $rtn_data = array();

    if ($xls_file_name) {
        $xls_file_type = get_file_ext($xls_file_name);

        if ($xls_file_type == 'xls') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } elseif ($file_type == 'xlsx') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else {
            echo '처리할 수 있는 엑셀 파일이 아닙니다';
            exit;
        }

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($xls_file);

        $spreadsheet_data1 = $spreadsheet->getSheet(0)->toArray(null, true, true, true);

        unset($excel_data_arr);

        if (!empty($spreadsheet_data1)) {
            $spreadsheet_data1_cnt = count($spreadsheet_data1);
            $q = 0;
            for ($i = 2; $i <= $spreadsheet_data1_cnt; $i++) {
                if ($spreadsheet_data1[$i]['A']) {
                    $excel_data_arr[$q]['rlt_title'] = addslashes($spreadsheet_data1[$i]['A']); //학원명
                    $excel_data_arr[$q]['rlt_student'] = addslashes($spreadsheet_data1[$i]['B']); //수용능력인원
                    $excel_data_arr[$q]['rlt_cate'] = addslashes($spreadsheet_data1[$i]['C']); //분야명
                    $excel_data_arr[$q]['rlt_psnby_thcc_cntnt'] = addslashes($spreadsheet_data1[$i]['D']); //인당수강료내용
                    $excel_data_arr[$q]['rlt_add1'] = addslashes($spreadsheet_data1[$i]['E']); //도로명주소
                    $excel_data_arr[$q]['rlt_add2'] = addslashes($spreadsheet_data1[$i]['F']); //도로명상세주소
                    $excel_data_arr[$q]['rlt_tel1'] = addslashes($spreadsheet_data1[$i]['G']); //전화번호
                    $excel_data_arr[$q]['rlt_udate'] = addslashes($spreadsheet_data1[$i]['H']); //수정일자
                    $excel_data_arr[$q]['rlt_lat'] = addslashes($spreadsheet_data1[$i]['I']); //위도
                    $excel_data_arr[$q]['rlt_long'] = addslashes($spreadsheet_data1[$i]['J']); //경도
                    $q++;
                }
            }
        }

        // printr($excel_data_arr);
        // exit;

        $tt_cnt1 = 0; //총
        $tt_cnt2 = 0; //등록

        unset($err_msg);
        if ($excel_data_arr) {
            foreach ($excel_data_arr as $key => $val) {
                if ($val['rlt_title']) {
                    $val['rlt_tel1'] = str_replace('-', '', $val['rlt_tel1']);

                    unset($arr_query);
                    $arr_query = array(
                        "rlt_cate" => $val['rlt_cate'],
                        "rlt_title" => $val['rlt_title'],
                        "rlt_student" => $val['rlt_student'],
                        "rlt_psnby_thcc_cntnt" => $val['rlt_psnby_thcc_cntnt'],
                        "rlt_add1" => $val['rlt_add1'],
                        "rlt_add2" => $val['rlt_add2'],
                        "rlt_tel1" => $val['rlt_tel1'],
                        "rlt_udate" => $val['rlt_udate'],
                        "rlt_lat" => $val['rlt_lat'],
                        "rlt_long" => $val['rlt_long'],
                        "rlt_show" => "Y",
                        "rlt_wdate" => $DB->now(),
                    );

                    $_last_idx = $DB->insert('recomand_location_t', $arr_query);
                    $tt_cnt2++;
                }
                $tt_cnt1++;
            }
        }

        if ($err_msg) {
            $err_msg_t = '<ul class="list-group mt-2">';
            foreach ($err_msg as $key => $val) {
                if ($val) {
                    $err_msg_t .= '<li class="list-group-item disabled text-danger" aria-disabled="true">' . $val . '</li>';
                }
            }
            $err_msg_t .= '</ul>';
        } else {
            $err_msg_t = '';
        }

        $rtn_data['result'] = 'Y';
        $rtn_data['data'] = array(
            'tt_cnt1' => $tt_cnt1,
            'tt_cnt2' => $tt_cnt2,
            'err_msg' => $err_msg_t
        ); //총, 등록, 미등록(중복)
    } else {
        $rtn_data['result'] = 'N';
        $rtn_data['data'] = '';
    }

    echo json_encode($rtn_data);
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
