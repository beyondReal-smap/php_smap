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
        alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
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

/* 
$member_info_row = get_member_t_info($_SESSION['_mt_idx']);
$current_date = date("Y-m-d H:i:s"); 
*/
?>
<style>
    html {
        height: 100%;
        overflow-y: unset !important;
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
    }
</style>
<div class="container-fluid idx_pg bg_main px-0 py-0 h-100">
    <div class="idx_pg_div">
        <section class="main_top">
            <div>
                <!--D-6 멤버 스케줄 미참석 팝업 임시로 넣어놓았습니다.-->
                <div class="px_16 py-3 bg-white top_weather" id="top_weather_box">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <!-- 로딩할때 사용 -->
                        <div class="date_weather d-flex align-items-center flex-wrap">
                            <div class="d-flex align-items-center fs_14 fw_600 text_dynamic mr-1 mb_08"><?= DateType(date("Y-m-d"), 3) ?><span class="loader loader_sm mr-2 ml-2"></span></div>
                        </div>
                    </div>
                    <p class="fs_12 text_light_gray text_dynamic p_content line_h1_2">잠시만 기다려주세요! 기상 데이터를 가져오는 중입니다.!</p>
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
                                    $('#top_weather_box').html(data);
                                }
                            },
                            error: function(err) {
                                console.log(err);
                            },
                        });
                    }
                </script>
            </div>
        </section>
        <!-- 지도 wrap -->
        <script script src="https://apis.openapi.sk.com/tmap/vectorjs?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
        <script src="https://apis.openapi.sk.com/tmap/jsv2?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
        <script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>

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
                <div class="" id="map" style="height:100%">
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
        ?>
            <section class="opt_bottom" style="transform: translateY(57%);">
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
                            <div id="group_member_list_box"></div>
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
                            <div id="schedule_list_box"></div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- D-4 그룹 생성 직후 홈화면(오너)에 필요한 부분입니다. [끝] -->
        <? } else {  // 그룹원일 경우 
        ?>
            <section class="opt_bottom">
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
                            <div id="schedule_list_box"></div>
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
                    <p class="optim_plan"><span>무료플랜 사용횟수</span></p>
                    <p class="text-primary fs_14 fw_600 text_dynamic mt-3 line_h1_4" id="pathCountday">금일 2회 사용 가능</p>
                    <p class=" text-primary fs_14 fw_600 text_dynamic line_h1_4" id="pathCountmonth">이번달 60회 사용 가능</p>
                    <p class="text_gray fs_11 text_dynamic line_h1_3 mt-2"> 무료 플랜은 일 2회 월 60회까지 최적경로 표시가 가능합니다.</p>
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
    var pathCount;

    function toggleInfobox() {
        var infoboxes = document.getElementsByClassName('infobox');
        var img = document.getElementById('infoboxImg');

        // 이미지 경로 변경
        if (img.src.includes('ico_info_on.png')) {
            img.src = './img/ico_info_off.png';
        } else {
            img.src = './img/ico_info_on.png';
        }

        // infobox 클래스 토글
        for (var i = 0; i < infoboxes.length; i++) {
            infoboxes[i].classList.toggle('on');
        }
    }
    $(document).ready(function() {
        // console.time("forEachLoopExecutionTime");
        f_get_box_list2();
        f_get_box_list();
        schedule_map(<?= $sgdt_row['sgdt_idx'] ?>);
        setTimeout(() => {
            pedestrian_path_check(<?= $sgdt_row['sgdt_idx'] ?>);
        }, 200);
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
                    $('#pathCountday').text("일 사용횟수를 모두 사용하셨습니다."); // 모달에 표시
                    $('#pathCountmonth').text("이번달 " + data.path_count_month + "회 사용 가능"); // 모달에 표시
                    $('#showPathButton').prop('disabled', true);
                    $('#path_day_count').val(data.path_count_day);
                    $('#optimal_modal').modal('show');

                } else if (data.result == 'Y') {
                    $('#pathCountday').text("금일 " + data.path_count_day + "회 사용 가능 "); // 모달에 표시
                    $('#pathCountmonth').text("이번달 " + data.path_count_month + "회 사용 가능"); // 모달에 표시
                    $('#showPathButton').prop('disabled', false);
                    $('#path_day_count').val(data.path_count_day);
                    $('#optimal_modal').modal('show');
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
        }, 200);
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
</script>
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
    // 버튼 엘리먼트 찾기
    var showPathButton = document.getElementById('showPathButton');

    function initializeMap(my_profile, st_lat, st_lng, markerData) {
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
        markers.push(profileMarker);

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
            markers.push(profileMarker);
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

    // 버튼에 클릭 이벤트 핸들러 등록
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
            timeout: 5000,
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

                    // console.log(ajaxData); // 전체 JSON 데이터 출력
                    // console.log(resultData); // features 배열 출력
                    // 기존에 그려진 라인 & 마커가 있다면 초기화
                    if (resultdrawArr.length > 0) {
                        for (var i in resultdrawArr) {
                            resultdrawArr[i].setMap(null);
                        }
                        resultdrawArr = [];
                    }

                    drawInfoArr = [];
                    polylines = [];
                    // console.log(scheduleStatus);
                    var j = 0;
                    var linecolor = "#4fb534";

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
                                // endIcon: naver.maps.PointingIcon.BLOCK_ARROW,
                                // endIconSize: 13,
                                strokeColor: linecolor,
                                strokeOpacity: 0.7,
                                strokeWeight: 7,
                                map: map,
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

                            if (properties.pointType == "SP") {
                                markerImg = "./img/mark_connect.png";
                                pType = "S";
                                size = new naver.maps.Size(18, 18);
                                anchor = new naver.maps.Point(18, 18);
                            } else if (properties.pointType == "EP") {
                                markerImg = "./img/mark_connect.png";
                                pType = "E";
                                size = new naver.maps.Size(18, 18);
                                anchor = new naver.maps.Point(18, 18);
                            } else {
                                markerImg = "./img/mark_connect.png";
                                pType = "P";
                                size = new naver.maps.Size(4, 4);
                                anchor = new naver.maps.Point(0, 0);
                            }
                            if (scheduleStatus[j] == 'point_ing') {
                                //linecolor = '#d58c19';
                                j++;

                            } else if (scheduleStatus[j] == 'point_done') {
                                //linecolor = '#4fb534';
                                j++;

                            } else if (scheduleStatus[j] == 'point_gonna') {
                                //linecolor = '#b4b3b2';
                                j++;
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
                                    content: '<div><img src="' + markerImg + '" style="width:15px"></div>',
                                    size: new naver.maps.Size(15, 15),
                                    origin: new naver.maps.Point(0, 0),
                                    anchor: new naver.maps.Point(7, 7)
                                },
                                zIndex: 0
                            };
                            // console.log(properties.pointType);
                            // console.log(markerOptions);
                            var marker_p = new naver.maps.Marker(markerOptions);

                            resultdrawArr.push(marker_p);
                            markers.push(marker_p);
                        }
                    };
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
        $('#splinner_modal').modal('hide');
        console.timeEnd("forEachLoopExecutionTime");
    }
    // 두 지점의 위도와 경도를 인자로 받아 직선거리를 계산하는 함수
    function getDistance(lat1, lon1, lat2, lon2) {
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
        const distance = getDistance(startY, startX, endY, endX);

        console.log(`출발지와 도착지 사이의 직선거리: ${distance.toFixed(2)} km`);
        var straightDistance = distance.toFixed(2);
        if (straightDistance >= 5) {
            jalert('출발지와 도착지 사이의 거리가 너무 멀어 최적경로 표기가 어렵습니다.');
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
                var linecolor = "#4fb534";

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

                        if (properties.pointType == "SP") {
                            markerImg = "./img/mark_connect.png";
                            pType = "S";
                            size = new naver.maps.Size(18, 18);
                            anchor = new naver.maps.Point(18, 18);
                        } else if (properties.pointType == "EP") {
                            markerImg = "./img/mark_connect.png";
                            pType = "E";
                            size = new naver.maps.Size(18, 18);
                            anchor = new naver.maps.Point(18, 18);
                        } else {
                            markerImg = "./img/mark_connect.png";
                            pType = "P";
                            size = new naver.maps.Size(4, 4);
                            anchor = new naver.maps.Point(0, 0);
                        }
                        if (scheduleStatus[j] == 'point_ing') {
                            j++;

                        } else if (scheduleStatus[j] == 'point_done') {
                            j++;

                        } else if (scheduleStatus[j] == 'point_gonna') {
                            j++;
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
                                content: '<div><img src="' + markerImg + '" style="width:15px"></div>',
                                size: new naver.maps.Size(15, 15),
                                origin: new naver.maps.Point(0, 0),
                                anchor: new naver.maps.Point(7, 7)
                            },
                            zIndex: 0
                        };
                        var marker_p = new naver.maps.Marker(markerOptions);

                        resultdrawArr.push(marker_p);
                        markers.push(marker_p);
                    }
                };

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
                    var errorMessage = '일정을 2개 이상 입력 시 최적경로 표시 가능해요.';
                }

                jalert(errorMessage);
            }
        });
    }

    function schedule_map(sgdt_idx) {
        $('#splinner_modal').modal('toggle');
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
                    }, 200);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
        $('#splinner_modal').modal('hide');
        console.timeEnd("forEachLoopExecutionTime");
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>