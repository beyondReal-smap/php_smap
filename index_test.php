<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '1';
$h_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";


if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        // alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
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
<script script src="https://apis.openapi.sk.com/tmap/vectorjs?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
<script src="https://apis.openapi.sk.com/tmap/jsv2?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>



<div class="container-fluid idx_pg px-0 ">
    <div class="idx_pg_div">
        <section class="main_top">
            <!--D-6 멤버 스케줄 미참석 팝업 임시로 넣어놓았습니다.-->
            <div class="py-3 px_16 top_weather" id="top_weather_box" style="height: 58px;">
                <div class="d-flex align-items-center p_address">
                    <p class="fs_12 text_light_gray fw_500 text_dynamic">잠시만 기다려주세요! 주소 정보를 가져오는 중입니다.!</p>
                    <!-- <p class="fs_12 text_light_gray text_dynamic p_content line_h1_2">잠시만 기다려주세요! 기상 데이터를 가져오는 중입니다.!</p> -->
                </div>
                <!-- 로딩할때 사용 -->
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="date_weather d-flex align-items-center flex-wrap">
                        <div class="d-flex align-items-center fs_14 fw_600 text_dynamic mr-1 mt_08"><?= DateType(date("Y-m-d"), 3) ?>
                            <span class="loader loader_sm ml-2 mr-2"></span>
                        </div>
                        <div class="d-flex align-items-center mt_08 mr-3">
                            <p class="ml-1 fs_11 fw_600 text-text fw_500 mr-2"><span class="fs_11 text_light_gray mr-1">기상 데이터를 가져오는 중입니다.!</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    get_weather();
                });

                function get_weather() {
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
                        timeout: 50000,
                        success: function(data) {
                            if (data) {
                                $('#top_weather_box').empty(); // 섹션 비우기
                                $('#top_weather_box').html(data);
                                f_get_box_list();
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                }
            </script>
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
                    <button type="button" class="btn point point_mypoint" onclick="f_my_location_btn(<?= $_SESSION['_mt_idx'] ?>,<?= $sgdt_row['sgdt_idx'] ?>)">
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
            <section class="opt_bottom" style="transform: translateY(56%);">
                <div class="top_bar_wrap text-center pt_08">
                    <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                    <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
                </div>
                <div class="">
                    <div class="grp_wrap">
                        <div class="border bg-white rounded-lg px_16 py_16">
                            <p class="fs_16 fw_600 mb-3">그룹원</p>
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
                            <div id="group_member_list_box">
                                <div class="mem_wrap mem_swiper">
                                    <div class="swiper-wrapper d-flex">
                                        <div class="swiper-slide checks mem_box">
                                            <label>
                                                <input type="radio" name="rd2" checked onclick="mem_schedule(<?= $sgdt_row['sgdt_idx'] ?>);">
                                                <div class="prd_img mx-auto">
                                                    <div class="rect_square rounded_14">
                                                        <img src="<?= $session_img ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="프로필이미지" />
                                                    </div>
                                                </div>
                                                <!-- 처음은 사용자 본인이 나옵니다. -->
                                                <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic" <? if ($sgt_cnt > 0) { ?> style="height:26px" <? } ?>><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 일정리스트 -->
                    <div class="task_wrap">
                        <div class="border bg-white rounded-lg mb-3">
                            <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
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
                            </form>
                            <script>
                                $(document).ready(function() {
                                    f_get_box_list();
                                });
                            </script>
                            <div id="schedule_list_box">
                                <div class="task_header px_16 pt_16">
                                    <div class="border-bottom  pb-3">
                                        <div class="task_header_tit">
                                            <p class="fs_16 fw_600 line_h1_2 mr-3">현재 위치</p>
                                            <div class="d-flex align-items-center justify-content-end">
                                                <p class="move_txt fs_13 mr-3"></p>
                                                <p class="d-flex bettery_txt fs_13"><span class="d-flex align-items-center flex-shrink-0 mr-2"><img src="./img/battery.png?v=20240404" width="14px" class="battery_img" alt="베터리시용량"></span></p>
                                            </div>
                                        </div>
                                        <p class="fs_14 fw_500 text_light_gray text_dynamic line_h1_3 mt-2">현재 위치 받아오는 중..</p>
                                    </div>
                                </div>
                                <div class="task_body px_16 pt-3 pb_16">
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
            <section class="opt_bottom" style="transform: translateY(0%);">
                <div class="top_bar_wrap text-center pt_08">
                    <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                    <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
                </div>
                <div class="">
                    <!-- 일정리스트 -->
                    <div class="task_wrap">
                        <div class="border bg-white rounded-lg mb-3">
                            <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
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
                            </form>
                            <div id="schedule_list_box">
                                <div class="task_header px_16 pt_16">
                                    <div class="border-bottom  pb-3">
                                        <div class="task_header_tit">
                                            <p class="fs_16 fw_600 line_h1_2 mr-3">현재 위치</p>
                                            <div class="d-flex align-items-center justify-content-end">
                                                <p class="move_txt fs_13 mr-3"></p>
                                                <p class="d-flex bettery_txt fs_13"><span class="d-flex align-items-center flex-shrink-0 mr-2"><img src="./img/battery.png?v=20240404" width="14px" class="battery_img" alt="베터리시용량"></span></p>
                                            </div>
                                        </div>
                                        <p class="fs_14 fw_500 text_light_gray text_dynamic line_h1_3 mt-2">현재 위치 받아오는 중..</p>
                                    </div>
                                </div>
                                <div class="task_body px_16 pt-3 pb_16">
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
            <div class="flt_body pb-5 d-flex align-items-start justify-content-between">
                <div>
                    <p class="fc_3d72ff fs_14 fw_700 text-primary">환영합니다.</p>
                    <p class="text_dynamic line_h1_3 fs_17 fw_700 mt-2">친구에게 받은
                        <span class="text-primary">초대링크</span>로 가입하셨나요?
                    </p>
                    <p class="text_dynamic line_h1_3 text_gray fs_14 mt-3 fw_500">그룹원을 추가하면 실시간 위치 조회를 할 수 있어요.</p>
                </div>
                <img src="./img/send_img.png" class="flt_img_send" width="66px" alt="초대링크" />
            </div>
            <div class="flt_footer flt_footer_b">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 flt_close" onclick="floating_link_cancel()">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='./invitation_code'">네</button>
                </div>
            </div>
        </div>
    </div>
<? } ?>
<!-- 그룹만들기 플러팅 -->
<div class="floating_wrap " id="group_make_modal">
    <div class="flt_inner">
        <div class="flt_head">
            <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹만들기</span></p>
        </div>
        <div class="flt_body pb-5 pt-3">
            <p class="text_dynamic line_h1_3 fs_17 fw_700">친구들과 함께할
                <span class="text-primary">나만의 그룹을</span> 만들어 볼까요?
            </p>
            <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">그룹원을 추가하면 실시간 위치 조회를 할 수 있어요.</p>
        </div>
        <div class="flt_footer">
            <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'">다음</button>
        </div>
    </div>
</div>
<!-- D-11 그룹 있을 때 초대링크로 앱 접속  -->
<div class="modal fade" id="dbgroup_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center pb-5">
                <img src="./img/warring.png" width="72px" class="pt-3" alt="그룹참여불가능" />
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">그룹에 참여할 수 없어요.</p>
                <p class="fs_14 text_dynamic text_gray mt-2 line_h1_2 px-4">현재 참여한(생성한) 그룹이 있어 다른 그룹에 참여할 수 없어요. 이 그룹에 참여하시려면 모든 그룹의 활동을 끝내고 이후 다시 시도해 주세요.</p>
            </div>
            <div class="modal-footer px-0 py-0">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close">알겠어요!</button>
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
                <img src="./img/optimal_map.png" width="48px" class="pt-3" alt="최적의경로" />
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">현재 위치에서부터 다음 일정까지의
                    최적의 경로를 표시할까요?
                </p>
                <p class="fs_12 text_dynamic text_gray mt-2 line_h1_2">최적경로 및 예상시간과 거리가 표시됩니다.</p>
                <div class="optimal_info_wrap">
                    <p class="optim_plan" id="pathType"><span>Basic</span></p>
                    <p class="text-primary fs_14 fw_600 text_dynamic mt-3 line_h1_4" id="pathCountday">금일 2회 사용 가능</p>
                    <p class=" text-primary fs_14 fw_600 text_dynamic line_h1_4" id="pathCountmonth">이번달 60회 사용 가능</p>
                    <p class="text_gray fs_11 text_dynamic line_h1_3 mt-2" id="pathContent"> Basic 사용자는 하루 2번, 월 60번까지 사용 가능해요!</p>
                </div>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">취소하기</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" id="showPathButton">표시하기</button>
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
                <img src="./img/warring.png" width="72px" class="pt-3" alt="플랜" />
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4 mb-3">유료플랜이 종료되어
                    이래 기능이 제한되었어요
                </p>
                <div class="planinfo_box">
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-center flex-wrap">
                            <p class="fs_16 text_dynamic fw_700 mb-4 mr-2">일정 최적경로 사용횟수</p>
                            <p class="fs_11 text_dynamic fw_700 mb-4">(하루/월)</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center">
                            <p class="text_light_gray fs_14 fw_700 mr-2">10/300</p>
                            <i class="text_light_gray fs_14 xi-arrow-right mr-2"></i>
                            <p class="text-primary fs_14 fw_700">2/60</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="fs_16 text_dynamic fw_700 line_h1_3 mb-4">내 장소 저장</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <p class="text_light_gray fs_14 fw_700 mr-2">무제한</p>
                            <i class="text_light_gray fs_14 xi-arrow-right mr-2"></i>
                            <p class="text-primary fs_14 fw_700">2개</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="fs_16 text_dynamic fw_700 line_h1_3 mb-4">로그 조회기간</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <p class="text_light_gray fs_14 fw_700 mr-2">2주</p>
                            <i class="text_light_gray fs_14 xi-arrow-right mr-2"></i>
                            <p class="text-primary fs_14 fw_700">2일</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="rect_modalbner">
                            <!-- 광고가표시됩니다.-->
                        </div>
                    </div>
                    <p class="fs_14 text_gray text_dynamic line_h1_3">유료플랜을 연장하면
                        다시 위 기능을 사용할 수 있어요.
                    </p>
                </div>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="location.href='./plan_info'">연장할래요!</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close">알겠어요</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var map = new naver.maps.Map("map", {
        center: new naver.maps.LatLng(<?= $_SESSION['_mt_lat'] ?>, <?= $_SESSION['_mt_long'] ?>),
        zoom: 16,
        mapTypeControl: false
    }); // 전역 변수로 map을 선언하여 다른 함수에서도 사용 가능하도록 합니다.
    var scheduleMarkers = []; // 스케줄 마커를 저장할 배열입니다.
    var optimalPath; // 최적 경로를 표시할 변수입니다.
    var drawInfoArr = [];
    var resultdrawArr = [];
    var scheduleMarkerCoordinates = [];
    var scheduleStatus = [];
    var startX, startY, endX, endY; // 출발지와 도착지 좌표 변수 초기화
    var markers;
    var polylines;
    var profileMarkers = [];
    var pathCount;
    // 버튼 엘리먼트 찾기
    var showPathButton = document.getElementById('showPathButton');
    var ll = 0;

    //손으로 바텀시트 움직이기
    document.addEventListener("DOMContentLoaded", function() {
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
                    var newTransformValue = isVisible ? "translateY(0)" : "translateY(56%)";
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
                        var newTransformValue = isVisible ? 'translateY(0)' : 'translateY(56%)';
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

    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    }

    function toggleInfobox() {
        var infoboxes = document.getElementsByClassName('infobox');
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
    $(document).ready(function() {
        // console.time("forEachLoopExecutionTime");
        schedule_map(<?= $sgdt_row['sgdt_idx'] ?>);
        f_get_box_list();
        f_get_box_list2();
        setTimeout(() => {
            pedestrian_path_check(<?= $sgdt_row['sgdt_idx'] ?>);
        }, 2500);
        <? if ($_SESSION['_mt_level'] == '2') { ?>
            //$('#planinfo_modal').modal('show');
        <? } ?>

        <? //if ($member_info_row['mt_level'] == '2' && $current_date > $member_info_row['mt_plan_date'] && $sgdt_cnt < 1) {
        ?>
        // $('#planinfo_modal').modal('show');
        <? //}
        ?>

    });
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
                if (data.result == 'Y' && data.path_count_day == 0) {
                    $('#pathType').text(data.path_type); // 모달에 표시
                    $('#pathCountday').text("일 사용횟수를 모두 사용하셨습니다."); // 모달에 표시
                    $('#pathCountmonth').text("이번달 " + data.path_count_month + "회 사용 가능"); // 모달에 표시
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
                    $('#showPathButton').prop('disabled', false);
                    $('#path_day_count').val(data.path_count_day);
                    if (data.path_type == 'Pro') {
                        $('#pathContent').addClass('d-none');
                    } else {
                        $('#pathContent').removeClass('d-none');
                    }
                    $('#optimal_modal').modal('show');
                } else if (data.result == 'Noschedule') {
                    jalert('최적경로 조회는 두 개 이상의 일정이 입력되었을 때만 이용할 수 있어요.');

                } else {
                    jalert('잘못된 접근입니다.');
                }
            },
            error: function(err) {
                console.log(err);
            },
        });

    }
    //멤버아이콘 클릭시
    function mem_schedule(sgdt_idx) {
        console.time("forEachLoopExecutionTime");
        document.getElementById('sgdt_idx').value = sgdt_idx;
        schedule_map(sgdt_idx);
        f_get_box_list();
        setTimeout(() => {
            pedestrian_path_check(sgdt_idx);
        }, 1000);
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

    function initializeMap(my_profile, st_lat, st_lng, markerData) {
        if (markerData.marker_reload == 'Y') {
            // profileMarkers 배열에 담겨있는 마커 제거
            for (var i = 0; i < profileMarkers.length; i++) {
                profileMarkers[i].setMap(null); // 지도에서 마커 제거
            }
            // 마커 배열 초기화
            profileMarkers = [];
            // 기존 프로필 마커 추가
            var profileMarkerOptions = {
                position: new naver.maps.LatLng(st_lat, st_lng),
                map: map,
                icon: {
                    content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                    size: new naver.maps.Size(44, 44),
                    origin: new naver.maps.Point(0, 0),
                    anchor: new naver.maps.Point(22, 22)
                },
                zIndex: 3
            };
            var profileMarker = new naver.maps.Marker(profileMarkerOptions);
            profileMarkers.push(profileMarker);

            for (var i = 1; i <= markerData.profile_count; i++) {
                var profileMarkerOptions = {
                    position: new naver.maps.LatLng(markerData['profilemarkerLat_' + i], markerData['profilemarkerLong_' + i]),
                    map: map,
                    icon: {
                        content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + markerData['profilemarkerImg_' + i] + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                        size: new naver.maps.Size(44, 44),
                        origin: new naver.maps.Point(0, 0),
                        anchor: new naver.maps.Point(22, 22)
                    },
                    zIndex: 2
                };
                var profileMarker = new naver.maps.Marker(profileMarkerOptions);
                profileMarkers.push(profileMarker);
            }
        } else {
            map = new naver.maps.Map("map", {
                center: new naver.maps.LatLng(st_lat, st_lng),
                zoom: 16,
                mapTypeControl: false
            });

            var optBottom = document.querySelector('.opt_bottom');
            if (optBottom) {
                var transformY = optBottom.style.transform;
                if (transformY == 'translateY(0px)') {
                    map.panBy(new naver.maps.Point(0, 180)); // 위로 180 픽셀 이동
                }
            }
            // 마커 배열 초기화
            markers = [];
            polylines = [];
            profileMarkers = [];
            // 기존 프로필 마커 추가
            var profileMarkerOptions = {
                position: new naver.maps.LatLng(st_lat, st_lng),
                map: map,
                icon: {
                    content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                    size: new naver.maps.Size(44, 44),
                    origin: new naver.maps.Point(0, 0),
                    anchor: new naver.maps.Point(22, 22)
                },
                zIndex: 3
            };
            var profileMarker = new naver.maps.Marker(profileMarkerOptions);
            profileMarkers.push(profileMarker);
            // markers.push(profileMarker);

            for (var i = 1; i <= markerData.profile_count; i++) {
                var profileMarkerOptions = {
                    position: new naver.maps.LatLng(markerData['profilemarkerLat_' + i], markerData['profilemarkerLong_' + i]),
                    map: map,
                    icon: {
                        content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + markerData['profilemarkerImg_' + i] + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                        size: new naver.maps.Size(44, 44),
                        origin: new naver.maps.Point(0, 0),
                        anchor: new naver.maps.Point(22, 22)
                    },
                    zIndex: 2
                };
                var profileMarker = new naver.maps.Marker(profileMarkerOptions);
                profileMarkers.push(profileMarker);
                // markers.push(profileMarker);
            }
            // 스케줄 마커 추가
            if (markerData.schedule_chk === 'Y') {
                var positions = [];
                for (var i = 1; i <= markerData.count; i++) {
                    if (i === 1) {
                        // 출발지 좌표
                        startX = markerData['markerLat_' + i];
                        startY = markerData['markerLong_' + i];
                    } else if (i === markerData.count) {
                        // 도착지 좌표
                        endX = markerData['markerLat_' + i];
                        endY = markerData['markerLong_' + i];
                    }

                    var markerLat = markerData['markerLat_' + i];
                    var markerOptions = {
                        position: new naver.maps.LatLng(markerData['markerLat_' + i], markerData['markerLong_' + i]),
                        map: map,
                        icon: {
                            content: markerData['markerContent_' + i],
                            size: new naver.maps.Size(61, 61),
                            origin: new naver.maps.Point(0, 0),
                            anchor: new naver.maps.Point(30, 30)
                        },
                        zIndex: 1
                    };

                    var marker = new naver.maps.Marker(markerOptions);
                    positions.push(marker.getPosition());
                    scheduleMarkers.push(marker);
                    markers.push(marker);
                }
                // 스케줄 마커의 개수
                var markerCount = markerData['count'];
                // 스케줄 마커의 좌표 배열
                scheduleMarkerCoordinates = [];
                scheduleStatus = [];
                for (var i = 1; i <= markerCount; i++) {
                    var lat = markerData['markerLat_' + i];
                    var lng = markerData['markerLong_' + i];
                    var status = markerData['markerStatus_' + i];
                    scheduleMarkerCoordinates.push(new naver.maps.LatLng(lat, lng));
                    scheduleStatus.push(status);
                }
            }
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

        // initializeMap 함수 끝에 map 변수의 상태를 체크하고 map이 정상적으로 생성되었을 때에만 setCursor 호출
        if (map) {
            map.setCursor('pointer');
        }
    }
    // 최적경로버튼에 클릭 이벤트 핸들러 등록
    showPathButton.addEventListener('click', function(event) {
        var pathCount = document.getElementById('path_day_count');
        // pathCount가 2 이상인 경우에만 특정 동작을 수행
        if (pathCount == 0) {
            jalert('오늘 사용할 최적경로를 모두 사용하였습니다.');
        } else {
            showOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus);
            $('#optimal_modal').modal('hide');
        }
    });

    // 최경경로 사용 여부 확인
    function pedestrian_path_check(sgdt_idx) {
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
            timeout: 50000,
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                if (data.result == 'Y') {
                    var jsonString = data.sllt_json_text;
                    var start = jsonString.indexOf('{"type":"FeatureCollection"'); // JSON 데이터의 시작 위치 찾기
                    var end = jsonString.lastIndexOf('}') + 1; // JSON 데이터의 끝 위치 찾기

                    // 시작 위치와 끝 위치를 기준으로 JSON 데이터 추출
                    var validJsonString = jsonString.substring(start, end);

                    var ajaxData = JSON.parse(validJsonString); // 추출된 JSON 데이터 파싱
                    var resultData = ajaxData.features; // features 배열 추출

                    // 경로 및 시간 정보를 처리
                    var totalidstance = ((resultData[0].properties.totalDistance) / 1000).toFixed(1);
                    var totalTime = ((resultData[0].properties.totalTime) / 60).toFixed(0);
                    var elementWithAriaLabel = $('.optimal_box').filter(function() {
                        return $(this).attr('aria-label') !== undefined;
                    });

                    // 요소의 aria-label 속성 값에서 / 이후의 값을 가져옵니다.
                    var labelText = elementWithAriaLabel.attr('aria-label').split('/')[1].trim();

                    // console.log(ajaxData); // 전체 JSON 데이터 출력
                    // console.log(resultData); // features 배열 출력
                    // 기존에 그려진 라인 & 마커가 있다면 초기화
                    // 각 경유지까지의 예상 소요 시간 계산 함수 호출
                    calculateWalkingTime(startX, startY, endX, endY, scheduleMarkerCoordinates, function(totalWalkingTime) {

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

                        var j = 0;
                        var linecolor = "#140082";
                        var pp_marker = 0;
                        var ii = 0;
                        var mm = 0;
                        ll = 0;

                        for (var i in resultData) {
                            var geometry = resultData[i].geometry;
                            var properties = resultData[i].properties;

                            if (geometry.type == "LineString") {
                                var path = [];
                                var polylinePath = [];

                                var jj = 0;
                                for (var j in geometry.coordinates) {
                                    var latlng = new Tmapv2.Point(geometry.coordinates[j][0], geometry.coordinates[j][1]);
                                    var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
                                    var convertChange = new naver.maps.LatLng(convertPoint._lat, convertPoint._lng);
                                    path.push(convertChange);
                                    if (jj > 0) {
                                        ii += jj;
                                        makeMarker(map, new naver.maps.LatLng(convertPoint._lat, convertPoint._lng), path[path.length - 2], ii);
                                    }
                                    jj++;
                                }

                                // Naver Maps API의 Polyline로 변경
                                var polyline_ = new naver.maps.Polyline({
                                    path: path,
                                    strokeColor: linecolor,
                                    strokeOpacity: 0.8,
                                    strokeWeight: 7,
                                    map: map
                                });

                                // console.log(polyline_);
                                // console.log(linecolor);

                                resultdrawArr.push(polyline_);
                                polylines.push(polyline_);
                            } else {
                                var markerImg = "./img/map_direction.svg";
                                var pType = "";
                                var size;
                                var anchor;
                                var zIndexhtml;
                                var angle_t = 0; // 추가: 초기 각도값 설정

                                if (properties.pointType == "SP") { // 경로 출발지 마커
                                    pType = "S";
                                    // contenthtml = '<div><img src="' + markerImg + '"></div>';
                                    zIndexhtml = 0;
                                } else if (properties.pointType == "EP") { // 경로 도착지 마커
                                    pType = "EP";
                                    markerImg = "./img/mark_connect.png";
                                    // contenthtml = '<div><img src="' + markerImg + '"></div>';
                                    zIndexhtml = 0;
                                    schedulehtml = '<p class="fs_23 fw_700 optimal_time">' + totalWalkingTime[pp_marker][0] + '<span class="fs_14">분</span></p>' +
                                        '<p class="fs_12 text_light_gray optimal_tance">' + totalWalkingTime[pp_marker][1] + 'km</p>';
                                    pp_marker++;
                                    var aria_cnt = (pp_marker * 2);
                                    $('.optimal_box[aria-label="' + aria_cnt + ' / ' + labelText + '"]').html(schedulehtml);
                                    // console.log('schedulehtml : ' + schedulehtml);
                                    // zIndexhtml = 5;
                                } else if (properties.pointType == "GP") { // 경로 이동 마커
                                    pType = "GP";
                                    // contenthtml = '<div><img src="' + markerImg + '"></div>';
                                    zIndexhtml = 0;
                                } else { // 경로 경유지 마커
                                    pType = "P";
                                    // contenthtml = '<div><img src="' + markerImg + '"></div>';
                                    zIndexhtml = 0;
                                    schedulehtml = '<p class="fs_23 fw_700 optimal_time">' + totalWalkingTime[pp_marker][0] + '<span class="fs_14">분</span></p>' +
                                        '<p class="fs_12 text_light_gray optimal_tance">' + totalWalkingTime[pp_marker][1] + 'km</p>';
                                    pp_marker++;
                                    var aria_cnt = (pp_marker * 2);
                                    // 해당 div 선택 후 내용 삽입
                                    $('.optimal_box[aria-label="' + aria_cnt + ' / ' + labelText + '"]').html(schedulehtml);
                                    // console.log('schedulehtml : ' + schedulehtml);
                                }
                                console.log(pType);
                                console.log(ll);
                                var latlon = new Tmapv2.Point(geometry.coordinates[0], geometry.coordinates[1]);
                                var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlon);
                                var routeInfoObj = {
                                    markerImage: markerImg,
                                    lng: convertPoint._lng,
                                    lat: convertPoint._lat,
                                    pointType: pType
                                };
                                var markerOptions = {
                                    position: new naver.maps.LatLng(routeInfoObj.lat, routeInfoObj.lng),
                                    map: map,
                                    title: 'map_location_maker' + mm,
                                    icon: {
                                        // content: contenthtml,
                                        url: markerImg,
                                        size: new naver.maps.Size(20, 20),
                                        origin: new naver.maps.Point(0, 0),
                                        anchor: new naver.maps.Point(10, 10),
                                        scaledSize: new naver.maps.Size(20, 20),
                                    },
                                    zIndex: zIndexhtml
                                };
                                var marker_p = new naver.maps.Marker(markerOptions);
                                // console.log(markerOptions);

                                resultdrawArr.push(marker_p);
                                location_markers.push(marker_p);
                                markers.push(marker_p);

                                if (mm > 0) {
                                    position1 = new naver.maps.LatLng(convertPoint._lat, convertPoint._lng);
                                    position2 = location_markers[location_markers.length - 2]['position'];

                                    var angle_t = f_get_angle(position2['x'], position2['y'], position1['x'], position1['y']);
                                    console.log(position1['x'], position1['y'], position2['x'], position2['y'], angle_t);

                                    $("div[title|='map_location_maker" + ll + "'").css('transform', 'rotate(' + angle_t + 'deg)');
                                    ll++;
                                }
                                mm++;
                            }
                        };
                    });
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }
    // 최적경로 구하기
    function showOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus) {
        // 스케줄 마커들의 좌표를 추출하여 경유지로 설정
        var viaPoints = scheduleMarkerCoordinates.map(function(coordinate, index) {
            if (index === 0 || index === scheduleMarkerCoordinates.length - 1) {
                // 출발지 또는 도착지인 경우, 무시하고 continue
                return null;
            }
            return {
                "viaPointId": "point_" + index,
                "viaPointName": "point_" + index,
                "viaY": coordinate._lat || coordinate.lng(), // 수정
                "viaX": coordinate._lng || coordinate.lat(), // 수정
                //"status": scheduleStatus[index] || scheduleStatus[index], // 수정
                "viaTime": 600
            };
        }).filter(function(point) {
            return point !== null; // 출발지와 도착지를 제외하기 위해 null을 제거
        });
        // 좌표값만을 추출하여 passList에 저장
        var passList = viaPoints.map(function(point) {
            // 좌표값을 EPSG3857로 변환
            var latlng = new Tmapv2.Point(point.viaX, point.viaY);
            var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
            // return convertPoint._lng + "," + convertPoint._lat;
            return point.viaX + "," + point.viaY;
        }).join("_");

        // 직선거리 계산
        const distance = getDistance(startY, startX, scheduleMarkerCoordinates, 5);
        // console.log(`출발지와 도착지 사이의 직선거리: ${distance.toFixed(2)} km`);
        var straightDistance = distance.toFixed(2);
        if (straightDistance >= 5) {
            jalert('일정과 일정 사이의 거리가 너무 멀어 최적경로 표기가 어렵습니다.(' + straightDistance + 'km)');
            return false;
        }
        // passList가 존재할 때만 데이터에 passList를 포함시킴
        let requestData = {
            "reqCoordType": "WGS84GEO",
            "resCoordType": "EPSG3857",
            "startName": "출발",
            "startX": startY, // 수정
            "startY": startX, // 수정
            "endName": "도착",
            "endX": endY, // 수정
            "endY": endX, // 수정
            "endID": "goal",
            // "searchOption": "10"
        };

        if (passList) {
            requestData.passList = passList; // 경유지 좌표값 추가
        }
        let dataToSend = JSON.stringify(requestData);

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
                var resultData = response.features;
                // 경로 및 시간 정보를 처리
                var totalidstance = ((resultData[0].properties.totalDistance) / 1000).toFixed(1);
                var totalTime = ((resultData[0].properties.totalTime) / 60).toFixed(0);
                // console.log('보행자 경로안내: 총 거리 : ' + totalidstance + "km," + ' 총 시간 : ' + totalTime + '분');

                var elementWithAriaLabel = $('.optimal_box').filter(function() {
                    return $(this).attr('aria-label') !== undefined;
                });

                // 요소의 aria-label 속성 값에서 / 이후의 값을 가져옵니다.
                var labelText = elementWithAriaLabel.attr('aria-label').split('/')[1].trim();

                // 각 경유지까지의 예상 소요 시간 계산 함수 호출
                calculateWalkingTime(startX, startY, endX, endY, scheduleMarkerCoordinates, function(totalWalkingTime) {

                    // 기존에 그려진 라인 & 마커가 있다면 초기화
                    if (resultdrawArr.length > 0) {
                        for (var i in resultdrawArr) {
                            resultdrawArr[i].setMap(null);
                        }
                        resultdrawArr = [];
                    }

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

                    var j = 0;
                    var linecolor = "#140082";
                    var pp_marker = 0;
                    var ii = 0;
                    var mm = 0;
                    var ll = 0;

                    for (var i in resultData) {
                        var geometry = resultData[i].geometry;
                        var properties = resultData[i].properties;

                        if (geometry.type == "LineString") {
                            var path = [];
                            var polylinePath = [];
                            var jj = 0;

                            for (var j in geometry.coordinates) {
                                var latlng = new Tmapv2.Point(geometry.coordinates[j][0], geometry.coordinates[j][1]);
                                var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
                                var convertChange = new naver.maps.LatLng(convertPoint._lat, convertPoint._lng);
                                path.push(convertChange);
                                if (jj > 0) {
                                    ii += jj;
                                    //makeMarker(map, new naver.maps.LatLng(convertPoint._lat, convertPoint._lng), path[path.length - 2], ii);
                                }
                                jj++;
                            }
                            // Naver Maps API의 Polyline로 변경
                            var polyline_ = new naver.maps.Polyline({
                                path: path,
                                strokeColor: linecolor,
                                strokeOpacity: 0.8,
                                strokeWeight: 7,
                                map: map
                            });
                            // console.log(polyline_);
                            // console.log(linecolor);

                            resultdrawArr.push(polyline_);
                            polylines.push(polyline_);
                        } else {
                            var markerImg = "./img/map_direction.svg";
                            var pType = "";
                            var size;
                            var anchor;
                            var zIndexhtml;
                            var angle_t = 0; // 추가: 초기 각도값 설정

                            if (properties.pointType == "SP") { // 경로 출발지 마커
                                // markerImg = "./img/mark_connect.png";
                                pType = "S";
                                // contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                            } else if (properties.pointType == "EP") { // 경로 도착지 마커
                                markerImg = "./img/mark_connect.png";
                                pType = "EP";
                                // contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                                schedulehtml = '<p class="fs_23 fw_700 optimal_time">' + totalWalkingTime[pp_marker][0] + '<span class="fs_14">분</span></p>' +
                                    '<p class="fs_12 text_light_gray optimal_tance">' + totalWalkingTime[pp_marker][1] + 'km</p>';
                                pp_marker++;
                                var aria_cnt = (pp_marker * 2);
                                $('.optimal_box[aria-label="' + aria_cnt + ' / ' + labelText + '"]').html(schedulehtml);
                                // zIndexhtml = 5;
                            } else if (properties.pointType == "GP") { // 경로 이동 마커
                                // markerImg = "./img/mark_connect.png";
                                pType = "GP";
                                // contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                            } else { // 경로 경유지 마커
                                // markerImg = "./img/mark_connect.png";
                                pType = "P";
                                // contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                                schedulehtml = '<p class="fs_23 fw_700 optimal_time">' + totalWalkingTime[pp_marker][0] + '<span class="fs_14">분</span></p>' +
                                    '<p class="fs_12 text_light_gray optimal_tance">' + totalWalkingTime[pp_marker][1] + 'km</p>';
                                pp_marker++;
                                var aria_cnt = (pp_marker * 2);
                                // 해당 div 선택 후 내용 삽입
                                $('.optimal_box[aria-label="' + aria_cnt + ' / ' + labelText + '"]').html(schedulehtml);
                            }

                            var latlon = new Tmapv2.Point(geometry.coordinates[0], geometry.coordinates[1]);
                            var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlon);
                            var routeInfoObj = {
                                markerImage: markerImg,
                                lng: convertPoint._lng,
                                lat: convertPoint._lat,
                                pointType: pType
                            };
                            var markerOptions = {
                                position: new naver.maps.LatLng(routeInfoObj.lat, routeInfoObj.lng),
                                map: map,
                                title: 'map_location_maker' + mm,
                                icon: {
                                    // content: contenthtml,
                                    url: markerImg,
                                    size: new naver.maps.Size(20, 20),
                                    origin: new naver.maps.Point(0, 0),
                                    anchor: new naver.maps.Point(10, 10),
                                    scaledSize: new naver.maps.Size(20, 20),
                                },
                                zIndex: zIndexhtml
                            };
                            var marker_p = new naver.maps.Marker(markerOptions);

                            location_markers.push(marker_p);
                            resultdrawArr.push(marker_p);
                            markers.push(marker_p);

                            if (mm > 0) {
                                position1 = new naver.maps.LatLng(convertPoint._lat, convertPoint._lng);
                                position2 = location_markers[location_markers.length - 2]['position'];

                                var angle_t = f_get_angle(position2['x'], position2['y'], position1['x'], position1['y']);
                                // console.log(position1['x'], position1['y'], position2['x'], position2['y'], angle_t);

                                $("div[title|='map_location_maker" + ll + "'").css('transform', 'rotate(' + angle_t + 'deg)');
                                ll++;
                            }
                            mm++;
                        }
                    };
                });

                // 성공 시 ajax로 DB에 log json 추가
                var sgdt_idx = $('#pedestrian_path_modal_sgdt_idx').val();

                var form_data = new FormData();
                form_data.append("act", "loadpath_add");
                form_data.append("sgdt_idx", sgdt_idx);
                // form_data.append("sllt_json_text", resultData);
                form_data.append("sllt_json_text", JSON.stringify(response));
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
                    success: function(data) {
                        if (data == 'Y') {} else {
                            jalert('잘못된 접근입니다.');
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    },
                });

            },
            error: function(request, status, error) {
                console.log(request.responseJSON.error.code);
                console.log(request.responseJSON.error);
                console.log("code:" + request.status + "\n" +
                    "message:" + request.responseText + "\n" +
                    "error:" + error);
                if (request.responseJSON.error.code == '3102') {
                    var errorMessage = '해당 서비스가 지원되지 않는 구간이라 최적 경로 안내가 어려워요.';
                } else if (request.responseJSON.error.code == '3002') {
                    var errorMessage = '길안내를 제공하지 않는 부분이 있어서, 최적 경로 안내가 어려워요.';
                } else if (request.responseJSON.error.code == '1009') {
                    var errorMessage = '일부 구간이 너무 멀어서, 최적 경로 안내가 힘들어요.';
                } else if (request.responseJSON.error.code == '9401') {
                    var errorMessage = '최적경로 조회는 두 개 이상의 일정이 입력되었을 때만 이용할 수 있어요.';
                } else if (request.responseJSON.error.code == '1100') {
                    var errorMessage = '최적경로는 최대 7개까지의 일정의 경로를 표시 가능해요.';
                } else if (request.responseJSON.error.code == '2200') {
                    var errorMessage = '최적경로 API에서 지원하지는 주소 범위입니다.';
                }

                jalert(errorMessage);
            }
        });
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
            title: 'map_line_maker' + index,
            icon: {
                url: ICON_SPRITE_IMAGE_URL,
                size: new naver.maps.Size(12, 12), // 이미지 크기
                // origin: new naver.maps.Point(iconSpritePositionX, iconSpritePositionY), // 스프라이트 이미지에서 클리핑 위치
                anchor: new naver.maps.Point(6, 6), // 지도상 위치에서 이미지 위치의 offset
                scaledSize: new naver.maps.Size(12, 12),
                origin: new naver.maps.Point(0, 0),
            }
        });

        var angle_t = f_get_angle(position2['x'], position2['y'], position1['x'], position1['y']);
        // console.log(position1['x'], position1['y'], position2['x'], position2['y'], angle_t);

        $("div[title|='map_line_maker" + index + "'").css('transform', 'rotate(' + angle_t + 'deg)');

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
    // 최적경로 구하기
    /*
    function showOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates, scheduleStatus) {
        // 스케줄 마커들의 좌표를 추출하여 경유지로 설정
        var viaPoints = scheduleMarkerCoordinates.map(function(coordinate, index) {
            if (index === 0 || index === scheduleMarkerCoordinates.length - 1) {
                // 출발지 또는 도착지인 경우, 무시하고 continue
                return null;
            }
            return {
                "viaPointId": "point_" + index,
                "viaPointName": "point_" + index,
                "viaY": coordinate._lat || coordinate.lng(), // 수정
                "viaX": coordinate._lng || coordinate.lat(), // 수정
                //"status": scheduleStatus[index] || scheduleStatus[index], // 수정
                "viaTime": 600
            };
        }).filter(function(point) {
            return point !== null; // 출발지와 도착지를 제외하기 위해 null을 제거
        });
        // 좌표값만을 추출하여 passList에 저장
        var passList = viaPoints.map(function(point) {
            // 좌표값을 EPSG3857로 변환
            var latlng = new Tmapv2.Point(point.viaX, point.viaY);
            var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
            // return convertPoint._lng + "," + convertPoint._lat;
            return point.viaX + "," + point.viaY;
        }).join("_");

        // 직선거리 계산
        const distance = getDistance(startY, startX, scheduleMarkerCoordinates, 5);
        // console.log(`출발지와 도착지 사이의 직선거리: ${distance.toFixed(2)} km`);
        var straightDistance = distance.toFixed(2);
        if (straightDistance >= 5) {
            jalert('일정과 일정 사이의 거리가 너무 멀어 최적경로 표기가 어렵습니다.(' + straightDistance + 'km)');
            return false;
        }
        // passList가 존재할 때만 데이터에 passList를 포함시킴
        let requestData = {
            "reqCoordType": "WGS84GEO",
            "resCoordType": "EPSG3857",
            "startName": "출발",
            "startX": startY, // 수정
            "startY": startX, // 수정
            "endName": "도착",
            "endX": endY, // 수정
            "endY": endX, // 수정
            "endID": "goal",
            // "searchOption": "10"
        };

        if (passList) {
            requestData.passList = passList; // 경유지 좌표값 추가
        }
        let dataToSend = JSON.stringify(requestData);

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
                var resultData = response.features;
                // 경로 및 시간 정보를 처리
                var totalidstance = ((resultData[0].properties.totalDistance) / 1000).toFixed(1);
                var totalTime = ((resultData[0].properties.totalTime) / 60).toFixed(0);
                // console.log('보행자 경로안내: 총 거리 : ' + totalidstance + "km," + ' 총 시간 : ' + totalTime + '분');

                var elementWithAriaLabel = $('.optimal_box').filter(function() {
                    return $(this).attr('aria-label') !== undefined;
                });

                // 요소의 aria-label 속성 값에서 / 이후의 값을 가져옵니다.
                var labelText = elementWithAriaLabel.attr('aria-label').split('/')[1].trim();

                // 각 경유지까지의 예상 소요 시간 계산 함수 호출
                calculateWalkingTime(startX, startY, endX, endY, scheduleMarkerCoordinates, function(totalWalkingTime) {

                    // 기존에 그려진 라인 & 마커가 있다면 초기화
                    if (resultdrawArr.length > 0) {
                        for (var i in resultdrawArr) {
                            resultdrawArr[i].setMap(null);
                        }
                        resultdrawArr = [];
                    }

                    // 기존에 그려진 라인 & 마커가 있다면 초기화
                    if (resultdrawArr.length > 0) {
                        for (var i in resultdrawArr) {
                            resultdrawArr[i].setMap(null);
                        }
                        resultdrawArr = [];
                    }

                    drawInfoArr = [];
                    polylines = [];

                    var j = 0;
                    var linecolor = "#140082";
                    var pp_marker = 0;

                    for (var i in resultData) {
                        var geometry = resultData[i].geometry;
                        var properties = resultData[i].properties;

                        if (geometry.type == "LineString") {
                            var path = [];
                            var polylinePath = [];
                            for (var j in geometry.coordinates) {
                                var latlng = new Tmapv2.Point(geometry.coordinates[j][0], geometry.coordinates[j][1]);
                                var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
                                var convertChange = new naver.maps.LatLng(convertPoint._lat, convertPoint._lng);
                                path.push(convertChange);
                            }
                            // Naver Maps API의 Polyline로 변경
                            var polyline_ = new naver.maps.Polyline({
                                path: path,
                                strokeColor: linecolor,
                                strokeOpacity: 0.8,
                                strokeWeight: 7,
                                map: map
                            });
                            // console.log(polyline_);
                            // console.log(linecolor);

                            resultdrawArr.push(polyline_);
                            polylines.push(polyline_);
                        } else {
                            var markerImg = "";
                            var pType = "";
                            var size;
                            var anchor;

                            if (properties.pointType == "SP") { // 경로 출발지 마커
                                markerImg = "./img/mark_connect.png";
                                pType = "S";
                                size = new naver.maps.Size(18, 18);
                                anchor = new naver.maps.Point(18, 18);
                                contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                            } else if (properties.pointType == "EP") { // 경로 도착지 마커
                                markerImg = "./img/mark_connect.png";
                                pType = "E";
                                size = new naver.maps.Size(18, 18);
                                anchor = new naver.maps.Point(18, 18);
                                contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                                schedulehtml = '<p class="fs_23 fw_700 optimal_time">' + totalWalkingTime[pp_marker][0] + '<span class="fs_14">분</span></p>' +
                                    '<p class="fs_12 text_light_gray optimal_tance">' + totalWalkingTime[pp_marker][1] + 'km</p>';
                                pp_marker++;
                                var aria_cnt = (pp_marker * 2);
                                $('.optimal_box[aria-label="' + aria_cnt + ' / ' + labelText + '"]').html(schedulehtml);
                                // zIndexhtml = 5;
                            } else if (properties.pointType == "GP") { // 경로 이동 마커
                                markerImg = "./img/mark_connect.png";
                                pType = "E";
                                size = new naver.maps.Size(18, 18);
                                anchor = new naver.maps.Point(18, 18);
                                contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                            } else { // 경로 경유지 마커
                                markerImg = "./img/mark_connect.png";
                                pType = "P";
                                size = new naver.maps.Size(4, 4);
                                anchor = new naver.maps.Point(0, 0);
                                contenthtml = '<div><img src="' + markerImg + '" style="width:15px"></div>';
                                zIndexhtml = 0;
                                schedulehtml = '<p class="fs_23 fw_700 optimal_time">' + totalWalkingTime[pp_marker][0] + '<span class="fs_14">분</span></p>' +
                                    '<p class="fs_12 text_light_gray optimal_tance">' + totalWalkingTime[pp_marker][1] + 'km</p>';
                                pp_marker++;
                                var aria_cnt = (pp_marker * 2);
                                // 해당 div 선택 후 내용 삽입
                                $('.optimal_box[aria-label="' + aria_cnt + ' / ' + labelText + '"]').html(schedulehtml);
                            }
                            // console.log(scheduleStatus[j]);
                            // console.log(linecolor);
                            // console.log(j);
                            var latlon = new Tmapv2.Point(geometry.coordinates[0], geometry.coordinates[1]);
                            var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlon);
                            var routeInfoObj = {
                                markerImage: markerImg,
                                lng: convertPoint._lng,
                                lat: convertPoint._lat,
                                pointType: pType
                            };
                            var markerOptions = {
                                position: new naver.maps.LatLng(routeInfoObj.lat, routeInfoObj.lng),
                                map: map,
                                icon: {
                                    content: contenthtml,
                                    size: new naver.maps.Size(15, 15),
                                    origin: new naver.maps.Point(0, 0),
                                    anchor: new naver.maps.Point(7, 7)
                                },
                                zIndex: zIndexhtml
                            };
                            var marker_p = new naver.maps.Marker(markerOptions);

                            resultdrawArr.push(marker_p);
                            markers.push(marker_p);
                        }
                    };
                });

                // 성공 시 ajax로 DB에 log json 추가
                var sgdt_idx = $('#pedestrian_path_modal_sgdt_idx').val();

                var form_data = new FormData();
                form_data.append("act", "loadpath_add");
                form_data.append("sgdt_idx", sgdt_idx);
                // form_data.append("sllt_json_text", resultData);
                form_data.append("sllt_json_text", JSON.stringify(response));
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
                    success: function(data) {
                        if (data == 'Y') {} else {
                            jalert('잘못된 접근입니다.');
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    },
                });

            },
            error: function(request, status, error) {
                console.log(request.responseJSON.error.code);
                console.log(request.responseJSON.error);
                console.log("code:" + request.status + "\n" +
                    "message:" + request.responseText + "\n" +
                    "error:" + error);
                if (request.responseJSON.error.code == '3102') {
                    var errorMessage = '해당 서비스가 지원되지 않는 구간이라 최적 경로 안내가 어려워요.';
                } else if (request.responseJSON.error.code == '3002') {
                    var errorMessage = '길안내를 제공하지 않는 부분이 있어서, 최적 경로 안내가 어려워요.';
                } else if (request.responseJSON.error.code == '1009') {
                    var errorMessage = '일부 구간이 너무 멀어서, 최적 경로 안내가 힘들어요.';
                } else if (request.responseJSON.error.code == '9401') {
                    var errorMessage = '최적경로 조회는 두 개 이상의 일정이 입력되었을 때만 이용할 수 있어요.';
                } else if (request.responseJSON.error.code == '1100') {
                    var errorMessage = '최적경로는 최대 7개까지의 일정의 경로를 표시 가능해요.';
                } else if (request.responseJSON.error.code == '2200') {
                    var errorMessage = '최적경로 API에서 지원하지는 주소 범위입니다.';
                }

                jalert(errorMessage);
            }
        });
    }
    */
    // 출발점, 도착지, 경유지까지의 예상 소요 시간을 계산하는 함수
    function calculateWalkingTime(startX, startY, endX, endY, scheduleMarkerCoordinates, callback) {
        var arr_distance = [];

        // 두 번째 경유지부터 마지막 경유지까지의 예상 소요 시간 계산
        for (var i = 1; i < scheduleMarkerCoordinates.length; i++) {
            // getWalkingTime 함수를 호출하는 대신, 비동기 콜백 함수를 사용하여 예상 소요 시간을 처리
            getWalkingTime(scheduleMarkerCoordinates[i - 1]._lat, scheduleMarkerCoordinates[i - 1]._lng, scheduleMarkerCoordinates[i]._lat, scheduleMarkerCoordinates[i]._lng, function(totalTime, totalidstance) {
                arr_distance.push([totalTime, totalidstance]);
                // 모든 예상 소요 시간이 계산된 후에 콜백 함수를 호출하여 결과 반환
                if (arr_distance.length === scheduleMarkerCoordinates.length - 1) {
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

    function schedule_map(sgdt_idx) {
        // $('#splinner_modal').modal('toggle');
        var form_data = new FormData();
        form_data.append("act", "schedule_map_list");
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
                if (data) {
                    var my_profile = data.my_profile;
                    var st_lat = data.my_lat;
                    var st_lng = data.mt_long;

                    // 이미지가 들어있는 부모 요소를 찾습니다.
                    var parentElement = document.getElementById('map');

                    // 부모 요소의 자식 요소로 있는 모든 이미지를 제거합니다.
                    while (parentElement.firstChild) {
                        parentElement.removeChild(parentElement.firstChild);
                    }

                    initializeMap(my_profile, st_lat, st_lng, data);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function map_panto(lat, lng) {
        map.setCenter(new naver.maps.LatLng(lat, lng));


        var optBottom = document.querySelector('.opt_bottom');
        if (optBottom) {
            var transformY = optBottom.style.transform;
            if (transformY == 'translateY(0px)') {
                map.panBy(new naver.maps.Point(0, 180)); // 위로 180 픽셀 이동
            }
        }
    }

    function f_my_location_btn(mt_idx) {
        var form_data = new FormData();
        var sgdt_idx = $('#sgdt_idx').val();
        schedule_map(sgdt_idx);
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
                    var lat = data.mlt_lat;
                    var lng = data.mlt_long;

                    map.setCenter(new naver.maps.LatLng(lat, lng));

                    var optBottom = document.querySelector('.opt_bottom');
                    if (optBottom) {
                        var transformY = optBottom.style.transform;
                        if (transformY == 'translateY(0px)') {
                            map.panBy(new naver.maps.Point(0, 180)); // 위로 180 픽셀 이동
                        }
                    }
                    setTimeout(() => {
                        pedestrian_path_check(sgdt_idx);
                    }, 1000);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
        // $('#splinner_modal').modal('hide');
        console.timeEnd("forEachLoopExecutionTime");
    }
    // 실시간 마커 이동
    function marker_reload(sgdt_idx) {
        var form_data = new FormData();
        form_data.append("act", "marker_reload");
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
                if (data) {
                    var my_profile = data.my_profile;
                    var st_lat = data.my_lat;
                    var st_lng = data.mt_long;
                    var session_lat = data.session_lat;
                    var session_long = data.session_long;

                    initializeMap(my_profile, st_lat, st_lng, data);
                    map_panto(st_lat, st_lng);
                    get_weather();
                    f_get_box_list();
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    setInterval(() => {
        var sgdt_idx = $('#sgdt_idx').val();
        // marker_reload(sgdt_idx);
        // console.log(sgdt_idx);
    }, 30000);
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>