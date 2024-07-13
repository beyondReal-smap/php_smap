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
            mt_idx = '" . $mt_idx . "'
            AND mlt_accuacy < " . $slt_mlt_accuacy . "
            AND mlt_gps_time BETWEEN '" . $event_start_date . " 00:00:00' AND '" . $event_start_date . " 23:59:59'
        ORDER BY
            mlt_gps_time
    ),
    labeled_data AS (
        SELECT
            *,
            CASE
                WHEN mlt_speed >= 1 AND mt_health_work > prev_mt_health_work THEN 'move'
                WHEN mlt_speed >= 1 AND mlt_accuacy < 35 THEN 'move'
                ELSE 'stay'
            END AS label,
            CASE 
                WHEN LAG(mlt_lat) OVER (ORDER BY mlt_gps_time) IS NULL THEN 0
                ELSE 6371 * ACOS(COS(RADIANS(LAG(mlt_lat) OVER (ORDER BY mlt_gps_time))) * COS(RADIANS(mlt_lat)) * COS(RADIANS(mlt_long) - RADIANS(LAG(mlt_long) OVER (ORDER BY mlt_gps_time))) + SIN(RADIANS(LAG(mlt_lat) OVER (ORDER BY mlt_gps_time))) * SIN(RADIANS(mlt_lat)))
            END AS distance
        FROM
            initial_data
    ),
    window_function_data AS (
        SELECT
            *,
            SUM(CASE WHEN mlt_speed >= 1 AND mt_health_work > prev_mt_health_work THEN 1 WHEN mlt_speed >= 1 AND mlt_accuacy < 35 THEN 1 ELSE 0 END) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 100 PRECEDING AND CURRENT ROW) AS mp_cnt,
            AVG(mlt_speed) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 10 PRECEDING AND CURRENT ROW) AS avg_speed_last_10,
            AVG(mlt_speed) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS avg_speed_last_5,
            AVG(mlt_speed) OVER (ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 5 FOLLOWING) AS avg_speed_next_5,
            AVG(mlt_speed) OVER (ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 10 FOLLOWING) AS avg_speed_next_10
        FROM
            labeled_data
    ),
    final_data AS (
        SELECT
            *,
            AVG(mp_cnt) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 10 PRECEDING AND CURRENT ROW) AS avg_ap_cnt_last_10,
            AVG(mp_cnt) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 5 PRECEDING AND CURRENT ROW) AS avg_ap_cnt_last_5,
            AVG(mp_cnt) OVER (ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 5 FOLLOWING) AS avg_ap_cnt_next_5,
            AVG(mp_cnt) OVER (ORDER BY mlt_gps_time ROWS BETWEEN CURRENT ROW AND 10 FOLLOWING) AS avg_ap_cnt_next_10
        FROM
            window_function_data
    ),
    move_stay_logic AS (
        SELECT
            *,
            CASE
                WHEN mp_cnt > avg_ap_cnt_last_10 - 1 AND avg_speed_last_10 > 1 THEN 'move'
                WHEN mp_cnt > avg_ap_cnt_last_5 - 1 AND avg_speed_last_5 > 1 THEN 'move'
                WHEN mp_cnt > 0 AND mp_cnt < avg_ap_cnt_next_10 AND avg_speed_next_10 > 0.7 THEN 'move'
                WHEN mp_cnt > 0 AND mp_cnt < avg_ap_cnt_next_5 AND avg_speed_next_5 > 0.7 THEN 'move'
                WHEN mp_cnt >= avg_ap_cnt_next_5 AND avg_speed_next_5 < 1 THEN 'stay'
                WHEN mp_cnt >= avg_ap_cnt_next_10 AND avg_speed_next_10 < 1 THEN 'stay'
                ELSE label
            END AS move_stay
        FROM
            final_data
    ),
    labeled_data_with_lag AS (
        SELECT
            *,
            LAG(move_stay) OVER (ORDER BY mlt_gps_time) AS prev_label
        FROM
            move_stay_logic
    ),
    labeled_data_with_grp AS (
        SELECT
            *,
            SUM(CASE WHEN move_stay <> prev_label THEN 1 ELSE 0 END) OVER (ORDER BY mlt_gps_time) AS grp
        FROM
            labeled_data_with_lag
    ),
    labeled_data_with_grp_status AS (
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
    filtered_data AS (
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
            MIN(CASE WHEN grp_status = 'S' THEN mlt_gps_time END) AS start_time,
            MAX(CASE WHEN grp_status = 'E' THEN mlt_gps_time END) AS end_time,
            TIMESTAMPDIFF(SECOND, MIN(CASE WHEN grp_status = 'S' THEN mlt_gps_time END), MAX(CASE WHEN grp_status = 'E' THEN mlt_gps_time END)) / 60 AS duration,
            SUM(CASE WHEN prev_lat IS NOT NULL AND prev_long IS NOT NULL THEN 6371 * ACOS(COS(RADIANS(mlt_lat)) * COS(RADIANS(prev_lat)) * COS(RADIANS(prev_long) - RADIANS(mlt_long)) + SIN(RADIANS(mlt_lat)) * SIN(RADIANS(prev_lat))) ELSE 0 END) AS distance,
            MAX(CASE WHEN grp_status = 'S' THEN mlt_lat END) AS start_lat,
            MAX(CASE WHEN grp_status = 'S' THEN mlt_long END) AS start_long
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
    WHERE
        duration >= 5
    ORDER BY
        start_time
    ";
}

// 전체 이동로그 구하기
function get_move_query($mt_idx, $slt_mlt_accuacy, $event_start_date) {
    return "
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
                    mt_idx = '" . $mt_idx . "'
                    AND mlt_accuacy  < " . $slt_mlt_accuacy . "
                    AND mlt_gps_time BETWEEN '" . $event_start_date . " 00:00:00' AND '" . $event_start_date . " 23:59:59'
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
        ),
        error_detection AS (
            SELECT
                *,
                LAG(mlt_speed) OVER (ORDER BY mlt_gps_time) AS prev_speed,
                LAG(mlt_gps_time) OVER (ORDER BY mlt_gps_time) AS prev_time,
                CASE
                    WHEN LAG(mlt_speed) OVER (ORDER BY mlt_gps_time) IS NOT NULL THEN
                        ABS(mlt_speed - LAG(mlt_speed) OVER (ORDER BY mlt_gps_time)) / 
                        TIMESTAMPDIFF(SECOND, LAG(mlt_gps_time) OVER (ORDER BY mlt_gps_time), mlt_gps_time)
                    ELSE NULL
                END AS speed_change_rate,
                CASE
                    WHEN LAG(distance) OVER (ORDER BY mlt_gps_time) IS NOT NULL THEN
                        ABS(distance - LAG(distance) OVER (ORDER BY mlt_gps_time)) / 
                        TIMESTAMPDIFF(SECOND, LAG(mlt_gps_time) OVER (ORDER BY mlt_gps_time), mlt_gps_time)
                    ELSE NULL
                END AS distance_change_rate
            FROM filtered_data
        ),
        reliable_movement AS (
            SELECT
                *,
                CASE
                    WHEN speed_change_rate > 22.22 OR  -- 초속 22.22m/s (약 80km/h)
                        (distance_change_rate > 0.0222 AND mlt_speed > 22.22) OR  -- 초속 22.22m/s (약 80km/h) 이상일 때 거리 변화 체크
                        mlt_speed > 33.33  -- 초속 33.33m/s (약 120km/h) 이상일 때
                    THEN FALSE
                    ELSE TRUE
                END AS is_reliable
            FROM error_detection
        ),
        acceleration_data AS (
            SELECT 
                *,
                (mlt_speed - LAG(mlt_speed) OVER (ORDER BY mlt_gps_time)) / 
                TIMESTAMPDIFF(SECOND, LAG(mlt_gps_time) OVER (ORDER BY mlt_gps_time), mlt_gps_time) AS acceleration
            FROM reliable_movement
            WHERE is_reliable = TRUE
        ),
        direction_data AS (
            SELECT 
                *,
                DEGREES(ATAN2(
                    mlt_lat - LAG(mlt_lat, 2) OVER (ORDER BY mlt_gps_time),
                    mlt_long - LAG(mlt_long, 2) OVER (ORDER BY mlt_gps_time)
                )) AS direction_change
            FROM acceleration_data
            WHERE ABS(acceleration) < 10 -- 10 m/s^2를 초과하는 가속도는 제외
        ),
        speed_consistency AS (
            SELECT 
                *,
                ABS(mlt_speed - AVG(mlt_speed) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 2 PRECEDING AND 2 FOLLOWING)) AS speed_deviation
            FROM direction_data
            WHERE ABS(direction_change) < 90 -- 90도 이상의 급격한 방향 변화 제외
        ),
        moving_average AS (
            SELECT 
                *,
                AVG(mlt_lat) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 2 PRECEDING AND 2 FOLLOWING) AS avg_lat,
                AVG(mlt_long) OVER (ORDER BY mlt_gps_time ROWS BETWEEN 2 PRECEDING AND 2 FOLLOWING) AS avg_long
            FROM speed_consistency
            WHERE speed_deviation < 5 -- 5 m/s 이상의 속도 편차를 보이는 데이터 제외
        )
        SELECT
            mlt_gps_time, 
            mlt_lat, 
            mlt_long, 
            mlt_speed, 
            mt_health_work, 
            mlt_accuacy,
            move_stay as label,
            distance
        FROM moving_average
        WHERE (move_stay = 'move' AND distance > 0.1) OR prev_lat IS NULL
        ORDER BY mlt_gps_time;
        ";    
}
?>
