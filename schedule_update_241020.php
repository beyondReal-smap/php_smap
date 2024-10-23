<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "event_source") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    if ($_POST['sgdt_mt_idx']) {
        $mt_idx_t = $_POST['sgdt_mt_idx'];
    } else {
        $mt_idx_t = $_SESSION['_mt_idx'];
    }

    $arr_data = array();

    //나의 일정
    unset($list);
    $DB->where('mt_idx', $mt_idx_t);
    $DB->where("( sst_sdate >= '" . $_POST['start'] . " 23:59:59' and sst_edate <= '" . $_POST['end'] . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $cd = cal_remain_days($row['sst_sdate'], $row['sst_edate']);

                if ($cd) {
                    for ($q = 0; $q < $cd; $q++) {
                        $sdate_t = date("Y-m-d", strtotime($row['sst_sdate'] . " +" . $q . " days"));
                        $arr_data[] = array(
                            'id' => $row['sst_idx'],
                            'start' => $sdate_t,
                            'end' => $row['sst_edate'],
                            'title' => $row['sst_title'],
                        );
                    }
                }
            }
        }
    }

    //나에게 온 일정
    $DB->where('mt_idx', $mt_idx_t);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

    if ($row_sgdt['gc_sgt_idx']) {
        unset($list);
        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where(" ( sst_sdate >= '" . $_POST['start'] . " 23:59:59' and sst_edate <= '" . $_POST['end'] . " 00:00:00' )");
        $DB->where('sst_show', 'Y');
        $list = $DB->get('smap_schedule_t');

        if ($list) {
            foreach ($list as $row) {
                if ($row['sst_title']) {
                    $cd = cal_remain_days($row['sst_sdate'], $row['sst_edate']);

                    if ($cd) {
                        for ($q = 0; $q < $cd; $q++) {
                            $sdate_t = date("Y-m-d", strtotime($row['sst_sdate'] . " +" . $q . " days"));
                            $arr_data[] = array(
                                'id' => $row['sst_idx'],
                                'start' => $sdate_t,
                                'end' => $row['sst_edate'],
                                'title' => $row['sst_title'],
                            );
                        }
                    }
                }
            }
        }
    }

    $rtn = json_encode($arr_data);

    echo $rtn;
} elseif ($_POST['act'] == "list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert(translate($translations['txt_login_required'], $userLang), './login', '');
    }

    function getUserSchedules($mt_idx, $date) {
        global $DB, $logger;
        
        $logger->write('getUserSchedules - 사용자 일정 조회 시작');
        $user = $DB->rawQuery("SELECT mt_nickname, mt_name, mt_file1 FROM member_t WHERE mt_idx = " . $mt_idx);
        $logger->write('getUserSchedules - 사용자 정보 조회 완료');
        
        $schedules = $DB->rawQuery("
            SELECT sst.*, m.mt_nickname, m.mt_name, m.mt_file1
            FROM smap_schedule_t sst
            LEFT JOIN member_t m ON sst.mt_idx = m.mt_idx
            WHERE sst.mt_idx = " . $mt_idx . " AND DATE(sst.sst_sdate) <= '" . $date . "' AND DATE(sst.sst_edate) >= '" . $date . "' AND sst.sst_show = 'Y'
            ORDER BY sst.sst_all_day DESC, sst.sst_sdate ASC, sst.sst_edate ASC
        ");
        $logger->write('getUserSchedules - 사용자 일정 조회 완료');
        
        return [
            'info' => [
                'nickname' => $user['mt_nickname'] ?: $user['mt_name'],
                'profile_img' => $user['mt_file1']
            ],
            'schedules' => $schedules
        ];
    }

    function getGroupSchedules($mt_idx, $date) {
        global $DB, $logger;
        
        $logger->write('getGroupSchedules - 그룹 일정 조회 시작');
        $groups = $DB->rawQuery("
            SELECT DISTINCT sgt.sgt_idx, sgt.sgt_title
            FROM smap_group_t sgt
            JOIN smap_group_detail_t sgdt ON sgt.sgt_idx = sgdt.sgt_idx
            WHERE sgdt.mt_idx = " . $mt_idx . " AND sgt.sgt_show = 'Y' AND sgdt.sgdt_show = 'Y' AND sgdt.sgdt_discharge = 'N' AND sgdt.sgdt_exit = 'N'
        ");
        
        $groupSchedules = [];
        foreach ($groups as $group) {
            $members = $DB->rawQuery("
                SELECT m.mt_idx, m.mt_nickname, m.mt_name, m.mt_file1, sgdt.sgdt_owner_chk, sgdt.sgdt_leader_chk
                FROM smap_group_detail_t sgdt
                JOIN member_t m ON sgdt.mt_idx = m.mt_idx
                WHERE sgdt.sgt_idx = " . $group['sgt_idx'] . " AND sgdt.sgdt_show = 'Y' AND sgdt.sgdt_discharge = 'N' AND sgdt.sgdt_exit = 'N'
            ");
            
            $memberSchedules = [];
            foreach ($members as $member) {
                $schedules = $DB->rawQuery("
                    SELECT sst.* 
                    FROM smap_schedule_t sst
                    WHERE sst.mt_idx = " . $member['mt_idx'] . " AND DATE(sst.sst_sdate) <= '" . $date . "' AND DATE(sst.sst_edate) >= '" . $date . "' AND sst.sst_show = 'Y'
                    ORDER BY sst.sst_all_day DESC, sst.sst_sdate ASC, sst.sst_edate ASC
                ");
                
                $memberSchedules[] = [
                    'info' => $member,
                    'schedules' => $schedules
                ];
            }
            
            $groupSchedules[] = [
                'info' => $group,
                'members' => $memberSchedules
            ];
        }
        
        return $groupSchedules;
    }

    $mt_idx = $_SESSION['_mt_idx']; // 세션에서 사용자 ID를 가져옵니다.
    $event_start_date = $_POST['event_start_date'];

    $scheduleData = [
        'user' => getUserSchedules($mt_idx, $event_start_date),
        'groups' => getGroupSchedules($mt_idx, $event_start_date)
    ];

    echo json_encode($scheduleData);


} elseif ($_POST['act'] == "get_schedule_member") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    } ?>
    <ul class="member_group" id="accordionExample">
        <?php
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx, GROUP_CONCAT(sgdt_idx) as gc_sgdt_idx');

        if ($row_sgdt['gc_sgt_idx']) {
            unset($list_sgt);
            // $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
            $DB->where("sgdt_idx in (" . $row_sgdt['gc_sgdt_idx'] . ")");
            $DB->where('sgt_show', 'Y');
            $DB->orderBy("sgt_udate", "desc");
            $DB->orderBy("sgt_idx", "asc");
            $list_sgt = $DB->get('smap_group_t');

            if ($list_sgt) {
                foreach ($list_sgt as $row_sgt) {
                    $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
        ?>
                    <li>
                        <div class="group_name">
                            <h2 class="mb-0">
                                <button type="button" class="btn btn-secondary justify-content-between w-100 btn-md rounded-0 pl_20 pr_25 fw_700 border-0" data-toggle="collapse" data-target="#group_cont_<?= $row_sgt['sgt_idx'] ?>" aria-expanded="false" aria-controls="group_cont_<?= $row_sgt['sgt_idx'] ?>"><?= $row_sgt['sgt_title'] ?> (<?= number_format($member_cnt_t) ?>) <img src="<?= CDN_HTTP ?>/img/arrow_down.png"></button>
                            </h2>
                        </div>

                        <div id="group_cont_<?= $row_sgt['sgt_idx'] ?>" class="group_cont collapse">
                            <div class="card-body">
                                <ul class="member_lst">
                                    <?php
                                    unset($list_sgdt);
                                    $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);

                                    if ($list_sgdt['data']) {
                                        foreach ($list_sgdt['data'] as $key => $val) {
                                            if ($_SESSION['_mt_idx'] == $val['mt_idx']) {
                                                $val['mt_nickname'] = '나';
                                            }
                                    ?>
                                            <li>
                                                <div class="d-flex align-items-center">
                                                    <div class="checks m-0">
                                                        <label for="sgdt_idx_r1_<?= $val['sgdt_idx'] ?>">
                                                            <input type="radio" class="sgdt_idx_c" name="sgdt_idx_r1" id="sgdt_idx_r1_<?= $val['sgdt_idx'] ?>" value="<?= $val['sgdt_idx'] ?>" <?php if ($_POST['sgdt_idx'] == $val['sgdt_idx']) {
                                                                                                                                                                                                    echo " checked";
                                                                                                                                                                                                } ?> />
                                                            <span class="ic_box"><i class="xi-check-min"></i></span>
                                                        </label>
                                                        <input type="hidden" name="mt_nickname_r1_<?= $val['sgdt_idx'] ?>" id="mt_nickname_r1_<?= $val['sgdt_idx'] ?>" value="<?= $val['mt_nickname'] ?>" />
                                                        <input type="hidden" name="mt_idx_r1_<?= $val['sgdt_idx'] ?>" id="mt_idx_r1_<?= $val['sgdt_idx'] ?>" value="<?= $val['mt_idx'] ?>" />
                                                    </div>
                                                    <div class="prd_img flex-shrink-0 mr_12">
                                                        <div class="rect_square rounded_14">
                                                            <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2" style="word-break: break-all;"><?= $val['mt_nickname'] ?></p>
                                                        <div class="d-flex align-items-center flex-wrap">
                                                            <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1" style="word-break: break-all;"><?= $translations['txt_' . $val['sgdt_owner_leader_chk_t']] ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </li>
            <?php
                }
            }
        } else {
            ?>
            <li>
                <div class="pt-5 text-center">
                    <img src="<?= CDN_HTTP ?>/img/warring.png" width="82px" alt="자료없음">
                    <p class="mt_20 fc_gray_900 text-center"><?= $translations['txt_no_members_registered'] ?></p>
                </div>
                <!-- 멤버가 없을때 -->
            </li>
        <?php
        }
        ?>
    </ul>
<? } elseif ($_POST['act'] == "get_schedule_map") { ?>
    <form method="post" name="frm_schedule_map" id="frm_schedule_map">
        <div class="modal-header">
            <p class="modal-title line1_text fs_20 fw_700"><?= $translations['txt_select_location'] ?></p>
            <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
        </div>
        <div class="modal-body scroll_bar_y" style="min-height: 600px;">
            <div class="px-0 py-0 map_wrap">
                <div class="map_wrap_re">
                    <div class="pin_cont bg-white pt_20 px_16 pb_16 rounded_10">
                        <ul>
                            <li class="d-flex">
                                <div class="name flex-fill">
                                    <span class="fs_12 fw_600 text-primary"><?= $translations['txt_selected_location'] ?></span>
                                    <div class="fs_14 fw_600 text_dynamic mt-1 line_h1_3"></div>
                                </div>
                                <button type="button" class="mark_btn on"></button>
                            </li>
                        </ul>
                    </div>
                    <div class="map_ab" id="naver_map">
                        <div class="point point2">
                            <img src="<?= CDN_HTTP ?>/img/pin_marker.png" width="39px" alt="이미지">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer border-0 p-0">
            <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?= $translations['txt_select_location_complete'] ?></button>
        </div>
    </form>
    <?php
} elseif ($_POST['act'] == "map_location_input") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('slt_add', $_POST['slt_add']);
    $DB->where('slt_show', 'Y');
    $row_slt = $DB->getone('smap_location_t');

    if ($row_slt['slt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "slt_show" => "N",
        );

        $DB->where('slt_idx', $row_slt['slt_idx']);

        $DB->update('smap_location_t', $arr_query);

        $_last_idx = $row_slt['slt_idx'];
    } else {
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_SESSION['_mt_idx'],
            "slt_title" => $_POST['slt_title'],
            "slt_add" => $_POST['slt_add'],
            "slt_lat" => $_POST['slt_lat'],
            "slt_long" => $_POST['slt_long'],
            "slt_show" => "Y",
            "slt_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('smap_location_t', $arr_query);
    }

    echo $_last_idx;
} elseif ($_POST['act'] == "list_like_location") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($mem_row['mt_level'] == '2') {
        $limit = 2;
    } else {
        $limit = 10;
    }

    unset($list_slt);
    $DB->where('(mt_idx =' . $_POST['mt_idx'] . ' or sgdt_idx = ' . $_POST['sgdt_idx'] . ')');
    $DB->where('slt_show', 'Y');
    $DB->orderby('slt_wdate', 'asc');
    $list_slt = $DB->get('smap_location_t', $limit);

    if ($list_slt) {
        foreach ($list_slt as $row_slt) {
    ?>
            <li class="border bg-white  rounded_10">
                <input type="hidden" name="slt_title" id="slt_title_<?= $row_slt['slt_idx'] ?>" value="<?= $row_slt['slt_title'] ?>" />
                <input type="hidden" name="slt_add" id="slt_add_<?= $row_slt['slt_idx'] ?>" value="<?= $row_slt['slt_add'] ?>" />
                <input type="hidden" name="slt_lat" id="slt_lat_<?= $row_slt['slt_idx'] ?>" value="<?= $row_slt['slt_lat'] ?>" />
                <input type="hidden" name="slt_long" id="slt_long_<?= $row_slt['slt_idx'] ?>" value="<?= $row_slt['slt_long'] ?>" />
                <div class="">
                    <div class="checks m-0">
                        <label class="p-4">
                            <input type="radio" class="slt_idx_c" name="slt_idx_r1" id="slt_idx_r1" value="<?= $row_slt['slt_idx'] ?>" />
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="flex-fill">
                                <div class="name">
                                    <span class="fw_700"><?= $row_slt['slt_title'] ?></span>
                                </div>
                                <div class="fs_13 fc_gray_600 text_dynamic mt-1 line_h1_3" style="white-space: pre-line;"><?= $row_slt['slt_add'] ?></div>
                            </div>
                        </label>
                    </div>
                </div>
            </li>
        <?php
        }
    } else {
        ?>
        <li>
            <div class="pt-5 text-center">
                <img src="<?= CDN_HTTP ?>/img/warring.png" width="82px" alt="자료없음">
                <p class="mt_20 fc_gray_500 text-center line_h1_4"><?= $translations['txt_no_registered_locations'] ?></p>
            </div>
            <!-- 멤버가 없을때 -->
        </li>
        <?php
    }
} elseif ($_POST['act'] == "map_location_like_delete") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['slt_idx'] == '') {
        p_alert('잘못된 접근입니다. slt_idx');
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('slt_idx', $_POST['slt_idx']);
    $row_slt = $DB->getone('smap_location_t');

    if ($row_slt['slt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "slt_show" => "N",
        );

        $DB->where('slt_idx', $row_slt['slt_idx']);

        $DB->update('smap_location_t', $arr_query);

        echo "Y";
    } else {
        echo "N";
    }
} elseif ($_POST['act'] == "list_contact") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    unset($list_sctg);
    if ($_POST['sst_idx']) {
        $DB->where('sst_idx', $_POST['sst_idx']);
    } else {
        $DB->where("sst_idx is null");
    }
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->groupBy("sct_category");
    $DB->orderBy("sct_idx", "desc");
    $list_sctg = $DB->get('smap_contact_t');

    if ($list_sctg) {
        foreach ($list_sctg as $row_sctg) {
        ?>
            <!-- <li data-toggle="modal" data-target="#contact_modify">
            <div class="text-primary mb-3"><?= $row_sctg['sct_category'] ?></div> -->
            <li>
                <!-- <div class="text-primary mb-3"><?= $row_sctg['sct_category'] ?></div> -->
                <ul class="contact_list" id="contact_list_li">
                    <?php
                    unset($list_sct);
                    if ($_POST['sst_idx']) {
                        $DB->where('sst_idx', $_POST['sst_idx']);
                    } else {
                        $DB->where("sst_idx is null");
                    }
                    $DB->where('mt_idx', $_SESSION['_mt_idx']);
                    $DB->where('sct_category', $row_sctg['sct_category']);
                    $DB->orderBy("sct_idx", "desc");
                    $list_sct = $DB->get('smap_contact_t');

                    if ($list_sct) {
                        foreach ($list_sct as $row_sct) {
                    ?>
                            <!-- <li class="d-flex justify-content-between">
                                <div><?= $row_sct['sct_title'] ?></div>
                                <div class="fc_gray_500"><?= $row_sct['sct_hp'] ?></div>
                                <input type="hidden" name="sst_idx" id="sst_idx<?= $row_sct['sst_idx'] ?>" value="<?= $row_sct['sst_idx'] ?>" />
                            </li> -->
                            <li class="d-flex justify-content-between">
                                <div class="pr-3"><?= $row_sct['sct_title'] ?></div>
                                <a href="tel:<?= $row_sct['sct_hp'] ?>" class="fc_gray_500"><?= $row_sct['sct_hp'] ?></a>
                                <input type="hidden" name="sst_idx" id="sst_idx<?= $row_sct['sst_idx'] ?>" value="<?= $row_sct['sst_idx'] ?>" />
                            </li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </li>
        <?php
        }
    } else {
        ?>
        <!-- <li>
            <button type="button" class="border bg-white px_12 py-4 rounded_16 align-items-center d-flex flex-column justify-content-center w-100" data-toggle="modal" data-target="#schedule_contact">
                <img class="d-block" src="<?= CDN_HTTP ?>/img/ico_add.png" style="width:2.0rem;">
                <span class="fc_gray_500 fw_700 mt-3">새로운 연락처추가</span>
            </button>
        </li> -->
    <?php
    }
} elseif ($_POST['act'] == "contact_input") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sst_idx']) {
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_SESSION['_mt_idx'],
            "sst_idx" => $_POST['sst_idx'],
            "sct_category" => $_POST['sct_category'],
            "sct_title" => $_POST['sct_title'],
            "sct_hp" => $_POST['sct_hp'],
            "sct_wdate" => $DB->now(),
        );
        $_last_idx = $DB->insert('smap_contact_t', $arr_query);
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where('sct_category', $_POST['sct_category']);
        $DB->where('sct_title', $_POST['sct_title']);
        $DB->where('sct_hp', $_POST['sct_hp']);
        $row_slt = $DB->getone('smap_contact_t');

        if ($row_slt['slt_idx'] == '') {
            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_SESSION['_mt_idx'],
                "sct_category" => $_POST['sct_category'],
                "sct_title" => $_POST['sct_title'],
                "sct_hp" => $_POST['sct_hp'],
                "sct_wdate" => $DB->now(),
            );

            $_last_idx = $DB->insert('smap_contact_t', $arr_query);
        }
    }

    echo "Y";
} elseif ($_POST['act'] == "schedule_form") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sst_title'] == '') {
        p_alert('잘못된 접근입니다. sst_title');
    }
    if ($_POST['sst_sdate'] == '') {
        p_alert('잘못된 접근입니다. sst_sdate');
    }
    // if ($_POST['sgdt_idx_t'] == '') {
    //     p_alert('잘못된 접근입니다. sgdt_idx_t');
    // }
    // if ($_POST['slt_idx_t'] == '') {
    //     p_alert('잘못된 접근입니다. slt_idx_t');
    // }

    if ($_POST['sgdt_idx']) {
        $DB->where('sgdt_idx', $_POST['sgdt_idx']);
        $row_sgdt = $DB->getone('smap_group_detail_t');
    }

    if ($_POST['sst_all_day'] != 'Y') {
        $_POST['sst_all_day'] = 'N';
    }
    if ($_POST['sst_schedule_alarm_chk'] != 'Y') {
        $_POST['sst_schedule_alarm_chk'] = 'N';
    }
    // JSON을 배열로 변환
    $repeat_array = json_decode($_POST['sst_repeat_json'], true);
    // 반복을 저장할 배열
    $repeat_values = array();

    // "r1"이 1이 아니거나 값이 없는 경우를 제외하고 반복을 생성
    if ($repeat_array['r1'] == 1 || empty($repeat_array['r1'])) {
        // $repeat_sdate = $_POST['pick_sdate'];
        // $repeat_edate = $_POST['pick_edate'];

        // // 시작 날짜와 종료 날짜를 DateTime 객체로 변환
        // $start_date = new DateTime($repeat_sdate);
        // $end_date = new DateTime($repeat_edate);

        // // 날짜 사이의 모든 날짜를 구하기 위한 DatePeriod 생성
        // $date_period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date->modify('+1 day'));

        // // 모든 날짜를 저장할 배열
        // $all_dates = array();

        // // DatePeriod에서 각 날짜를 반복하여 배열에 추가
        // foreach ($date_period as $date) {
        //     $all_dates[] = $date->format('Y-m-d');
        // }
    } else {
        // 시작 날짜와 종료 날짜
        $repeat_sdate = $_POST['pick_sdate'];
        // $repeat_edate = $_POST['pick_edate'];
        // $repeat_edate = '2049-12-31'; // repeat_edate를 2099년 12월 31일로 설정 ->2049년 12월 31일로 설정 
        $timestamp = strtotime($repeat_sdate);
        $repeat_edate = date('Y-m-d', strtotime('+3 years', $timestamp)); // 10년 뒤의 날짜 계산

        // 시작 날짜와 종료 날짜를 DateTime 객체로 변환
        $start_date = new DateTime($repeat_sdate);
        $end_date = new DateTime($repeat_edate);

        if ($repeat_array['r1'] == 3) { // 매�� 반복일 시 요일 찾은 후 반복 실행
            if (isset($repeat_array['r2']) && !empty($repeat_array['r2'])) {
                // "r2" 값을 쉼표로 분리하여 배열로 변환
                $r2_values = explode(',', $repeat_array['r2']);
                // 배열에 추가
                foreach ($r2_values as $value) {
                    if (!empty($value)) {
                        $repeat_values[] = $value;
                    }
                }
                // "r2" 값에 해당하는 요일에 대해 날짜를 구하고 저장할 배열
                $all_dates = array();

                // DatePeriod에서 각 날짜를 반복하여 배열에 추가
                foreach ($repeat_values as $day_of_week) {
                    $current_date = clone $start_date;
                    // 해당 요일의 날짜를 찾아 배열에 추가
                    while ($current_date <= $end_date) {
                        if ($current_date->format('N') == $day_of_week) {
                            $all_dates[] = $current_date->format('Y-m-d');
                        }
                        $current_date->modify('+1 day');
                    }
                }
            }
        } else if ($repeat_array['r1'] == 2) { // 매일 반복
            // 시작 날짜와 종료 날짜를 DateTime 객체로 변환
            $start_date = new DateTime($repeat_sdate);
            $end_date = new DateTime($repeat_edate);

            // 날짜 사이의 모든 날짜를 구하기 위한 DatePeriod 생성
            $date_period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date);

            // 모든 날짜를 저장할 배열
            $all_dates = array();

            // DatePeriod에서 각 날짜를 반복하여 배열에 추가
            foreach ($date_period as $date) {
                $all_dates[] = $date->format('Y-m-d');
            }
        } else if ($repeat_array['r1'] == 4) { // 매월 반복
            // 시작 날짜와 종료 날짜를 DateTime 객체로 변환
            $start_date = new DateTime($repeat_sdate);
            $end_date = new DateTime($repeat_edate);

            // 날짜 사이의 모든 날짜를 구하기 위한 DatePeriod 생성
            $date_period = new DatePeriod($start_date, new DateInterval('P1M'), $end_date);

            // 모든 날짜를 저장할 배열
            $all_dates = array();

            // DatePeriod에서 각 날짜를 반복하여 배열에 추가
            foreach ($date_period as $date) {
                $all_dates[] = $date->format('Y-m-d');
            }
        } else if ($repeat_array['r1'] == 5) { // 매년 반복
            // 시작 날짜와 종료 날짜를 DateTime 객체로 변환
            $start_date = new DateTime($repeat_sdate);
            $end_date = new DateTime($repeat_edate);

            // 날짜 사이의 모든 날짜를 구하기 위한 DatePeriod 생성
            $date_period = new DatePeriod($start_date, new DateInterval('P1Y'), $end_date);

            // 모든 날짜를 저장할 배열
            $all_dates = array();

            // DatePeriod에서 각 날짜를 반복하여 배열에 추가
            foreach ($date_period as $date) {
                $all_dates[] = $date->format('Y-m-d');
            }
        }
    }
    if ($_POST['sst_idx']) {
        // 업데이트
        if ($_POST['sst_pidx']) {
            // 반복 일정일 경우 삭제 후 다시 추가입력
            $DB->where('sst_pidx', $_POST['sst_pidx']);
            $DB->delete('smap_schedule_t');

            $DB->where('sst_idx', $_POST['sst_pidx']);
            $DB->delete('smap_schedule_t');

            foreach ($all_dates as $date) {
                $sst_schedule_alarm = '';
                $sst_sdate = $date . ' ' . $_POST['pick_stime'];
                $sst_edate = $date . ' ' . $_POST['pick_etime'];
                // 일정알림시간 구하기
                if ($_POST['sst_all_day'] == 'N') {
                    if ($_POST['sst_schedule_alarm_chk'] == 'Y') {
                        // sst_pick_result 값이 시간(minute) 단위로 전송되므로, 이 값을 정수형으로 변환하여 사용합니다.
                        $sst_pick_result = intval($_POST['sst_pick_result']);
                        // sst_sdate 값을 DateTime 객체로 변환합니다.
                        $sst_sdatetime = new DateTime($sst_sdate);
                        if (
                            $_POST['sst_pick_type'] == 'minute'
                        ) {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result minute")->format('Y-m-d H:i:s');
                        } else if ($_POST['sst_pick_type'] == 'hour') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result hour")->format('Y-m-d H:i:s');
                        } else if ($_POST['sst_pick_type'] == 'day') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result day")->format('Y-m-d H:i:s');
                        }
                    }
                }

                unset($arr_query);
                $arr_query = array(
                    "mt_idx" => $_SESSION['_mt_idx'],
                    "sst_title" => $_POST['sst_title'],
                    "sst_sdate" => $sst_sdate,
                    "sst_edate" => $sst_edate,
                    "sst_all_day" => $_POST['sst_all_day'],
                    "sst_repeat_json" => $_POST['sst_repeat_json'],
                    "sst_repeat_json_v" => $_POST['sst_repeat_json_v'],
                    "sgt_idx" => $row_sgdt['sgt_idx'],
                    "sgdt_idx" => $_POST['sgdt_idx'],
                    "sgdt_idx_t" => $_POST['sgdt_idx_t'],
                    "sst_alram" => $_POST['sst_alram'],
                    "sst_alram_t" => $_POST['sst_alram_t'],
                    "slt_idx" => $_POST['slt_idx'],
                    "slt_idx_t" => $_POST['slt_idx_t'],
                    "sst_location_title" => $_POST['sst_location_title'],
                    "sst_location_add" => $_POST['sst_location_add'],
                    "sst_location_lat" => $_POST['sst_location_lat'],
                    "sst_location_long" => $_POST['sst_location_long'],
                    "sst_supplies" => $_POST['sst_supplies'],
                    "sst_memo" => $_POST['sst_memo'],
                    "sst_show" => "Y",
                    "sst_wdate" => $DB->now(),
                    "sst_adate" => $_POST['sst_adate'],
                    "sst_location_alarm" => $_POST['sst_location_alarm'],
                    "sst_schedule_alarm_chk" => $_POST['sst_schedule_alarm_chk'],
                    "sst_pick_type" => $_POST['sst_pick_type'],
                    "sst_pick_result" => $_POST['sst_pick_result'],
                    "sst_schedule_alarm" => $sst_schedule_alarm,
                    "sst_update_chk" => $_POST['sst_update_chk'],
                    "sst_sedate" => $_POST['sst_sdate'] . ' ~ ' . $_POST['sst_edate'],
                );
                if ($_last_idx) {
                    $arr_query['sst_pidx'] = $_last_idx;
                    $DB->insert('smap_schedule_t', $arr_query);
                } else {
                    $_last_idx = $DB->insert('smap_schedule_t', $arr_query);
                }
            }
            //연락처 테이블 업데이트
            if ($_last_idx) {
                unset($arr_query);
                $arr_query = array(
                    "sst_idx" => $_last_idx,
                );
                $DB->where('sst_idx', $_POST['sst_pidx']);
                $DB->update('smap_contact_t', $arr_query);
            }
        } else {
            // 반복 일정일 경우 삭제 후 다시 추가입력
            $DB->where('sst_pidx', $_POST['sst_idx']);
            $sst_plist = $DB->get('smap_schedule_t');

            if ($sst_plist) {
                $DB->where('sst_pidx', $_POST['sst_idx']);
                $DB->delete('smap_schedule_t');

                $DB->where('sst_idx', $_POST['sst_idx']);
                $DB->delete('smap_schedule_t');
                foreach ($all_dates as $date) {
                    $sst_schedule_alarm = '';
                    $sst_sdate = $date . ' ' . $_POST['pick_stime'];
                    $sst_edate = $date . ' ' . $_POST['pick_etime'];
                    // 일정알림시간 구하기
                    if ($_POST['sst_all_day'] == 'N') {
                        if ($_POST['sst_schedule_alarm_chk'] == 'Y') {
                            // sst_pick_result 값이 시간(minute) 단위로 전송되므로, 이 값을 정수형으로 변환하여 사용합니다.
                            $sst_pick_result = intval($_POST['sst_pick_result']);
                            // sst_sdate 값을 DateTime 객체로 변환합니다.
                            $sst_sdatetime = new DateTime($sst_sdate);
                            if (
                                $_POST['sst_pick_type'] == 'minute'
                            ) {
                                // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                                $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result minute")->format('Y-m-d H:i:s');
                            } else if ($_POST['sst_pick_type'] == 'hour') {
                                // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                                $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result hour")->format('Y-m-d H:i:s');
                            } else if ($_POST['sst_pick_type'] == 'day') {
                                // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                                $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result day")->format('Y-m-d H:i:s');
                            }
                        }
                    }

                    unset($arr_query);
                    $arr_query = array(
                        "mt_idx" => $_SESSION['_mt_idx'],
                        "sst_title" => $_POST['sst_title'],
                        "sst_sdate" => $sst_sdate,
                        "sst_edate" => $sst_edate,
                        "sst_all_day" => $_POST['sst_all_day'],
                        "sst_repeat_json" => $_POST['sst_repeat_json'],
                        "sst_repeat_json_v" => $_POST['sst_repeat_json_v'],
                        "sgt_idx" => $row_sgdt['sgt_idx'],
                        "sgdt_idx" => $_POST['sgdt_idx'],
                        "sgdt_idx_t" => $_POST['sgdt_idx_t'],
                        "sst_alram" => $_POST['sst_alram'],
                        "sst_alram_t" => $_POST['sst_alram_t'],
                        "slt_idx" => $_POST['slt_idx'],
                        "slt_idx_t" => $_POST['slt_idx_t'],
                        "sst_location_title" => $_POST['sst_location_title'],
                        "sst_location_add" => $_POST['sst_location_add'],
                        "sst_location_lat" => $_POST['sst_location_lat'],
                        "sst_location_long" => $_POST['sst_location_long'],
                        "sst_supplies" => $_POST['sst_supplies'],
                        "sst_memo" => $_POST['sst_memo'],
                        "sst_show" => "Y",
                        "sst_wdate" => $DB->now(),
                        "sst_adate" => $_POST['sst_adate'],
                        "sst_location_alarm" => $_POST['sst_location_alarm'],
                        "sst_schedule_alarm_chk" => $_POST['sst_schedule_alarm_chk'],
                        "sst_pick_type" => $_POST['sst_pick_type'],
                        "sst_pick_result" => $_POST['sst_pick_result'],
                        "sst_schedule_alarm" => $sst_schedule_alarm,
                        "sst_update_chk" => $_POST['sst_update_chk'],
                        "sst_sedate" => $_POST['sst_sdate'] . ' ~ ' . $_POST['sst_edate'],
                    );
                    if ($_last_idx) {
                        $arr_query['sst_pidx'] = $_last_idx;
                        $DB->insert('smap_schedule_t', $arr_query);
                    } else {
                        $_last_idx = $DB->insert('smap_schedule_t', $arr_query);
                    }
                }
            } else {
                if ($_POST['sst_all_day'] == 'N') {
                    if ($_POST['sst_schedule_alarm_chk'] == 'Y') {
                        // sst_pick_result 값이 시간(minute) 단위로 전송되므로, 이 값을 정수형으로 변환하여 사용합니다.
                        $sst_pick_result = intval($_POST['sst_pick_result']);
                        // sst_sdate 값을 DateTime 객체로 변환합니다.
                        $sst_sdatetime = new DateTime($_POST['sst_sdate']);
                        if ($_POST['sst_pick_type'] == 'minute') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result minute")->format('Y-m-d H:i:s');
                        } else if ($_POST['sst_pick_type'] == 'hour') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result hour")->format('Y-m-d H:i:s');
                        } else if ($_POST['sst_pick_type'] == 'day') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result day")->format('Y-m-d H:i:s');
                        }
                    }
                }
                unset($arr_query);
                $arr_query = array(
                    "sst_title" => $_POST['sst_title'],
                    "sst_sdate" => $_POST['sst_sdate'],
                    "sst_edate" => $_POST['sst_edate'],
                    "sst_all_day" => $_POST['sst_all_day'],
                    "sst_repeat_json" => $_POST['sst_repeat_json'],
                    "sst_repeat_json_v" => $_POST['sst_repeat_json_v'],
                    "sgt_idx" => $row_sgdt['sgt_idx'],
                    "sgdt_idx" => $_POST['sgdt_idx'],
                    "sgdt_idx_t" => $_POST['sgdt_idx_t'],
                    "sst_alram" => $_POST['sst_alram'],
                    "sst_alram_t" => $_POST['sst_alram_t'],
                    "slt_idx" => $_POST['slt_idx'],
                    "slt_idx_t" => $_POST['slt_idx_t'],
                    "sst_location_title" => $_POST['sst_location_title'],
                    "sst_location_add" => $_POST['sst_location_add'],
                    "sst_location_lat" => $_POST['sst_location_lat'],
                    "sst_location_long" => $_POST['sst_location_long'],
                    "sst_supplies" => $_POST['sst_supplies'],
                    "sst_memo" => $_POST['sst_memo'],
                    "sst_udate" => $DB->now(),
                    "sst_adate" => $_POST['sst_adate'],
                    "sst_location_alarm" => $_POST['sst_location_alarm'],
                    "sst_schedule_alarm_chk" => $_POST['sst_schedule_alarm_chk'],
                    "sst_pick_type" => $_POST['sst_pick_type'],
                    "sst_pick_result" => $_POST['sst_pick_result'],
                    "sst_schedule_alarm" => $sst_schedule_alarm,
                    "sst_update_chk" => $_POST['sst_update_chk'],
                    "sst_sedate" => $_POST['sst_sdate'] . ' ~ ' . $_POST['sst_edate'],
                );

                $DB->where('sst_idx', $_POST['sst_idx']);

                $DB->update('smap_schedule_t', $arr_query);

                $_last_idx = $_POST['sst_idx'];
            }
        }
        //일정 확인하여 오너가 수정한지 본인이 수정한지 확인
        $DB->where('sst_idx', $_last_idx);
        $sst_row = $DB->getone('smap_schedule_t');

        $DB->where('sgdt_idx', $sst_row['sgdt_idx']);
        $sgdt_row = $DB->getone('smap_group_detail_t');

        if ($_SESSION['_mt_idx'] == $sgdt_row['mt_idx']) { //해당 일정이 본인 일정일 경우
            if ($sgdt_row['sgdt_owner_chk'] == 'Y' || $sgdt_row['sgdt_leader_chk'] == 'Y') { // 본인이 오너 or 리더일 경우
            } else { // 본인이 그룹원일 경우
                // 해당 그룹의 오너/리더 구하기
                $mem_row = get_member_t_info($sgdt_row['mt_idx']); // 그룹원 회원 정보
                $DB->where('sgt_idx', $sst_row['sgt_idx']);
                $DB->where('(sgdt_owner_chk ="Y" or sgdt_leader_chk="Y") and sgdt_exit = "N"');
                $sgdt_list = $DB->get('smap_group_detail_t');
                if ($sgdt_list) {
                    foreach ($sgdt_list as $sgdt_row_ol) {
                        unset($member_row);
                        $member_row = get_member_t_info($sgdt_row_ol['mt_idx']); // 오너/리더 회원정보
                        $plt_type = '2';
                        $sst_idx = $_last_idx;
                        $plt_condition = '그룹원이 자신의 일정 수정';
                        $plt_memo = '해당 그룹의 그룹오너/리더에게 일정이 수정되었다는 푸시알림';
                        // $mt_id = $member_row['mt_idx'];
                        $mt_id = $member_row['mt_id'] ? $member_row['mt_id'] : $member_row['mt_email'];
                        $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
                        $mem_nickname = $mem_row['mt_nickname'] ? $mem_row['mt_nickname'] : $mem_row['mt_name'];
                        $translations_schedule = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $member_row['mt_lang'] . '.php';
                        $plt_title =  $translations_schedule['txt_schedule_updated']; //일정 수정알림 ✏️
                        $plt_content = $translations_schedule['txt_schedule_updated_content'];
                        $plt_content = str_replace('{nick_name}', $mem_nickname, $plt_content);
                        $plt_content = str_replace('{sst_title}', $sst_row['sst_title'], $plt_content);

                        $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
                    }
                }
            }
        } else { // 해당 일정이 본인 일정이 아닐 경우
            // 해당 되는 사람에게 일정알림 보내기
            $owner_row = get_member_t_info($_SESSION['_mt_idx']); // 오너/리더 회원 정보
            $member_row = get_member_t_info($sgdt_row['mt_idx']); // 그룹원 회원 정보
            $plt_type = '2';
            $sst_idx = $_last_idx;
            $plt_condition = '그룹오너가 그룹원 일정 수정';
            $plt_memo = '해당 그룹원에게 일정이 수정되었다는 푸시알림';
            // $mt_id = $member_row['mt_idx'];
            $mt_id = $member_row['mt_id'] ? $member_row['mt_id'] : $member_row['mt_email'];
            $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
            $owner_nickname = $owner_row['mt_nickname'] ? $owner_row['mt_nickname'] : $owner_row['mt_name'];
            $translations_schedule = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $member_row['mt_lang'] . '.php';
            $plt_title = $translations_schedule['txt_schedule_updated']; //일정 수정알림 ✏️
            $plt_content = $translations_schedule['txt_schedule_updated_content_member'];
            $plt_content = str_replace('{nick_name}', $owner_nickname, $plt_content);
            $plt_content = str_replace('{sst_title}', $sst_row['sst_title'], $plt_content);

            $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
        }
    } else {
        // 처음 등록
        if ($repeat_array['r1'] == 1 || empty($repeat_array['r1'])) {
            // 일정알림시간 구하기
            if ($_POST['sst_all_day'] == 'N') {
                if ($_POST['sst_schedule_alarm_chk'] == 'Y') {
                    // sst_pick_result 값이 시간(minute) 단위로 전송되므로, 이 값을 정수형으로 변환하여 사용합니다.
                    $sst_pick_result = intval($_POST['sst_pick_result']);
                    // sst_sdate 값을 DateTime 객체로 변환합니다.
                    $sst_sdatetime = new DateTime($_POST['sst_sdate']);
                    if ($_POST['sst_pick_type'] == 'minute') {
                        // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                        $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result minute")->format('Y-m-d H:i:s');
                    } else if ($_POST['sst_pick_type'] == 'hour') {
                        // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                        $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result hour")->format('Y-m-d H:i:s');
                    } else if ($_POST['sst_pick_type'] == 'day') {
                        // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                        $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result day")->format('Y-m-d H:i:s');
                    }
                }
            }

            unset($arr_query);
            $arr_query = array(
                "mt_idx" => $_SESSION['_mt_idx'],
                "sst_title" => $_POST['sst_title'],
                "sst_sdate" =>  $_POST['sst_sdate'],
                "sst_edate" =>  $_POST['sst_edate'],
                "sst_all_day" => $_POST['sst_all_day'],
                "sst_repeat_json" => $_POST['sst_repeat_json'],
                "sst_repeat_json_v" => $_POST['sst_repeat_json_v'],
                "sgt_idx" => $row_sgdt['sgt_idx'],
                "sgdt_idx" => $_POST['sgdt_idx'],
                "sgdt_idx_t" => $_POST['sgdt_idx_t'],
                "sst_alram" => $_POST['sst_alram'],
                "sst_alram_t" => $_POST['sst_alram_t'],
                "slt_idx" => $_POST['slt_idx'],
                "slt_idx_t" => $_POST['slt_idx_t'],
                "sst_location_title" => $_POST['sst_location_title'],
                "sst_location_add" => $_POST['sst_location_add'],
                "sst_location_lat" => $_POST['sst_location_lat'],
                "sst_location_long" => $_POST['sst_location_long'],
                "sst_supplies" => $_POST['sst_supplies'],
                "sst_memo" => $_POST['sst_memo'],
                "sst_show" => "Y",
                "sst_wdate" => $DB->now(),
                "sst_adate" => $_POST['sst_adate'],
                "sst_location_alarm" => $_POST['sst_location_alarm'],
                "sst_schedule_alarm_chk" => $_POST['sst_schedule_alarm_chk'],
                "sst_pick_type" => $_POST['sst_pick_type'],
                "sst_pick_result" => $_POST['sst_pick_result'],
                "sst_schedule_alarm" => $sst_schedule_alarm,
                "sst_update_chk" => $_POST['sst_update_chk'],
                "sst_sedate" => $_POST['sst_sdate'] . ' ~ ' . $_POST['sst_edate'],
            );

            $_last_idx = $DB->insert('smap_schedule_t', $arr_query);
        } else {
            foreach ($all_dates as $date) {
                $sst_schedule_alarm = '';
                $sst_sdate = $date . ' ' . $_POST['pick_stime'];
                $sst_edate = $date . ' ' . $_POST['pick_etime'];
                // 일정알림시간 구하기
                if ($_POST['sst_all_day'] == 'N') {
                    if ($_POST['sst_schedule_alarm_chk'] == 'Y') {
                        // sst_pick_result 값이 시간(minute) 단위로 전송되므로, 이 값을 정수형으로 변환하여 사용합니다.
                        $sst_pick_result = intval($_POST['sst_pick_result']);
                        // sst_sdate 값을 DateTime 객체로 변환합니다.
                        $sst_sdatetime = new DateTime($sst_sdate);
                        if ($_POST['sst_pick_type'] == 'minute') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result minute")->format('Y-m-d H:i:s');
                        } else if ($_POST['sst_pick_type'] == 'hour') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result hour")->format('Y-m-d H:i:s');
                        } else if ($_POST['sst_pick_type'] == 'day') {
                            // sst_sdate로부터 sst_pick_result 시간 만큼 감산하여 알림 시간을 계산합니다.
                            $sst_schedule_alarm = $sst_sdatetime->modify("-$sst_pick_result day")->format('Y-m-d H:i:s');
                        }
                    }
                }

                unset($arr_query);
                $arr_query = array(
                    "mt_idx" => $_SESSION['_mt_idx'],
                    "sst_title" => $_POST['sst_title'],
                    "sst_sdate" => $sst_sdate,
                    "sst_edate" => $sst_edate,
                    "sst_all_day" => $_POST['sst_all_day'],
                    "sst_repeat_json" => $_POST['sst_repeat_json'],
                    "sst_repeat_json_v" => $_POST['sst_repeat_json_v'],
                    "sgt_idx" => $row_sgdt['sgt_idx'],
                    "sgdt_idx" => $_POST['sgdt_idx'],
                    "sgdt_idx_t" => $_POST['sgdt_idx_t'],
                    "sst_alram" => $_POST['sst_alram'],
                    "sst_alram_t" => $_POST['sst_alram_t'],
                    "slt_idx" => $_POST['slt_idx'],
                    "slt_idx_t" => $_POST['slt_idx_t'],
                    "sst_location_title" => $_POST['sst_location_title'],
                    "sst_location_add" => $_POST['sst_location_add'],
                    "sst_location_lat" => $_POST['sst_location_lat'],
                    "sst_location_long" => $_POST['sst_location_long'],
                    "sst_supplies" => $_POST['sst_supplies'],
                    "sst_memo" => $_POST['sst_memo'],
                    "sst_show" => "Y",
                    "sst_wdate" => $DB->now(),
                    "sst_adate" => $_POST['sst_adate'],
                    "sst_location_alarm" => $_POST['sst_location_alarm'],
                    "sst_schedule_alarm_chk" => $_POST['sst_schedule_alarm_chk'],
                    "sst_pick_type" => $_POST['sst_pick_type'],
                    "sst_pick_result" => $_POST['sst_pick_result'],
                    "sst_schedule_alarm" => $sst_schedule_alarm,
                    "sst_update_chk" => $_POST['sst_update_chk'],
                    "sst_sedate" => $_POST['sst_sdate'] . ' ~ ' . $_POST['sst_edate'],
                );
                if ($_last_idx) {
                    $arr_query['sst_pidx'] = $_last_idx;
                    $DB->insert('smap_schedule_t', $arr_query);
                } else {
                    $_last_idx = $DB->insert('smap_schedule_t', $arr_query);
                }
            }
        }
        //일정 확인하여 오너가 수정한지 본인이 수정한지 확인
        $DB->where('sst_idx', $_last_idx);
        $sst_row = $DB->getone('smap_schedule_t');

        $DB->where('sgdt_idx', $sst_row['sgdt_idx']);
        $sgdt_row = $DB->getone('smap_group_detail_t');

        if ($_SESSION['_mt_idx'] == $sgdt_row['mt_idx']) { //해당 일정이 본인 일정일 경우
            if ($sgdt_row['sgdt_owner_chk'] == 'Y' || $sgdt_row['sgdt_leader_chk'] == 'Y') { // 본인이 오너 or 리더일 경우
            } else { // 본인이 그룹원일 경우
                // 해당 그룹의 오너/리더 구하기
                $mem_row = get_member_t_info($sgdt_row['mt_idx']); // 그룹원 회원 정보
                $DB->where('sgt_idx', $sst_row['sgt_idx']);
                $DB->where('(sgdt_owner_chk ="Y" or sgdt_leader_chk="Y") and sgdt_exit = "N"');
                $sgdt_list = $DB->get('smap_group_detail_t');
                if ($sgdt_list) {
                    foreach ($sgdt_list as $sgdt_row_ol) {
                        unset($member_row);
                        $member_row = get_member_t_info($sgdt_row_ol['mt_idx']); // 오너/리더 회원정보
                        $plt_type = '2';
                        $sst_idx = $_last_idx;
                        $plt_condition = '그룹원이 자신의 일정 입력';
                        $plt_memo = '해당 그룹의 그룹오너/리더에게 일정이 생성되었다는 푸시알림';
                        // $mt_id = $member_row['mt_idx'];
                        $mt_id = $member_row['mt_id'] ? $member_row['mt_id'] : $member_row['mt_email'];
                        $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
                        $mem_nickname = $mem_row['mt_nickname'] ? $mem_row['mt_nickname'] : $mem_row['mt_name'];
                        $translations_schedule = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $member_row['mt_lang'] . '.php';
                        $plt_title = $translations_schedule['txt_schedule_created']; //일정 생성알림 ➕
                        $plt_content = $translations_schedule['txt_schedule_created_content']; //님이 새로운 일정을 생성했습니다.
                        $plt_content = str_replace('{nick_name}', $mem_nickname, $plt_content);

                        $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
                    }
                }
            }
        } else { // 해당 일정이 본인 일정이 아닐 경우
            // 해당 되는 사람에게 일정알림 보내기
            $owner_row = get_member_t_info($_SESSION['_mt_idx']); // 오너/리더 회원 정보
            $member_row = get_member_t_info($sgdt_row['mt_idx']); // 그룹원 회원 정보
            $plt_type = '2';
            $sst_idx = $_last_idx;
            $plt_condition = '그룹오너가 그룹원 일정 입력';
            $plt_memo = '해당 그룹원에게 일정이 생성되었다는 푸시알림';
            // $mt_id = $member_row['mt_idx'];
            $mt_id = $member_row['mt_id'] ? $member_row['mt_id'] : $member_row['mt_email'];
            $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
            $owner_nickname = $owner_row['mt_nickname'] ? $owner_row['mt_nickname'] : $owner_row['mt_name'];
            $translations_schedule = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $member_row['mt_lang'] . '.php';
            $plt_title = $translations_schedule['txt_schedule_created']; //일정 생성알림 ➕
            $plt_content = $translations_schedule['txt_schedule_created_content_member']; //님이 새로운 일정을 생성했습니다.
            $plt_content = str_replace('{nick_name}', $owner_nickname, $plt_content);

            $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
        }
    }
    //연락처 테이블 업데이트
    if ($_last_idx) {
        unset($arr_query);
        $arr_query = array(
            "sst_idx" => $_last_idx,
        );

        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where("sst_idx is null");

        $DB->update('smap_contact_t', $arr_query);
    }
    // p_gotourl("./schedule");
    echo "Y";
} elseif ($_POST['act'] == "schedule_delete") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sst_idx'] == '') {
        p_alert('잘못된 접근입니다. sst_idx');
    }

    unset($arr_query);
    $arr_query = array(
        "sst_show" => 'N',
        "sst_ddate" => $DB->now(),
    );

    // $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sst_idx', $_POST['sst_idx']);

    $DB->update('smap_schedule_t', $arr_query);


    //일정 확인하여 오너가 수정한지 본인이 수정한지 확인
    $DB->where('sst_idx', $_POST['sst_idx']);
    $sst_row = $DB->getone('smap_schedule_t');

    $DB->where('sgdt_idx', $sst_row['sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');

    if ($_SESSION['_mt_idx'] == $sgdt_row['mt_idx']) { //해당 일정이 본인 일정일 경우
        if ($sgdt_row['sgdt_owner_chk'] == 'Y' || $sgdt_row['sgdt_leader_chk'] == 'Y') { // 본인이 오너 or 리더일 경우
        } else { // 본인이 그룹원일 경우
            // 해당 그룹의 오너/리더 구하기
            $mem_row = get_member_t_info($sgdt_row['mt_idx']); // 그룹원 회원 정보
            $DB->where('sgt_idx', $sst_row['sgt_idx']);
            $DB->where('(sgdt_owner_chk ="Y" or sgdt_leader_chk="Y") and sgdt_exit = "N"');
            $sgdt_list = $DB->get('smap_group_detail_t');
            if ($sgdt_list) {
                foreach ($sgdt_list as $sgdt_row_ol) {
                    unset($member_row);
                    $member_row = get_member_t_info($sgdt_row_ol['mt_idx']); // 오너/리더 회원정보
                    $plt_type = '2';
                    $sst_idx = $sst_row['sst_idx'];
                    $plt_condition = '그룹원이 자신의 일정 삭제';
                    $plt_memo = '해당 그룹의 그룹오너/리더에게 일정이 삭제되었다는 푸시알림';
                    // $mt_id = $member_row['mt_idx'];
                    $mt_id = $member_row['mt_id'] ? $member_row['mt_id'] : $member_row['mt_email'];
                    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
                    $mem_nickname = $mem_row['mt_nickname'] ? $mem_row['mt_nickname'] : $mem_row['mt_name'];
                    $translations_schedule = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $member_row['mt_lang'] . '.php';
                    $plt_title = $translations_schedule['txt_schedule_deleted']; //일정 삭제 알림 ❌
                    $plt_content = $translations_schedule['txt_schedule_deleted_content'];
                    $plt_content = str_replace('{nick_name}', $mem_nickname, $plt_content);
                    $plt_content = str_replace('{sst_title}', $sst_row['sst_title'], $plt_content);

                    $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
                }
            }
        }
    } else { // 해당 일정이 본인 일정이 아닐 경우
        // 해당 되는 사람에게 일정알림 보내기
        $owner_row = get_member_t_info($_SESSION['_mt_idx']); // 오너/리더 회원 정보
        $member_row = get_member_t_info($sgdt_row['mt_idx']); // 그룹원 회원 정보
        $plt_type = '2';
        $sst_idx = $sst_row['sst_idx'];
        $plt_condition = '그룹오너가 그룹원 일정 삭제';
        $plt_memo = '해당 그룹원에게 일정이 삭제되었다는 푸시알림';
        // $mt_id = $member_row['mt_idx'];
        $mt_id = $member_row['mt_id'] ? $member_row['mt_id'] : $member_row['mt_email'];
        $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
        $owner_nickname = $owner_row['mt_nickname'] ? $owner_row['mt_nickname'] : $owner_row['mt_name'];
        $translations_schedule = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $member_row['mt_lang'] . '.php';
        $plt_title = $translations_schedule['txt_schedule_deleted']; //일정 삭제 알림 ❌
        $plt_content = $translations_schedule['txt_schedule_deleted_content_member'];
        $plt_content = str_replace('{nick_name}', $owner_nickname, $plt_content);
        $plt_content = str_replace('{sst_title}', $sst_row['sst_title'], $plt_content);

        $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
    }

    echo "Y";
} elseif ($_POST['act'] == "calendar_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    if ($_POST['sgdt_mt_idx']) {
        $mt_idx_t = $_POST['sgdt_mt_idx'];
    } else {
        $mt_idx_t = $_SESSION['_mt_idx'];
    }

    if ($_POST['sdate']) {
        $sdate = $_POST['sdate'];
    } else {
        $sdate = date('Y-m-d');
    }

    if ($_POST['week_chk'] == 'Y') {
        $end_cnt = '7';
    } else {
        $end_cnt = '42';

        $sdate = date('Y-m-01', strtotime($sdate));
    }

    $get_first_date_week = date('w', make_mktime($sdate));
    $get_first_date = date('Y-m-d', strtotime($sdate . " -" . $get_first_date_week . "days"));

    if ($_POST['sgdt_mt_idx']) {
        $mt_idx_t = $_POST['sgdt_mt_idx'];
    } else {
        $mt_idx_t = $_SESSION['_mt_idx'];
    }

    $arr_data = array();

    if ($sdate) {
        $_POST['start'] = date('Y-m-01', make_mktime($sdate));
        $_POST['end'] = date('Y-m-t', make_mktime($sdate));
    }

    //내가 등록한 일정
    unset($list);
    $DB->where('mt_idx', $mt_idx_t);
    $DB->where("( sst_sdate >= '" . $_POST['start'] . " 00:00:00' and sst_edate <= '" . $_POST['end'] . " 23:59:59' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $cd = cal_remain_days2($row['sst_sdate'], $row['sst_edate']);

                if ($cd) {
                    for ($q = 0; $q < $cd; $q++) {
                        $sdate_t = date("Y-m-d", strtotime($row['sst_sdate'] . " +" . $q . " days"));
                        $arr_data[$sdate_t] = array(
                            'id' => $row['sst_idx'],
                            'start' => $sdate_t,
                            'end' => $row['sst_edate'],
                            'title' => $row['sst_title'],
                        );
                    }
                }
            }
        }
    }

    //나에게 등록된 일정
    $DB->where('mt_idx', $mt_idx_t);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_show', 'Y');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx, GROUP_CONCAT(sgdt_idx) as gc_sgdt_idx, sgdt_owner_chk, sgdt_leader_chk');

    if ($row_sgdt['gc_sgt_idx']) {
        unset($list);
        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where("sgdt_idx in (" . $row_sgdt['gc_sgdt_idx'] . ")");
        $DB->where(" ( sst_sdate >= '" . $_POST['start'] . " 00:00:00' and sst_edate <= '" . $_POST['end'] . " 23:59:59' )");
        $DB->where('sst_show', 'Y');
        $list = $DB->get('smap_schedule_t');

        if ($list) {
            foreach ($list as $row) {
                if ($row['sst_title']) {
                    $cd = cal_remain_days2($row['sst_sdate'], $row['sst_edate']);

                    if ($cd) {
                        for ($q = 0; $q < $cd; $q++) {
                            $sdate_t = date("Y-m-d", strtotime($row['sst_sdate'] . " +" . $q . " days"));
                            $arr_data[$sdate_t] = array(
                                'id' => $row['sst_idx'],
                                'start' => $sdate_t,
                                'end' => $row['sst_edate'],
                                'title' => $row['sst_title'],
                            );
                        }
                    }
                }
            }
        }
    }
    // 내가 오너라면 모든이들의 일정 확인한번 더 하기
    if ($row_sgdt == 'Y') {
        unset($list);
        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where(" ( sst_sdate >= '" . $_POST['start'] . " 00:00:00' and sst_edate <= '" . $_POST['end'] . " 23:59:59' )");
        $DB->where('sst_show', 'Y');
        $list = $DB->get('smap_schedule_t');

        if ($list) {
            foreach ($list as $row) {
                if ($row['sst_title']) {
                    $cd = cal_remain_days2($row['sst_sdate'], $row['sst_edate']);

                    if ($cd) {
                        for ($q = 0; $q < $cd; $q++) {
                            $sdate_t = date("Y-m-d", strtotime($row['sst_sdate'] . " +" . $q . " days"));
                            $arr_data[$sdate_t] = array(
                                'id' => $row['sst_idx'],
                                'start' => $sdate_t,
                                'end' => $row['sst_edate'],
                                'title' => $row['sst_title'],
                            );
                        }
                    }
                }
            }
        }
    }

    ?>

    <form>
        <div class="date_conent">
            <div class="cld_content">
                <div class="cld_body fs_15 fw_500">
                    <ul>
                        <?php
                        for ($d = 0; $d < $end_cnt; $d++) {
                            $c_id = date("Y-m-d", strtotime($get_first_date . " +" . $d . "days"));
                            $c_id2 = date("j", strtotime(date($c_id))); //일
                            $c_id3 = date("w", strtotime(date($c_id))); //요일
                            $c_id4 = date("n", strtotime(date($c_id))); //월
                            $c_id5 = date("n", strtotime(date($sdate))); //월

                            if ($c_id3 == '0') {
                                $week_c = ' sun';
                            } elseif ($c_id3 == '6') {
                                $week_c = ' sat';
                            } else {
                                $week_c = '';
                            }

                            if ($c_id == date("Y-m-d")) {
                                $today_c = ' today';
                            } else {
                                $today_c = '';
                            }

                            if ($arr_data[$c_id]) {
                                $schdl_c = ' schdl';
                            } else {
                                $schdl_c = '';
                            }

                            if ($c_id4 == $c_id5) {
                                echo '<li onclick="f_day_click(\'' . $c_id . '\');"><div id="calendar_' . $c_id . '" class="c_id ' . $week_c . $today_c . $schdl_c . '"><span>' . $c_id2 . '</span></div></li>';
                            } else {
                                echo '<li onclick="f_day_click(\'' . $c_id . '\');"><div id="calendar_' . $c_id . '" class="c_id lastday"><span>' . $c_id2 . '</span></div></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </form>
<? } elseif ($_POST['act'] == "group_member_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    $sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); // 오너인 그룹수
    $DB->where('sgdt_idx', $_POST['group_sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

    unset($list_sgt);
    $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
    $DB->where('sgt_show', 'Y');
    $DB->orderBy("sgt_udate", "desc");
    $DB->orderBy("sgt_idx", "asc");
    $list_sgt = $DB->get('smap_group_t');

    $DB->where('sgdt_idx', $sgdt_row['sgdt_idx']);
    $DB->where('sgdt_show', 'Y');
    $sgdt_row = $DB->getone('smap_group_detail_t');

    $mllt_row = get_member_location_log_t_info($sgdt_row['mt_idx']);

?>

    <!-- 프로필 tab_scroll scroll_bar_x -->
    <div class="mem_wrap mem_swiper">
        <div class="swiper-wrapper d-flex">
            <!-- 사용자 본인 -->
            <!-- <div class="swiper-slide checks mem_box">
                <label>
                    <input type="radio" name="rd2" <?= $sgdt_row['sgdt_owner_chk'] == 'Y' ? 'checked' : '' ?> onclick="mem_schedule(<?= $sgdt_row['sgdt_idx'] ?>, <?= $mllt_row['mlt_lat'] ?>, <?= $mllt_row['mlt_long'] ?>);">
                    <div class="prd_img mx-auto">
                        <div class="rect_square rounded_14">
                            <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="프로필이미지" />
                        </div>
                    </div>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                </label>
            </div> -->

            <?php
            if ($list_sgt) {
                foreach ($list_sgt as $row_sgt) {
                    $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
                    unset($list_sgdt);
                    $list_sgdt = get_sgdt_member_lists($row_sgt['sgt_idx']);
                    $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
                    if ($invite_cnt || $list_sgdt['data']) {
                        if ($list_sgdt['data']) {
                            foreach ($list_sgdt['data'] as $key => $val) {
            ?>
                                <div class="swiper-slide checks mem_box">
                                    <label>
                                        <input type="radio" name="rd2" <?= $val['sgdt_owner_chk'] == 'Y' ? 'checked' : '' ?> onclick="mem_schedule(<?= $val['sgdt_idx'] ?>, <?= $val['mt_lat'] ?>, <?= $val['mt_long'] ?>);">
                                        <div class="prd_img mx-auto">
                                            <div class="rect_square rounded_14">
                                                <img src="<?= $val['mt_file1_url'] ?>" alt="프로필이미지" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                                            </div>
                                        </div>
                                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                                    </label>
                                </div>
            <?php
                            }
                        }
                    }
                }
            }
            ?>

            <!-- 그룹원 추가 -->
            <?php if ($sgt_cnt > 0) { ?>
                <div class="swiper-slide mem_box add_mem_box" onclick="location.href='./group'">
                    <button class="btn mem_add">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_4 text_dynamic"><?= $translations['txt_invite_group_members'] ?></p>
                </div>
            <?php } else { ?>
                <div class="swiper-slide mem_box add_mem_box" style="visibility: hidden;">
                    <button class="btn mem_add">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_4 text_dynamic"><?= $translations['txt_invite_group_members'] ?></p>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        // Swiper 초기화
        var mem_swiper = new Swiper('.mem_swiper', {
            slidesPerView: 'auto',
            spaceBetween: 12,
        });
    </script>


    <? } elseif ($_POST['act'] == "member_schedule_list") {
    if (!isset($_SESSION['_mt_idx']) || empty($_SESSION['_mt_idx'])) {
        echo "<script>alert('" . $_SESSION['msg'] . "');location.replace('/login.php');</script>";
        exit;
    }

    if (empty($_SESSION['_mt_idx'])) {
        p_alert($translations['txt_login_required'], './login', '');
        exit;
    }

    if (!isset($DB) || !$DB) {
        echo json_encode(['error' => '데이터베이스 연결 오류']);
        exit;
    }

    function getBatteryInfo($percentage)
    {
        if ($percentage >= 80) {
            return ['color' => '#4CAF50', 'image' => './img/battery_green.png', 'percentage' => $percentage];
        } elseif ($percentage >= 50) {
            return ['color' => '#FFC107', 'image' => './img/battery_yellow.png', 'percentage' => $percentage];
        } else {
            return ['color' => '#FF204E', 'image' => './img/battery_red.png', 'percentage' => $percentage];
        }
    }

    function getSchedules($sgdt_idx, $event_start_date, $mt_idx)
    {
        global $DB;

        //나의 일정
        unset($list);
        if ($sgdt_idx != '') {
            $DB->where('sgdt_idx', $sgdt_idx);
        } else {
            $DB->where('mt_idx', $mt_idx);
        }
        $DB->where(" ( sst_sdate <= '" . $event_start_date . " 23:59:59' and sst_edate >= '" . $event_start_date . " 00:00:00' )");
        $DB->where('sst_show', 'Y');
        $DB->orderBy('sst_sdate', 'ASC');
        $list = $DB->get('smap_schedule_t');

        return $list;
    }

    if ($_POST['sgdt_idx'] != '' && $_POST['sgdt_idx'] != 'undefined') {
        // 그룹 상세 정보 조회
        $DB->where('sgdt_idx', $_POST['sgdt_idx']);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $sgdt_row = $DB->getone('smap_group_detail_t');

        // logToFile("[" . date("Y-m-d H:i:s") . "] sgdt_row: " . print_r($sgdt_row, true));

        if (!$sgdt_row) {
            echo json_encode(['error' => '그룹 상세 정보를 찾을 수 없습니다.']);
            exit;
        }

        // 그룹 멤버 정보 조회
        $DB->where('sgt_idx', $sgdt_row['sgt_idx']);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->orderBy('sgdt_owner_chk', 'ASC');
        $DB->orderBy('sgdt_leader_chk', 'ASC');
        $DB->orderBy('sgdt_wdate', 'ASC');
        $sgdt_list = $DB->get('smap_group_detail_t');

        // logToFile("[" . date("Y-m-d H:i:s") . "] sgdt_list: " . print_r($sgdt_list, true));
    }
    $owner_count = 0;
    if ($sgdt_list) {
        foreach ($sgdt_list as $member) {
            if ($member['sgdt_owner_chk'] == 'Y') {
                $owner_count++;
            }
        }
    }
    $result = ['result' => 'N', 'sgdt_idx' => $sgdt_row['sgdt_idx'], 'members' => [], 'owner_count' => $owner_count]; // 결과를 저장할 배열, result 값 초기화


    // 캐시 설정 (예: Redis 사용)
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);

    // 캐시 키 생성을 위한 함수
    function getCacheKey($mt_idx) {
        return "location_info:{$mt_idx}";
    }

    // 배치로 모든 멤버의 위치 정보를 가져오는 함수
    function batchGetLocationInfo($DB, $member_indices) {
        $query = "SELECT mlt.mt_idx, mlt.mlt_battery, mlt.mlt_speed, mlt.mlt_lat, mlt.mlt_long
                FROM member_location_log_t mlt
                INNER JOIN (
                    SELECT mt_idx, MAX(mlt_gps_time) as max_time
                    FROM member_location_log_t
                    WHERE mt_idx IN (" . implode(',', $member_indices) . ")
                    GROUP BY mt_idx
                ) latest ON mlt.mt_idx = latest.mt_idx AND mlt.mlt_gps_time = latest.max_time";
        
        return $DB->rawQuery($query);
    }

    if ($sgdt_list && $sgdt_list != '') {
        $member_indices = array_column($sgdt_list, 'mt_idx');
        $cached_locations = [];
        $uncached_indices = [];

        // 캐시에서 위치 정보 확인
        foreach ($member_indices as $mt_idx) {
            $cache_key = getCacheKey($mt_idx);
            $cached_data = $redis->get($cache_key);
            if ($cached_data) {
                $cached_locations[$mt_idx] = json_decode($cached_data, true);
            } else {
                $uncached_indices[] = $mt_idx;
            }
        }

        // 캐시되지 않은 위치 정보 배치로 가져오기
        if (!empty($uncached_indices)) {
            $fresh_locations = batchGetLocationInfo($DB, $uncached_indices);
            foreach ($fresh_locations as $location) {
                $mt_idx = $location['mt_idx'];
                $cache_key = getCacheKey($mt_idx);
                $redis->setex($cache_key, 300, json_encode($location)); // 5분 동안 캐시
                $cached_locations[$mt_idx] = $location;
            }
        }

        foreach ($sgdt_list as $sgdt_member) {
            // 멤버 언어 업데이트
            if ($_SESSION['mt_idx'] == $sgdt_member['mt_idx']) {
                $DB->where('mt_idx', $sgdt_member['mt_idx']);
                $DB->update('member_t', ['mt_lang' => $_POST['mt_lang']]);
            }

            // 멤버 정보 조회
            $DB->where('mt_idx', $sgdt_member['mt_idx']);
            $member_info = $DB->getone('member_t', 'mt_idx, mt_name, mt_nickname, mt_sido, mt_gu, mt_dong, mt_file1, mt_lat, mt_long, mt_lang');
            $member_info['my_profile'] = $member_info['mt_file1'] == "" ? $ct_no_img_url : get_image_url($member_info['mt_file1']);
            $member_info['sgdt_idx'] = $sgdt_member['sgdt_idx'];

            // logToFile("[" . date("Y-m-d H:i:s") . "] member_info: " . print_r($member_info, true));
            
            // 캐시된 위치 정보 사용
            $location_info = $cached_locations[$sgdt_member['mt_idx']] ?? null;

            // logToFile("[" . date("Y-m-d H:i:s") . "] location_info: " . print_r($location_info, true));

            // 위치 정보가 없는 경우 빈 배열로 초기화
            if (!$location_info) {
                $location_info = [
                    'mlt_battery' => 0,
                    'mlt_speed' => 0,
                    'mlt_lat' => $member_info['mt_lat'],
                    'mlt_long' => $member_info['mt_long']
                ];
            } else {
                $member_info['mt_lat'] = $location_info['mlt_lat'];
                $member_info['mt_long'] = $location_info['mlt_long'];
            }

            // 배터리 정보 조회
            $battery_info = getBatteryInfo(intval($location_info['mlt_battery'] ?? 0));

            // 일정 정보 조회
            $schedules = getSchedules($sgdt_member['sgdt_idx'], $_POST['event_start_date'], $sgdt_member['mt_idx']);

            // 경로 데이터 조회
            $arr_sst_idx = get_schedule_main($sgdt_member['sgdt_idx'], $_POST['event_start_date'], $sgdt_member['mt_idx']);
            $schedule_count = count($arr_sst_idx);
            $arr_sst_date = get_schedule_date($sgdt_member['sgdt_idx'], $_POST['event_start_date'], $sgdt_member['mt_idx']);

            // result_data['members'] 배열 초기화 (sgdt_idx를 키로 사용)
            $result['members'][$sgdt_member['sgdt_idx']] = [
                'result' => 'N',
                'sllt_json_text' => null,
                'sllt_json_walk' => null,
                'member_info' => $member_info,
                'location_info' => $location_info,
                'battery_info' => $battery_info,
                'schedules' => $schedules,
            ];

            if (!empty($arr_sst_date)) {
                $latest_date = max($arr_sst_date);
                $wdate = date('Y-m-d');
                $DB->where('sgdt_idx', $sgdt_member['sgdt_idx']);
                $DB->where('sllt_date', $wdate);
                $DB->orderby('sllt_wdate', 'desc');
                $sllt_row = $DB->getone('smap_loadpath_log_t');

                $result['result'] = 'Y'; // 전체 결과도 Y로 변경

                // logToFile($sllt_row['sllt_schedule_count'] . ' ' . count($arr_sst_date));
                if ($sllt_row && $sllt_row['sllt_schedule_count'] === count($arr_sst_date) - 1) {    
                    $result['members'][$sgdt_member['sgdt_idx']]['result'] = 'Y'; // 멤버별 결과도 Y로 변경
                    $result['members'][$sgdt_member['sgdt_idx']]['sllt_json_text'] = $sllt_row['sllt_json_text'];
                    $result['members'][$sgdt_member['sgdt_idx']]['sllt_json_walk'] = $sllt_row['sllt_json_walk'];
                } else {
                    $result['members'][$sgdt_member['sgdt_idx']]['result'] = 'N'; // 멤버별 결과도 N로 변경
                    $result['members'][$sgdt_member['sgdt_idx']]['sllt_json_text'] = null;
                    $result['members'][$sgdt_member['sgdt_idx']]['sllt_json_walk'] = null;
                }
            }

            if ($member_info) {
                $result['result'] = 'Y'; // 전체 결과도 Y로 변경
            }
        }
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $member_info = $DB->getone('member_t', 'mt_idx, mt_name, mt_sido, mt_gu, mt_dong, mt_file1, mt_lat, mt_long');
        $member_info['sgdt_idx'] = '';
        
        // 위치 정보 (캐시 사용)
        $cache_key = getCacheKey($_SESSION['_mt_idx']);
        $cached_location = $redis->get($cache_key);
        if ($cached_location) {
            $location_info = json_decode($cached_location, true);
        } else {
            $DB->where('mt_idx', $_SESSION['_mt_idx']);
            $DB->orderby('mlt_gps_time', 'desc');
            $location_info = $DB->getone('member_location_log_t', 'mlt_battery, mlt_speed, mlt_lat, mlt_long');
            if ($location_info) {
                $redis->setex($cache_key, 300, json_encode($location_info)); // 5분 동안 캐시
            }
        }

        // 배터리 정보
        $battery_info = getBatteryInfo(intval($location_info['mlt_battery'] ?? 0));

        // 일정 정보
        $schedules = getSchedules($_POST['sgdt_idx'], $_POST['event_start_date'], $_SESSION['_mt_idx']);    

        // 위치 정보가 없는 경우 빈 배열로 초기화
        if (!$location_info) {
            $location_info = [
                'mlt_battery' => 0,
                'mlt_speed' => 0,
                'mlt_lat' => $member_info['mt_lat'],
                'mlt_long' => $member_info['mt_long']
            ];
        }
        $result['members'][$_SESSION['_mt_idx']] = [
            'result' => 'N',
            'sllt_json_text' => null,
            'sllt_json_walk' => null,
            'member_info' => $member_info,
            'location_info' => $location_info,
            'battery_info' => $battery_info,
            'schedules' => $schedules,
        ];
        if ($member_info) {
            $result['result'] = 'Y'; // 전체 결과도 Y로 변경
            $result['members'][$_SESSION['_mt_idx']]['member_info']['mt_lat'] = $location_info['mlt_lat'] == "" ? $member_info['mt_lat'] : $location_info['mlt_lat'];
            $result['members'][$_SESSION['_mt_idx']]['member_info']['mt_long'] = $location_info['mlt_long'] == "" ? $member_info['mt_long'] :  $location_info['mlt_long'];
            $result['members'][$_SESSION['_mt_idx']]['member_info']['my_profile'] = $member_info['mt_file1'] == "" ? $ct_no_img_url : get_image_url($member_info['mt_file1']);
        }
    }

    

    // logToFile("result: " . print_r($result, true));

    // JSON으로 데이터 반환
    echo json_encode($result);
    exit;
} elseif ($_POST['act'] == "member_location_reload") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sgdt_idx']) {
        $DB->where('sgdt_idx', $_POST['sgdt_idx']);
        $sgdt_row = $DB->getone('smap_group_detail_t');
        if ($sgdt_row) {
            $DB->where('mt_idx', $sgdt_row['mt_idx']);
            $mem_row = $DB->getone('member_t');

            $DB->where('mt_idx', $mem_row['mt_idx']);
            $DB->orderby('mlt_gps_time', 'desc');
            $mt_location_info = $DB->getone('member_location_log_t');

            $battery_percentage = isset($mt_location_info['mlt_battery']) ? intval($mt_location_info['mlt_battery']) : 0;
            $battery_color = '';

            if ($battery_percentage >= 80) {
                $battery_color = '#4CAF50'; // 초록색 계열
                $battery_image = './img/battery_green.png';
            } elseif ($battery_percentage >= 50) {
                $battery_color = '#FFC107'; // 노란색 계열
                $battery_image = './img/battery_yellow.png';
            } else {
                $battery_color = '#FF204E'; // 빨간색 계열
                $battery_image = './img/battery_red.png';
            }

            function generateAddress($mem_row) {
                $components = [
                    $mem_row['mem_sido'],
                    $mem_row['mt_gu'],
                    $mem_row['mt_dong']
                ];
            
                $address = '';
                $seen = [];
            
                foreach ($components as $component) {
                    $parts = explode(' ', trim($component));
                    foreach ($parts as $part) {
                        if (!empty($part) && !in_array($part, $seen)) {
                            $address .= $part . ' ';
                            $seen[] = $part;
                        }
                    }
                }
            
                $address = rtrim($address);  // 마지막 공백 제거
            
                return $address;
            }
            
            // 사용 예시
            $mem_row = [
                'mem_sido' => $mem_row['mem_sido'],
                'mt_gu' => $mem_row['mt_gu'],
                'mt_dong' => $mem_row['mt_dong']
            ];
            
            $address = generateAddress($mem_row);
    ?>
            <div class="border-bottom  pb-3">
                <div class="task_header_tit">
                    <p class="fs_16 fw_600 line_h1_2 mr-3"><?= $translations['txt_current_location'] ?></p>
                    <div class="d-flex align-items-center justify-content-end">
                        <!-- <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span> <?= round($mt_location_info['mlt_speed']) ?>km/h<? } ?></p> -->
                        <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?><?= $translations['txt_moving'] ?></span><? } ?></p>
                        <!-- <p class="d-flex bettery_txt fs_13"><span class="d-flex align-items-center flex-shrink-0 mr-2"><img src="./img/battery.png?v=20240404" width="14px" class="battery_img" alt="베터리시용량"></span> <?= $mt_location_info['mlt_battery'] ?>%</p> -->
                        <p class="d-flex fs_13">
                            <span class="d-flex align-items-center flex-shrink-0 mr-2">
                                <img src="<?= $battery_image; ?>" width="14px" class="battery_img" alt="베터리시용량">
                            </span>
                            <span style="color: <?= $battery_color; ?>"><?= $battery_percentage; ?>%</span>
                        </p>
                    </div>
                </div>
                <p class="fs_14 fw_500 text_light_gray text_dynamic line_h1_3 mt-2"><?= $address ?></p>
            </div>
        <?php
        }
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $mem_row = $DB->getone('member_t');

        $DB->where('mt_idx', $mem_row['mt_idx']);
        $DB->orderby('mlt_gps_time', 'desc');
        $mt_location_info = $DB->getone('member_location_log_t');

        $battery_percentage = intval($mt_location_info['mlt_battery']);
        $battery_color = '';

        if ($battery_percentage >= 80) {
            $battery_color = '#4CAF50'; // 초록색 계열
            $battery_image = './img/battery_green.png';
        } elseif ($battery_percentage >= 50) {
            $battery_color = '#FFC107'; // 노란색 계열
            $battery_image = './img/battery_yellow.png';
        } else {
            $battery_color = '#FF204E'; // 빨간색 계열
            $battery_image = './img/battery_red.png';
        }
        ?>
        <div class="border-bottom  pb-3">
            <div class="task_header_tit">
                <p class="fs_16 fw_600 line_h1_2 mr-3"><?= $translations['txt_current_location'] ?></p>
                <div class="d-flex align-items-center justify-content-end">
                    <!-- <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span> <?= round($mt_location_info['mlt_speed']) ?>km/h<? } ?></p> -->
                    <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?><?= $translations['txt_moving'] ?></span><? } ?></p>
                    <!-- <p class="d-flex bettery_txt fs_13"><span class="d-flex align-items-center flex-shrink-0 mr-2"><img src="./img/battery.png?v=20240404" width="14px" class="battery_img" alt="베터리시용량"></span> <?= $mt_location_info['mlt_battery'] ?>%</p> -->
                    <p class="d-flex fs_13">
                        <span class="d-flex align-items-center flex-shrink-0 mr-2">
                            <img src="<?= $battery_image; ?>" width="14px" class="battery_img" alt="베터리시용량">
                        </span>
                        <span style="color: <?= $battery_color; ?>"><?= $battery_percentage; ?>%</span>
                    </p>
                </div>
            </div>
            <p class="fs_14 fw_500 text_light_gray text_dynamic line_h1_3 mt-2"><?= $mem_row['mt_sido'] . ' ' . $mem_row['mt_gu'] . ' ' . $mem_row['mt_dong'] ?></p>
        </div>
<?php
    }
} elseif ($_POST['act'] == "schedule_map_list") {
    define('CACHE_EXPIRE_TIME', 120); // 2분
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    $cache_key = "schedule_map_list_" . $_POST['sgdt_idx'] . "_" . $_SESSION['_mt_idx'];
    $cached_data = CacheUtil::get($cache_key);
    if ($cached_data) {
        echo json_encode($cached_data);
        exit;
    }

    // 함수 정의: 그룹 상세 정보 조회
    function get_group_detail($sgdt_idx)
    {
        global $DB;
        $DB->where('sgdt_idx', $sgdt_idx);
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        return $DB->getone('smap_group_detail_t');
    }

    // 함수 정의: 그룹원 리스트 조회
    function get_group_members($sgt_idx)
    {
        global $DB;
        $DB->where('sgt_idx', $sgt_idx);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        return $DB->get('smap_group_detail_t');
    }

    // 함수 정의: 회원 데이터 조회 및 캐싱
    function get_member_data($mt_idx)
    {
        global $DB, $ct_no_img_url;
        $cache_key = "member_data_" . $mt_idx;
        $cached_data = CacheUtil::get($cache_key);

        if ($cached_data) {
            $DB->where('mt_idx', $mt_idx);
            $DB->where('mt_update_dt', $cached_data['last_update'], '>');
            $updated = $DB->getOne('member_t', 'COUNT(*) as count');
            if ($updated['count'] == 0) {
                return $cached_data;
            }
        }

        $DB->where('mt_idx', $mt_idx);
        $mem_row = $DB->getone('member_t');
        $DB->where('mt_idx', $mt_idx);
        $DB->orderby('mlt_gps_time', 'desc');
        $mt_location_info = $DB->getone('member_location_log_t');

        $current_time = date('Y-m-d H:i:s');
        $member_data = [
            "lat" => $mt_location_info['mlt_lat'] ?? $mem_row['mt_lat'],
            "long" => $mt_location_info['mlt_long'] ?? $mem_row['mt_long'],
            "profile" => $mem_row['mt_file1'] ? get_image_url($mem_row['mt_file1']) : $ct_no_img_url,
            "last_update" => $current_time
        ];

        $DB->where('mt_idx', $mt_idx);
        $DB->update('member_t', ['mt_update_dt' => $current_time]);
        CacheUtil::set($cache_key, $member_data, 60); // 1분 캐시
        return $member_data;
    }

    $sgdt_row = get_group_detail($_POST['sgdt_idx']);
    $result_data = [];

    if ($sgdt_row) {
        $my_data = get_member_data($sgdt_row['mt_idx']);
    } else {
        $my_data = get_member_data($_SESSION['_mt_idx']);
    }

    $result_data = [
        "mt_lat" => $my_data['lat'],
        "mt_long" => $my_data['long'],
        "my_profile" => $my_data['profile'],
    ];

    $sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']);
    $sgdt_leader_cnt = f_get_leader_cnt($_SESSION['_mt_idx']);
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        $sgdt_list = get_group_members($sgdt_row['sgt_idx']);
        if ($sgdt_list) {
            $profile_count = 1;
            foreach ($sgdt_list as $sgdtg_row) {
                if ($sgdtg_row['sgdt_idx'] == $sgdt_row['sgdt_idx']) continue;

                $member_data = get_member_data($sgdtg_row['mt_idx']);
                $result_data["profilemarkerLat_$profile_count"] = $member_data['lat'];
                $result_data["profilemarkerLong_$profile_count"] = $member_data['long'];
                $result_data["profilemarkerImg_$profile_count"] = $member_data['profile'];
                $profile_count++;
            }
            $result_data['profile_count'] = $profile_count - 1;
        }
    }

    $arr_sst_idx = get_schedule_main($_POST['sgdt_idx'], $_POST['event_start_date'], $sgdt_row['mt_idx']);
    $cnt = count($arr_sst_idx);
    if ($cnt < 1) {
        $result_data['schedule_chk'] = 'N';
    } else {
        $arr_sst_idx_im = implode(',', $arr_sst_idx);
        $DB->where("sst_idx IN ($arr_sst_idx_im)");
        $DB->where('sst_show', 'Y');
        $DB->orderBy("sst_all_day", "asc");
        $DB->orderBy("sst_sdate", "asc");
        $DB->orderBy("sst_edate", "asc");
        $list_sst = $DB->get('smap_schedule_t');

        if ($list_sst) {
            $count = 1;
            $current_date = date('Y-m-d H:i:s');
            $color_sets = [
                ['#E6F2FF', '#E0F0FF'],
                ['#D6E6FF', '#E0E6FF'],
                ['#E5F1FF', '#E0F0FF'],
                ['#F0F8FF', '#E6F0FF'],
                ['#E0FFFF', '#E6FFFF'],
                ['#E6F2FF', '#E0EDFF'],
                ['#D6E6FF', '#E0E0FF'],
                ['#E5F1FF', '#E0EDFF'],
                ['#F0F8FF', '#E6EDFF'],
                ['#E0FFFF', '#E6FEFF'],
            ];

            $random_set = $color_sets[array_rand($color_sets)];
            $color1 = $random_set[0];
            $color2 = $random_set[1];

            foreach ($list_sst as $row_sst_a) {
                $mt_info = get_member_t_info($row_sst_a['mt_idx']);
                $mt_file1_url = get_image_url($mt_info['mt_file1']);

                $sst_sdate_e1 = get_date_ttime($row_sst_a['sst_sdate']);
                $sst_sdate_e2 = get_date_ttime($row_sst_a['sst_edate']);
                $sst_all_day_t = ($row_sst_a['sst_all_day'] == 'Y') ? $translations['all_day'] : "$sst_sdate_e1 ~ $sst_sdate_e2";

                $status = ($row_sst_a['sst_all_day'] == 'Y' || ($current_date >= $row_sst_a['sst_sdate'] && $current_date <= $row_sst_a['sst_edate'])) ? 'point_ing' : (($current_date >= $row_sst_a['sst_edate']) ? 'point_done' : 'point_gonna');
                $point_class = ($status == 'point_ing') ? 'point2' : (($status == 'point_done') ? 'point1' : 'point3');

                $content = <<<HTML
                <style>
                .infobox5 {
                    position: absolute;
                    left: 50%;
                    top: 100%;
                    transform: translate(10%, -50%);
                    background-color: #413F4A;
                    padding: 0.3rem 0.8rem;
                    border-radius: 0.4rem;
                    z-index: 1;
                    display: inline-block;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    margin-top: 0.4rem;
                    display: none;  /* 기본적으로 숨김 */ 
                }
                .infobox5 span {
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
                .infobox5 .title {
                    color: $color1;
                    display: block;
                    width: 100%;
                    margin-bottom: 0.1rem;
                    font-size: 12px !important;
                    font-weight: 800 !important;
                }
                .infobox5 .date-wrapper {
                    display: flex;
                    flex-direction: column;
                    align-items: flex-start;
                }
                .infobox5 .date {
                    color: $color2;
                    margin-bottom: 0;
                    font-size: 8px !important;
                    font-weight: 700 !important;
                }
                .infobox5 .date + .date {
                    margin-top: 0.05rem;
                }
                .infobox5.on {
                    display: inline-block;  /* .on 클래스가 추가되면 표시 */
                }
                </style>
                <div class="point_wrap {$point_class}">
                    <button type="button" class="btn point {$status}">
                        <span class="point_inner">
                            <span class="point_txt">{$count}</span>
                        </span>
                    </button>
                    <div class="infobox5 rounded_04 px_08 py_03 on">
                        <span class="title">{$row_sst_a['sst_title']}</span>
                        <div class="date-wrapper">
                            <span class="date">S: {$sst_sdate_e1}</span>
                            <span class="date">E: {$sst_sdate_e2}</span>
                        </div>
                    </div>
                </div>
                HTML;

                $result_data["markerLat_$count"] = $row_sst_a['sst_location_lat'];
                $result_data["markerLong_$count"] = $row_sst_a['sst_location_long'];
                $result_data["markerContent_$count"] = $content;
                $result_data["markerStatus_$count"] = $status;

                $result_data["markerPointClass_$count"] = $point_class;
                $result_data["markerPointSstTitle_$count"] = $row_sst_a['sst_title'];
                $result_data["markerPointSstSdateE1_$count"] = $sst_sdate_e1;
                $result_data["markerPointSstSdateE2_$count"] = $sst_sdate_e2;
                $count++;
            }
        }
        $result_data['schedule_chk'] = 'Y';
        $result_data['count'] = $count - 1;
        $result_data['sgdt_idx'] = $_POST['sgdt_idx'];
    }

    CacheUtil::set($cache_key, $result_data, CACHE_EXPIRE_TIME);
    echo json_encode($result_data);
    exit;
} elseif ($_POST['act'] == "load_path_chk") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    // 회원구분
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($mem_row) {
        if ($mem_row['mt_level'] == 5) {
            $mt_type = 'Plus';
            $path_count_day = 10;
            $path_count_month = 300;
            $ad_count = 0;
        } else {
            $mt_type = 'Basic';
            $path_count_day = 2;
            $path_count_month = 60;

            // 무료회원일 경우 광고 카운트 확인하기
            $ad_row = get_ad_log_check($_SESSION['_mt_idx']);
            $ad_count = $ad_row['path_count'];
        }
        $wdate = date('Y-m-d');
        // 현재 날짜의 년도와 월을 가져옵니다.
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $result_data = array();
        $result_data['ad_count'] = $ad_count; // 오늘광고개수확인하기

        $DB->where('sgdt_idx', $_POST['sgdt_idx']);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $sgdt_row = $DB->getone('smap_group_detail_t');

        // 일정 카운트
        $arr_sst_idx = get_schedule_main($_POST['sgdt_idx'], $_POST['event_start_date'], $sgdt_row['mt_idx']);
        $schedule_count = count($arr_sst_idx);

        //나의 일정카운트 시 장소없는 것은 찾아내기
        $arr_sst_null = array();
        unset($list);
        $DB->where('sgdt_idx', $_POST['sgdt_idx']);
        $DB->where(" ( sst_sdate <= '" . $_POST['event_start_date'] . " 23:59:59' and sst_edate >= '" . $_POST['event_start_date'] . " 00:00:00' )");
        $DB->where(" ( sst_location_lat = 0 or sst_location_long = 0) ");
        $DB->where('sst_show', 'Y');
        $list = $DB->get('smap_schedule_t');

        if ($list) {
            foreach ($list as $row) {
                if ($row['sst_title']) {
                    $arr_sst_null[] = $row['sst_idx'];
                }
            }
        }
        $arr_sst_null = array_unique($arr_sst_null);
        $location_null_count = count($arr_sst_null);

        if ($location_null_count > 0) { // 장소값이 없는 부분이 있을 경우 리턴
            $result_data['result'] = 'NoLocation';
            $result_data['path_count_day'] = '0';
            $result_data['path_count_month'] = '0';
        } else {
            if ($schedule_count > 1) {
                // 월 카운트
                $DB->where('mt_idx', $_SESSION['_mt_idx']);
                // $DB->where('sgdt_idx', $_POST['sgdt_idx']);
                $DB->where('sllt_date', [$start_date, $end_date], 'BETWEEN', 'AND');
                $sllt_month_list = $DB->get('smap_loadpath_log_t');
                $month_count = count($sllt_month_list);
                // 일 카운트
                $DB->where('mt_idx', $_SESSION['_mt_idx']);
                // $DB->where('sgdt_idx', $_POST['sgdt_idx']);
                $DB->where('sllt_date', $wdate);
                $sllt_list = $DB->get('smap_loadpath_log_t');
                $sllt_count = count($sllt_list);
                if ($sllt_count < $path_count_day) {
                    $result_data['result'] = 'Y';
                    $result_data['path_type'] = $mt_type;
                    $result_data['path_count_day'] = $path_count_day - $sllt_count;
                    $result_data['path_count_month'] = $path_count_month - $month_count;
                } else {
                    $result_data['result'] = 'Y';
                    $result_data['path_type'] = $mt_type;
                    $result_data['path_count_day'] = '0';
                    $result_data['path_count_month'] = $path_count_month - $month_count;
                }
            } else {
                $result_data['result'] = 'Noschedule';
                $result_data['path_count_day'] = '0';
                $result_data['path_count_month'] = '0';
            }
        }
    } else {
        $result_data['result'] = 'N';
        $result_data['path_count_day'] = '0';
        $result_data['path_count_month'] = '0';
    }

    $rtn = json_encode($result_data);

    echo $rtn;
    exit;
} elseif ($_POST['act'] == "pedestrian_path_chk") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    // logToFile("pedestrian_path_chk start");
    // smap_group_detail_t에서 sgdt_idx를 통해서 그룹 sgt_idx를 찾는다.
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $sgdt_row = $DB->getone('smap_group_detail_t');

    // // logToFile("1");
    // sgt_idx로 그룹원들을 찾는다.
    $DB->where('sgt_idx', $sgdt_row['sgt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $sgdt_list = $DB->get('smap_group_detail_t');
    // // logToFile("2");
    $result_data = array();
    $result_data['result'] = 'N';

    foreach ($sgdt_list as $sgdt_member) {
        // 일정 카운트
        // logToFile($sgdt_member['sgdt_idx'] . ' / ' . $_POST['event_start_date'] . ' / ' . $sgdt_member['mt_idx']);
        $arr_sst_idx = get_schedule_main($sgdt_member['sgdt_idx'], $_POST['event_start_date'], $sgdt_member['mt_idx']);
        $schedule_count = count($arr_sst_idx);
        // // logToFile("schedule_count : " . $schedule_count);
        // 일정 등록일/수정일 배열
        $arr_sst_date = get_schedule_date($sgdt_member['sgdt_idx'], $_POST['event_start_date'], $sgdt_member['mt_idx']);
        // logToFile("arr_sst_date : " . print_r($arr_sst_date, true));
        // // logToFile("arr_sst_date_count : " . count($arr_sst_date));
        if ($arr_sst_date && $_POST['sgdt_idx'] == $sgdt_member['sgdt_idx']) {
            $latest_date = max($arr_sst_date); // 등록/수정일 중 가장 최근일자 뽑아오기
            $wdate = date('Y-m-d');

            // $DB->where('mt_idx', $sgdt_member['mt_idx']);
            $DB->where('sgdt_idx', $sgdt_member['sgdt_idx']);
            // $DB->where("sllt_wdate >= '" . $latest_date  . "'");
            $DB->where('sllt_date', $wdate);
            $DB->orderby('sllt_wdate', 'desc');
            $sllt_row = $DB->getone('smap_loadpath_log_t');
            // logToFile("sllt_row : " . print_r($sllt_row, true));

            if ($sllt_row && $sllt_row['sllt_schedule_count'] === count($arr_sst_date) - 1) {
                $result_data['result'] = 'Y'; // 경로 데이터 유무와 관계없이 result를 Y로 변경
                $result_data['mt_idx'] = $sllt_row['mt_idx'] ?? null; // sllt_row가 없으면 mt_idx는 null
                $result_data['members'][$sgdt_member['sgdt_idx']] = array(
                    'sllt_json_text' => $sllt_row['sllt_json_text'] ?? null, // sllt_row가 없으면 null
                    'sllt_json_walk' => $sllt_row['sllt_json_walk'] ?? null  // sllt_row가 없으면 null
                );
            } else {
                $result_data['result'] = 'N';
            }
        }
    }
    // logToFile("result_data : " . print_r($result_data, true));
    echo json_encode($result_data);
} elseif ($_POST['act'] == "loadpath_add") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $sgdt_row = $DB->getone('smap_group_detail_t');

    // 일정 카운트
    $arr_sst_idx = get_schedule_main($_POST['sgdt_idx'], $_POST['event_start_date'], $sgdt_row['mt_idx']);
    $schedule_count = count($arr_sst_idx);
    $date = date('Y-m-d');
    unset($arr_query);
    $arr_query = array(
        "mt_idx" => $_SESSION['_mt_idx'],
        "sgdt_idx" => $_POST['sgdt_idx'],
        "sllt_json_text" => $_POST['sllt_json_text'],
        "sllt_json_walk" => $_POST['sllt_json_walk'],
        "sllt_schedule_count" => $schedule_count,
        "sllt_date" => $date,
        "sllt_wdate" => $DB->now(),
        "sllt_language" => $_POST['sllt_language'],
    );
    $DB->insert('smap_loadpath_log_t', $arr_query);

    echo 'Y';
} elseif ($_POST['act'] == "my_location_search") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    $DB->where('mt_idx', $_POST['mt_idx']);
    $DB->orderby('mlt_gps_time', 'desc');
    $mllt_row = $DB->getone('member_location_log_t');
    $result_data = array();

    if ($mllt_row['mlt_idx']) {
        $result_data['mlt_lat'] = $mllt_row['mlt_lat'];
        $result_data['mlt_long'] = $mllt_row['mlt_long'];
    } else {
        $result_data['mlt_lat'] = $_SESSION['_mt_lat'];
        $result_data['mlt_long'] = $_SESSION['_mt_long'];
    }
    $rtn = json_encode($result_data);

    echo $rtn;
} elseif ($_POST['act'] == "marker_reload") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_show', 'Y');
    $sgdt_row = $DB->getone('smap_group_detail_t');
    // 나의 위치 마커정보 등록
    if ($sgdt_row) {
        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $mem_row = $DB->getone('member_t');

        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $DB->orderby('mlt_gps_time', 'desc');
        $mt_location_info = $DB->getone('member_location_log_t');

        unset($result_data);
        $result_data = array(
            "mt_lat" => $mt_location_info['mlt_lat'] == "" ? $mem_row['mt_lat'] : $mt_location_info['mlt_lat'],
            "mt_long" => $mt_location_info['mlt_long'] == "" ? $mem_row['mt_long'] :  $mt_location_info['mlt_long'],
            "my_profile" => $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']),
        );
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $mem_row = $DB->getone('member_t');
        unset($result_data);
        $result_data = array(
            "mt_lat" => $_SESSION['_mt_lat'] == "" ? $mem_row['mt_lat'] : $_SESSION['_mt_lat'],
            "mt_long" => $_SESSION['_mt_long'] == "" ? $mem_row['mt_long'] : $_SESSION['_mt_long'],
            "my_profile" => $_SESSION['_mt_file1'] == "" ? $ct_no_img_url : $_SESSION['_mt_file1'],
        );
    }
    session_location_update($_SESSION['_mt_idx']);

    $sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
    $sgdt_leader_cnt = f_get_leader_cnt($_SESSION['_mt_idx']); //리더인 그룹수
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        // 오너,리더일 경우 해당 그룹의 그룹원 전체 조회
        $DB->where('sgt_idx', $sgdt_row['sgt_idx']);
        $DB->where('sgdt_idx', $sgdt_row['sgdt_idx'], '!=');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        $sgdt_list = $DB->get('smap_group_detail_t');
        if ($sgdt_list) {
            $profile_count = 1;
            foreach ($sgdt_list as $sgdtg_row) {
                $DB->where('mt_idx', $sgdtg_row['mt_idx']);
                $DB->orderby('mlt_gps_time', 'desc');
                $mt_location_info = $DB->getone('member_location_log_t');

                $DB->where('mt_idx', $sgdtg_row['mt_idx']);
                $mem_row = $DB->getone('member_t');

                $result_data['profilemarkerLat_' . $profile_count] = $mt_location_info['mlt_lat'] == "" ? 37.5665 : $mt_location_info['mlt_lat'];
                $result_data['profilemarkerLong_' . $profile_count] = $mt_location_info['mlt_long'] == "" ? 126.9780 :  $mt_location_info['mlt_long'];
                $result_data['profilemarkerImg_' . $profile_count] = $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']);
                $profile_count++;
            }
        }
        $result_data['profile_count'] = $profile_count - 1;
    }
    $result_data['marker_reload'] = 'Y';
    echo json_encode($result_data);
    exit;
}


include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
