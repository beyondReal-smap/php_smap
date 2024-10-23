<?php


$DB->where('mt_idx', $_SESSION['_mt_idx']);
$row = $DB->getone('member_t', 'mt_lang');
$userLangConfig = $row['mt_lang'] ? $row['mt_lang'] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$translationsConfig = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $userLangConfig . '.php';

$arr_mt_type = array(
    '1'   => $translationsConfig['txt_general'],
    '2'   => $translationsConfig['txt_kakao'],
    '3'   => $translationsConfig['txt_apple'],
    '4'   => $translationsConfig['txt_google'],
);

$arr_mt_type_option = '';
foreach ($arr_mt_type as $key => $val) {
    if ($val) {
        $arr_mt_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_status = array(
    '1'   => $translationsConfig['txt_normal'],
    '2'   => $translationsConfig['txt_suspended'],
);

$arr_mt_status_option = '';
foreach ($arr_mt_status as $key => $val) {
    if ($val) {
        $arr_mt_status_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_gender = array(
    '1'   => $translationsConfig['txt_male'],
    '2'   => $translationsConfig['txt_female'],
);

$arr_mt_gender_option = '';
foreach ($arr_mt_gender as $key => $val) {
    if ($val) {
    }
    $arr_mt_gender_option .= "<option value='".$key."' >".$val."</option>";
}

$arr_mt_level = array(
    '1'   => $translationsConfig['txt_withdrawal_status'],
    '2'   => $translationsConfig['txt_free'],
    '3'   => $translationsConfig['txt_dormant'],
    '4'   => $translationsConfig['txt_grace'],
    '5'   => $translationsConfig['txt_paid'],
);

$arr_mt_level_option = '';
foreach ($arr_mt_level as $key => $val) {
    if ($val) {
        $arr_mt_level_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_retire_chk = array(
    '1'   => $translationsConfig['txt_service_complex'],
    '2'   => $translationsConfig['txt_no_needed_features'],
    '3'   => $translationsConfig['txt_use_other_services'],
    '4'   => $translationsConfig['txt_other_reasons'],
);

$arr_mt_retire_chk_option = '';
foreach ($arr_mt_retire_chk as $key => $val) {
    if ($val) {
        $arr_mt_retire_chk_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_bt_type = array(
    '1'   => $translationsConfig['txt_home'],
    '2'   => $translationsConfig['txt_my_places'],
);

$arr_bt_type_option = '';
foreach ($arr_bt_type as $key => $val) {
    if ($val) {
        $arr_bt_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_bt_type_thumb = array(
    '1'   => array(1024,768),
    '2'   => array(1024,768),
);

$arr_date_group = array(3,5,7,15,30,60,90,120);

$arr_date_group_option = '';
foreach ($arr_date_group as $key => $val) {
    if ($val) {
        $arr_date_group_option .= "<option value='".$val."' >".$val . $translationsConfig['txt_day'] . "</option>"; 
    }
}

$arr_qt_status = array(
    '1'   => $translationsConfig['txt_ansr_pending'],
    '2'   => $translationsConfig['txt_ansr_complete'],
);

$arr_qt_status_option = '';
foreach ($arr_qt_status as $key => $val) {
    if ($val) {
        $arr_qt_status_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_weather_sky_icon = array(
    '1'   => 'weather_1.png', //구름 뒤에 있는 해
    '2'   => 'weather_2.png', //구름
    '3'   => 'weather_3.png', //안개
    '4'   => 'weather_4.png', //비
    '5'   => 'weather_5.png', //비와 눈
    '6'   => 'weather_6.png', //눈
    '7'   => 'weather_7.png', //천둥번개
    '8'   => 'weather_8.png', //맑음
);

$arr_mt_weather_sky = array(
    '1'   => $translationsConfig['txt_weather_partly_cloudy'], //구름 뒤에 있는 해
    '2'   => $translationsConfig['txt_weather_cloudy'], //구름
    '3'   => $translationsConfig['txt_weather_foggy'], //안개
    '4'   => $translationsConfig['txt_weather_rainy'], //비
    '5'   => $translationsConfig['txt_weather_rain_snow'], //비와 눈
    '6'   => $translationsConfig['txt_weather_snowy'], //눈
    '7'   => $translationsConfig['txt_weather_thunderstorms'], //천둥번개
    '8'   => $translationsConfig['txt_weather_clear'], //맑음
);

$arr_mt_weather_sky_option = '';
foreach ($arr_mt_weather_sky as $key => $val) {
    if ($val) {
        $arr_mt_weather_sky_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_alram = array(
    '1'   => $translationsConfig['txt_before_schedule'],
    '2'   => $translationsConfig['txt_10_minutes_before'],
    '3'   => $translationsConfig['txt_1_hour_before'],
    '4'   => $translationsConfig['txt_1_day_before'],
);

$arr_sst_alram_option = '';
foreach ($arr_sst_alram as $key => $val) {
    if ($val) {
        $arr_sst_alram_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_repeat_json = array(
    '1'   => $translationsConfig['txt_do_not_repeat'],
    '2'   => $translationsConfig['txt_every_day'],
    '3'   => $translationsConfig['txt_weekly'],
    '4'   => $translationsConfig['txt_monthly'],
    '5'   => $translationsConfig['txt_yearly'],
);

$arr_sst_repeat_json_option = '';
foreach ($arr_sst_repeat_json as $key => $val) {
    if ($val) {
        $arr_sst_repeat_json_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_repeat_json_r2 = array(
    '1'   => $translationsConfig['txt_monday'],
    '2'   => $translationsConfig['txt_tuesday'],
    '3'   => $translationsConfig['txt_wednesday'],
    '4'   => $translationsConfig['txt_thursday'],
    '5'   => $translationsConfig['txt_friday'],
    '6'   => $translationsConfig['txt_saturday'],
    '7'   => $translationsConfig['txt_sunday'],
);

$arr_sst_repeat_json_r2_option = '';
foreach ($arr_sst_repeat_json_r2 as $key => $val) {
    if ($val) {
        $arr_sst_repeat_json_r2_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_rlt_cate = array(
    '1'   => $translationsConfig['txt_exam_prep'],
    '2'   => $translationsConfig['txt_international'],
    '3'   => $translationsConfig['txt_performing_arts_uni'],
    '4'   => $translationsConfig['txt_arts_crafts_uni'],
    '5'   => $translationsConfig['txt_other_uni'],
);

$arr_rlt_cate_option = '';
foreach ($arr_rlt_cate as $key => $val) {
    if ($val) {
        $arr_rlt_cate_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_pft_send_type = array(
    '1'   => $translationsConfig['txt_all_members'],
    '2'   => $translationsConfig['txt_specific_user'],
    '3'   => $translationsConfig['txt_specific_group'],
);

$arr_pft_send_type_option = '';
foreach ($arr_pft_send_type as $key => $val) {
    if ($val) {
        $arr_pft_send_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_pft_status = array(
    '1'   => $translationsConfig['txt_pending'],
    '2'   => $translationsConfig['txt_sending'],
    '3'   => $translationsConfig['txt_sent'],
);

$arr_pft_status_option = '';
foreach ($arr_pft_status as $key => $val) {
    if ($val) {
        $arr_pft_status_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_agree = array(
    '1'   => $translationsConfig['txt_terms_of_service'],
    '2'   => $translationsConfig['txt_privacy_policy'],
    '3'   => $translationsConfig['txt_location_based_service_terms'],
    '4'   => $translationsConfig['txt_third_party_info'],
    '5'   => $translationsConfig['txt_marketing_info'],
);


$arr_plt_type = array(
    '1'   => $translationsConfig['txt_on_signup'],
    '2'   => $translationsConfig['txt_location_notif_alarm'],
    '3'   => $translationsConfig['txt_sent'],
);

$arr_plt_type_option = '';
foreach ($arr_plt_type as $key => $val) {
    if ($val) {
        $arr_plt_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}
$arr_grant = array(
    '1'   => $translationsConfig['txt_owner'],
    '2'   => $translationsConfig['txt_leader'],
    '3'   => $translationsConfig['txt_group_members'],
);

$arr_ot_pay_type = array(
    '1'   => $translationsConfig['txt_new_signup'],
    '2'   => $translationsConfig['txt_in_app_payment'],
    '3'   => $translationsConfig['txt_coupon'],
    '4'   => $translationsConfig['txt_referrer'],
);

$arr_ot_pay_type_option = '';
foreach ($arr_ot_pay_type as $key => $val) {
    if ($val) {
        $arr_ot_pay_type_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_days = array(
    '30'   => $translationsConfig['txt_1_month'],
    '60'   => $translationsConfig['txt_2_months'],
    '180'   => $translationsConfig['txt_6_months'],
    '365'   => $translationsConfig['txt_12_months'],
);

$arr_ct_days_option = '';
foreach ($arr_ct_days as $key => $val) {
    if ($val) {
        $arr_ct_days_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_show = array(
    'Y'   => $translationsConfig['txt_exposed'],
    'N'   => $translationsConfig['txt_hidden'],
);

$arr_ct_show_option = '';
foreach ($arr_ct_show as $key => $val) {
    if ($val) {
        $arr_ct_show_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_use = array(
    'Y'   => $translationsConfig['txt_used'],
    'N'   => $translationsConfig['txt_unused'],
);

$arr_ct_use_option = '';
foreach ($arr_ct_use as $key => $val) {
    if ($val) {
        $arr_ct_use_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_end = array(
    'Y'   => $translationsConfig['txt_expired'],
    'N'   => $translationsConfig['txt_unexpired'],
);

$arr_ct_end_option = '';
foreach ($arr_ct_end as $key => $val) {
    if ($val) {
        $arr_ct_end_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_slt_enter_chk = array(
    'Y'   => $translationsConfig['txt_alarm'],
    'N'   => $translationsConfig['txt_released'],
);

$arr_slt_enter_chk_option = '';
foreach ($arr_slt_enter_chk as $key => $val) {
    if ($val) {
        $arr_slt_enter_chk_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}
?>