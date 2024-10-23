<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";


if ($_POST['act'] == "list" || $_POST['act'] == "invite_list") {
    $data = [
        'groups' => [],
        'action' => $_POST['act']
    ];

    // 사용자의 그룹 정보를 한 번에 조회
    $query = "SELECT g.sgt_idx, g.sgt_title, gd.sgdt_idx, 
                     COUNT(DISTINCT m.sgdt_idx) as member_cnt,
                     COUNT(DISTINCT i.sit_idx) as invite_cnt
              FROM smap_group_detail_t gd
              JOIN smap_group_t g ON gd.sgt_idx = g.sgt_idx
              LEFT JOIN smap_group_detail_t m ON g.sgt_idx = m.sgt_idx AND m.sgdt_discharge = 'N' AND m.sgdt_exit = 'N' AND m.sgdt_show = 'Y'
              LEFT JOIN smap_invite_t i ON g.sgt_idx = i.sgt_idx AND i.sit_status = 'W'
              WHERE gd.mt_idx = " . $_SESSION['_mt_idx'] . " AND gd.sgdt_discharge = 'N' AND gd.sgdt_exit = 'N' AND gd.sgdt_show = 'Y'
                AND g.sgt_show = 'Y'
              GROUP BY g.sgt_idx
              ORDER BY " . ($_POST['act'] == "list" ? "g.sgt_idx ASC, g.sgt_udate DESC" : "g.sgt_udate DESC, g.sgt_idx ASC");

    $logger->write("그룹 정보 조회 쿼리 실행: " . $query);
    $groups = $DB->rawQuery($query);

    if ($groups) {
        $logger->write("그룹 정보 조회 성공, 그룹 수: " . count($groups));
        foreach ($groups as $group) {
            $groupData = [
                'sgt_idx' => $group['sgt_idx'],
                'sgt_title' => $group['sgt_title'],
                'member_cnt' => $group['member_cnt'],
                'invites' => [],
                'members' => []
            ];

            if ($_POST['act'] == "invite_list") {
                $groupData['sgdt_idx'] = $group['sgdt_idx'];
            }

            if ($group['invite_cnt'] > 0) {
                $groupData['invites'][] = [
                    'count' => $group['invite_cnt']
                ];
            }

            // 그룹 멤버 정보를 한 번에 조회
            $memberQuery = "SELECT m.mt_file1, COALESCE(m.mt_nickname, m.mt_name) as nickname, gd.sgdt_group_chk,
                                   case when gd.sgdt_owner_chk = 'Y' then '" . $translations['txt_owner'] . "' when gd.sgdt_leader_chk = 'Y' then '" . $translations['txt_leader'] . "' else '' end as sgdt_owner_leader_chk_t, gd.sgdt_adate
                            FROM smap_group_detail_t gd
                            JOIN member_t m ON gd.mt_idx = m.mt_idx
                            WHERE gd.sgt_idx = " . $group['sgt_idx'] . " AND gd.sgdt_discharge = 'N' AND gd.sgdt_exit = 'N' AND gd.sgdt_show = 'Y' AND m.mt_idx != " . $_SESSION['_mt_idx'];
            $logger->write("그룹 멤버 정보 조회 쿼리 실행: " . $memberQuery);
            $members = $DB->rawQuery($memberQuery);

            foreach ($members as $member) {
                $sgdt_adate = $member['sgdt_adate'];
                if ($member['sgdt_group_chk'] == 'Y') {
                    $sgdt_adate = $translations['txt_indefinite'];
                } else if (DateTime::createFromFormat('Y-m-d H:i:s', $sgdt_adate) !== false) {
                    $today = new DateTime();
                    $date = new DateTime($sgdt_adate);
                    $remainingDays = floor(($date->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24)) + 1;
                    $sgdt_adate = $remainingDays > 0 ? $remainingDays . $translations['txt_days_ago'] : $translations['txt_expired'];
                }

                $groupData['members'][] = [
                    'mt_file1_url' => "/img/uploads/" . $member['mt_file1'],
                    'nickname' => $member['nickname'],
                    'sgdt_owner_leader_chk_t' => $member['sgdt_owner_leader_chk_t'],
                    'sgdt_adate' => $sgdt_adate,
                ];
            }

            $data['groups'][] = $groupData;
        }
    } else {
        $logger->write("그룹 정보 조회 실패 또는 그룹 없음");
    }

    echo json_encode(['result' => 'success', 'data' => $data]);
    error_log("최종 데이터 반환: " . json_encode($data));
    exit;
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
                                <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="<?=$translations['txt_image'] ?>" />
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
                                    <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1"><?=$translations['txt_remaining_period']?> : <?= $val['sgdt_adate'] ?></p>
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
        p_alert($translations['txt_login_required'], './login', '');
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
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sgt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_invalid_access']);
    }
} elseif ($_POST['act'] == "leader_delete") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
    $plt_condition = $translations['txt_owner_removed_leader'];
    $plt_memo = $translations['txt_push_notification_to_removed_leader'];
    $mt_id = $member_row['mt_id'];
    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
    $group_name = $row_sgt['sgt_title'];
    $plt_title =  $translations['txt_leader_removal_notification'];
    $plt_content =  sprintf($translations['txt_removed_from_group_leader'], $group_name);

    $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);

    echo "Y";
} elseif ($_POST['act'] == "leader_add") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
    $plt_condition = $translations['txt_owner_registered_leader'];
    $plt_memo = $translations['txt_push_notification_to_registered_leader'];
    $mt_id = $member_row['mt_id'];
    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
    $group_name = $row_sgt['sgt_title'];
    $plt_title =  $translations['txt_leader_registration_notification'];
    $plt_content =  sprintf($translations['txt_registered_as_group_leader'], $group_name);

    $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
    echo "Y";
} elseif ($_POST['act'] == "mem_out") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sgt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_invalid_access']);
    }
} elseif ($_POST['act'] == "group_out") {
    if ($_POST['sgdt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sgt_title'] == '') {
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_login_required'], './login', '');
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
        p_alert($translations['txt_invalid_access']);
    }
} elseif ($_POST['act'] == "link_modal") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sgt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_invalid_access']);
    }
    if ($_POST['sgdt_group_chk'] == '') {
        p_alert($translations['txt_invalid_access']);
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
        p_alert($translations['txt_invalid_access']);
    }
    if ($_POST['mt_idx'] == '') {
        p_alert($translations['txt_invalid_access']);
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
            if ($sgdt_count > 0) {
                //이미 다른그룹에 속해있음
                echo ('J');
            } else {
                // 그룹오너 등급 확인하기
                $DB->where('mt_idx', $sgt_row['mt_idx']);
                $owner_row = $DB->getone('member_t');
                if ($owner_row['mt_level'] == '5') { //오너가 유료회원이                   면
                    $group_count = 10;
                } else {
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
                    echo ('Y');
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