<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "delete_all") {
    if($_SESSION['_mt_idx'] == '') {
        p_alert($translations['txt_login_required'], './login', '');
    }

    unset($arr_query);
    $arr_query = array(
        "plt_show" => 'N',
    );

    $DB->where('plt_show', 'Y');
    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    $DB->update('push_log_t', $arr_query);

    echo "Y";
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
