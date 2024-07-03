<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
$_POST['mt_idx'] = '216';
$_POST['event_start_date'] = '2024-04-25';
if ($_POST['mt_idx']) {
    $arr_sst_idx = get_schedule_array($_POST['mt_idx'], $_POST['event_start_date']);
    $cnt = count($arr_sst_idx);

    $rtn = get_gps_distance($_POST['mt_idx'], $_POST['event_start_date']);

/*     // WITH 절에 해당하는 하위 쿼리 실행
    $sub_query = "
    WITH RankedLogs AS (
    SELECT
        mt_idx,
        mlt_accuacy,
        mlt_speed,
        mlt_lat,
        mlt_long,
        mlt_gps_time,
        ROW_NUMBER() OVER (ORDER BY mlt_gps_time ASC) AS rn
    FROM
        member_location_log_t
    WHERE 1=1
        AND mt_idx = " . $mt_idx . "
        AND mlt_gps_time BETWEEN '". $sdate. " 00:00:00' AND '" . $sdate . " 23:59:59'
        AND mlt_speed > 0 
),
Diffs AS (
    SELECT
        L1.rn,
        L1.mlt_lat AS lat1,
        L1.mlt_long AS long1,
        L2.mlt_lat AS lat2,
        L2.mlt_long AS long2,
        L1.mlt_gps_time AS wdate1,
        L2.mlt_gps_time AS wdate2,
        L1.mlt_speed AS speed,
        L1.mlt_accuacy AS accuracy,
        TIMESTAMPDIFF(SECOND, L1.mlt_gps_time, L2.mlt_gps_time) AS time_diff_seconds
    FROM
        RankedLogs L1
    INNER JOIN RankedLogs L2 ON L1.rn = L2.rn - 1
)
SELECT SUM(CASE 
	          WHEN rslt.time_diff_seconds > ". $slt_mlt_accuacy." 
               AND ROUND((rslt.distance_meters / rslt.time_diff_seconds) * 3600 / 1000, 1) < ".$slt_mlt_speed." 
               AND rslt.time_diff_seconds > rslt.distance_meters / 0.6 
              THEN rslt.distance_meters / 0.6 
	       ELSE time_diff_seconds 
	    END) / 60 AS movig_minute   
      ,SUM(rslt.distance_meters) AS moving_meters
      
      
FROM 
(
	SELECT
	    rn,
	    lat1,
	    long1,
	    lat2,
	    long2,
	    wdate1,
	    wdate2,
	    speed,
	    accuracy,
	    time_diff_seconds,
	    ROUND(6371000 * ACOS(COS(RADIANS(lat2)) * COS(RADIANS(lat1)) * COS(RADIANS(long1) - RADIANS(long2)) + SIN(RADIANS(lat1)) * SIN(RADIANS(lat2))), 1) AS distance_meters
	FROM
	    Diffs
) rslt
WHERE ROUND((rslt.distance_meters / rslt.time_diff_seconds) * 3600 / 1000, 1) > 2
";

    // 하위 쿼리 실행
    $list = $DB->Query($sub_query);
    printr($list);

    // 결과 확인
    if ($list) {
        // 가져온 결과를 사용하여 필요한 작업 수행
        foreach ($list as $row) {
            $gps_time = $row['movig_minute'];
            $gsp_km = $row['moving_meters'];
            // 나머지 필드들에 대한 작업 수행
        }
    } else {
        // 하위 쿼리에서 결과가 없는 경우 처리할 내용
        echo "No data found.";
    }
    echo $gps_time;
    echo $gsp_km; */
  printr($rtn)  ;
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
<?
}
?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>