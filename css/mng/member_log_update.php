<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "list1") {
    unset($list);
    $DB->pageLimit = $gp_n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.sgt_show', 'Y');

    //검색
    if ($_POST['obj_search_txt']) {
        $DB->Where('( instr(a1.sgt_title, \''.$_POST['obj_search_txt'].'\') )');
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.sgt_idx", "desc");
    } else {
        $DB->orderBy("a1.sgt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("smap_group_t a1", $pg);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);
    ?>
<table class="table inx-table inx-table-card">
    <thead class="thead-dark">
        <tr>
            <th class="text-center">
                그룹명
            </th>
            <th class="text-center">
                그룹원
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
    if ($list) {
        foreach ($list as $row) {
            unset($list_gp);
            $DB->where('sgt_idx', $row['sgt_idx']);
            // $DB->where('sgdt_owner_chk', 'N');
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
            $DB->where('sgdt_show', 'Y');
            $DB->orderby('sgdt_idx', 'desc');
            $list_gp = $DB->get('smap_group_detail_t');

            unset($arr_gp);
            $arr_gp = array();
            if($list_gp) {
                $q = 1;
                $mp_cnt = 0;
                $gp_leader = '-';
                foreach($list_gp as $row_gp) {
                    if($q < 6) {
                        $arr_gp[] = $row_gp['mt_idx'];
                    } else {
                        $mp_cnt++;
                    }
                    if($row_gp['sgdt_leader_chk'] == 'Y') {
                        $DB->where('mt_idx', $row_gp['mt_idx']);
                        $row_mt4 = $DB->getone('member_t');

                        $gp_leader = $row_mt4['mt_name']." (".$row_mt4['mt_id'].")";
                    }
                    $q++;
                }
            }
            ?>
        <tr onclick="f_member_location_list_group('<?=$row['sgt_idx']?>');" style="cursor: pointer;">
            <td data-title="그룹명">
                <?=$row['sgt_title']?>
            </td>
            <td data-title="그룹원">
                <div class="avatars">
                    <?php
                        if($mp_cnt) {
                            ?>
                    <span class="avatar-text">
                        <?=number_format($mp_cnt)?>+
                    </span>
                    <?php
                        }
                        if($arr_gp) {
                            foreach($arr_gp as $key => $val) {
                                $DB->where('mt_idx', $val);
                                $row_mt3 = $DB->getone('member_t');
                                ?>
                    <span class="avatar">
                        <a data-toggle="tooltip" data-placement="top" title="<?=$row_mt3['mt_name']?> (<?=$row_mt3['mt_id']?>)"><img src="../img/uploads/<?= $row_mt3['mt_file1']?>" onerror="this.src='<?=$ct_no_profile_img_url?>'" width="25" height="25" /></a>
                    </span>
                    <?php
                            }
                        }
            ?>
                </div>
            </td>
        </tr>
        <?php
            $counts--;
        }
    } else {
        ?>
        <tr>
            <td colspan="2" class="text-center"><b>자료가 없습니다.</b></td>
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
} elseif ($_POST['act'] == "group_detail") {
    if($_POST['sgt_idx'] == "") {
        p_alert("잘못된 접근입니다. sgt_idx", 'back');
    }
    if($_POST['sel_search_sdate']=='') {
        $_POST['sel_search_sdate'] = date('Y-m-d');
    }

    unset($list);

    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $arr_sst_mt_idx_im = implode(',', $arr_sst_mt_idx);
    $DB->where('mt_idx in (' . $arr_sst_mt_idx_im . ')');
    $DB->orderBy("sgdt_owner_chk", "asc");
    $DB->orderBy("sgdt_leader_chk", "asc");
    $list = $DB->get('smap_group_detail_t');

    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $row_gp = $DB->getone('smap_group_t');
?>
<h4 class="card-title">위치정보 - <?=$row_gp['sgt_title']?> 그룹 멤버 <?=$_POST['sel_search_sdate']?></h4>

<div class="row no-gutters mb-2">
    <div class="col-xl-12">
        <div class="float-right form-inline">
            <div class="form-group mx-1">
                <input type="text" name="sel_search_sdate" id="sel_search_sdate" value="<?=$_POST['sel_search_sdate']?>" class="form-control form-control-sm" readonly />
            </div>

            <div class="form-group mx-1">
                <input type="button" class="btn btn-info" value="검색" onclick="f_member_location_list_group('<?=$_POST['sgt_idx']?>');" />
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    jQuery('#sel_search_sdate').datetimepicker({
        format: 'Y-m-d',
        timepicker: false
    });
});
</script>

<table class="table inx-table inx-table-card">
    <thead class="thead-dark">
        <tr>
            <th class="text-center">
                멤버정보
            </th>
            <th class="text-center">
                위치정보
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
    if ($list) {
        foreach ($list as $row) {
            $DB->where('mt_idx', $row['mt_idx']);
            $row_mt = $DB->getone('member_t');

            if($row['sgdt_owner_chk']=='Y') {
                $owner_t = ' / 오너';
            } else{
                if($row['sgdt_leader_chk']=='Y') {
                    $owner_t = ' / 리더';
                } else {
                    $owner_t = '';
                }
            }
            ?>
        <tr>
            <td data-title="멤버정보">
                <?=$row_mt['mt_id']?> (<?=$row_mt['mt_name']?>)<?=$owner_t?>
            </td>
            <td data-title="위치정보">
                <div class="row pl-2">
                    <?php
                    unset($list_sst2);
                    $DB->where('mt_idx', $row_mt['mt_idx']);
                    $DB->where('sgt_idx', $_POST['sgt_idx']);
                    $DB->where(" ( sst_sdate >= '".$_POST['sel_search_sdate']." 00:00:00' and sst_edate <= '".$_POST['sel_search_sdate']." 23:59:59' )");
                    $list_sst2 = $DB->get('smap_schedule_t');

                    $w = 1;
                    if($list_sst2) {
                        foreach($list_sst2 as $row_sst2) {
                ?>
                    <button type="button" onclick="f_member_location_info('<?=$row_sst2['sst_idx']?>');" class="btn btn-xs btn-outline-info mr-2 mb-2"><?=$w?>. <?=$row_sst2['sst_location_title']?></button>
                    <?php
                            $w++;
                        }
                    }
                ?>
                </div>
            </td>
        </tr>
        <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="2" class="text-center"><b>등록된 멤버가 없습니다.</b></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<?
} elseif ($_POST['act'] == "get_line") {
    unset($arr_data);

    if($_POST['sst_idx']) {
        $DB->where('sst_idx', $_POST['sst_idx']);
        $row_sst = $DB->getone('smap_schedule_t');

        unset($list_mlt);
        $DB->where('mt_idx', $row_sst['mt_idx']);
        $DB->where(" ( mlt_wdate >= '".$row_sst['sst_sdate']."' and mlt_wdate <= '".$row_sst['sst_edate']."' )");
        $list_mlt = $DB->get('member_location_log_t');

        unset($gps_data);
        $gps_data = array();
        if($list_mlt) {
            foreach($list_mlt as $row_mlt) {
                $gps_data[] = array($row_mlt['mlt_lat'], $row_mlt['mlt_long']);
            }
        }
        $arr_data['result'] = 'true';
        $arr_data['msg'] = 'GPS라인 데이터입니다.';
        $arr_data['data']['gps'] = $gps_data;
        $arr_data['data']['map_gps'] = array($row_sst['sst_location_lat'], $row_sst['sst_location_long']);
    } else {
        $arr_data['result'] = 'false';
        $arr_data['msg'] = 'GPS라인 테이터를 가져올 수 없습니다.';
        $arr_data['data'] = null;
    }

    echo json_encode($arr_data);
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";