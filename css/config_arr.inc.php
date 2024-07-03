<?php

$arr_mt_type = array(
    '1'   => '일반',
    '2'   => '카카오',
    '3'   => '애플',
    '4'   => '구글',
);

$arr_mt_type_option = '';
foreach ($arr_mt_type as $key => $val) {
    if ($val) {
        $arr_mt_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_status = array(
    '1'   => '정상',
    '2'   => '정지',
);

$arr_mt_status_option = '';
foreach ($arr_mt_status as $key => $val) {
    if ($val) {
        $arr_mt_status_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_gender = array(
    '1'   => '남성',
    '2'   => '여성',
);

$arr_mt_gender_option = '';
foreach ($arr_mt_gender as $key => $val) {
    if ($val) {
    }
    $arr_mt_gender_option .= "<option value='".$key."' >".$val."</option>";
}

$arr_mt_level = array(
    '1'   => '탈퇴',
    '2'   => '무료',
    '3'   => '휴면',
    '4'   => '유예',
    '5'   => '유료',
);

$arr_mt_level_option = '';
foreach ($arr_mt_level as $key => $val) {
    if ($val) {
        $arr_mt_level_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_retire_chk = array(
    '1'   => '서비스가 복잡해요.',
    '2'   => '필요한 기능이 없어요.',
    '3'   => '다른 서비스를 이용할래요.',
    '4'   => '기타 이유',
);

$arr_mt_retire_chk_option = '';
foreach ($arr_mt_retire_chk as $key => $val) {
    if ($val) {
        $arr_mt_retire_chk_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_bt_type = array(
    '1'   => '홈',
    '2'   => '내장소',
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
        $arr_date_group_option .= "<option value='".$val."' >".$val."일</option>";
    }
}

$arr_qt_status = array(
    '1'   => '답변대기',
    '2'   => '답변완료',
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
    '1'   => '오늘은 구름이 조금 있지만 햇살이 보일 거예요.', //구름 뒤에 있는 해
    '2'   => '오늘은 구름이 많이 끼어 있어요.', //구름
    '3'   => '오늘은 안개가 많이 끼었어요. 시야가 흐릴 수 있으니 조심하세요.', //안개
    '4'   => '오늘은 비가 내릴 예정이에요. 우산을 챙기세요.', //비
    '5'   => '오늘은 비와 눈이 섞여 내릴 거예요. 따뜻한 옷과 우산을 챙기세요.', //비와 눈
    '6'   => '오늘은 눈이 내릴 예정이에요. 미끄럼에 주의하세요.', //눈
    '7'   => '오늘은 천둥번개가 치는 날입니다. 가능하다면 실내에서 지내세요.', //천둥번개
    '8'   => '오늘은 하늘이 맑아요. 기분 좋은 하루 보내세요.', //맑음
);

$arr_mt_weather_sky_option = '';
foreach ($arr_mt_weather_sky as $key => $val) {
    if ($val) {
        $arr_mt_weather_sky_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_alram = array(
    '1'   => '일정 시작전',
    '2'   => '10분전',
    '3'   => '1시간전',
    '4'   => '1일전',
);

$arr_sst_alram_option = '';
foreach ($arr_sst_alram as $key => $val) {
    if ($val) {
        $arr_sst_alram_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_repeat_json = array(
    '1'   => '반복 안 함',
    '2'   => '매일',
    '3'   => '1주 마다',
    '4'   => '매월',
    '5'   => '매년',
);

$arr_sst_repeat_json_option = '';
foreach ($arr_sst_repeat_json as $key => $val) {
    if ($val) {
        $arr_sst_repeat_json_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_sst_repeat_json_r2 = array(
    '1'   => '월',
    '2'   => '화',
    '3'   => '수',
    '4'   => '목',
    '5'   => '금',
    '6'   => '토',
    '7'   => '일',
);

$arr_sst_repeat_json_r2_option = '';
foreach ($arr_sst_repeat_json_r2 as $key => $val) {
    if ($val) {
        $arr_sst_repeat_json_r2_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_rlt_cate = array(
    '1'   => '입시/검정/보습',
    '2'   => '국제화',
    '3'   => '예능(대)',
    '4'   => '기예(대)',
    '5'   => '기타(대)',
);

$arr_rlt_cate_option = '';
foreach ($arr_rlt_cate as $key => $val) {
    if ($val) {
        $arr_rlt_cate_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_pft_send_type = array(
    '1'   => '전체회원',
    '2'   => '특정사용자',
    '3'   => '특정그룹',
);

$arr_pft_send_type_option = '';
foreach ($arr_pft_send_type as $key => $val) {
    if ($val) {
        $arr_pft_send_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_pft_status = array(
    '1'   => '발송대기',
    '2'   => '발송중',
    '3'   => '발송완료',
);

$arr_pft_status_option = '';
foreach ($arr_pft_status as $key => $val) {
    if ($val) {
        $arr_pft_status_option .= "<option value='".$key."' >".$val."</option>";
    }
}

$arr_mt_agree = array(
    '1'   => '서비스 이용약관',
    '2'   => '개인정보 처리방침',
    '3'   => '위치기반서비스 이용약관',
    '4'   => '개인정보 제3자 제공',
    '5'   => '마케팅 정보 수집 및 이용',
);


$arr_plt_type = array(
    '1'   => '회원가입시',
    '2'   => '장소알림',
    '3'   => '발송완료',
);

$arr_plt_type_option = '';
foreach ($arr_plt_type as $key => $val) {
    if ($val) {
        $arr_plt_type_option .= "<option value='".$key."' >".$val."</option>";
    }
}
$arr_grant = array(
    '1'   => '오너',
    '2'   => '리더',
    '3'   => '그룹원',
);

$arr_ot_pay_type = array(
    '1'   => '신규가입',
    '2'   => '앱결제',
    '3'   => '쿠폰',
    '4'   => '추천인',
);

$arr_ot_pay_type_option = '';
foreach ($arr_ot_pay_type as $key => $val) {
    if ($val) {
        $arr_ot_pay_type_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_days = array(
    '30'   => '1개월',
    '60'   => '2개월',
    '180'   => '6개월',
    '365'   => '12개월',
);

$arr_ct_days_option = '';
foreach ($arr_ct_days as $key => $val) {
    if ($val) {
        $arr_ct_days_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_show = array(
    'Y'   => '노출',
    'N'   => '미노출',
);

$arr_ct_show_option = '';
foreach ($arr_ct_show as $key => $val) {
    if ($val) {
        $arr_ct_show_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_use = array(
    'Y'   => '사용완료',
    'N'   => '미사용',
);

$arr_ct_use_option = '';
foreach ($arr_ct_use as $key => $val) {
    if ($val) {
        $arr_ct_use_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_ct_end = array(
    'Y'   => '만료',
    'N'   => '미만료',
);

$arr_ct_end_option = '';
foreach ($arr_ct_end as $key => $val) {
    if ($val) {
        $arr_ct_end_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}

$arr_slt_enter_chk = array(
    'Y'   => '알림',
    'N'   => '해제',
);

$arr_slt_enter_chk_option = '';
foreach ($arr_slt_enter_chk as $key => $val) {
    if ($val) {
        $arr_slt_enter_chk_option .= "<option value='" . $key . "' >" . $val . "</option>";
    }
}