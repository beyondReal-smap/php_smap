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

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$sgt_row = $DB->getone('smap_group_t');

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_idx', $sgt_row['sgt_idx']);
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_owner_chk', 'Y');
$sgdt_row = $DB->getone('smap_group_detail_t');

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
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>
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
                </script>
                <!-- <div class="mt_25">
                    <p class="tit_h2">위치</p>
                    <div id="main_location_box"></div>
                </div> -->
            </div>
        </section>
        <!-- 지도 wrap -->
        <section class="pg_map_wrap" id="naver_map">
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
            <section class="opt_bottom">
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
        <? } ?>
        <!-- D-4 그룹 생성 직후 홈화면(오너)에 필요한 부분입니다. [끝] -->
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
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0">표시하기</button>
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
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="location.href='plan_info.php'">연장할래요!</button>
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
        slidesPerView: 12,
        spaceBetween: 12,
        breakpoints: {
            220: {
                slidesPerView: 4.5,
            },
            280: {
                slidesPerView: 5.5,
            },
            320: {
                slidesPerView: 6.3,
            },
            330: {
                slidesPerView: 6.5,
            },
            370: {
                slidesPerView: 7,
            },
            410: {
                slidesPerView: 7.6,
            },
            420: {
                slidesPerView: 8,
            },
            450: {
                slidesPerView: 8.5,
            },
            500: {
                slidesPerView: 9.5,
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
    function initializeMap(my_profile, st_lat, st_lng, markerData) {
        var map = new naver.maps.Map("naver_map", {
            center: new naver.maps.LatLng(st_lat, st_lng),
            zoom: 19,
            mapTypeControl: false
        });

        // 기존 프로필 마커 추가
        var profileMarkerOptions = {
            position: new naver.maps.LatLng(st_lat, st_lng),
            map: map,
            icon: {
                content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                size: new naver.maps.Size(44, 44),
                origin: new naver.maps.Point(0, 0),
                anchor: new naver.maps.Point(22, 22)
            }
        };

        var profileMarker = new naver.maps.Marker(profileMarkerOptions);

        // 스케줄 마커 추가
        if (markerData.schedule_chk === 'Y') {
            var positions = [];
            for (var i = 1; i <= markerData.count; i++) {
                var markerOptions = {
                    position: new naver.maps.LatLng(markerData['markerLat_' + i], markerData['markerLong_' + i]),
                    map: map,
                    icon: {
                        content: markerData['markerContent_' + i],
                        size: new naver.maps.Size(61, 61),
                        origin: new naver.maps.Point(0, 0),
                        anchor: new naver.maps.Point(30, 30)
                    }
                };

                var marker = new naver.maps.Marker(markerOptions);
                positions.push(marker.getPosition());
            }
        }

        map.setCursor('pointer');

        // 지도 마커클릭시 상세내역 보여짐
        $('.point_wrap').click(function() {
            $('.point_wrap').click(function() {
                $(this).find('.infobox').addClass('on');
                $('.point_wrap').not(this).find('.infobox').removeClass('on');
            });
        });
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
                    var parentElement = document.getElementById('naver_map');

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
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>