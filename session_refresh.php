<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if($_POST['act'] == 'location_refresh') {
    if($_POST['lat'] && $_POST['long']){
        $_SESSION['_mt_lat'] = $_POST['lat'];
        $_SESSION['_mt_long'] = $_POST['long'];

        echo 'Y';
    }else{
        echo 'N';
    }
    exit;
}
