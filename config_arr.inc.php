<?php

$arr_mt_type = array(
    '1'   => translate('일반', $userLang),
    '2'   => translate('카카오', $userLang),
    '3'   => translate('애플', $userLang),
    '4'   => translate('구글', $userLang),
);

$arr_mt_type_option = '';
foreach ($arr_mt_type as $key => $val) {
    if ($val) {
        $arr_mt_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_status = array(
    '1'   => translate('정상', $userLang),
    '2'   => translate('정지', $userLang),
);

$arr_mt_status_option = '';
foreach ($arr_mt_status as $key => $val) {
    if ($val) {
        $arr_mt_status_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_gender = array(
    '1'   => translate('남성', $userLang),
    '2'   => translate('여성', $userLang),
);

$arr_mt_gender_option = '';
foreach ($arr_mt_gender as $key => $val) {
    if ($val) {
    }
    $arr_mt_gender_option .= "<option value='".$key."' >".$val."</option>";
}

$arr_mt_level = array(
    '1'   => translate('탈퇴', $userLang),
    '2'   => translate('무료', $userLang),
    '3'   => translate('휴면', $userLang),
    '4'   => translate('유예', $userLang),
    '5'   => translate('유료', $userLang),
);

$arr_mt_level_option = '';
foreach ($arr_mt_level as $key => $val) {
    if ($val) {
        $arr_mt_level_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_retire_chk = array(
    '1'   => translate('서비스가 복잡해요.', $userLang),
    '2'   => translate('필요한 기능이 없어요.', $userLang),
    '3'   => translate('다른 서비스를 이용할래요.', $userLang),
    '4'   => translate('기타 이유', $userLang),
);

$arr_mt_retire_chk_option = '';
foreach ($arr_mt_retire_chk as $key => $val) {
    if ($val) {
        $arr_mt_retire_chk_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_bt_type = array(
    '1'   => translate('홈', $userLang),
    '2'   => translate('내장소', $userLang),
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
        $arr_date_group_option .= "<option value='".$val."' >".$val.translate('일', $userLang)."</option>"; // "일" 번역
    }
}

$arr_qt_status = array(
    '1'   => translate('답변대기', $userLang),
    '2'   => translate('답변완료', $userLang),
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
    '1'   => translate('오늘은 구름이 조금 있지만 햇살이 보일 거예요.', $userLang), //구름 뒤에 있는 해
    '2'   => translate('오늘은 구름이 많이 끼어 있어요.', $userLang), //구름
    '3'   => translate('오늘은 안개가 많이 끼었어요. 시야가 흐릴 수 있으니 조심하세요.', $userLang), //안개
    '4'   => translate('오늘은 비가 내릴 예정이에요. 우산을 챙기세요.', $userLang), //비
    '5'   => translate('오늘은 비와 눈이 섞여 내릴 거예요. 따뜻한 옷과 우산을 챙기세요.', $userLang), //비와 눈
    '6'   => translate('오늘은 눈이 내릴 예정이에요. 미끄럼에 주의하세요.', $userLang), //눈
    '7'   => translate('오늘은 천둥번개가 치는 날입니다. 가능하다면 실내에서 지내세요.', $userLang), //천둥번개
    '8'   => translate('오늘은 하늘이 맑아요. 기분 좋은 하루 보내세요.', $userLang), //맑음
);

$arr_mt_weather_sky_option = '';
foreach ($arr_mt_weather_sky as $key => $val) {
    if ($val) {
        $arr_mt_weather_sky_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_alram = array(
    '1'   => translate('일정 시작전', $userLang),
    '2'   => translate('10분전', $userLang),
    '3'   => translate('1시간전', $userLang),
    '4'   => translate('1일전', $userLang),
);

$arr_sst_alram_option = '';
foreach ($arr_sst_alram as $key => $val) {
    if ($val) {
        $arr_sst_alram_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_repeat_json = array(
    '1'   => translate('반복 안 함', $userLang),
    '2'   => translate('매일', $userLang),
    '3'   => translate('1주 마다', $userLang),
    '4'   => translate('매월', $userLang),
    '5'   => translate('매년', $userLang),
);

$arr_sst_repeat_json_option = '';
foreach ($arr_sst_repeat_json as $key => $val) {
    if ($val) {
        $arr_sst_repeat_json_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_repeat_json_r2 = array(
    '1'   => translate('월', $userLang),
    '2'   => translate('화', $userLang),
    '3'   => translate('수', $userLang),
    '4'   => translate('목', $userLang),
    '5'   => translate('금', $userLang),
    '6'   => translate('토', $userLang),
    '7'   => translate('일', $userLang),
);

$arr_sst_repeat_json_r2_option = '';
foreach ($arr_sst_repeat_json_r2 as $key => $val) {
    if ($val) {
        $arr_sst_repeat_json_r2_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_rlt_cate = array(
    '1'   => translate('입시/검정/보습', $userLang),
    '2'   => translate('국제화', $userLang),
    '3'   => translate('예능(대)', $userLang),
    '4'   => translate('기예(대)', $userLang),
    '5'   => translate('기타(대)', $userLang),
);

$arr_rlt_cate_option = '';
foreach ($arr_rlt_cate as $key => $val) {
    if ($val) {
        $arr_rlt_cate_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_pft_send_type = array(
    '1'   => translate('전체회원', $userLang),
    '2'   => translate('특정사용자', $userLang),
    '3'   => translate('특정그룹', $userLang),
);

$arr_pft_send_type_option = '';
foreach ($arr_pft_send_type as $key => $val) {
    if ($val) {
        $arr_pft_send_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_pft_status = array(
    '1'   => translate('발송대기', $userLang),
    '2'   => translate('발송중', $userLang),
    '3'   => translate('발송완료', $userLang),
);

$arr_pft_status_option = '';
foreach ($arr_pft_status as $key => $val) {
    if ($val) {
        $arr_pft_status_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_agree = array(
    '1'   => translate('서비스 이용약관', $userLang),
    '2'   => translate('개인정보 처리방침', $userLang),
    '3'   => translate('위치기반서비스 이용약관', $userLang),
    '4'   => translate('개인정보 제3자 제공', $userLang),
    '5'   => translate('마케팅 정보 수집 및 이용', $userLang),
);


$arr_plt_type = array(
    '1'   => translate('회원가입시', $userLang),
    '2'   => translate('장소알림', $userLang),
    '3'   => translate('발송완료', $userLang),
);

$arr_plt_type_option = '';
foreach ($arr_plt_type as $key => $val) {
    if ($val) {
        $arr_plt_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}
$arr_grant = array(
    '1'   => translate('오너', $userLang),
    '2'   => translate('리더', $userLang),
    '3'   => translate('그룹원', $userLang),
);

$arr_ot_pay_type = array(
    '1'   => translate('신규가입', $userLang),
    '2'   => translate('앱결제', $userLang),
    '3'   => translate('쿠폰', $userLang),
    '4'   => translate('추천인', $userLang),
);

$arr_ot_pay_type_option = '';
foreach ($arr_ot_pay_type as $key => $val) {
    if ($val) {
        $arr_ot_pay_type_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_days = array(
    '30'   => translate('1개월', $userLang),
    '60'   => translate('2개월', $userLang),
    '180'   => translate('6개월', $userLang),
    '365'   => translate('12개월', $userLang),
);

$arr_ct_days_option = '';
foreach ($arr_ct_days as $key => $val) {
    if ($val) {
        $arr_ct_days_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_show = array(
    'Y'   => translate('노출', $userLang),
    'N'   => translate('미노출', $userLang),
);

$arr_ct_show_option = '';
foreach ($arr_ct_show as $key => $val) {
    if ($val) {
        $arr_ct_show_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_use = array(
    'Y'   => translate('사용완료', $userLang),
    'N'   => translate('미사용', $userLang),
);

$arr_ct_use_option = '';
foreach ($arr_ct_use as $key => $val) {
    if ($val) {
        $arr_ct_use_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_end = array(
    'Y'   => translate('만료', $userLang),
    'N'   => translate('미만료', $userLang),
);

$arr_ct_end_option = '';
foreach ($arr_ct_end as $key => $val) {
    if ($val) {
        $arr_ct_end_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_slt_enter_chk = array(
    'Y'   => translate('알림', $userLang),
    'N'   => translate('해제', $userLang),
);

$arr_slt_enter_chk_option = '';
foreach ($arr_slt_enter_chk as $key => $val) {
    if ($val) {
        $arr_slt_enter_chk_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}