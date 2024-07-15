<?php
function get_stay_query($mt_idx, $slt_mlt_accuacy, $event_start_date) {
    return "
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
}

// 전체 이동로그 구하기
function get_move_query($mt_idx, $slt_mlt_accuacy, $event_start_date) {
    return "
    WITH filtered_data AS (
        SELECT *,
            LAG(mlt_lat, 1) OVER (ORDER BY mlt_gps_time) AS prev_lat,
            LAG(mlt_long, 1) OVER (ORDER BY mlt_gps_time) AS prev_lon,
            LAG(mlt_gps_time, 1) OVER (ORDER BY mlt_gps_time) AS prev_time,
            LAG(mlt_speed, 1) OVER (ORDER BY mlt_gps_time) AS prev_speed,
            LEAD(mlt_lat, 1) OVER (ORDER BY mlt_gps_time) AS next_lat,
            LEAD(mlt_long, 1) OVER (ORDER BY mlt_gps_time) AS next_long
        FROM member_location_log_t
        WHERE mt_idx = '" . $_POST['sgdt_mt_idx'] . "'
        AND mlt_accuacy < 20  -- 정확도 조건을 더 엄격하게 설정
        AND (mlt_lat BETWEEN 33 AND 38) AND (mlt_long BETWEEN 124 AND 132)  -- 한국의 대략적인 위경도 범위
        AND mlt_gps_time BETWEEN '" . $_POST['event_start_date'] . " 00:00:00' AND '" . $_POST['event_start_date'] . " 23:59:59'
    ),
    calculated_data AS (
        SELECT *,
            6371 * 2 * ASIN(SQRT(
                POWER(SIN((mlt_lat - prev_lat) * pi()/180 / 2), 2) +
                COS(prev_lat * pi()/180) * COS(mlt_lat * pi()/180) *
                POWER(SIN((mlt_long - prev_lon) * pi()/180 / 2), 2)
            )) * 1000 AS distance_from_prev,
            6371 * 2 * ASIN(SQRT(
                POWER(SIN((next_lat - mlt_lat) * pi()/180 / 2), 2) +
                COS(mlt_lat * pi()/180) * COS(next_lat * pi()/180) *
                POWER(SIN((next_long - mlt_long) * pi()/180 / 2), 2)
            )) * 1000 AS distance_to_next,
            6371 * 2 * ASIN(SQRT(
                POWER(SIN((mlt_lat - prev_lat) * pi()/180 / 2), 2) +
                COS(prev_lat * pi()/180) * COS(mlt_lat * pi()/180) *
                POWER(SIN((mlt_long - prev_lon) * pi()/180 / 2), 2)
            )) * 1000 / NULLIF(TIMESTAMPDIFF(SECOND, prev_time, mlt_gps_time), 0) AS calculated_speed,
            (mlt_speed - prev_speed) / NULLIF(TIMESTAMPDIFF(SECOND, prev_time, mlt_gps_time), 0) AS acceleration,
            DEGREES(ATAN2(
                SIN(mlt_long - prev_lon) * COS(mlt_lat),
                COS(prev_lat) * SIN(mlt_lat) - SIN(prev_lat) * COS(mlt_lat) * COS(mlt_long - prev_lon)
            )) AS bearing_change
        FROM filtered_data
    ),
    stationary_check AS (
        SELECT *,
            SUM(CASE WHEN calculated_speed < 1.4 THEN 1 ELSE 0 END) 
                OVER (ORDER BY mlt_gps_time ROWS BETWEEN 2 PRECEDING AND 2 FOLLOWING) AS slow_count,
            AVG(distance_from_prev) 
                OVER (ORDER BY mlt_gps_time ROWS BETWEEN 2 PRECEDING AND 2 FOLLOWING) AS avg_distance
        FROM calculated_data
    )
    SELECT mlt_gps_time AS start_time
        ,mlt_gps_time AS end_time
        ,mlt_lat AS start_lat
        ,mlt_long AS start_long
        ,row_num
    FROM (
        SELECT *, 
            ROW_NUMBER() OVER (ORDER BY mlt_gps_time ASC) AS row_num
        FROM stationary_check
        WHERE mlt_speed < 33.3333  -- 120 km/h in m/s (최대 속도 상향 조정)
        AND calculated_speed < 33.3333  -- 120 km/h in m/s
        AND ABS(acceleration) < 5  -- 가속도 제한 더 엄격하게 (약 0.5G)
        AND (prev_lat IS NULL OR ABS(bearing_change) < 120)  -- 120도 이상의 급격한 방향 변화 제외
        AND (distance_from_prev < 1000 OR distance_to_next < 1000)  -- 앞뒤 포인트와의 거리가 1km 이상인 경우 제외
    ) AS numbered_data
    WHERE row_num % 5 = 1  -- 데이터 포인트 간격 조정
    ORDER BY mlt_gps_time ASC;
        "   ; 
}
?>
