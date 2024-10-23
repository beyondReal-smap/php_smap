<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_SESSION['_mt_idx']) {
    unset($arr_query);
    $arr_query = array(
        "mt_token_id" => '',
        "mt_lgdate" => $DB->now(),
    );

    $DB->where('mt_idx', $_SESSION['_mt_idx']);

    $DB->update('member_t', $arr_query);
}

// session_unset();
// session_destroy();
unset($_SESSION['_mt_idx']);
unset($_SESSION['_mt_id']);
unset($_SESSION['_mt_hp']);
unset($_SESSION['_mt_name']);
unset($_SESSION['_mt_nickname']);
unset($_SESSION['_mt_level']);
unset($_SESSION['_mt_file1']);
if (!$chk_mobile) {
    unset($_COOKIE);
}
?>
<script>
    var message = {
        "type": "memberLogout",
    };
    if (isAndroid()) {
        window.smapAndroid.memberLogout();
    } else if (isiOS()) {
        window.webkit.messageHandlers.smapIos.postMessage(message);
    }

    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    }
</script>
<?php

gotourl("./join_entry");

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
