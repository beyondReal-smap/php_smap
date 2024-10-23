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
        p_alert($translations['txt_login_required'], './login', '');
    }

    $point_t = 'POINT(' . $_SESSION['_mt_long'] . ', ' . $_SESSION['_mt_lat'] . ')';

    unset($list_rlt);
    $list_rlt = $DB->rawQuery("SELECT *, ST_Distance_Sphere(" . $point_t . ", POINT(rlt_long, rlt_lat)) AS distance FROM recomand_location_t WHERE ST_Distance_Sphere(" . $point_t . ", POINT(rlt_long, rlt_lat)) <= " . RECOM_CIRCLE . " and rlt_show = 'Y' ORDER BY distance asc limit 10");

    if ($list_rlt) {
        foreach ($list_rlt as $row_rlt) { ?>
            <div class="border_orange rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                <div class="mr-2">
                    <p class="fs_13 fc_orange rounded_04 bg_fff5ea text-center px_06 py_02 text_dynamic line1_text line_h1_4 w_fit mb-2"><?=$translations['txt_recommended_place']?></p>
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
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sgdt_idx'] == '') {
        p_alert($translations['txt_invalid_access_sgdt_idx'], './login', '');
    }
    if ($_POST['sgdt_mt_idx'] == '') {
        p_alert($translations['txt_invalid_access_sgdt_mt_idx'], './login', '');
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
                    <img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" width="16px" alt="<?=$translations['txt_month_selection_icon']?>" />
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
                                            console.log('<?=$translations['txt_invalid_access']?>.');
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
        p_alert($translations['txt_login_required'], './login', '');
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

        // 데이터를 JSON 형태로 반환
        $locationLogData = array(
            "scheduleCount" => number_format($cnt),
            "distance" => get_distance_km($rtn[0]),
            "duration" => get_distance_hm($rtn[1]),
            "steps" => $rtn[2]
        );

        echo json_encode($locationLogData);
    }
} elseif ($_POST['act'] == "get_line2") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    // Function to calculate distance between two coordinates using Haversine formula
    function haversineDistance2($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // 거리 계산 함수
    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
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

    function isOutlier($prev, $current, $next)
    {
        $distancePrevCurrent = calculateDistance(
            $prev['latitude'],
            $prev['longitude'],
            $current['latitude'],
            $current['longitude']
        );
        $distanceCurrentNext = calculateDistance(
            $current['latitude'],
            $current['longitude'],
            $next['latitude'],
            $next['longitude']
        );
        $distancePrevNext = calculateDistance(
            $prev['latitude'],
            $prev['longitude'],
            $next['latitude'],
            $next['longitude']
        );

        // 현재 위치가 이전과 다음 위치 사이에서 너무 벗어나 있는지 확인
        $threshold = 1.8; // 이 값은 상황에 따라 조정 가능
        if ($distancePrevCurrent + $distanceCurrentNext > $distancePrevNext * $threshold) {
            return true;
        }
        return false;
    }

    function formatStayTime($seconds)
    {
        global $translations;

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $stay_time_formatted = "";

        if ($hours > 0) {
            $stay_time_formatted .= $hours . ' ' . $translations['txt_hr'];
        }

        $stay_time_formatted .= $minutes . ' ' . $translations['txt_minu'] . ' ' . $translations['txt_stay'];
        return $stay_time_formatted;
    }

    function log_to_file($message, $file_path)
    {
        $date = date('Y-m-d H:i:s');
        $log_message = "[{$date}] {$message}" . PHP_EOL;
        file_put_contents($file_path, $log_message, FILE_APPEND);
    }


    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');

    // 기본 지도 위치 지정
    if ($sgdt_row) {
        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $mem_row = $DB->getone('member_t');

        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        // $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
        // $DB->where(" mlt_speed>= " . $slt_mlt_speed);
        $DB->orderby('mlt_gps_time', 'desc');
        $mt_location_info = $DB->getone('member_location_log_t');

        if ($_SESSION['_mt_lat'] == '') {
            $_SESSION['_mt_lat'] = 37.5665;
        }
        if ($_SESSION['_mt_long'] == '') {
            $_SESSION['_mt_long'] = 126.9780;
        }
        $arr_data['my_lat'] = $mt_location_info['mlt_lat'] == "" ? $_SESSION['_mt_lat'] : $mt_location_info['mlt_lat'];
        $arr_data['mt_long'] = $mt_location_info['mlt_long'] == "" ? $_SESSION['_mt_long'] : $mt_location_info['mlt_long'];
        $arr_data['my_profile'] = $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']);
    } else {
        $arr_data['my_lat'] = $_SESSION['_mt_lat'] == "" ? 37.5665 : $_SESSION['_mt_lat'];
        $arr_data['mt_long'] = $_SESSION['_mt_long'] == "" ? 126.9780 : $_SESSION['_mt_long'];
        $arr_data['my_profile'] = $_SESSION['_mt_file1'] == "" ? $ct_no_img_url : $_SESSION['_mt_file1'];
    }

    // ... (일정 마커 구하기, 내 장소 마커 구하기)
    // 일정 마커 구하기
    $arr_sst_idx = get_schedule_main($_POST['sgdt_idx'], $_POST['event_start_date'], $sgdt_row['mt_idx']);
    // log_to_file("get_schedule_main: " . $_POST['sgdt_idx'] . ' / ' . $_POST['event_start_date']. ' / ' . $sgdt_row['mt_idx'], $log_file);
    // log_to_file("arr_sst_idx array: " . json_encode($arr_sst_idx, JSON_PRETTY_PRINT), $log_file);
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
                    $sst_all_day_t = $translations['txt_all_day']; 
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

                $arr_data['markerLat_' . $count] = $row_sst_a['sst_location_lat'];
                $arr_data['markerLong_' . $count] = $row_sst_a['sst_location_long'];
                $arr_data['markerTitle_' . $count] = $row_sst_a['sst_title'];
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
            $arr_data['locationmarkerLat_' . $mycount] = $row_slt['slt_lat'];
            $arr_data['locationmarkerLong_' . $mycount] = $row_slt['slt_long'];
            $arr_data['locationmarkerTitle_' . $mycount] = $row_slt['slt_title'];
            $mycount++;
        }
        $arr_data['location_chk'] = 'Y';
        $arr_data['location_count'] = $mycount - 1;
    } else {
        $arr_data['location_chk'] = 'N';
        $arr_data['location_count'] = 0;
    }

    // ... (기존 PHP 코드)

    // 이동로그 및 체류로그 데이터 처리

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
                $DB->where(" mlt_speed < " . $slt_mlt_speed);
                $DB->where(" (mlt_lat> 0 and mlt_long > 0)");
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
                            $stay_time_formatted .= $hours . ' ' . $translations['txt_hr'] . ' ';
                        }
                        $stay_time_formatted .= $minutes . ' ' . $translations['txt_minu'] . ' ' . $translations['txt_stay'];

                        $addr = get_search_coordinate2address($first_location['mlt_lat'], $first_location['mlt_long'], $userLang);
                        $address = $addr['area2'] . ' ' . $addr['area3'];

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
                    $stay_time_formatted .= $hours . ' ' .$translations['txt_hr'] . ' ';
                }
                $stay_time_formatted .= $minutes . ' ' . $translations['txt_minu'] . ' ' . $translations['txt_stay'];

                // Get address
                $addr = get_search_coordinate2address($row_mlt['start_lat'], $row_mlt['start_long'], $userLang);
                $address = $addr['area2'] . ' ' . $addr['area3'];

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

    // 이벤트 필터링 및 병합

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

        // 종료 시간을 기준으로 정렬
        usort($events, function ($a, $b) {
            return strtotime($a['endTime']) - strtotime($b['endTime']);
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
                $lastValidLocation['latitude'],
                $lastValidLocation['longitude'],
                $current['latitude'],
                $current['longitude']
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
            $filteredEvents[] = $lastValidLocation;
        } else if ($currentStatus == 'move') {
            // Add remaining move events
            $filteredEvents = array_merge($filteredEvents, $tempMoveEvents);
        }

        // 마지막 stay 이벤트가 추가되지 않았다면 추가
        if (end($filteredEvents)['logStatus'] != 'stay' && $currentStatus == 'stay') {
            $lastValidLocation['endTime'] = end($events)['endTime'];
            $lastValidLocation['stayTime'] = formatStayTime(strtotime($lastValidLocation['endTime']) - strtotime($stayStartTime));
            $filteredEvents[] = $lastValidLocation;
        }

        // stay 상태의 시간 범위에 있는 move 데이터를 삭제
        $filteredEventsFinal = [];
        $stayPeriods = [];

        // 먼저 모든 stay 기간을 수집
        foreach ($filteredEvents as $event) {
            if ($event['logStatus'] == 'stay') {
                $stayPeriods[] = [
                    'startTime' => strtotime($event['startTime']),
                    'endTime' => strtotime($event['endTime'])
                ];
            }
        }

        // 그 다음 모든 이벤트를 처리
        foreach ($filteredEvents as $event) {
            if ($event['logStatus'] == 'stay') {
                // stay 이벤트는 항상 유지
                $filteredEventsFinal[] = $event;
            } else {
                $isWithinStayPeriod = false;
                $eventStartTime = strtotime($event['startTime']);
                $eventEndTime = strtotime($event['endTime']);

                foreach ($stayPeriods as $period) {
                    if ($eventStartTime >= $period['startTime'] && $eventEndTime <= $period['endTime']) {
                        $isWithinStayPeriod = true;
                        break;
                    }
                }

                if (!$isWithinStayPeriod) {
                    $filteredEventsFinal[] = $event;
                }
            }
            // log_to_file("event " . $event, $log_file);
        }
        // log_to_file(" jj events: " . json_encode($filteredEventsFinal, JSON_PRETTY_PRINT), $log_file);
        // stay 이벤트 병합 및 체류 시간 계산
        $mergedStayEvents = [];
        $currentStay = null;
        $sameTimeEvents = [];

        $lastEvent = end($filteredEventsFinal);
        $filteredEventsFinalWithoutLast = array_slice($filteredEventsFinal, 0, -1);

        foreach ($filteredEventsFinalWithoutLast as $event) {
            if ($event['logStatus'] == 'stay') {
                if ($currentStay === null) {
                    $currentStay = $event;
                    $sameTimeEvents = [$event];
                } else {
                    $currentEndTime = strtotime($currentStay['endTime']);
                    $nextStartTime = strtotime($event['startTime']);

                    if ($nextStartTime <= $currentEndTime) {
                        // 겹치는 경우, 시작 시간은 더 이른 것으로, 종료 시간은 더 늦은 것으로 업데이트
                        $currentStay['startTime'] = min($currentStay['startTime'], $event['startTime']);
                        $currentStay['endTime'] = max($currentStay['endTime'], $event['endTime']);
                        $sameTimeEvents = [$currentStay];
                    } else {
                        // 겹치지 않는 경우, 현재 stay를 저장하고 새로운 stay 시작
                        if (!empty($sameTimeEvents)) {
                            $mergedStayEvents[] = $sameTimeEvents[0];  // 같은 시간대의 이벤트 중 하나만 저장
                        } else {
                            $mergedStayEvents[] = $currentStay;
                        }
                        $currentStay = $event;
                        $sameTimeEvents = [$event];
                    }
                }
            } else {
                if ($currentStay !== null) {
                    if (!empty($sameTimeEvents)) {
                        $mergedStayEvents[] = $sameTimeEvents[0];  // 같은 시간대의 이벤트 중 하나만 저장
                    } else {
                        $mergedStayEvents[] = $currentStay;
                    }
                    $currentStay = null;
                    $sameTimeEvents = [];
                }
                $mergedStayEvents[] = $event;
            }
        }

        // 마지막 이벤트 처리
        if ($lastEvent['logStatus'] == 'stay') {
            if ($currentStay !== null) {
                $currentEndTime = strtotime($currentStay['endTime']);
                $lastStartTime = strtotime($lastEvent['startTime']);

                if ($lastStartTime <= $currentEndTime) {
                    // 마지막 stay 이벤트가 현재 stay와 겹치는 경우
                    $currentStay['endTime'] = max($currentStay['endTime'], $lastEvent['endTime']);
                    $mergedStayEvents[] = $currentStay;
                } else {
                    // 겹치지 않는 경우, 현재 stay를 저장하고 마지막 stay 추가
                    $mergedStayEvents[] = $currentStay;
                    $mergedStayEvents[] = $lastEvent;
                }
            } else {
                // 현재 stay가 없는 경우, 마지막 stay 이벤트 추가
                $mergedStayEvents[] = $lastEvent;
            }
        } else {
            // 마지막 이벤트가 stay가 아닌 경우
            if ($currentStay !== null) {
                $mergedStayEvents[] = $currentStay;
            }
            $mergedStayEvents[] = $lastEvent;
        }

        // 결과 출력
        // print_r($mergedStayEvents);


        // 체류 시간 계산 및 포맷팅
        foreach ($filteredEventsFinal as &$event) {
            if ($event['logStatus'] == 'stay') {
                $stayDuration = strtotime($event['endTime']) - strtotime($event['startTime']);
                $hours = floor($stayDuration / 3600);
                $minutes = floor(($stayDuration % 3600) / 60);
                $event['stayTime'] = sprintf(" %d" . ' ' . $translations['txt_hr'] . " %d" . $translations['txt_minu'] . ' '. $translations['txt_stay'], $hours, $minutes);
            }
            // log_to_file("event['stayTime']: " . $event['stayTime'], $log_file);
        }

        $mevents = $filteredEventsFinal;
    }

    // 최종 마커 데이터 생성
    $finalMarkers = [];
    foreach ($mevents as $index => $event) {
        $marker = [];
        $marker['latitude'] = $event['latitude'];
        $marker['longitude'] = $event['longitude'];

        // HTML 문자열 대신 데이터만 전달
        if ($event['logStatus'] == 'stay') {
            $start_time = new DateTime($event['startTime']);
            $end_time = new DateTime($event['endTime']);

            $marker['type'] = 'stay';
            $marker['stay_move_count'] = $index + 1;
            $marker['time'] = $start_time->format('H:i') . ' ~ ' . $end_time->format('H:i');
            $marker['stayTime'] = $event['stayTime'];
            $marker['address'] = $event['address'];
        } else {
            $marker['type'] = 'move';
            $marker['stay_move_count'] = $index + 1;
            $marker['time'] = datetype($event['startTime'], 7);
        }

        $finalMarkers[] = $marker;
    }

    $arr_data['log_markers'] = $finalMarkers;
    $arr_data['log_count'] = count($finalMarkers);
    $arr_data['log_chk'] = 'Y';


    // $arr_data 배열 출력
    echo json_encode($arr_data, JSON_PRETTY_PRINT);
} elseif ($_POST['act'] == "get_line") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    // Function to calculate distance between two coordinates using Haversine formula
    function haversineDistance2($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // 거리 계산 함수
    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
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

    function isOutlier($prev, $current, $next)
    {
        $distancePrevCurrent = calculateDistance(
            $prev['latitude'],
            $prev['longitude'],
            $current['latitude'],
            $current['longitude']
        );
        $distanceCurrentNext = calculateDistance(
            $current['latitude'],
            $current['longitude'],
            $next['latitude'],
            $next['longitude']
        );
        $distancePrevNext = calculateDistance(
            $prev['latitude'],
            $prev['longitude'],
            $next['latitude'],
            $next['longitude']
        );

        // 현재 위치가 이전과 다음 위치 사이에서 너무 벗어나 있는지 확인
        $threshold = 1.8; // 이 값은 상황에 따라 조정 가능
        if ($distancePrevCurrent + $distanceCurrentNext > $distancePrevNext * $threshold) {
            return true;
        }
        return false;
    }

    function formatStayTime($seconds)
    {
        global $translations;

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $stay_time_formatted = "";

        if ($hours > 0) {
            $stay_time_formatted .= $hours . ' ' . $translations['txt_hr'] . ' ';
        }

        $stay_time_formatted .= $minutes . ' ' . $translations['txt_minu'] . ' ' . $translations['txt_stay'];
        return $stay_time_formatted;
    }

    function log_to_file($message, $file_path)
    {
        $date = date('Y-m-d H:i:s');
        $log_message = "[{$date}] {$message}" . PHP_EOL;
        file_put_contents($file_path, $log_message, FILE_APPEND);
    }


    $DB->where('sgdt_idx', $_POST['sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');

    // 기본 지도 위치 지정
    if ($sgdt_row) {
        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $mem_row = $DB->getone('member_t');

        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        // $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
        // $DB->where(" mlt_speed>= " . $slt_mlt_speed);
        $DB->orderby('mlt_gps_time', 'desc');
        $mt_location_info = $DB->getone('member_location_log_t');

        if ($_SESSION['_mt_lat'] == '') {
            $_SESSION['_mt_lat'] = 37.5665;
        }
        if ($_SESSION['_mt_long'] == '') {
            $_SESSION['_mt_long'] = 126.9780;
        }
        $arr_data['my_lat'] = $mt_location_info['mlt_lat'] == "" ? $_SESSION['_mt_lat'] : $mt_location_info['mlt_lat'];
        $arr_data['mt_long'] = $mt_location_info['mlt_long'] == "" ? $_SESSION['_mt_long'] : $mt_location_info['mlt_long'];
        $arr_data['my_profile'] = $mem_row['mt_file1'] == "" ? $ct_no_img_url : get_image_url($mem_row['mt_file1']);
    } else {
        $arr_data['my_lat'] = $_SESSION['_mt_lat'] == "" ? 37.5665 : $_SESSION['_mt_lat'];
        $arr_data['mt_long'] = $_SESSION['_mt_long'] == "" ? 126.9780 : $_SESSION['_mt_long'];
        $arr_data['my_profile'] = $_SESSION['_mt_file1'] == "" ? $ct_no_img_url : $_SESSION['_mt_file1'];
    }

    // ... (일정 마커 구하기, 내 장소 마커 구하기)
    // 일정 마커 구하기
    $arr_sst_idx = get_schedule_main($_POST['sgdt_idx'], $_POST['event_start_date'], $sgdt_row['mt_idx']);
    // log_to_file("get_schedule_main: " . $_POST['sgdt_idx'] . ' / ' . $_POST['event_start_date']. ' / ' . $sgdt_row['mt_idx'], $log_file);
    // log_to_file("arr_sst_idx array: " . json_encode($arr_sst_idx, JSON_PRETTY_PRINT), $log_file);
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
                    $sst_all_day_t = $translations['txt_all_day']; 
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

                $arr_data['markerLat_' . $count] = $row_sst_a['sst_location_lat'];
                $arr_data['markerLong_' . $count] = $row_sst_a['sst_location_long'];
                $arr_data['markerTitle_' . $count] = $row_sst_a['sst_title'];
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
            $arr_data['locationmarkerLat_' . $mycount] = $row_slt['slt_lat'];
            $arr_data['locationmarkerLong_' . $mycount] = $row_slt['slt_long'];
            $arr_data['locationmarkerTitle_' . $mycount] = $row_slt['slt_title'];
            $mycount++;
        }
        $arr_data['location_chk'] = 'Y';
        $arr_data['location_count'] = $mycount - 1;
    } else {
        $arr_data['location_chk'] = 'N';
        $arr_data['location_count'] = 0;
    }

    // ... (기존 PHP 코드)

    // 이동로그 및 체류로그 데이터 처리

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
                $DB->where(" mlt_speed < " . $slt_mlt_speed);
                $DB->where(" (mlt_lat> 0 and mlt_long > 0)");
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
                            $stay_time_formatted .= $hours . ' ' . $translations['txt_hr'] . ' ';
                        }
                        $stay_time_formatted .= $minutes . ' ' . $translations['txt_minu'] . ' ' . $translations['txt_stay'];

                        $addr = get_search_coordinate2address($first_location['mlt_lat'], $first_location['mlt_long'], $userLang);
                        $address = $addr['area2'] . ' ' . $addr['area3'];

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


    // 오늘 자 체류로그가 있을 때
    if ($list_stay) {
        $log_count = 1;
        $list_count_stay =       count($list_stay);
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
                    $stay_time_formatted .= $hours . ' ' . $translations['txt_hr'] . ' ';
                }
                $stay_time_formatted .= $minutes . ' ' . $translations['txt_minu'] . ' ' . $translations['txt_stay'];

                // Get address
                $addr = get_search_coordinate2address($row_mlt['start_lat'], $row_mlt['start_long'], $userLang);
                $address = $addr['area2'] . ' ' . $addr['area3'];

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

    // 이벤트 필터링 및 병합

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

        // 종료 시간을 기준으로 정렬
        usort($events, function ($a, $b) {
            return strtotime($a['endTime']) - strtotime($b['endTime']);
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
                $lastValidLocation['latitude'],
                $lastValidLocation['longitude'],
                $current['latitude'],
                $current['longitude']
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
            $filteredEvents[] = $lastValidLocation;
        } else if ($currentStatus == 'move') {
            // Add remaining move events
            $filteredEvents = array_merge($filteredEvents, $tempMoveEvents);
        }

        // 마지막 stay 이벤트가 추가되지 않았다면 추가
        if (end($filteredEvents)['logStatus'] != 'stay' && $currentStatus == 'stay') {
            $lastValidLocation['endTime'] = end($events)['endTime'];
            $lastValidLocation['stayTime'] = formatStayTime(strtotime($lastValidLocation['endTime']) - strtotime($stayStartTime));
            $filteredEvents[] = $lastValidLocation;
        }

        // stay 상태의 시간 범위에 있는 move 데이터를 삭제
        $filteredEventsFinal = [];
        $stayPeriods = [];

        // 먼저 모든 stay 기간을 수집
        foreach ($filteredEvents as $event) {
            if ($event['logStatus'] == 'stay') {
                $stayPeriods[] = [
                    'startTime' => strtotime($event['startTime']),
                    'endTime' => strtotime($event['endTime'])
                ];
            }
        }

        // 그 다음 모든 이벤트를 처리
        foreach ($filteredEvents as $event) {
            if ($event['logStatus'] == 'stay') {
                // stay 이벤트는 항상 유지
                $filteredEventsFinal[] = $event;
            } else {
                $isWithinStayPeriod = false;
                $eventStartTime = strtotime($event['startTime']);
                $eventEndTime = strtotime($event['endTime']);

                foreach ($stayPeriods as $period) {
                    if ($eventStartTime >= $period['startTime'] && $eventEndTime <= $period['endTime']) {
                        $isWithinStayPeriod = true;
                        break;
                    }
                }

                if (!$isWithinStayPeriod) {
                    $filteredEventsFinal[] = $event;
                }
            }
            // log_to_file("event " . $event, $log_file);
        }
        // log_to_file(" jj events: " . json_encode($filteredEventsFinal, JSON_PRETTY_PRINT), $log_file);
        // stay 이벤트 병합 및 체류 시간 계산
        $mergedStayEvents = [];
        $currentStay = null;
        $sameTimeEvents = [];

        $lastEvent = end($filteredEventsFinal);
        $filteredEventsFinalWithoutLast = array_slice($filteredEventsFinal, 0, -1);

        foreach ($filteredEventsFinalWithoutLast as $event) {
            if ($event['logStatus'] == 'stay') {
                if ($currentStay === null) {
                    $currentStay = $event;
                    $sameTimeEvents = [$event];
                } else {
                    $currentEndTime = strtotime($currentStay['endTime']);
                    $nextStartTime = strtotime($event['startTime']);

                    if ($nextStartTime <= $currentEndTime) {
                        // 겹치는 경우, 시작 시간은 더 이른 것으로, 종료 시간은 더 늦은 것으로 업데이트
                        $currentStay['startTime'] = min($currentStay['startTime'], $event['startTime']);
                        $currentStay['endTime'] = max($currentStay['endTime'], $event['endTime']);
                        $sameTimeEvents = [$currentStay];
                    } else {
                        // 겹치지 않는 경우, 현재 stay를 저장하고 새로운 stay 시작
                        if (!empty($sameTimeEvents)) {
                            $mergedStayEvents[] = $sameTimeEvents[0];  // 같은 시간대의 이벤트 중 하나만 저장
                        } else {
                            $mergedStayEvents[] = $currentStay;
                        }
                        $currentStay = $event;
                        $sameTimeEvents = [$event];
                    }
                }
            } else {
                if ($currentStay !== null) {
                    if (!empty($sameTimeEvents)) {
                        $mergedStayEvents[] = $sameTimeEvents[0];  // 같은 시간대의 이벤트 중 하나만 저장
                    } else {
                        $mergedStayEvents[] = $currentStay;
                    }
                    $currentStay = null;
                    $sameTimeEvents = [];
                }
                $mergedStayEvents[] = $event;
            }
        }

        // 마지막 이벤트 처리
        if ($lastEvent['logStatus'] == 'stay') {
            if ($currentStay !== null) {
                $currentEndTime = strtotime($currentStay['endTime']);
                $lastStartTime = strtotime($lastEvent['startTime']);

                if ($lastStartTime <= $currentEndTime) {
                    // 마지막 stay 이벤트가 현재 stay와 겹치는 경우
                    $currentStay['endTime'] = max($currentStay['endTime'], $lastEvent['endTime']);
                    $mergedStayEvents[] = $currentStay;
                } else {
                    // 겹치지 않는 경우, 현재 stay를 저장하고 마지막 stay 추가
                    $mergedStayEvents[] = $currentStay;
                    $mergedStayEvents[] = $lastEvent;
                }
            } else {
                // 현재 stay가 없는 경우, 마지막 stay 이벤트 추가
                $mergedStayEvents[] = $lastEvent;
            }
        } else {
            // 마지막 이벤트가 stay가 아닌 경우
            if ($currentStay !== null) {
                $mergedStayEvents[] = $currentStay;
            }
            $mergedStayEvents[] = $lastEvent;
        }

        // 결과 출력
        // print_r($mergedStayEvents);


        // 체류 시간 계산 및 포맷팅
        foreach ($filteredEventsFinal as &$event) {
            if ($event['logStatus'] == 'stay') {
                $stayDuration = strtotime($event['endTime']) - strtotime($event['startTime']);
                $hours = floor($stayDuration / 3600);
                $minutes = floor(($stayDuration % 3600) / 60);
                $event['stayTime'] = sprintf(" %d" . ' ' . $translations['txt_hr'] . " %d" . ' ' . $translations['txt_minu'] . ' ' . $translations['txt_stay'], $hours, $minutes);
            }
            // log_to_file("event['stayTime']: " . $event['stayTime'], $log_file);
        }

        $mevents = $filteredEventsFinal;
    }

    // 최종 마커 데이터 생성
    $finalMarkers = [];
    foreach ($mevents as $index => $event) {
        $marker = [];
        $marker['latitude'] = $event['latitude'];
        $marker['longitude'] = $event['longitude'];

        // HTML 문자열 대신 데이터만 전달
        if ($event['logStatus'] == 'stay') {
            $start_time = new DateTime($event['startTime']);
            $end_time = new DateTime($event['endTime']);

            $marker['type'] = 'stay';
            $marker['stay_move_count'] = $index + 1;
            $marker['time'] = $start_time->format('H:i') . ' ~ ' . $end_time->format('H:i');
            $marker['stayTime'] = $event['stayTime'];
            $marker['address'] = $event['address'];
        } else {
            $marker['type'] = 'move';
            $marker['stay_move_count'] = $index + 1;
            $marker['time'] = datetype($event['startTime'], 7);
        }

        $finalMarkers[] = $marker;
    }

    $arr_data['log_markers'] = $finalMarkers;
    $arr_data['log_count'] = count($finalMarkers);
    $arr_data['log_chk'] = 'Y';


    // $arr_data 배열 출력
    echo json_encode($arr_data, JSON_PRETTY_PRINT);
} elseif ($_POST['act'] == "input_location") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
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

    p_alert($translations['txt_registered'], './location', '');
} elseif ($_POST['act'] == "my_location_list") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
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

            $result_data['markerLat_' . $count] = $row_slt['slt_lat'];
            $result_data['markerLong_' . $count] = $row_slt['slt_long'];
            $result_data['markerTitle_' . $count] = $row_slt['slt_title'];

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
        echo json_encode(['result' => 'error', 'message' => $translations['txt_login_required']]);
        exit;
    }

    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    $limit = ($mem_row['mt_level'] == '2') ? 4 : 10;

    $DB->where('(sgdt_idx = "' . $_POST['sgdt_idx'] . '" or mt_idx="' . $_POST . ['mt_idx'] . '")');
    $DB->where('slt_show', 'Y');
    $DB->orderby('slt_wdate', 'asc');
    $slt_list = $DB->get('smap_location_t', $limit);

    $data = [
        'slt_list' => $slt_list,
    ];

    // 오너인 그룹 수
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgt_show', 'Y');
    $row = $DB->getone('smap_group_t', 'count(*) as cnt');
    $data['sgt_cnt'] = $row['cnt'];

    // 리더인 그룹 수
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_leader_chk', 'Y');
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
    $data['sgdt_leader_cnt'] = $row['cnt'];

    if ($data['sgt_cnt'] > 0 || $data['sgdt_leader_cnt'] > 0) {
        // 추천 장소 추가
        $DB->where('rlt_show', 'Y');
        $data['rlt_list'] = $DB->get('recomand_location_t');
    }

    echo json_encode(['result' => 'success', 'data' => $data]);
    exit;
} elseif ($_POST['act'] == "location_delete") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['slt_idx'] == '') {
        p_alert($translations['txt_invalid_access_slt_idx'], './login', '');
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
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['slt_idx'] == '') {
        p_alert($translations['txt_invalid_access_slt_idx'], './login', '');
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
        p_alert($translations['txt_login_required'], './login', '');
    }
    if ($_POST['sgdt_idx'] == '') {
        p_alert($translations['txt_invalid_access_sgdt_idx'], './login', '');
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
        echo json_encode(['result' => 'error', 'message' => $translations['txt_login_required']]);
        exit;
    }

    if (!isset($_POST['group_sgdt_idx'])) {
        echo json_encode(['result' => 'error', 'message' => $translations['txt_invalid_access_group_sgdt_idx']]);
        exit;
    }


    $sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); // 오너인 그룹 수
    $DB->where('sgdt_idx', $_POST['group_sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');


    $DB->where('sgdt_idx', $_POST['group_sgdt_idx']);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

    $data = [
        'my_info' => [
            'sgt_idx' => $sgdt_row['sgt_idx'],
            'sgdt_idx' => $sgdt_row['sgdt_idx'],
            'mt_idx' => $sgdt_row['mt_idx'],
            'profile_image' => $_SESSION['_mt_file1'],
            'nickname' => $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'],
        ],
        'group_members' => [],
        'sgt_cnt' => $sgt_cnt,
    ];

    if ($row_sgdt) {

        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where('sgt_show', 'Y');
        $DB->orderBy("sgt_udate", "desc");
        $DB->orderBy("sgt_idx", "asc");
        $list_sgt = $DB->get('smap_group_t');

        if ($list_sgt) {
            foreach ($list_sgt as $row_sgt) {
                $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
                $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);

                if ($invite_cnt || $list_sgdt['data']) {
                    if ($list_sgdt['data']) {
                        foreach ($list_sgdt['data'] as $val) {                           
                            $data['group_members'][] = [
                            'sgt_idx' => $row_sgt['sgt_idx'],
                            'sgdt_idx' => $val['sgdt_idx'],
                            'profile_image' => $val['mt_file1_url'],
                            'nickname' => $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'],
                            'mt_idx' => $val['mt_idx']
                        ];
                    }
                }
            }
        }
    }
}

echo json_encode(['result' => 'success', 'data' => $data]);
} elseif ($_POST['act'] == "marker_reload") {
if ($_SESSION['_mt_idx'] == '') {
    p_alert($translations['txt_login_required'], './login', '');
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