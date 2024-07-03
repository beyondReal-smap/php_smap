<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if($_POST['mt_idx']) {
    $DB->where('mt_idx', $_POST['mt_idx']);
    $row = $DB->getone('member_t');

    if($row['mt_idx']) {
        $_last_idx = $row['mt_idx'];

        $img_width_t = '500';
        $img_height_t = '500';

        $c = 1;
        $v = date("YmdHis");
        $file_nm_t = 'mt_file'.$c;
        if($_FILES[$file_nm_t]) {
            $bt_file = $_FILES[$file_nm_t]['tmp_name'];
            $bt_file_name = $_FILES[$file_nm_t]['name'];
            $bt_file_size = $_FILES[$file_nm_t]['size'];
            $bt_file_type = $_FILES[$file_nm_t]['type'];

            $temp_img_txt = "mt_file".$c;
            $temp_img_on_txt = $temp_img_txt."_on";
            $temp_img_ori_txt = $temp_img_txt."_ori";
            $temp_img_size_txt = $temp_img_txt."_size";

            if (!empty($bt_file_name)) {
                $_POST[$temp_img_on_txt] = $temp_img_txt."_".$_last_idx . "_" . $c . "_" . $v . ".".get_file_ext($bt_file_name);
                upload_file($bt_file, $_POST[$temp_img_on_txt], $ct_img_dir."/");
                thumnail_width($ct_img_dir."/".$_POST[$temp_img_on_txt], $_POST[$temp_img_on_txt], $ct_img_dir."/", $img_width_t, $img_height_t);

                unset($arr_query_img);
                $arr_query_img[$temp_img_txt] = $_POST[$temp_img_on_txt];

                $DB->where('mt_idx', $_last_idx);

                $DB->update('member_t', $arr_query_img);
            }
        }

        echo result_data('true', '회원 이미지 등록 성공', '회원 이미지 등록에 성공했습니다.', null);
    } else {

        echo result_data('false', '회원 이미지 등록 실패', '회원 이미지 등록에 실패했습니다.', null);
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail_inc.php";
