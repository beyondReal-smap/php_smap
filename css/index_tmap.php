<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '1';
$h_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
}
$s_date = date("Y-m-d");
//오너인 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$row = $DB->getone('smap_group_t', 'count(*) as cnt');
$sgt_cnt = $row['cnt'];

//리더인 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgdt_leader_chk', 'Y');
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_discharge', 'N');
$DB->where('sgdt_exit', 'N');
$row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
$sgdt_leader_cnt = $row['cnt'];

//초대된 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgdt_owner_chk', 'N');
$DB->where('sgdt_show', 'Y');
$row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
$sgdt_cnt = $row['cnt'];

// 그룹생성여부
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$sgt_row = $DB->getone('smap_group_t');

//
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_idx', $sgt_row['sgt_idx']);
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_owner_chk', 'Y');
$sgdt_row = $DB->getone('smap_group_detail_t');

if (!$sgdt_row['sgdt_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_owner_chk', 'N');
    $sgdt_row = $DB->getone('smap_group_detail_t');
}

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
<script script src="https://apis.openapi.sk.com/tmap/vectorjs?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
<script src="https://apis.openapi.sk.com/tmap/jsv2?version=1&appKey=6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq"></script>
<!-- <script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script> -->
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
                        get_location_main();
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
                            timeout: 5000,
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

                    function get_location_main() {
                        var form_data = new FormData();
                        form_data.append("act", "main_location");

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
                                    $('#main_location_box').html(data);
                                }
                            },
                            error: function(err) {
                                console.log(err);
                            },
                        });
                    }
                    /*
                    function get_schedule_main() {
                        var form_data = new FormData();
                        form_data.append("act", "main_schedule");

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
                                    $('#main_schedule_box').html(data);
                                }
                            },
                            error: function(err) {
                                console.log(err);
                            },
                        });
                    }
                    */
                </script>
                <!-- <div class="mt_25">
                    <p class="tit_h2">위치</p>
                    <div id="main_location_box"></div>
                </div> -->
            </div>
        </section>
        <!-- 지도 wrap -->
        <section class="pg_map_wrap" id="">
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
            </div>

        </section>
        <!-- D-4 그룹 생성 직후 홈화면(오너)에 필요한 부분입니다. [시작] -->
        <? if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
            $DB->where('mt_idx', $_SESSION['_mt_idx']);
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
            $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

            unset($list_sgt);
            $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
            $DB->where('sgt_show', 'Y');
            $DB->orderBy("sgt_udate", "desc");
            $DB->orderBy("sgt_idx", "asc");
            $list_sgt = $DB->get('smap_group_t');
        ?>
            <section class="opt_bottom" style="transform: translateY(70%);">
                <div class="top_bar_wrap text-center pt_08">
                    <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                    <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
                </div>
                <div class="">
                    <!--프로필  tab_scroll scroll_bar_x-->
                    <div class="mem_wrap mem_swiper">
                        <div class="swiper-wrapper d-flex">
                            <div class="swiper-slide checks mem_box">
                                <label>
                                    <input type="radio" name="rd2" checked onclick="mem_schedule(<?= $sgdt_row['sgdt_idx'] ?>);">
                                    <div class="prd_img mx-auto">
                                        <div class="rect_square rounded_14">
                                            <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="프로필이미지" />
                                        </div>
                                    </div>
                                    <!-- 처음은 사용자 본인이 나옵니다. -->
                                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">나</p>
                                </label>
                            </div>
                            <?php
                            if ($list_sgt) {
                                foreach ($list_sgt as $row_sgt) {
                                    $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
                                    unset($list_sgdt);
                                    $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
                                    $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
                                    if ($invite_cnt || $list_sgdt['data']) {
                                        if ($list_sgdt['data']) {
                                            foreach ($list_sgdt['data'] as $key => $val) {
                            ?>
                                                <div class="swiper-slide checks mem_box">
                                                    <label>
                                                        <input type="radio" name="rd2" onclick="mem_schedule(<?= $val['sgdt_idx'] ?>);">
                                                        <!-- <div class="prd_img mx-auto on_arm"> -->
                                                        <div class="prd_img mx-auto "> <!-- 알림왔을 때 on_arm 추가 -->
                                                            <div class="rect_square rounded_14">
                                                                <img src="<?= $val['mt_file1_url'] ?>" alt="프로필이미지" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                                                            </div>
                                                        </div>
                                                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic"><?= $val['mt_name'] ?></p>
                                                    </label>
                                                </div>
                            <?
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                            <!-- 그룹원 추가 -->
                            <? if ($sgt_cnt > 0) { ?>
                                <div class="swiper-slide mem_box add_mem_box" onclick="location.href='./group'">
                                    <button class="btn mem_add">
                                        <i class="xi-plus-min fs_20"></i>
                                    </button>
                                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">그룹원 추가</p>
                                </div>
                            <? } ?>
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
                            <div id="schedule_list_box"></div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- D-4 그룹 생성 직후 홈화면(오너)에 필요한 부분입니다. [끝] -->
        <? } else { ?>
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
            <input type="hidden" name="group_out_modal_sgdt_idx" id="group_out_modal_sgdt_idx" value="" />
            <div class="modal-body text-center pb-4">
                <img src="./img/optimal_map.png" width="48px" class="pt-3" alt="최적의경로" />
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">현재 위치에서부터 다음 일정까지의
                    최적의 경로를 표시할까요?
                </p>
                <p class="fs_12 text_dynamic text_gray mt-2 line_h1_2">최적경로 및 예상시간과 거리가 표시됩니다.</p>
                <div class="optimal_info_wrap">
                    <p class="optim_plan"><span>무료플랜 사용횟수</span></p>
                    <p class="text-primary fs_14 fw_600 text_dynamic mt-3 line_h1_4">금일 2회 사용 가능</p>
                    <p class="text-primary fs_14 fw_600 text_dynamic line_h1_4">이번달 60회 사용 가능</p>
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
                            <p class="text-primary fs_14 fw_700">2개</p>
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
    $(document).ready(function() {
        schedule_map(<?= $sgdt_row['sgdt_idx'] ?>);
        f_get_box_list();
    });
    //최적경로 표시 모달 띄우기
    function pedestrian_path_modal(sgdt_idx) {
        if (sgdt_idx) {
            $('#pedestrian_path_modal_sgdt_idx').val(sgdt_idx);
        }
        $('#optimal_modal').modal('show');
    }
    //멤버아이콘 클릭시
    function mem_schedule(sgdt_idx) {
        document.getElementById('sgdt_idx').value = sgdt_idx;
        schedule_map(sgdt_idx);
        f_get_box_list();
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
    //프로필 슬라이더
    var mem_swiper = new Swiper(".mem_swiper", {
        slidesPerView: 7.9,
        spaceBetween: 12,
        breakpoints: {
            220: {
                slidesPerView: 4,
            },
            280: {
                slidesPerView: 4.5,
            },
            300: {
                slidesPerView: 4.8,
            },
            320: {
                slidesPerView: 5.2,
            },
            340: {
                slidesPerView: 5.8,
            },
            360: {
                slidesPerView: 6.2,
            },
            400: {
                slidesPerView: 6.5,
            },
            410: {
                slidesPerView: 7,
            },
            450: {
                slidesPerView: 7.6,
            },
            500: {
                slidesPerView: 7.9,
            },
        }

    });

    // 문서 전체를 클릭했을 때 마커 상세내역 사라짐
    $(document).click(function(event) {
        if (!$(event.target).closest('.point_wrap, .infobox').length) {
            $('.point_wrap .infobox').removeClass('on');
        }
    });
</script>
<script>
    var map; // 전역 변수로 map을 선언하여 다른 함수에서도 사용 가능하도록 합니다.
    var scheduleMarkers = []; // 스케줄 마커를 저장할 배열입니다.
    var optimalPath; // 최적 경로를 표시할 변수입니다.
    var drawInfoArr = [];
    var resultdrawArr = [];

    function initializeMap(my_profile, st_lat, st_lng, markerData) {
        map = new Tmapv2.Map("map", {
            center: new Tmapv2.LatLng(st_lat, st_lng),
            zoom: 17,
            mapTypeControl: false
        });


        // 기존 프로필 마커 추가
        var profileMarker = new Tmapv2.Marker({
            position: new Tmapv2.LatLng(st_lat, st_lng),
            map: map,
            icon: my_profile,
            iconSize: new Tmapv2.Size(44, 44),
            // icon: new Tmapv2.IconHtml('<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>')
        });
        // 스케줄 마커 추가
        if (markerData.schedule_chk === 'Y') {
            for (var i = 1; i <= markerData.count; i++) {
                var marker;
                if (i === 1) {
                    marker = new Tmapv2.Marker({
                        position: new Tmapv2.LatLng(markerData['markerLat_' + i], markerData['markerLong_' + i]),
                        map: map,
                        icon: 'https://tmapapi.sktelecom.com/upload/tmap/marker/pin_r_m_1.png',
                        iconSize: new Tmapv2.Size(24, 38)
                    });
                    // 출발지 좌표
                    var startX = markerData['markerLat_' + i];
                    var startY = markerData['markerLong_' + i];
                } else if (i === markerData.count) {
                    marker = new Tmapv2.Marker({
                        position: new Tmapv2.LatLng(markerData['markerLat_' + i], markerData['markerLong_' + i]),
                        map: map,
                        icon: 'https://tmapapi.sktelecom.com/upload/tmap/marker/pin_r_m_' + i + '.png',
                        iconSize: new Tmapv2.Size(24, 38)
                    });
                    // 도착지 좌표
                    var endX = markerData['markerLat_' + i];
                    var endY = markerData['markerLong_' + i];
                } else {
                    var markerColor = getMarkerColorBasedOnSchedule(i);
                    marker = new Tmapv2.Marker({
                        position: new Tmapv2.LatLng(markerData['markerLat_' + i], markerData['markerLong_' + i]),
                        map: map,
                        icon: markerColor,
                        iconSize: new Tmapv2.Size(24, 38)
                    });
                }
                scheduleMarkers.push(marker);
            }
            // 스케줄 마커의 개수
            var markerCount = markerData['count'];
            // 스케줄 마커의 좌표 배열
            var scheduleMarkerCoordinates = [];
            for (var i = 1; i <= markerCount; i++) {
                var lat = markerData['markerLat_' + i];
                var lng = markerData['markerLong_' + i];
                scheduleMarkerCoordinates.push(new Tmapv2.LatLng(lat, lng));
            }
        }
        map.setCenter(new Tmapv2.LatLng(st_lat, st_lng));

        // 버튼 엘리먼트 찾기
        var showPathButton = document.getElementById('showPathButton');

        // 버튼에 클릭 이벤트 핸들러 등록
        showPathButton.addEventListener('click', function(event) {
            showOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates);
            $('#optimal_modal').modal('hide');
        });
    }

    function showOptimalPath(startX, startY, endX, endY, scheduleMarkerCoordinates) {
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

        var headers = {};
        headers["appKey"] = "6BGAw3YxGA6tVPu0Olbio7fwXiGjDV7g4VRlF3Pq";
        // 최적 경로 요청
        $.ajax({
            method: "POST",
            headers: headers,
            url: "https://apis.openapi.sk.com/tmap/routes/pedestrian?version=1&format=json&callback=result",
            async: false,
            contentType: "application/json",
            data: JSON.stringify({
                "reqCoordType": "WGS84GEO",
                "resCoordType": "EPSG3857",
                "startName": "출발",
                "startX": startY, // 수정
                "startY": startX, // 수정
                "endName": "도착",
                "endX": endY, // 수정
                "endY": endX, // 수정
                "endID": "goal",
                "passList": passList, // 경유지 좌표값 추가
            }),
            success: function(response) {
                var resultData = response.features;
                console.log(resultData);
                // 기존에 그려진 라인 & 마커가 있다면 초기화
                if (resultdrawArr.length > 0) {
                    for (var i in resultdrawArr) {
                        resultdrawArr[i].setMap(null);
                    }
                    resultdrawArr = [];
                }

                drawInfoArr = [];

                for (var i in resultData) {
                    var geometry = resultData[i].geometry;
                    var properties = resultData[i].properties;

                    if (geometry.type == "LineString") {
                        var path = [];
                        for (var j in geometry.coordinates) {
                            var latlng = new Tmapv2.Point(geometry.coordinates[j][0], geometry.coordinates[j][1]);
                            var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlng);
                            var convertChange = new Tmapv2.LatLng(convertPoint._lat, convertPoint._lng);
                            path.push(convertChange);
                        }

                        // 라인 추가
                        var polyline_ = new Tmapv2.Polyline({
                            path: path,
                            strokeColor: "#FF0000",
                            strokeWeight: 6,
                            map: map
                        });

                        resultdrawArr.push(polyline_);
                    } else {
                        var markerImg = "";
                        var pType = "";
                        var size;

                        if (properties.pointType == "S") {
                            markerImg = "/upload/tmap/marker/pin_r_m_s.png";
                            pType = "S";
                            size = new Tmapv2.Size(24, 38);
                        } else if (properties.pointType == "E") {
                            markerImg = "/upload/tmap/marker/pin_r_m_e.png";
                            pType = "E";
                            size = new Tmapv2.Size(24, 38);
                        } else {
                            markerImg = "http://topopen.tmap.co.kr/imgs/point.png";
                            pType = "P";
                            size = new Tmapv2.Size(8, 8);
                        }

                        var latlon = new Tmapv2.Point(geometry.coordinates[0], geometry.coordinates[1]);
                        var convertPoint = new Tmapv2.Projection.convertEPSG3857ToWGS84GEO(latlon);
                        var routeInfoObj = {
                            markerImage: markerImg,
                            lng: convertPoint._lng,
                            lat: convertPoint._lat,
                            pointType: pType
                        };

                        // Marker 추가
                        var marker_p = new Tmapv2.Marker({
                            position: new Tmapv2.LatLng(routeInfoObj.lat, routeInfoObj.lng),
                            icon: routeInfoObj.markerImage,
                            iconSize: size,
                            map: map
                        });

                        resultdrawArr.push(marker_p);
                    }
                }
                //drawLine(drawInfoArr);

            },
            error: function(request, status, error) {
                console.log("code:" + request.status + "\n" +
                    "message:" + request.responseText + "\n" +
                    "error:" + error);
            }
        });
    }

    function drawLine(arrPoint) {
        var polyline_;

        polyline_ = new Tmapv2.Polyline({
            path: arrPoint,
            strokeColor: "#DD0000",
            strokeWeight: 6,
            map: map
        });
        resultdrawArr.push(polyline_);
    }

    function schedule_map(sgdt_idx) {
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
    // 스케줄에 따라 마커의 색상이나 아이콘을 반환하는 함수
    function getMarkerColorBasedOnSchedule(content) {
        // 스케줄에 따라 마커의 색상이나 아이콘을 설정하는 로직을 추가하세요.
        // 예시: content에 따라 색상을 지정하거나, 특정 조건에 따라 아이콘을 선택합니다.
        // 여기서는 임의로 색상을 선택하도록 했습니다.
        return 'https://tmapapi.sktelecom.com/upload/tmap/marker/pin_b_m_' + content + '.png';
    }

    function map_panto(lat, lng) {
        map.setCenter(new Tmapv2.LatLng(lat, lng));
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>