<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '5';
$h_menu = '5';

$_SUB_HEAD_TITLE = $translations['txt_log']; 
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        // alert($translations['txt_login_attempt_other_device'], './logout');
    }
}

if ($_GET['sdate'] == '') {
    $_GET['sdate'] = date('Y-m-d');
}
$sdate = date('Y-m-d');
$tt = strtotime($sdate);

$numDay = date('d', $tt);
$numMonth = date('m', $tt);
$numMonth2 = date('n', $tt);
// 숫자가 1자리일 경우 앞에 0을 붙여주는 로직 추가
$numMonth2 = str_pad($numMonth2, 2, '0', STR_PAD_LEFT);
$numYear = date('Y', $tt);
$prevMonth = date('Y-m-01', strtotime($sdate . " -" . $dayOfWeek . "days"));
$nextMonth = date('Y-m-01', strtotime($sdate . " +" . $dayOfWeek . "days"));
$calendar_date_title = $numYear . "." . $numMonth2;
$now_month_year = $numYear . "-" . $numMonth;

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

//오너제외한 그룹원 수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$row_sgt = $DB->getone('smap_group_t', 'sgt_idx');

$DB->where('sgt_idx', $row_sgt['sgt_idx']);
$DB->where('sgdt_owner_chk', 'N');
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_discharge', 'N');
$DB->where('sgdt_exit', 'N');
$row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
$expt_cnt = $row['cnt'];
?>
<style>
    .sch_cld_wrap {
        padding: 1rem 0;
    }

    #wrap {
        height: 100vh;
        min-height: 100vh;
    }

    .log_wrap {
        position: fixed;
        top: 0;
        left: 50%;
        width: 100%;
        height: 100%;
        max-width: 50rem;
        transform: translateX(-50%);
        height: 100% !important;
        min-height: 100% !important;
    }

    /* 로딩 화면 스타일 */
    #map-loading {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .dots-spinner {
        display: flex;
        gap: 10px;
    }

    .dot {
        width: 8px;
        height: 8px;
        background-color: #0046FE;
        border-radius: 50%;
        animation: dot-bounce 1s infinite ease-in-out;
    }

    .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes dot-bounce {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.5);
        }
    }


    .mt-2.mb-3.px_16 .dots-spinner {
        display: flex;
        gap: 10px;
    }

    .mt-2.mb-3.px_16 .dot {
        width: 8px;
        height: 8px;
        background-color: #0046FE;
        /* 기본색 */
        border-radius: 50%;
        animation: dot-bounce 1s infinite ease-in-out;
    }

    .mt-2.mb-3.px_16 .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .mt-2.mb-3.px_16 .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    */
</style>
<div id="loading">
    <!-- 로딩 화면 추가 -->
    <div id="map-loading" style="display: none;">
        <div class="dots-spinner">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
</div>
<div class="container sub_pg log_wrap px-0">
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
                    <a href="javascript:;" onclick="f_calendar_log_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="<?=$translations['txt_month_selection_icon'] ?>" style="width:1.6rem; "></a>
                    <p class="fs_15 fw_600" id="calendar_date_title"><?= $calendar_date_title ?></p>
                </div>
                <button type="button" class="btn h-auto swiper-button-next"><i class="xi-angle-right-min"></i></button>
            </div>
        </div>
        <div id="schedule_calandar_box" class="cld_date_wrap"></div>
    </section>
    <!-- 지도 -->
    <section class="log_map_wrap" id="map">
    </section>
    <!-- 로그 -->
    <?
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        // $translateY = 70;
        $translateY = 69;
    } else {
        // $translateY = 54;
        $translateY = 46;
    }
    ?>
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
        <section class="opt_bottom" style="transform: translateY(<?= $translateY ?>%);">
            <div class="top_bar_wrap text-center pt_08">
                <img src="./img/top_bar.png" class="top_bar" width="34px" alt="<?=$translations['txt_top_bar'] ?>" />
                <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="<?=$translations['txt_top_up'] ?>" />
            </div>
            <div>
                <!-- 위치조정슬라이더 자리 -->
                <div class="px_16 mb-3">
                    <div class="border bg-white rounded-lg px_16 py_12">
                        <div class="loc_rog_adj">
                            <p class="fs_16 fw_600"><?=$translations['txt_follow_route'] ?></p>
                            <div class="pt-4">
                                <input type="range" class="custom-range" id="timeSlider" min='1' max='1' value='1'>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 그룹원 자리 -->
                <div class="grp_wrap"></div>
                <!-- 위치기록 보여주는 자리 -->
                <div class="mt-2 mb-3 px_16">
                    <!-- <div class="dots-spinner">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div> -->
                </div>
            <? } else { ?>
                <section class="opt_bottom" style="transform: translateY(<?= $translateY ?>%);">
                    <div class="top_bar_wrap text-center pt_08">
                        <img src="./img/top_bar.png" class="top_bar" width="34px" alt="<?=$translations['txt_top_bar'] ?>" />
                        <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="<?=$translations['txt_top_up'] ?>" />
                    </div>
                    <div>
                        <!-- 위치조정슬라이더 자리 -->
                        <div class="px_16 mb-3">
                            <div class="border bg-white rounded-lg px_16 py_16">
                                <div class="loc_rog_adj pb-4">
                                    <p class="fs_16 fw_600"><?=$translations['txt_follow_route'] ?></p>
                                    <div class="pt-4">
                                        <input type="range" class="custom-range" id="timeSlider" min='1' max='1' value='1'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 위치기록 보여주는 자리 -->
                        <div class="mt-2 mb-3 px_16">
                            <!-- <div class="dots-spinner">
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div> -->
                        </div>
                    </div>
                <? } ?>
            </div>
        </section>
</div>
<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i><?=$translations['txt_location_notif'] ?></p>
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>
<!-- H-1 그룹없음 / 무료플랜 플러팅 -->
<? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge"><?=$translations['txt_create_group'] ?></span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700"><?=$translations['txt_create_group_first_question'] ?></p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?=$translations['txt_follow_route_check_location'] ?>
                </p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'"><?=$translations['txt_next'] ?></button>
            </div>
        </div>
    </div>
<? } ?>
<!-- <? if ($sgt_cnt == 1 && $expt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹원 초대하기</span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700">이동 경로로 알아보는 
                    <span class="text-primary">그룹원</span>의 하루!
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">SMAP-로그에서 제공하는 이동경로 조회기능을 통해 
                    그룹원의 하루를 재구성해 볼 수 있어요.</p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'">초대하러 가기</button>
            </div>
        </div>
    </div>
<? } ?> -->

<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const adMessages = [
            "로그를 불러오는 중...\n🎬 광고 시청으로 SMAP을 응원해 주세요!",
            "로그 데이터 확인을 위해 광고 시청이 필요합니다.\n🙏 여러분의 협조에 감사드립니다."
        ];

        // 무작위로 광고 메시지 선택
        const randomAdMessage = adMessages[Math.floor(Math.random() * adMessages.length)];

        // 선택된 메시지를 모달에 삽입
        document.getElementById('adMessage').innerText = randomAdMessage;
    });
</script> -->
<!-- D-6 광고표시 후 로그 표출 : 3의배수 카운트 중 나오는 모달창  -->
<!-- <div class="modal fade" id="showAd_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" name="pedestrian_path_modal_sgdt_idx" id="pedestrian_path_modal_sgdt_idx" value="" />
            <input type="hidden" name="path_day_count" id="path_day_count" value="" />
            <div class="modal-body text-center pb-4">
                <img src="./img/loud_speaker.png" width="48px" class="pt-3" alt="최적의경로" />
                <p id="adMessage" class="fs_16 text_dynamic fw_700 line_h1_3 mt-4"></p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close" onclick="history.back()">취소하기</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="showAd(<?= $ad_data ?>)">광고보기</button>
                </div>
            </div>
        </div>
    </div>
</div> -->
<script>
    let optimalPath; // 최적 경로를 표시할 변수입니다.
    let drawInfoArr = [];
    let scheduleMarkerCoordinates = [];
    let scheduleStatus = [];
    let startX, startY, endX, endY; // 출발지와 도착지 좌표 변수 초기화
    let pathCount;
    // 버튼 엘리먼트 찾기
    let showPathButton = document.getElementById('showPathButton');
    let showPathAdButton = document.getElementById('showPathAdButton'); //광고실행버튼
    let map;
    let centerLat, centerLng;
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
    let polylinePath = [];
    let resultdrawArr = [];
    let locationMarker;
    let currentSelectedDate = '<?= $_GET['sdate'] ?>' || new Date().toISOString().split('T')[0];
    let sgdtMtIdx = $('#sgdt_mt_idx').val(); // 초기 sgdt_mt_idx 값 저장
    let sgdtIdx = $('#sgdt_idx').val(); // 초기 sgdt_idx 값 저장
    let mapInitialized = false; // 지도 초기화 여부를 나타내는 변수 추가
    let markers = [];
    let polylines = [];
    let profileMarkers = [];
    let scheduleMarkers = [];
    let logMarkers = [];
    let optBottom = document.querySelector(".opt_bottom");
    let isPannedDown = false;
    let originalCenter = null; // 초기 중심 좌표 저장
    let currentLat;
    let currentLng;
    const timeSlider = document.getElementById('timeSlider');
    const loadingElement = document.getElementById('map-loading');
    // optBottom 이벤트 리스너 저장
    let optBottomTouchStartListener, optBottomTouchMoveListener, optBottomMouseDownListener, optBottomMouseMoveListener, optBottomMouseUpListener;
    let previousTransformY = optBottom.style.transform; 
</script>
<?php
if ($userLang === 'ko' && $mem_row['mt_map'] == 'N') {
    // 네이버 지도 스크립트
?>
    <script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>"></script>
    <script>
        map = new naver.maps.Map("map", {
            center: new naver.maps.LatLng(<?= $_SESSION['_mt_lat'] ?>, <?= $_SESSION['_mt_long'] ?>),
            zoom: 16,
            mapTypeControl: false
        });

        function initNaverMap(markerData, sgdt_idx) {
            map.setCenter(new naver.maps.LatLng(markerData.my_lat, markerData.mt_long));
            clearAllMapElements();

            if (markerData && markerData.log_markers && markerData.log_markers.length > 0) {
                if (markerData.log_chk === "Y") {
                    let polylinePath = [];
                    let gradient = createGradient(markerData.log_markers.length);
                    let stayCount = 1;

                    markerData.log_markers.forEach((marker, index) => {
                        const logmarkerLat = parseFloat(marker.latitude);
                        const logmarkerLng = parseFloat(marker.longitude);

                        polylinePath.push(new naver.maps.LatLng(logmarkerLat, logmarkerLng));

                        const markerContent = document.createElement('div');
                        markerContent.className = 'point_wrap point5';
                        markerContent.setAttribute('data-rangeindex', index + 1);

                        const stayMarker = document.createElement('div');
                        stayMarker.className = 'stay_marker d-none';
                        const stayMarkerInner = document.createElement('div');
                        stayMarkerInner.className = 'stay_marker_inner';
                        stayMarker.appendChild(stayMarkerInner);
                        markerContent.appendChild(stayMarker);

                        const infoBox = document.createElement('div');
                        if (marker.type === 'stay') {
                            infoBox.className = 'infobox rounded-sm bg-white px_08 py_08 d-none';
                            infoBox.innerHTML = `
                                <p class="fs_12 fw_900 text_dynamic">${marker.time}</p>
                                <p class="fs_10 fw_600 text_dynamic text-primary line_h1_2 mt-2">${marker.stayTime}</p>
                                <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">${marker.address}</p>
                            `;
                            stayCount++;
                        } else {
                            infoBox.className = 'infobox infobox_2 rounded-sm px_08 py_08 d-none';
                            infoBox.style.backgroundColor = '#413F4A';
                            infoBox.style.color = '#E6F3FF';
                            infoBox.innerHTML = `<p class="fs_12 fw_800 text_dynamic">${marker.time}</p>`;
                        }

                        // infoBox 스타일 변경
                        infoBox.style.position = 'absolute'; // infoBox를 마커 내에서 절대 위치로 설정
                        infoBox.style.zIndex = '3'; // 다른 마커 요소보다 높은 z-index 값 설정
                        infoBox.classList.add('d-none');
                        markerContent.appendChild(infoBox);

                        if (marker.type === 'stay') {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'btn log_point point_stay';
                            button.style.position = 'relative'; // button을 기준으로 자식 요소의 위치를 지정

                            const pointInner = document.createElement('span');
                            pointInner.className = 'point_inner';

                            const pointTxt = document.createElement('span');
                            pointTxt.className = 'point_txt';
                            pointTxt.textContent = stayCount - 1;

                            pointInner.appendChild(pointTxt);
                            button.appendChild(pointInner);

                            markerContent.appendChild(button);
                        }

                        const newMarker = new naver.maps.Marker({
                            map: map,
                            position: new naver.maps.LatLng(logmarkerLat, logmarkerLng),
                            icon: {
                                content: markerContent,
                                size: new naver.maps.Size(48, 48), // 아이콘 크기 조정
                                anchor: new naver.maps.Point(10, 10) // 앵커 포인트 조정
                            },
                            zIndex: 1,
                        });

                        logMarkers.push(newMarker);
                    });

                    // 폴리라인 경로 생성
                    for (let i = 0; i < polylinePath.length - 1; i++) {
                        const polyline = new naver.maps.Polyline({
                            path: [polylinePath[i], polylinePath[i + 1]],
                            strokeColor: gradient[i],
                            strokeOpacity: 0.5,
                            strokeWeight: 5,
                            map: map
                        });
                        polylines.push(polyline);
                    }

                    function updateMarkerVisibility(sliderValue) {
                        const marker = markerData.log_markers[sliderValue - 1];
                        if (!marker) return;

                        map.setCenter(new naver.maps.LatLng(parseFloat(marker.latitude), parseFloat(marker.longitude)));

                        currentLat = parseFloat(marker.latitude);
                        currentLng = parseFloat(marker.longitude);

                        logMarkers.forEach((mapMarker, index) => {
                            const content = mapMarker.icon.content;
                            const infoBox = content.querySelector('.infobox');
                            const stayMarker = content.querySelector('.stay_marker');
                            const button = content.querySelector('.btn.log_point');

                            // 현재 선택된 마커의 stay_marker와 infoBox만 표시
                            if (index === sliderValue - 1) {
                                stayMarker?.classList.remove('d-none');
                                if (infoBox) {
                                    infoBox.classList.remove('d-none');
                                    infoBox.style.display = 'block';
                                }
                            } else {
                                stayMarker?.classList.add('d-none');
                                if (infoBox) {
                                    infoBox.classList.add('d-none');
                                    infoBox.style.display = 'none';
                                }
                            }

                            // stay 버튼은 항상 표시
                            if (button) {
                                button.classList.remove('d-none');
                            }
                        });
                    }

                    let prevOptBottomState = null; // opt_bottom의 이전 상태를 저장할 변수

                    if (timeSlider) {
                        timeSlider.max = markerData.log_markers.length;
                        timeSlider.value = 1;
                        updateMarkerVisibility(1);

                        timeSlider.addEventListener('input', function() {
                            // map.setOptions({
                            //     animation: null
                            // }); // 애니메이션 비활성화
                            const sliderValue = parseInt(this.value);
                            updateMarkerVisibility(sliderValue);

                            // opt_bottom이 올라가 있을때
                            if (optBottom.style.transform === 'translateY(0px)') {
                                map.panBy(new naver.maps.Point(0, 180));
                            }
                        });
                    }
                }
            }

            // 스케줄 마커 추가
            if (markerData.schedule_chk === 'Y') {
                for (let i = 1; i <= markerData.count; i++) {
                    const markerLat = parseFloat(markerData['markerLat_' + i]);
                    const markerLng = parseFloat(markerData['markerLong_' + i]);
                    const markerTitle = markerData['markerTitle_' + i];

                    // 랜덤 색상 생성
                    const randomColor = generateRandomColor();

                    // DOM 노드 생성
                    const pointWrapDiv = document.createElement('div');
                    pointWrapDiv.className = 'point_wrap point1';

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn point point_sch';

                    const spanInner = document.createElement('span');
                    spanInner.className = 'point_inner';

                    const image = document.createElement('img');
                    image.src = './img/sch_alarm.png';
                    image.alt = 'Desired Image';
                    image.className = 'btn point point_ing';
                    image.style.width = '24px';
                    image.style.height = '24px';

                    const infoboxDiv = document.createElement('div');
                    infoboxDiv.className = 'infobox1 rounded_04 px_08 py_03 on';

                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                    titleSpan.textContent = markerTitle;

                    // 스타일 DOM 노드 생성
                    const style = document.createElement('style');
                    style.textContent = `
                        .infobox1 {
                            position: absolute !important;
                            left: 50%; 
                            top: 100%; 
                            transform: translate(-50%, -80%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 1;
                            white-space: nowrap; 
                        }

                        .infobox1 span {
                            color: ${randomColor};
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                    // DOM 노드 연결
                    spanInner.appendChild(image);
                    button.appendChild(spanInner);
                    infoboxDiv.appendChild(titleSpan);
                    pointWrapDiv.appendChild(style); // 스타일 노드 추가
                    pointWrapDiv.appendChild(button);
                    pointWrapDiv.appendChild(infoboxDiv);

                    // 네이버 지도 마커 생성
                    const scheduleMarker = new naver.maps.Marker({
                        map: map,
                        position: new naver.maps.LatLng(markerLat, markerLng),
                        icon: {
                            content: pointWrapDiv,
                            size: new naver.maps.Size(48, 48), // 아이콘 크기 조정
                            anchor: new naver.maps.Point(24, 24) // 앵커 포인트 조정
                        },
                        zIndex: 1
                    });

                    scheduleMarkers.push(scheduleMarker);
                }
            }

            // 내 장소 마커 추가
            if (markerData.location_chk === 'Y') {
                for (let i = 1; i <= markerData.location_count; i++) {
                    const locationLat = parseFloat(markerData['locationmarkerLat_' + i]);
                    const locationLng = parseFloat(markerData['locationmarkerLong_' + i]);
                    const locationTitle = markerData['locationmarkerTitle_' + i];

                    // 랜덤 색상 생성
                    const randomColor = generateRandomColor();

                    // DOM 노드 생성
                    const pointWrapDiv = document.createElement('div');
                    pointWrapDiv.className = 'point_wrap point1';

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn point point_myplc';

                    const spanInner = document.createElement('span');
                    spanInner.className = 'point_inner';

                    const image = document.createElement('img');
                    image.src = './img/loc_alarm.png';
                    image.alt = 'Desired Image';
                    image.className = 'btn point point_ing';
                    image.style.width = '24px';
                    image.style.height = '24px';

                    const infoboxDiv = document.createElement('div');
                    infoboxDiv.className = 'infobox2 rounded_04 px_08 py_03 on';

                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                    titleSpan.textContent = locationTitle;

                    // 스타일 DOM 노드 생성
                    const style = document.createElement('style');
                    style.textContent = `
                        .infobox2 {
                            position: absolute;
                            left: 50%; 
                            top: 100%; 
                            transform: translate(-50%, 40%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 1;
                            white-space: nowrap; 
                        }

                        .infobox2 span {
                            color: ${randomColor}; 
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                    // DOM 노드 연결
                    spanInner.appendChild(image);
                    button.appendChild(spanInner);
                    infoboxDiv.appendChild(titleSpan);
                    pointWrapDiv.appendChild(style); // 스타일 노드 추가
                    pointWrapDiv.appendChild(button);
                    pointWrapDiv.appendChild(infoboxDiv);

                    // 네이������ 지도 마커 생성
                    const locationMarker = new naver.maps.Marker({
                        map: map,
                        position: new naver.maps.LatLng(locationLat, locationLng),
                        icon: {
                            content: pointWrapDiv,
                            size: new naver.maps.Size(48, 48), // 아이콘 크기 조정
                            anchor: new naver.maps.Point(24, 24) // 앵커 포인트 조정
                        },
                        zIndex: 1
                    });

                    // 마커 배열에 추가 (필요하다면)
                    markers.push(locationMarker);
                }
            }

            // 랜덤 색상 생성
            const randomColor = generateRandomColor();

            // DOM 노드 생성
            const pointWrapDiv = document.createElement('div');
            pointWrapDiv.className = 'point_wrap point1';

            const mapUserDiv = document.createElement('div');
            mapUserDiv.className = 'map_user';
            pointWrapDiv.appendChild(mapUserDiv);

            const mapRtImgDiv = document.createElement('div');
            mapRtImgDiv.className = 'map_rt_img rounded_14';
            mapUserDiv.appendChild(mapRtImgDiv);

            const rectSquareDiv = document.createElement('div');
            rectSquareDiv.className = 'rect_square';
            mapRtImgDiv.appendChild(rectSquareDiv);

            const image = document.createElement('img');
            image.src = markerData.my_profile;
            image.alt = '프로필 이미지';
            image.onerror = function() {
                this.src = '<?= $ct_no_img_url ?>';
            };
            rectSquareDiv.appendChild(image);

            // 스타일 DOM 노드 생성 (필요시 추가 스타일 적용)
            // const style = document.createElement('style');
            // style.textContent = `
            // .point_wrap { /* 추가적인 스타일 */ }
            // .map_user { /* 추가적인 스타일 */ }
            // /* ... */
            // `;
            // pointWrapDiv.appendChild(style);

            // 네이버 지도 마커 생성
            const profileMarker = new naver.maps.Marker({
                map: map,
                position: new naver.maps.LatLng(markerData.my_lat, markerData.mt_long),
                icon: {
                    content: pointWrapDiv,
                    size: new naver.maps.Size(44, 44),
                    anchor: new naver.maps.Point(22, 22)
                },
                zIndex: 2
            });

            // 마커 배열에 추가
            profileMarkers.push(profileMarker);
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
    </script>
<?php
} else {
    // 구글 지도 스크립트
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
            if (mapInitialized) { // 이미 초기화되었다면 함수 종료
                return;
            }

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
                zoom: 16,
                mapTypeControl: false,
                mapId: "e40062e414aad354",
                fullscreenControl: false,
                disableDoubleClickZoom: true,
                clickableIcons: false, // 장소 아이콘 클릭 비활성화
                language: '<?= $userLang ?>',
                animatedZoom: false // 애니메이션 줌 비활성화
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // 추가 옵션 설정
            map.setOptions({
                disableDefaultUI: true, // 기본 UI 비활성화
                gestureHandling: 'greedy' // 스크롤 동작 변경
            });

            mapInitialized = true; // 지도 초기화 완료 표시
            console.log("Map initialized successfully");

            // 지도가 완전히 로딩된 후 이벤트 리스너 등록 (한 번만 등록되도록 수정)
            google.maps.event.addListenerOnce(map, 'idle', () => {
                if (state.isDataLoaded) {
                    drawPathOnMap();
                }
            });

            return map;
        }

        async function initGoogleMap(markerData, sgdt_idx) {
            try {
                await loadGoogleMapsScript();

                if (!map) {
                    await initMap(markerData.my_lat, markerData.mt_long);
                } else {
                    map.setCenter({
                        lat: parseFloat(markerData.my_lat),
                        lng: parseFloat(markerData.mt_long)
                    });
                }
                console.log("Google Map initialized with custom data");

                clearAllMapElements();

                if (markerData && markerData.log_markers && markerData.log_markers.length > 0) {
                    if (markerData.log_chk === "Y") {
                        let polylinePath = [];
                        let gradient = createGradient(markerData.log_markers.length);
                        let stayCount = 1;

                        markerData.log_markers.forEach((marker, index) => {
                            const logmarkerLat = parseFloat(marker.latitude);
                            const logmarkerLng = parseFloat(marker.longitude);
                            const {
                                AdvancedMarkerElement
                            } = google.maps.marker;

                            polylinePath.push(new google.maps.LatLng(logmarkerLat, logmarkerLng));

                            const markerContent = document.createElement('div');
                            markerContent.className = 'point_wrap point5';
                            markerContent.setAttribute('data-rangeindex', index + 1);

                            const stayMarker = document.createElement('div');
                            stayMarker.className = 'stay_marker d-none';
                            const stayMarkerInner = document.createElement('div');
                            stayMarkerInner.className = 'stay_marker_inner';
                            stayMarker.appendChild(stayMarkerInner);
                            markerContent.appendChild(stayMarker);

                            const infoBox = document.createElement('div');
                            if (marker.type === 'stay') {
                                infoBox.className = 'infobox rounded-sm bg-white px_08 py_08 d-none';
                                infoBox.innerHTML = `
                                <p class="fs_12 fw_900 text_dynamic">${marker.time}</p>
                                <p class="fs_10 fw_600 text_dynamic text-primary line_h1_2 mt-2">${marker.stayTime}</p>
                                <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">${marker.address}</p>
                            `;
                                stayCount++;
                            } else {
                                infoBox.className = 'infobox infobox_2 rounded-sm px_08 py_08 d-none';
                                infoBox.style.backgroundColor = '#413F4A';
                                infoBox.style.color = '#E6F3FF';
                                infoBox.innerHTML = `<p class="fs_12 fw_800 text_dynamic">${marker.time}</p>`;
                            }

                            // infoBox 스타일 변경
                            infoBox.style.position = 'absolute'; // infoBox를 마커 내에서 절대 위치로 설정
                            infoBox.style.zIndex = '3'; // 다른 마커 요소보다 높은 z-index 값 설정
                            infoBox.classList.add('d-none');
                            markerContent.appendChild(infoBox);

                            if (marker.type === 'stay') {
                                const button = document.createElement('button');
                                button.type = 'button';
                                button.className = 'btn log_point point_stay';
                                button.style.position = 'relative'; // button을 기준으로 자식 요소의 위치를 지정

                                const pointInner = document.createElement('span');
                                pointInner.className = 'point_inner';

                                const pointTxt = document.createElement('span');
                                pointTxt.className = 'point_txt';
                                pointTxt.textContent = stayCount - 1;

                                pointInner.appendChild(pointTxt);
                                button.appendChild(pointInner);

                                markerContent.appendChild(button);
                            }

                            const newMarker = new AdvancedMarkerElement({
                                map: map,
                                position: {
                                    lat: logmarkerLat,
                                    lng: logmarkerLng
                                },
                                content: markerContent,
                                zIndex: 99, // 다른 마커 요소보다 낮은 z-index 값 설정 (필요시 조절)
                            });

                            logMarkers.push(newMarker);
                        });

                        // 경로 생성 (polyline) - 항상 표시
                        for (let i = 0; i < polylinePath.length - 1; i++) {
                            const polyline = new google.maps.Polyline({
                                path: [polylinePath[i], polylinePath[i + 1]],
                                strokeColor: gradient[i],
                                strokeOpacity: 0.5,
                                strokeWeight: 5,
                                map: map
                            });
                            polylines.push(polyline);
                        }

                        function updateMarkerVisibility(sliderValue) {
                            const marker = markerData.log_markers[sliderValue - 1];
                            if (!marker) return;

                            map.setOptions({
                                center: {
                                    lat: parseFloat(marker.latitude),
                                    lng: parseFloat(marker.longitude)
                                }
                            });

                            currentLat = marker.latitude;
                            currentLng = marker.longitude;


                            logMarkers.forEach((mapMarker, index) => {
                                const content = mapMarker.content;
                                const infoBox = content.querySelector('.infobox');
                                const stayMarker = content.querySelector('.stay_marker');
                                const button = content.querySelector('.btn.log_point');

                                // 현재 선택된 마커의 stay_marker와 infoBox만 표시
                                if (index === sliderValue - 1) {
                                    stayMarker?.classList.remove('d-none');
                                    if (infoBox) {
                                        infoBox.classList.remove('d-none');
                                        infoBox.style.display = 'block';
                                    }
                                } else {
                                    stayMarker?.classList.add('d-none');
                                    if (infoBox) {
                                        infoBox.classList.add('d-none');
                                        infoBox.style.display = 'none';
                                    }
                                }

                                // stay 버튼은 항상 표시
                                if (button) {
                                    button.classList.remove('d-none');
                                }
                            });
                        }

                        if (timeSlider) {
                            timeSlider.max = markerData.log_markers.length;
                            timeSlider.value = 1;
                            updateMarkerVisibility(1);

                            timeSlider.addEventListener('input', function() {
                                const sliderValue = parseInt(this.value);
                                updateMarkerVisibility(sliderValue);

                                // opt_bottom이 올라가 있고, 아직 panBy가 실행되지 않았을 때만 실행
                                if (optBottom.style.transform === 'translateY(0px)') {
                                    const currentCenter = map.getCenter();
                                    map.setOptions({
                                        center: {
                                            lat: currentCenter.lat() - (300 / 111000) * 1.5,
                                            lng: currentCenter.lng()
                                        }
                                    });
                                }
                            });
                        }
                    }
                }

                // 스케줄 마커 추가
                if (markerData.schedule_chk === 'Y') {
                    for (let i = 1; i <= markerData.count; i++) {
                        const markerLat = parseFloat(markerData['markerLat_' + i]);
                        const markerLng = parseFloat(markerData['markerLong_' + i]);
                        const markerTitle = markerData['markerTitle_' + i];

                        // 랜덤 색상 생성
                        const randomColor = generateRandomColor();

                        // DOM 노드 생성
                        const pointWrapDiv = document.createElement('div');
                        pointWrapDiv.className = 'point_wrap point1';

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn point point_sch';

                        const spanInner = document.createElement('span');
                        spanInner.className = 'point_inner';

                        const image = document.createElement('img');
                        image.src = './img/sch_alarm.png';
                        image.alt = 'Desired Image';
                        image.className = 'btn point point_ing';
                        image.style.width = '24px';
                        image.style.height = '24px';

                        const infoboxDiv = document.createElement('div');
                        infoboxDiv.className = 'infobox1 rounded_04 px_08 py_03 on';

                        const titleSpan = document.createElement('span');
                        titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                        titleSpan.textContent = markerTitle;

                        // 스타일 DOM 노드 생성
                        const style = document.createElement('style');
                        style.textContent = `
                        .infobox1 {
                            position: absolute !important;
                            left: 50%;
                            top: 100%; 
                            transform: translate(-50%, -80%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 5;
                            white-space: nowrap; 
                        }

                        .infobox1 span {
                            color: ${randomColor};
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                        // DOM 노드 연결
                        spanInner.appendChild(image);
                        button.appendChild(spanInner);
                        infoboxDiv.appendChild(titleSpan);
                        pointWrapDiv.appendChild(style);
                        pointWrapDiv.appendChild(button);
                        pointWrapDiv.appendChild(infoboxDiv);

                        // Google Maps 마커 생성 (AdvancedMarkerElement 사용)
                        const {
                            AdvancedMarkerElement
                        } = google.maps.marker;
                        const scheduleMarker = new AdvancedMarkerElement({
                            map: map,
                            position: {
                                lat: markerLat,
                                lng: markerLng
                            },
                            content: pointWrapDiv,
                            zIndex: 1
                        });

                        scheduleMarkers.push(scheduleMarker);
                    }
                }

                // 내 장소 마커 추가
                if (markerData.location_chk === 'Y') {
                    for (let i = 1; i <= markerData.location_count; i++) {
                        const locationLat = parseFloat(markerData['locationmarkerLat_' + i]);
                        const locationLng = parseFloat(markerData['locationmarkerLong_' + i]);
                        const locationTitle = markerData['locationmarkerTitle_' + i];
                        // console.log('locationLat: ' + locationLat + ', locationLng: ' + locationLng + ', locationTitle: ' + locationTitle);
                        createLocationMarker(locationTitle, locationLat, locationLng);
                    }
                }

                // 프로필 마커
                addGoogleProfileMarker(markerData.my_lat, markerData.mt_long, markerData.my_profile);
            } catch (error) {
                console.error("Error in initGoogleMap:", error);
            }
        }
        function createLocationMarker(locationTitle, locationLat, locationLng){
                        // 랜덤 색상 생성
                        const randomColor = generateRandomColor();

                        // DOM 노드 생성
                        const pointWrapDiv = document.createElement('div');
                        pointWrapDiv.className = 'point_wrap point1';

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn point point_myplc';

                        const spanInner = document.createElement('span');
                        spanInner.className = 'point_inner';

                        const image = document.createElement('img');
                        image.src = './img/loc_alarm.png';
                        image.alt = 'Desired Image';
                        image.className = 'btn point point_ing';
                        image.style.width = '24px';
                        image.style.height = '24px';

                        const infoboxDiv = document.createElement('div');
                        infoboxDiv.className = 'infobox2 rounded_04 px_08 py_03 on';

                        const titleSpan = document.createElement('span');
                        titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                        titleSpan.textContent = locationTitle;

                        // 스타일 DOM 노드 생성
                        const style = document.createElement('style');
                        style.textContent = `
                        .infobox2 {
                            position: absolute;
                            left: 50%; 
                            top: 100%; 
                            transform: translate(-50%, 40%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 5;
                            white-space: nowrap; 
                        }

                        .infobox2 span {
                            color: ${randomColor}; 
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                        // DOM 노드 연결
                        spanInner.appendChild(image);
                        button.appendChild(spanInner);
                        infoboxDiv.appendChild(titleSpan);
                        pointWrapDiv.appendChild(style); // 스타일 노드 추가
                        pointWrapDiv.appendChild(button);
                        pointWrapDiv.appendChild(infoboxDiv);

                        // Google Maps 마커 생성 (AdvancedMarkerElement 사용)
                        const {
                            AdvancedMarkerElement
                        } = google.maps.marker;
                        const locationMarker = new AdvancedMarkerElement({
                            map: map,
                            position: {
                                lat: locationLat,
                                lng: locationLng
                            },
                            content: pointWrapDiv,
                            zIndex: 2
                        });

                        markers.push(locationMarker);
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
                    this.src = 'https://app2.smap.site/img/no_image.png';
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
        }



        function createLocationMarkerContent(title, lat, lng) {
            // 랜덤 색상 생성
            const randomColor = generateRandomColor();

            // DOM 노드 생성
            const pointWrapDiv = document.createElement('div');
            pointWrapDiv.className = 'point_wrap point1';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn point point_myplc';

            const spanInner = document.createElement('span');
            spanInner.className = 'point_inner';

            const image = document.createElement('img');
            image.src = './img/loc_alarm.png';
            image.alt = 'Desired Image';
            image.className = 'btn point point_ing';
            image.style.width = '24px';
            image.style.height = '24px';

            const infoboxDiv = document.createElement('div');
            infoboxDiv.className = 'infobox2 rounded_04 px_08 py_03 on';

            const titleSpan = document.createElement('span');
            titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
            titleSpan.textContent = title;

            // 스타일 DOM 노드 생성
            const style = document.createElement('style');
            style.textContent = `
                .infobox2 {
                    position: absolute;
                    left: 50%; 
                    top: 100%; 
                    transform: translate(-50%, 40%); 
                    background-color: #413F4A;
                    padding: 0.3rem 0.8rem; 
                    border-radius: 0.4rem;
                    z-index: 1;
                    white-space: nowrap; 
                }
                
                .infobox2 span {
                    color: ${randomColor};
                    font-size: 14px !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
            `;

            // DOM 노드 연결
            spanInner.appendChild(image);
            button.appendChild(spanInner);
            infoboxDiv.appendChild(titleSpan);
            pointWrapDiv.appendChild(button);
            pointWrapDiv.appendChild(infoboxDiv);
            pointWrapDiv.appendChild(style); // 스타일 노드 추가

            // 마커 생성 및 반환
            const {
                AdvancedMarkerElement
            } = google.maps.marker;
            const marker = new AdvancedMarkerElement({
                map: map,
                position: {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                },
                content: pointWrapDiv,
                zIndex: 1,
            });

            return marker;
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
                    this.src = 'https://app2.smap.site/img/no_image.png';
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
        }
    </script>
<?php
}
?>
<script>
    $(document).ready(function() {
        showMapLoading();
        
        async function initializeData() {
            try {
                const data = await createGroupMember(<?= $sgdt_row['sgdt_idx'] ?>);
                if (!data) {
                    // 데이터가 없는 경우 본인 데이터로 초기화
                    const myData = {
                        mt_idx: <?= $_SESSION['_mt_idx'] ?>,
                        sgdt_idx: <?= $sgdt_row['sgdt_idx'] ?? 'null' ?>
                    };
                    
                    $('#sgdt_mt_idx').val(myData.mt_idx);
                    $('#sgdt_idx').val(myData.sgdt_idx);
                    
                    // 본인 데이터로 초기화
                    await Promise.all([
                        f_calendar_log_init('today'),
                        f_get_log_location(myData.mt_idx)
                    ]);
                    hideMapLoading();
                    return;
                }

                // 첫 번째 멤버 찾기 (본인 제외)
                let firstMemberKey = null;
                if (data.members) {
                    Object.keys(data.members).forEach(key => {
                        if (key !== data.sgdt_idx.toString() && !firstMemberKey) {
                            firstMemberKey = key;
                        }
                    });
                }

                // 첫 번째 멤버가 있는 경우
                if (firstMemberKey && data.members[firstMemberKey]) {
                    const member = data.members[firstMemberKey];
                    $('#sgdt_mt_idx').val(member.member_info.mt_idx);
                    $('#sgdt_idx').val(member.member_info.sgdt_idx);
                    
                    // 첫 번째 멤버의 데이터로 초기화
                    await Promise.all([
                        f_calendar_log_init('today'),
                        f_get_log_location(member.member_info.mt_idx)
                    ]);

                    // 지도 데이터 업데이트
                    await updateMemberLocationInfo();
                } else {
                    // 첫 번째 멤버가 없는 경우 본인 데이터로 초기화
                    const myData = {
                        mt_idx: <?= $_SESSION['_mt_idx'] ?>,
                        sgdt_idx: <?= $sgdt_row['sgdt_idx'] ?? 'null' ?>
                    };
                    
                    $('#sgdt_mt_idx').val(myData.mt_idx);
                    $('#sgdt_idx').val(myData.sgdt_idx);
                    
                    await Promise.all([
                        f_calendar_log_init('today'),
                        f_get_log_location(myData.mt_idx)
                    ]);
                }
            } catch (error) {
                console.error("초기화 중 오류 발생:", error);
                jalert('<?=$translations['txt_error_occurred'] ?>');
            } finally {
                hideMapLoading();
            }
        }

        // 초기화 함수 실행
        initializeData().catch(error => {
            console.error("최종 에러:", error);
            hideMapLoading(); // 에러 발생 시에도 로딩 화면을 숨김
        });
    });

    function createGroupMember(sgdt_idx) {
        return new Promise((resolve, reject) => {
            // sgdt_idx가 없는 경우 빈 데이터 반환
            if (!sgdt_idx) {
                resolve(null);
                return;
            }

            // 캐시된 데이터 확인
            let cachedData = sessionStorage.getItem('groupMemberData_' + sgdt_idx);
            if (cachedData) {
                let response = JSON.parse(cachedData);
                if (response.result === 'Y') {
                    renderMemberList(response);
                    return resolve(response);
                }
            }

            // AJAX 요청
            var form_data = new FormData();
            form_data.append("act", "member_schedule_list");
            form_data.append("sgdt_idx", sgdt_idx);
            form_data.append("event_start_date", '<?= $s_date ?>');
            form_data.append("mt_lang", '<?= $userLang ?>');

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
                    if (data.result === 'Y') {
                        sessionStorage.setItem('groupMemberData_' + sgdt_idx, JSON.stringify(data));
                        renderMemberList(data);
                        resolve(data);
                    } else {
                        // 데이터가 없는 경우에도 정상적으로 처리
                        console.log("No loadMemberSchedule data available");
                        resolve(null);
                    }
                },
                error: function(err) {
                    console.error('AJAX request failed: ', err);
                    resolve(null); // 에러 발생 시에도 null 반환하여 계속 진행
                }
            });
        });
    }

    async function initializeMapAndMarkers(data, sgdt_idx) {
        if ('ko' === '<?= $userLang ?>' && '<?= $mem_row['mt_map'] ?>' == 'N') {
            await initNaverMap(data, sgdt_idx);
        } else {
            await initGoogleMap(data, sgdt_idx);
        }
    }

    function renderMemberList(data) {
        const grpWrap = $('.grp_wrap');
        grpWrap.empty(); // 기존 내용 삭제

        // 전체 HTML 구조 생성
        const html = `
        <div       class="border bg-white rounded-lg px_16 py_16">
            <p class="fs_16 fw_600 mb-3"><?=$translations['txt_group_members'] ?></p>
            <div id="group_member_list_box">
                <div class="mem_wrap mem_swiper">
                    <div class="swiper-wrapper d-flex">
                        ${generateMemberItems(data)}
                    </div>
                </div>
            </div>
        </div>
    `;
        grpWrap.html(html);

        // Swiper 다시 초기화
        mem_swiper = new Swiper(".mem_swiper", {
            slidesPerView: 'auto',
            spaceBetween: 12,
        });
    }

    // 멤버 아이템 생성 함수
    function generateMemberItems(data) {
        let html = '';
        let firstMemberKey = null;

        // 그룹 멤버 정보 먼저 추가 (본인 제외)
        if (data.members && typeof data.members === 'object') {
            Object.keys(data.members).forEach(key => {
                const member = data.members[key];
                if (key !== data.sgdt_idx.toString()) {
                    // 첫 번째 멤버의 키 저장
                    if (!firstMemberKey) {
                        firstMemberKey = key;
                    }
                    html += `
                    <div class="swiper-slide checks mem_box">
                        <label>
                            <input type="radio" name="rd2" ${key === firstMemberKey ? 'checked' : ''} onclick="f_profile_click(${member.member_info.mt_idx}, ${member.member_info.sgdt_idx});">
                            <div class="prd_img mx-auto"> 
                                <div class="rect_square rounded_14">
                                    <img src="${member.member_info.my_profile}" alt="<?= $translations['txt_profile_image'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                                </div>
                            </div>
                            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">${member.member_info.mt_nickname}</p>
                        </label>
                    </div>
                    `;
                }
            });
        }

        // 본인 정보 마지막에 추가
        if (data.members && data.members[data.sgdt_idx]) {
            html += `
            <div class="swiper-slide checks mem_box">
                <label>
                    <input type="radio" name="rd2" onclick="f_profile_click(${data.members[data.sgdt_idx].member_info.mt_idx}, ${data.members[data.sgdt_idx].member_info.sgdt_idx});">
                    <div class="prd_img mx-auto">
                        <div class="rect_square rounded_14">
                            <img src="${data.members[data.sgdt_idx].member_info.my_profile}" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                        </div>
                    </div>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">${data.members[data.sgdt_idx].member_info.mt_nickname}</p>
                </label>
            </div>
            `;
        }

        // 그룹원추가 버튼 추가
        html += `
        <div class="swiper-slide mem_box add_mem_box" ${data.sgt_cnt > 0 ? 'onclick="location.href=\'./group\'"' : 'style="visibility: hidden;"'}>
            <button class="btn mem_add">
                <i class="xi-plus-min fs_20"></i>
            </button>
            <p class="fs_12 fw_400 text-center mt-1 line_h1_2 text_dynamic" style="word-break: break-all; line-height: 1.2; white-space: normal; overflow: visible;">
                <?= $translations['txt_add_member'] ?>
            </p>
        </div>
    `;

        // 첫 번째 멤버의 데이터로 초기화
        if (firstMemberKey && data.members[firstMemberKey]) {
            const firstMember = data.members[firstMemberKey];
            $('#sgdt_mt_idx').val(firstMember.member_info.mt_idx);
            $('#sgdt_idx').val(firstMember.member_info.sgdt_idx);
        }

        return html;
    }

    function createGradient(steps) {
        const rainbow = [
            '#FF0000', // 빨간색
            '#FFA500', // 주황색
            '#FFFF00', // 노란색
            '#00FF00', // 초록색
            '#0000FF', // 파란색
            '#000080', // 남색
            '#800080', // 보라색
        ];

        // steps가 1일 경우 첫 번째 색상만 반환
        if (steps === 1) {
            return [rainbow[0]];
        }

        const gradientColors = [];
        for (let i = 0; i < steps; i++) {
            const rainbowIndex = Math.floor((i / steps) * (rainbow.length - 1));
            const ratio = (i / steps) * (rainbow.length - 1) - rainbowIndex;

            const color1 = rainbow[rainbowIndex];
            const color2 = rainbow[Math.min(rainbowIndex + 1, rainbow.length - 1)];

            const r = Math.round(parseInt(color1.slice(1, 3), 16) * (1 - ratio) + parseInt(color2.slice(1, 3), 16) * ratio);
            const g = Math.round(parseInt(color1.slice(3, 5), 16) * (1 - ratio) + parseInt(color2.slice(3, 5), 16) * ratio);
            const b = Math.round(parseInt(color1.slice(5, 7), 16) * (1 - ratio) + parseInt(color2.slice(5, 7), 16) * ratio);

            gradientColors.push(`#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`);
        }

        return gradientColors;
    }

    // 로딩 화면을 보이게 하는 함수
    function showMapLoading(center = true) {
        const spinnerDots = document.querySelectorAll('.dot'); // 모든 .dot 요소 선택
        // const otherSpinnerDots = document.querySelectorAll('.mt-2.mb-3.px_16 .dot'); // .mt-2.mb-3.px_16의 .dot 요소 선택

        // 랜덤 색상 적용
        const randomColor = generateSpinnerColor();

        // 두 스피너의 색상 변경
        spinnerDots.forEach(dot => {
            dot.style.backgroundColor = randomColor;
        });
        // otherSpinnerDots.forEach(dot => {
        //     dot.style.backgroundColor = randomColor;
        // });

        loadingElement.style.display = 'flex'; // 로딩바 표시
        // optBottom 이벤트 비활성화
        optBottom.ontouchstart = null;
        optBottom.ontouchmove = null;
        optBottom.onmousedown = null;
        document.onmousemove = null;
        document.onmouseup = null;
    }

    // 로딩 화면을 숨기는 함수
    function hideMapLoading() {
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
        
        // 이벤트 리스너 재설정
        if (optBottom) {
            // 기존 이벤트 리스너 제거
            optBottom.removeEventListener('touchstart', optBottomTouchStartListener);
            optBottom.removeEventListener('touchmove', optBottomTouchMoveListener);
            optBottom.removeEventListener('mousedown', optBottomMouseDownListener);
            document.removeEventListener('mousemove', optBottomMouseMoveListener);
            document.removeEventListener('mouseup', optBottomMouseUpListener);

            // 새로운 이벤트 리스너 설정
            optBottom.addEventListener('touchstart', optBottomTouchStartListener);
            optBottom.addEventListener('touchmove', optBottomTouchMoveListener);
            optBottom.addEventListener('mousedown', optBottomMouseDownListener);
            document.addEventListener('mousemove', optBottomMouseMoveListener);
            document.addEventListener('mouseup', optBottomMouseUpListener);
        }
    }

    function generateSpinnerColor() {
        const colorSets = [
            '#FF0000', // 빨간색
            '#FFA500', // 주황색
            '#0000FF', // 파란색
            '#000080', // 남색
            '#800080', // 보라색
        ];

        const randomIndex = Math.floor(Math.random() * colorSets.length);
        return colorSets[randomIndex];
    }

    function clearAllMapElements() {
        clearMapElements(profileMarkers);
        clearMapElements(scheduleMarkers);
        clearMapElements(markers);
        clearMapElements(logMarkers); // logMarkers도 clear
        clearPolylines();
    }

    function clearMapElements(elements) {
        if (elements && elements.length > 0) {
            elements.forEach(element => {
                if (element.setMap) {
                    element.setMap(null); // 지도에서 요소 제거
                }
            });
            elements.splice(0, elements.length); // 배열 요소 완전히 제거
        }
    }

    function clearPolylines() {
        if (polylines && polylines.length > 0) {
            polylines.forEach(polyline => {
                if (polyline.setMap) {
                    polyline.setMap(null); // 지도에서 폴리라인 제거
                }
            });
            polylines.splice(0, polylines.length); // 배열 요소 완전히 제거
        }
    }

    function generateRandomColor() {
        const colorSets = [
            '#E6F2FF', // 연한 파란색
            '#D6E6FF', // 연한 라벤더
            '#E5F1FF', // 연한 하늘색
            '#F0F8FF', // 연한 앨리스 블루
            '#E0FFFF', // 연한 민트색
            '#E0F0FF', // 밝은 연한 파란색
            '#E0E6FF', // 밝은 연한 라벤더
            '#E0F0FF', // 밝은 연한 하늘색
            '#E6F0FF', // 밝은 연한 앨리스 블루
            '#E6FFFF' // 밝은 연한 민트색
        ];

        const randomIndex = Math.floor(Math.random() * colorSets.length);
        return colorSets[randomIndex];
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

    function updateMemberLocationInfo() {
        return new Promise((resolve, reject) => {
            showMapLoading();

            // sessionStorage 키 생성 (날짜 포함)
            // let cacheKey = 'memberLineData_' + $('#sgdt_idx').val() + '_' + currentSelectedDate;
            // let cachedData = sessionStorage.getItem(cacheKey);

            // if (cachedData) {
            //     // 캐싱된 데이터가 있으면 사용
            //     let response = JSON.parse(cachedData);
            //     if (response) {
            //         initializeMapAndMarkers(response, $('#sgdt_idx').val());
            //         highlightSelectedDate();
            //         updateTimeSlider(response.log_count);
            //         resolve(response);
            //         hideMapLoading();
            //         return; // 함수 종료
            //     }
            // }

            let form_data = new FormData();
            form_data.append("act", "get_line");
            form_data.append("sgdt_mt_idx", $('#sgdt_mt_idx').val());
            form_data.append("sgdt_idx", $('#sgdt_idx').val());
            form_data.append("event_start_date", currentSelectedDate);
            let ad_data = fetchAdDisplayStatus();

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
                    if (data) {
                        // sessionStorage에 데이터 저장 (날짜 포함 키 사용)
                        // sessionStorage.setItem(cacheKey, JSON.stringify(data));
                        // 데이터 처리를 비동기적으로 수행
                        initializeMapAndMarkers(data, $('#sgdt_idx').val());
                        highlightSelectedDate();
                        updateTimeSlider(data.log_count);
                        resolve(data);
                    } else {
                        console.log('No data received');
                        reject('No data received');
                    }
                },
                error: function(err) {
                    console.log(err);
                    jalert('<?=$translations['txt_no_data'] ?>');
                    reject(err);
                },
                complete: function() {
                    // AJAX 요청 및 데이터 처리가 완료된 후 로딩 인디케이터 숨기기
                    hideMapLoading();
                }
            });
        });
    }

    function highlightSelectedDate() {
        $('.c_id').removeClass('active');
        $('#calendar_' + currentSelectedDate).addClass('active');
        $('#event_start_date').val(currentSelectedDate);
    }

    function updateTimeSlider(logCount) {
        timeSlider.max = logCount;
        timeSlider.value = 0;
    }

    async function f_profile_click(mt_idx, sgdt_idx) {
        try {
            $('#sgdt_mt_idx').val(mt_idx);
            $('#sgdt_idx').val(sgdt_idx);

            // await f_calendar_log_init();
            await f_calendar_log_init('today'); // 달력 스케줄
            await f_get_log_location(mt_idx);

            const data = await updateMemberLocationInfo();
            map_panto(data.my_lat, data.mt_long);
        } catch (error) {
            console.error("오류 발생:", error);
            alert("처리 중 문제가 발생했습니다. 다시 시도해 주세요.");
        }
    }

    function f_day_click(sdate) {
        if (sdate === currentSelectedDate) return; // 이미 선택된 날짜면 아무 것도 하지 않음

        currentSelectedDate = sdate;

        // 선택한 날짜의 년월 추출
        const selectedDate = new Date(sdate);
        const selectedYear = selectedDate.getFullYear();
        const selectedMonth = String(selectedDate.getMonth() + 1).padStart(2, '0');
        const newCalendarTitle = `${selectedYear}.${selectedMonth}`;

        // calendar_date_title 업데이트
        const calendarTitleElement = document.getElementById('calendar_date_title');
        if (calendarTitleElement) {
            calendarTitleElement.textContent = newCalendarTitle;
        }

        if (typeof(history.pushState) != "undefined") {
            let url = './log?sdate=' + sdate;
            history.pushState(null, '', url);
        } else {
            location.href = url;
        }

        $('#event_start_date').val(sdate);
        $('#schedule-title').text(get_date_t(sdate));

        highlightSelectedDate();
        f_get_log_location($('#sgdt_mt_idx').val());
        updateMemberLocationInfo()
            .then(data => {
                // AJAX 요청 성공 시 data 사용
                map_panto(data.my_lat, data.mt_long);
                // ... data를 활용한 추가 작업 ...
            })
            .catch(error => {
                // AJAX 요청 실패 시 에러 처리
                console.error("AJAX 오류:", error);
            })
    }

    function f_get_log_location(i, s = "") {
        let form_data = new FormData();
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
            dataType: 'json', // JSON 데이터 타입 명시
            success: function(data) {
                if (data) {
                    // 기존 요소 찾기
                    const grpWrap = document.querySelector('.mt-2.mb-3.px_16');

                    if (grpWrap) {
                        let newUl = grpWrap.querySelector('#location_log_box_dynamic');

                        if (!newUl) {
                            const newDiv2 = document.createElement('div');
                            newDiv2.className = 'border bg-white rounded-lg px_16 py_12';

                            const newP = document.createElement('p');
                            newP.className = 'fs_16 fw_600 mb-3';
                            newP.textContent = "<?=$translations['txt_log_summary'] ?>";

                            newUl = document.createElement('ul');
                            newUl.className = 'loc_rog_ul d-flex align-item-center justify-content-between py-2';
                            newUl.id = 'location_log_box_dynamic';

                            newP.appendChild(newUl);
                            newDiv2.appendChild(newP);
                            grpWrap.appendChild(newDiv2);
                        }

                        newUl.innerHTML = `
                            <li class="text-center border-right flex-fill loc_rog_ul_l12">
                                <p class="fs_15 fw_400 text_gray line_h1_2 text_dynamic d-flex align-items-center justify-content-center" style="height: 32px;"><?=$translations['txt_distance_km'] ?></p>
                                <hr class="my-1">
                                <p class="fs_15 fw_600 line_h1_2 text_dynamic">${data.distance}</p>
                            </li>
                            <li class="text-center border-right flex-fill loc_rog_ul_l13">
                                <p class="fs_15 fw_400 text_gray line_h1_2 text_dynamic d-flex align-items-center justify-content-center" style="height: 32px;"><?=$translations['txt_travel_time'] ?></p>
                                <hr class="my-1">
                                <p class="fs_15 fw_600 line_h1_2 text_dynamic">${data.duration}</p>
                            </li>
                            <li class="text-center flex-fill loc_rog_ul_l14">
                                <p class="fs_15 fw_400 text_gray line_h1_2 text_dynamic d-flex align-items-center justify-content-center" style="height: 32px;"><?=$translations['txt_steps'] ?></p>
                                <hr class="my-1">
                                <p class="fs_15 fw_600 line_h1_2 text_dynamic">${data.steps.toLocaleString()} <?=$translations['txt_steps_short'] ?></p>
                            </li>
                        `;
                    } else {
                        console.error("위치 요약을 표시할 요소를 찾을 수 없습니다.");
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                console.log("Response:", xhr.responseText);
            }
        });
    }

    // function addLocationAdjustmentSlider() {
    //     const targetDiv = document.querySelector('.px_16.mb-3');
    //     if (targetDiv) {
    //         // targetDiv 내부에 이미 내용이 있는지 확인
    //         if (targetDiv.innerHTML.trim() === '') {
    //             // 내용이 비어있는 경우에만 새로운 요소 추가
    //             const html = `
    //             <div class="border bg-white rounded-lg px_16 py_16">
    //                 <div class="loc_rog_adj pb-4">
    //                     <p class="fs_16 fw_600">${'<?= translate('이동경로 따라가기', $userLang); ?>'}</p>
    //                     <div class="pt-4">
    //                         <input type="range" class="custom-range" id="timeSlider" min='1' max='1' value='1'>
    //                     </div>
    //                 </div>
    //             </div>
    //         `;
    //             const newDiv = document.createElement('div');
    //             newDiv.innerHTML = html;
    //             targetDiv.appendChild(newDiv);
    //         } else {
    //             console.log('targetDiv에 이미 내용이 있어 새로운 요소를 추가하지 않았습니다.');
    //         }
    //     } else {
    //         console.error('.px_16.mb-3 요소를 찾을 수 없습니다.');
    //     }
    // }

    function checkAdCount() {
        let ad_data = fetchAdDisplayStatus();
        console.log('log.php - ad_alert : ' + ad_data.ad_alert + ' ad_show : ' + ad_data.ad_show + ' ad_count : ' + ad_data.ad_count);

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
                        gtag('event', 'log_ad', {
                            'event_category': 'show_log',
                            'event_label': 'show',
                            'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                            'platform': isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown')
                        });
                        setTimeout(() => {
                            updateMemberLocationInfo();
                        }, 1000); // 광고 표시 시도 후 1초 뒤에 지도 로드
                    });
            } else {
                updateAdDisplayCount(ad_data);
                updateMemberLocationInfo();
            }
        } catch (err) {
            console.log("Error in checkAdCount: " + err);
            updateAdDisplayCount(ad_data);
            updateMemberLocationInfo();
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
        if ((
                $mem_row['mt_level'] == '2'
                // && ($_SESSION['_mt_idx'] == 286 || $_SESSION['_mt_idx'] == 275 || $_SESSION['_mt_idx'] == 281 )
            )
            || $_SESSION['_mt_idx'] == 281
        ) {
            // 무료회원일 경우 광고 카운트 확인하기
            $ad_row = get_ad_log_check($_SESSION['_mt_idx']);
            $ad_count = $ad_row['log_count']; // 현재 광고 수
            $ad_check = $ad_count % 5;

            if ($ad_check == 4) { // 클릭이 5번째일 때
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
                    let message = {
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
        let logData = {
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
        let form_data = new FormData();
        form_data.append("act", "show_ad_log");
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
    //손으로 바텀시트 움직이기
    document.addEventListener("DOMContentLoaded", function() {
        // console.log('bottom');
        let startY = 0;
        let isDragging;

        if (optBottom) {
            optBottom.addEventListener("touchstart", function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
            });
            optBottom.addEventListener("touchmove", function(event) {
                let currentY = event.touches[0].clientY; // 현재 터치 좌표
                let deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    let newTransformValue = isVisible ? "translateY(0)" : "translateY(42.5%)";
                    optBottom.style.transform = newTransformValue;
                }
            });


            optBottom.addEventListener('mousedown', function(event) {
                startY = event.clientY; // 클릭 시작 좌표 저장
                isDragging = true;
            });

            document.addEventListener('mousemove', function(event) {
                if (isDragging) {
                    let currentY = event.clientY; // 현재 마우스 좌표
                    let deltaY = currentY - startY; // 움직임의 차이 계산

                    // 움직임이 일정 값 이상이면 보이거나 숨김
                    if (Math.abs(deltaY) > 50) {
                        let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                        let newTransformValue = isVisible ? 'translateY(0)' : 'translateY(42.5%)';
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
        optBottomTouchStartListener = optBottom.ontouchstart;
        optBottomTouchMoveListener = optBottom.ontouchmove;
        optBottomMouseDownListener = optBottom.onmousedown;
        optBottomMouseMoveListener = document.onmousemove;
        optBottomMouseUpListener = document.onmouseup;
    });
    // 실시간 마커 이동
    function marker_reload(sgdt_idx) {
        let form_data = new FormData();
        form_data.append("act", "marker_reload");
        form_data.append("sgdt_idx", sgdt_idx);
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
            dataType: 'json',
            success: function(data) {
                if (data) {
                    let my_profile = data.my_profile;
                    let st_lat = data.my_lat;
                    let st_lng = data.mt_long;

                    initNaverMap(my_profile, st_lat, st_lng, data);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function isAndroidDevice() {
        if (/Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined') {
            console.log('<?=$translations['txt_android'] ?>');
        }
        return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
    }

    function isiOSDevice() {
        if (/iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos) {
            console.log('<?=$translations['txt_ios'] ?>');
        }
        return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
    }

    function map_panto(lat, lng) {
        currentLat = lat;
        currentLng = lng;
        if (previousTransformY === 'translateY(0px)') {
            panMapDown();
        } else if (isPannedDown) {
            panMapUp();
        }
    }

    // MutationObserver 설정
    let isPanning = false; // 패닝 중인지 확인하는 플래그
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'style' && optBottom.style.transform !== previousTransformY) {
                previousTransformY = optBottom.style.transform;
                if (previousTransformY === 'translateY(0px)') {
                    if (!isPanning) panMapDown();
                } else {
                    if (!isPanning) panMapUp();
                }
            }
        });
    });

    function panMapDown() {
        isPanning = true;
        originalCenter = map.getCenter();
        let newLat = 'ko' === '<?= $userLang ?>' && '<?= $mem_row['mt_map'] ?>' == 'N' ? (currentLat || originalCenter.lat()) - (300 / 111000) * 1.05 : (currentLat || originalCenter.lat()) - (300 / 111000) * 1.5;
        let newCenter = 'ko' === '<?= $userLang ?>' && '<?= $mem_row['mt_map'] ?>' == 'N' ? new naver.maps.LatLng(newLat, currentLng || originalCenter.lng()) : new google.maps.LatLng(newLat, currentLng || originalCenter.lng());
        if ('ko' === '<?= $userLang ?>' && '<?= $mem_row['mt_map'] ?>' == 'N') {
            map.panTo(newCenter, {
                duration: 700,
                easing: 'easeOutCubic',
                complete: function() {
                    isPanning = false;
                    isPannedDown = true;
                }
            });
        } else {
            map.setOptions({
                animation: null
            });
            map.setCenter(newCenter);
            isPanning = false;
            isPannedDown = true;
        }
    }

    function panMapUp() {
        if (isPannedDown) {
            isPanning = true;
            let targetLatLng = currentLat ? ('ko' === '<?= $userLang ?>' && '<?= $mem_row['mt_map'] ?>' == 'N' ? new naver.maps.LatLng(currentLat, currentLng) : new google.maps.LatLng(currentLat, currentLng)) : originalCenter;
            if ('ko' === '<?= $userLang ?>' && '<?= $mem_row['mt_map'] ?>' == 'N') {
                map.panTo(targetLatLng, {
                    duration: 700,
                    easing: 'easeOutCubic',
                    complete: function() {
                        isPanning = false;
                        isPannedDown = false;
                        originalCenter = null;
                    }
                });
            } else {
                if (originalCenter) {
                    map.setOptions({
                        animation: null
                    });
                    map.setCenter(targetLatLng);
                    isPanning = false;
                    isPannedDown = false;
                    originalCenter = null;
                }
            }
        }
    }

    // 감시 시작
    observer.observe(optBottom, {
        attributes: true,
        attributeFilter: ['style']
    });

    //손으로 바텀시트 움직이기
    document.addEventListener('DOMContentLoaded', function() {
        let startY = 0;
        let isDragging;

        if (optBottom) {
            optBottom.addEventListener('touchstart', function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
            });

            optBottom.addEventListener('touchmove', function(event) {
                let currentY = event.touches[0].clientY; // 현재 터치 좌표
                let deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    let newTransformValue = isVisible ? 'translateY(0)' : 'translateY(<?= $translateY ?>%)';
                    optBottom.style.transform = newTransformValue;
                }
            });

            optBottom.addEventListener('mousedown', function(event) {
                startY = event.clientY; // 클릭 시작 좌표 저장
                isDragging = true;
            });

            document.addEventListener('mousemove', function(event) {
                if (isDragging) {
                    let currentY = event.clientY; // 현재 마우스 좌표
                    let deltaY = currentY - startY; // 움직임의 차이 계��

                    // 움직임이 일정 값 이상이면 보이거나 숨김
                    if (Math.abs(deltaY) > 50) {
                        let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                        let newTransformValue = isVisible ? 'translateY(0)' : 'translateY(<?= $translateY ?>%)';
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
        optBottomTouchStartListener = optBottom.ontouchstart;
        optBottomTouchMoveListener = optBottom.ontouchmove;
        optBottomMouseDownListener = optBottom.onmousedown;
        optBottomMouseMoveListener = document.onmousemove;
        optBottomMouseUpListener = document.onmouseup;
    });
    // 실시간 마커 이동
    function marker_reload(sgdt_idx) {
        let form_data = new FormData();
        form_data.append("act", "marker_reload");
        form_data.append("sgdt_idx", sgdt_idx);
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
            dataType: 'json',
            success: function(data) {
                if (data) {
                    let my_profile = data.my_profile;
                    let st_lat = data.my_lat;
                    let st_lng = data.mt_long;

                    initNaverMap(my_profile, st_lat, st_lng, data);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

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
    //     let sgdt_idx = $('#sgdt_idx').val();
    //     marker_reload(sgdt_idx);
    // }, 30000);
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>