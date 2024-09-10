<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "event_source") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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
        p_alert(translate('로그인이 필요합니다.', $userLang), './login', '');
    }
    $arr_sst_idx = get_schedule_array($_SESSION['_mt_idx'], $_POST['event_start_date']);
    $cnt = count($arr_sst_idx);
    if ($cnt > 0) {
        $arr_sst_idx_im = implode(',', $arr_sst_idx);

        unset($list_sst);
        $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
        $DB->where('sst_show', 'Y');
        $DB->groupBy("mt_idx");
        $DB->orderBy("sst_sdate", "asc");
        $list_sst = $DB->get('smap_schedule_t');
    }

    //오너인 그룹수
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgt_show', 'Y');
    $row = $DB->getone('smap_group_t', 'count(*) as cnt');
    $sgt_cnt = $row['cnt'];

    //리더인 그룹수
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_owner_chk', 'N');
    $DB->where('sgdt_leader_chk', 'Y');
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
    $sgdt_cnt = $row['cnt'];

    //초대된 그룹수
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

    if ($row_sgdt['gc_sgt_idx']) {
        unset($list_sgt);
        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where('sgt_show', 'Y');
        $DB->orderBy("sgt_idx", "asc");
        $DB->orderBy("sgt_udate", "desc");
        $list_sgt = $DB->get('smap_group_t');
    }    ?>
    <p class="fs_12 fw_700 text-primary mb-3 pt_20"><?= datetype($_POST['event_start_date'], 21) ?><?= translate('의 일정입니다.', $userLang) ?></p>
    <div id="mbr_wr">
        <!-- 그룹이 있든 없든 항상 본인은 맨 위에 표시 본인일정에는 .user_grplist 추가됩니다.-->
        <div class="grp_list user_grplist">
            <ul class="mbr_wr_ul">
                <li class="schdl_list">
                    <ul>
                        <li id="mbr_hd01_1" class="mbr_hd">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center flex-auto">
                                    <a href="#" class="d-flex align-items-center flex-fill">
                                        <div class="prd_img flex-shrink-0 mr_12">
                                            <div class="rect_square rounded_14">
                                                <img src="<?= $_SESSION['_mt_file1'] ?>" alt="이미지" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                                            </div>
                                        </div>
                                        <p class="fs_14 fw_500 text_dynamic mr-2"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                                    </a>
                                </div>
                                <div class="d-flex align-items-center flex-shrink-0">
                                    <a href='./schedule_form?sdate=<?= $_POST['event_start_date'] ?>&mt_idx=<?= $_SESSION['_mt_idx'] ?>' class="fs_13 fc_navy"><i class="xi-plus-min"></i><?= translate('일정 추가하기', $userLang) ?></a>
                                    <? if ($cnt > 0) { ?>
                                        <button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr01_1" aria-expanded="false" aria-controls="mbr01"><img class="open_ic" src="<?= CDN_HTTP ?>/img/ic_open.png" style="width:1.0rem;"></button>
                                    <? } else { ?>
                                        <button type="button" style="visibility:hidden" class=" btn btn-link ml-3" data-toggle="collapse" data-target="#mbr01_1" aria-expanded="false" aria-controls="mbr01"><img class="open_ic" src="<?= CDN_HTTP ?>/img/ic_open.png" style="width:1.0rem;"></button>
                                    <? } ?>
                                </div>
                            </div>
                            <? if ($cnt > 0) { ?>
                                <div id="mbr01_1" class="collapse " aria-labelledby="mbr01_1" aria-labelledby="mbr_hd01_1" data-parent="#mbr_wr">
                                    <ul class="pt-4 pb-3">
                                        <?
                                        if ($list_sst) {
                                            unset($list_sst_a);
                                            $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
                                            $DB->where('sst_show', 'Y');
                                            $DB->orderBy("sst_all_day", "asc");
                                            $DB->orderBy("sst_sdate", "asc");
                                            $DB->orderBy("sst_edate", "asc");
                                            $list_sst_a = $DB->get('smap_schedule_t');
                                            if ($list_sst_a) {
                                                $count = 1;
                                                foreach ($list_sst_a as $row_sst_a) {
                                                    $current_date = date('Y-m-d H:i:s');
                                                    // if (get_date_ttime($current_date) >= get_date_ttime($row_sst_a['sst_edate'])) {
                                                    if ($row_sst_a['sst_all_day'] == 'Y') {
                                                        $point_status = 'point_ing';
                                                    } else if ($current_date >= $row_sst_a['sst_edate']) {
                                                        $point_status = 'point_done';
                                                    } else if ($current_date >= $row_sst_a['sst_sdate'] && $current_date <= $row_sst_a['sst_edate']) {
                                                        $point_status = 'point_ing';
                                                    } else {
                                                        $point_status = 'point_gonna';
                                                    }
                                                    if ($row_sst_a['sst_update_chk'] == '1,2,3') {
                                                        $grant = '전체';
                                                    } else {
                                                        $grantNumbers = explode(',', $row_sst_a['sst_update_chk']);
                                                        // 권한 문자열을 담을 배열 초기화
                                                        $grantStrings = array();

                                                        // 각 권한 번호에 따른 권한 문자열 배열에 추가
                                                        foreach ($grantNumbers as $number) {
                                                            if (isset($arr_grant[$number])) {
                                                                $grantStrings[] = $arr_grant[$number];
                                                            }
                                                        }

                                                        // 권한 문자열 합치기
                                                        $grant = implode(', ', $grantStrings);
                                                    }
                                        ?>
                                                    <li class="py-2">
                                                        <a href="./schedule_form?sst_idx=<?= $row_sst_a['sst_idx'] ?>" class="d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <div class="task <?= $point_status ?>">
                                                                    <span class="point_inner">
                                                                        <span class="point_txt"><?= $count ?></span>
                                                                    </span>
                                                                </div>
                                                                <div class="mx-3">
                                                                    <p class="fs_13 fw_700 text_dynamic line_h1_3 line1_text"><?= $row_sst_a['sst_title'] ?></p>
                                                                    <p class="fs_10 fw_300 text_gray line_h1_3"><span>수정권한 : </span> <?= $grant ?></p>
                                                                </div>
                                                            </div>
                                                            <!-- 수정권한 없을때 지워주세요 -->
                                                            <p><i class="xi-angle-right-min text_light_gray fs_13"></i></p>
                                                        </a>
                                                    </li>
                                        <?
                                                    $count++;
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            <?
                            }
                            ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <?php
        if ($sgt_cnt > 0 || $sgdt_cnt > 0) {

            if ($list_sgt) {
                foreach ($list_sgt as $row_sgt) {
                    $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
        ?>
                    <!--F-3 전체 그룹원 일정-->
                    <div class="grp_list">
                        <div class="grp_tit">
                            <p class="fs_17 fw_700 line_h1_3 line1_text text_dynamic"><?= $row_sgt['sgt_title'] ?></p>
                        </div>
                        <ul class="mbr_wr_ul">
                            <? if ($member_cnt_t <= 1) { ?>
                                <!-- 그룹원 추가 버튼 -->
                                <li class="schdl_list">
                                    <!-- <button type="button" class="btn w-100 h-auto fs_13 fc_navy schdl_btn" data-toggle="modal" data-target="#link_modal"><i class="xi-plus-min mr-2"></i>그룹원 초대하기</button> -->
                                    <button type="button" class="btn w-100 h-auto fs_13 fc_navy schdl_btn" onclick="share_link_modal('<?= $row_sgt['sgt_idx'] ?>')"><i class="xi-plus-min mr-2"></i><?= translate('그룹원 초대하기', $userLang) ?></button>
                                </li>
                                <? } else {
                                unset($list_sgdt);
                                $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
                                if ($list_sgdt['data']) {
                                    foreach ($list_sgdt['data'] as $key => $val) {
                                        $arr_sst_idx = get_schedule_array2($val['sgdt_idx'], $_POST['event_start_date'], $val['mt_idx']);
                                        $cnt = count($arr_sst_idx);
                                        if (translate($val['sgdt_owner_leader_chk_t'], $userLang) != translate('오너', $userLang)) {
                                ?>
                                            <li class="schdl_list">
                                                <ul>
                                                    <li id="mbr_hd02_1" class="mbr_hd">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center flex-auto">
                                                                <a href="#" class="d-flex align-items-center flex-fill">
                                                                    <div class="prd_img flex-shrink-0 mr_12">
                                                                        <div class="rect_square rounded_14">
                                                                            <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                                                        </div>
                                                                    </div>
                                                                    <p class="fs_14 fw_500 text_dynamic mr-2"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                                                                </a>
                                                            </div>
                                                            <div class="d-flex align-items-center flex-shrink-0">
                                                                <? if ($sgt_cnt > 0 || $sgdt_cnt > 0) {
                                                                    if (translate($val['sgdt_owner_leader_chk_t'], $userLang) != translate('오너', $userLang)) { ?>
                                                                        <a href="./schedule_form?sdate=<?= $_POST['event_start_date'] ?>&sgdt_idx=<?= $val['sgdt_idx'] ?>" class="fs_13 fc_navy"><i class="xi-plus-min"></i><?= translate('일정 추가하기', $userLang) ?></a>
                                                                <? }
                                                                } ?>
                                                                <? if ($cnt > 0) { ?>
                                                                    <button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr02_<?= $key ?>" aria-expanded="false" aria-controls="mbr02_1"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>
                                                                <? } else { ?>
                                                                    <button type="button" style="visibility:hidden" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr02_<?= $key ?>" aria-expanded="false" aria-controls="mbr02_1"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>
                                                                <? } ?>
                                                            </div>
                                                        </div>
                                                        <? if ($cnt > 0) { ?>
                                                            <div id="mbr02_<?= $key ?>" class="collapse " aria-labelledby="mbr02_<?= $key ?>" aria-labelledby="mbr_hd02_1" data-parent="#mbr_wr">
                                                                <ul class="pt-4 pb-3">
                                                                    <?
                                                                    $arr_sst_idx_im = implode(',', $arr_sst_idx);
                                                                    unset($list_sst);
                                                                    $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
                                                                    $DB->where('sst_show', 'Y');
                                                                    $DB->groupBy("mt_idx");
                                                                    $DB->orderBy("sst_sdate", "asc");
                                                                    $DB->orderBy("sst_edate", "asc");
                                                                    $list_sst = $DB->get('smap_schedule_t');
                                                                    if ($list_sst) {
                                                                        unset($list_sst_a);
                                                                        $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
                                                                        $DB->where('sst_show', 'Y');
                                                                        $DB->orderBy("sst_all_day", "asc");
                                                                        $DB->orderBy("sst_sdate", "asc");
                                                                        $DB->orderBy("sst_edate", "asc");
                                                                        $list_sst_a = $DB->get('smap_schedule_t');
                                                                        if ($list_sst_a) {
                                                                            $count = 1;
                                                                            foreach ($list_sst_a as $row_sst_a) {
                                                                                $current_date = date('Y-m-d H:i:s');
                                                                                if ($row_sst_a['sst_all_day'] == 'Y') {
                                                                                    $point_status = 'point_ing';
                                                                                } else if ($current_date >= $row_sst_a['sst_edate']) {
                                                                                    $point_status = 'point_done';
                                                                                } else if ($current_date >= $row_sst_a['sst_sdate'] && $current_date <= $row_sst_a['sst_edate']) {
                                                                                    $point_status = 'point_ing';
                                                                                } else {
                                                                                    $point_status = 'point_gonna';
                                                                                }
                                                                                if ($row_sst_a['sst_update_chk'] == '1,2,3') {
                                                                                    $grant = '전체';
                                                                                } else {
                                                                                    $grantNumbers = explode(',', $row_sst_a['sst_update_chk']);
                                                                                    // 권한 문자열을 담을 배열 초기화
                                                                                    $grantStrings = array();

                                                                                    // 각 권한 번호에 따른 권한 문자열 배열에 추가
                                                                                    foreach ($grantNumbers as $number) {
                                                                                        if (isset($arr_grant[$number])) {
                                                                                            $grantStrings[] = $arr_grant[$number];
                                                                                        }
                                                                                    }

                                                                                    // 권한 문자열 합치기
                                                                                    $grant = implode(', ', $grantStrings);
                                                                                }
                                                                    ?>
                                                                                <li class="py-2">
                                                                                    <a href="./schedule_form?sst_idx=<?= $row_sst_a['sst_idx'] ?>" class="d-flex align-items-center justify-content-between">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="task <?= $point_status ?>">
                                                                                                <span class="point_inner">
                                                                                                    <span class="point_txt"><?= $count ?></span>
                                                                                                </span>
                                                                                            </div>
                                                                                            <div class="mx-3">
                                                                                                <p class="fs_13 fw_700 text_dynamic line_h1_3 line1_text"><?= $row_sst_a['sst_title'] ?></p>
                                                                                                <p class="fs_10 fw_300 text_gray line_h1_3"><span>수정권한 : </span> <?= $grant ?></p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- 수정권한 없을때 지워주세요 -->
                                                                                        <p><i class="xi-angle-right-min text_light_gray fs_13"></i></p>
                                                                                    </a>
                                                                                </li>
                                                                    <?
                                                                                $count++;
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </div>
                                                        <? } ?>
                                                    </li>
                                                </ul>
                                            </li>
                            <?          }
                                    }
                                }
                            } ?>
                        </ul>
                    </div>
        <? }
            }
        }
        ?>
    </div>
    <script>
        function share_link_modal(i) {
            var form_data = new FormData();
            form_data.append("act", "link_modal");
            form_data.append("sgt_idx", i);

            $.ajax({
                url: "./group_update",
                enctype: "multipart/form-data",
                data: form_data,
                type: "POST",
                async: true,
                contentType: false,
                processData: false,
                cache: true,
                timeout: 5000,
                success: function(data) {
                    if (data == 'N') {
                        jalert('초대코드를 다 사용하였습니다.');
                    } else {
                        $('#share_url').val(data);
                    }
                },
                error: function(err) {
                    console.log(err);
                },
            });
            $('#link_modal').modal('show');
        }
    </script>
<? } elseif ($_POST['act'] == "get_schedule_member") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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
                                                            <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1" style="word-break: break-all;"><?= translate($val['sgdt_owner_leader_chk_t'], $userLang) ?></p>
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
                    <p class="mt_20 fc_gray_900 text-center"><?= translate('등록된 멤버가 없습니다.', $userLang) ?></p>
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
            <p class="modal-title line1_text fs_20 fw_700">위치 선택</p>
            <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
        </div>
        <div class="modal-body scroll_bar_y" style="min-height: 600px;">
            <div class="px-0 py-0 map_wrap">
                <div class="map_wrap_re">
                    <div class="pin_cont bg-white pt_20 px_16 pb_16 rounded_10">
                        <ul>
                            <li class="d-flex">
                                <div class="name flex-fill">
                                    <span class="fs_12 fw_600 text-primary"><?= translate('선택한 위치', $userLang) ?></span>
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
            <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?= translate('위치 선택완료', $userLang) ?></button>
        </div>
    </form>
    <?php
} elseif ($_POST['act'] == "map_location_input") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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
        p_alert('로그인이 필요합니다.', './login', '');
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
                <p class="mt_20 fc_gray_500 text-center line_h1_4"><?= translate('등록된 위치가 없습니다.', $userLang) ?></p>
            </div>
            <!-- 멤버가 없을때 -->
        </li>
        <?php
    }
} elseif ($_POST['act'] == "map_location_like_delete") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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
        p_alert('로그인이 필요합니다.', './login', '');
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
        p_alert('로그인이 필요합니다.', './login', '');
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
        p_alert('로그인이 필요합니다.', './login', '');
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

        if ($repeat_array['r1'] == 3) { // 매주 반복일 시 요일 찾은 후 반복 실행
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
                        $mt_id = $member_row['mt_id'];
                        $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
                        $mem_nickname = $mem_row['mt_nickname'] ? $mem_row['mt_nickname'] : $mem_row['mt_name'];
                        $plt_title =  "일정 수정알림 ✏️";
                        $plt_content =  $mem_nickname . '님이 \'' . $sst_row['sst_title'] . '\' 일정을 수정했습니다.';

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
            $mt_id = $member_row['mt_id'];
            $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
            $owner_nickname = $owner_row['mt_nickname'] ? $owner_row['mt_nickname'] : $owner_row['mt_name'];
            $plt_title = '일정 수정알림 ✏️';
            $plt_content = $owner_nickname . '님이 \'' . $sst_row['sst_title'] . '\' 일정을 수정했습니다. 확인해보세요.';

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
                        $mt_id = $member_row['mt_id'];
                        $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
                        $mem_nickname = $mem_row['mt_nickname'] ? $mem_row['mt_nickname'] : $mem_row['mt_name'];
                        $plt_title = '일정 생성알림 ➕';
                        $plt_content = $mem_nickname . '님이 새로운 일정을 생성했습니다.';

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
            $mt_id = $member_row['mt_id'];
            $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
            $owner_nickname = $owner_row['mt_nickname'] ? $owner_row['mt_nickname'] : $owner_row['mt_name'];
            $plt_title = '일정 생성알림 ➕';
            $plt_content = $owner_nickname . '님이 새로운 일정을 생성했습니다. 확인해보세요.';

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
        p_alert('로그인이 필요합니다.', './login', '');
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
                    $mt_id = $member_row['mt_id'];
                    $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
                    $mem_nickname = $mem_row['mt_nickname'] ? $mem_row['mt_nickname'] : $mem_row['mt_name'];
                    $plt_title = '일정 삭제 알림 ❌';
                    $plt_content = $mem_nickname . '님이 \'' . $sst_row['sst_title'] . '\' 일정을 삭제했습니다.';

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
        $mt_id = $member_row['mt_id'];
        $member_nickname = $member_row['mt_nickname'] ? $member_row['mt_nickname'] : $member_row['mt_name'];
        $owner_nickname = $owner_row['mt_nickname'] ? $owner_row['mt_nickname'] : $owner_row['mt_name'];
        $plt_title = '일정 삭제알림 ❌';
        $plt_content = $owner_nickname . '님이 \'' . $sst_row['sst_title'] . '\' 일정을 삭제했습니다. 확인해보세요.';

        $result = api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content);
    }

    echo "Y";
} elseif ($_POST['act'] == "calendar_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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
    if($row_sgdt == 'Y'){
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
        p_alert('로그인이 필요합니다.', './login', '');
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
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic"><?= translate('그룹원 추가', $userLang) ?></p>
                </div>
            <?php } else { ?>
                <div class="swiper-slide mem_box add_mem_box" style="visibility: hidden;">
                    <button class="btn mem_add">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic"><?= translate('그룹원 추가', $userLang) ?></p>
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
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');
    if ($sgdt_row) {
        $DB->where('mt_idx', $sgdt_row['mt_idx']);
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
    } ?>
    <div class="task_header px_16 pt_16" id="my_location_div">
        <div class="border-bottom  pb-3">
            <div class="task_header_tit">
                <p class="fs_16 fw_600 line_h1_2 mr-3"><?= translate('현재 위치', $userLang); ?></p>
                <div class="d-flex align-items-center justify-content-end">
                    <!-- <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span> <?= round($mt_location_info['mlt_speed']) ?>km/h<? } ?></p> -->
                    <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span><? } ?></p>
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
    </div>
    <?
    $arr_sst_idx = get_schedule_main($_POST['sgdt_idx'], $_POST['event_start_date'], $sgdt_row['mt_idx']);
    $cnt = count($arr_sst_idx);
    if ($cnt < 1) {
    ?>
        <!-- 내용 없을 때 박스 -->
        <div class="task_body px_16 pt-3 pb_16">
            <div class="task_body_cont num_point_map">
                <div class="pt-5">
                    <button type="button" class="btn w-100 rounded add_sch_btn"
                        onclick="trackButtonClick(); location.href='./schedule_form?sdate=<?= $_POST['event_start_date'] ?>&sgdt_idx=<?= $_POST['sgdt_idx'] ?>'">
                        <i class="xi-plus-min mr-3"></i> <?= translate('일정을 추가해보세요!', $userLang) ?> 
                    </button>
                </div>
            </div>
        </div>

        <script>
            function trackButtonClick() {
                gtag('event', 'show_optimal_path', {
                    'event_category': 'optimal_path',
                    'event_label': 'show',
                    'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                    'platform': isAndroid() ? 'Android' : (isiOS() ? 'iOS' : 'Unknown')
                });
            }

            function isAndroid() {
                return navigator.userAgent.match(/Android/i);
            }

            function isiOS() {
                return navigator.userAgent.match(/iPhone|iPad|iPod|Mac|Apple/i);
            }

            function isAndroidDevice() {
                return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
            }

            function isiOSDevice() {
                return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
            }
        </script>

        <?
    } else {
        $arr_sst_idx_im = implode(',', $arr_sst_idx);

        unset($list_sst);
        $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
        $DB->where('sst_show', 'Y');
        $DB->groupBy("mt_idx");
        $DB->orderBy("sst_sdate", "asc");
        $DB->orderBy("sst_edate", "asc");
        $list_sst = $DB->get('smap_schedule_t');

        if ($list_sst) {
        ?>
            <div class="task_body px_16 pt-3 pb_16">
                <div class="task_body_tit">
                    <p class="fs_16 fw_600 line_h1_2"><?= translate('일정', $userLang); ?><span class="text_light_gray fs_14 ml-1">(<?= $cnt ?> <?= translate('개', $userLang) ?>)</span></p>
                    <!-- 비활성화일때 있습니다. disabled-->
                    <button type="button" class="btn fs_12 fw_500 h-auto w-auto text-primary optimal_btn" onclick="pedestrian_path_modal('<?= $_POST['sgdt_idx'] ?>')"><?= translate('최적경로 표시하기', $userLang) ?><i class="xi-angle-right-min fs_13"></i></button>
                </div>
                <div class="task_body_cont num_point_map">
                    <!-- 일정 있을 때 사용해주세요. -->
                    <div class="">
                        <div class="swiper task_slide">
                            <div class="swiper-wrapper">
                                <?php
                                unset($list_sst_a);
                                $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
                                $DB->where('sst_show', 'Y');
                                $DB->orderBy("sst_all_day", "asc");
                                $DB->orderBy("sst_sdate", "asc");
                                $DB->orderBy("sst_edate", "asc");
                                $list_sst_a = $DB->get('smap_schedule_t');
                                $count = 1;
                                $ing_chk = false;
                                $complete_chk = false;
                                $current_date = date('Y-m-d H:i:s');
                                if ($list_sst_a) {
                                    foreach ($list_sst_a as $row_sst_a) {
                                        if ($row_sst_a['sst_all_day'] == 'Y') {
                                            $point_status = 'point_ing';
                                            $point_tcss = 'ing_txt';
                                            $point_text = '하루종일';
                                            // } else if (get_date_ttime($current_date) >= get_date_ttime($row_sst_a['sst_sdate']) && get_date_ttime($current_date) <= get_date_ttime($row_sst_a['sst_edate'])) {
                                            // } else if (substr($current_date, 10, 9) >= substr($row_sst_a['sst_edate'], 10, 9)) {
                                        } else if ($current_date >= $row_sst_a['sst_edate']) {
                                            $point_status = 'point_done';
                                            $point_tcss = 'done_txt';
                                            $point_text = translate('완료', $userLang);
                                        } else if ($current_date >= $row_sst_a['sst_sdate'] && $current_date <= $row_sst_a['sst_edate']) {
                                            $point_status = 'point_ing';
                                            $point_tcss = 'ing_txt';
                                            $point_text = translate('진행중', $userLang);
                                            $complete_chk = true;
                                        } else {
                                            $point_status = 'point_gonna';
                                            $point_tcss = 'gonna_txt';
                                            $point_text = translate('진행예정', $userLang);
                                        }
                                ?>
                                        <? if ($count > 1) { ?>
                                            <div class="swiper-slide optimal_box">
                                                <? if ($current_date <= $row_sst_a['sst_sdate'] && !$ing_chk && !$complete_chk) { ?>
                                                    <!-- <p class="fs_23 fw_700 optimal_time">2<span class="fs_14">분</span></p> -->
                                                    <!-- <p class="fs_12 text_light_gray optimal_tance">164m</p> -->
                                                <? $ing_chk = true;
                                                } ?>
                                            </div>
                                        <? } ?>
                                        <div class="swiper-slide task_point_box" <? if ($row_sst_a['sst_location_lat'] > 0 && $row_sst_a['sst_location_long'] > 0) { ?> onclick="map_panto('<?= $row_sst_a['sst_location_lat'] ?>','<?= $row_sst_a['sst_location_long'] ?>')" <? } ?>>
                                            <div class="task <?= $point_status ?>">
                                                <span class="point_inner">
                                                    <span class="point_txt"><?= $count ?></span>
                                                </span>
                                            </div>
                                            <p class="text_lightgray fs_13 mt-1 status_txt <?= $point_tcss ?>"><?= $point_text ?></p>
                                        </div>
                                <?
                                        $count++;
                                    }
                                }
                                ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        <script>
            // 일정 슬라이드
            var task_swiper = new Swiper(".task_slide", {
                slidesPerView: 8,
                // slidesPerGroup: 8,
                pagination: {
                    el: ".task_slide .swiper-pagination",
                    clickable: true,
                },
            });
        </script>
        <?
    }
} elseif ($_POST['act'] == "member_location_reload") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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
                    <p class="fs_16 fw_600 line_h1_2 mr-3"><?= translate('현재 위치', $userLang); ?></p>
                    <div class="d-flex align-items-center justify-content-end">
                        <!-- <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span> <?= round($mt_location_info['mlt_speed']) ?>km/h<? } ?></p> -->
                        <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span><? } ?></p>
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
                <p class="fs_16 fw_600 line_h1_2 mr-3"><?= translate('현재 위치', $userLang); ?></p>
                <div class="d-flex align-items-center justify-content-end">
                    <!-- <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span> <?= round($mt_location_info['mlt_speed']) ?>km/h<? } ?></p> -->
                    <p class="move_txt fs_13 mr-3"><span class="mr-1"><? if ($mt_location_info['mlt_speed'] > 1) { ?>이동중</span><? } ?></p>
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
        p_alert('로그인이 필요합니다.', './login', '');
    }

    $cache_key = "schedule_map_list_" . $_POST['sgdt_idx'] . "_" . $_SESSION['_mt_idx'];
    $cached_data = CacheUtil::get($cache_key);
    if ($cached_data) {
        echo json_encode($cached_data);
        exit;
    }

    // 함수 정의: 그룹 상세 정보 조회
    function get_group_detail($sgdt_idx) {
        global $DB;
        $DB->where('sgdt_idx', $sgdt_idx);
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->where('sgdt_show', 'Y');
        return $DB->getone('smap_group_detail_t');
    }

    // 함수 정의: 그룹원 리스트 조회
    function get_group_members($sgt_idx) {
        global $DB;
        $DB->where('sgt_idx', $sgt_idx);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        return $DB->get('smap_group_detail_t');
    }

    // 함수 정의: 회원 데이터 조회 및 캐싱
    function get_member_data($mt_idx) {
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
        "my_lat" => $my_data['lat'],
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
                $sst_all_day_t = ($row_sst_a['sst_all_day'] == 'Y') ? '하루종일' : "$sst_sdate_e1 ~ $sst_sdate_e2";

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
        p_alert('로그인이 필요합니다.', './login', '');
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

        if($location_null_count > 0){ // 장소값이 없는 부분이 있을 경우 리턴
            $result_data['result'] = 'NoLocation';
            $result_data['path_count_day'] = '0';
            $result_data['path_count_month'] = '0';
        }else{
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
}elseif ($_POST['act'] == "pedestrian_path_chk") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    // logToFile("start");
    // smap_group_detail_t에서 sgdt_idx를 통해서 그룹 sgt_idx를 찾는다.
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $sgdt_row = $DB->getone('smap_group_detail_t');

    // logToFile("1");
    // sgt_idx로 그룹원들을 찾는다.
    $DB->where('sgt_idx', $sgdt_row['sgt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $sgdt_list = $DB->get('smap_group_detail_t');
    // logToFile("2");
    $result_data = array();
    $result_data['result'] = 'N';

    foreach ($sgdt_list as $sgdt_member) {
        // 일정 카운트
        // logToFile($sgdt_member['mt_idx']);
        $arr_sst_idx = get_schedule_main($sgdt_member['sgdt_idx'], $_POST['event_start_date'], $sgdt_member['mt_idx']);
        $schedule_count = count($arr_sst_idx);
        // logToFile("schedule_count : " . $schedule_count);
        // 일정 등록일/수정일 배열
        $arr_sst_date = get_schedule_date($sgdt_member['sgdt_idx'], $_POST['event_start_date'], $sgdt_member['mt_idx']);
        // logToFile("arr_sst_date : " . $arr_sst_date);
        if ($arr_sst_date) {
            $latest_date = max($arr_sst_date); // 등록/수정일 중 가장 최근일자 뽑아오기
            $wdate = date('Y-m-d');
            
            // $DB->where('mt_idx', $sgdt_member['mt_idx']);
            $DB->where('sgdt_idx', $sgdt_member['sgdt_idx']);
            $DB->where('sllt_schedule_count', $schedule_count);
            $DB->where("sllt_wdate >= '" . $latest_date  . "'");
            $DB->where('sllt_date', $wdate);
            $DB->orderby('sllt_wdate', 'desc');
            $sllt_row = $DB->getone('smap_loadpath_log_t');
            // logToFile($sllt_row);

            if ($sllt_row['sllt_idx']) {
                $result_data['result'] = 'Y';
                $result_data['mt_idx'] = $sllt_row['mt_idx'];
                $result_data['members'][$sgdt_member['sgdt_idx']] = array(
                    'sllt_json_text' => $sllt_row['sllt_json_text'],
                    'sllt_json_walk' => $sllt_row['sllt_json_walk']
                );
            }
        }
    }

    echo json_encode($result_data);
} elseif ($_POST['act'] == "loadpath_add") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
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
        p_alert('로그인이 필요합니다.', './login', '');
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
        p_alert('로그인이 필요합니다.', './login', '');
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
            "my_lat" => $mt_location_info['mlt_lat'] == "" ? $mem_row['mt_lat'] : $mt_location_info['mlt_lat'],
            "mt_long" => $mt_location_info['mlt_long'] == "" ? $mem_row['mt_long'] :  $mt_location_info['mlt_long'],
            "my_profile" => $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']),
        );
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $mem_row = $DB->getone('member_t');
        unset($result_data);
        $result_data = array(
            "my_lat" => $_SESSION['_mt_lat'] == "" ? $mem_row['mt_lat'] : $_SESSION['_mt_lat'],
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
