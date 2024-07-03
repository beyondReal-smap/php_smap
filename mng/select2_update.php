<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "member_t") {
    unset($list);
    $DB->where('a1.mt_level', '2');
    $DB->where('a1.mt_status', '1');
    $DB->where('a1.mt_show', 'Y');
    $DB->Where('( instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\') )');
    $list = $DB->get('member_t a1');

    $rtn_data = array();

    if($list) {
        foreach($list as $row) {
            if($row['mt_idx']) {
                $rtn_data[] = array(
                    'id' => $row['mt_idx'],
                    'text' => $row['mt_name']." (".$row['mt_id'].")",
                );
            }
        }
    }

    echo json_encode($rtn_data, JSON_UNESCAPED_UNICODE);
} elseif ($_POST['act'] == "group_t") {
    unset($list);
    $DB->where('instr(a1.sgt_title, \''.$_POST['obj_search_txt'].'\')');
    $DB->where('a1.sgt_show', 'Y');
    $DB->orderBy("a1.sgt_idx", "desc");

    $list = $DB->get('smap_group_t a1');

    if($list) {
        foreach($list as $row) {
            if($row['sgt_idx']) {
                $rtn_data[] = array(
                    'id' => $row['sgt_idx'],
                    'text' => $row['sgt_title']." (".$row['sgt_code'].")",
                );
            }
        }
    }

    echo json_encode($rtn_data, JSON_UNESCAPED_UNICODE);
} elseif ($_POST['act'] == "smap_location_t") {
    unset($list);
    $DB->where('a1.mt_idx', $_POST['mt_idx']);
    $DB->where('a1.slt_show', 'Y');
    $DB->Where('( instr(a1.slt_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.slt_add, \''.$_POST['obj_search_txt'].'\') )');
    $list = $DB->get('smap_location_t a1');

    $rtn_data = array();

    if($list) {
        foreach($list as $row) {
            if($row['slt_idx']) {
                $rtn_data[] = array(
                    'id' => $row['slt_idx'],
                    'text' => $row['slt_title']." (".$row['slt_add'].")",
                    'slt_add' => $row['slt_add'],
                );
            }
        }
    }

    echo json_encode($rtn_data, JSON_UNESCAPED_UNICODE);
} elseif ($_POST['act'] == "find_group") {
    unset($list);
    $DB->where('a1.mt_idx', $_POST['mt_idx']);
    $DB->where('a1.sgdt_show', 'Y');
    $DB->orderBy("a1.sgt_idx", "desc");

    $list = $DB->get('smap_group_detail_t a1');

    if($list) {
        foreach($list as $row) {
            if($row['sgt_idx']) {
                $DB->where('sgt_idx', $row['sgt_idx']);
                $row_sg = $DB->getone('smap_group_t');

                echo "<option value=\"".$row_sg['sgt_idx']."\">".$row_sg['sgt_title']."</option>";
            }
        }
    }
} elseif ($_POST['act'] == "find_group_detail") {
    unset($list);
    $DB->where('a1.sgt_idx', $_POST['sgt_idx']);
    $DB->where('a1.sgdt_discharge', 'N');
    $DB->where('a1.sgdt_exit', 'N');
    $DB->orderBy("a1.sgdt_owner_chk", "asc");
    $DB->orderBy("a1.sgdt_leader_chk", "asc");
    $list = $DB->get('smap_group_detail_t a1');

    if($list) {
        foreach($list as $row) {
            if($row['sgdt_idx']) {
                $DB->where('mt_idx', $row['mt_idx']);
                $row_mt = $DB->getone('member_t');

                echo "<option value=\"".$row['sgdt_idx']."\">".$row_mt['mt_name']." (".$row_mt['mt_id'].")</option>";
            }
        }
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
