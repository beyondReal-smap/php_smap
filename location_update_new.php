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
                                    locale: '<?= $userLang ?>',
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
    if (empty($_SESSION['_mt_idx'])) {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    
    $mt_idx_t = $_POST['sgdt_mt_idx'] ?? $_SESSION['_mt_idx'];
    $sdate = $_POST['sdate'] ?? date('Y-m-d');
    $lsdate = $_POST['lsdate'] ?? '';
    $ledate = $_POST['ledate'] ?? '';
    
    // Get member information
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    
    // Calculate the start date based on member level
    $days_back = ($mem_row['mt_level'] == 5 || $mem_row['mt_level'] == 9) ? 14 : 2;
    $start_date = date('Y-m-d', strtotime("$sdate -$days_back days"));
    
    $get_first_date_week = date('w', strtotime($start_date));
    $get_end_date_week = date('w', strtotime($sdate));
    $get_first_date = date('Y-m-d', strtotime("$start_date - $get_first_date_week days"));
    $get_end_date = date('Y-m-d', strtotime("$sdate + " . (6 - $get_end_date_week) . " days"));
    
    $diff_days = (strtotime($get_end_date) - strtotime($get_first_date)) / (60 * 60 * 24); // 초 단위를 일 단위로 변환
    
    $_POST['start'] = $get_first_date;
    $_POST['end'] = $get_end_date;
    
    $arr_data = [];
    
    // Retrieve summarized location logs
    $sql = "
        SELECT 
            mlt_idx,
            DATE(mlt_gps_time) AS log_date,
            MIN(mlt_gps_time) AS start_time,
            MAX(mlt_gps_time) AS end_time
        FROM 
            member_location_log_t
        WHERE 
            mt_idx = '$mt_idx_t'
            AND mlt_accuacy < $slt_mlt_accuacy
            AND mlt_speed >= $slt_mlt_speed
            AND mlt_lat > 0 AND mlt_long > 0
            AND mlt_gps_time BETWEEN '" . $_POST['start'] . " 00:00:00' AND '" . $_POST['end'] . " 23:59:59'
        GROUP BY 
            DATE(mlt_gps_time)
        ORDER BY 
            log_date ASC
    ";
    
    $list = $DB->rawQuery($sql);
    
    foreach ($list as $row) {
        $arr_data[$row['log_date']] = [
            'id' => $row['mlt_idx'],
            'start' => $row['start_time'],
            'end' => $row['end_time'],
        ];
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
            // centeredSlides: true,
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

    function getBasicMapInfo($DB, $sgdt_idx, $session_data) {
        // 기본 위치 설정
        $default_lat = 37.5665;
        $default_long = 126.9780;
    
        // 세션 데이터에서 위치 정보 가져오기
        $session_lat = $session_data['_mt_lat'] ?? $default_lat;
        $session_long = $session_data['_mt_long'] ?? $default_long;
    
        $arr_data = [
            'my_lat' => $session_lat,
            'mt_long' => $session_long,
            'my_profile' => $session_data['_mt_file1'] ?? $GLOBALS['ct_no_img_url']
        ];
    
        $DB->where('sgdt_idx', $sgdt_idx);
        $sgdt_row = $DB->getOne('smap_group_detail_t');
    
        if ($sgdt_row) {
            $mt_idx = $sgdt_row['mt_idx'];
    
            // 멤버 정보와 위치 정보를 한 번의 쿼리로 가져오기
            $DB->join('member_location_log_t mll', 'm.mt_idx = mll.mt_idx', 'LEFT');
            $DB->where('m.mt_idx', $mt_idx);
            $DB->orderBy('mll.mlt_gps_time', 'DESC');
            $result = $DB->getOne('member_t m', 'm.mt_file1, mll.mlt_lat, mll.mlt_long');
    
            if ($result) {
                $arr_data['my_lat'] = $result['mlt_lat'] ?: $session_lat;
                $arr_data['mt_long'] = $result['mlt_long'] ?: $session_long;
                $arr_data['my_profile'] = $result['mt_file1'] ? get_image_url($result['mt_file1']) : $GLOBALS['ct_no_img_url'];
            }
        }
    
        return $arr_data;
    }

    function getScheduleMarkers($DB, $sgdt_idx, $event_start_date, $mt_idx) {
        $arr_data = [];
        $arr_sst_idx = get_schedule_main($sgdt_idx, $event_start_date, $mt_idx);
        $cnt = count($arr_sst_idx);
    
        if ($cnt < 1) {
            $arr_data['schedule_chk'] = 'N';
        } else {
            $arr_sst_idx_im = implode(',', $arr_sst_idx);
            $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
            $DB->where('sst_show', 'Y');
            $DB->orderBy("sst_all_day", "asc");
            $DB->orderBy("sst_sdate", "asc");
            $list_sst = $DB->get('smap_schedule_t');
    
            if ($list_sst) {
                $count = 1;
                foreach ($list_sst as $row_sst_a) {
                    $sst_all_day_t = getSstAllDayText($row_sst_a);
                    $content = getMarkerContent($row_sst_a['sst_title']);
    
                    $arr_data['markerLat_' . $count] = $row_sst_a['sst_location_lat'];
                    $arr_data['markerLong_' . $count] = $row_sst_a['sst_location_long'];
                    $arr_data['markerContent_' . $count] = $content;
                    $count++;
                }
            }
            $arr_data['schedule_chk'] = 'Y';
            $arr_data['count'] = $count - 1;
        }
    
        return $arr_data;
    }
    
    function getSstAllDayText($row_sst_a) {
        if ($row_sst_a['sst_all_day'] == 'Y') {
            return '하루종일';
        } else {
            $repeat_array = json_decode($row_sst_a['sst_repeat_json'], true);
            if ($repeat_array['r1'] == 1 || empty($repeat_array['r1'])) {
                $sst_sdate_e1 = get_date_ttime($row_sst_a['sst_sdate']);
                $sst_sdate_e2 = get_date_ttime($row_sst_a['sst_edate']);
                return $sst_sdate_e1 . ' ~ ' . $sst_sdate_e2;
            } else {
                $sst_sdate_e1 = get_date_ttime($row_sst_a['sst_sdate']);
                $sst_sdate_e2 = get_date_ttime($row_sst_a['sst_edate']);
                return $sst_sdate_e1 . ' ~ ' . $sst_sdate_e2;
            }
        }
    }
    
    function getMarkerContent($title) {
        $random_color = generateRandomColor();
        return '
            <style>
            .infobox1 {
                position: absolute !important;
                left: 50%;
                top: 100%;
                transform: translate(-50%, -80%);
                background-color: #413F4A;
                padding: 0.3rem 0.8rem;
                border-radius: 0.4rem;
                z-index: 1;
                white-space: nowrap;
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
                    <span class="fs_12 fw_800 text_dynamic line_h1_2 mt-2">' . $title . '</span>
                </div>
            </div>
        ';
    }

    function generateRandomColor() {
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
    
        return $color_sets[array_rand($color_sets)];
    }

    function getLocationMarkers($DB, $session_mt_idx, $sgdt_mt_idx, $sgdt_idx) {
        $arr_data = [];
    
        // 사용자 레벨에 따른 제한 설정
        $mem_row = $DB->getOne('member_t', 'mt_level', ['mt_idx' => $session_mt_idx]);
        $limit = ($mem_row['mt_level'] == '2') ? 4 : 10;
    
        // 내장소 마커 구하기
        $DB->where("(mt_idx = ? OR sgdt_idx = ?)", [$sgdt_mt_idx, $sgdt_idx]);
        $DB->where('slt_show', 'Y');
        $DB->orderBy('slt_wdate', 'ASC');
        $list_slt = $DB->get('smap_location_t', $limit);
    
        if ($list_slt) {
            $mycount = 1;
            foreach ($list_slt as $row_slt) {
                $content = getLocationMarkerContent($row_slt['slt_title']);
    
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
    
        return $arr_data;
    }
    
    function getLocationMarkerContent($title) {
        $random_color = generateRandomColor();
        return '
            <style>
            .infobox2 {
                position: absolute;
                left: 50%;
                top: 100%;
                transform: translate(-50%, 40%);
                background-color: #413F4A;
                padding: 0.3rem 0.8rem;
                border-radius: 0.4rem;
                z-index: 1;
                white-space: nowrap;
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
                    <span class="fs_12 fw_800 text_dynamic line_h1_2 mt-2">' . $title . '</span>
                </div>
            </div>
        ';
    }

    function getDailyMovementLogs($sgdt_mt_idx, $slt_mlt_accuacy, $event_start_date, $slt_mlt_speed, $DB) {
        global $logger;
        $logger->write("sgdt_mt_idx: {$sgdt_mt_idx} / slt_mlt_accuacy: {$slt_mlt_accuacy} / event_start_date: {$event_start_date} / slt_mlt_speed: {$slt_mlt_speed}");
        $current_date = date('Y-m-d H:i:s');
        $total_log_count = 1;
        $stay_count = 1;
        $location_data = array();
    
        // 전체 이동로그 구하기
        $move_query = get_move_query($sgdt_mt_idx, $slt_mlt_accuacy, $event_start_date);  
        // $logger->write("move_query: {$move_query}");
        $list_move = $DB->Query($move_query);
    
        if ($list_move) {
            $location_data = processMoveLogs($list_move, $sgdt_mt_idx, $slt_mlt_accuacy, $slt_mlt_speed, $event_start_date, $DB, $total_log_count, $stay_count);
            $arr_data = [
                'log_chk' => 'Y',
                'log_count' => count($location_data) / 5,  // 5는 각 로그 항목의 필드 수
                'location_data' => $location_data
            ];
        } else {
            $arr_data = [
                'log_chk' => 'N',
                'log_count' => 0
            ];
        }
    
        return $arr_data;
    }
    
    function processMoveLogs($list_move, $sgdt_mt_idx, $slt_mlt_accuacy, $slt_mlt_speed, $event_start_date, $DB, &$total_log_count, &$stay_count) {
        $location_data = array();
        $move_log_count = 1;
    
        foreach ($list_move as $row_mlt) {
            if ($move_log_count == 1) {
                $first_location = getFirstLocationLog($sgdt_mt_idx, $slt_mlt_accuacy, $slt_mlt_speed, $event_start_date, $DB);
                if ($first_location) {
                    $location_data = processFirstLocationLog($first_location, $event_start_date, $total_log_count, $stay_count, $location_data);
                }
            }
            
            $location_data = processMoveLog($row_mlt, $total_log_count, $location_data);
            $move_log_count++;
            $total_log_count++;
        }
    
        return $location_data;
    }
    
    function getFirstLocationLog($sgdt_mt_idx, $slt_mlt_accuacy, $slt_mlt_speed, $event_start_date, $DB) {
        $DB->where('mt_idx', $sgdt_mt_idx);
        $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
        $DB->where("mlt_speed < " . $slt_mlt_speed);
        $DB->where("(mlt_lat > 0 and mlt_long > 0)");
        $DB->where("mlt_gps_time BETWEEN '" . $event_start_date . " 00:00:00' AND '" . $event_start_date . " 23:59:59'");
        $DB->orderBy("mlt_gps_time", "asc");
        return $DB->getone('member_location_log_t');
    }
    
    function processFirstLocationLog($first_location, $event_start_date, &$total_log_count, &$stay_count, $location_data) {
        if ($first_location['rownum'] == 1) {
            $first_log_date = strtotime($event_start_date . " 00:00:00");
            $last_log_date = strtotime($first_location['mlt_gps_time']);
            $stay_time_seconds = $last_log_date - $first_log_date;
        
            if ($stay_time_seconds >= 300) {
                $stay_time_formatted = formatStayTime($stay_time_seconds);
                $addr = get_search_coordinate2address($first_location['mlt_lat'], $first_location['mlt_long']);
                $address = $addr['area2'] . ' ' . $addr['area3'];
                $content = generateStayContent($stay_count, $first_location['mlt_gps_time'], $stay_time_formatted, $address, $total_log_count);
    
                $location_data = addLocationData($location_data, $total_log_count, $first_location['mlt_gps_time'], $first_location['mlt_lat'], $first_location['mlt_long'], $content, 'stay');
                $stay_count++;
                $total_log_count++;
            }
        }
        return $location_data;
    }
    
    function processMoveLog($row_mlt, $total_log_count, $location_data) {
        $content = generateMoveContent($row_mlt['mlt_gps_time'], $total_log_count);
        return addLocationData($location_data, $total_log_count, $row_mlt['mlt_gps_time'], $row_mlt['mlt_lat'], $row_mlt['mlt_long'], $content, 'move');
    }
    
    function formatStayTime($stay_time_seconds) {
        $hours = floor($stay_time_seconds / 3600);
        $minutes = floor(($stay_time_seconds % 3600) / 60);
        $stay_time_formatted = "";
        if ($hours > 0) {
            $stay_time_formatted .= $hours . "시간 ";
        }
        $stay_time_formatted .= $minutes . "분 체류";
        return $stay_time_formatted;
    }
    
    function generateStayContent($stay_count, $gps_time, $stay_time_formatted, $address, $total_log_count) {
        return '<div class="point_wrap point2" data-rangeindex="' . $total_log_count . '">
                    <button type="button" class="btn log_point point_stay">
                        <span class="point_inner">
                            <span class="point_txt">' . $stay_count . '</span>
                        </span>
                    </button>
                    <div class="infobox rounded-sm bg-white px_08 py_08">
                        <p class="fs_12 fw_800 text_dynamic"> 00:00 ~ ' . datetype($gps_time, 7) . '</p>
                        <p class="fs_10 fw_600 text_dynamic text-primary line_h1_2 mt-2">' . $stay_time_formatted . '</p>
                        <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">' . $address . '</p>
                    </div>
                </div>';
    }
    
    function generateMoveContent($gps_time, $total_log_count) {
        return '<div class="point_wrap point2 d-none log_marker" data-rangeindex="' . $total_log_count . '">
                    <div class="infobox infobox_2 rounded-sm px_08 py_08" style="background-color: #413F4A; color: #E6F3FF;">
                        <p class="fs_12 fw_800 text_dynamic">' . datetype($gps_time, 7) . '</p>
                    </div>
                </div>';
    }
    
    function addLocationData($location_data, $total_log_count, $gps_time, $lat, $long, $content, $status) {
        $location_data['startTime_' . $total_log_count] = $gps_time;
        $location_data['endTime_' . $total_log_count] = $gps_time;
        $location_data['logmarkerLat_' . $total_log_count] = $lat;
        $location_data['logmarkerLong_' . $total_log_count] = $long;
        $location_data['logmarkerContent_' . $total_log_count] = $content;
        $location_data['logStatus_' . $total_log_count] = $status;
        return $location_data;
    }

    function processStayLogs($sgdt_mt_idx, $slt_mlt_accuacy, $event_start_date, $DB) {
        $stay_query = get_stay_query($sgdt_mt_idx, $slt_mlt_accuacy, $event_start_date);
        $list_stay = $DB->Query($stay_query);
    
        if ($list_stay) {
            $filtered_stays = filterStays($list_stay);
            $location_data = processFilteredStays($filtered_stays);
            
            $arr_data = [
                'log_chk' => 'Y',
                'log_count' => count($location_data) / 6,  // 6은 각 로그 항목의 필드 수
                'location_data' => $location_data
            ];
        } else {
            $arr_data = [
                'log_chk' => 'N',
                'log_count' => 0
            ];
        }
    
        return $arr_data;
    }
    
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
    
    function filterStays($list_stay) {
        $filtered_stays = [$list_stay[0]];
    
        for ($i = 1; $i < count($list_stay); $i++) {
            $prev_stay = &$filtered_stays[count($filtered_stays) - 1];
            $current_stay = $list_stay[$i];
            
            $distance = haversineDistance($prev_stay['start_lat'], $prev_stay['start_long'], $current_stay['start_lat'], $current_stay['start_long']);
            
            if ($prev_stay['label'] === $current_stay['label']) {
                if ($distance <= 100) {
                    $prev_stay['duration'] += $current_stay['duration'];
                    $prev_stay['end_time'] = $current_stay['end_time'];
                } else {
                    $filtered_stays[] = $current_stay;
                }
            } else {
                $filtered_stays[] = $current_stay;
            }
        }
    
        return $filtered_stays;
    }
    
    function processFilteredStays($filtered_stays) {
        $location_data = [];
        $total_log_count = 1;
        $stay_count = 1;
    
        foreach ($filtered_stays as $row_mlt) {
            if ($row_mlt['label'] === 'stay') {
                $start_time = new DateTime($row_mlt['start_time']);
                $end_time = new DateTime($row_mlt['end_time']);
                
                $duration = $end_time->getTimestamp() - $start_time->getTimestamp();
                $stay_time_formatted = formatStayTimeFiltered($duration);
    
                $addr = get_search_coordinate2address($row_mlt['start_lat'], $row_mlt['start_long']);
                $address = $addr['area2'] . ' ' . $addr['area3'];
    
                $content = generateStayContentFiltered($stay_count, $start_time, $end_time, $stay_time_formatted, $address, $total_log_count);
    
                $location_data = addLocationDataFiltered($location_data, $total_log_count, $row_mlt['start_time'], $row_mlt['end_time'], $row_mlt['start_lat'], $row_mlt['start_long'], $content, 'stay');
    
                $stay_count++;
                $total_log_count++;
            }
        }
    
        return $location_data;
    }
    
    function formatStayTimeFiltered($duration) {
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
    
        $stay_time_formatted = "";
        if ($hours > 0) {
            $stay_time_formatted .= $hours . "시간 ";
        }
        $stay_time_formatted .= $minutes . "분 체류";
    
        return $stay_time_formatted;
    }
    
    function generateStayContentFiltered($stay_count, $start_time, $end_time, $stay_time_formatted, $address, $total_log_count) {
        return '<div class="point_wrap point2" data-rangeindex="' . $total_log_count . '">
                    <button type="button" class="btn log_point point_stay">
                        <span class="point_inner">
                            <span class="point_txt">' . $stay_count . '</span>
                        </span>
                    </button>
                    <div class="infobox rounded-sm bg-white px_08 py_08">
                        <p class="fs_12 fw_800 text_dynamic">' . $start_time->format('H:i') . ' ~ ' . $end_time->format('H:i') . '</p>
                        <p class="fs_10 fw_600 text_dynamic text-primary line_h1_2 mt-2">' . $stay_time_formatted . '</p>
                        <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">' . $address . '</p>
                    </div>
                </div>';
    }
    
    function addLocationDataFiltered($location_data, $total_log_count, $start_time, $end_time, $lat, $long, $content, $status) {
        $location_data['startTime_' . $total_log_count] = $start_time;
        $location_data['endTime_' . $total_log_count] = $end_time;
        $location_data['logmarkerLat_' . $total_log_count] = $lat;
        $location_data['logmarkerLong_' . $total_log_count] = $long;
        $location_data['logmarkerContent_' . $total_log_count] = $content;
        $location_data['logStatus_' . $total_log_count] = $status;
        return $location_data;
    }

    function processAndSortLocationData($arr_data, $location_data) {
        if ($arr_data['log_count'] > 0) {
            $location_data = array_values($location_data);
            $event_count = count($location_data) / 6;
            $events = createEvents($location_data, $event_count);
            $events = sortEvents($events);
            $arr_data = updateArrData($arr_data, $events);
            $json_data = json_encode($events, JSON_PRETTY_PRINT);
            return [$arr_data, $json_data];
        }
        return [$arr_data, null];
    }
    
    function createEvents($location_data, $event_count) {
        $events = [];
        for ($i = 0; $i < $event_count; $i++) {
            $start_index = $i * 6;
            $event = createEvent($location_data, $start_index);
            if ($event['logStatus'] === 'stay') {
                array_unshift($events, $event);
            } else {
                $events[] = $event;
            }
        }
        return $events;
    }
    
    function createEvent($location_data, $start_index) {
        $content = $location_data[$start_index + 4];
        $log_status = $location_data[$start_index + 5];
        preg_match('/data-rangeindex="(\d+)"/', $content, $matches);
        $range_index = $matches[1];
        
        $start_time = strtotime($location_data[$start_index]);
        $end_time = strtotime($location_data[$start_index + 1]);
        $time_key = 'startTime';
        $time_value = ($start_time <= $end_time) ? $location_data[$start_index] : $location_data[$start_index + 1];
    
        return [
            $time_key => $time_value,
            'latitude' => $location_data[$start_index + 2],
            'longitude' => $location_data[$start_index + 3],
            'content' => $content,
            'rangeIndex' => $range_index,
            'logStatus' => $log_status
        ];
    }
    
    function sortEvents($events) {
        usort($events, function ($a, $b) {
            return strtotime($a['startTime']) - strtotime($b['startTime']);
        });
        return $events;
    }
    
    function updateArrData($arr_data, $events) {
        foreach ($events as $index => $event) {
            $arr_data['logmarkerLat_' . ($index + 1)] = $event['latitude'];
            $arr_data['logmarkerLong_' . ($index + 1)] = $event['longitude'];
            $arr_data['logmarkerContent_' . ($index + 1)] = str_replace(
                'data-rangeindex="' . $event['rangeIndex'] . '"', 
                'data-rangeindex="' . ($index + 1) . '"', 
                $event['content']
            );
        }
        return $arr_data;
    }
    
    function cacheResult($cache_key, $arr_data) {
        CacheUtil::set($cache_key, $arr_data, 1800);
    }
    
    // 메인 실행 함수
    function processLocationDataAndCache($arr_data, $location_data, $cache_key) {
        list($arr_data, $json_data) = processAndSortLocationData($arr_data, $location_data);
        cacheResult($cache_key, $arr_data);
        return [$arr_data, $json_data];
    }
    
    function cacheMemberLogs($DB, $params) {
        global $logger;
        $logger->write("Starting cacheMemberLogs for sgdt_idx: {$params['sgdt_idx']}");
        // 필ㅛ한 파라미터 추출
        $sgdt_idx = $params['sgdt_idx'];
        $event_start_date = $params['event_start_date'];

        // Step 1: Retrieve group members based on sgdt_idx
        $DB->where('sgdt_idx', $sgdt_idx);
        $initialGroupMembers = $DB->get('smap_group_detail_t');
    
        // Step 2: Extract sgt_idx from the initial group members
        $sgt_ids = array_column($initialGroupMembers, 'sgt_idx');
    
        // Step 3: Retrieve all group members based on the sgt_idx
        $groupMembers = [];
        if (!empty($sgt_ids)) {
            $DB->where('sgt_idx', $sgt_ids, 'IN');
            $groupMembers = $DB->get('smap_group_detail_t');
        }
    
        // Step 4: Initialize groupLogs array
        $groupLogs = [];
        $now = new DateTime();

        // Step 5: Loop through group members
        foreach ($groupMembers as $member) {
            $logger->write("Processing member: {$member['mt_idx']}");
            // Get mt_level from member_t
            $DB->where('mt_idx', $member['mt_idx']);
            $memberInfo = $DB->getOne('member_t', 'mt_level');

            // Determine the time range based on mt_level
            if ($memberInfo['mt_level'] == 5) {
                $startDate = (new DateTime())->modify('-14 days');
                $days = 14;
            } else {
                $startDate = (new DateTime())->modify('-2 days');
                $days = 2;
            }

            // Loop through each day
            for ($i = 0; $i < $days; $i++) {
                $currentDate = clone $startDate;
                $currentDate->modify("+{$i} days");
            
                // Update params for this member and this day
                $memberParams = $params;
                $memberParams['sgdt_mt_idx'] = $member['mt_idx'];
                $memberParams['event_start_date'] = $currentDate->format('Y-m-d');
                $memberParams['start_date'] = $currentDate->format('Y-m-d 00:00:00');
                $memberParams['end_date'] = $currentDate->format('Y-m-d 23:59:59');
            
                // Process map data for this member and this day
                list($arr_data, $json_data) = processAllMapData($DB, $memberParams);
            
                // Cache the processed data with a unique key
                $cacheKey = "member_logs_{$member['sgdt_idx']}_{$currentDate->format('Y-m-d')}";
                $logger->write("Caching data for member {$member['mt_idx']} on {$currentDate->format('Y-m-d')}");
                
                CacheUtil::set($cacheKey, json_encode($arr_data), 1800); // 30분 동안 캐시
            }
        }
        $logger->write("Finished cacheMemberLogs for sgdt_idx: {$params['sgdt_idx']}");
    }

    function processAllMapData($DB, $params) {
        global $logger;
        $logger->write("Starting processAllMapData for sgdt_idx: {$params['sgdt_idx']}");
        
        $arr_data = array();
    
        // 필요한 파라미터 추출
        $sgdt_idx = $params['sgdt_idx'];
        $event_start_date = $params['event_start_date'];
        $sgdt_mt_idx = $params['sgdt_mt_idx'];
        $session_mt_idx = $params['session_mt_idx'];
        $slt_mlt_accuacy = $params['slt_mlt_accuacy'];
        $slt_mlt_speed = $params['slt_mlt_speed'];
        $cache_key = $params['cache_key'];
    
        // 1. 기본 지도 정보 가져오기
        $basic_info = getBasicMapInfo($DB, $sgdt_idx, $params['session']);
        $arr_data = array_merge($arr_data, $basic_info);
    
        // 2. 일정 마커 가져오기
        $schedule_markers = getScheduleMarkers($DB, $sgdt_idx, $event_start_date, $basic_info['mt_idx']);
        $arr_data = array_merge($arr_data, $schedule_markers);
    
        // 3. 내 장소 마커 가져오기
        $location_markers = getLocationMarkers($DB, $session_mt_idx, $basic_info['mt_idx'], $sgdt_idx);
        $arr_data = array_merge($arr_data, $location_markers);
    
        // 4. 이동 로그 마커 가져오기
        $move_markers = getDailyMovementLogs($sgdt_mt_idx, $slt_mlt_accuacy, $event_start_date, $slt_mlt_speed, $DB);
        $arr_data = array_merge($arr_data, $move_markers);
    
        // 5. 체류 로그 마커 가져오기
        $stay_markers = processStayLogs($sgdt_mt_idx, $slt_mlt_accuacy, $event_start_date, $DB);
        $arr_data = array_merge($arr_data, $stay_markers);
    
        // 6. 마커 처리 및 정렬
        list($arr_data, $json_data) = processLocationDataAndCache($arr_data, $arr_data['location_data'] ?? [], $cache_key);
        
        $logger->write("Finished processAllMapData for sgdt_idx: {$params['sgdt_idx']}");

        return [$arr_data, $json_data];
    }

    //////////////////////////////////////////////////////////////////////
    // 메인 실행 부분 //
    //////////////////////////////////////////////////////////////////////
    $cache_key = "member_logs_{$_POST['sgdt_idx']}_{$_POST['event_start_date']}";
    $cached_data = CacheUtil::get($cache_key);

    if ($cached_data === null) {
        $logger->write("Cache miss for key: {$cache_key}. Processing data.");
        // 캐시에 데이터가 없으면 계산 수행
        $params = [
            'sgdt_idx' => $_POST['sgdt_idx'],
            'event_start_date' => $_POST['event_start_date'],
            'sgdt_mt_idx' => $_POST['sgdt_mt_idx'],
            'session_mt_idx' => $_SESSION['_mt_idx'],
            'slt_mlt_accuacy' => $slt_mlt_accuacy,
            'slt_mlt_speed' => $slt_mlt_speed,
            'cache_key' => $cache_key,
            'session' => $_SESSION
        ];
        
        // cacheMemberLogs($DB, $params);
        
        // processAllMapData 함수를 호출하여 데이터 처리
        list($arr_data, $json_data) = processAllMapData($DB, $params);
        
        // 처리된 데이터를 캐시에 저장
        CacheUtil::set($cache_key, json_encode($arr_data), 60); // 60초 동안 캐시
        
        $cached_data = json_encode($arr_data);
    } else {
        $logger->write("Cache hit for key: {$cache_key}. Using cached data.");
    }

    $logger->write("Returning data: {$cached_data}");

    // 데이터 출력
    echo $cached_data;
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
            <?php if ($sgt_cnt > 0) { ?>
                <div class="swiper-slide mem_box add_mem_box" onclick="location.href='./group'">
                    <button class="btn mem_add">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">그룹원 추가</p>
                </div>
            <?php } else { ?>
                <div class="swiper-slide mem_box add_mem_box" style="visibility: hidden;">
                    <button class="btn mem_add">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">그룹원 추가</p>
                </div>
            <?php } ?>
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
