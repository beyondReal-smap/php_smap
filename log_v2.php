<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '5';
$h_menu = '5';
$_SUB_HEAD_TITLE = "로그";
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

if ($_GET['sdate'] == '') {
    $_GET['sdate'] = date('Y-m-d');
}
$sdate = date('Y-m-d');
$tt = strtotime($sdate);

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('mt_show', 'Y');
$mem_row = $DB->getone('member_t');
if ($mem_row['mt_level'] == 5) {
    // 오늘날짜로부터 14일 전까지 표시
    $log_start_date = date('Y-m-d', strtotime($sdate . '-14 days'));
    $log_end_date = date('Y-m-d');
} else {
    // 오늘날짜로부터 하루 전까지 표시
    $log_start_date = date('Y-m-d', strtotime($sdate . '-2 day'));
    $log_end_date = date('Y-m-d');
}

if (!$_GET['sgdt_mt_idx']) {
    $_GET['sgdt_mt_idx'] = $_SESSION['_mt_idx'];
}
$row_slmt['sgdt_mt_idx'] = $_GET['sgdt_mt_idx'];

$mt_location_info = get_member_location_log_t_info($row_slmt['sgdt_mt_idx']);
$mt_info = get_member_t_info($row_slmt['sgdt_mt_idx']);

$m_mt_lat = $mt_location_info['mlt_lat'];
$m_mt_long = $mt_location_info['mlt_long'];
$mt_file1_url = get_image_url($mt_info['mt_file1']);
$sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
$sgdt_leader_cnt = f_get_leader_cnt($_SESSION['_mt_idx']); //리더인 그룹수
$sgdt_cnt = f_group_invite_cnt($_SESSION['_mt_idx']); //초대된 그룹수
$sgt_row = f_group_info($_SESSION['_mt_idx']); // 그룹생성여부


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
    .sch_cld_wrap {
        padding: 1rem 0;
    }
</style>
<div class="container sub_pg  log_wrap px-0">
    <!-- 달력 -->
    <section class="sch_cld_wrap bg-white ">
        <input type="hidden" name="week_calendar" id="week_calendar" value="Y" />
        <input type="hidden" name="lsdate" id="lsdate" value="<?= $log_start_date ?>" />
        <input type="hidden" name="ledate" id="ledate" value="<?= $log_end_date ?>" />
        <input type="hidden" name="csdate" id="csdate" value="<?= $sdate ?>" />
        <input type="hidden" name="nmy" id="nmy" value="<?= $now_month_year ?>" />
        <input type="hidden" name="sgdt_mt_idx" id="sgdt_mt_idx" value="<?= $row_slmt['sgdt_mt_idx'] ?>" />
        <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
        <input type="hidden" name="event_start_date" id="event_start_date" value="<?= $_GET['sdate'] ?>" />
        <input type="hidden" name="map_mt_lat" id="map_mt_lat" value="<?= $m_mt_lat ?>" />
        <input type="hidden" name="map_mt_long" id="map_mt_long" value="<?= $m_mt_long ?>" />
        <input type="hidden" name="map_mt_file1" id="map_mt_file1" value="<?= $mt_file1_url ?>" />
        <input type="hidden" name="sst_idx" id="sst_idx" value="" />
        <div class="cld_head_wr">
            <div class="add_cal_tit">
                <button type="button" class="btn h-auto swiper-button-prev"><i class="xi-angle-left-min"></i></button>
                <div class="sel_month d-inline-flex flex-grow-1 text-centerf">
                    <a href="javascript:;" onclick="f_calendar_log_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="월 선택 아이콘" style="width:1.6rem; "></a>
                    <p class="fs_15 fw_600" id="calendar_date_title"><?= $calendar_date_title ?></p>
                </div>
                <button type="button" class="btn h-auto swiper-button-next"><i class="xi-angle-right-min"></i></button>
            </div>
            <!-- <div class="cld_head fs_12">
                <ul>
                    <li class="sun">일</li>
                    <li>월</li>
                    <li>화</li>
                    <li>수</li>
                    <li>목</li>
                    <li>금</li>
                    <li class="sat">토</li>
                </ul>
            </div> -->
        </div>
        <div id="schedule_calandar_box" class="cld_date_wrap"></div>
        <!-- <div id="location_info_box"></div> -->
    </section>
    <!-- 지도 -->
    <section class="log_map_wrap" id="map">
    </section>
    <!-- 로그 -->
    <?
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        $translateY = 71;
    } else {
        $translateY = 52;
    }
    ?>
    <section class="opt_bottom" style="transform: translateY(<?= $translateY ?>%);">
        <div class="top_bar_wrap text-center pt_08">
            <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
            <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
        </div>
        <div>
            <div class="px_16 mb-3">
                <div class="border bg-white rounded-lg px_16 py_16">
                    <!-- 위치조정 슬라이드 -->
                    <div class="border-bottom loc_rog_adj pb-4">
                        <p class="fs_16 fw_600">위치로그 탐색</p>
                        <div class="pt-4">
                            <input type="range" class="custom-range" id="timeSlider" min='1' max='1' value='1'>
                        </div>
                    </div>
                    <div class="mt-4">
                        <!-- 위치기록 요약 -->
                        <div class="loc_rog">
                            <p class="fs_16 fw_600">위치기록 요약</p>
                            <ul class="loc_rog_ul d-flex align-item-center justify-content-between py-3" id="location_log_box">
                                <li class="text-center border-right flex-fill loc_rog_ul_l11">
                                    <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">일정개수</p>
                                    <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0<span>개</span></p>
                                </li>
                                <li class="text-center border-right flex-fill loc_rog_ul_l12">
                                    <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">이동거리</p>
                                    <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0m</p>
                                </li>
                                <li class="text-center border-right flex-fill loc_rog_ul_l13">
                                    <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">이동시간</p>
                                    <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0분</p>
                                </li>
                                <li class="text-center flex-fill loc_rog_ul_l14">
                                    <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic">걸음수</p>
                                    <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0걸음</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
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
                <!-- <div class="grp_wrap">
                    <div class="border bg-white rounded-lg px_16 py_16">
                        <p class="fs_16 fw_600 mb-3">그룹원</p>
                        <form method="post" name="frm_group_list" id="frm_group_list" onsubmit="return false;">
                            <input type="hidden" name="act" id="act" value="group_member_list" />
                            <input type="hidden" name="obj_list2" id="obj_list2" value="group_member_list_box" />
                            <input type="hidden" name="obj_frm2" id="obj_frm2" value="frm_group_list" />
                            <input type="hidden" name="obj_uri2" id="obj_uri2" value="./location_update" />
                            <input type="hidden" name="obj_pg2" id="obj_pg2" value="1" />
                            <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                            <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                            <input type="hidden" name="group_sgdt_idx" id="group_sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
                        </form>
                        <div id="group_member_list_box"></div>
                    </div>
                </div> -->
                <div class="grp_wrap">
                    <div class="border bg-white rounded-lg px_16 py_16">
                        <p class="fs_16 fw_600 mb-3">그룹원</p>
                        <!--프로필 tab_scroll scroll_bar_x-->
                        <!-- <div class="" id="location_member_box"></div> -->
                        <div class="mem_wrap swiper mem_swiper">
                            <div class="swiper-wrapper d-flex ">
                                <div class="checks mem_box w_fit mr_12">
                                    <label>
                                        <input type="radio" name="member_r1" id="member_r1_<?= $_SESSION['_mt_idx'] ?>" value="<?= $_SESSION['_mt_idx'] ?>" checked />
                                        <div class="prd_img mx-auto" onclick="f_profile_click('<?= $_SESSION['_mt_idx'] ?>','<?= $sgdt_row['sgdt_idx'] ?>');">
                                            <!-- 알림왔을 때 on_arm 추가 -->
                                            <div class="rect_square rounded_14">
                                                <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                            </div>
                                        </div>
                                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic" onclick="f_profile_click('<?= $_SESSION['_mt_idx'] ?>','<?= $sgdt_row['sgdt_idx'] ?>');"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
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
                                                    <div class="checks mem_box w_fit mr_12">
                                                        <label>
                                                            <input type="radio" name="member_r1" id="member_r1_<?= $val['mt_idx'] ?>" value="<?= $val['mt_idx'] ?>" />
                                                            <div class="prd_img mx-auto" onclick="f_profile_click('<?= $val['mt_idx'] ?>','<?= $val['sgdt_idx'] ?>');">
                                                                <!-- 알림왔을 때 on_arm 추가 -->
                                                                <div class="rect_square rounded_14">
                                                                    <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                                                </div>
                                                            </div>
                                                            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic" onclick="f_profile_click('<?= $val['mt_idx'] ?>','<?= $val['sgdt_idx'] ?>');"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
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
                    </div>
                </div>
            <? } ?>
        </div>
    </section>
</div>
<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>위치가 등록되었습니다.</p> <!-- 성공메시지 -->
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>
<!-- H-1 그룹없음 / 무료플랜 플러팅 -->
<? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹만들기</span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700">아직 그룹을 만들지 않으셨네요.</p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">로그는 그룹원의 이동경로를 확인할 수 있습니다.
                    그룹을 만들고 이 기능을 사용해볼까요?
                </p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'">다음</button>
            </div>
        </div>
    </div>
<? } ?>

<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>"></script>
<script>
    var map = new naver.maps.Map("map", {
        center: new naver.maps.LatLng(<?= $_SESSION['_mt_lat'] ?>, <?= $_SESSION['_mt_long'] ?>),
        zoom: 16,
        mapTypeControl: false
    });
    var scheduleMarkers = []; // 스케줄 마커를 저장할 배열입니다.
    var logMarkers = []; // 로그 마커를 저장할 배열입니다.
    var polylinePath = [];
    var resultdrawArr = [];
    var locationMarker;
    var markers;
    var polylines;
    $(document).ready(function() {
        // f_get_box_list2();
        f_calendar_log_init('today'); // 달력 스케쥴
        f_get_log_location('<?= $row_slmt['sgdt_mt_idx'] ?>'); // 위치기록 요약
        setTimeout(() => {
            f_member_location_info(); // 지도
        }, 100);
    });

    function f_profile_click(i, sgdt_idx) {
        console.time("forEachLoopExecutionTime");
        $('#sgdt_mt_idx').val(i);
        $('#sgdt_idx').val(sgdt_idx);
        var nmy = $('#nmy').val();
        var csm = $('#csdate').val().substr(0, 7);
        if (nmy == csm) {
            var tty = 'today';
        } else {
            var tty = '';
        }
        // console.log(i);
        f_calendar_log_init(tty); // 달력 스케쥴
        f_get_log_location(i); // 위치기록 요약
        setTimeout(() => {
            f_member_location_info(); // 지도
        }, 100);
    }

    function f_day_click(sdate) {
        if (typeof(history.pushState) != "undefined") {
            var state = '';
            var title = '';
            var url = './log?sdate=' + sdate;
            history.pushState(state, title, url);

            // f_cld_wrap();

            var sgdt_mt_idx = $('#sgdt_mt_idx').val();
            $('#event_start_date').val(sdate);
            $('#schedule-title').text(get_date_t(sdate));
            $('.c_id').removeClass('active');
            $('#calendar_' + sdate).addClass('active');
            f_get_log_location(sgdt_mt_idx); // 위치기록 요약
            setTimeout(() => {
                f_member_location_info(); // 지도
            }, 100);
        } else {
            location.href = url;
        }
    }

    function f_get_member_location(i) {
        var form_data = new FormData();
        form_data.append("act", "location_member");

        if (i) {
            form_data.append("mt_idx", i);
        }

        $.ajax({
            url: "./location_update",
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
                    $('#location_member_box').html(data);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function f_get_log_location(i, s = "") {
        // $('#splinner_modal').modal('toggle');
        var form_data = new FormData();
        form_data.append("act", "location_log");
        if (s) {
            form_data.append("event_start_date", s);
        } else {
            form_data.append("event_start_date", $('#event_start_date').val());
        }

        if (i) {
            form_data.append("mt_idx", i);
            $('#sgdt_mt_idx').val(i);
        }

        $.ajax({
            url: "./location_update",
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
                    $('#location_log_box').html(data);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

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

    function f_member_location_info() {
        console.time("forEachLoopExecutionTime");
        var form_data = new FormData();
        form_data.append("act", "get_line");
        form_data.append("sgdt_mt_idx", $('#sgdt_mt_idx').val());
        form_data.append("sgdt_idx", $('#sgdt_idx').val());
        form_data.append("event_start_date", $('#event_start_date').val());

        $.ajax({
            url: "./location_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 10000,
            dataType: 'json',
            success: function(data) {
                // console.log(data);
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
                    const timeSlider = document.querySelector('#timeSlider');
                    timeSlider.max = data.log_count; // newDataLogCount는 변경할 max 값입니다.
                    console.log(data.log_count);
                    console.timeEnd("forEachLoopExecutionTime");
                    initializeMap(my_profile, st_lat, st_lng, data);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
                jalert('타임아웃');
                $('#splinner_modal').modal('hide');
                console.timeEnd("forEachLoopExecutionTime");
            },
        });
    }

    function initializeMap(my_profile, st_lat, st_lng, markerData) {
        console.time("forEachLoopExecutionTime");
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
        markers = [];
        polylines = [];
        // 본인 프로필 마커 추가
        var profileMarkerOptions = {
            position: new naver.maps.LatLng(st_lat, st_lng),
            map: map,
            icon: {
                content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                size: new naver.maps.Size(44, 44),
                origin: new naver.maps.Point(0, 0),
                anchor: new naver.maps.Point(22, 22)
            },
            zIndex: 2
        };
        var profileMarker = new naver.maps.Marker(profileMarkerOptions);
        markers.push(profileMarker);


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
                    },
                    zIndex: 1
                };

                var marker = new naver.maps.Marker(markerOptions);
                positions.push(marker.getPosition());

                scheduleMarkers.push(marker);
                markers.push(marker);
            }
        }
        // 로그 마커 추가
        if (markerData.log_chk === 'Y') {
            var logpositions = [];
            var polylinePath = [];
            for (var i = 1; i <= markerData.log_count; i++) {
                if (i == 1) {
                    // 로그 위치값 마커 추가
                    var locationMarkerOptions = {
                        position: new naver.maps.LatLng(markerData['logmarkerLat_' + i], markerData['logmarkerLong_' + i]),
                        map: map,
                        icon: {
                            content: '<div class="point_wrap point5"><div class="stay_marker"><div class="stay_marker_inner"></div></div></div>',
                            size: new naver.maps.Size(20, 20),
                            origin: new naver.maps.Point(0, 0),
                            anchor: new naver.maps.Point(10, 10)
                        },
                        zIndex: 3
                    };
                    locationMarker = new naver.maps.Marker(locationMarkerOptions);
                }
                var markerOptions = {
                    position: new naver.maps.LatLng(markerData['logmarkerLat_' + i], markerData['logmarkerLong_' + i]),
                    map: map,
                    icon: {
                        content: markerData['logmarkerContent_' + i],
                        size: new naver.maps.Size(61, 61),
                        origin: new naver.maps.Point(0, 0),
                        anchor: new naver.maps.Point(20, 20)
                    },
                    zIndex: 2
                };

                var marker = new naver.maps.Marker(markerOptions);
                logpositions.push(marker.getPosition());
                logMarkers.push(marker);
                polylinePath.push(new naver.maps.LatLng(markerData['logmarkerLat_' + i], markerData['logmarkerLong_' + i]));
                markers.push(marker);
            }
        }
        // range input 요소의 값이 변경될 때마다 호출되는 함수
        document.getElementById('timeSlider').addEventListener('input', function() {
            // range input 요소의 값 가져오기
            var sliderValue = parseFloat(this.value);

            // 마커의 새로운 위도 및 경도 계산 (예시)
            var newLat = markerData['logmarkerLat_' + (sliderValue)];
            var newLng = markerData['logmarkerLong_' + (sliderValue)];

            // 마커의 새로운 위치로 이동
            map.setCenter(new naver.maps.LatLng(newLat, newLng));
            locationMarker.setPosition(new naver.maps.LatLng(newLat, newLng));

            var optBottom = document.querySelector('.opt_bottom');
            if (optBottom) {
                var transformY = optBottom.style.transform;
                if (transformY == 'translateY(0px)') {
                    map.panBy(new naver.maps.Point(0, 180)); // 위로 180 픽셀 이동
                }
            }
        });

        // 로그 경로 라인 추가
        var polyline = new naver.maps.Polyline({
            path: polylinePath, //선 위치 변수배열
            strokeColor: '#140082',
            strokeOpacity: 0.8, //선 투명도 0 ~ 1
            strokeWeight: 4, //선 두께
            map: map //오버레이할 지도,
        });
        resultdrawArr.push(polyline);
        polylines.push(polyline);

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
            polylines.forEach(function(polyline) {
                // 폴리라인의 경계를 가져옵니다.
                var polylineBounds = polyline.getBounds();
                if (polylineBounds && bounds.intersects(polylineBounds)) {
                    polyline.setMap(map);
                } else {
                    polyline.setMap(null);
                }
            });
        });

        // 지도 마커클릭시 상세내역 보여짐
        $('.point_wrap').click(function() {
            $('.point_wrap').click(function() {
                $(this).find('.infobox').addClass('on');
                $(this).find('.point_stay').addClass('on');
                $('.point_wrap').not(this).find('.infobox').removeClass('on');
                $('.point_wrap').not(this).find('.point_stay').removeClass('on');
            });
        });

        // initializeMap 함수 끝에 map 변수의 상태를 체크하고 map이 정상적으로 생성되었을 때에만 setCursor 호출
        if (map) {
            map.setCursor('pointer');
        }
        $('#splinner_modal').modal('hide');
        console.timeEnd("forEachLoopExecutionTime");
    }

    function map_panto(lat, lng) {
        map.setCenter(new naver.maps.LatLng(lat, lng));
    }

    function makeMarker(map, position1, position2, index) {
        var ICON_GAP = 0;
        var ICON_SPRITE_IMAGE_URL = 'https://smap.dmonster.kr/design/img/map_direction.svg';
        var iconSpritePositionX = (index * ICON_GAP) + 1;
        var iconSpritePositionY = 1;

        var marker = new naver.maps.Marker({
            map: map,
            position: position1,
            title: 'map_maker' + index,
            icon: {
                url: ICON_SPRITE_IMAGE_URL,
                size: new naver.maps.Size(12, 12), // 이미지 크기
                // origin: new naver.maps.Point(iconSpritePositionX, iconSpritePositionY), // 스프라이트 이미지에서 클리핑 위치
                anchor: new naver.maps.Point(6, 6), // 지도상 위치에서 이미지 위치의 offset
                scaledSize: new naver.maps.Size(12, 12)
            }
        });

        var angle_t = f_get_angle(position2['x'], position2['y'], position1['x'], position1['y']);
        // console.log(position1['x'], position1['y'], position2['x'], position2['y'], angle_t);

        $("div[title|='map_maker" + index + "'").css('transform', 'rotate(' + angle_t + 'deg)');

        return marker;
    }
    // 문서 전체를 클릭했을 때 마커 상세내역 사라짐
    $(document).click(function(event) {
        if (!$(event.target).closest('.point_wrap, .infobox').length) {
            $('.point_wrap .infobox').removeClass('on');
            $('.point_wrap .point_stay').removeClass('on');
        }
    });
    //손으로 바텀시트 움직이기
    document.addEventListener('DOMContentLoaded', function() {
        var startY = 0;

        var optBottom = document.querySelector('.opt_bottom');
        if (optBottom) {
            optBottom.addEventListener('touchstart', function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
            });

            optBottom.addEventListener('touchmove', function(event) {
                var currentY = event.touches[0].clientY; // 현재 터치 좌표
                var deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    var isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    var newTransformValue = isVisible ? 'translateY(0)' : 'translateY(<?= $translateY ?>%)';
                    optBottom.style.transform = newTransformValue;
                }
            });
        } else {
            console.error("요소를 찾을 수 없습니다.");
        }
    });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>