<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

$_POST['sgdt_mt_idx'] = '242';
// $_POST['sgdt_mt_idx'] = '249';
$_POST['event_start_date'] = '2024-05-07';

// 오늘자 이동로그 구하기
$current_date = date('Y-m-d H:i:s');
//이동로그 마지막 구하기
$DB->where('mt_idx', $_POST['sgdt_mt_idx']);
$DB->where(" ( mlt_gps_time >= '" . $_POST['event_start_date'] . " 00:00:00' and mlt_gps_time <= '" . $_POST['event_start_date'] . " 23:59:59' )");
// $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
// $DB->where("mlt_speed >= " . $slt_mlt_speed);
$DB->orderby('mlt_gps_time', 'desc');
$mt_last_location = $DB->getone('member_location_log_t');

unset($list_mlt);
$DB->where('mt_idx', $_POST['sgdt_mt_idx']);
$DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
$DB->where("mlt_speed >= " . $slt_mlt_speed);
$DB->where(" ( mlt_gps_time >= '" . $_POST['event_start_date'] . " 00:00:00' and mlt_gps_time <= '" . $_POST['event_start_date'] . " 23:59:59' )");
$DB->orderBy("mlt_gps_time", "asc");
$list_mlt = $DB->get('member_location_log_t');
// 오늘 자 이동로그가 있을 때
if ($list_mlt) {
    $log_count = 1;
    $stay_count = 1;
    $list_count = count($list_mlt);
    foreach ($list_mlt as $row_mlt) {
        // 이동로그의 위도와 경도
        $latitude_log = $row_mlt['mlt_lat'];
        $longitude_log = $row_mlt['mlt_long'];

        // 위도와 경도 오차범위 계산에 사용될 값
        $latitude_tolerance = 0.00045; // 약 50m의 위도 오차범위 (1도의 위도 차이는 약 111km, 100m는 약 0.0009도 50m는 약 0.00045도)
        $longitude_tolerance = 0.00045; // 약 50m의 경도 오차범위

        if ($log_count >= $list_count) { // 움직임의 마지막일 경우 해당일자의 마지막시간까지 확인하기
            // $list_mlt[$log_count]['mlt_gps_time'] =  $_POST['event_start_date'] . " 23:59:59";
            $list_mlt[$log_count]['mlt_gps_time'] =  $mt_last_location['mlt_gps_time'];
            $last_location_time = $row_mlt['mlt_gps_time'];
            $last_location_lat = $row_mlt['mlt_lat'];
            $last_location_long = $row_mlt['mlt_long'];
        }
        

        // 속도가 일정 속도보다 낮고 위도경도 오차범위 내에 있을 경우 구한 후 체류시간 구하기
        unset($list_stay);
        $DB->where('mt_idx', $_POST['sgdt_mt_idx']);
        $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
        $DB->where("mlt_speed <= " . $slt_mlt_speed);
        $DB->where("mlt_lat BETWEEN " . ($latitude_log - $latitude_tolerance) . " AND " . ($latitude_log + $latitude_tolerance));
        $DB->where("mlt_long BETWEEN " . ($longitude_log - $longitude_tolerance) . " AND " . ($longitude_log + $longitude_tolerance));
        $DB->where(" ( mlt_gps_time > '" . $row_mlt['mlt_gps_time'] . "' and mlt_gps_time <= '" . $list_mlt[$log_count]['mlt_gps_time'] . "' )");
        $DB->orderBy("mlt_gps_time", "asc");
        $list_stay = $DB->get('member_location_log_t');

        if ($list_stay) {
            // 체류시간을 계산할 변수 초기화
            $stay_time = 0;

            // 체류시간 계산을 위해 첫 번째 및 마지막 이동로그의 mlt_gps_time 값을 저장
            $first_log_date = strtotime($list_stay[0]['mlt_gps_time']);
            $last_log_date = strtotime(end($list_stay)['mlt_gps_time']);

            // 평균값 초기화
            $avg_lat = 0;
            $avg_long = 0;
            $location_count = 0;

            // 결과 레코드 반복
            foreach ($list_stay as $row) {
                // 평균값 누적
                $avg_lat += $row['mlt_lat'];
                $avg_long += $row['mlt_long'];
                $location_count++;
            }

            // 평균 계산
            if ($location_count > 0) {
                $avg_lat /= $location_count;
                $avg_long /= $location_count;
            }
            // 결과 레코드 반복
            foreach ($list_stay as $row) {
                unset($arr_query);
                $arr_query = array(
                    "stay_lat" => $avg_lat,
                    "stay_long" => $avg_long,
                );
                $DB->where('mlt_idx', $row['mlt_idx']);
                $DB->update('member_location_log_t', $arr_query);
            }

            // 체류시간 계산 (단위: 초)
            $stay_time_seconds = $last_log_date - $first_log_date;
            // 체류시간 5분이상일 경우 마커 표시
            if ($stay_time_seconds >= 300) {
                $row_mlt['mlt_lat'] = $avg_lat;
                $row_mlt['mlt_long'] = $avg_long;
                // 시간과 분으로 변환
                $hours = floor($stay_time_seconds / 3600);
                $minutes = floor(($stay_time_seconds % 3600) / 60);

                // 형식에 맞게 문자열로 표현
                $stay_time_formatted = "";
                if ($hours > 0) {
                    $stay_time_formatted .= $hours . "시간 ";
                }
                $stay_time_formatted .= $minutes . "분 체류";
                $addr = get_search_coordinate2address($avg_lat, $avg_long);
                $address =  $addr['area1'] . ' ' . $addr['area2'] . ' ' . $addr['area3'];
                $content = '<div class="point_wrap point2"  data-rangeindex="' . $log_count . '">
                                    <button type="button" class="btn log_point point_stay">
                                        <span class="point_inner">
                                            <span class="point_txt">' . $stay_count . '</span>
                                        </span>
                                    </button>
                                    <div class="infobox rounded-sm bg-white px_08 py_08">
                                        <p class="fs_12 fw_700 text_dynamic">' . datetype($list_stay[0]['mlt_gps_time'], 7) . ' ~ ' . datetype(end($list_stay)['mlt_gps_time'], 7) . '</p>
                                        <p class="fs_10 fw_500 text_dynamic text-primary line_h1_2 mt-2">' . $stay_time_formatted . '</p>
                                        <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">' . $address . '</p>
                                    </div>
                                </div>';
                $stay_count++;
            } else {
                $content = '<div class="point_wrap point2 d-none log_marker"  data-rangeindex="' . $log_count . '">
            <div class="infobox infobox_2 rounded-sm bg-white px_08 py_08">
                <p class="fs_12 fw_700 text_dynamic">' . datetype($list_stay[0]['mlt_gps_time'], 7) . '</p>
            </div>
        </div>';
            }
        } else {
            $content = '<div class="point_wrap point2 d-none log_marker"  data-rangeindex="' . $log_count . '">
            <div class="infobox infobox_2 rounded-sm bg-white px_08 py_08">
                <p class="fs_12 fw_700 text_dynamic">' . datetype($row_mlt['mlt_gps_time'], 7) . '</p>
            </div>
        </div>';
        }

        $arr_data['logmarkerLat_' . $log_count] = $row_mlt['mlt_lat'];
        $arr_data['logmarkerLong_' . $log_count] = $row_mlt['mlt_long'];
        $arr_data['logmarkerContent_' . $log_count] = $content;
        $log_count++;
    }

    // 마지막 위치 채류시간 구하기
    $latitude_log = $mt_last_location['mlt_lat'];
    $longitude_log = $mt_last_location['mlt_long'];

    $latitude_tolerance = 0.00045; // 약 50m의 위도 오차범위 (1도의 위도 차이는 약 111km, 100m는 약 0.0009도 50m는 약 0.00045도)
    $longitude_tolerance = 0.00045; // 약 50m의 경도 오차범위

    $chk_lat = $latitude_log - $last_location_lat;
    $chk_long = $longitude_log - $last_location_long;
    if (abs($chk_lat) > $latitude_tolerance && abs($chk_long) > $longitude_tolerance) {

        // 속도가 일정 속도보다 낮고 위도경도 오차범위 내에 있을 경우 구한 후 체류시간 구하기
        unset($list_stay);
        $DB->where('mt_idx', $_POST['sgdt_mt_idx']);
        $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
        $DB->where("mlt_speed <= " . $slt_mlt_speed);
        $DB->where("mlt_lat BETWEEN " . ($latitude_log - $latitude_tolerance) . " AND " . ($latitude_log + $latitude_tolerance));
        $DB->where("mlt_long BETWEEN " . ($longitude_log - $longitude_tolerance) . " AND " . ($longitude_log + $longitude_tolerance));
        $DB->where("( mlt_gps_time > '" . $last_location_time . "' and mlt_gps_time <= '" . $mt_last_location['mlt_gps_time'] . "' )");
        $DB->orderBy("mlt_gps_time", "asc");
        $list_stay = $DB->get('member_location_log_t');

        if ($list_stay) {
            // 체류시간을 계산할 변수 초기화
            $stay_time = 0;

            // 체류시간 계산을 위해 첫 번째 및 마지막 이동로그의 mlt_gps_time 값을 저장
            $first_log_date = strtotime($list_stay[0]['mlt_gps_time']);
            $last_log_date = strtotime(end($list_stay)['mlt_gps_time']);

            // 체류시간 계산 (단위: 초)
            $stay_time_seconds = $last_log_date - $first_log_date;
            // 체류시간 5분이상일 경우 마커 표시
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
                $addr = get_search_coordinate2address($mt_last_location['mlt_lat'], $mt_last_location['mlt_long']);
                $address =  $addr['area1'] . ' ' . $addr['area2'] . ' ' . $addr['area3'];
                $content = '<div class="point_wrap point2"  data-rangeindex="' . $log_count . '">
                                        <button type="button" class="btn log_point point_stay">
                                            <span class="point_inner">
                                                <span class="point_txt">' . $stay_count . '</span>
                                            </span>
                                        </button>
                                        <div class="infobox rounded-sm bg-white px_08 py_08">
                                            <p class="fs_12 fw_700 text_dynamic">' . datetype($list_stay[0]['mlt_gps_time'], 7) . ' ~ ' . datetype(end($list_stay)['mlt_gps_time'], 7) . '</p>
                                            <p class="fs_10 fw_500 text_dynamic text-primary line_h1_2 mt-2">' . $stay_time_formatted . '</p>
                                            <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">' . $address . '</p>
                                        </div>
                                    </div>';
                $stay_count++;
            } else {
                $content = '<div class="point_wrap point2 d-none log_marker"  data-rangeindex="' . $log_count . '">
                <div class="infobox infobox_2 rounded-sm bg-white px_08 py_08">
                    <p class="fs_12 fw_700 text_dynamic">' . datetype($list_stay[0]['mlt_gps_time'], 7) . '</p>
                </div>
            </div>';
            }
        } else {
            $content = '<div class="point_wrap point2 d-none log_marker"  data-rangeindex="' . $log_count . '">
                <div class="infobox infobox_2 rounded-sm bg-white px_08 py_08">
                    <p class="fs_12 fw_700 text_dynamic">' . datetype($mt_last_location['mlt_gps_time'], 7) . '</p>
                </div>
            </div>';
        }

        $arr_data['logmarkerLat_' . $log_count] = $mt_last_location['mlt_lat'];
        $arr_data['logmarkerLong_' . $log_count] = $mt_last_location['mlt_long'];
        $arr_data['logmarkerContent_' . $log_count] = $content;
        $log_count++;
    }
    // JSON으로 변환하여 출력
    $arr_data['log_chk'] = 'Y';
    $arr_data['log_count'] = $log_count - 1;
    // $arr_data['log_count'] = $log_count;

} else {
    // JSON으로 변환하여 출력
    $arr_data['log_chk'] = 'N';
    $arr_data['log_count'] = 0;
}

echo json_encode($arr_data);
?>
<?php
?>