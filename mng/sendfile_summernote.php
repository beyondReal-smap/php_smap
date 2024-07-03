<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act']=='upload') {
    $temp_img_txt = "file";
    $temp_img_on_txt = "file_on";
    $temp_img_temp_on_txt = "file_temp_on";
    $temp_img_del_txt = "file_del";

    if ($_FILES[$temp_img_txt]['name']) {
        $sm_file = $_FILES[$temp_img_txt]['tmp_name'];
        $sm_file_name = $_FILES[$temp_img_txt]['name'];
        $sm_file_size = $_FILES[$temp_img_txt]['size'];
        $sm_file_type = $_FILES[$temp_img_txt]['type'];

        if ($sm_file_name!="") {
            @unlink($ct_img_dir."/".$_POST[$temp_img_on_txt]);
            $_POST[$temp_img_on_txt] = "summernote_".$_POST['ctype']."_".$_POST['file_no']."_".date("YmdHis").".".get_file_ext($sm_file_name);
            upload_file($sm_file, $_POST[$temp_img_on_txt], $ct_img_dir."/");
        }
    } else {
        if ($_POST[$temp_img_del_txt]) {
            unlink($ct_img_dir_a."/".$_POST[$temp_img_del_txt]);
        }
    }

    $return_arr['url'] = get_file_url($_POST[$temp_img_on_txt]);

    $return = json_encode($return_arr);

    echo $return;
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";