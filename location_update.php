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

    // 캐시 키 생성
    $cache_key = 'get_line_' . $_POST['sgdt_idx'] . '_' . $_POST['event_start_date'] . '_' . $_POST['sgdt_mt_idx'];

    // 캐시에서 데이터 확인
    $cached_data = CacheUtil::get($cache_key);
    
    if ($cached_data === null) {
        // 캐시에 데이터가 없으면 계산 수행
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
        // 마커2
        $loc_new = [];

        // 전체 이동로그 구하기
        unset($list_move);
        $move_query = get_move_query($mt_idx, $slt_mlt_accuacy, $event_start_date);
                
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

                            $loc_new[] = [
                                'start_time' => $first_location['mlt_gps_time'],
                                'end_time' => $first_location['mlt_gps_time'],
                                'stay_time_formatted' => $stay_time_formatted,
                                'address' => $address,
                                'mlt_lat' => $first_location['mlt_lat'],
                                'mlt_long' => $first_location['mlt_long'],
                                'stay_move_flg' => 'stay'
                            ];
                        }
                    }
                }

                $loc_new[] = [
                    'start_time' => $row_mlt['start_time'],
                    'end_time' => $row_mlt['end_time'],
                    'stay_time_formatted' => $stay_time_formatted,
                    'address' => $address,
                    'mlt_lat' => $row_mlt['start_lat'],
                    'mlt_long' => $row_mlt['start_long'],
                    'stay_move_flg' => 'move'
                ];
            }
        }
        //체류시간구하기
        unset($list_stay);
        $stay_query = get_stay_query($mt_idx, $slt_mlt_accuacy, $event_start_date);
        $list_stay = $DB->Query($stay_query);
        // Function to calculate distance between two coordinates using Haversine formula
        function haversineDistance2($lat1, $lon1, $lat2, $lon2) {
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
                $distance = haversineDistance2($prev_stay['start_lat'], $prev_stay['start_long'], $current_stay['start_lat'], $current_stay['start_long']);
            
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

                    $loc_new[] = [
                        'start_time' => $row_mlt['start_time'],
                        'end_time' => $row_mlt['end_time'],
                        'stay_time_formatted' => $stay_time_formatted,
                        'address' => $address,
                        'mlt_lat' => $row_mlt['start_lat'],
                        'mlt_long' => $row_mlt['start_long'],
                        'stay_move_flg' => 'stay'
                    ];
                }
            }
        }

        // 거리 계산 함수
        function calculateDistance($lat1, $lon1, $lat2, $lon2) {
            // Haversine 공식을 사용하여 두 지점 사이의 거리를 계산하는 함수입니다.
            $earthRadius = 6371; // 지구 반지름 (단위: km)

            $lat1 = deg2rad($lat1);
            $lon1 = deg2rad($lon1);
            $lat2 = deg2rad($lat2);
            $lon2 = deg2rad($lon2);

            $deltaLat = $lat2 - $lat1;
            $deltaLon = $lon2 - $lon1;

            $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                cos($lat1) * cos($lat2) *
                sin($deltaLon / 2) * sin($deltaLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            $distance = $earthRadius * $c;

            return $distance;
        }

        function isOutlier($prev, $current, $next) {
            $distancePrevCurrent = calculateDistance(
                $prev['latitude'], $prev['longitude'],
                $current['latitude'], $current['longitude']
            );
            $distanceCurrentNext = calculateDistance(
                $current['latitude'], $current['longitude'],
                $next['latitude'], $next['longitude']
            );
            $distancePrevNext = calculateDistance(
                $prev['latitude'], $prev['longitude'],
                $next['latitude'], $next['longitude']
            );
        
            // 현재 위치가 이전과 다음 위치 사이에서 너무 벗어나 있는지 확인
            $threshold = 1.8; // 이 값은 상황에 따라 조정 가능
            if ($distancePrevCurrent + $distanceCurrentNext > $distancePrevNext * $threshold) {
                return true;
            }
            return false;
        }

        function formatStayTime($seconds) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return sprintf("%02d:%02d", $hours, $minutes);
        }
        
        if (!empty($loc_new)) {
            $events = [];
            $filteredEvents = [];
            $filteredEventsMove = [];
            $maxDistance = 5; // 최대 허용 거리 (km)
            $maxSpeed = 80; // 최대 허용 속도 (km/h)
            $minSpeed = 0.0; // 최소 허용 속도 (km/h), 정지 상태와 구분하기 위함
            $stayThreshold = 0.1; // stay 상태에서 허용되는 최대 이동 거리 (km)

            // 기존 데이터 배열로부터 이벤트를 생성
            foreach ($loc_new as $log) {
                $events[] = [
                    'startTime' => $log['start_time'],
                    'endTime' => $log['end_time'],
                    'latitude' => $log['mlt_lat'],
                    'longitude' => $log['mlt_long'],
                    'address' => $log['address'],
                    'stayTime' => $log['stay_time_formatted'],
                    'logStatus' => $log['stay_move_flg'],
                    'totalLogCount' => $log['total_log_count']
                ];
            }

            // 시작 시간을 기준으로 정렬
            usort($events, function ($a, $b) {
                return strtotime($a['startTime']) - strtotime($b['startTime']);
            });

            $currentStatus = null;
            $stayStartTime = null;
            $stayEndTime = null;
            $lastValidLocation = null;
            $tempMoveEvents = [];

            for ($i = 0; $i < count($events); $i++) {
                $current = $events[$i];
                
                if ($i == 0) {
                    $filteredEvents[] = $current;
                    $currentStatus = $current['logStatus'];
                    $stayStartTime = $current['startTime'];
                    $stayEndTime = $current['endTime'];
                    $lastValidLocation = $current;
                    continue;
                }

                $distance = calculateDistance(
                    $lastValidLocation['latitude'], $lastValidLocation['longitude'],
                    $current['latitude'], $current['longitude']
                );

                $timeDiff = (strtotime($current['startTime']) - strtotime($lastValidLocation['startTime'])) / 3600; // in hours
                
                if ($timeDiff > 0) {
                    $speed = $distance / $timeDiff;

                    if ($currentStatus == 'stay') {
                        if ($distance > $stayThreshold) {
                            // Move detected
                            $lastValidLocation['endTime'] = $current['startTime'];
                            $lastValidLocation['stayTime'] = formatStayTime(strtotime($lastValidLocation['endTime']) - strtotime($stayStartTime));
                            $filteredEvents[] = $lastValidLocation;

                            $current['logStatus'] = 'move';
                            $currentStatus = 'move';
                            $tempMoveEvents = [$current];
                            $lastValidLocation = $current;
                        } else {
                            // Still in stay status, update end time
                            $lastValidLocation['endTime'] = $current['endTime'];
                        }
                    } else { // move status
                        if ($distance <= $stayThreshold && $speed < $minSpeed) {
                            // Stay detected
                            $current['logStatus'] = 'stay';
                            $currentStatus = 'stay';
                            $stayStartTime = $current['startTime'];
                            $stayEndTime = $current['endTime'];
                            
                            // Clear temporary move events
                            $tempMoveEvents = [];
                            
                            $filteredEvents[] = $current;
                            $lastValidLocation = $current;
                        } else if ($distance <= $maxDistance && $speed <= $maxSpeed) {
                            // Valid move
                            $tempMoveEvents[] = $current;
                            $lastValidLocation = $current;
                        }
                        // If it's an invalid move (too fast or too far), we ignore it
                    }
                } else {
                    // 시간 차이가 0이면 동일한 시간의 데이터로 간주하고 추가
                    if ($currentStatus == 'move') {
                        $tempMoveEvents[] = $current;
                    } else {
                        $filteredEvents[] = $current;
                    }
                }
            }

            // 마지막 이벤트 처리
            if ($currentStatus == 'stay') {
                $lastValidLocation['endTime'] = end($events)['endTime'];
                $lastValidLocation['stayTime'] = formatStayTime(strtotime($lastValidLocation['endTime']) - strtotime($stayStartTime));
                $filteredEvents[count($filteredEvents) - 1] = $lastValidLocation;
            } else if ($currentStatus == 'move') {
                // Add remaining move events
                $filteredEvents = array_merge($filteredEvents, $tempMoveEvents);
            }

            // stay 상태의 시간 범위에 있는 move 데이터를 삭제
            $filteredEventsFinal = [];
            $stayPeriods = [];

            foreach ($filteredEvents as $event) {
                if ($event['logStatus'] == 'stay') {
                    $stayPeriods[] = [
                        'startTime' => strtotime($event['startTime']),
                        'endTime' => strtotime($event['endTime'])
                    ];
                    $filteredEventsFinal[] = $event;
                } else {
                    $isWithinStayPeriod = false;
                    $eventStartTime = strtotime($event['startTime']);
                    $eventEndTime = strtotime($event['endTime']);

                    foreach ($stayPeriods as $period) {
                        if ($eventStartTime > $period['startTime'] && $eventEndTime < $period['endTime']) {
                            $isWithinStayPeriod = true;
                            break;
                        }
                    }

                    if (!$isWithinStayPeriod) {
                        $filteredEventsFinal[] = $event;
                    }
                }
            }

            // 최종 필터링된 이벤트 목록
            // $filteredEvents = $filteredEventsFinal;

            // // stay 기간 생성
            // $stayPeriods = [];
            // foreach ($filteredEvents as $event) {
            //     if ($event['logStatus'] == 'stay') {
            //         $stayPeriods[] = [
            //             'startTime' => strtotime($event['startTime']),
            //             'endTime' => strtotime($event['endTime'])
            //         ];
            //     }
            // }

            // // stay 기간 내의 move 데이터 필터링
            // foreach ($filteredEvents as $event) {
            //     $eventStartTime = strtotime($event['startTime']);
            //     $eventEndTime = strtotime($event['endTime']);
            //     $shouldKeep = true;

            //     if ($event['logStatus'] == 'move') {
            //         foreach ($stayPeriods as $stay) {
            //             // 이벤트가 stay 기간과 겹치는지 확인
            //             if (($eventStartTime >= $stay['startTime'] && $eventStartTime < $stay['endTime']) ||
            //                 ($eventEndTime > $stay['startTime'] && $eventEndTime <= $stay['endTime']) ||
            //                 ($eventStartTime <= $stay['startTime'] && $eventEndTime >= $stay['endTime'])) {
            //                 $shouldKeep = false;
            //                 break;
            //             }
            //         }
            //     }

            //     if ($shouldKeep) {
            //         $filteredEventsMove[] = $event;
            //     }
            // }

            $events = $filteredEventsFinal;


            $stay_count = 1;
            $move_count = 1;
            $log_count = 1;

            foreach ($events as $index => &$event) {
                $event['totalLogCount'] = $index + 1;
                $log_count = $index + 1;
                
                if ($event['logStatus'] == 'stay') {
                    $start_time = new DateTime($event['startTime']);
                    $end_time = new DateTime($event['endTime']);

                    $event['stay_move_count'] = $stay_count++;
                    $content = '<div class="point_wrap point2" data-rangeindex="' . $event['totalLogCount'] . '">
                                    <button type="button" class="btn log_point point_stay">
                                        <span class="point_inner">
                                            <span class="point_txt">' . $event['stay_move_count'] . '</span>
                                        </span>
                                    </button>
                                    <div class="infobox rounded-sm bg-white px_08 py_08">
                                        <p class="fs_12 fw_800 text_dynamic">' . $start_time->format('H:i') . ' ~ ' . $end_time->format('H:i') . '</p>
                                        <p class="fs_10 fw_600 text_dynamic text-primary line_h1_2 mt-2">' . $event['stayTime'] . '</p>
                                        <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">' . $event['address'] . '</p>
                                    </div>
                                </div>';
                } else {
                    $event['stay_move_count'] = $move_count++;
                    $content = '<div class="point_wrap point2 d-none log_marker" data-rangeindex="' . $event['totalLogCount'] . '">
                                    <div class="infobox infobox_2 rounded-sm px_08 py_08" style="background-color: #413F4A; color: #E6F3FF;">
                                        <p class="fs_12 fw_800 text_dynamic">' . datetype($event['startTime'], 7) . '</p>
                                    </div>
                                </div>';
                }
                $event['content'] = $content;
            }

            // $arr_data 배열에 각 이벤트의 정보를 담습니다.
            foreach ($events as $index => $event) {
                $arr_data['logmarkerLat_' . ($index + 1)] = $event['latitude'];
                $arr_data['logmarkerLong_' . ($index + 1)] = $event['longitude'];
                $arr_data['logmarkerContent_' . ($index + 1)] = $event['content'];
            }
            // $log_count 에 필터링된 이벤트의 갯수를 저장합니다.
            $arr_data['log_count'] = $log_count;
            $arr_data['log_chk'] = 'Y';
        } else {
            $arr_data['log_count'] = 0;
            $arr_data['log_chk'] = 'N';
        }
        // 결과 데이터를 캐시에 저장 (30분 동안)
        CacheUtil::set($cache_key, $arr_data, 1800);
    } else {
        // 캐시에서 데이터를 가져옴
        $arr_data = $cached_data;
    }
    // $arr_data 배열 출력
    echo json_encode($arr_data, JSON_PRETTY_PRINT);
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