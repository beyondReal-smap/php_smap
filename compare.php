<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "list") {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_show', 'Y');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

    unset($list_sgt);
    $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
    $DB->where('sgt_show', 'Y');
    $DB->orderBy("sgt_idx", "asc");
    $DB->orderBy("sgt_udate", "desc");
    $list_sgt = $DB->get('smap_group_t');
    ?>
    <div class="mt_85 pt_20 pb_100">
        <?php
        if ($list_sgt) {
            foreach ($list_sgt as $row_sgt) {
                $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
        ?>
                <div class="border bg-white rounded-lg mb-3">
                    <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'">
                        <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3"><?= $row_sgt['sgt_title'] ?><span class="ml-2">(<?= $member_cnt_t ?>)</span></p>
                        <i class="fs_15 text_gray xi-angle-right-min"></i>
                    </div>
                    <?php
                    unset($list_sgdt);
                    $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
                    $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
                    if ($invite_cnt || $list_sgdt['data']) { ?>
                        <div class="group-body px_16 py_04">
                            <?
                            if ($invite_cnt) {
                            ?>
                                <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_12 group_list_ing">
                                    <?= number_format($invite_cnt) ?>명 초대중
                                </p>
                                <?php
                            }
                            if ($list_sgdt['data']) {
                                foreach ($list_sgdt['data'] as $key => $val) {
                                ?>
                                    <div class="d-flex align-items-center justify-content-between py_12 group_list">
                                        <div class="w_fit">
                                            <a href="#" class="d-flex align-items-center">
                                                <div class="prd_img flex-shrink-0 mr_12">
                                                    <div class="rect_square rounded_14">
                                                        <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                                                    <div class="d-flex align-items-center flex-wrap ">
                                                        <? if ($val['sgdt_owner_leader_chk_t']) { ?>
                                                            <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"><?= $val['sgdt_owner_leader_chk_t'] ?></p>
                                                        <? } ?>
                                                        <? if ($val['sgdt_adate']) { ?>
                                                            <? if ($val['sgdt_owner_leader_chk_t']) { ?>
                                                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                                            <? } ?>
                                                            <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : <?= $val['sgdt_adate'] ?></p>
                                                        <? } ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    <?
                    }
                    ?>
                </div>
        <?php
            }
        }
        ?>
    </div>
<?php
} elseif ($_POST['act'] == "invite_list") {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_show', 'Y');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

    unset($list_sgt);
    $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
    $DB->where('sgt_show', 'Y');
    $DB->orderBy("sgt_udate", "desc");
    $DB->orderBy("sgt_idx", "asc");
    $list_sgt = $DB->get('smap_group_t');
    ?>
    <div class="mt_85 pt_20 pb_100">
        <?php
        if ($list_sgt) {
            foreach ($list_sgt as $row_sgt) {

                $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
                $DB->where('mt_idx', $_SESSION['_mt_idx']);
                $DB->where('sgt_idx', $row_sgt['sgt_idx']);
                $DB->where('sgdt_discharge', 'N');
                $DB->where('sgdt_exit', 'N');
                $DB->where('sgdt_show', 'Y');
                $sgdt_row = $DB->getone('smap_group_detail_t');
        ?>
                <div class="border bg-white rounded-lg mb-3">
                    <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer">
                        <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3"><?= $row_sgt['sgt_title'] ?><span class="ml-2">(<?= $member_cnt_t ?>)</span></p>
                        <button type="button" class="btn fs_14 fw_500 text_gray h_fit_im px-0 py-0 mx-0 my-0 text-right" onclick="f_modal_out_group('<?= $sgdt_row['sgdt_idx'] ?>');"><?= translate('그룹나가기', $userLang) ?></button>
                    </div>
                    <?php
                    unset($list_sgdt);
                    $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
                    $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
                    if ($list_sgdt['data'] || $invite_cnt) { ?>
                        <div class="group-body px_16 py_04">
                            <? if ($invite_cnt) {
                            ?>
                                <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_12 group_list_ing">
                                    <?= number_format($invite_cnt) ?>명 초대중
                                </p>
                                <?php
                            }

                            if ($list_sgdt['data']) {
                                foreach ($list_sgdt['data'] as $key => $val) {
                                ?>
                                    <div class="d-flex align-items-center justify-content-between py_12 group_list">
                                        <div class="w_fit">
                                            <a href="#" class="d-flex align-items-center">
                                                <div class="prd_img flex-shrink-0 mr_12">
                                                    <div class="rect_square rounded_14">
                                                        <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                                                    <div class="d-flex align-items-center flex-wrap ">
                                                        <? if ($val['sgdt_owner_leader_chk_t']) { ?>
                                                            <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"><?= $val['sgdt_owner_leader_chk_t'] ?></p>
                                                        <? } ?>
                                                        <? if ($val['sgdt_adate']) { ?>
                                                            <? if ($val['sgdt_owner_leader_chk_t']) { ?>
                                                                <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                                            <? } ?>
                                                            <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : <?= $val['sgdt_adate'] ?></p>
                                                        <? } ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    <?
                    }
                    ?>
                </div>
        <?php
            }
        }
        ?>
    </div>
    <?php
} else if ($_POST['act'] == "list_info") {
    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t');

    unset($list_sgdt);
    $list_sgdt = get_sgdt_member_list($_POST['sgt_idx']);

    if ($list_sgdt['data']) {
        foreach ($list_sgdt['data'] as $key => $val) {
    ?>
            <div class="py_16 d-flex align-items-center justify-content-between border-bottom group_info_member" data-sgdt-idx="<?= $val['sgdt_idx'] ?>">
                <div class="w_fit">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                            <div class="d-flex align-items-center flex-wrap ">
                                <? if ($val['sgdt_owner_leader_chk_t']) { ?>
                                    <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"><?= $val['sgdt_owner_leader_chk_t'] ?></p>
                                <? } ?>
                                <? if ($val['sgdt_adate']) { ?>
                                    <? if ($val['sgdt_owner_leader_chk_t']) { ?>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                    <? } ?>
                                    <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">남은기간 : <?= $val['sgdt_adate'] ?></p>
                                <? } ?>
                            </div>
                        </div>
                    </a>
                </div>
                <button type="button" class="btn h-auto w-auto p-3 fc_gray" data-toggle="modal" onclick="moreButtonClick('<?= $val['sgdt_leader_chk'] ?>','<?= $val['sgdt_idx'] ?>', '<?= $val['mt_name'] ?>')"><i class=" xi-ellipsis-v"></i></button>
            </div>
    <?php
        }
    }
} elseif ($_POST['act'] == "group_member_order") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $row_sgpt = $DB->getone('smap_group_personal_t');

    if ($row_sgpt['sgpt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "sgdt_json" => $_POST['json_sgdt_idx'],
            "sgpt_udate" => $DB->now(),
        );

        $DB->where('sgpt_idx', $row_sgpt['sgpt_idx']);

        $DB->update('smap_group_personal_t', $arr_query);
    } else {
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_SESSION['_mt_idx'],
            "sgt_idx" => $_POST['sgt_idx'],
            "sgdt_json" => $_POST['json_sgdt_idx'],
            "sgpt_wdate" => $DB->now(),
            "sgpt_udate" => $DB->now(),
        );

        $_last_idx = $DB->insert('smap_group_personal_t', $arr_query);
    }
} elseif ($_POST['act'] == "chg_sgt_title") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['sgt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgt_idx');
    }

    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $DB->where('sgdt_owner_chk', 'Y');
    $row_sgdt = $DB->getone('smap_group_detail_t');

    if ($row_sgdt['mt_idx'] == $_SESSION['_mt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "sgt_title" => $_POST['sgt_title_chg'],
            "sgt_udate" => $DB->now(),
        );

        $DB->where('sgt_idx', $_POST['sgt_idx']);

        $DB->update('smap_group_t', $arr_query);

        p_gotourl("./group_info?sgt_idx=" . $_POST['sgt_idx']);
    } else {
        p_alert("잘못된 접근입니다.");
    }
} elseif ($_POST['act'] == "leader_delete") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $row_sgdt = $DB->getone('smap_group_detail_t');

    if ($row_sgdt['sgdt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "sgdt_leader_chk" => 'N',
        );

        $DB->where('sgt_idx', $row_sgdt['sgt_idx']);

        $DB->update('smap_group_detail_t', $arr_query);
    }

    unset($arr_query);
    $arr_query = array(
        "sgdt_leader_chk" => 'N',
        "sgdt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);

    $DB->update('smap_group_detail_t', $arr_query);

    ##########################################################################################################
    #2024.05.22 리더 해제 시 push메세지 보내기
    ##########################################################################################################
    $DB->where('sgt_idx', $row_sgdt['sgt_idx']);
    $DB->where('sgt_show', 'Y');
    $row_sgt = $DB->getone('smap_group_t');
    unset($member_row);
    $member_row = get_member_t_info($row_sgdt['mt_idx']);
    $plt_type = '2';
    $sst_idx = '';
    $plt_condition = '오너가 리더해제';
    $plt_memo = '해당 그룹의 그룹오너가 해제한 리더에게 푸시알림';
    $mt_id = $member_row['mt_id'];
    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
    $group_name = $row_sgt['sgt_title'];
    $plt_title =  "[SMAP] 리더 해제알림 🚫";
    $plt_content =  '\'' . $group_name . '\' 그룹의 리더에서 해제되었습니다.';

    $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content); 
    /*     $DB->where('sgt_idx', $row_sgdt['sgt_idx']);
    $DB->where('sgt_show', 'Y');
    $row_sgt = $DB->getone('smap_group_t');
    unset($member_row);
    $member_row = get_member_t_info($row_sgdt['mt_idx']);
    $plt_type = '2';
    $sst_idx = $_last_idx;
    $plt_condition = '오너가 리더해제';
    $plt_memo = '해당 그룹의 그룹오너가 해제한 리더에게 푸시알림';
    $mt_id = $member_row['mt_idx'];
    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
    $group_name = $row_sgt['sgt_title'];
    $plt_title =  "[SMAP] 리더 해제알림 🚫";
    $plt_content =  '\'' . $group_name . '\' 그룹의 리더에서 해제되었습니다.';

    $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content); */
    ##########################################################################################################

    echo "Y";
} elseif ($_POST['act'] == "leader_add") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $row_sgdt = $DB->getone('smap_group_detail_t');

    if ($row_sgdt['sgdt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "sgdt_leader_chk" => 'N',
        );

        $DB->where('sgt_idx', $row_sgdt['sgt_idx']);

        $DB->update('smap_group_detail_t', $arr_query);
    }

    unset($arr_query);
    $arr_query = array(
        "sgdt_leader_chk" => 'Y',
        "sgdt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);

    $DB->update('smap_group_detail_t', $arr_query);

    ##########################################################################################################
    #2024.05.22 리더 등록 시 push메세지 보내기
    ##########################################################################################################
    $DB->where('sgt_idx', $row_sgdt['sgt_idx']);
    $DB->where('sgt_show', 'Y');
    $row_sgt = $DB->getone('smap_group_t');

    unset($member_row);
    $member_row = get_member_t_info($row_sgdt['mt_idx']);
    $plt_type = '2';
    $sst_idx = '';
    $plt_condition = '오너가 리더등록';
    $plt_memo = '해당 그룹의 그룹오너가 등록한 리더에게 푸시알림';
    $mt_id = $member_row['mt_id'];
    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
    $group_name = $row_sgt['sgt_title'];
    $plt_title =  '[SMAP] 리더 등록알림 👑';
    $plt_content =  '\'' . $group_name . '\' 그룹의 리더로 등록되었습니다.';

    $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
/*     $DB->where('sgt_idx', $row_sgdt['sgt_idx']);
    $DB->where('sgt_show', 'Y');
    $row_sgt = $DB->getone('smap_group_t');

    unset($member_row);
    $member_row = get_member_t_info($row_sgdt['mt_idx']);
    $plt_type = '2';
    $sst_idx = $_last_idx;
    $plt_condition = '오너가 리더등록';
    $plt_memo = '해당 그룹의 그룹오너가 등록한 리더에게 푸시알림';
    $mt_id = $member_row['mt_idx'];
    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
    $group_name = $row_sgt['sgt_title'];
    $plt_title =  "[SMAP] 리더 등록알림 👑";
    $plt_content =  '\'' . $group_name . '\' 그룹의 리더로 등록되었습니다.';

    $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content); */
    ##########################################################################################################

    echo "Y";
} elseif ($_POST['act'] == "mem_out") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }

    unset($arr_query);
    $arr_query = array(
        "sgdt_discharge" => 'Y',
        "sgdt_ddate" => $DB->now(),
        "sgdt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->update('smap_group_detail_t', $arr_query);

    unset($arr_query);
    $arr_query = array(
        "slt_show" => 'N',
        "slt_ddate" => $DB->now(),
        "slt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->update('smap_location_t', $arr_query);

    unset($arr_query);
    $arr_query = array(
        "sst_show" => 'N',
        "sst_ddate" => $DB->now(),
        "sst_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->update('smap_schedule_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "group_delete") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['sgt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgt_idx');
    }

    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $DB->where('sgdt_owner_chk', 'Y');
    $row_sgdt = $DB->getone('smap_group_detail_t');

    if ($row_sgdt['mt_idx'] == $_SESSION['_mt_idx']) {
        // 그룹 삭제처리
        unset($arr_query);
        $arr_query = array(
            "sgt_show" => 'N',
            "sgt_udate" => $DB->now(),
        );

        $DB->where('sgt_idx', $_POST['sgt_idx']);

        $DB->update('smap_group_t', $arr_query);

        //해당되는 그룹원 삭제처리
        $arr_query = array(
            "sgdt_show" => 'N',
            "sgdt_udate" => $DB->now(),
        );
        $DB->where('sgt_idx', $_POST['sgt_idx']);
        $DB->update('smap_group_detail_t', $arr_query);

        unset($arr_query);
        $arr_query = array(
            "slt_show" => 'N',
            "slt_ddate" => $DB->now(),
            "slt_udate" => $DB->now(),
        );

        $DB->where('sgt_idx', $_POST['sgt_idx']);
        $DB->update('smap_location_t', $arr_query);

        unset($arr_query);
        $arr_query = array(
            "sst_show" => 'N',
            "sst_ddate" => $DB->now(),
            "sst_udate" => $DB->now(),
        );

        $DB->where('sgt_idx', $_POST['sgt_idx']);
        $DB->update('smap_schedule_t', $arr_query);

        echo "Y";
    } else {
        p_alert("잘못된 접근입니다.");
    }
} elseif ($_POST['act'] == "group_out") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }

    unset($arr_query);
    $arr_query = array(
        "sgdt_exit" => 'Y',
        "sgdt_show" => 'N',
        "sgdt_xdate" => $DB->now(),
        "sgdt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->update('smap_group_detail_t', $arr_query);

    unset($arr_query);
    $arr_query = array(
        "slt_show" => 'N',
        "slt_ddate" => $DB->now(),
        "slt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->update('smap_location_t', $arr_query);

    unset($arr_query);
    $arr_query = array(
        "sst_show" => 'N',
        "sst_ddate" => $DB->now(),
        "sst_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->update('smap_schedule_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "chk_sgt_title") { // 그룹명 중복검사
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['sgt_title'] == '') {
        p_alert("잘못된 접근입니다. sgt_title");
    }

    $DB->where('sgt_title', $_POST['sgt_title']);
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgt_show', 'Y');
    $row = $DB->getone('smap_group_t');

    if ($row['sgt_idx']) {
        echo json_encode(false);
    } else {
        echo json_encode(true);
    }
} elseif ($_POST['act'] == "group_create") { // 그룹생성
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }

    $DB->where('sgt_title', $_POST['sgt_title']);
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgt_show', 'Y');
    $row = $DB->getone('smap_group_t');

    if ($row['sgt_idx'] == '') {
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_SESSION['_mt_idx'],
            "sgt_title" => $_POST['sgt_title'],
            "sgt_code" => get_sgt_code(),
            "sgt_show" => "Y",
            "sgt_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('smap_group_t', $arr_query);

        unset($arr_query);
        $arr_query = array(
            "sgt_idx" => $_last_idx,
            "mt_idx" => $_SESSION['_mt_idx'],
            "sgdt_owner_chk" => "Y",
            "sgdt_leader_chk" => "N",
            "sgdt_discharge" => "N",
            "sgdt_exit" => "N",
            "sgdt_show" => "Y",
            "sgdt_push_chk" => "Y",
            "sgdt_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('smap_group_detail_t', $arr_query);

        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_SESSION['_mt_idx'],
            "sgdt_idx" => $_last_idx,
            "sgdt_mt_idx" => $_SESSION['_mt_idx'],
            "slmt_wdate" => $DB->now(),
        );
        $_last_idx = $DB->insert('smap_location_member_t', $arr_query);

        p_gotourl("./group");
    } else {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }
} elseif ($_POST['act'] == "link_modal") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['sgt_idx'] == '') {
        p_alert("잘못된 접근입니다. sgt_idx");
    }

    $DB->where('sgt_idx', $_POST['sgt_idx']);
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sit_status', '1');
    $row = $DB->getone('smap_invite_t');

    if ($row['sit_idx'] == '') {
        $DB->where('sgt_idx', $_POST['sgt_idx']);
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $sit_row = $DB->get('smap_invite_t');
        $sit_count = count($sit_row);
        /*
        if ($sit_count <= 10) {
            $sit_code_t = get_sit_code();

            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_SESSION['_mt_idx'],
                "sgt_idx" => $_POST['sgt_idx'],
                "sit_code" => $sit_code_t,
                "sit_status" => '1',
                "sit_wdate" => $DB->now(),
            );

            $_last_idx = $DB->insert('smap_invite_t', $arr_query);

            $rtn = $ct_invite_url . $sit_code_t;
        } else {
            $rtn = 'N';
        }
        */
        $sit_code_t = get_sit_code();

        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_SESSION['_mt_idx'],
            "sgt_idx" => $_POST['sgt_idx'],
            "sit_code" => $sit_code_t,
            "sit_status" => '1',
            "sit_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('smap_invite_t', $arr_query);

        $rtn = $ct_invite_url . $sit_code_t;
    } else {
        $rtn = $ct_invite_url . $row['sit_code'];
    }

    echo $rtn;
} elseif ($_POST['act'] == "share_link") {
    if ($_POST['currentURL'] == '') {
        p_alert("잘못된 접근입니다. currentURL");
    }

    $parsed_url = parse_url($_POST['currentURL']);
    parse_str($parsed_url['query'], $params);

    if ($params['sit_code']) {
        $sit_code_t = trim($params['sit_code']);

        unset($arr_query);
        $arr_query = array(
            "sit_status" => '2',
        );

        $DB->where('sit_code', $sit_code_t);

        $DB->update('smap_invite_t', $arr_query);

        echo "Y";
    } else {
        echo "N";
    }
} elseif ($_POST['act'] == "group_activity_period") { // 그룹활동기한 무기한 설정
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }
    unset($arr_query);
    $arr_query = array(
        "sgdt_group_chk" => 'Y',
        "sgdt_adate" => '2099-12-31',
        "sgdt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);

    $DB->update('smap_group_detail_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "group_activity_period_detail") { // 그룹활동기한 디테일 설정
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }
    if ($_POST['sgdt_group_chk'] == '') {
        p_alert('잘못된 접근입니다. sgdt_group_chk');
    }
    unset($arr_query);
    $arr_query = array(
        "sgdt_group_chk" => $_POST['sgdt_group_chk'],
        "sgdt_adate" => $_POST['sgdt_adate'],
        "sgdt_udate" => $DB->now(),
    );

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);

    $DB->update('smap_group_detail_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "group_invite_code_chk") { // 초대코드 사용하여 그룹가입
    if ($_POST['sit_code'] == '') {
        p_alert('잘못된 접근입니다. sit_code');
    }
    if ($_POST['mt_idx'] == '') {
        p_alert('잘못된 접근입니다. mt_idx');
    }
    $DB->where('sit_code', $_POST['sit_code']);
    $DB->where('sit_status', '2');
    $sit_row = $DB->getone('smap_invite_t');
    if ($sit_row['sit_idx']) {
        $DB->where('sgt_idx', $sit_row['sgt_idx']);
        $DB->where('sgt_show', 'Y');
        $sgt_row = $DB->getone('smap_group_t');
        if ($sgt_row['sgt_idx']) {
            // 다른그룹에 속해있는지 확인
            $DB->where('mt_idx', $_SESSION['_mt_idx']);
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
            $DB->where('sgdt_show', 'Y');
            $sgdt_list = $DB->get('smap_group_detail_t');
            $sgdt_count = count($sgdt_list);
            if($sgdt_count > 0){
                //이미 다른그룹에 속해있음
                echo ('J');
            }else{
                // 그룹오너 등급 확인하기
                $DB->where('mt_idx', $sgt_row['mt_idx']);
                $owner_row = $DB->getone('member_t');
                if($owner_row['mt_level'] == '5'){ //오너가 유료회원이면
                    $group_count = 10;
                }else {
                    $group_count = 4;
                }
                // 해당 그룹 인원 확인
                $DB->where('sgt_idx', $sit_row['sgt_idx']);
                $DB->where('sgdt_discharge', 'N');
                $DB->where('sgdt_exit', 'N');
                $DB->where('sgdt_show', 'Y');
                $sgdt_list = $DB->get('smap_group_detail_t');
                $sgdt_count = count($sgdt_list);
                if ($sgdt_count > $group_count) {
                    //이미 인원마감
                    echo ('C');
                } else {
                    unset($arr_query);
                    $arr_query = array(
                        "sit_status" => '3',
                        "sit_adate" => $DB->now(),
                    );
                    $DB->where('sit_idx', $sit_row['sit_idx']);
                    $DB->update('smap_invite_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "sgt_idx" => $sit_row['sgt_idx'],
                        "mt_idx" => $_SESSION['_mt_idx'],
                        "sgdt_owner_chk" => 'N',
                        "sgdt_leader_chk" => 'N',
                        "sgdt_discharge" => 'N',
                        "sgdt_group_chk" => 'D',
                        "sgdt_exit" => 'N',
                        "sgdt_show" => 'Y',
                        "sgdt_push_chk" => 'Y',
                        "sgdt_wdate" => $DB->now(),
                    );
                    $_last_idx = $DB->insert('smap_group_detail_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "mt_idx" => $sgt_row['mt_idx'],
                        "sgdt_idx" => $_last_idx,
                        "sgdt_mt_idx" => $_SESSION['_mt_idx'],
                        "slmt_wdate" => $DB->now(),
                    );
                    $_last_idx = $DB->insert('smap_location_member_t', $arr_query);
                    
                    # 2024.05.22 그룹 가입 시 그룹오너/리더에게 푸시메세지 전송
                    unset($mem_row);
                    $mem_row = get_member_t_info($_SESSION['_mt_idx']); // 그룹원 회원 정보
                    $DB->where('sgt_idx', $sgt_row['sgt_idx']);
                    $DB->where('(sgdt_owner_chk ="Y" or sgdt_leader_chk="Y") and sgdt_exit = "N"');
                    $sgdt_list = $DB->get('smap_group_detail_t');
                    if ($sgdt_list) {
                        foreach ($sgdt_list as $sgdt_row_ol) {
                            unset($member_row);
                            $member_row = get_member_t_info($sgdt_row_ol['mt_idx']); // 오너/리더 회원정보
                            $plt_type = '2';
                            $sst_idx = $_last_idx;
                            $plt_condition = '그룹원이 그룹오너의 그룹에 가입';
                            $plt_memo = '초대 코드를 통해 새로운 그룹원이 그룹에 가입 푸시알림';
                            $mt_id = $member_row['mt_id'];
                            $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
                            $mem_nickname = $mem_row['mt_nickname'] ? $mem_row['mt_nickname'] : $mem_row['mt_name'];
                            $plt_title = '[SMAP] 새로운 멤버가 합류했어요 🎉';
                            $plt_content = $mem_nickname . '님이 합류했습니다.';

                            $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
                        }
                    }

                    echo('Y');
                }
            }
        } else {
            echo ('D');
        }
    } else {
        echo ('N');
    }
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";