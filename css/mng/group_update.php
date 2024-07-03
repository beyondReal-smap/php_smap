<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "update") {
    if($_POST['sgt_title'] == "") {
        p_alert("잘못된 접근입니다. sgt_title", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "sgt_title" => $_POST['sgt_title'],
    );

    $DB->where('sgt_idx', $_POST['sgt_idx']);

    $DB->update('smap_group_t', $arr_query);
    $_last_idx = $_POST['sgt_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    unset($arr_query);
    $arr_query = array(
        "sgt_show" => 'N',
    );

    $DB->where('sgt_idx', $_POST['obj_idx']);

    $DB->update('smap_group_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "chg_leader") {
    unset($arr_query);
    $arr_query = array(
        "sgdt_leader_chk" => 'N',
    );

    $DB->where('sgt_idx', $_POST['sgt_idx']);

    $DB->update('smap_group_detail_t', $arr_query);

    unset($arr_query);
    $arr_query = array(
        "sgdt_leader_chk" => 'Y',
    );

    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $DB->where('mt_idx', $_POST['mt_idx']);

    $DB->update('smap_group_detail_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.sgt_show', 'Y');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.sgt_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.sgt_code, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
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
            <th class="text-center" style="width:100px;">
                번호
            </th>
            <th class="text-center" style="width:160px;">
                관리
            </th>
            <th class="text-center" style="width:160px;">
                오너
            </th>
            <th class="text-center" style="width:160px;">
                리더
            </th>

            <th class="text-center">
                그룹명
            </th>
            <th class="text-center" style="width:120px;">
                초대코드
            </th>
            <th class="text-center">
                그룹원
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
            $DB->where('mt_idx', $row['mt_idx']);
            $row_mt = $DB->getone('member_t');

            unset($list_gp);
            $DB->where('sgt_idx', $row['sgt_idx']);
            $DB->where('sgdt_owner_chk', 'N');
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
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
        <tr>
            <td data-title="번호" class="text-center">
                <?=$counts?>
            </td>
            <td data-title="관리" class="text-center">
                <input type="button" class="btn btn-outline-primary btn-sm" value="상세" onclick="location.href='./group_form?act=update&sgt_idx=<?=$row['sgt_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./group_update', '<?=$row['sgt_idx']?>');" />
            </td>
            <td data-title="오너" class="text-center">
                <?=$row_mt['mt_name']?> (<?=$row_mt['mt_id']?>)
            </td>
            <td data-title="리더" class="text-center">
                <?=$gp_leader?>
            </td>

            <td data-title="그룹명">
                <span class="line1_text"><?=$row['sgt_title']?></span>
            </td>

            <td data-title="초대코드" class="text-center">
                <?=$row['sgt_code']?>
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
                        <a data-toggle="tooltip" data-placement="top" title="<?=$row_mt3['mt_name']?> (<?=$row_mt3['mt_id']?>)"><img src="https://picsum.photos/<?=$row_mt['mt_file1']?>" onerror="this.src='<?=$ct_no_profile_img_url?>'" width="25" height="25" /></a>
                    </span>
                    <?php
                            }
                        }
            ?>
                </div>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['sgt_wdate'], 6)?>
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
    if($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
