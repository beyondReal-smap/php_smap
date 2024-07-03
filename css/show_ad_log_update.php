<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_POST['act'] == 'show_ad_path_log') {
    if (isset($_POST['mt_idx'])) {
        $sdate = date("Y-m-d");

        // Validate and sanitize log_count
        // $path_count = filter_var($_POST['path_count'], FILTER_VALIDATE_INT);
        // if ($path_count === false) {
        //     echo 'Invalid path count';
        //     exit;
        // }

        // 기존 로그 카운트 가져오기
        $DB->where('mt_idx', $_POST['mt_idx']);
        $DB->where('salt_date', $sdate);
        $existing_log = $DB->getOne('smap_ad_log_t');

        if ($existing_log) {
            // 기존 로그가 있으면 업데이트
            $new_count = $existing_log['path_count'] + 1;
        } else {
            // 기존 로그가 없으면 새로 생성
            $new_count = 1;
        }

        unset($arr_query);
        $arr_query = array(
            "path_count" => $new_count,
            "salt_udate" => $DB->now()
        );

        if ($existing_log) {
            $DB->where('mt_idx', $_POST['mt_idx']);
            $DB->where('salt_date', $sdate);
            $DB->update('smap_ad_log_t', $arr_query);
        } else {
            $arr_query['mt_idx'] = $_POST['mt_idx'];
            $arr_query['salt_date'] = $sdate;
            $DB->insert('smap_ad_log_t', $arr_query);
        }

        echo 'Y';
    } else {
        echo 'N';
    }
    exit;
} else if ($_POST['act'] == 'show_ad_log') {
    if (isset($_POST['mt_idx'])) {
        $sdate = date("Y-m-d");

        // Validate and sanitize log_count
        // $log_count = filter_var($_POST['log_count'], FILTER_VALIDATE_INT);
        // if ($log_count === false) {
        //     echo 'Invalid log count';
        //     exit;
        // }

        // 기존 로그 카운트 가져오기
        $DB->where('mt_idx', $_POST['mt_idx']);
        $DB->where('salt_date', $sdate);
        $existing_log = $DB->getOne('smap_ad_log_t');

        if ($existing_log) {
            // 기존 로그가 있으면 업데이트
            $new_count = $existing_log['log_count'] + 1;
        } else {
            // 기존 로그가 없으면 새로 생성
            $new_count = 1;
        }

        unset($arr_query);
        $arr_query = array(
            "log_count" => $new_count,
            "salt_udate" => $DB->now()
        );

        if ($existing_log) {
            $DB->where('mt_idx', $_POST['mt_idx']);
            $DB->where('salt_date', $sdate);
            $DB->update('smap_ad_log_t', $arr_query);
        } else {
            $arr_query['mt_idx'] = $_POST['mt_idx'];
            $arr_query['salt_date'] = $sdate;
            $DB->insert('smap_ad_log_t', $arr_query);
        }

        echo 'Y';
    } else {
        echo 'N';
    }
    exit;
}


