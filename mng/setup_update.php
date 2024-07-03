<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "update") {
    unset($arr_query);
    $arr_query = array(
        "st_agree1" => $_POST['st_agree1'],
        "st_agree2" => $_POST['st_agree2'],
        "st_agree3" => $_POST['st_agree3'],
        "st_agree4" => $_POST['st_agree4'],
        "st_agree5" => $_POST['st_agree5'],
        "st_agree6" => $_POST['st_agree6'],
        "st_company_boss" => $_POST['st_company_boss'],
        "st_company_num1" => $_POST['st_company_num1'],
        "st_company_num2" => $_POST['st_company_num2'],
        "st_company_name" => $_POST['st_company_name'],
        "st_company_zip" => $_POST['st_company_zip'],
        "st_company_add1" => $_POST['st_company_add1'],
        "st_company_add2" => $_POST['st_company_add2'],
        "st_privacy_admin" => $_POST['st_privacy_admin'],
        "st_customer_tel" => $_POST['st_customer_tel'],
        "st_customer_email" => $_POST['st_customer_email'],
        "st_customer_time" => $_POST['st_customer_time'],
        "st_app_version_aos" => $_POST['st_app_version_aos'],
        "st_app_version_ios" => $_POST['st_app_version_ios'],
        "st_app_version_aos_chk" => $_POST['st_app_version_aos_chk'],
        "st_app_version_ios_chk" => $_POST['st_app_version_ios_chk'],
   );

    $DB->where('st_idx', '1');

    $DB->update('setup_t', $arr_query);

    p_alert("수정되었습니다.");
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
