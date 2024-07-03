<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

$color_sets = array(
    '#E6F2FF', // 연한 파란색
    '#D6E6FF', // 연한 라벤더
    '#E5F1FF', // 연한 하늘색
    '#F0F8FF', // 연한 앨리스 블루
    '#E0FFFF', // 연한 민트색
    '#E0F0FF', // 밝은 연한 파란색
    '#E0E6FF', // 밝은 연한 라벤더
    '#E0F0FF', // 밝은 연한 하늘색
    '#E6F0FF', // 밝은 연한 앨리스 블루
    '#E6FFFF'  // 밝은 연한 민트색
);

$random_color = $color_sets[array_rand($color_sets)];

if ($_POST['act'] == "recom_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }

    $point_t = 'POINT(' . $_SESSION['_mt_long'] . ', ' . $_SESSION['_mt_lat'] . ')';

    unset($list_rlt);
    $list_rlt = $DB->rawQuery("SELECT *, ST_Distance_Sphere(" . $point_t . ", POINT(rlt_long, rlt_lat)) AS distance FROM recomand_location_t WHERE ST_Distance_Sphere(" . $point_t . ", POINT(rlt_long, rlt_lat)) <= " . RECOM_CIRCLE . " and rlt_show = 'Y' ORDER BY distance asc limit 10");

    if ($list_rlt) {
        foreach ($list_rlt as $row_rlt) { ?>
            <div class="border_orange rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                <div class="mr-2">
                    <p class="fs_13 fc_orange rounded_04 bg_fff5ea text-center px_06 py_02 text_dynamic line1_text line_h1_4 w_fit mb-2">추천장소</p>
                    <p class="fs_14 fw_600 text_dynamic line_h1_2 py_02"><?= $row_rlt['rlt_title'] ?> <small class="text-muted">(<?= get_distance_t($row_rlt['distance']) ?>)</small></p>
                    <p class="line1_text fs_13 fw_400 text_dynamic line_h1_4 py_02"><?= $row_rlt['rlt_add1'] ?></p>
                    <p class="line1_text fs_13 fw_400 text_dynamic line_h1_4 py_02"><?= $row_rlt['rlt_tel1'] ?></p>
                </div>
                <?php if ($row_rlt['rlt_url']) { ?>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn text_gray fs_20 h_fit_im px-2" onclick="gourl('<?= $row_rlt['rlt_url'] ?>');"><i class="xi-plus fc_orange"></i></button>
                    </div>
                <?php } ?>
            </div>
        <?
        }
    }
} elseif ($_POST['act'] == "location_member_input") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }
    if ($_POST['sgdt_mt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_mt_idx');
    }

    unset($arr_query);
    $arr_query = array(
        "mt_idx" => $_SESSION['_mt_idx'],
        "sgdt_idx" => $_POST['sgdt_idx'],
        "sgdt_mt_idx" => $_POST['sgdt_mt_idx'],
        "slmt_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('smap_location_member_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "location_info") {
    if ($_POST['mt_idx']) { ?>
        <div class="cld_head_wr">
            <div class="add_cal_tit">
                <button type="button" id="btn-schedule-prev" class="btn h-auto swiper-button-prev"><i class="xi-angle-left-min"></i></button>
                <div class="sel_month d-inline-flex flex-grow-1 text-centerf">
                    <img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" width="16px" alt="월 선택 아이콘" />
                    <p class="fs_15 fw_600" id="schedule-title"></p>
                </div>
                <button type="button" id="btn-schedule-next" class="btn h-auto swiper-button-next"><i class="xi-angle-right-min"></i></button>
            </div>
            <div class="cld_date_wrap cld_head fs_12">
                <link rel="stylesheet" type="text/css" href="<?= CDN_HTTP ?>/lib/fullcalendar/fullcalendar.min.css" />
                <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/moment.min.js"></script>
                <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/fullcalendar.min.js"></script>
                <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/ko.js"></script>
                <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/gcal.min.js"></script>
                <link rel="stylesheet" type="text/css" href="<?= CDN_HTTP ?>/lib/fullcalendar/fullcalendar.custom.css?v=<?= $v_txt ?>" />
                <script>
                    (function($) {
                        'use strict';
                        $(function() {
                            if ($('#schedule_box').length) {
                                var $calendar = $('#schedule_box');
                                $calendar.fullCalendar({
                                    locale: 'ko',
                                    header: false,
                                    viewRender: (view) => {
                                        let date
                                        switch (view.type) {
                                            case 'month':
                                                date = view.title
                                                break
                                        }
                                        <?php if ($_GET['sdate']) { ?>
                                            $('#schedule-title').text('<?= DateType($_GET['sdate'], '19') ?>');
                                        <?php } else { ?>
                                            $('#schedule-title').text(date);
                                            // console.log(view);
                                        <?php } ?>
                                    },
                                    defaultView: 'month', // 주 단위로 변경
                                    navLinks: false,
                                    editable: false,
                                    eventLimit: true,
                                    displayEventTime: false,
                                    allDayDefault: true,
                                    contentHeight: "auto",
                                    eventSources: [{
                                        url: './schedule_update',
                                        type: 'POST',
                                        data: {
                                            act: 'event_source',
                                            sgdt_mt_idx: $('#sgdt_mt_idx').val(),
                                        },
                                        error: function() {
                                            console.log('잘못된 접근입니다.');
                                        },
                                    }],
                                    eventAfterAllRender: function(view) {
                                        $('.fc-event-container').html('<div class="event_dot"></div>');
                                        $('.fc-more-cell').html('<div class="event_dot"></div>');
                                    },
                                    eventClick: function(event) {
                                        console.log('event sst_idx: ' + event.start._i);
                                    },
                                    dayClick: function(date, jsEvent, view) {
                                        var sdate = date.format();

                                        if (typeof(history.pushState) != "undefined") {
                                            var state = '';
                                            var title = '';
                                            var url = './log?sdate=' + sdate;
                                            history.pushState(state, title, url);

                                            $('#event_start_date').val(sdate);
                                            setTimeout(() => {
                                                f_get_info_location($('#sgdt_mt_idx').val(), sdate);
                                            }, 100);
                                        } else {
                                            location.href = url;
                                        }
                                    }
                                });
                                $("#btn-schedule-next").on('click', function() {
                                    $calendar.fullCalendar('next')
                                });
                                $("#btn-schedule-prev").on('click', function() {
                                    $calendar.fullCalendar('prev')
                                });
                                $("#btn-schedule-today").on('click', function() {
                                    $calendar.fullCalendar('today');
                                });
                            }
                        });
                    })(jQuery);
                </script>
                <div id="schedule_box"></div>
            </div>
        </div>
        <script>
            // 달력 바텀시트 업다운
            $('.down_wrap').click(function() {
                var cldDateWrap = $('.sch_cld_wrap .cld_date_wrap');

                // .on 클래스를 토글
                cldDateWrap.toggleClass('on');

                // .on 클래스의 유무에 따라 이미지 파일 이름 변경
                var imgSrc = cldDateWrap.hasClass('on') ? 'btn_tl_arrow.png' : 'btn_bl_arrow.png';
                $('.down_wrap img.top_down').attr('src', './img/' + imgSrc);

                // CSS 스타일 추가
                if (cldDateWrap.hasClass('on')) {
                    $('.sch_wrap').css('padding-top', '34.7rem');
                } else {
                    $('.sch_wrap').css('padding-top', '15.3rem');
                }
            });
        </script>
    <?
    }
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
    $lsdate = $_POST['lsdate'];
    $ledate = $_POST['ledate'];

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');

    if ($mem_row['mt_level'] == 5) {
        // 오늘날짜로부터 14일 전까지 표시
        $start_date = date('Y-m-d', strtotime($sdate . '-14 days'));
        $sdate = date('Y-m-d', strtotime($sdate));
    } else if ($mem_row['mt_level'] == 9) {
        $start_date = date('Y-m-d', strtotime($sdate . '-14 days'));
        $sdate = date('Y-m-d', strtotime($sdate));
    } else {
        // 오늘날짜로부터 하루 전까지 표시
        $start_date = date('Y-m-d', strtotime($sdate . '-2 day'));
        $sdate = date('Y-m-d', strtotime($sdate));
    }

    $get_first_date_week = date('w', make_mktime($start_date));
    $get_end_date_week = date('w', make_mktime($sdate));
    $get_first_date = date('Y-m-d', strtotime($start_date . " - " . $get_first_date_week . "days"));
    $get_end_date = date('Y-m-d', strtotime($sdate . " + " . (6 - $get_end_date_week) . "days"));

    $diff_days = strtotime($get_end_date) - strtotime($get_first_date);
    $diff_days = round($diff_days / (60 * 60 * 24)); // 초 단위를 일 단위로 변환하여 반올림

    $arr_data = array();

    if ($get_first_date) {
        $_POST['start'] = date('Y-m-d', make_mktime($get_first_date));
        $_POST['end'] = date('Y-m-d', make_mktime($get_end_date));
    }

    //나의 로그
    unset($list);
    $DB->where('mt_idx', $mt_idx_t);
    $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
    $DB->where("mlt_speed >= " . $slt_mlt_speed);
    $DB->where("(mlt_lat > 0 AND mlt_long > 0) ");
    $DB->where("( mlt_gps_time >= '" . $_POST['start'] . " 00:00:00' and mlt_gps_time <= '" . $_POST['end'] . " 23:59:59' )");
    $DB->orderby('mlt_gps_time', 'asc');
    $list = $DB->get('member_location_log_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['mlt_idx']) {
                $cd = cal_remain_days2($row['mlt_gps_time'], $row['mlt_gps_time']);
                if ($cd) {
                    for ($q = 0; $q < $cd; $q++) {
                        $sdate_t = date("Y-m-d", strtotime($row['mlt_gps_time'] . " +" . $q . " days"));
                        $arr_data[$sdate_t] = array(
                            'id' => $row['mlt_idx'],
                            'start' => $sdate_t,
                            'end' => $row['mlt_gps_time'],
                        );
                    }
                }
            }
        }
    }

    ?>
    <form>
        <div class="date_conent">
            <div class="cld_content">
                <div class="swiper cld_swiper cld_body fs_15 fw_500">
                    <ul class="swiper-wrapper flex-nowrap">
                        <?php
                        for ($d = 0; $d <= $diff_days; $d++) {
                            $c_id = date("Y-m-d", strtotime($get_first_date  . " +" . $d . "days"));
                            $c_id2 = date("j", strtotime(date($c_id))); //일
                            $c_id3 = date("w", strtotime(date($c_id))); //요일
                            $c_id4 = date("n", strtotime(date($c_id))); //월
                            $c_id5 = date("n", strtotime(date($start_date))); //월
                            $c_id6 = date("Y-m-d", strtotime($start_date)); //로그 시작일
                            $c_id7 = date("Y-m-d", strtotime($get_end_date)); //로그 주마지막일
                            $c_id8 = date("Y-m-d", strtotime($get_first_date)); //로그 주시작일
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
                            if ($c_id <= $c_id6 || $c_id > date("Y-m-d")) {
                                $lastday_c = ' lastday ';
                                $week_c = '';
                            } else {
                                $lastday_c = '';
                            }

                            if (!$lastday_c) {
                                echo '<li onclick="f_day_click(\'' . $c_id . '\');" class="swiper-slide"><div id="calendar_' . $c_id . '" class="c_id ' . $week_c . $today_c . $schdl_c .  $lastday_c . '"><span>' . $c_id2 . '</span></div></li>';
                            } else  if ($c_id4 == $c_id5 && $lastday_c) {
                                echo '<li class="swiper-slide"><div id="calendar_' . $c_id . '" class="c_id ' . $week_c . $today_c . $schdl_c .  $lastday_c . '"><span>' . $c_id2 . '</span></div></li>';
                            } else {
                                // echo '<li onclick="f_day_click(\'' . $c_id . '\');" class="swiper-slide"><div id="calendar_' . $c_id . '" class="c_id lastday "><span>' . $c_id2 . '</span></div></li>';
                                echo '<li onclick="(\'' . $c_id . '\');" class="swiper-slide"><div id="calendar_' . $c_id . '" class="c_id lastday "><span>' . $c_id2 . '</span></div></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </form>
    <script>
        // 오늘 날짜에 해당하는 리스트 아이템 찾기
        var todayItem = document.querySelector('.cld_swiper .swiper-slide .today');

        // 오늘 날짜에 해당하는 아이템이 있으면
        if (todayItem) {
            // 해당 아이템의 인덱스 찾기
            var todayIndex = Array.prototype.indexOf.call(todayItem.parentNode.children, todayItem);

            // 페이지네이션 슬라이더로 포커스 이동
            // cld_swiper.slideTo(todayIndex);
        }
        //달력슬라이드
        var cld_swiper = new Swiper(".cld_swiper", {
            slidesPerView: 7,
            slidesPerGroup: 7,
            spaceBetween: 0,
            initialSlide: 100, // 마지막장으로
            navigation: {
                nextEl: ".add_cal_tit .swiper-button-next",
                prevEl: ".add_cal_tit .swiper-button-prev",
            },
        });
    </script>
    <?
} elseif ($_POST['act'] == "location_log") {
    if ($_POST['mt_idx']) {
        $arr_sst_idx = get_schedule_array($_POST['mt_idx'], $_POST['event_start_date']);
        $cnt = count($arr_sst_idx);

        $rtn = get_gps_distance($_POST['mt_idx'], $_POST['event_start_date']);
    ?>
        <li class="text-center border-right flex-fill loc_rog_ul_l11">
            <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">일정개수</p>
            <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic"><?= number_format($cnt) ?><span>개</span></p>
        </li>
        <li class="text-center border-right flex-fill loc_rog_ul_l12">
            <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">이동거리</p>
            <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic"><?= get_distance_km($rtn[0]) ?></p>
        </li>
        <li class="text-center border-right flex-fill loc_rog_ul_l13">
            <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">이동시간</p>
            <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic"><?= get_distance_hm($rtn[1]) ?></p>
        </li>
        <li class="text-center flex-fill loc_rog_ul_l14">
            <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">걸음수</p>
            <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic"><?= $rtn[2] ?>걸음</p>
        </li>
    <?php
    }
} elseif ($_POST['act'] == "get_line") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');

    // 기본 지도 위치 지정
    if ($sgdt_row) {
        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $mem_row = $DB->getone('member_t');

        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        // $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
        // $DB->where("mlt_speed >= " . $slt_mlt_speed);
        $DB->orderby('mlt_gps_time', 'desc');
        $mt_location_info = $DB->getone('member_location_log_t');

        if ($_SESSION['_mt_lat'] == '') {
            $_SESSION['_mt_lat'] = 37.5665;
        }
        if ($_SESSION['_mt_long'] == '') {
            $_SESSION['_mt_long'] = 126.9780;
        }
        $arr_data['my_lat'] = $mt_location_info['mlt_lat'] == "" ? $_SESSION['_mt_lat'] : $mt_location_info['mlt_lat'];
        $arr_data['mt_long'] = $mt_location_info['mlt_long'] == "" ? $_SESSION['_mt_long'] :  $mt_location_info['mlt_long'];
        $arr_data['my_profile'] = $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']);
        
    } else {
        $arr_data['my_lat'] = $_SESSION['_mt_lat'] == "" ? 37.5665 : $_SESSION['_mt_lat'];
        $arr_data['mt_long'] = $_SESSION['_mt_long'] == "" ? 126.9780 : $_SESSION['_mt_long'];
        $arr_data['my_profile'] = $_SESSION['_mt_file1'] == "" ? $ct_no_img_url : $_SESSION['_mt_file1'];
    }

    // 일정 마커 구하기
    $arr_sst_idx = get_schedule_main($_POST['sgdt_idx'], $_POST['event_start_date'], $sgdt_row['mt_idx']);
    $cnt = count($arr_sst_idx);
    if ($cnt < 1) {
        // JSON으로 변환하여 출력
        $arr_data['schedule_chk'] = 'N';
    } else {
        $arr_sst_idx_im = implode(',', $arr_sst_idx);
        unset($list_sst);
        $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
        $DB->where('sst_show', 'Y');
        // $DB->groupBy("mt_idx");
        $DB->orderBy("sst_all_day", "asc");
        $DB->orderBy("sst_sdate", "asc");
        $list_sst = $DB->get('smap_schedule_t');

        if ($list_sst) {
            $count = 1;
            $current_date = date('Y-m-d H:i:s');
            foreach ($list_sst as $row_sst_a) {
                $mt_info = get_member_t_info($row_sst_a['mt_idx']);
                $mt_file1_url = get_image_url($mt_info['mt_file1']);

                if ($row_sst_a['sst_all_day'] == 'Y') {
                    $sst_all_day_t = '하루종일';
                } else {
                    $repeat_array = json_decode($row_sst_a['sst_repeat_json'], true);
                    // 반복을 저장할 배열
                    $repeat_values = array();

                    // "r1"이 1이 아니거나 값이 없는 경우를 제외하고 반복을 생성
                    if ($repeat_array['r1'] == 1 || empty($repeat_array['r1'])) {
                        $sst_sdate_d1 = datetype($row_sst_a['sst_sdate'], 5);
                        $sst_sdate_e1 = get_date_ttime($row_sst_a['sst_sdate']);
                        $sst_sdate_d2 = datetype($row_sst_a['sst_edate'], 5);
                        $sst_sdate_e2 = get_date_ttime($row_sst_a['sst_edate']);
                        // $sst_all_day_t = $sst_sdate_d1 . $sst_sdate_e1 . ' ~ ' . $sst_sdate_d2 . $sst_sdate_e2;
                        $sst_all_day_t = $sst_sdate_e1 . ' ~ ' . $sst_sdate_e2;
                    } else {
                        $sst_sdate_e1 = get_date_ttime($row_sst_a['sst_sdate']);
                        $sst_sdate_e2 = get_date_ttime($row_sst_a['sst_edate']);
                        $sst_all_day_t = $sst_sdate_e1 . ' ~ ' . $sst_sdate_e2;
                    }
                }
                // $content = 
                //         '<div class="point_wrap point1">
                //             <button type="button" class="btn point_sch">
                //                 <span class="point_inner">
                //                     <img src="./img/sch_alarm.png" alt="Desired Image" class="btn point point_ing" style="width: 24px; height: 24px;"/>
                //                     <span class="point_txt"></span>
                //                 </span>
                //             </button>
                //             <div class="infobox infobox_2 rounded_04 px_08 py_03 on" style="background-color: #413F4A !important; top: -2rem; right: -3.5rem;">
                //                 <span class="fs_12 fw_700 text_dynamic line_h1_2" style="color: ' . $random_color . '; display: block; width: 100%;">' . $row_sst_a['sst_title'] . '</span>
                //             </div>
                //         </div>';
                $content = '
                    <style>
                    .infobox1 {
                        position: absolute !important;
                        left: 50%; /* 아이콘의 중심에 위치 */
                        top: 100%; /* 아이콘의 아래쪽에 위치 */
                        transform: translate(-50%, -80%); /* 중앙 정렬 및 약간 아래쪽으로 이동 */
                        background-color: #413F4A;
                        padding: 0.3rem 0.8rem; /* 상하 0.3rem, 좌우 0.8rem */
                        border-radius: 0.4rem;
                        z-index: 1;
                        white-space: nowrap; /* 한 줄로 표시 */
                    }
                    
                    .infobox1 span {
                        color: ' . $random_color . ';
                        font-size: 12px !important;
                        white-space: nowrap !important;
                        overflow: hidden !important;
                        text-overflow: ellipsis !important;
                    }
                    
                    </style>
                    <div class="point_wrap point1">
                        <button type="button" class="btn point point_sch">
                            <span class="point_inner">
                                <img src="./img/sch_alarm.png" alt="Desired Image" class="btn point point_ing" style="width: 24px; height: 24px;"/>
                            </span>
                        </button>
                        <div class="infobox1 rounded_04 px_08 py_03 on">
                            <span class="fs_12 fw_800 text_dynamic line_h1_2 mt-2">' . $row_sst_a['sst_title'] . '</span>
                        </div>
                    </div>
                ';

                $arr_data['markerLat_' . $count] = $row_sst_a['sst_location_lat'];
                $arr_data['markerLong_' . $count] = $row_sst_a['sst_location_long'];
                $arr_data['markerContent_' . $count] = $content;
                $count++;
            }
        }
        // JSON으로 변환하여 출력
        $arr_data['schedule_chk'] = 'Y';
        $arr_data['count'] = $count - 1;
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($mem_row['mt_level'] == '2') {
        $limit = 4;
    } else {
        $limit = 10;
    }
    // 내장소 마커 구하기
    unset($list_slt);
    $DB->where("( mt_idx = '" . $sgdt_row['mt_idx'] . "' or sgdt_idx = '" . $_POST['sgdt_idx'] . "' )");
    $DB->where('slt_show', 'Y');
    $DB->orderby('slt_wdate', 'asc');
    $list_slt = $DB->get('smap_location_t', $limit);
    if ($list_slt) {
        $mycount = 1;
        foreach ($list_slt as $row_slt) {
            // (로그마커조정) 로그에 있는 내 장소 마커
            $content = '
                <style>
                .infobox2 {
                    position: absolute;
                    left: 50%; /* 아이콘의 중심에 위치 */
                    top: 100%; /* 아이콘의 아래쪽에 위치 */
                    transform: translate(-50%, 40%); /* 중앙 정렬 및 약간 아래쪽으로 이동 */
                    background-color: #413F4A;
                    padding: 0.3rem 0.8rem; /* 상하 0.3rem, 좌우 0.8rem */
                    border-radius: 0.4rem;
                    z-index: 1;
                    white-space: nowrap; /* 한 줄로 표시 */
                }
                
                .infobox2 span {
                    color: ' . $random_color . ';
                    font-size: 12px !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
                
                </style>
                <div class="point_wrap point1">
                    <button type="button" class="btn point point_myplc">
                        <span class="point_inner">
                            <img src="./img/loc_alarm.png" alt="Desired Image" class="btn point point_ing" style="width: 24px; height: 24px;"/>
                        </span>
                    </button>
                    <div class="infobox2 rounded_04 px_08 py_03 on">
                        <span class="fs_12 fw_800 text_dynamic line_h1_2 mt-2">' . $row_slt['slt_title'] . '</span>
                    </div>
                </div>
            ';

            $arr_data['locationmarkerLat_' . $mycount] = $row_slt['slt_lat'];
            $arr_data['locationmarkerLong_' . $mycount] = $row_slt['slt_long'];
            $arr_data['locationmarkerContent_' . $mycount] = $content;
            $mycount++;
        }
        $arr_data['location_chk'] = 'Y';
        $arr_data['location_count'] = $mycount - 1;
    } else {
        $arr_data['location_chk'] = 'N';
        $arr_data['location_count'] = 0;
    }
    // 오늘자 이동로그 구하기
    $current_date = date('Y-m-d H:i:s');

    $total_log_count = 1;
    $stay_count = 1;
    // 마커 배열에 담은 후 재배치 필요
    $location_data = array();
    // 전체 이동로그 구하기
    unset($list_move);
    $move_query = "
        WITH initial_data AS (
                SELECT
                    mlt_idx,
                    mt_idx,
                    mlt_gps_time,
                    mlt_speed,
                    mlt_lat,
                    mlt_long,
                    mt_health_work,
                    mlt_accuacy,
                    LAG(mt_health_work) OVER (
                    ORDER BY mlt_gps_time) AS prev_mt_health_work,
                    LAG(mlt_lat) OVER (
                    ORDER BY mlt_gps_time) AS prev_lat,
                    LAG(mlt_long) OVER (
                    ORDER BY mlt_gps_time) AS prev_long,
                    CASE
                        WHEN mlt_speed > 1 AND mt_health_work > LAG(mt_health_work) OVER (ORDER BY mlt_gps_time) THEN 'move'
                        WHEN mlt_speed > 1 AND mlt_accuacy < 35 THEN 'move'
                        ELSE 'stay'
                    END AS label
                FROM (
                    SELECT 
                        *,
                        (@row_num := @row_num + 1) AS row_num
                    FROM (
                        SELECT *
                        FROM member_location_log_t
                        WHERE 
                            mt_idx = '" . $_POST['sgdt_mt_idx'] . "'
                            AND mlt_accuacy  < " . $slt_mlt_accuacy . "
                            AND mlt_gps_time BETWEEN '" . $_POST['event_start_date'] . " 00:00:00' AND '" . $_POST['event_start_date'] . " 23:59:59'
                        ORDER BY mlt_gps_time
                    ) t, (SELECT @row_num := 0) r
                ) s
                WHERE MOD(row_num, 8) = 1
            ),
        labeled_data AS (
        SELECT
            *,
            (CASE 
                WHEN LAG(mlt_lat) OVER (ORDER BY mlt_gps_time) IS NULL THEN @distance := 0
                ELSE @distance := (6371 * ACOS(COS(RADIANS(LAG(mlt_lat) OVER (ORDER BY mlt_gps_time))) * COS(RADIANS(mlt_lat)) * COS(RADIANS(mlt_long) - RADIANS(LAG(mlt_long) OVER (ORDER BY mlt_gps_time))) + SIN(RADIANS(LAG(mlt_lat) OVER (ORDER BY mlt_gps_time))) * SIN(RADIANS(mlt_lat))))
            END) AS distance
            FROM
                initial_data
        ),
        window_function_data AS (
        SELECT
            *,
            SUM(CASE
        WHEN mlt_speed >= 1 AND mt_health_work > prev_mt_health_work THEN 1
        WHEN mlt_speed >= 1 AND mlt_accuacy < 35 THEN 1
        ELSE 0
        END) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN 100 PRECEDING AND CURRENT ROW) AS mp_cnt,
            AVG(mlt_speed) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN 10 PRECEDING AND CURRENT ROW) AS avg_speed_last_10,
            AVG(mlt_speed) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS avg_speed_last_5,
            AVG(mlt_speed) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 5 FOLLOWING) AS avg_speed_next_5,
            AVG(mlt_speed) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 10 FOLLOWING) AS avg_speed_next_10
        FROM
            labeled_data
        ),
        final_data AS (
        SELECT
            *,
            AVG(mp_cnt) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN 10 PRECEDING AND CURRENT ROW) AS avg_ap_cnt_last_10,
            AVG(mp_cnt) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS avg_ap_cnt_last_5,
            AVG(mp_cnt) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 5 FOLLOWING) AS avg_ap_cnt_next_5,
            AVG(mp_cnt) OVER (
            ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 10 FOLLOWING) AS avg_ap_cnt_next_10
        FROM
            window_function_data
        ),
        move_stay_logic AS (
        SELECT
            *,
            CASE
                WHEN mp_cnt > avg_ap_cnt_last_10 - 1
                AND avg_speed_last_10 > 1 THEN 'move'
                WHEN mp_cnt > avg_ap_cnt_last_5 - 1
                AND avg_speed_last_5 > 1 THEN 'move'
                WHEN mp_cnt > 0
                AND mp_cnt < avg_ap_cnt_next_10
                AND avg_speed_next_10 > 0.7 THEN 'move'
                WHEN mp_cnt > 0
                AND mp_cnt < avg_ap_cnt_next_5
                AND avg_speed_next_5 > 0.7 THEN 'move'
                WHEN mp_cnt >= avg_ap_cnt_next_5
                AND avg_speed_next_5 < 1 THEN 'stay'
                WHEN mp_cnt >= avg_ap_cnt_next_10
                AND avg_speed_next_10 < 1 THEN 'stay'
                ELSE label
            END AS move_stay
            FROM
                final_data
        ),
        labeled_data_with_lag AS (
        SELECT
            *,
            LAG(move_stay) OVER (
            ORDER BY mlt_gps_time) AS prev_label
            -- 이전위치 라벨
        FROM
            move_stay_logic
        ),
        labeled_data_with_grp AS ( -- 연속된 로그 항목을 그룹화
            SELECT
                *,
                SUM(CASE WHEN move_stay <> prev_label THEN 1 ELSE 0 END) OVER (ORDER BY mlt_gps_time) AS grp -- stay, move 사이의 변경점마다 새로운 그룹을 할당
            FROM
                labeled_data_with_lag
        ),
        labeled_data_with_grp_status AS ( -- 그룹의 시작 및 종료를 식별하고, 각 그룹의 첫 번째 및 마지막 로그를 확인
            SELECT
                *,
                CASE
                    WHEN ROW_NUMBER() OVER (PARTITION BY grp ORDER BY mlt_gps_time) = 1 THEN 'S' 
                    WHEN ROW_NUMBER() OVER (PARTITION BY grp ORDER BY mlt_gps_time DESC) = 1 THEN 'E' 
                    WHEN mlt_gps_time = (SELECT MAX(mlt_gps_time) FROM labeled_data_with_grp) THEN 'E'
                    ELSE NULL
                END AS grp_status
            FROM
                labeled_data_with_grp
        ),
        filtered_data AS ( -- 움직임의 시작과 끝이 모두 포함된 그룹만 선택
            SELECT
                *
            FROM
                labeled_data_with_grp_status
            WHERE
                grp IN (
                    SELECT
                        grp
                    FROM
                        labeled_data_with_grp_status
                    GROUP BY
                        grp
                    HAVING
                        COUNT(CASE WHEN grp_status = 'S' THEN 1 END) = 1
                        AND COUNT(CASE WHEN grp_status = 'E' THEN 1 END) >= 1
                )
        )
        SELECT
            mlt_speed,
            mlt_lat,
            mlt_long,
            mt_health_work,
            mlt_accuacy, 
            mlt_gps_time,
            prev_lat,
            prev_long,
            distance,
            move_stay as label
        FROM
            filtered_data
        WHERE 1=1
        AND (move_stay = 'move' AND distance > 0.1) OR prev_lat IS NULL
        ORDER BY mlt_gps_time
";    
    $list_move = $DB->Query($move_query);
    // 오늘 자 이동로그가 있을 때
    if ($list_move) {
        $move_log_count = 1;
        $list_count = count($list_move);
        $move_count = count($list_move);
        foreach ($list_move as $row_mlt) {
            if ($move_log_count == '1') {
                // 00:00분 처음 들어간 로그 확인하기
                $DB->where('mt_idx', $_POST['sgdt_mt_idx']);
                $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
                $DB->where("mlt_speed < " . $slt_mlt_speed);
                $DB->where("(mlt_lat > 0 and mlt_long > 0)");
                $DB->where("mlt_gps_time BETWEEN '" . $_POST['event_start_date'] . " 00:00:00' AND '" . $_POST['event_start_date'] . " 23:59:59'");
                $DB->orderBy("mlt_gps_time", "asc");
                $first_location = $DB->getone('member_location_log_t');

                if ($first_location['rownum'] == 1) {
                    $first_log_date = strtotime($_POST['event_start_date'] . " 00:00:00");
                    $last_log_date = strtotime($first_location['mlt_gps_time']);
                    $stay_time_seconds = $last_log_date - $first_log_date;
                
                    if ($stay_time_seconds >= 300) {
                        // 시간과 분으로 변환
                        $hours = floor($stay_time_seconds / 3600);
                        $minutes = floor(($stay_time_seconds % 3600) / 60);

                        // 형식에 맞게 문자열로 표현
                        $stay_time_formatted = "";
                        if ($hours > 0) {
                            $stay_time_formatted .= $hours . "시간 ";
                        }
                        $stay_time_formatted .= $minutes . "분 체류";

                        $addr = get_search_coordinate2address($first_location['mlt_lat'], $first_location['mlt_long']);
                        $address =  $addr['area2'] . ' ' . $addr['area3'];
                        $content = '<div class="point_wrap point2"  data-rangeindex="' . $total_log_count . '">
                                                <button type="button" class="btn log_point point_stay">
                                                    <span class="point_inner">
                                                        <span class="point_txt">' . $stay_count . '</span>
                                                    </span>
                                                </button>
                                                <div class="infobox rounded-sm bg-white px_08 py_08">
                                                    <p class="fs_12 fw_700 text_dynamic"> 00:00 ~ ' . datetype($first_location['mlt_gps_time'], 7) . '</p>
                                                    <p class="fs_10 fw_500 text_dynamic text-primary line_h1_2 mt-2">' . $stay_time_formatted . '</p>
                                                    <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">' . $address . '</p>
                                                </div>
                                            </div>';
                        $location_data['startTime_' . $total_log_count] = $first_location['mlt_gps_time'];
                        $location_data['endTime_' . $total_log_count] = $first_location['mlt_gps_time'];
                        $location_data['logmarkerLat_' . $total_log_count] = $first_location['mlt_lat'];
                        $location_data['logmarkerLong_' . $total_log_count] = $first_location['mlt_long'];
                        $location_data['logmarkerContent_' . $total_log_count] = $content;
                        $location_data['logStatus_' . $total_log_count] = 'stay';
                        $stay_count++;
                        $total_log_count++;
                    }
                }
            }
            
            $content = '<div class="point_wrap point2 d-none log_marker"  data-rangeindex="' . $total_log_count . '">
                            <div class="infobox infobox_2 rounded-sm bg-white px_08 py_08">
                                <p class="fs_12 fw_700 text_dynamic">' . datetype($row_mlt['mlt_gps_time'], 7) . '</p>
                            </div>
                        </div>';
            $location_data['startTime_' . $total_log_count] = $row_mlt['mlt_gps_time'];
            $location_data['endTime_' . $total_log_count] = $row_mlt['mlt_gps_time'];
            $location_data['logmarkerLat_' . $total_log_count] = $row_mlt['mlt_lat'];
            $location_data['logmarkerLong_' . $total_log_count] = $row_mlt['mlt_long'];
            $location_data['logmarkerContent_' . $total_log_count] = $content;
            $location_data['logStatus_' . $total_log_count] = 'move';
            $move_log_count++;
            $total_log_count++;
        }
        // JSON으로 변환하여 출력
        $arr_data['log_chk'] = 'Y';
        $arr_data['log_count'] = $total_log_count - 1;
    } else {
        // JSON으로 변환하여 출력
        $arr_data['log_chk'] = 'N';
        $arr_data['log_count'] = 0;
    }
    //체류시간구하기
    unset($list_stay);
    $stay_query = "
       WITH initial_data AS (
            SELECT
                mlt_idx,
                mt_idx,
                mlt_gps_time,
                mlt_speed,
                mlt_lat,
                mlt_long,
                mlt_accuacy,
                mt_health_work,
                LAG(mt_health_work) OVER (
                ORDER BY mlt_gps_time) AS prev_mt_health_work,
                LAG(mlt_lat) OVER (
                ORDER BY mlt_gps_time) AS prev_lat,
                LAG(mlt_long) OVER (
                ORDER BY mlt_gps_time) AS prev_long
            FROM
                member_location_log_t
            WHERE
                mt_idx = '" . $_POST['sgdt_mt_idx'] . "'
                AND mlt_accuacy  < " . $slt_mlt_accuacy . "
                AND mlt_gps_time BETWEEN '" . $_POST['event_start_date'] . " 00:00:00' AND '" . $_POST['event_start_date'] . " 23:59:59'
            ORDER BY
                mlt_gps_time
            ),
            labeled_data AS (
            SELECT
                *,
                CASE
                    WHEN mlt_speed >= 1
                        AND mt_health_work > prev_mt_health_work THEN 'move'
                        WHEN mlt_speed >= 1
                        AND mlt_accuacy < 35 THEN 'move'
                        ELSE 'stay'
                    END AS label
               ,(CASE 
                    WHEN LAG(mlt_lat) OVER (ORDER BY mlt_gps_time) IS NULL THEN @distance := 0
                    ELSE @distance := (6371 * ACOS(COS(RADIANS(LAG(mlt_lat) OVER (ORDER BY mlt_gps_time))) * COS(RADIANS(mlt_lat)) * COS(RADIANS(mlt_long) - RADIANS(LAG(mlt_long) OVER (ORDER BY mlt_gps_time))) + SIN(RADIANS(LAG(mlt_lat) OVER (ORDER BY mlt_gps_time))) * SIN(RADIANS(mlt_lat))))
                END) AS distance
                FROM
                    initial_data
            ),
            window_function_data AS (
            SELECT
                *,
                SUM(CASE
            WHEN mlt_speed >= 1 AND mt_health_work > prev_mt_health_work THEN 1
            WHEN mlt_speed >= 1 AND mlt_accuacy < 35 THEN 1
            ELSE 0
            END) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN 100 PRECEDING AND CURRENT ROW) AS mp_cnt,
                AVG(mlt_speed) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN 10 PRECEDING AND CURRENT ROW) AS avg_speed_last_10,
                AVG(mlt_speed) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS avg_speed_last_5,
                AVG(mlt_speed) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 5 FOLLOWING) AS avg_speed_next_5,
                AVG(mlt_speed) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 10 FOLLOWING) AS avg_speed_next_10
            FROM
                labeled_data
            ),
            final_data AS (
            SELECT
                *,
                AVG(mp_cnt) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN 10 PRECEDING AND CURRENT ROW) AS avg_ap_cnt_last_10,
                AVG(mp_cnt) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS avg_ap_cnt_last_5,
                AVG(mp_cnt) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 5 FOLLOWING) AS avg_ap_cnt_next_5,
                AVG(mp_cnt) OVER (
                ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 10 FOLLOWING) AS avg_ap_cnt_next_10
            FROM
                window_function_data
            ),
            move_stay_logic AS (
            SELECT
                *,
                CASE
                    WHEN mp_cnt > avg_ap_cnt_last_10 - 1
                    AND avg_speed_last_10 > 1 THEN 'move'
                    WHEN mp_cnt > avg_ap_cnt_last_5 - 1
                    AND avg_speed_last_5 > 1 THEN 'move'
                    WHEN mp_cnt > 0
                    AND mp_cnt < avg_ap_cnt_next_10
                    AND avg_speed_next_10 > 0.7 THEN 'move'
                    WHEN mp_cnt > 0
                    AND mp_cnt < avg_ap_cnt_next_5
                    AND avg_speed_next_5 > 0.7 THEN 'move'
                    WHEN mp_cnt >= avg_ap_cnt_next_5
                    AND avg_speed_next_5 < 1 THEN 'stay'
                    WHEN mp_cnt >= avg_ap_cnt_next_10
                    AND avg_speed_next_10 < 1 THEN 'stay'
                    ELSE label
                END AS move_stay
                FROM
                    final_data
            ),
            labeled_data_with_lag AS (
            SELECT
                *,
                LAG(move_stay) OVER (
                ORDER BY mlt_gps_time) AS prev_label
                -- 이전위치 라벨
            FROM
                move_stay_logic
            ),
            labeled_data_with_grp AS ( -- 연속된 로그 항목을 그룹화
                SELECT
                    *,
                    SUM(CASE WHEN move_stay <> prev_label THEN 1 ELSE 0 END) OVER (ORDER BY mlt_gps_time) AS grp -- stay, move 사이의 변경점마다 새로운 그룹을 할당
                FROM
                    labeled_data_with_lag
            ),
            labeled_data_with_grp_status AS ( -- 그룹의 시작 및 종료를 식별하고, 각 그룹의 첫 번째 및 마지막 로그를 확인
                SELECT
                    *,
                    CASE
                        WHEN ROW_NUMBER() OVER (PARTITION BY grp ORDER BY mlt_gps_time) = 1 THEN 'S' 
                        WHEN ROW_NUMBER() OVER (PARTITION BY grp ORDER BY mlt_gps_time DESC) = 1 THEN 'E' 
                        WHEN mlt_gps_time = (SELECT MAX(mlt_gps_time) FROM labeled_data_with_grp) THEN 'E'
                        ELSE NULL
                    END AS grp_status
                FROM
                    labeled_data_with_grp
            ),
            filtered_data AS ( -- 움직임의 시작과 끝이 모두 포함된 그룹만 선택
                SELECT
                    *
                FROM
                    labeled_data_with_grp_status
                WHERE
                    grp IN (
                        SELECT
                            grp
                        FROM
                            labeled_data_with_grp_status
                        GROUP BY
                            grp
                        HAVING
                            COUNT(CASE WHEN grp_status = 'S' THEN 1 END) = 1
                            AND COUNT(CASE WHEN grp_status = 'E' THEN 1 END) >= 1
                    )
            ),
            result_data AS (
                SELECT
                    move_stay,
                    grp,
                    MIN(CASE WHEN grp_status = 'S' THEN mlt_gps_time END) AS start_time, -- 첫 로그 찍힌 시간
                    MAX(CASE WHEN grp_status = 'E' THEN mlt_gps_time END) AS end_time, -- 마지막 로그 찍힌 시간
                    TIMESTAMPDIFF(SECOND, MIN(CASE WHEN grp_status = 'S' THEN mlt_gps_time END), MAX(CASE WHEN grp_status = 'E' THEN mlt_gps_time END)) / 60 AS duration, -- 머문시간 분으로 표출
                    SUM(
                        CASE WHEN prev_lat IS NOT NULL AND prev_long IS NOT NULL -- 이전 위경도 값이 비어있지 않을 경우
                        THEN 
                            (6371 * ACOS(
                                COS(RADIANS(mlt_lat)) * COS(RADIANS(prev_lat)) * COS(RADIANS(prev_long) - RADIANS(mlt_long)) + SIN(RADIANS(mlt_lat)) * SIN(RADIANS(prev_lat))
                            ))
                        ELSE 0 
                        END
                    ) AS distance, -- 이동한 거리(6371 : 지구 반지름(km))
                    MAX(CASE WHEN grp_status = 'S' THEN mlt_lat END) AS start_lat, -- 시작위치의 위도
                    MAX(CASE WHEN grp_status = 'S' THEN mlt_long END) AS start_long -- 시작위치의 경도
                FROM
                    filtered_data
                GROUP BY
                    move_stay, grp
            )
            SELECT
                move_stay as label,
                grp,
                start_time,
                end_time,
                duration,
                distance,
                start_lat,
                start_long
            FROM
                result_data
            WHERE 1=1
                AND duration >= 5 -- 체류시간이 5분이상일때
            ORDER BY
                start_time
            ";
    $list_stay = $DB->Query($stay_query);
    // Function to calculate distance between two coordinates using Haversine formula
    function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // 오늘 자 체류로그가 있을 때
    if ($list_stay) {
        $log_count = 1;
        $list_count_stay = count($list_stay);
        $staylog_count = count($list_stay);
        $filtered_stays = [];

        // First stay data point is always kept
        $filtered_stays[] = $list_stay[0];

        for ($i = 1; $i < $list_count_stay; $i++) {
            $prev_stay = &$filtered_stays[count($filtered_stays) - 1];
            $current_stay = $list_stay[$i];
        
            // Calculate the distance between the previous stay and the current stay
            $distance = haversineDistance($prev_stay['start_lat'], $prev_stay['start_long'], $current_stay['start_lat'], $current_stay['start_long']);
        
            // Check if both labels are the same
            if ($prev_stay['label'] === $current_stay['label']) {
                // If the distance is within 100 meters, accumulate duration and update end_time
                if ($distance <= 100) {
                    $prev_stay['duration'] += $current_stay['duration'];
                    $prev_stay['end_time'] = $current_stay['end_time'];
                } else {
                    // If the distance is greater than 100 meters, keep the current stay
                    $filtered_stays[] = $current_stay;
                }
            } else {
                // If the labels are different, keep the current stay
                $filtered_stays[] = $current_stay;
            }
        }

        // Process the filtered stays
        foreach ($filtered_stays as $row_mlt) {
            // Check if the label is 'stay'
            if ($row_mlt['label'] === 'stay') {
                // Convert start_time and end_time to DateTime objects for accurate calculation
                $start_time = new DateTime($row_mlt['start_time']);
                $end_time = new DateTime($row_mlt['end_time']);
                
                // Calculate duration in minutes
                $duration = $end_time->getTimestamp() - $start_time->getTimestamp();
                $hours = floor($duration / 3600);
                $minutes = floor(($duration % 3600) / 60);

                // Format stay time
                $stay_time_formatted = "";
                if ($hours > 0) {
                    $stay_time_formatted .= $hours . "시간 ";
                }
                $stay_time_formatted .= $minutes . "분 체류";

                // Get address
                $addr = get_search_coordinate2address($row_mlt['start_lat'], $row_mlt['start_long']);
                $address =  $addr['area2'] . ' ' . $addr['area3'];

                // Content formatting
                $content = '<div class="point_wrap point2" data-rangeindex="' . $total_log_count . '">
                                            <button type="button" class="btn log_point point_stay">
                                                <span class="point_inner">
                                                    <span class="point_txt">' . $stay_count . '</span>
                                                </span>
                                            </button>
                                            <div class="infobox rounded-sm bg-white px_08 py_08">
                                                <p class="fs_12 fw_700 text_dynamic">' . $start_time->format('H:i') . ' ~ ' . $end_time->format('H:i') . '</p>
                                                <p class="fs_10 fw_500 text_dynamic text-primary line_h1_2 mt-2">' . $stay_time_formatted . '</p>
                                                <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">' . $address . '</p>
                                            </div>
                                        </div>';
                $stay_count++;

                // Location data storage
                $location_data['startTime_' . $total_log_count] = $row_mlt['start_time'];
                $location_data['endTime_' . $total_log_count] = $row_mlt['end_time'];
                $location_data['logmarkerLat_' . $total_log_count] = $row_mlt['start_lat'];
                $location_data['logmarkerLong_' . $total_log_count] = $row_mlt['start_long'];
                $location_data['logmarkerContent_' . $total_log_count] = $content;
                $location_data['logStatus_' . $total_log_count] = 'stay';
                $log_count++;
                $total_log_count++;
            }
        }

        // JSON output
        $arr_data['log_chk'] = 'Y';
        $arr_data['log_count'] = $total_log_count - 1;

    } else {
        if ($list_move) {
            // Handle move data if necessary
        } else {
            // JSON output for no logs
            $arr_data['log_chk'] = 'N';
            $arr_data['log_count'] = 0;
        }
    }

    // 파일에 JSON 데이터 저장
    // if (file_put_contents('./log_240610.txt', array_values($location_data)) === false) {
    //     echo json_encode(array("error" => "Failed to write to log file"));
    //     exit;
    // }

    if ($arr_data['log_count'] > 0) {
        // $location_data 배열의 키를 재정렬
        $location_data = array_values($location_data);
        $event_count = count($location_data) / 6; // 각 이벤트당 6개의 항목이 있으므로, 전체 항목을 6으로 나눠서 이벤트 개수를 계산합니다.
        $events = array();

        for ($i = 0; $i < $event_count; $i++) {
            $start_index = $i * 6;
            $content = $location_data[$start_index + 4];
            $log_status = $location_data[$start_index + 5];
            preg_match('/data-rangeindex="(\d+)"/', $content, $matches); // content에서 data-rangeindex 값을 추출합니다.
            $range_index = $matches[1]; // 추출된 data-rangeindex 값을 저장합니다.
            // 시작 시간과 종료 시간 비교하여 더 빠른 값을 선택합니다.
            $start_time = strtotime($location_data[$start_index]);
            $end_time = strtotime($location_data[$start_index + 1]);
            if ($start_time <= $end_time) {
                $time_key = 'startTime';
                $time_value = $location_data[$start_index];
            } else {
                $time_key = 'startTime';
                $time_value = $location_data[$start_index + 1];
            }            

            $event = array(
                $time_key => $time_value,
                'latitude' => $location_data[$start_index + 2],
                'longitude' => $location_data[$start_index + 3],
                'content' => $content,
                'rangeIndex' => $range_index, // data-rangeindex 값을 저장합니다.
                'logStatus' => $log_status // stay 또는 move 여부를 저장합니다.
            );
            if ($log_status === 'stay') {
                array_unshift($events, $event); // stay인 경우 배열의 앞에 추가
            } else {
                $events[] = $event; // move인 경우 배열의 뒤에 추가
            }
        }

        // 시작 시간을 기준으로 정렬
        usort($events, function ($a, $b) {
            return strtotime($a['startTime']) - strtotime($b['startTime']);
        });

        // $arr_data 배열에 각 이벤트의 정보를 담습니다.
        foreach ($events as $index => $event) {
            $arr_data['logmarkerLat_' . ($index + 1)] = $event['latitude'];
            $arr_data['logmarkerLong_' . ($index + 1)] = $event['longitude'];
            // content 내용도 수정하여 저장합니다.
            $arr_data['logmarkerContent_' . ($index + 1)] = str_replace('data-rangeindex="' . $event['rangeIndex'] . '"', 'data-rangeindex="' . ($index + 1) . '"', $event['content']);
        }

        // 이벤트 데이터를 JSON 형식으로 인코딩
        $json_data = json_encode($events, JSON_PRETTY_PRINT);

        // 파일에 JSON 데이터 저장
        // if (file_put_contents('./log_data_240610.json', $json_data) === false) {
        //     echo json_encode(array("error" => "Failed to write to log file"));
        //     exit;
        // }
    }

    // $arr_data 배열 출력
    echo json_encode($arr_data);
    exit;
} elseif ($_POST['act'] == "input_location") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->where('slt_add', $_POST['sst_location_add']);
    $DB->where('slt_show', 'Y');
    $row_slt = $DB->getone('smap_location_t');

    if ($row_slt['slt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "slt_show" => "N",
        );

        $DB->where('slt_idx', $row_slt['slt_idx']);

        // $DB->update('smap_location_t', $arr_query);

        $_last_idx = $row_slt['slt_idx'];
    } else {
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $_SESSION['_mt_idx'],
            "slt_title" => $_POST['slt_title'],
            "sgt_idx" => $_POST['sgt_idx'],
            "sgdt_idx" => $_POST['sgdt_idx'],
            "slt_add" => $_POST['sst_location_add'],
            "slt_lat" => $_POST['sst_location_lat'],
            "slt_long" => $_POST['sst_location_long'],
            "slt_show" => "Y",
            "slt_enter_alarm" => "Y",
            "slt_wdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('smap_location_t', $arr_query);
    }

    p_alert('등록되었습니다.', './location', '');
} elseif ($_POST['act'] == "my_location_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($mem_row['mt_level'] == '2') {
        $limit = 4;
    } else {
        $limit = 10;
    }

    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->where('slt_show', 'Y');
    $DB->orderby('slt_wdate', 'asc');
    $slt_list = $DB->get('smap_location_t', $limit);
    $cnt = count($slt_list);
    unset($result_data);
    $count = 1;

    if ($cnt < 1) {
        // 기본 지도 위치 지정
        if ($sgdt_row) {
            $DB->where('mt_idx', $sgdt_row['mt_idx']);
            $mem_row = $DB->getone('member_t');

            $DB->where('mt_idx', $sgdt_row['mt_idx']);
            $DB->orderby('mlt_gps_time', 'desc');
            $mt_location_info = $DB->getone('member_location_log_t');

            $result_data = array(
                "my_lat" => $mt_location_info['mlt_lat'] == "" ? $_SESSION['_mt_lat'] : $mt_location_info['mlt_lat'],
                "mt_long" => $mt_location_info['mlt_long'] == "" ? $_SESSION['_mt_long'] :  $mt_location_info['mlt_long'],
                "my_profile" => $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']),
            );
        } else {
            $result_data = array(
                "my_lat" => $_SESSION['_mt_lat'] == "" ? 37.5665 : $_SESSION['_mt_lat'],
                "mt_long" => $_SESSION['_mt_long'] == "" ? 126.9780 : $_SESSION['_mt_long'],
                "my_profile" => $_SESSION['_mt_file1'] == "" ? $ct_no_img_url : $_SESSION['_mt_file1'],
            );
        }
    } else {
        unset($result_data);
        foreach ($slt_list as $row_slt) {
            if ($count == 1) {
                $DB->where('mt_idx', $sgdt_row['mt_idx']);
                $mem_row = $DB->getone('member_t');

                $DB->where('mt_idx', $sgdt_row['mt_idx']);
                $DB->orderby('mlt_gps_time', 'desc');
                $mt_location_info = $DB->getone('member_location_log_t');

                $result_data = array(
                    "my_lat" => $mt_location_info['mlt_lat'] == "" ? $_SESSION['_mt_lat'] : $mt_location_info['mlt_lat'],
                    "mt_long" => $mt_location_info['mlt_long'] == "" ? $_SESSION['_mt_long'] :  $mt_location_info['mlt_long'],
                    "my_profile" => $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']),
                );
            }
            //(내장소 마커 조정)내 장소에 있는 내 장소 마커
            $content = '
                        <style>
                        .infobox3 {
                            position: absolute;
                            left: 50%; /* 아이콘의 중심에 위치 */
                            top: 100%; /* 아이콘의 아래쪽에 위치 */
                            transform: translate(-50%, -70%); /* 중앙 정렬 및 약간 아래쪽으로 이동 */
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; /* 상하 0.3rem, 좌우 0.8rem */
                            border-radius: 0.4rem;
                            z-index: 1;
                            white-space: nowrap; /* 한 줄로 표시 */
                        }
                        
                        .infobox3 span {
                            color: ' . $random_color . ';
                            font-size: 12px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        
                        </style>
                        <div class="point_wrap point1">
                            <button type="button" class="btn point point_myplc">
                                <span class="point_inner">
                                    <img src="./img/loc_alarm.png" alt="Desired Image" class="btn point point_ing" style="width: 24px; height: 24px;"/>
                                </span>
                            </button>
                            <div class="infobox3 rounded_04 px_08 py_03 on">
                                <span class="fs_12 fw_800 text_dynamic line_h1_2 mt-2">' . $row_slt['slt_title'] . '</span>
                            </div>
                        </div>
                    ';

            $result_data['markerLat_' . $count] = $row_slt['slt_lat'];
            $result_data['markerLong_' . $count] = $row_slt['slt_long'];
            $result_data['markerContent_' . $count] = $content;
            $count++;
        }
        // JSON으로 변환하여 출력
        $result_data['schedule_chk'] = 'Y';
        $result_data['count'] = $count - 1;
    }

    //오너인 그룹수
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgt_show', 'Y');
    $row = $DB->getone('smap_group_t', 'count(*) as cnt');
    $sgt_cnt = $row['cnt'];

    //리더인 그룹수
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_leader_chk', 'Y');
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
    $sgdt_leader_cnt = $row['cnt'];
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        // 추천장소 추가
        $DB->where('rlt_show', 'Y');
        $rlt_list = $DB->get('recomand_location_t');
        $rlt_cnt = count($rlt_list);
        if ($rlt_cnt > 0) {
            foreach ($rlt_list as $rlt_row) {
                $content = '<div class="point_wrap point2">
                                <button type="button" class="btn point point_recommend">
                                    <span class="point_inner">
                                        <span class="point_txt">
                                        </span>
                                    </span>
                                </button>
                                <div class="infobox rounded-sm bg-white px_08 py_08 on">
                                    <p class="fs_12 fw_500 text_dynamic line_h1_2 mt-2">' . $rlt_row['rlt_title'] . '
                                    </p>
                                </div>
                            </div>';

                $result_data['markerLat_' . $count] = $rlt_row['rlt_lat'];
                $result_data['markerLong_' . $count] = $rlt_row['rlt_long'];
                $result_data['markerContent_' . $count] = $content;
                $count++;
            }
            // JSON으로 변환하여 출력
            $result_data['schedule_chk'] = 'Y';
            $result_data['count'] = $count - 1;
        }
    }

    echo json_encode($result_data);
    exit;
} elseif ($_POST['act'] == "location_map_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if($mem_row['mt_level'] == '2'){
        $limit = 4;
    }else{
        $limit = 10;
    }
    $DB->where('(sgdt_idx = "' . $_POST['sgdt_idx'] . '" or mt_idx="' . $_POST . ['mt_idx'] . '")');
    $DB->where('slt_show', 'Y');
    $DB->orderby('slt_wdate','asc');
    $slt_list = $DB->get('smap_location_t', $limit);
    $cnt = count($slt_list);
    ?>
    <div class="px_16" style="margin-bottom: 12px;">
        <div class="border bg-white rounded-lg px_16 py_16">
            <p class="fs_16 fw_600 mb-3">리스트</p>
            <div class="swiper locSwiper location_point_list_wrap pb-0">
                <div class="swiper-wrapper lo_grid_wrap">

                    <!--장소 추가-->
                    <div class="trace_box trace_add_place swiper-slide" onclick="map_info_box_show()" style="height: 135px;">
                        <div class="trace_box_txt_box text-center" style="height: 91.5px;">
                            <p class="fs_13 fw_400 text_dynamic line_h1_4 text-center">내장소를
                                추가해보세요!
                            </p>
                            <button type="button" class="btn trace_addbtn"></button>
                        </div>
                    </div>
                    <?
                    if ($cnt < 1) {
                        // 아무 것도 하지 않음
                    } else {
                        $count = 1;
                        unset($result_data);
                        foreach ($slt_list as $slt_row) {
                            // $slt_row['slt_add']에서 첫 번째 단어를 제거
                            $parts = explode(' ', $slt_row['slt_add']);
                            array_shift($parts);
                            $slt_row['slt_add'] = implode(' ', $parts);
                            ?>
                            <div class="trace_box swiper-slide" style="height: 135px;">
                                <div class="trace_box_txt_box" onclick="map_panto('<?= $slt_row['slt_lat'] ?>','<?= $slt_row['slt_long'] ?>')" style="height: 63.5px;">
                                    <p class="mr-2">
                                        <span class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 mb-2" style="display: inline-block; max-width: 6em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $slt_row['slt_title'] ?></span>
                                    </p>
                                    <p class="line2_text fs_13 fw_400 text_dynamic line_h1_4"><?= $slt_row['slt_add'] ?></p>
                                </div>
                                <div class="trace_box_btn_box" style="height: 28px;">
                                    <!-- .trace_armbtn에 .on을 추가하면 활성화 상태입니다. -->
                                    <button type="button" class="btn trace_armbtn <?php if ($slt_row['slt_enter_alarm'] == 'Y') echo 'on'; ?>" onclick="f_location_alarm_modal('<?= $slt_row['slt_idx'] ?>','<?= $slt_row['slt_enter_alarm'] ?>')"></button>
                                    <!-- <button type="button" class="btn trace_binbtn" data-toggle="modal" data-target="#location_delete_modal"></button> -->
                                    <button type="button" class="btn trace_binbtn" onclick="f_del_location_modal('<?= $slt_row['slt_idx'] ?>')" style="margin-left: 3px;"></button>
                                </div>
                            </div>
                            <?php
                        }
                    }

                    //오너인 그룹수
                    $DB->where('mt_idx', $_SESSION['_mt_idx']);
                    $DB->where('sgt_show', 'Y');
                    $row = $DB->getone('smap_group_t', 'count(*) as cnt');
                    $sgt_cnt = $row['cnt'];

                    //리더인 그룹수
                    $DB->where('mt_idx', $_SESSION['_mt_idx']);
                    $DB->where('sgdt_leader_chk', 'Y');
                    $DB->where('sgdt_show', 'Y');
                    $DB->where('sgdt_discharge', 'N');
                    $DB->where('sgdt_exit', 'N');
                    $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
                    $sgdt_leader_cnt = $row['cnt'];
                    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
                        // 추천장소 추가
                        $DB->where('rlt_show', 'Y');
                        $rlt_list = $DB->get('recomand_location_t');
                        $rlt_cnt = count($rlt_list);
                        if ($rlt_cnt > 0) {
                            foreach ($rlt_list as $rlt_row) { ?>
                                <div class="trace_box trace_frt_place swiper-slide">
                                    <div class="trace_box_txt_box" onclick="map_panto_recomand('<?= $rlt_row['rlt_lat'] ?>','<?= $rlt_row['rlt_long'] ?>','<?= $rlt_row['rlt_add1'] . $rlt_row['rlt_add2'] ?>','<?= $rlt_row['rlt_title'] ?>')">
                                        <p class="mr-2">
                                            <span class="fs_13 fc_d58c19 rounded_04 bg_fbf3e8 px_06 py_02 line1_text line_h1_4 mb-2">추천장소</span>
                                        </p>
                                        <p class="line2_text fs_13 fw_400 text_dynamic line_h1_4"><?= $rlt_row['rlt_add1'] . $rlt_row['rlt_add2'] ?></p>
                                    </div>
                                    <div class="d-flex align-items-center trace_box_btn_box">
                                        <button type="button" class="btn trace_frtplace_btn" onclick="map_panto_recomand('<?= $rlt_row['rlt_lat'] ?>','<?= $rlt_row['rlt_long'] ?>','<?= $rlt_row['rlt_add1'] . $rlt_row['rlt_add2'] ?>','<?= $rlt_row['rlt_title'] ?>')"></button>
                                    </div>
                                </div>
                    <?
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        // 내장소 슬라이드
        var loc_swiper = new Swiper(".locSwiper", {
            slidesPerView: 'auto',
            spaceBetween: 10,
        });
    </script>
<? } elseif ($_POST['act'] == "location_delete") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['slt_idx'] == '') {
        p_alert('잘못된 접근입니다. slt_idx');
    }

    unset($arr_query);
    $arr_query = array(
        "slt_show" => 'N',
        "slt_ddate" => $DB->now(),
    );

    // $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('slt_idx', $_POST['slt_idx']);

    $DB->update('smap_location_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "location_alarm_chk") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['slt_idx'] == '') {
        p_alert('잘못된 접근입니다. slt_idx');
    }
    $DB->where('slt_idx', $_POST['slt_idx']);
    $DB->where('slt_show', 'Y');
    $slt_row = $DB->getone('smap_location_t');

    unset($arr_query);
    if ($slt_row['slt_enter_alarm'] == 'N') {
        $arr_query = array(
            "slt_enter_alarm" => 'Y',
            "slt_udate" => $DB->now(),
        );
    } else {
        $arr_query = array(
            "slt_enter_alarm" => 'N',
            "slt_udate" => $DB->now(),
        );
    }
    // $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('slt_idx', $_POST['slt_idx']);
    $DB->update('smap_location_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "location_add") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. sgdt_idx');
    }

    // 회원구분
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($mem_row) {
        $DB->where('sgdt_idx', $_POST['sgdt_idx']);
        $sgdt_row = $DB->getone('smap_group_detail_t');
        if ($sgdt_row['sgdt_idx']) {
            $DB->where('mt_idx', $sgdt_row['mt_idx']);
            $DB->where('sgdt_idx', $sgdt_row['sgdt_idx']);
            $DB->where('slt_show', 'Y');
            $slt_list = $DB->get('smap_location_t');
            $slt_count = count($slt_list);

            if (($slt_count < 4 && $mem_row['mt_level'] == 2) || $mem_row['mt_level'] == 5) {
                unset($arr_query);
                $arr_query = array(
                    "insert_mt_idx" => $_SESSION['_mt_idx'],
                    "mt_idx" => $sgdt_row['mt_idx'],
                    "sgt_idx" => $sgdt_row['sgt_idx'],
                    "sgdt_idx" => $sgdt_row['sgdt_idx'],
                    "slt_title" => $_POST['slt_title'],
                    "slt_add" => $_POST['slt_add'],
                    "slt_lat" => $_POST['slt_lat'],
                    "slt_long" => $_POST['slt_long'],
                    "slt_show" => 'Y',
                    "slt_enter_alarm" => 'Y',
                    "slt_wdate" => $DB->now(),
                );

                $DB->insert('smap_location_t', $arr_query);

                echo "Y";
            } else {
                echo "E";
            }
        } else {
            echo "N";
        }
    } else {
        echo "N";
    }
} elseif ($_POST['act'] == "group_member_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['group_sgdt_idx'] == '') {
        p_alert('잘못된 접근입니다. group_sgdt_idx');
    }

    $sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
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
?>
    <div class="mem_wrap swiper mem_swiper">
        <div class="swiper-wrapper d-flex ">
            <div class="swiper-slide checks mem_box">
                <label>
                    <input type="radio" name="rd2" checked onclick="mem_schedule(<?= $sgdt_row['sgt_idx'] ?>,<?= $sgdt_row['sgdt_idx'] ?>);">
                    <div class="prd_img mx-auto">
                        <div class="rect_square rounded_14">
                            <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                        </div>
                    </div>
                    <!-- 처음은 사용자 본인이 나옵니다. -->
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                </label>
            </div>
            <?php
            if ($list_sgt) {
                foreach ($list_sgt as $row_sgt) {
                    $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
                    unset($list_sgdt);
                    $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
                    $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
                    if ($invite_cnt || $list_sgdt['data']) {
                        if ($list_sgdt['data']) {
                            foreach ($list_sgdt['data'] as $key => $val) {
            ?>
                                <div class="swiper-slide checks mem_box">
                                    <label>
                                        <input type="radio" name="rd2" onclick="mem_schedule(<?= $row_sgt['sgt_idx'] ?>,<?= $val['sgdt_idx'] ?>);">
                                        <!-- <div class="prd_img mx-auto on_arm"> -->
                                        <div class="prd_img mx-auto "> <!-- 알림왔을 때 on_arm 추가 -->
                                            <div class="rect_square rounded_14">
                                                <img src="<?= $val['mt_file1_url'] ?>" alt="프로필이미지" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                                            </div>
                                        </div>
                                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                                    </label>
                                </div>
            <?
                            }
                        }
                    }
                }
            }
            ?>
            <!-- 그룹원 추가 -->
            <? if ($sgt_cnt > 0) { ?>
                <div class="swiper-slide mem_box add_mem_box" onclick="location.href='./group'">
                    <button class="btn mem_add ">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">그룹원 추가</p>
                </div>
            <? } ?>
        </div>
    </div>
    <script>
        //프로필 슬라이더
        var mem_swiper = new Swiper(".mem_swiper", {
            slidesPerView: 'auto',
            spaceBetween: 12,
        });
    </script>
<? } elseif ($_POST['act'] == "marker_reload") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    unset($arr_data);
    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_show', 'Y');
    $sgdt_row = $DB->getone('smap_group_detail_t');

    // 기본 지도 위치 지정
    if ($sgdt_row) {
        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $mem_row = $DB->getone('member_t');

        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $DB->orderby('mlt_gps_time', 'desc');
        $mt_location_info = $DB->getone('member_location_log_t');

        $arr_data = array(
            "my_lat" => $mt_location_info['mlt_lat'] == "" ? $mem_row['mt_lat'] : $mt_location_info['mlt_lat'],
            "mt_long" => $mt_location_info['mlt_long'] == "" ? $mem_row['mt_long'] :  $mt_location_info['mlt_long'],
            "my_profile" => $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']),
        );
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $mem_row = $DB->getone('member_t');
        $arr_data = array(
            "my_lat" => $_SESSION['_mt_lat'] == "" ? $mem_row['mt_lat'] : $_SESSION['_mt_lat'],
            "mt_long" => $_SESSION['_mt_long'] == "" ? $mem_row['mt_long'] : $_SESSION['_mt_long'],
            "my_profile" => $_SESSION['_mt_file1'] == "" ? $ct_no_img_url : $_SESSION['_mt_file1'],
        );
    }
    $arr_data['marker_reload'] = 'Y';
    echo json_encode($arr_data);
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
