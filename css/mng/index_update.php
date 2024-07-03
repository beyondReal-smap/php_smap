<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "chart1") {
    $rtn_data = array();

    // $rtn_data['labels'] = array($_POST['sdate'], $_POST['edate']);
    // 시작 날짜와 종료 날짜 변수 설정
    $start_date = $_POST['sdate'];
    $end_date = $_POST['edate'];

    // 데이터베이스 연결 및 쿼리 실행
    $DB->join('member_t', 'dates.date = DATE(member_t.mt_wdate)','left');
    $DB->where('dates.date BETWEEN "' . $start_date . '" AND "' . $end_date . '"');
    $DB->groupBy('dates.date');
    $mem_join_list = $DB->get('
    (SELECT "'. $start_date .'" + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS DATE
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION  ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
    ) AS dates', null, 'dates.date AS date, COUNT(member_t.mt_wdate) AS member_count');
    // $lastQuery = $DB->getLastQuery();
    // echo $lastQuery;
    // exit;
    // 데이터베이스에서 가져온 값들을 사용하여 그래프 데이터를 설정
    $data1 = array();
    $labels = array();
    $join_total = 0;
    foreach ($mem_join_list as $row) {
        $data1[] = $row['member_count'];
        $labels[] = $row['date'];
        $join_total += $row['member_count'];
    }
    $rtn_data['data1'] = $data1;
    $rtn_data['labels'] = $labels;

    // 데이터베이스 연결 및 쿼리 실행
    $DB->join('member_t', 'dates.date = DATE(member_t.mt_rdate)', 'left');
    $DB->where('dates.date BETWEEN "' . $start_date . '" AND "' . $end_date . '"');
    $DB->groupBy('dates.date');
    $mem_retire_list = $DB->get('
    (SELECT "' . $start_date . '" + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS DATE
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION  ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
    ) AS dates', null, 'dates.date, COUNT(member_t.mt_rdate) AS member_count');
    // $lastQuery = $DB->getLastQuery();
    // echo $lastQuery;
    // exit;
    $data2 = array();
    $retire_total = 0;
    foreach ($mem_retire_list as $row) {
        $data2[] = $row['member_count'];
        $retire_total += $row['member_count'];
    }
    $rtn_data['data2'] = $data2;

    $rtn_data['data3'] = $join_total;
    $rtn_data['data4'] = $retire_total;

    echo json_encode($rtn_data);
} elseif ($_POST['act'] == "chart2") {
    $rtn_data = array();

    // 시작 날짜와 종료 날짜 변수 설정
    $start_date = $_POST['sdate'];
    $end_date = $_POST['edate'];

    // 데이터베이스 연결 및 쿼리 실행
    $DB->join('smap_group_detail_t', 'dates.date = DATE(smap_group_detail_t.sgdt_wdate) AND smap_group_detail_t.sgdt_discharge ="N" AND smap_group_detail_t.sgdt_exit ="N" AND smap_group_detail_t.sgdt_show ="Y"', 'left');
    $DB->where('dates.date BETWEEN "' . $start_date . '" AND "' . $end_date . '"');
    $DB->groupBy('dates.date');
    $group_list = $DB->get('
    (SELECT "' . $start_date . '" + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS DATE
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION  ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
    ) AS dates', null, 'dates.date AS date, COUNT(smap_group_detail_t.sgdt_wdate) AS member_count');
    // $lastQuery = $DB->getLastQuery();
    // echo $lastQuery;
    // exit;
    // 데이터베이스에서 가져온 값들을 사용하여 그래프 데이터를 설정
    $data1 = array();
    $labels = array();
    $group_total = 0;
    foreach ($group_list as $row) {
        $data1[] = $row['member_count'];
        $labels[] = $row['date'];
        $group_total += $row['member_count'];
    }
    $rtn_data['data1'] = $data1;
    $rtn_data['labels'] = $labels;

    // 데이터베이스 연결 및 쿼리 실행
    $DB->join('smap_schedule_t', 'dates.date = DATE(smap_schedule_t.sst_wdate) AND smap_schedule_t.sst_pidx is null AND smap_schedule_t.sst_show="Y"', 'left');
    $DB->where('dates.date BETWEEN "' . $start_date . '" AND "' . $end_date . '"');
    $DB->groupBy('dates.date');
    $schedule_list = $DB->get('
    (SELECT "' . $start_date . '" + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS DATE
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION  ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
    ) AS dates', null, 'dates.date, COUNT(smap_schedule_t.sst_wdate) AS member_count');
    // $lastQuery = $DB->getLastQuery();
    // echo $lastQuery;
    // exit;
    $data2 = array();
    $schedule_total = 0;
    foreach ($schedule_list as $row) {
        $data2[] = $row['member_count'];
        $schedule_total += $row['member_count'];
    }
    $rtn_data['data2'] = $data2;

    $rtn_data['data3'] = $group_total;
    $rtn_data['data4'] = $schedule_total;

    echo json_encode($rtn_data);
} elseif ($_POST['act'] == "chart3") {
    $rtn_data = array();

    // 시작 날짜와 종료 날짜 변수 설정
    $start_date = $_POST['sdate'];
    $end_date = $_POST['edate'];

    // 데이터베이스 연결 및 쿼리 실행
    $DB->join('qna_t', 'dates.date = DATE(qna_t.qt_qdate) AND qna_t.qt_show = "Y"', 'left');
    $DB->where('dates.date BETWEEN "' . $start_date . '" AND "' . $end_date . '"');
    $DB->groupBy('dates.date');
    $qna_list = $DB->get('
    (SELECT "' . $start_date . '" + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS DATE
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION  ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
    ) AS dates', null, 'dates.date AS date, COUNT(qna_t.qt_qdate) AS member_count');
    // $lastQuery = $DB->getLastQuery();
    // echo $lastQuery;
    // exit;
    // 데이터베이스에서 가져온 값들을 사용하여 그래프 데이터를 설정
    $data1 = array();
    $labels = array();
    $qna_total = 0;
    foreach ($qna_list as $row) {
        $data1[] = $row['member_count'];
        $labels[] = $row['date'];
        $qna_total += $row['member_count'];
    }
    $rtn_data['data1'] = $data1;
    $rtn_data['labels'] = $labels;

    $rtn_data['data3'] = $qna_total;

    echo json_encode($rtn_data);
} elseif ($_POST['act'] == "chart4") {
    $rtn_data = array();

    // 시작 날짜와 종료 날짜 변수 설정
    $start_date = $_POST['sdate'];
    $end_date = $_POST['edate'];

    // 데이터베이스 연결 및 쿼리 실행
    $DB->join('order_t', 'dates.date = DATE(order_t.ot_cdate) AND order_t.ot_pay_type ="2" AND order_t.ot_status ="2" AND order_t.ot_show ="Y"', 'left');
    $DB->where('dates.date BETWEEN "' . $start_date . '" AND "' . $end_date . '"');
    $DB->groupBy('dates.date');
    $order_list = $DB->get('
    (SELECT "' . $start_date . '" + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS DATE
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION  ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
    ) AS dates', null, 'dates.date AS date, COUNT(order_t.ot_cdate) AS member_count');
    // $lastQuery = $DB->getLastQuery();
    // echo $lastQuery;
    // exit;
    // 데이터베이스에서 가져온 값들을 사용하여 그래프 데이터를 설정
    $data1 = array();
    $labels = array();
    $order_total = 0;
    foreach ($order_list as $row) {
        $data1[] = $row['member_count'];
        $labels[] = $row['date'];
        $order_total += $row['member_count'];
    }
    $rtn_data['data1'] = $data1;
    $rtn_data['labels'] = $labels;

    // 데이터베이스 연결 및 쿼리 실행
    $DB->join('order_t', 'dates.date = DATE(order_t.ot_ccdate) AND order_t.ot_pay_type ="2" AND order_t.ot_status ="99" AND order_t.ot_show ="Y"', 'left');
    $DB->where('dates.date BETWEEN "' . $start_date . '" AND "' . $end_date . '"');
    $DB->groupBy('dates.date');
    $cancel_list = $DB->get('
    (SELECT "' . $start_date . '" + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS DATE
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION  ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
    ) AS dates', null, 'dates.date AS date, COUNT(order_t.ot_ccdate) AS member_count');
    // $lastQuery = $DB->getLastQuery();
    // echo $lastQuery;
    // exit;
    $data2 = array();
    $cancel_total = 0;
    foreach ($cancel_list as $row) {
        $data2[] = $row['member_count'];
        $cancel_total += $row['member_count'];
    }
    $rtn_data['data2'] = $data2;

    $rtn_data['data3'] = $order_total;
    $rtn_data['data4'] = $cancel_total;

    echo json_encode($rtn_data);
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
