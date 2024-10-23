<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>

<div class="container sub_pg psn_wrap">
    <div class="mt-4">
        <div class="">
            <p class="fs_24 fw_700 wh_pre line_h1_3"><?= $translations['txt_smap_permission_guide'] ?></p>
            <p class="fs_12 fc_gray_600 mt-3 line_h1_2 text_dynamic line_h1_3"><?= $translations['txt_smap_permission_request'] ?></p>
        </div>
        <div class="mt_45 psn_textwrap">
            <ul class="">
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3"><?= $translations['txt_location_access'] ?></p>
                    <p class="text_gray text_dynamic line_h1_3"><?= $translations['txt_location_access_desc'] ?></p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3"><?= $translations['txt_camera_access'] ?></p>
                    <p class="text_gray text_dynamic line_h1_3"><?= $translations['txt_camera_access_desc'] ?></p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3"><?= $translations['txt_storage_access'] ?></p>
                    <p class="text_gray text_dynamic line_h1_3"><?= $translations['txt_storage_access_desc'] ?></p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3"><?= $translations['txt_activity_recognition'] ?></p>
                    <p class="text_gray text_dynamic line_h1_3"><?= $translations['txt_activity_recognition_desc'] ?></p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3"><?= $translations['txt_disable_power_saving'] ?></p>
                    <p class="text_gray text_dynamic line_h1_3"><?= $translations['txt_disable_power_saving_desc'] ?></p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3"><?= $translations['txt_allow_data_usage'] ?></p>
                    <p class="text_gray text_dynamic line_h1_3"><?= $translations['txt_allow_data_usage_desc'] ?></p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3"><?= $translations['txt_exclude_battery_saving'] ?></p>
                    <p class="text_gray text_dynamic line_h1_3"><?= $translations['txt_exclude_battery_saving_desc'] ?></p>
                </li>
            </ul>
        </div>
    </div>
    <div class="b_botton">
        <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='./onbding'"><?= $translations['txt_confirm'] ?></button>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>