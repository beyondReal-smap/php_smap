<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == "weather_get") {
    $get_weather_t = 'Y';

    session_location_update($_SESSION['_mt_idx']); // 세션위치 재정의

    // 캐시 키 생성
    $cache_key = 'weather_dat_' . $_SESSION['_mt_idx'];

    // 캐시에서 데이터 확인
    // $cached_data = CacheUtil::get($cache_key);

    if ($cached_data) {
        // 캐시에서 데이터를 찾았을 경우
        $get_weather_status = $cached_data['status'];
        $get_weather_min = $cached_data['min'];
        $get_weather_max = $cached_data['max'];
        $get_weather_per = $cached_data['per'];
        $get_weather_icon = $cached_data['icon'];
        $get_weather_txt = $cached_data['txt'];
        $region = $cached_data['region'];
    } else {
        // 캐시에 데이터가 없는 경우, API를 호출하여 데이터를 가져옴
        include $_SERVER['DOCUMENT_ROOT'] . "/weather.inc.php";

        $weatherClass = new weatherClass();

        //단기예보
        $response1 = $weatherClass->requestForecast($_SESSION['_mt_lat'], $_SESSION['_mt_long'], 0, 6, '');

        $today_weather1 = $weatherClass->parseWeather($response1, "TMN", "TMX", "POP");
        $get_weather_min = $today_weather1['TMN'];
        $get_weather_max = $today_weather1['TMX'];
        $get_weather_per = $today_weather1['POP'];

        //초단기예보
        $response2 = $weatherClass->requestForecast($_SESSION['_mt_lat'], $_SESSION['_mt_long'], 0, 0, '');
        // $logger->write($weatherClass);
        $logger->write("Full API Response: " . json_encode($response2));
        $today_weather2 = $weatherClass->parseWeather($response2, "SKY", "PTY");

        $logger->write("mt_idx : " . $_SESSION['_mt_idx']);
        $logger->write("get_weather_min : " . $get_weather_min);
        $logger->write("get_weather_max : " . $get_weather_max);
        $logger->write("get_weather_per : " . $get_weather_per);
        $logger->write("SKY : " . $today_weather2['SKY']);
        $logger->write("PTY : " . $today_weather2['PTY']);
   
        // 날씨 상태 결정 로직 (기존 코드와 동일)
        if ($today_weather2['SKY'] == '1') {
            $get_weather_status = '8'; //맑음
        } elseif ($today_weather2['SKY'] == '3') {
            $get_weather_status = '1'; //구름 뒤에 있는 해(구름 많음)
        } elseif ($today_weather2['SKY'] == '4') {
            $get_weather_status = '2'; //구름(흐림)
            if ($today_weather2['PTY'] == '-') {
                $get_weather_status = '8'; //맑음
            } elseif ($today_weather2['PTY'] == '1') { //비
                $get_weather_status = '4'; //비
            } elseif ($today_weather2['PTY'] == '2') { //진눈개비
                $get_weather_status = '5'; //비와 눈
            } elseif ($today_weather2['PTY'] == '3') { //눈
                $get_weather_status = '6'; //눈
            } elseif ($today_weather2['PTY'] == '4') { //소나기
                $get_weather_status = '4'; //비
            } elseif ($today_weather2['PTY'] == '5') { //빗방울
                $get_weather_status = '4'; //비
            } elseif ($today_weather2['PTY'] == '6') { //빗방울/눈날림
                $get_weather_status = '5'; //비와 눈
            } elseif ($today_weather2['PTY'] == '7') { //눈날림
                $get_weather_status = '5'; //눈
            } else {
                $get_weather_status = '8'; //맑음
            }
        } elseif ($today_weather2['SKY'] == '-') {
            // 기존 로직과 동일
        } else {
            $get_weather_status = '8'; //맑음
        }

        $get_weather_icon = $arr_mt_weather_sky_icon[$get_weather_status];
        $get_weather_txt = $arr_mt_weather_sky[$get_weather_status];
        // logToFile("index_update - lat : {$_SESSION['_mt_lat']} / lng : {$_SESSION['_mt_long']} ");
        $region = get_search_coordinate2address($_SESSION['_mt_lat'], $_SESSION['_mt_long'], $userLang);

        // 캐시에 데이터 저장
        $cache_data = [
            'status' => $get_weather_status,
            'min' => $get_weather_min,
            'max' => $get_weather_max,
            'per' => $get_weather_per,
            'icon' => $get_weather_icon,
            'txt' => $get_weather_txt,
            'region' => $region
        ];
        CacheUtil::set($cache_key, $cache_data, 3600); // 1시간 동안 캐시 유지

        // logToFile("index_update - lat : {$_SESSION['_mt_lat']} / lng : {$_SESSION['_mt_long']} ");
        if ($_SESSION['_mt_idx']) {
            unset($arr_query);
            $arr_query = array(
                "mt_lat" => $_SESSION['_mt_lat'],
                "mt_long" => $_SESSION['_mt_long'],
                "mt_sido" => $region['area1'],
                "mt_gu" => $region['area2'],
                "mt_dong" => $region['area3'],
                "mt_weather_pop" => $get_weather_per,
                "mt_weather_sky" => $get_weather_status,
                "mt_weather_tmn" => $get_weather_min,
                "mt_weather_tmx" => $get_weather_max,
                "mt_weather_date" => $DB->now(),
            );

            $DB->where('mt_idx', $_SESSION['_mt_idx']);
            $DB->update('member_t', $arr_query);
        }
    }
    
    // $logger->write("------------------");
    // $logger->write($get_weather_icon);
    // $logger->write("------------------");
    // $logger->write($get_weather_txt);
    if ($get_weather_t == 'Y') {
?>
        <div class="d-flex align-items-center p_address">
            <p class="fs_12 text_light_gray fw_500 text_dynamic"><?= $region['area1'] ?> ·</p>
            <p class="fs_12 text_light_gray fw_500 text_dynamic"><?= $region['area3'] ?></p>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="date_weather d-flex align-items-center flex-wrap">
                <div class="fs_14 fw_600 text_dynamic mr-1 mt_08"><?= DateType(date("Y-m-d"), 3) ?><span class="ml-1"><img src="<?= CDN_HTTP ?>/img/<?= $get_weather_icon ?>" width="18px" alt="날씨" /></span></div>
                <div class="d-flex align-items-center mt_08 mr-3">
                    <p class="ml-1 fs_11 fw_600 text-text fw_500 mr-2"><span class="fs_11 text_light_gray mr-1">강수확률</span><?= $get_weather_per ?></p>
                    <p class="ml-1 fs_11 fw_600 text-text fw_500 mr-2"><span class="fs_11 text_light_gray mr-1">최저</span><?= $get_weather_min ?></p>
                    <p class="ml-1 fs_11 fw_600 fc_red fw_500"><span class="fs_11 text_light_gray mr-1">최고</span><?= $get_weather_max ?></p>
                </div>
            </div>
        </div>
        <!-- <p class="fs_12 text_gray text_dynamic p_content line_h1_2"><?= $get_weather_txt ?></p> -->
    <?php
    }
} else if ($_POST['act'] == "main_location") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }

    $s_date = date("Y-m-d");
    unset($list_slmt);
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->orderBy("slmt_idx", "desc");
    $list_slmt = $DB->get('smap_location_member_t');

    if ($list_slmt) {
    ?>
        <div class="mt-4">
            <?php
            foreach ($list_slmt as $row_slmt) {
                $mt_info = get_member_t_info($row_slmt['sgdt_mt_idx']);
                $mt_file1_url = get_image_url($mt_info['mt_file1']);
                $rtn = get_gps_distance_k($row_slmt['sgdt_mt_idx'], $s_date);
                $mt_location_info = get_member_location_log_t_info($row_slmt['sgdt_mt_idx']);

                $arr_rtn = array();
                if ($mt_location_info['mlt_speed']) {
                    $arr_rtn['speed_info'] = '이동중';
                    $mt_speed_t = round(($mt_location_info['mlt_speed'] * 3.6), 2);
                    if ($mt_speed_t < 1) {
                        $mt_speed_t = '1';
                    }
                    $arr_rtn['speed_km'] = $mt_speed_t . "km/h";
                }

                $my_add = get_search_coordinate2address($mt_location_info['mlt_lat'], $mt_location_info['mlt_long'], $userLang);
                $arr_rtn['my_add'] = $my_add['area1'] . " " . $my_add['area2'] . " " . $my_add['area3'];

                $arr_sst_idx = get_schedule_array($row_slmt['sgdt_mt_idx'], $s_date);
                $cnt = count($arr_sst_idx);

                if ($cnt > 0) {
                    $arr_sst_idx_im = implode(',', $arr_sst_idx);

                    unset($list_sst_a);
                    $DB->where("sst_idx in (" . $arr_sst_idx_im . ")");
                    $DB->where('sst_show', 'Y');
                    $DB->orderBy("sst_all_day", "asc");
                    $DB->orderBy("sst_sdate", "asc");
                    $list_sst_a = $DB->get('smap_schedule_t', 2);

                    $qw = 0;
                    $arr_main_location = array();
                    if ($list_sst_a) {
                        foreach ($list_sst_a as $row_sst_a) {
                            $gpd_d = gps_distance($mt_location_info['mlt_lat'], $mt_location_info['mlt_long'], $row_sst_a['sst_location_lat'], $row_sst_a['sst_location_long']);

                            if ($gpd_d < 0.1) { //100m이내면 체류중
                                $row_ds1 = $DB->rawQueryOne("SELECT mlt_wdate FROM member_location_log_t WHERE mt_idx = '" . $row_slmt['sgdt_mt_idx'] . "' and ST_Distance_Sphere(POINT(" . $mt_location_info['mlt_long'] . ", " . $mt_location_info['mlt_lat'] . "), POINT(mlt_long, mlt_lat)) <= 100 AND mlt_wdate BETWEEN '" . $s_date . " 00:00:00' AND '" . $s_date . " 23:59:59' ORDER BY mlt_wdate ASC LIMIT 0, 1");

                                $visit_time = cal_remain_times($row_ds1['mlt_wdate'], date("Y-m-d H:i:s"));
                                $visit_time = get_distance_m($visit_time);

                                $arr_rtn['speed_info'] = $row_sst_a['sst_title'] . ' ' . $visit_time;
                                $arr_rtn['speed_km'] = '체류중';
                            }

                            if ($qw == 1) {
                                $arr_rtn['my_add_next'] = $row_sst_a['sst_location_add'];
                                $arr_rtn['my_arrive_next'] = "예정";
                                $arr_rtn['my_time_next'] = "오후 04:20";
                            } else {
                                $arr_rtn['my_add_next'] = '';
                                $arr_rtn['my_arrive_next'] = '';
                                $arr_rtn['my_time_next'] = '';
                            }

                            $qw++;
                        }
                    }
                }
            ?>
                <div class="mb_25">
                    <div class="d-flex align-items-center">
                        <div class="w_fit">
                            <a href="./location?sgdt_mt_idx=<?= $row_slmt['sgdt_mt_idx'] ?>" class="d-flex align-items-center">
                                <div class="prd_img flex-shrink-0 mr_12">
                                    <div class="rect_square rounded_14">
                                        <img src="<?= $mt_file1_url ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                    </div>
                                </div>
                                <div>
                                    <p class="fs_14 fw_500 text_dynamic mr-2"><?= $mt_info['mt_nickname'] ?></p>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <?php
                                        if ($arr_rtn['speed_info'] == '이동중') {
                                        ?>
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1 mr-2"><?= $arr_rtn['speed_info'] ?> ·</p>
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1"><?= $arr_rtn['speed_km'] ?></p>
                                        <?php
                                        } else {
                                        ?>
                                            <p class="fs_12 fw_400 text_dynamic text_light_gray line_h1_2 mt-1 mr-2"><?= $arr_rtn['speed_info'] ?> -</p>
                                            <p class="fs_12 fw_400 text_dynamic text_light_gray line_h1_2 mt-1"><?= $arr_rtn['speed_km'] ?></p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="bg-white d-flex align-items-start justify-content-between border rounded-lg py_16 px-2">
                            <div class="text-center w_50 pr-2 border-right">
                                <p class="fs_14 fw_500">현재위치</p>
                                <p class="fs_12 fc_gray_600 fw_500 line1_text text_dynamic mt_08"><?= $arr_rtn['my_add'] ?></p>
                                <div class="fc_primary rounded-pill bg_secondary mx-auto text-center px_08 py_03 text_dynamic w_fit h_fit_im d-flex flex-wrap align-items-center justify-content-center mt_08 re_time_txt">
                                    <p class="fs_12 pr_03 d-none">도착함 ·</p>
                                    <p class="fs_12 d-none">오후 03:20</p>
                                </div>
                            </div>
                            <div class="text-center w_50 pl-2">
                                <p class="fs_14 fw_500 text_gray ">다음 위치</p>
                                <p class="fs_12 text_light_gray line1_text text_dynamic mt_08"><?= $arr_rtn['my_add_next'] ?></p>
                                <div class="text_gray rounded-pill bg_efefef mx-auto text-center px_08 py_03 text_dynamic w_fit h_fit_im d-flex flex-wrap align-items-center justify-content-center mt_08 re_time_txt">
                                    <p class="fs_11 pr_03"><?= $arr_rtn['my_time_next'] ?></p>
                                    <p class="fs_11"><?= $arr_rtn['my_arrive_next'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    <?php
    } else {
    ?>
        <!-- 내용 없을 때 박스 -->
        <div class="mt-4 pb-4">
            <div class="border rounded-lg px_16 py_16 none_box mb_25">
                <div class="text-center">
                    <p class="fs_14 text_gray text_dynamic">그룹을 생성해주세요!</p>
                    <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="location.href='./group_create'">그룹 생성 하러가기<i class="xi-angle-right-min ml_19"></i></button>
                </div>
            </div>
        </div>
<?php
    }
} else if ($_POST['act'] == "get_mt_file1") {
    if ($_SESSION['_mt_idx'] == '') {
        p_alert('로그인이 필요합니다.', './login', '');
    }
    if ($_POST['mt_idx'] == '') {
        p_alert('잘못된 접근입니다. mt_idx');
    }

    $mt_info = get_member_t_info($_POST['mt_idx']);
    $mt_file1_url = get_image_url($mt_info['mt_file1']);

    echo $mt_file1_url;
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
