<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '1';
$h_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";

// 앱 체크(auth를 탓는지 체크)
if (!$_SESSION['_auth_chk']) {
    // 로그인 체크
    if (!isset($_SESSION['_mt_idx'])) {
        // frame 탔는지 체크
        if ($_SESSION['frame_chk'] == true && !isset($_SESSION['_mt_idx'])) {
            // frame 탔을 경우
            $_SESSION['frame_chk'] = false;
            alert(translate('로그인이 필요합니다.', $userLang), './login', '');
        } else if (!isset($_SESSION['_mt_idx']) && $chk_mobile) { // mt_idx 값이 없고 모바일일 경우
            $_SESSION['frame_chk'] = false;
            alert(translate('로그인이 필요합니다.', $userLang), './login', '');
        } else {
            // frame 안탔을 경우
            $_SESSION['frame_chk'] = true;
            header('Location: ./frame');
            exit;
        }
    } else { // 이미 로그인을 했을 경우
        // frame 탔을 경우
        if ($_SESSION['frame_chk'] == true) {
            $_SESSION['frame_chk'] = false;
        } else {
            // frame 안탔을 경우
            $_SESSION['frame_chk'] = true;
            header('Location: ./frame');
            exit;
        }
    }
}

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다.\n 다시 로그인 부탁드립니다.', $userLang), './logout');
    }
}

$s_date = date("Y-m-d");
$sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
$sgdt_leader_cnt = f_get_leader_cnt($_SESSION['_mt_idx']); //리더인 그룹수
$sgdt_cnt = f_group_invite_cnt($_SESSION['_mt_idx']); //초대된 그룹수
$sgt_row = f_group_info($_SESSION['_mt_idx']); // 그룹생성여부

// 참여한그룹여부
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_idx', $sgt_row['sgt_idx']);
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_owner_chk', 'Y');
$sgdt_row = $DB->getone('smap_group_detail_t');

if (!$sgdt_row['sgdt_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_owner_chk', 'N');
    $sgdt_row = $DB->getone('smap_group_detail_t');
}
$member_info_row = get_member_t_info($_SESSION['_mt_idx']);
?>
<style>
    #map {
        width: 100%;
        height: 400px;
        /* 또는 원하는 높이 */
    }

    html {
        height: 100%;
        overflow-y: unset !important;
    }

    #wrap {
        height: 100vh;
        min-height: 100vh;
        overflow-y: hidden;
    }

    .head_01 {
        background-color: #FBFBFF;
    }

    .idx_pg {
        position: fixed;
        top: 0;
        left: 50%;
        width: 100%;
        height: 100%;
        max-width: 50rem;
        transform: translateX(-50%);
        padding: 10.6rem 0 6rem 0;
        height: 100% !important;
        min-height: 100% !important;
    }
</style>
<div class="container-fluid idx_pg px-0 ">
    <div class="idx_pg_div">
        <section class="main_top">
            <!--D-6 멤버 스케줄 미참석 팝업 임시로 넣어놓았습니다.-->
            <div class="py-3 px_16 top_weather" id="top_weather_box" style="height: 58px;">
                <div class="d-flex align-items-center p_address">
                    <p class="fs_12 text_light_gray fw_500 text_dynamic"><?= translate('잠시만 기다려주세요! 주소 정보를 가져오는 중입니다.!', $userLang); ?></p>
                    <!-- <p class="fs_12 text_light_gray text_dynamic p_content line_h1_2">잠시만 기다려주세요! 기상 데이터를 가져오는 중입니다.!</p> -->
                </div>
                <!-- 로딩할때 사용 -->
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="date_weather d-flex align-items-center flex-wrap">
                        <div class="d-flex align-items-center fs_14 fw_600 text_dynamic mr-1 mt_08"><?= DateType(date("Y-m-d"), 3) ?>
                            <span class="loader loader_sm ml-2 mr-2"></span>
                        </div>
                        <div class="d-flex align-items-center mt_08 mr-3">
                            <p class="ml-1 fs_11 fw_600 text-text fw_500 mr-2"><span class="fs_11 text_light_gray mr-1"><?= translate('기상 데이터를 가져오는 중입니다.!', $userLang); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 지도 wrap -->
        <section class="pg_map_wrap num_point_map" id="">
            <div class="pg_map_inner" id="map_info_box">
                <div class="banner">
                    <div class="banner_inner">
                        <!-- Swiper -->
                        <div class="swiper banSwiper">
                            <div class="swiper-wrapper">
                                <?
                                $DB->where('bt_type', 1);
                                $DB->where('bt_show', 'Y');
                                $DB->orderby('bt_rank', 'asc');
                                $banner_list = $DB->get('banner_t');
                                if ($banner_list) {
                                    foreach ($banner_list as $bt_row) {
                                ?>
                                        <div class="swiper-slide">
                                            <div class="bner_txt">
                                                <p class="text-primary fs_13 mr-2"><i class="xi-info"></i></p>
                                                <p class="text_dynamic fs_12 fw_500 line_h1_3"><?= $bt_row['bt_title'] ?></p>
                                            </div>
                                            <div class="">
                                                <div class="rect_bner">
                                                    <img src="<?= $ct_img_url . '/' . $bt_row['bt_file'] ?>" alt="배너이미지" onerror="this.src='<?= $ct_no_img_url ?>'" />
                                                </div>
                                            </div>
                                        </div>
                                <?
                                    }
                                }
                                ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
                <div class="log_map_wrap" id="map" style="height:100%">
                </div>
                <div class="point_wrap point_myplt" style="top:2rem">
                    <button type="button" class="btn point point_mypoint" onclick="f_my_location_btn(<?= $_SESSION['_mt_idx'] ?>)">

                        <span class="point_inner">
                            <span class="point_txt"><img src="./img/ico_mypoint.png" width="18px" alt="내위치" /></span>
                        </span>
                    </button>
                </div>
                <div class="point_wrap point_myplt" style="top:6rem">
                    <button type="button" class="btn point point_mypoint" onclick="toggleInfobox()" id="infoboxBtn">
                        <span class="point_inner">
                            <span class="point_txt"><img src="./img/ico_info_on.png" width="35px" alt="info" id="infoboxImg" /></span>
                        </span>
                    </button>
                </div>
            </div>
        </section>
        <!-- D-4 그룹 생성 직후 홈화면(오너)에 필요한 부분입니다. [시작] -->
        <? if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) { // 오너, 리더일 경우
            $session_img = get_profile_image_url($member_info_row['mt_file1']);
        ?>
            <section class="opt_bottom" style="transform: translateY(82%);">
                <div class="top_bar_wrap text-center pt_08">
                    <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                    <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
                </div>
                <div class="">
                    <div class="grp_wrap">
                        <div class="border bg-white rounded-lg px_16 py_16">
                            <p class="fs_16 fw_600 mb-3"><?= translate('그룹원', $userLang); ?></p>
                            <form method="post" name="frm_group_list" id="frm_group_list" onsubmit="return false;">
                                <input type="hidden" name="act" id="act" value="group_member_list" />
                                <input type="hidden" name="obj_list2" id="obj_list2" value="group_member_list_box" />
                                <input type="hidden" name="obj_frm2" id="obj_frm2" value="frm_group_list" />
                                <input type="hidden" name="obj_uri2" id="obj_uri2" value="./schedule_update" />
                                <input type="hidden" name="obj_pg2" id="obj_pg2" value="1" />
                                <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                                <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                                <input type="hidden" name="group_sgdt_idx" id="group_sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
                            </form>
                            <style>
                                @keyframes loading {
                                    0% {
                                        transform: rotate(0deg);
                                    }

                                    100% {
                                        transform: rotate(360deg);
                                    }
                                }

                                .loading-animation {
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    border: 4px solid #f3f3f3;
                                    border-top: 4px solid #3498db;
                                    animation: loading 1s infinite linear;
                                }
                            </style>

                            <div id="group_member_list_box">
                                <div class="mem_wrap mem_swiper">
                                    <div class="swiper-wrapper d-flex">
                                        <!-- 로딩 애니메이션 추가 -->
                                        <div id="loading-placeholder" class="d-flex align-items-center justify-content-center" style="width: 100%; height: 81px;">
                                            <div class="loading-animation"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 일정리스트 -->
                    <div class="task_wrap">
                        <div class="border bg-white rounded-lg mb-3">
                            <div id="schedule_list_box">
                                <div class="task_header px_16 pt_16" id="my_location_div">
                                    <div class="border-bottom  pb-3">
                                        <div class="task_header_tit">
                                            <p class="fs_16 fw_600 line_h1_2 mr-3"><?= translate('현재 위치', $userLang); ?></p>
                                            <div class="d-flex align-items-center justify-content-end">
                                                <p class="move_txt fs_13 mr-3"></p>
                                                <p class="d-flex bettery_txt fs_13">
                                                    <span class="d-flex align-items-center flex-shrink-0 mr-2">
                                                        <img src="./img/battery.png?v=20240404" width="14px" class="battery_img" alt="베터리시용량">
                                                    </span>
                                                    <span class="battery_percentage" style=""></span>
                                                </p>
                                            </div>
                                        </div>
                                        <p class="fs_14 fw_500 text_light_gray text_dynamic line_h1_3 mt-2"><?= translate('현재 위치 받아오는 중..', $userLang); ?></p>
                                    </div>
                                </div>
                                <div class="task_body px_16 pt-3">
                                    <div class="task_body_cont num_point_map">
                                        <div class="pt-5">
                                            <!-- <button type="button" class="btn w-100 rounded add_sch_btn" onclick="location.href='./schedule_form?sdate=<?= $_POST['event_start_date'] ?>&sgdt_idx=<?= $_POST['sgdt_idx'] ?>'"><i class="xi-plus-min mr-3"></i> 일정을 추가해보세요!</button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- D-4 그룹 생성 직후 홈화면(오너)에 필요한 부분입니다. [끝] -->
        <? } else {  // 그룹원일 경우
        ?>
            <? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
                <section class="opt_bottom" style="transform: translateY(50%);">
                <? } else { ?>
                    <section class="opt_bottom" style="transform: translateY(0%);">
                    <? } ?>
                    <div class="top_bar_wrap text-center pt_08">
                        <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                        <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
                    </div>
                    <div class="">
                        <!-- 일정리스트 -->
                        <div class="task_wrap">
                            <div class="border bg-white rounded-lg mb-3">
                                <!-- <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                                <input type="hidden" name="act" id="act" value="member_schedule_list" />
                                <input type="hidden" name="obj_list" id="obj_list" value="schedule_list_box" />
                                <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                                <input type="hidden" name="obj_uri" id="obj_uri" value="./schedule_update" />
                                <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                                <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                                <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                                <input type="hidden" name="event_start_date" id="event_start_date" value="<?= $s_date ?>" />
                                <input type="hidden" name="event_start_date_t" id="event_start_date_t" value="<?= DateType($s_date, 19) ?>" />
                                <input type="hidden" name="main_schedule" id="main_schedule" value="Y" />
                                <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
                            </form> -->
                                <div id="schedule_list_box">
                                    <div class="task_header px_16 pt_16" id="my_location_div">
                                        <div class="border-bottom  pb-3">
                                            <div class="task_header_tit">
                                                <p class="fs_16 fw_600 line_h1_2 mr-3"><?= translate('현재 위치', $userLang); ?></p>
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <p class="move_txt fs_13 mr-3"></p>
                                                    <p class="d-flex bettery_txt fs_13">
                                                        <span class="d-flex align-items-center flex-shrink-0 mr-2">
                                                            <img src="./img/battery.png?v=20240404" width="14px" class="battery_img" alt="베터리시용량">
                                                        </span>
                                                        <span class="battery_percentage" style="color: #FFC107"></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="fs_14 fw_500 text_light_gray text_dynamic line_h1_3 mt-2"><?= translate('현재 위치 받아오는 중..', $userLang); ?></p>
                                        </div>
                                    </div>
                                    <div class="task_body px_16 pt-3">
                                        <div class="task_body_cont num_point_map">
                                            <div class="pt-5">
                                                <!-- <button type="button" class="btn w-100 rounded add_sch_btn" onclick="location.href='./schedule_form?sdate=<?= $_POST['event_start_date'] ?>&sgdt_idx=<?= $_POST['sgdt_idx'] ?>'"><i class="xi-plus-min mr-3"></i> 일정을 추가해보세요!</button> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </section>
                <? } ?>
    </div>
</div>
<!-- 초대링크로 가입하셨나요? 플러팅 -->
<? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
    <div class="floating_wrap on" id="first_floating_modal">
        <div class="flt_inner">
            <div class="flt_head">
                <div></div>
            </div>
            <div class="flt_body d-flex flex-column">
                <!-- Top row with text on the left and image on the right -->
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <p class="fc_3d72ff fs_14 fw_700 text-primary mb-3">🎉 환영합니다!</p>
                        <p class="text_dynamic line_h1_3 fs_17 fw_700 mt-3">SMAP과 함께
                            위치와 일정을 관리하며
                            편리한 일상을 누리세요.</p>
                        <!-- <span class="text-primary"></span>로 가입하셨나요?</p> -->
                    </div>
                    <img src="./img/send_img.png" class="flt_img_send" width="66px" alt="초대링크" />
                </div>
                <!-- Bottom row with additional text -->
                <div class="mb-4">
                    <p class="text_dynamic line_h1_3 text_gray fs_14 mt-1 fw_500"></p>
                </div>
            </div>
            <style>
                .flt_footer .btn {
                    height: 55px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    text-align: center;
                    padding: 10px;
                }

                .flt_footer .btn small {
                    font-size: 10px;
                    /* 작은 글씨 크기 조정 */
                    margin-top: 5px;
                    /* 큰 글씨와 작은 글씨 사이 간격 조정 */
                }
            </style>
            <div class="flt_footer flt_footer_b">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 flt_close" onclick="location.href='./group_create'">
                        그룹 오너 되기<br>
                        <!-- <small>그룹을 만들고 그룹원을 초대할 수 있어요</small> -->
                        <small>(부모, 관리자)</small>
                    </button>
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='./invitation_code'">
                        초대코드 입력하기<br>
                        <!-- <small>초대코드를 입력하고 그룹에 참여하세요</small> -->
                        <small>(자녀, 그룹원)</small>
                    </button>
                </div>
            </div>
        </div>
    </div>

<? } ?>
<!-- 그룹만들기 플러팅 -->
<div class="floating_wrap " id="group_make_modal">
    <div class="flt_inner">
        <div class="flt_head">
            <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= translate("그룹만들기", $userLang) ?></span></p> <!-- "그룹만들기" 번역 -->
        </div>
        <div class="flt_body pb-5 pt-3">
            <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= translate("친구들과 함께할", $userLang) ?> <!-- "친구들과 함께할" 번역 -->
                <span class="text-primary"><?= translate("나만의 그룹", $userLang) ?></span><?= translate("을 만들어 볼까요?", $userLang) ?> <!-- "나만의 그룹", "을 만들어 볼까요?" 번역 -->
            </p>
            <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?= translate("그룹원을 추가하면 실시간 위치 조회를 할 수 있어요.", $userLang) ?></p> <!-- "그룹원을 추가하면 실시간 위치 조회를 할 수 있어요." 번역 -->
        </div>
        <div class="flt_footer">
            <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'"><?= translate("다음", $userLang) ?></button> <!-- "다음" 번역 -->
        </div>
    </div>
</div>
<!-- D-11 그룹 있을 때 초대링크로 앱 접속  -->
<div class="modal fade" id="dbgroup_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center pb-5">
                <img src="./img/warring.png" width="72px" class="pt-3" alt="<?= translate("그룹참여불가능", $userLang) ?>" /> <!-- "그룹참여불가능" 번역 -->
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4"><?= translate("그룹에 참여할 수 없어요.", $userLang) ?></p> <!-- "그룹에 참여할 수 없어요." 번역 -->
                <p class="fs_14 text_dynamic text_gray mt-2 line_h1_2 px-4"><?= translate("현재 참여한(생성한) 그룹이 있어 다른 그룹에 참여할 수 없어요. 이 그룹에 참여하시려면 모든 그룹의 활동을 끝내고 이후 다시 시도해 주세요.", $userLang) ?></p> <!-- "현재 참여한(생성한) 그룹이 있어 다른 그룹에 참여할 수 없어요. 이 그룹에 참여하시려면 모든 그룹의 활동을 끝내고 이후 다시 시도해 주세요." 번역 -->
            </div>
            <div class="modal-footer px-0 py-0">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close"><?= translate("알겠어요!", $userLang) ?></button> <!-- "알겠어요!" 번역 -->
            </div>
        </div>
    </div>
</div>
<!-- D-6 최적경로 사용 : 최적경로 표시하기 버튼 클릭시 나오는 모달창  -->
<div class="modal fade" id="optimal_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" name="pedestrian_path_modal_sgdt_idx" id="pedestrian_path_modal_sgdt_idx" value="" />
            <input type="hidden" name="path_day_count" id="path_day_count" value="" />
            <div class="modal-body text-center pb-4">
                <img src="./img/optimal_map.png" width="48px" class="pt-3" alt="<?= translate("최적의경로", $userLang) ?>" /> <!-- "최적의경로" 번역 -->
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4"><?= translate("현재 위치에서부터 다음 일정까지의
                    최적의 경로를 표시할까요?", $userLang) ?></p> <!-- "현재 위치에서부터 다음 일정까지의 최적의 경로를 표시할까요?" 번역 -->
                <p class="fs_12 text_dynamic text_gray mt-2 line_h1_2"><?= translate("최적경로 및 예상시간과 거리가 표시됩니다.", $userLang) ?></p> <!-- "최적경로 및 예상시간과 거리가 표시됩니다." 번역 -->
                <div class="optimal_info_wrap">
                    <p class="optim_plan" id="pathType"><span><?= translate("Basic", $userLang) ?></span></p> <!-- "Basic" 번역 -->
                    <p class="text-primary fs_14 fw_600 text_dynamic mt-3 line_h1_4" id="pathCountday"><?= translate("금일 2회 사용 가능", $userLang) ?></p> <!-- "금일 2회 사용 가능" 번역 -->
                    <p class=" text-primary fs_14 fw_600 text_dynamic line_h1_4" id="pathCountmonth"><?= translate("이번달 60회 사용 가능", $userLang) ?></p> <!-- "이번달 60회 사용 가능" 번역 -->
                    <p class="text_gray fs_11 text_dynamic line_h1_3 mt-2" id="pathContent"><?= translate("Basic 사용자는 하루 2번, 월 60번까지 사용 가능해요!", $userLang) ?></p> <!-- "Basic 사용자는 하루 2번, 월 60번까지 사용 가능해요!" 번역 -->
                </div>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?= translate("취소하기", $userLang) ?></button> <!-- "취소하기" 번역 -->
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" id="showPathButton"><?= translate("표시하기", $userLang) ?></button> <!-- "표시하기" 번역 -->
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0 d-none" id="showPathAdButton"><?= translate("표시하기", $userLang) ?></button> <!-- "표시하기" 번역 -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- D-12 유료플랜 종료  -->
<div class="modal fade" id="planinfo_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center pb-5">
                <img src="./img/warring.png" width="72px" class="pt-3" alt="<?= translate("플랜", $userLang) ?>" /> <!-- "플랜" 번역 -->
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4 mb-3"><?= translate("구독기간이 종료되어
                    이래 기능이 제한되었어요", $userLang) ?></p> <!-- "구독기간이 종료되어 이래 기능이 제한되었어요" 번역 -->
                <div class="planinfo_box">
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-center flex-wrap">
                            <p class="fs_16 text_dynamic fw_700 mb-4 mr-2"><?= translate("일정 최적경로 사용횟수", $userLang) ?></p> <!-- "일정 최적경로 사용횟수" 번역 -->
                            <p class="fs_11 text_dynamic fw_700 mb-4"><?= translate("(하루/월)", $userLang) ?></p> <!-- "(하루/월)" 번역 -->
                        </div>
                        <div class="d-flex align-items-center justify-content-center">
                            <p class="text_light_gray fs_14 fw_700 mr-2">10/300</p>
                            <i class="text_light_gray fs_14 xi-arrow-right mr-2"></i>
                            <p class="text-primary fs_14 fw_700">2/60</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="fs_16 text_dynamic fw_700 line_h1_3 mb-4"><?= translate("내 장소 저장", $userLang) ?></p> <!-- "내 장소 저장" 번역 -->
                        <div class="d-flex align-items-center justify-content-center">
                            <p class="text_light_gray fs_14 fw_700 mr-2"><?= translate("무제한", $userLang) ?></p> <!-- "무제한" 번역 -->
                            <i class="text_light_gray fs_14 xi-arrow-right mr-2"></i>
                            <p class="text-primary fs_14 fw_700"><?= translate("2개", $userLang) ?></p> <!-- "2개" 번역 -->
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="fs_16 text_dynamic fw_700 line_h1_3 mb-4"><?= translate("로그 조회기간", $userLang) ?></p> <!-- "로그 조회기간" 번역 -->
                        <div class="d-flex align-items-center justify-content-center">
                            <p class="text_light_gray fs_14 fw_700 mr-2"><?= translate("2주", $userLang) ?></p> <!-- "2주" 번역 -->
                            <i class="text_light_gray fs_14 xi-arrow-right mr-2"></i>
                            <p class="text-primary fs_14 fw_700"><?= translate("2일", $userLang) ?></p> <!-- "2일" 번역 -->
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="rect_modalbner">
                            <?= translate("광고가표시됩니다.", $userLang) ?> <!-- "광고가표시됩니다." 번역 -->
                        </div>
                    </div>
                    <p class="fs_14 text_gray text_dynamic line_h1_3"><?= translate("구독기간을 연장하면
                        다시 위 기능을 사용할 수 있어요.", $userLang) ?></p> <!-- "구독기간을 연장하면 다시 위 기능을 사용할 수 있어요." 번역 -->
                </div>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="location.href='./plan_info'"><?= translate("구독할래요!", $userLang) ?></button> <!-- "구독할래요!" 번역 -->
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close"><?= translate("알겠어요", $userLang) ?></button> <!-- "알겠어요" 번역 -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var scheduleMarkers = []; // 스케줄 마커를 저장할 배열입니다.
    var optimalPath; // 최적 경로를 표시할 변수입니다.
    var drawInfoArr = [];
    var resultdrawArr = [];
    var scheduleMarkerCoordinates = [];
    var scheduleStatus = [];
    var startX, startY, endX, endY; // 출발지와 도착지 좌표 변수 초기화
    var markers = [];
    var polylines = [];
    var profileMarkers = [];
    var pathCount;
    // 버튼 엘리먼트 찾기
    var showPathButton = document.getElementById('showPathButton');
    var showPathAdButton = document.getElementById('showPathAdButton'); //광고실행버튼
    let map;
    var centerLat, centerLng;
    // 전역 상태 객체
    const state = {
        pathData: null,
        walkingData: null,
        isDataLoaded: false
    };
    // 그룹원별 슬라이드 컨테이너를 저장할 객체
    const groupMemberSlides = {};
    let googleMapsLoaded = false;
    let googleMapsLoadPromise = null;
    let optBottomSelect;
    let bottomSheetHeight;
    let mapContainer = document.getElementById("map");
    let mapHeight = mapContainer.getBoundingClientRect().height;
    let verticalCenterOffset;
    let optBottom = document.querySelector(".opt_bottom");
    let isPannedDown = false;
    let originalCenter = null; // 초기 중심 좌표 저장
    let currentLat;
    let currentLng;
</script>
<script src="https://apis.openapi.sk.com/tmap/jsv2?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
<script script src="https://apis.openapi.sk.com/tmap/vectorjs?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
<?php
// 한국어 사용자를 위한 네이버 지도 API 스크립트
if ($userLang == 'ko') {
?>
    <script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>
    <!-- SK TMAP -->
    <script>
        map = new naver.maps.Map("map", {
            center: new naver.maps.LatLng(<?= $_SESSION['_mt_lat'] ?>, <?= $_SESSION['_mt_long'] ?>),
            zoom: 16,
            mapTypeControl: false
        }); // 전역 변수로 map을 선언하여 다른 함수에서도 사용 가능하도록 합니다.

        function initNaverMap(markerData, sgdt_idx) {
            // 지도 객체가 존재하면 초기화
            if (map) {
                // 기존 마커 제거
                profileMarkers.forEach(marker => marker.setMap(null));
                scheduleMarkers.forEach(marker => marker.setMap(null));
                markers.forEach(marker => marker.setMap(null));

                // 기존 폴리라인 제거
                polylines.forEach(polyline => polyline.setMap(null));

                // 배열 초기화
                profileMarkers = [];
                scheduleMarkers = [];
                markers = [];
                polylines = [];
            } else {
                // 지도 객체가 없다면 새로 생성
                map = new naver.maps.Map("map", {
                    center: new naver.maps.LatLng(37.5666805, 126.9784147), // 기본 중심 좌표 (필요에 따라 수정)
                    zoom: 16,
                    mapTypeControl: false
                });
            }
            let profileCount = 0;
            let scheduleCount = 0;

            map.setZoom(16); // 줌 레벨 16으로 초기화

            scheduleMarkerCoordinates = [];

            for (const sgdtIdx in markerData) {
                const memberData = markerData[sgdtIdx];

                // 프로필 마커 생성
                const profileLat = parseFloat(memberData.member_info.my_lat);
                const profileLng = parseFloat(memberData.member_info.mt_long);
                const profileImageUrl = memberData.member_info.my_profile;

                if (!isNaN(profileLat) && !isNaN(profileLng)) {
                    profileCount++;
                    const profileMarkerOptions = {
                        position: new naver.maps.LatLng(profileLat, profileLng),
                        map: map,
                        icon: {
                            content: `<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="${profileImageUrl}" alt="이미지" onerror="this.src='<?= $ct_no_img_url ?>'"/></div></div></div></div>`,
                            size: new naver.maps.Size(44, 44),
                            origin: new naver.maps.Point(0, 0),
                            anchor: new naver.maps.Point(22, 22),
                        },
                        zIndex: 2,
                    };
                    const profileMarker = new naver.maps.Marker(profileMarkerOptions);
                    profileMarkers.push(profileMarker);
                }

                // 현재 멤버의 sgdt_idx와 입력받은 sgdt_idx가 일치하는 경우에만 스케줄 마커 생성
                if (sgdtIdx === sgdt_idx.toString()) {
                    currentLat = parseFloat(memberData.member_info.my_lat);
                    currentLng = parseFloat(memberData.member_info.mt_long);
                    // 스케줄 마커 생성
                    memberData.schedules.forEach((schedule, index) => {
                        const scheduleLat = parseFloat(schedule.sst_location_lat);
                        const scheduleLng = parseFloat(schedule.sst_location_long);
                        const status = schedule.sst_all_day === 'Y' ?
                            'point_ing' :
                            new Date() >= new Date(schedule.sst_edate) ?
                            'point_done' :
                            new Date() >= new Date(schedule.sst_sdate) && new Date() <= new Date(schedule.sst_edate) ?
                            'point_ing' :
                            'point_gonna';
                        const sst_sdate_e1 = new Date(schedule.sst_sdate).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        const sst_sdate_e2 = new Date(schedule.sst_edate).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        const colorSets = [
                            ['#E6F2FF', '#E0F0FF'],
                            ['#D6E6FF', '#E0E6FF'],
                            ['#E5F1FF', '#E0F0FF'],
                            ['#F0F8FF', '#E6F0FF'],
                            ['#E0FFFF', '#E6FFFF'],
                            ['#E6F2FF', '#E0EDFF'],
                            ['#D6E6FF', '#E0E0FF'],
                            ['#E5F1FF', '#E0EDFF'],
                            ['#F0F8FF', '#E6EDFF'],
                            ['#E0FFFF', '#E6FEFF'],
                        ];
                        const randomSet = colorSets[Math.floor(Math.random() * colorSets.length)];
                        const color1 = randomSet[0];
                        const color2 = randomSet[1];
                        const pointClass =
                            status === 'point_ing' ?
                            'point2' :
                            status === 'point_done' ?
                            'point1' :
                            'point3';

                        if (!isNaN(scheduleLat) && !isNaN(scheduleLng)) {
                            scheduleCount++;
                            const markerContent = `
                                                <style>
                                                    .infobox5 {
                                                    position: absolute;
                                                    left: 50%;
                                                    top: 100%;
                                                    transform: translate(10%, -50%);
                                                    background-color: #413F4A;
                                                    padding: 0.3rem 0.8rem;
                                                    border-radius: 0.4rem;
                                                    z-index: 1;
                                                    display: inline-block;
                                                    white-space: nowrap;
                                                    overflow: hidden;
                                                    text-overflow: ellipsis;
                                                    margin-top: 0.4rem;
                                                    display: none;  /* 기본적으로 숨김 */ 
                                                    }
                                                    .infobox5 span {
                                                    white-space: nowrap !important;
                                                    overflow: hidden !important;
                                                    text-overflow: ellipsis !important;
                                                    }
                                                    .infobox5 .title {
                                                    color: ${color1};
                                                    display: block;
                                                    width: 100%;
                                                    margin-bottom: 0.1rem;
                                                    font-size: 12px !important;
                                                    font-weight: 800 !important;
                                                    }
                                                    .infobox5 .date-wrapper {
                                                    display: flex;
                                                    flex-direction: column;
                                                    align-items: flex-start;
                                                    }
                                                    .infobox5 .date {
                                                    color: ${color2};
                                                    margin-bottom: 0;
                                                    font-size: 8px !important;
                                                    font-weight: 700 !important;
                                                    }
                                                    .infobox5 .date + .date {
                                                    margin-top: 0.05rem;
                                                    }
                                                    .infobox5.on {
                                                    display: inline-block;  /* .on 클래스가 추가되면 표시 */
                                                    }
                                                </style>
                                                <div class="point_wrap ${pointClass}">
                                                    <button type="button" class="btn point ${status}">
                                                    <span class="point_inner">
                                                        <span class="point_txt">${scheduleCount}</span>
                                                    </span>
                                                    </button>
                                                    <div class="infobox5 rounded_04 px_08 py_03 on">
                                                    <span class="title">${schedule.sst_title}</span>
                                                    <div class="date-wrapper">
                                                        <span class="date">S: ${sst_sdate_e1}</span>
                                                        <span class="date">E: ${sst_sdate_e2}</span>
                                                    </div>
                                                    </div>
                                                </div>
                                                `;
                            const markerOptions = {
                                position: new naver.maps.LatLng(scheduleLat, scheduleLng),
                                map: map,
                                icon: {
                                    content: markerContent,
                                    size: new naver.maps.Size(61, 61),
                                    origin: new naver.maps.Point(0, 0),
                                    anchor: new naver.maps.Point(30, 30),
                                },
                                zIndex: 1,
                            };
                            const marker = new naver.maps.Marker(markerOptions);
                            scheduleMarkers.push(marker);
                            markers.push(marker);

                            if (scheduleCount === 1) {
                                startX = scheduleLat;
                                startY = scheduleLng;
                            } else if (scheduleCount === memberData.schedules.length) {
                                endX = scheduleLat;
                                endY = scheduleLng;
                            }

                            scheduleMarkerCoordinates.push(new naver.maps.LatLng(scheduleLat, scheduleLng));
                            scheduleStatus.push(status);
                        }
                    });
                }
            }

            // marker_reload 값 설정 (필요에 따라 수정)
            markerData.marker_reload = profileCount > 0 || scheduleCount > 0 ? 'Y' : 'N';
            markerData.profile_count = profileCount;
            markerData.count = scheduleCount;

            // 지도 중심 설정 및 이동 제한 (필요에 따라 수정)
            if (profileCount > 0) {
                const firstProfileMarker = profileMarkers[0];
                map.setCenter(firstProfileMarker.getPosition());
            } else if (scheduleCount > 0) {
                const firstScheduleMarker = scheduleMarkers[0];
                map.setCenter(firstScheduleMarker.getPosition());
            }

            // 지도 이동 시 이벤트 리스너 추가
            naver.maps.Event.addListener(map, 'idle', function() {
                var bounds = map.getBounds();
                markers.forEach(function(marker) {
                    if (bounds.hasLatLng(marker.getPosition())) {
                        marker.setMap(map);
                    } else {
                        marker.setMap(null);
                    }
                });
                polylines.forEach(function(polyline_) {
                    // 폴리라인의 경계를 가져옵니다.
                    var polylineBounds = polyline_.getBounds();
                    if (polylineBounds && bounds.intersects(polylineBounds)) {
                        polyline_.setMap(map);
                    } else {
                        polyline_.setMap(null);
                    }
                });
            });

            // initNaverMap 함수 끝에 map 변수의 상태를 체크하고 map이 정상적으로 생성되었을 때에만 setCursor 호출
            if (map) {
                map.setCursor('pointer');
                map.panBy(new naver.maps.Point(0, verticalCenterOffset)); // 중심을 위로 이동
            }
        }
    </script>
<?php
    // 한국어 이외의 사용자를 위한 구글 지도 API 스크립트
} else {
?>
    <script>
        // Google Maps API 로드 함수
        function loadGoogleMapsScript() {
            if (googleMapsLoadPromise) {
                return googleMapsLoadPromise;
            }

            googleMapsLoadPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBkWlND5fvW4tmxaj11y24XNs_LQfplwpw&libraries=places,geometry,marker&v=weekly`;
                script.async = true;
                script.defer = true;
                script.onload = () => {
                    googleMapsLoaded = true;
                    resolve();
                };
                script.onerror = reject;
                document.head.appendChild(script);
            });

            return googleMapsLoadPromise;
        }

        // 지도 초기화 함수
        async function initMap(st_lat = 35.12806700000000, st_lng = 136.90676000000000) {
            if (!googleMapsLoaded) {
                console.log("Waiting for Google Maps API to load...");
                await loadGoogleMapsScript();
            }

            if (map) {
                map.setCenter({
                    lat: parseFloat(st_lat),
                    lng: parseFloat(st_lng)
                });
                return map;
            }

            const mapOptions = {
                center: {
                    lat: parseFloat(st_lat),
                    lng: parseFloat(st_lng)
                },
                zoom: 15,
                mapTypeControl: false,
                mapId: "e40062e414aad354",
                fullscreenControl: false,
                disableDoubleClickZoom: true,
                clickableIcons: false, // 장소 아이콘 클릭 비활성화
                language: '<?= $userLang ?>'
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            console.log("Map initialized successfully");
            // 지도가 완전히 로딩된 후 이벤트 리스너 등록
            google.maps.event.addListenerOnce(map, 'idle', () => {
                if (state.isDataLoaded) {
                    drawPathOnMap();
                }
            });
            return map;
        }

        // 페이지 로드 시 Google Maps 초기화
        window.addEventListener('load', () => {
            loadGoogleMapsScript().then(() => {
                initMap().catch(error => console.error("Error initializing map:", error));
            }).catch(error => console.error("Error loading Google Maps API:", error));
        });

        window.addEventListener('resize', function() {
            if (map) {
                google.maps.event.trigger(map, 'resize');
            }
        });

        // 구글 지도 API를 사용하는 지도 초기화 및 관련 함수들
        async function initGoogleMap(markerData, sgdt_idx) {
            try {
                await loadGoogleMapsScript();

                // sgdt_idx에 해당하는 멤버의 위치 정보를 사용하여 지도 중심 설정
                if (!map) {
                    await initMap(markerData[sgdt_idx].member_info.my_lat, markerData[sgdt_idx].member_info.mt_long);
                } else {
                    map.setCenter({
                        lat: parseFloat(markerData[sgdt_idx].member_info.my_lat),
                        lng: parseFloat(markerData[sgdt_idx].member_info.mt_long)
                    });
                }
                console.log("Google Map initialized with custom data");
            } catch (error) {
                console.error("Error in initGoogleMap:", error);
            }

            map.setZoom(15); // 줌 레벨 16으로 초기화

            if (markerData) {
                // 기존 마커와 폴리라인 제거
                clearAllMapElements();

                // 마커와 폴리라인 배열 초기화
                markers = [];
                polylines = [];
                profileMarkers = [];
                scheduleMarkers = [];

                let scheduleCount = 0; // 전체 스케줄 개수 초기화
                let profileCount = 0; // 프로필 마커 개수 초기화

                for (const currentSgdtIdx in markerData) { // markerData 객체 순회
                    const memberData = markerData[currentSgdtIdx];
                    const profileLat = parseFloat(memberData.member_info.my_lat);
                    const profileLng = parseFloat(memberData.member_info.mt_long);
                    const profileImageUrl = memberData.member_info.my_profile;

                    if (!isNaN(profileLat) && !isNaN(profileLng)) {
                        profileCount++;
                        addGoogleProfileMarker(profileLat, profileLng, profileImageUrl);
                    }
                }

                scheduleMarkerCoordinates = [];
                for (const currentSgdtIdx in markerData) {
                    // 현재 멤버의 sgdt_idx와 입력받은 sgdt_idx가 일치하는 경우에만 스케줄 마커 생성
                    if (currentSgdtIdx === sgdt_idx.toString()) {
                        currentLat = parseFloat(markerData[currentSgdtIdx].location_info.mlt_lat);
                        currentLng = parseFloat(markerData[currentSgdtIdx].location_info.mlt_long);
                        const memberData = markerData[currentSgdtIdx];

                        // 스케줄이 있는 멤버인지 확인
                        if (memberData.schedules.length > 0) {
                            markerData.schedule_chk = 'Y'; // schedule_chk 값 설정
                            memberData.schedules.forEach((schedule, index) => {
                                const scheduleLat = parseFloat(schedule.sst_location_lat);
                                const scheduleLng = parseFloat(schedule.sst_location_long);
                                const status = schedule.sst_all_day === 'Y' ?
                                    'point_ing' :
                                    new Date() >= new Date(schedule.sst_edate) ?
                                    'point_done' :
                                    new Date() >= new Date(schedule.sst_sdate) && new Date() <= new Date(schedule.sst_edate) ?
                                    'point_ing' :
                                    'point_gonna';
                                const sst_sdate_e1 = new Date(schedule.sst_sdate).toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                const sst_sdate_e2 = new Date(schedule.sst_edate).toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });

                                if (!isNaN(scheduleLat) && !isNaN(scheduleLng)) {
                                    scheduleCount++;
                                    scheduleMarkerCoordinates.push({
                                        lat: scheduleLat,
                                        lng: scheduleLng
                                    });
                                    try {
                                        createGoogleScheduleMarker(scheduleLat, scheduleLng, scheduleCount, schedule.sst_title,
                                            sst_sdate_e1, sst_sdate_e2, status
                                        )
                                    } catch (error) {
                                        console.error("Error creating scheduleMarker:", error);
                                    }

                                    if (scheduleCount === 1) {
                                        startX = scheduleLat;
                                        startY = scheduleLng;
                                    } else if (scheduleCount === memberData.schedules.length) {
                                        endX = scheduleLat;
                                        endY = scheduleLng;
                                    }
                                }
                            });
                        }
                    }
                }
                markerData.count = scheduleCount; // markerData에 전체 스케줄 개수 저장
            }
        }


        function createGoogleScheduleMarker(lat, lng, index, title, startTime, endTime, status) {
            // AdvancedMarkerElement는 이제 동기적으로 사용 가능합니다.
            const {
                AdvancedMarkerElement
            } = google.maps.marker;

            // 스타일 (CSS-in-JS 방식으로 추가)
            const style = document.createElement('style');
            style.textContent = `
            .infobox5 {
                position: absolute;
                left: 50%;
                top: 100%;
                transform: translate(10%, -50%);
                background-color: #413F4A;
                padding: 0.3rem 0.8rem;
                border-radius: 0.4rem;
                z-index: 1;
                display: inline-block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                margin-top: 0.4rem;
                display: none;  /* 기본적으로 숨김 */ 
            }
            .infobox5 span {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .infobox5 .title {
                color: #E0FFFF;
                display: block;
                width: 100%;
                margin-bottom: 0.1rem;
                font-size: 12px !important;
                font-weight: 800 !important;
            }
            .infobox5 .date-wrapper {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }
            .infobox5 .date {
                color: #E6FFFF;
                margin-bottom: 0;
                font-size: 8px !important;
                font-weight: 700 !important;
            }
            .infobox5 .date + .date {
                margin-top: 0.05rem;
            }
            .infobox5.on {
                display: inline-block;  /* .on 클래스가 추가되면 표시 */
            }
        `;
            document.head.appendChild(style);

            // 마커 콘텐츠 생성
            const content = document.createElement('div');
            content.className = 'point_wrap point' + index;

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn point ' + status;
            content.appendChild(button);

            const spanInner = document.createElement('span');
            spanInner.className = 'point_inner';
            button.appendChild(spanInner);

            const spanTxt = document.createElement('span');
            spanTxt.className = 'point_txt';
            spanTxt.textContent = index;
            spanInner.appendChild(spanTxt);

            const infobox = document.createElement('div');
            infobox.className = 'infobox5 rounded_04 px_08 py_03 on';
            content.appendChild(infobox);

            const titleSpan = document.createElement('span');
            titleSpan.className = 'title';
            titleSpan.textContent = title;
            infobox.appendChild(titleSpan);

            const dateWrapper = document.createElement('div');
            dateWrapper.className = 'date-wrapper';
            infobox.appendChild(dateWrapper);

            const startDateSpan = document.createElement('span');
            startDateSpan.className = 'date';
            startDateSpan.textContent = 'S: ' + startTime;
            dateWrapper.appendChild(startDateSpan);

            const endDateSpan = document.createElement('span');
            endDateSpan.className = 'date';
            endDateSpan.textContent = 'E: ' + endTime;
            dateWrapper.appendChild(endDateSpan);

            // 마커 생성
            const scheduleMarker = new AdvancedMarkerElement({
                map: map,
                position: {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                },
                content: content,
                zIndex: 1,
            });


            scheduleMarkers.push(scheduleMarker)
        }

        function addGoogleProfileMarker(lat, lng, imageUrl) {
            // Check if AdvancedMarkerElement is available
            if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                const {
                    AdvancedMarkerElement
                } = google.maps.marker;

                // Rest of your code remains the same
                const content = document.createElement('div');
                content.className = 'point_wrap';
                const mapUserDiv = document.createElement('div');
                mapUserDiv.className = 'map_user';
                content.appendChild(mapUserDiv);
                const mapRtImgDiv = document.createElement('div');
                mapRtImgDiv.className = 'map_rt_img rounded_14';
                mapUserDiv.appendChild(mapRtImgDiv);
                const rectSquareDiv = document.createElement('div');
                rectSquareDiv.className = 'rect_square';
                mapRtImgDiv.appendChild(rectSquareDiv);
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = '이미지';
                img.onerror = function() {
                    this.src = 'https://app.smap.site/img/no_image.png';
                };
                rectSquareDiv.appendChild(img);

                const profileMarker = new AdvancedMarkerElement({
                    map: map,
                    position: {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    },
                    content: content,
                    zIndex: 2,
                });
                profileMarkers.push(profileMarker);
            } else {
                // Fallback to standard Marker if AdvancedMarkerElement is not available
                const profileMarker = new google.maps.Marker({
                    map: map,
                    position: {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    },
                    icon: {
                        url: imageUrl,
                        scaledSize: new google.maps.Size(40, 40),
                    },
                    zIndex: 2,
                });
                profileMarkers.push(profileMarker);
            }
            map.panBy(0, verticalCenterOffset); // Google Maps에서는 픽셀 단위로 이동
        }

        async function showGoogleOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus) {
            const optBottom = document.querySelector('.opt_bottom');

            // 지도 이동을 위한 함수
            function moveMap() {
                const center = map.getCenter();
                const lat = parseFloat(startX);
                const lng = parseFloat(startY);

                if (optBottom && optBottom.style.transform === 'translateY(0px)') {
                    // opt_bottom이 열려 있을 때
                    map.setCenter({
                        lat: lat,
                        lng: lng
                    });

                    // setCenter 완료 후 panBy 실행
                    google.maps.event.addListenerOnce(map, 'idle', function() {
                        map.panBy(0, 180);
                    });
                } else {
                    // opt_bottom이 닫혀 있을 때
                    map.setCenter({
                        lat: lat,
                        lng: lng
                    });
                }

                // 현재 줌 레벨 유지
                const currentZoom = map.getZoom();
                // map.setZoom(currentZoom);
            }

            // 지도 이동 실행
            moveMap();

            // 나머지 코드는 그대로 유지...
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            // 전체 경로 좌표 배열 생성
            const waypoints = [{
                lat: parseFloat(startX),
                lng: parseFloat(startY)
            }].concat(
                scheduleMarkerCoordinates.map(coordinate => ({
                    lat: coordinate.lat,
                    lng: coordinate.lng,
                }))
            );

            // 경로 요청 옵션
            const request = {
                origin: waypoints[0],
                destination: waypoints[waypoints.length - 1],
                waypoints: waypoints
                    .slice(1, -1)
                    .map(waypoint => ({
                        location: waypoint,
                        stopover: true
                    })),
                travelMode: google.maps.TravelMode.WALKING,
                unitSystem: google.maps.UnitSystem.METRIC,
            };

            // 경로 요청
            directionsService.route(request, function(response, status) {
                if (status === 'OK') {
                    // 기존 경로 제거
                    clearMapElements(polylines);
                    polylines = []; // 폴리라인 배열 초기화
                    // 경로 폴리라인 가져오기
                    const path = response.routes[0].overview_path;

                    // 그라데이션 색상 배열 생성
                    const gradient = createGradientGoogle(path);

                    // 폴리라인을 여러 개로 분할하여 각각에 색상 적용
                    for (let i = 0; i < path.length - 1; i++) {
                        const polyline = new google.maps.Polyline({
                            path: [path[i], path[i + 1]],
                            strokeColor: gradient[i],
                            strokeWeight: 5,
                            geodesic: true,
                            strokeOpacity: 0.5,
                        });
                        polyline.setMap(map);
                        polylines.push(polyline); // 생성된 폴리라인을 배열에 추가
                    }

                    // 각 구간 정보 출력 및 슬라이드 업데이트
                    const legs = response.routes[0].legs;
                    let totalDistance = 0;
                    let totalDuration = 0;

                    // sllt_json_walk에 저장할 데이터
                    let walkingData = [];

                    for (let i = 0; i < legs.length; i++) {
                        const leg = legs[i];

                        // duration과 distance가 undefined인 경우 빈 문자열("")로 설정
                        const distance = leg.distance?.value ? leg.distance.value / 1000 : "";
                        const duration = leg.duration?.value ? leg.duration.value / 60 : "";

                        totalDistance += distance ? parseFloat(distance) : 0; // distance가 빈 문자열이면 0으로 처리
                        totalDuration += duration ? parseFloat(duration) : 0; // duration이 빈 문자열이면 0으로 처리

                        console.log(`구간 ${i + 1}:`);
                        console.log(` 거리: ${distance ? distance.toFixed(2) : ""} km`);
                        console.log(` 소요 시간: ${duration ? duration.toFixed(0) : ""} 분`);
                        console.log(` 출발지: ${leg.start_address}`);
                        console.log(` 도착지: ${leg.end_address}`);
                        console.log('--------------------');

                        // 슬라이드 업데이트 (이전 로직 유지)
                        if (i > 0) {
                            // sllt_json_walk 데이터 추가
                            walkingData.push({
                                distance: distance ? distance.toFixed(2) : "",
                                duration: duration ? duration.toFixed(0) : "",
                                start_address: leg.start_address,
                                end_address: leg.end_address,
                            });

                            const slideSelector = `.swiper-slide.optimal_box[aria-label^="${(i * 2)} / "]`;
                            const slides = document.querySelectorAll(slideSelector);
                            slides.forEach(slide => {
                                slide.innerHTML =
                                    duration || distance ?
                                    `
                                        <p class="fs_23 fw_700 optimal_time">${duration}<span class="fs_14"><?= translate('분', $userLang) ?></span></p>
                                        <p class="fs_12 text_light_gray optimal_tance">${distance}km</p>
                                    ` :
                                    "";
                            });
                        }
                    }

                    console.log(`총 거리: ${totalDistance.toFixed(2)} km`);
                    console.log(`총 소요 시간: ${totalDuration.toFixed(0)} 분`);

                    // sllt_json_text에 저장할 데이터
                    let pathData = path.map((coord, index) => ({
                        lat: coord.lat(),
                        lng: coord.lng(),
                        color: gradient[index], // 색상 정보 추가
                    }));

                    // 성공 시 ajax로 DB에 log json 추가
                    var sgdt_idx = $('#pedestrian_path_modal_sgdt_idx').val();

                    var form_data = new FormData();
                    form_data.append('act', 'loadpath_add');
                    form_data.append('sgdt_idx', sgdt_idx);
                    form_data.append('sllt_json_text', JSON.stringify(pathData)); // path 데이터 저장
                    form_data.append('sllt_json_walk', JSON.stringify(walkingData)); // legs 데이터 저장
                    form_data.append('event_start_date', '<?= $s_date ?>');
                    form_data.append("sllt_language", '<?= $userLang ?>');

                    $.ajax({
                        url: './schedule_update',
                        enctype: 'multipart/form-data',
                        data: form_data,
                        type: 'POST',
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 5000,
                        success: function(data) {
                            if (data === 'Y') {
                                // GA 이벤트 전송
                                gtag('event', 'show_optimal_path', {
                                    'event_category': 'optimal_path',
                                    'event_label': 'show',
                                    'user_id': '<?= $_SESSION["_mt_idx"] ?>',
                                    'platform': isAndroidDevice() ?
                                        'Android' : isiOSDevice() ?
                                        'iOS' : 'Unknown',
                                });
                            } else {
                                jalert('잘못된 접근입니다.');
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                } else {
                    console.error('Directions request failed due to ' + status);
                    jalert('경로 데이터를 받아오는데 실패했습니다.');
                }
            });
        }

        // 색상 그라데이션 생성 함수
        function createGradientGoogle(pathLength) {
            const colors = [
                '#FF0000', // 빨간색
                '#FFA500', // 주황색
                '#FFFF00', // 노란색
                '#00FF00', // 초록색
                '#0000FF', // 파란색
                '#000080', // 남색
                '#800080', // 보라색
            ];
            const gradient = [];
            const steps = pathLength.length - 1;
            const colorSteps = colors.length - 1; // 색상 단계 수

            for (let i = 0; i <= steps; i++) {
                const colorIndex = Math.floor(i / steps * colorSteps); // 현재 색상 인덱스
                const nextColorIndex = Math.min(colorIndex + 1, colorSteps); // 다음 색상 인덱스
                const ratio = (i / steps * colorSteps) - colorIndex; // 현재 색상 구간 내 비율

                const color = interpolateColor(colors[colorIndex], colors[nextColorIndex], ratio);
                gradient.push(color);
            }

            return gradient;
        }
    </script>
<?php } ?>
<script>
    $(document).ready(function() {
        mem_schedule(<?= $sgdt_row['sgdt_idx'] ?>);
        calcScreenOffset();
        f_get_box_list2();
        checkAdCount();
        fetchWeatherData();
    });

    function calcScreenOffset() {
        optBottomSelect = document.querySelector('.opt_bottom');
        bottomSheetHeight = optBottomSelect ? optBottomSelect.getBoundingClientRect().height : 0;
        verticalCenterOffset = (mapHeight - bottomSheetHeight) / 2;
    }

    function clearAllMapElements() {
        clearMapElements(profileMarkers);
        clearMapElements(scheduleMarkers);
        clearMapElements(markers);
        // clearMapElements(logMarkers);
        clearPolylines(); // 폴리라인을 위한 새로운 함수 사용
    }

    function clearMapElements(elements) {
        if (elements && elements.length > 0) {
            elements.forEach(element => {
                if (element.setMap) {
                    element.setMap(null); // 지도에서 요소 제거
                }
            });
            elements.length = 0; // 배열 초기화
        }
    }

    function clearPolylines() {
        if (polylines && polylines.length > 0) {
            polylines.forEach(polyline => {
                if (polyline.setMap) {
                    polyline.setMap(null); // 지도에서 폴리라인 제거
                }
            });
            polylines.length = 0; // 배열 초기화
        }
    }

    function processRouteData(responseData, walkData) {
        // JSON 문자열을 객체로 파싱
        let data = typeof responseData === 'string' ? JSON.parse(responseData) : responseData;

        // sllt_json_text 생성
        let sllt_json_text = [];
        data.features.forEach(feature => {
            if (feature.geometry.type === "LineString") {
                feature.geometry.coordinates.forEach(coord => {
                    let latlng = new Tmapv2.Point(coord[0], coord[1]);
                    let convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
                    sllt_json_text.push({
                        lat: convertPoint._lat,
                        lng: convertPoint._lng,
                        color: "#ff0000" // 모든 점에 대해 동일한 색상 사용
                    });
                });
            }
        });

        // sllt_json_walk 생성
        let walkDataParsed = JSON.parse(walkData);
        let sllt_json_walk = walkDataParsed.map(item => ({
            distance: parseFloat(item[1]).toFixed(2),
            duration: item[0]
        }));

        return {
            sllt_json_text: JSON.stringify(sllt_json_text),
            sllt_json_walk: JSON.stringify(sllt_json_walk)
        };
    }

    function fetchWeatherData() {
        var form_data = new FormData();
        form_data.append("act", "weather_get");

        $.ajax({
            url: "./index_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data) {
                    $('#top_weather_box').empty(); // 섹션 비우기
                    $('#top_weather_box').html(data);
                    try {
                        my_location_update();
                        // 광고보기 후 로그 표시 GA 이벤트 전송
                        gtag('event', 'index_ad', {
                            'event_category': 'show_log',
                            'event_label': 'show',
                            'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                            'platform': isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown')
                        });
                    } catch (err) {
                        console.log("Error in my_location_update: " + err);
                    }
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function loadMemberSchedule(sgdt_idx) {
        return new Promise((resolve, reject) => {
            var form_data = new FormData();
            form_data.append("act", "member_schedule_list");
            form_data.append("sgdt_idx", sgdt_idx);
            form_data.append("event_start_date", '<?= $s_date ?>');

            $.ajax({
                url: "./schedule_update",
                enctype: "multipart/form-data",
                data: form_data,
                type: "POST",
                async: true,
                contentType: false,
                processData: false,
                cache: true,
                timeout: 5000,
                dataType: 'json',
                success: function(data) {
                    // sllt_json_text 데이터 존재 여부 확인
                    if (data) {
                        generateScheduleHTML(data.members[sgdt_idx], sgdt_idx);
                        resolve(data); // data를 Promise에 전달하여 반환
                    } else {
                        console.log("No loadMemberSchedule data available");
                        resolve(null); // 또는 reject()를 사용하여 에러 처리
                    }
                },
                error: function(err) {
                    console.error('AJAX request failed: ', err);
                    reject(err);
                },
            });
        });
    }

    function trackButtonClick() {
        gtag('event', 'show_optimal_path', {
            'event_category': 'optimal_path',
            'event_label': 'show',
            'user_id': '<?= $_SESSION['_mt_idx'] ?>',
            'platform': isAndroid() ? 'Android' : (isiOS() ? 'iOS' : 'Unknown')
        });
    }

    function generateScheduleHTML(data, sgdt_idx) {
        // 1. 위치 정보 업데이트
        const locationContailer = document.getElementById('my_location_div');
        locationContailer.innerHTML = '';

        let locationHTML = `
            <div class="border-bottom  pb-3">
                <div class="task_header_tit">
                    <p class="fs_16 fw_600 line_h1_2 mr-3"><?= translate('현재 위치', $userLang) ?></p>
                    <div class="d-flex align-items-center justify-content-end">
                        <p class="move_txt fs_13 mr-3 style="color: ${data.battery_info.color};">${data.location_info.mlt_speed > 1 ? '이동중' : ''}</p>
                        <p class="d-flex bettery_txt fs_13">
                            <span class="d-flex align-items-center flex-shrink-0 mr-2">
                                <img src="${data.battery_info.image}" width="14px" class="battery_img" alt="베터리시용량">
                            </span>
                            <span class="battery_percentage" style="color: ${data.battery_info.color};">${data.location_info.mlt_battery}%</span>
                        </p>
                    </div>
                </div>
                <p class="fs_14 fw_500 text_light_gray text_dynamic line_h1_3 mt-2" style="white-space: pre-line;">${data.member_info.mt_gu ? data.member_info.mt_gu : ''} ${data.member_info.mt_dong ? data.member_info.mt_dong : ''}
                </p>
            </div>
        `;

        locationContailer.innerHTML = locationHTML;

        // 2. 일정 컨테이너 업데이트
        const scheduleContainer = document.querySelector('.task_body_cont');
        scheduleContainer.innerHTML = ''; // 기존 내용 지우기

        if (data.schedules.length === 0) {
            // 일정 없을 때 메시지 표시
            scheduleContainer.innerHTML = `
                <div class="pt-5">
                    <button type="button" class="btn w-100 rounded add_sch_btn" onclick="trackButtonClick(); location.href='./schedule_form?sdate=<?= $s_date ?>&sgdt_idx=${sgdt_idx}'">
                        <i class="xi-plus-min mr-3"></i> <?= translate('일정을 추가해보세요!', $userLang) ?>
                    </button>
                </div>
            `;
        } else {
            // 3. 일정 정보를 담은 HTML 생성
            let scheduleSpecificHTML = `
                    <div class="task_body_tit">
                        <p class="fs_16 fw_600 line_h1_2"><?= translate('일정', $userLang) ?><span class="text_light_gray fs_14 ml-1">(${data.schedules.length} <?= translate('개', $userLang) ?>)</span></p>
                        <button type="button" class="btn fs_12 fw_500 h-auto w-auto text-primary optimal_btn" onclick="pedestrian_path_modal('${data.schedules[0].sgdt_idx}')"><?= translate('최적경로 표시하기', $userLang) ?><i class="xi-angle-right-min fs_13"></i></button>
                    </div>
                    <div class="task_body_cont num_point_map">
                        <div class="">
                            <div class="swiper task_slide">
                                <div class="swiper-wrapper" aria-live="polite">
                                    ${data.schedules.map((item, index) => `
                                        <div class="swiper-slide task_point_box" onclick="map_panto('${item.sst_location_lat}','${item.sst_location_long}')" role="group" aria-label="${index + 1} / ${data.schedules.length}" style="width: 45.5px;">
                                            <div class="task point_${getPointStatus(item)}">
                                                <span class="point_inner">
                                                    <span class="point_txt">${index + 1}</span>
                                                </span>
                                            </div>
                                            <p class="text_lightgray fs_13 mt-1 status_txt ${getPointStatus(item)}_txt">${getStatusText(item)}</p>
                                        </div>
                                        ${index < data.schedules.length - 1 ? '<div class="swiper-slide optimal_box"></div>' : ''}
                                    `).join('')}
                                </div>
                                <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal swiper-pagination-lock"><span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 1" aria-current="true"></span></div>
                            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                        </div>
                    </div>
            `;

            if (data) {
                state.pathData = JSON.parse(data.sllt_json_text);
                state.walkingData = JSON.parse(data.sllt_json_walk);
                state.isDataLoaded = true;

                // state.pathData를 pedestrianData 형식으로 가공
                const processedPathData = {
                    members: {
                        [sgdt_idx]: {
                            sllt_json_walk: JSON.stringify(state.walkingData)
                        }
                    }
                };
                createOrUpdateSlidesForMember(sgdt_idx, processedPathData);
            } else {
                f_get_box_list();
                f_get_box_list2();
            }

            // 4. 생성된 HTML을 컨테이너에 추가 (현재 위치 정보 먼저 추가)
            scheduleContainer.innerHTML = scheduleSpecificHTML;

            // 5. Swiper 슬라이드 다시 초기화
            if (typeof task_swiper !== 'undefined') {
                task_swiper.destroy();
            }
            task_swiper = new Swiper(".task_slide", {
                slidesPerView: 8,
                pagination: {
                    el: ".task_slide .swiper-pagination",
                    clickable: true,
                },
            });
        }
    }

    function getPointStatus(item) {
        const currentDate = new Date();
        const scheduleStartDate = new Date(item.sst_sdate);
        const scheduleEndDate = new Date(item.sst_edate);

        if (item.sst_all_day === 'Y') {
            return 'ing';
        } else if (currentDate >= scheduleEndDate) {
            return 'done';
        } else if (currentDate >= scheduleStartDate && currentDate <= scheduleEndDate) {
            return 'ing';
        } else {
            return 'gonna';
        }
    }

    function getStatusText(item) {
        const currentDate = new Date();
        const scheduleStartDate = new Date(item.sst_sdate);
        const scheduleEndDate = new Date(item.sst_edate);

        if (item.sst_all_day === 'Y') {
            return "<?= translate('하루종일', $userLang) ?>";
        } else if (currentDate >= scheduleEndDate) {
            return "<?= translate('완료', $userLang) ?>";
        } else if (currentDate >= scheduleStartDate && currentDate <= scheduleEndDate) {
            return "<?= translate('진행중', $userLang) ?>";
        } else {
            return "<?= translate('진행예정', $userLang) ?>";
        }
    }

    // 디바운스 함수
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function processPathDataGoogle(data, sgdt_idx) {
        if (!data.members[sgdt_idx].sllt_json_text) {
            console.warn("No sllt_json_text data available.");
            return; // 함수 실행 종료
        }

        state.pathData = JSON.parse(data.members[sgdt_idx].sllt_json_text);
        state.walkingData = JSON.parse(data.members[sgdt_idx].sllt_json_walk);
        state.isDataLoaded = true;

        // 지도에 경로 그리기
        drawPathOnMap();
    }

    function drawPathOnMap() {
        if (!Array.isArray(state.pathData) || state.pathData.length === 0 || !map) {
            console.log("Path data or map not available or invalid format");
            return;
        }

        console.log("Drawing path on map");

        // 기존 폴리라인 제거
        clearMapElements(polylines);

        const pathCoordinates = state.pathData.map(
            coord => new google.maps.LatLng(coord.lat, coord.lng)
        );

        const gradient = createGradientGoogle(pathCoordinates);

        for (let i = 0; i < pathCoordinates.length - 1; i++) {
            const polyline = new google.maps.Polyline({
                path: [pathCoordinates[i], pathCoordinates[i + 1]],
                strokeColor: gradient[i],
                strokeWeight: 5,
                geodesic: true,
                strokeOpacity: 0.5,
            });
            polyline.setMap(map);
            polylines.push(polyline);
        }

        console.log("Path drawn on map");
    }

    async function mem_schedule(sgdt_idx, mlt_lat = 37.5666805, mlt_lng = 126.9784147) {
        try {
            const data = await loadMemberSchedule(sgdt_idx);
            console.log("받은 데이터:", data);

            // 지도 초기화 및 마커 설정, 경로 확인을 Promise.all로 감싸서 동시에 실행
            await Promise.all([
                initializeMapAndMarkers(data.members, sgdt_idx),
                pedestrian_path_check(sgdt_idx)
            ]);

            // 모든 작업이 완료된 후 지도 중심 설정
            map_panto(data.members[sgdt_idx].member_info.my_lat, data.members[sgdt_idx].member_info.mt_long);

            console.log("Map data and member schedule loaded successfully");
        } catch (error) {
            console.error("Failed to load map data or member schedule:", error);
            showErrorToUser("일정 로딩 중 오류가 발생했습니다. 다시 시도해 주세요.");
        }
    }


    async function createOrUpdateSlidesForMember(memberId, pedestrianData) {
        console.log("Creating or updating slides for member", memberId);

        if (!groupMemberSlides[memberId]) {
            groupMemberSlides[memberId] = Array.from(document.querySelectorAll('.swiper-slide.optimal_box'));
        }

        const slidesContainers = groupMemberSlides[memberId];

        if (pedestrianData && pedestrianData.members[memberId] && pedestrianData.members[memberId].sllt_json_walk) {
            const walkingData = JSON.parse(pedestrianData.members[memberId].sllt_json_walk);
            console.log("Walking data parsed", walkingData);

            await waitForDOM();

            walkingData.forEach((leg, index) => {
                const duration = leg.duration !== undefined ? leg.duration : "";
                const distance = leg.distance !== undefined ? leg.distance : "";

                const slideSelector = `.swiper-slide.optimal_box[aria-label^="${((index + 1) * 2)} / "]`;
                const slides = document.querySelectorAll(slideSelector);
                slides.forEach(slide => {
                    slide.innerHTML =
                        duration || distance ?
                        `
                        <p class="fs_23 fw_700 optimal_time">${duration}<span class="fs_14"><?= translate('분', $userLang) ?></span></p>
                        <p class="fs_12 text_light_gray optimal_tance">${distance}km</p>
                        ` :
                        "";
                    console.log(`Slide ${index + 1} updated for member ${memberId}`);
                });
            });
        } else {
            console.log("No walking data available, setting loading state");
            slidesContainers.forEach(container => {
                container.innerHTML = `
                    <div class="optimal_time loading-animation"></div>
                    <div class="optimal_tance loading-animation"></div>
                `;
            });
        }
    }

    function waitForDOM() {
        return new Promise(resolve => {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(resolve, 0);
            } else {
                document.addEventListener('DOMContentLoaded', resolve);
            }
        });
    }

    // 페이지 로드 시 초기화
    document.addEventListener('DOMContentLoaded', () => {
        console.log("DOM fully loaded and parsed");
        // 여기에 필요한 초기화 로직을 추가할 수 있습니다.
    });

    async function initializeMapAndMarkers(data, sgdt_idx) {
        // map.setZoom(16);
        if ('ko' === '<?= $userLang ?>') {
            await initNaverMap(data, sgdt_idx);
        } else if ('ko' !== '<?= $userLang ?>') {
            await initGoogleMap(data, sgdt_idx);
        } else {
            throw new Error('지도 API를 초기화할 수 없습니다.');
        }
    }

    function showErrorToUser(message) {
        // 사용자에게 오류 메시지를 표시하는 함수
        // 예: alert(message) 또는 더 세련된 UI 요소 사용
        alert(message);
    }

    // Ensure this function is attached to a button correctly
    // document.getElementById('yourButtonId').onclick = showAdWithAdData;
    // 최적경로 구하기
    function showOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus) {
        // 초기화 작업
        let viaPoints = [];
        let passList = '';
        let totalWalkingTimeJson = null;
        let requestData = {};

        // 스케줄 마커들의 좌표를 추출하여 경유지로 설정
        viaPoints = scheduleMarkerCoordinates.map(function(coordinate, index) {
            if (index === 0 || index === scheduleMarkerCoordinates.length - 1) {
                // 출발지 또는 도착지인 경우, 무시하고 continue
                return null;
            }
            return {
                "viaPointId": "point_" + index,
                "viaPointName": "point_" + index,
                "viaY": coordinate.lat, // 수정
                "viaX": coordinate.lng, // 수정
                "viaTime": 600
            };
        }).filter(function(point) {
            return point !== null; // 출발지와 도착지를 제외하기 위해 null을 제거
        });

        // 좌표값만을 추출하여 passList에 저장
        passList = viaPoints.map(function(point) {
            // 좌표값을 EPSG3857로 변환
            var latlng = new Tmapv2.Point(point.viaY, point.viaX);
            var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
            return point.viaX + "," + point.viaY;
        }).join("_");

        // 직선거리 계산
        const distance = getDistance(startY, startX, scheduleMarkerCoordinates, 5);
        const straightDistance = distance.toFixed(2);
        if (straightDistance >= 5) {
            jalert('일정과 일정 사이의 거리가 <br>너무 멀어 최적경로 표기가 어렵습니다.(' + straightDistance + 'km)');
            return false;
        }

        // passList가 존재할 때만 데이터에 passList를 포함시킴
        requestData = {
            "reqCoordType": "WGS84GEO",
            "resCoordType": "EPSG3857",
            "startName": "출발",
            "startX": startY, // 수정
            "startY": startX, // 수정
            "endName": "도착",
            "endX": endY, // 수정
            "endY": endX, // 수정
            "endID": "goal",
        };

        if (passList) {
            requestData.passList = passList; // 경유지 좌표값 추가
        }

        const dataToSend = JSON.stringify(requestData);

        var headers = {};
        headers["appKey"] = "6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq";

        // 최적 경로 요청
        $.ajax({
            method: "POST",
            headers: headers,
            url: "https://apis.openapi.sk.com/tmap/routes/pedestrian?version=1&format=json&callback=result",
            async: false,
            contentType: "application/json",
            data: dataToSend,
            success: function(response) {
                if (response && response.features && response.features.length > 0) {
                    var resultData = response.features;
                    var totalidstance = ((resultData[0].properties.totalDistance) / 1000).toFixed(1);
                    var totalTime = ((resultData[0].properties.totalTime) / 60).toFixed(0);

                    var elementWithAriaLabel = $('.optimal_box').filter(function() {
                        return $(this).attr('aria-label') !== undefined;
                    });

                    var labelText = elementWithAriaLabel.attr('aria-label').split('/')[1].trim();

                    // 각 경유지까지의 예상 소요 시간 계산 함수 호출
                    calculateWalkingTime(startX, startY, endX, endY, scheduleMarkerCoordinates, function(totalWalkingTime) {
                        totalWalkingTimeJson = totalWalkingTime;
                    });

                    // 성공 시 ajax로 DB에 log json 추가
                    var sgdt_idx = $('#pedestrian_path_modal_sgdt_idx').val();

                    let result = processRouteData(JSON.stringify(response), JSON.stringify(totalWalkingTimeJson));

                    var form_data = new FormData();
                    form_data.append("act", "loadpath_add");
                    form_data.append("sgdt_idx", sgdt_idx);
                    form_data.append("sllt_json_text", result.sllt_json_text);
                    form_data.append("sllt_json_walk", result.sllt_json_walk);
                    form_data.append("event_start_date", '<?= $s_date ?>');
                    form_data.append("sllt_language", '<?= $userLang ?>');

                    $.ajax({
                        url: "./schedule_update",
                        enctype: "multipart/form-data",
                        data: form_data,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 5000,
                        success: function(data) {
                            if (data != 'Y') {
                                jalert('잘못된 접근입니다.');
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    // 최적경로 표시 GA 이벤트 전송
                    gtag('event', 'show_optimal_path', {
                        'event_category': 'optimal_path',
                        'event_label': 'show',
                        'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                        'platform': isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown')
                    });
                } else {
                    console.error('유효하지 않은 API 응답 데이터');
                    jalert('경로 데이터를 받아오는데 실패했습니다.');
                }
            },
            error: function(request, status, error) {
                console.log(request.responseJSON.error.code);
                console.log(request.responseJSON.error);
                if (request.responseJSON.error.code == '3102') {
                    var errorMessage = '해당 서비스가 지원되지 않는 구간이라 <br>최적 경로 안내가 어려워요.';
                } else if (request.responseJSON.error.code == '3002') {
                    var errorMessage = '길안내를 제공하지 않는 부분이 있어서 <br>최적 경로 안내가 어려워요.';
                } else if (request.responseJSON.error.code == '1009') {
                    var errorMessage = '일부 구간이 너무 멀어서 <br>최적 경로 안내가 힘들어요.';
                } else if (request.responseJSON.error.code == '9401') {
                    var errorMessage = '최적경로 조회는 <br>두 개 이상의 일정이 입력되었을 때만 <br>이용할 수 있어요.';
                } else if (request.responseJSON.error.code == '1100') {
                    var errorMessage = '최적경로는 <br>최대 7개까지의 일정의 경로를 표시 가능해요.';
                } else if (request.responseJSON.error.code == '2200') {
                    var errorMessage = '최적경로 API에서 지원하지는 주소 범위입니다.';
                } else {
                    var errorMessage = '시스템 오류입니다.';
                }

                jalert(errorMessage);
            }
        });
    }

    async function pedestrian_path_check(sgdt_idx) {
        return new Promise((resolve, reject) => { // Promise 반환
            var form_data = new FormData();
            form_data.append("act", "pedestrian_path_chk");
            form_data.append("sgdt_idx", sgdt_idx);
            form_data.append("event_start_date", '<?= $s_date ?>');

            $.ajax({
                url: "./schedule_update",
                enctype: "multipart/form-data",
                data: form_data,
                type: "POST",
                async: true,
                contentType: false,
                processData: false,
                cache: true,
                timeout: 5000,
                dataType: 'json',
                success: function(data) {
                    // sllt_json_text 데이터 존재 여부 확인
                    if (data &&
                        data.result === 'Y' &&
                        data.members[sgdt_idx]) {
                        if ('ko' === '<?= $userLang ?>') {
                            processPathDataNaver(data, sgdt_idx);
                        } else if (typeof google !== 'undefined') {
                            processPathDataGoogle(data, sgdt_idx);
                            drawPathOnMap(); // 경로 그리기 함수 호출
                        }
                        resolve(data); // data 반환
                    } else {
                        console.log("No path data available or result is not 'Y' or no sllt_json_text");
                        resolve(null);
                    }
                },
                error: function(err) {
                    console.error('AJAX request failed: ', err);
                    reject(err); // 에러 발생 시 reject
                },
            });
        });
    }

    //최적경로 표시 모달 띄우기
    function pedestrian_path_modal(sgdt_idx) {
        if (sgdt_idx) {
            $('#pedestrian_path_modal_sgdt_idx').val(sgdt_idx);
        }
        var form_data = new FormData();
        form_data.append("act", "load_path_chk");
        form_data.append("sgdt_idx", sgdt_idx);
        form_data.append("event_start_date", '<?= $s_date ?>');
        $.ajax({
            url: "./schedule_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                if (data.result == 'Y' && data.path_count_day == 0) {
                    $('#pathType').text(data.path_type); // 모달에 표시
                    $('#pathCountday').text("일 사용횟수를 모두 사용하셨습니다."); // 모달에 표시
                    $('#pathCountmonth').text("이번달 " + data.path_count_month + "회 사용 가능"); // 모달에 표시
                    $('#showPathButton').removeClass('d-none');
                    $('#showPathAdButton').addClass('d-none');
                    $('#showPathButton').prop('disabled', true);
                    $('#path_day_count').val(data.path_count_day);
                    if (data.path_type == 'Pro') {
                        $('#pathContent').addClass('d-none');
                    } else {
                        $('#pathContent').removeClass('d-none');
                    }

                    $('#optimal_modal').modal('show');

                } else if (data.result == 'Y') {
                    $('#pathType').text(data.path_type); // 모달에 표시
                    $('#pathCountday').text("금일 " + data.path_count_day + "회 사용 가능 "); // 모달에 표시
                    $('#pathCountmonth').text("이번달 " + data.path_count_month + "회 사용 가능"); // 모달에 표시

                    if (data.ad_count == 0 && data.path_type == 'Basic') {
                        $('#showPathButton').addClass('d-none');
                        $('#showPathButton').prop('disabled', true);
                        $('#showPathAdButton').removeClass('d-none');
                        $('#showPathAdButton').prop('disabled', false);
                    } else {
                        $('#showPathButton').removeClass('d-none');
                        $('#showPathAdButton').addClass('d-none');
                        $('#showPathButton').prop('disabled', false);
                    }
                    $('#path_day_count').val(data.path_count_day);
                    if (data.path_type == 'Pro') {
                        $('#pathContent').addClass('d-none');
                    } else {
                        $('#pathContent').removeClass('d-none');
                    }
                    $('#optimal_modal').modal('show');
                } else if (data.result == 'Noschedule') {
                    jalert("최적경로 조회는 <br>두 개 이상의 일정이 입력되었을 때만 <br>이용할 수 있어요.");
                } else if (data.result == 'NoLocation') {
                    jalert("장소가 빠진 일정이 있어<br> 최적 경로를 찾을 수 없습니다.<br> 확인 부탁드려요!");
                } else {
                    jalert('잘못된 접근입니다.');
                }
            },
            error: function(err) {
                console.log(err);
            },
        });

    }

    //손으로 바텀시트 움직이기
    document.addEventListener("DOMContentLoaded", function() {
        // console.log('bottom');
        var startY = 0;
        var isDragging;

        var optBottom = document.querySelector(".opt_bottom");
        if (optBottom) {
            optBottom.addEventListener("touchstart", function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
            });
            optBottom.addEventListener("touchmove", function(event) {
                var currentY = event.touches[0].clientY; // 현재 터치 좌표
                var deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    var isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    var newTransformValue = isVisible ? "translateY(0)" : "translateY(82%)";
                    optBottom.style.transform = newTransformValue;
                }
            });


            optBottom.addEventListener('mousedown', function(event) {
                startY = event.clientY; // 클릭 시작 좌표 저장
                isDragging = true;
            });

            document.addEventListener('mousemove', function(event) {
                if (isDragging) {
                    var currentY = event.clientY; // 현재 마우스 좌표
                    var deltaY = currentY - startY; // 움직임의 차이 계산

                    // 움직임이 일정 값 이상이면 보이거나 숨김
                    if (Math.abs(deltaY) > 50) {
                        var isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                        var newTransformValue = isVisible ? 'translateY(0)' : 'translateY(82%)';
                        optBottom.style.transform = newTransformValue;
                    }
                }
            });

            document.addEventListener('mouseup', function() {
                isDragging = false;
            });

        } else {
            console.error("요소를 찾을 수 없습니다.");
        }
    });

    function toggleInfobox() {
        var infoboxes = document.getElementsByClassName('infobox5');
        var img = document.getElementById('infoboxImg');

        // 이미지 경로 변경
        if (img.src.includes('ico_info_on.png')) {
            img.src = './img/ico_info_off.png';
            for (var i = 0; i < infoboxes.length; i++) {
                infoboxes[i].classList.remove('on');
            }
        } else {
            img.src = './img/ico_info_on.png';
            for (var i = 0; i < infoboxes.length; i++) {
                infoboxes[i].classList.add('on');
            }
        }

    }

    // 초대링크 닫을시
    function floating_link_cancel() {
        document.getElementById('first_floating_modal').classList.remove('on');
        document.getElementById('group_make_modal').classList.add('on');
    }
    //배너 슬라이더

    var ban_swiper = new Swiper(".banSwiper", {
        //     autoplay: {
        //         delay: 2500,
        //         disableOnInteraction: false,
        //   },
        autoHeight: true,
        pagination: {
            el: ".banSwiper .swiper-pagination",
            type: "fraction",
        },
        navigation: {
            nextEl: ".banSwiper .swiper-button-next",
            prevEl: ".banSwiper .swiper-button-prev",
        },
    });

    showPathButton.addEventListener('click', function(event) {
        var pathCount = document.getElementById('path_day_count');

        if (pathCount.value == 0) {
            jalert('오늘 사용할 최적경로를 모두 사용하였습니다.');
            return;
        }

        const geocoder = new google.maps.Geocoder();
        const latlng = new google.maps.LatLng(startX, startY); // 경도, 위도 순서 주의

        geocoder.geocode({
            location: latlng
        }, (results, status) => {
            if (status === "OK") {
                if (results[0]) {
                    const country = results[0].address_components.find(component =>
                        component.types.includes("country")
                    );

                    if (country && country.short_name === "KR") {
                        // 대한민국 내에 있는 경우
                        Promise.resolve(showOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus))
                            .catch(error => {
                                console.error("showOptimalPath Error:", error);
                                return showGoogleOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus);
                            })
                            .finally(() => {
                                loadMemberSchedule($('#pedestrian_path_modal_sgdt_idx').val());
                                $('#optimal_modal').modal('hide');
                            });
                    } else {
                        // 대한민국 외의 경우
                        showGoogleOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus);
                        loadMemberSchedule($('#pedestrian_path_modal_sgdt_idx').val());
                        $('#optimal_modal').modal('hide');
                    }
                } else {
                    console.error("No results found");
                    // 결과가 없는 경우 처리
                }
            } else {
                console.error("Geocoder failed due to: " + status);
                // Geocoding 실패 처리
            }
        });
    });

    function getAdData() {
        return <?= $ad_data ?>;
    }

    // 경로 데이터 처리 - 네이버 지도
    function processPathDataNaver(data, sgdt_idx) {
        if (!data.members[sgdt_idx].sllt_json_text) {
            console.warn("No sllt_json_text data available.");
            return; // 함수 실행 종료
        }

        var jsonString = data.members[sgdt_idx].sllt_json_text;
        // resultData를 JSON 객체로 변환
        const resultDataObj = JSON.parse(jsonString);
        // var totalWalkingTime = JSON.parse(data.members[sgdt_idx].sllt_json_walk);

        // var start = jsonString.indexOf('{"type":"FeatureCollection"');
        // var end = jsonString.lastIndexOf('}') + 1;
        // if (start === -1 || end === 0) {
        //     console.log("Invalid JSON string");
        //     return;
        // }

        // var validJsonString = jsonString.substring(start, end);
        // var ajaxData = JSON.parse(validJsonString);
        // var resultData = ajaxData.features;

        if (!jsonString || jsonString.length === 0) {
            console.error("No features found in the JSON data.");
            return;
        }

        // var totalDistance = (resultData[0].properties.totalDistance / 1000).toFixed(1);
        // var totalTime = (resultData[0].properties.totalTime / 60).toFixed(0);

        // var elementWithAriaLabel = $('.optimal_box').filter(function() {
        //     return $(this).attr('aria-label') !== undefined;
        // });

        // loadMemberSchedule(sgdt_idx);

        // if (elementWithAriaLabel.length === 0) {
        //     console.error("No element with aria-label found");
        //     return;
        // }

        // var labelText = elementWithAriaLabel.attr('aria-label').split('/')[1].trim();

        // 경로 그리기 및 마커 설정을 비동기적으로 처리
        setTimeout(() => {
            drawPathAndMarkers(map, resultDataObj);
            // updateOptimalBoxes(totalWalkingTime, labelText);
        }, 100);
    }

    function hideLoader() {
        if (typeof window.FakeLoader !== 'undefined' && typeof window.FakeLoader.hideOverlay === 'function') {
            window.FakeLoader.hideOverlay();
        } else {
            console.log("FakeLoader not available, hiding loader skipped");
        }
    }

    function retryDrawPath(map, resultData, totalWalkingTime, labelText, retryCount = 0) {
        if (retryCount >= 5) {
            console.error("Failed to draw the path after multiple attempts.");
            return;
        }

        console.warn(`Retrying to draw path, attempt: ${retryCount + 1}`);

        setTimeout(() => {
            drawPathAndMarkers(map, resultData);
            // updateOptimalBoxes(totalWalkingTime, labelText);

            if (!isPathDrawn(polylines)) {
                console.warn("Path not drawn correctly, retrying...");
                retryDrawPath(map, resultData, totalWalkingTime, labelText, retryCount + 1);
            }
        }, 1000 * (retryCount + 1)); // 재시도 간격을 점진적으로 늘립니다.
    }

    // isPathDrawn 함수 추가
    function isPathDrawn(polylines) {
        return polylines.length > 0 && polylines.every(polyline => polyline.getPath().getLength() > 0);
    }

    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // 지구의 반지름 (km)
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const d = R * c; // 두 지점 사이의 거리 (km)
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function drawPathAndMarkers(map, resultData) {
        // 기존에 그려진 라인 & 마커가 있다면 초기화
        if (resultdrawArr.length > 0) {
            for (var i in resultdrawArr) {
                resultdrawArr[i].setMap(null);
            }
            resultdrawArr = [];
        }

        drawInfoArr = [];
        polylines = [];
        location_markers = [];

        // 전체 경로를 저장할 배열
        var path = [];

        // resultData 배열을 순회하며 path 배열에 좌표 추가
        for (var i = 0; i < resultData.length; i++) {
            var lat = resultData[i].lat;
            var lng = resultData[i].lng;
            var convertChange = new naver.maps.LatLng(lat, lng);
            path.push(convertChange);
        }

        // path 배열의 길이가 2 이상인 경우에만 폴리라인 생성
        if (path.length > 1) {
            // 그라데이션 생성
            const gradient = createGradient(path.length);

            // 여러 개의 폴리라인 생성
            for (let i = 0; i < path.length - 1; i++) {
                const partialPath = [path[i], path[i + 1]];

                var polyline = new naver.maps.Polyline({
                    path: partialPath,
                    strokeColor: gradient[i],
                    strokeOpacity: 0.8,
                    strokeWeight: 5,
                    map: map,
                });

                resultdrawArr.push(polyline);
                polylines.push(polyline);
            }
        }
    }

    function createGradient(pathLength) {
        const colors = [
            '#FF0000', // 빨간색
            '#FFA500', // 주황색
            '#FFFF00', // 노란색
            '#00FF00', // 초록색
            '#0000FF', // 파란색
            '#000080', // 남색
            '#800080', // 보라색
        ];
        const gradient = [];
        const steps = pathLength - 1;
        const colorSteps = colors.length - 1; // 색상 단계 수

        for (let i = 0; i <= steps; i++) {
            const colorIndex = Math.floor(i / steps * colorSteps); // 현재 색상 인덱스
            const nextColorIndex = Math.min(colorIndex + 1, colorSteps); // 다음 색상 인덱스
            const ratio = (i / steps * colorSteps) - colorIndex; // 현재 색상 구간 내 비율

            const color = interpolateColor(colors[colorIndex], colors[nextColorIndex], ratio);
            gradient.push(color);
        }

        return gradient;
    }

    // 두 색상 사이의 중간 색상 계산 함수
    function interpolateColor(color1, color2, ratio) {
        const hex = (number) => {
            const hexStr = number.toString(16);
            return hexStr.length === 1 ? '0' + hexStr : hexStr;
        };

        const r = Math.ceil(parseInt(color1.substring(1, 3), 16) * (1 - ratio) + parseInt(color2.substring(1, 3), 16) * ratio);
        const g = Math.ceil(parseInt(color1.substring(3, 5), 16) * (1 - ratio) + parseInt(color2.substring(3, 5), 16) * ratio);
        const b = Math.ceil(parseInt(color1.substring(5, 7), 16) * (1 - ratio) + parseInt(color2.substring(5, 7), 16) * ratio);

        return '#' + hex(r) + hex(g) + hex(b);
    }

    function isPathDrawn(polylines) {
        return polylines.length > 0;
    }

    function retryDrawPath(map, resultData, totalWalkingTime, labelText, retryCount = 0) {
        if (retryCount >= 5) { // 재시도 횟수 제한
            console.error("Failed to draw the path after multiple attempts.");
            return;
        }

        // 기존 데이터 초기화
        resultdrawArr.forEach(item => item.setMap(null));
        resultdrawArr = [];
        polylines = [];
        location_markers = [];

        console.warn(`Retrying to draw path, attempt: ${retryCount + 1}`);
        // drawPathAndMarkers(map, resultData);

        if (!isPathDrawn(polylines)) {
            console.warn("Path not drawn correctly, retrying...");
            setTimeout(() => {
                retryDrawPath(map, resultData, totalWalkingTime, labelText, retryCount + 1);
            }, 1000); // 1초 후 재시도
        }
    }

    // 라인 위 방향 표시
    function makeMarker(map, position1, position2, index) {
        var ICON_GAP = 0;
        var ICON_SPRITE_IMAGE_URL = './img/map_direction.svg';
        var iconSpritePositionX = (index * ICON_GAP) + 1;
        var iconSpritePositionY = 1;

        var marker = new naver.maps.Marker({
            map: map,
            position: position1,
            title: 'map_maker' + index,
            icon: {
                url: ICON_SPRITE_IMAGE_URL,
                size: new naver.maps.Size(8, 8), // 이미지 크기
                // origin: new naver.maps.Point(iconSpritePositionX, iconSpritePositionY), // 스프라이트 이미지에서 클리핑 위치
                anchor: new naver.maps.Point(4, 4), // 지도상 위치에서 이미지 위치의 offset
                scaledSize: new naver.maps.Size(8, 8),
                origin: new naver.maps.Point(0, 0),
            }
        });

        var angle_t = f_get_angle(position2['x'], position2['y'], position1['x'], position1['y']);
        // console.log(position1['x'], position1['y'], position2['x'], position2['y'], angle_t);

        $("div[title|='map_maker" + index + "'").css('transform', 'rotate(' + angle_t + 'deg)');

        return marker;
    }
    // 방향구하기
    function f_get_angle(lat1, lon1, lat2, lon2) {
        var lat1 = lat1 * Math.PI / 180;
        if (lat2 == '') {
            var lat2 = lat1 * Math.PI / 180;
            var lon2 = lon1;
        } else {
            var lat2 = lat2 * Math.PI / 180;
        }
        var dLon = (lon2 - lon1) * Math.PI / 180;

        var y = Math.sin(dLon) * Math.cos(lat2);
        var x = Math.cos(lat1) * Math.sin(lat2) -
            Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLon);

        var brng = Math.atan2(y, x);

        return (((brng * 180 / Math.PI) + 360) % 360);
    }
    // 두 지점의 위도와 경도를 인자로 받아 직선거리를 계산하는 함수
    function getDistance(lon1, lat1, scheduleMarkerCoordinates, maxDistance) {
        // 경유지가 존재할 경우, 각 경유지 사이의 거리를 계산하여 총 거리에 더합니다.
        for (let i = 1; i < scheduleMarkerCoordinates.length; i++) {
            const curLat = scheduleMarkerCoordinates[i]._lat;
            const curLon = scheduleMarkerCoordinates[i]._lng;
            const segmentDistance = calculateSegmentDistance(lat1, lon1, curLat, curLon);

            // 현재까지의 총 거리와 경유지까지의 거리를 합산하여 최대 거리를 초과하는지 확인합니다.
            if (segmentDistance > maxDistance) {
                // 최대 거리를 초과하는 경우에는 반복문을 종료합니다.
                return segmentDistance;
                break;
            }

            // 다음 경유지부터 출발점으로 설정하여 새로운 segmentDistance를 계산합니다.
            lat1 = curLat;
            lon1 = curLon;
        }
        return 0;

    }
    // 두 지점 사이의 직선 거리를 계산하는 보조 함수
    function calculateSegmentDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // 지구의 반지름 (단위: km)
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = R * c; // 직선거리 (단위: km)
        return distance;
    }
    // 도(degree)를 라디안(radian)으로 변환하는 함수
    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }
    // 출발점, 도착지, 경유지까지의 예상 소요 시간을 계산하는 함수
    function calculateWalkingTime(startX, startY, endX, endY, scheduleMarkerCoordinates, callback) {
        var arr_distance = [];
        var completedRequests = 0;

        // 두 번째 경유지부터 마지막 경유지까지의 예상 소요 시간 계산
        for (var i = 1; i < scheduleMarkerCoordinates.length; i++) {
            getWalkingTime(scheduleMarkerCoordinates[i - 1].lat, scheduleMarkerCoordinates[i - 1].lng, scheduleMarkerCoordinates[i].lat, scheduleMarkerCoordinates[i].lng, function(totalTime, totalidstance) {
                arr_distance.push([totalTime, totalidstance]);
                completedRequests++;

                // 모든 요청이 완료되었을 때 콜백 함수 호출
                if (completedRequests === scheduleMarkerCoordinates.length - 1) {
                    callback(arr_distance);
                }
            });
        }
    }
    // getWalkingTime 함수는 비동기적으로 실행되며, 결과는 콜백 함수를 통해 반환됩니다.
    function getWalkingTime(startX, startY, endX, endY, callback) {
        var apiKey = '6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq';
        var apiUrl = 'https://apis.openapi.sk.com/tmap/routes/pedestrian?version=1&format=json&callback=result';

        // API 호출에 필요한 매개변수 설정
        var requestData = {
            "reqCoordType": "WGS84GEO",
            "resCoordType": "EPSG3857",
            "startName": "출발",
            "startX": startY, // 수정
            "startY": startX, // 수정
            "endName": "도착",
            "endX": endY, // 수정
            "endY": endX, // 수정
            "endID": "goal",
        };

        // API 요청 보내기
        $.ajax({
            method: "POST",
            url: apiUrl,
            headers: {
                "appKey": apiKey
            },
            contentType: "application/json",
            data: JSON.stringify(requestData),
            async: false, // 동기적 요청 (비동기적으로 설정할 경우 결과를 반환하기 전에 함수가 종료될 수 있음)
            success: function(response) {
                // API 응답에서 예상 소요 시간 추출
                var totalTime = ((response.features[0].properties.totalTime) / 60).toFixed(0);
                var totalidstance = ((response.features[0].properties.totalDistance) / 1000).toFixed(1);
                // 결과를 콜백 함수를 통해 반환
                callback(totalTime, totalidstance);
            },
            error: function(xhr, status, error) {
                console.error('API 요청 실패:', error);
                // 에러 발생 시 콜백 함수를 호출하여 에러를 반환
                callback(-1, -1);
            }
        });
    }

    function my_location_update() {
        var sgdt_idx = $('#sgdt_idx').val();
        var form_data = new FormData();
        form_data.append("act", "member_location_reload");
        form_data.append("sgdt_idx", sgdt_idx);
        $.ajax({
            url: "./schedule_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data) {
                    $('#my_location_div').empty(); // 섹션 비우기
                    $('#my_location_div').html(data);
                } else {
                    console.log("Error: No data returned");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Error: " + textStatus + ", " + errorThrown);
            }
        });
    }

    function map_panto(lat, lng) {
        var optBottom = document.querySelector('.opt_bottom');

        if ('ko' === '<?= $userLang ?>') {
            map.setCenter(new naver.maps.LatLng(lat, lng));

            if (optBottom) {
                var transformY = optBottom.style.transform;
                if (transformY == 'translateY(0px)') {
                    map.panBy(new naver.maps.Point(0, verticalCenterOffset)); // 위로 180 픽셀 이동
                }
            }
        } else if (typeof google !== 'undefined') {
            map.setCenter({
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            });

            google.maps.event.addListenerOnce(map, 'idle', function() {
                if (optBottom) {
                    var transformY = optBottom.style.transform;
                    if (transformY == 'translateY(0px)') {
                        map.panBy(0, verticalCenterOffset); // 중심을 위로 이동
                    }
                }
            });
        }
    }

    function f_my_location_btn(mt_idx) {
        var form_data = new FormData();
        var sgdt_idx = $('#sgdt_idx').val();

        // schedule_map(sgdt_idx, true); // map_panto 실행하지 않음
        schedule_map(sgdt_idx, true)
            .then(data => {
                console.log("Map data loaded successfully:", data);
                // 여기서 추가적인 처리를 수행할 수 있습니다.
            })
            .catch(error => {
                console.error("Failed to load map data:", error);
                // 여기서 오류 처리를 수행할 수 있습니다.
            });

        form_data.append("act", "my_location_search");
        form_data.append("mt_idx", mt_idx);

        $.ajax({
            url: "./schedule_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            dataType: 'json',
            success: function(data) {
                if (data) {
                    var lat = parseFloat(data.mlt_lat); // 숫자로 변환
                    var lng = parseFloat(data.mlt_long); // 숫자로 변환
                    var optBottom = document.querySelector('.opt_bottom');

                    if ('ko' === '<?= $userLang ?>') {
                        // 네이버 지도 설정
                        map.setCenter(new naver.maps.LatLng(lat, lng));

                        if (optBottom) {
                            var transformY = optBottom.style.transform;
                            if (transformY == 'translateY(0px)') {
                                map.panBy(new naver.maps.Point(0, verticalCenterOffset));
                            }
                        }

                        setTimeout(() => {
                            pedestrian_path_check(sgdt_idx);
                        }, 2500);
                    } else {
                        // 구글 지도 설정
                        map.setCenter({
                            lat: lat,
                            lng: lng
                        });

                        if (optBottom) {
                            var transformY = optBottom.style.transform;
                            if (transformY == 'translateY(0px)') {
                                map.panBy(0, verticalCenterOffset);
                            }
                        }
                    }
                } else {
                    console.log('Error: No data received from server');
                }
            },
            error: function(err) {
                console.log('Error:', err);
            },
        });

        console.timeEnd("forEachLoopExecutionTime");
    }

    function checkAdCount() {
        var ad_data = fetchAdDisplayStatus();
        console.log('index.php - ad_alert : ' + ad_data.ad_alert + ' ad_show : ' + ad_data.ad_show + ' ad_count : ' + ad_data.ad_count);

        try {
            if (ad_data.ad_show == 'Y') {
                requestAdDisplay(ad_data)
                    .then(() => {
                        console.log("Ad shown successfully");
                    })
                    .catch((error) => {
                        console.error("Error in requestAdDisplay:", error);
                    })
                    .finally(() => {
                        updateAdDisplayCount(ad_data);
                        gtag('event', 'index_ad', {
                            'event_category': 'show_log',
                            'event_label': 'show',
                            'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                            'platform': isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown')
                        });
                        // setTimeout(() => {
                        //     updateMemberLocationInfo();
                        // }, 1000); // 광고 표시 시도 후 1초 뒤에 지도 로드
                    });
            } else {
                updateAdDisplayCount(ad_data);
            }
        } catch (err) {
            console.log("Error in checkAdCount: " + err);
            updateAdDisplayCount(ad_data);
        }
    }

    function fetchAdDisplayStatus() {
        <?php
        unset($arr_data);
        $arr_data = array(
            "ad_alert" => "N",
            "ad_show" => "N",
            "ad_count" => "0"
        );

        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $mem_row = $DB->getone('member_t');

        // 무료회원인지 확인하고 광고체크하기
        if (((
                $mem_row['mt_level'] == '2'
                // && ($_SESSION['_mt_idx'] == 286 || $_SESSION['_mt_idx'] == 275 || $_SESSION['_mt_idx'] == 281 )
            )
                || $_SESSION['_mt_idx'] == 281)  //시리
            && ($_SESSION['_mt_idx'] != 272) //지니
        ) {
            // 무료회원일 경우 광고 카운트 확인하기
            $ad_row = get_ad_log_check($_SESSION['_mt_idx']);
            $ad_count = $ad_row['path_count']; // 현재 광고 수
            $ad_check = $ad_count % 5;

            if ($ad_check == 1) { // 클릭이 5번째일 때
                $arr_data['ad_alert'] = 'Y';
                $arr_data['ad_show'] = 'Y';
            } else {
                $arr_data['ad_alert'] = 'N';
                $arr_data['ad_show'] = 'N';
            }

            $arr_data['ad_count'] = $ad_count;
        }

        $ad_data = json_encode($arr_data);
        ?>
        // console.log(<?= $_SESSION['_mt_idx'] ?>);
        // console.log(<?= $mem_row['mt_level'] ?>);
        // console.log("ad_count :  <?= $ad_count ?>");
        // console.log(<?= $ad_data ?>);
        return <?= $ad_data ?>;
    }

    function requestAdDisplay(data) {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                try {
                    var message = {
                        type: "showAd",
                    };
                    if (!isAndroidDevice() && !isiOSDevice()) {
                        console.log("Showing desktop ad");
                    } else if (isAndroidDevice()) {
                        window.smapAndroid.showAd();
                        console.log("Showing Android ad");
                    } else if (isiOSDevice()) {
                        window.webkit.messageHandlers.smapIos.postMessage(message);
                        console.log("Showing iOS ad");
                    }
                    console.log("Ad display attempted");
                    resolve();
                } catch (error) {
                    console.error("Error showing ad:", error);

                    // 에러 로그를 서버에 저장
                    saveErrorLog(error);

                    reject(error);
                }
            }, 800); // 0.8초 지연 후 광고 표시 시도
        });
    }

    function saveErrorLog(error) {
        var logData = {
            mt_idx: <?= $_SESSION['_mt_idx'] ?>,
            error_message: error.message,
            error_stack: error.stack,
            user_agent: navigator.userAgent,
            platform: isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown'),
            timestamp: new Date().toISOString()
        };

        $.ajax({
            url: "./save_ad_error_log.php",
            type: "POST",
            data: JSON.stringify(logData),
            contentType: "application/json",
            success: function(response) {
                console.log("Error log saved successfully:", response);
            },
            error: function(xhr, status, error) {
                console.error("Failed to save error log:", error);
            }
        });
    }

    function updateAdDisplayCount(data) {
        var form_data = new FormData();
        form_data.append("act", "show_ad_path_log");
        // form_data.append("log_count", data.ad_count);
        form_data.append("mt_idx", <?= $_SESSION['_mt_idx'] ?>);

        $.ajax({
            url: "./show_ad_log_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000
        });
    }

    // MutationObserver 설정
    let previousTransformY = optBottom.style.transform; // 이전 transformY 값 저장
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'style' && optBottom.style.transform !== previousTransformY) {
                previousTransformY = optBottom.style.transform;

                if (previousTransformY === 'translateY(0px)') {
                    panMapDown();
                } else if (isPannedDown) {
                    panMapUp();
                }
            }
        });
    });

    function panMapDown() {
        originalCenter = map.getCenter();
        let newLat = 'ko' === '<?= $userLang ?>' ? (currentLat || originalCenter.lat()) - (300 / 111000) * 1.05 : (currentLat || originalCenter.lat()) - (300 / 111000) * 1.8;
        let newCenter = 'ko' === '<?= $userLang ?>' ? new naver.maps.LatLng(newLat, currentLng || originalCenter.lng()) : new google.maps.LatLng(newLat, currentLng || originalCenter.lng());

        if ('ko' === '<?= $userLang ?>') {
            map.panTo(newCenter, {
                duration: 700,
                easing: 'easeOutCubic'
            });
        } else {
            // map.setOptions({
            //     animation: null
            // });
            // map.setCenter(newCenter);

            // 애니메이션 시간 설정 (밀리초 단위)
            const duration = 700; // 0.7초

            map.setOptions({
                animation: google.maps.Animation.BOUNCE
            });
            map.panTo(newCenter);

            // 애니메이션 시간 이후 애니메이션 옵션 초기화
            setTimeout(() => {
                map.setOptions({
                    animation: null
                });
            }, duration);
        }

        isPannedDown = true;
    }

    function panMapUp() {
        let targetLatLng = currentLat ? ('ko' === '<?= $userLang ?>' ? new naver.maps.LatLng(currentLat, currentLng) : new google.maps.LatLng(currentLat, currentLng)) : originalCenter;

        if ('ko' === '<?= $userLang ?>') {
            map.panTo(targetLatLng, {
                duration: 700,
                easing: 'easeOutCubic',
                onComplete: function() {
                    isPannedDown = false;
                    originalCenter = null;
                }
            });
        } else {
            if (targetLatLng) {
                // map.setOptions({
                //     animation: null
                // });
                // map.setCenter(targetLatLng);
                // 애니메이션 시간 설정 (밀리초 단위)
                const duration = 700; // 0.7초

                map.setOptions({
                    animation: google.maps.Animation.BOUNCE
                });
                map.panTo(targetLatLng);

                // 애니메이션 시간 이후 애니메이션 옵션 초기화
                setTimeout(() => {
                    map.setOptions({
                        animation: null
                    });
                }, duration);



                isPannedDown = false;
                originalCenter = null;
            }
        }
    }

    // 감시 시작
    observer.observe(optBottom, {
        attributes: true,
        attributeFilter: ['style']
    });

    function isAndroidDevice() {
        if (/Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined') {
            console.log('Android!!');
        }
        return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
    }

    function isiOSDevice() {
        if (/iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos) {
            console.log('iOS!!');
        }
        return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
    }

    // setInterval(() => {
    //     var sgdt_idx = $('#sgdt_idx').val();
    //     // marker_reload(sgdt_idx);
    //     // console.log(sgdt_idx);
    // }, 30000);
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>