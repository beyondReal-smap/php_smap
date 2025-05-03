<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '3';
$h_menu = '5';
$translations = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $userLang . '.php'; // 번역 파일 로드

$_SUB_HEAD_TITLE = $translations['txt_schedule'];
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_attempt_other_device'], './logout');
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

//오너인 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$row = $DB->getone('smap_group_t', 'count(*) as cnt');
$sgt_cnt = $row['cnt'];

//리더인 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgdt_owner_chk', 'N');
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
$DB->where('sgdt_discharge', 'N');
$DB->where('sgdt_exit', 'N');
$DB->where('sgdt_show', 'Y');
$row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
$sgdt_cnt = $row['cnt'];

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

// PHP 변수를 JSON으로 인코딩하여 JavaScript로 전달
$translations_json = json_encode($translations);

// PHP 부분 상단에 arr_grant 배열 정의 추가
$arr_grant = array(
    '1' => $translations['txt_owner'],
    '2' => $translations['txt_leader'],
    '3' => $translations['txt_member']
);

// translations_json과 함께 arr_grant도 JavaScript로 전달
$arr_grant_json = json_encode($arr_grant);
?>
<style>
    /* 로딩 화면 스타일 개선 */
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
        transition: opacity 0.3s ease;
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

    .grp_tit {
        background-color: rgba(0, 70, 254, 0.05) !important;
    }

    /* 달력 스타일 */
    .sch_cld_wrap {
        padding: 0.5rem 0 0 0;
        transition: height 0.3s ease;
    }

    .sch_wrap {
        padding-top: 0.5rem !important; /* 상단 패딩 줄임 */
        transition: transform 0.3s ease;
    }

    .fs_12.fw_700.text-primary.mb-3.pt_20 {
        padding-top: 16rem !important; /* pt_20을 0.5rem으로 줄임 */
    }

    .cld_head_wr {
        padding: 0 1rem;
    }

    .cld_body {
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .down_wrap {
        padding: 0.2rem 0 1rem 0 !important;
    }

    .down_wrap img {
        width: 12px;
        display: block;
        margin: 0 auto;
    }

    .add_cal_tit {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem; /* 간격을 줄이기 위해 마진을 줄임 */
    }

    .add_cal_tit .btn {
        padding: 0;
    }

    .add_cal_tit .sel_month {
        justify-content: center;
    }

    .cld_head ul {
        display: flex;
        justify-content: space-between;
        padding: 0 1rem;
        margin: 0;
        list-style: none;
    }

    .cld_head ul li {
        flex: 1;
        text-align: center;
        width: calc(100% / 7);
        padding: 0;
    }

    .cld_date_wrap {
        padding: 0 1rem;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        transition: height 0.3s ease;
    }

    .cld_date_wrap .cld_date ul {
        display: flex;
        flex-wrap: wrap;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .cld_date_wrap .cld_date ul li {
        width: calc(100% / 7);
        text-align: center;
        padding: 0;
    }

    /* 캘린더 전환 애니메이션 스타일 수정 */
    .sch_wrap {
        position: relative;
    }
    
    .calendar-month-view {
        height: auto;
        max-height: 400px; /* 월간 뷰의 최대 높이 */
    }
    
    .calendar-week-view {
        height: auto;
        max-height: 150px; /* 주간 뷰의 최대 높이 */
    }

    .disabled-link {
        pointer-events: none;
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .task {
        position: relative;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* 콘텐츠 컨테이너 스타일 */
    #mbr_wr {
        transition: opacity 0.3s ease;
        min-height: 100px; /* 최소 높이 설정으로 레이아웃 이동 방지 */
    }
    
    /* 캘린더 선택 효과 개선 */
    .c_id {
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    /* 오늘 날짜 위에 today 텍스트만 표시 */
    .c_id.today {
        position: relative;
    }

    .c_id.today::before {
        content: "today";
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 8px;
        color: #ff6600;
        font-weight: bold;
        text-transform: lowercase;
        background-color: white;
        padding: 1px 3px;
        border-radius: 3px;
        border: 1px solid #ffcc00;
        white-space: nowrap;
        z-index: 20;
    }

    /* 선택된 날짜 스타일 - 원형으로 표시 */
    .c_id.active, 
    .c_id.selected {
        background-color: #0046FE !important; /* 앱의 primary 색상 */
        color: #ffffff !important;
        border-radius: 50% !important; /* 원형으로 표시 */
        box-shadow: 0 2px 5px rgba(0, 70, 254, 0.3) !important;
    }

    /* 선택된 날짜의 텍스트 색상 */
    .c_id.active span, 
    .c_id.selected span {
        color: #ffffff !important;
    }

    /* 선택된 오늘 날짜 - 진한 파란색 원형 유지하고 today 라벨도 유지 */
    .c_id.today.active,
    .c_id.today.selected {
        background-color: #0046FE !important;
        color: #ffffff !important;
        border-radius: 50% !important;
        border: none !important;
        box-shadow: 0 2px 5px rgba(0, 70, 254, 0.3) !important;
    }
</style>
<link href="<?= CDN_HTTP ?>/lib/dragula/dragula.min.css" rel="stylesheet" />
<script type="text/javascript" src="<?= CDN_HTTP ?>/lib/dragula/dragula.min.js"></script>
<!-- <script type="text/JavaScript" src="https://developers.kakao.com/sdk/js/kakao.min.js"></script> -->
<input type="hidden" id="share_url" value="">
<script>
    // PHP에서 전달된 translations 변수를 JavaScript 변수로 할당
    const translations = <?= $translations_json ?>;
    const arr_grant = <?= $arr_grant_json ?>;

    // Kakao.init("<?= KAKAO_JAVASCRIPT_KEY ?>");

    // 스피너 색상 생성 함수
    function generateSpinnerColor() {
        const colorSets = [
            '#0046FE', // 기본 파란색
            '#4169E1', // 로얄 블루
            '#1E90FF', // 도지블루
            '#4682B4', // 스틸블루
            '#6495ED'  // 콘플라워블루
        ];
        return colorSets[Math.floor(Math.random() * colorSets.length)];
    }

    // 로딩 화면을 보이게 하는 함수
    function showMapLoading(center = true) {
        const loadingElement = document.getElementById('map-loading');
        const spinnerDots = document.querySelectorAll('.dot');

        // 랜덤 색상 적용
        const randomColor = generateSpinnerColor();
        spinnerDots.forEach(dot => {
            dot.style.backgroundColor = randomColor;
        });

        // 부드러운 페이드인 효과 적용
        loadingElement.style.opacity = '0';
        loadingElement.style.display = 'flex';
        
        // 강제 리플로우 트리거
        void loadingElement.offsetWidth;
        
        // 트랜지션 적용
        loadingElement.style.transition = 'opacity 0.3s ease';
        loadingElement.style.opacity = '1';
    }

    // 로딩 화면을 숨기는 함수
    function hideMapLoading() {
        const loadingElement = document.getElementById("map-loading");
        
        // 부드러운 페이드아웃 효과 적용
        loadingElement.style.transition = 'opacity 0.3s ease';
        loadingElement.style.opacity = '0';
        
        // 트랜지션이 완료된 후 display 속성 변경
        setTimeout(() => {
            loadingElement.style.display = 'none';
        }, 300);
    }

    function f_share_link(t) {
        var currentURL = $("#share_url").val();
        var JS_SHARE_TITLE = '<?= KAKAO_JS_SHARE_TITLE ?>';
        var JS_SHARE_DESC = '<?= KAKAO_JS_SHARE_DESC ?>';
        var JS_SHARE_IMG = '<?= KAKAO_JS_SHARE_IMG ?>';

        var form_data = new FormData();
        form_data.append("act", "share_link");
        form_data.append("currentURL", currentURL);

        $.ajax({
            url: "./group_update",
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
                    if (t == "kakao") {
                        if (isAndroid()) {
                            /*
                            Kakao.Share.sendDefault({
                                objectType: 'feed',
                                content: {
                                    title: JS_SHARE_TITLE,
                                    description: JS_SHARE_DESC,
                                    imageUrl: JS_SHARE_IMG,
                                    link: {
                                        webUrl: currentURL,
                                        mobileWebUrl: currentURL,
                                    },
                                },
                            });
                            */
                            window.smapAndroid.openShare("[" + JS_SHARE_TITLE + "]\r\n\r\n" + JS_SHARE_DESC + "\r\n\r\n" + currentURL);
                        } else if (isiOS()) {
                            /*
                            var message = {
                                "type": "kakaoSend",
                                "param": {
                                    title: JS_SHARE_TITLE,
                                    description: JS_SHARE_DESC,
                                    imageUrl: JS_SHARE_IMG,
                                    link: {
                                        webUrl: currentURL,
                                        mobileWebUrl: currentURL,
                                    }
                                }
                            };
                            */
                            var message = {
                                "type": "openShare",
                                "param": "[" + JS_SHARE_TITLE + "]\r\n\r\n" + JS_SHARE_DESC + "\r\n\r\n" + currentURL
                            };
                            window.webkit.messageHandlers.smapIos.postMessage(message);
                        }
                    } else if (t == "clipboard") {
                        var message = {
                            "type": "urlClipBoard",
                            "param": "[" + JS_SHARE_TITLE + "]\r\n\r\n" + JS_SHARE_DESC + "\r\n\r\n" + currentURL
                        };
                        if (isAndroid()) {
                            window.smapAndroid.urlClipBoard("[" + JS_SHARE_TITLE + "]\r\n\r\n" + JS_SHARE_DESC + "\r\n\r\n" + currentURL);
                        } else if (isiOS()) {
                            window.webkit.messageHandlers.smapIos.postMessage(message);
                        }
                        jalert('<?= $translations['txt_referral_code'] ?>');
                    } else if (t == "contact") {
                        var message = {
                            "type": "urlOpenSms",
                            "param": "[" + JS_SHARE_TITLE + "]\r\n\r\n" + JS_SHARE_DESC + "\r\n\r\n" + currentURL
                        };
                        if (isAndroid()) {
                            window.smapAndroid.urlOpenSms("[" + JS_SHARE_TITLE + "]\r\n\r\n" + JS_SHARE_DESC + "\r\n\r\n" + currentURL);
                        } else if (isiOS()) {
                            window.webkit.messageHandlers.smapIos.postMessage(message);
                        }
                    }
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod|Mac|Apple/i);
    }

    function isAndroidDevice() {
        return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
    }

    function isiOSDevice() {
        return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
    }

    // 전역 변수로 현재 선택된 날짜를 저장
    let currentSelectedDate = '';

    function loadScheduleData(sdate) {
        try {
            // 날짜가 지정되지 않은 경우 선택된 날짜 사용 (전역변수 우선)
            if (!sdate) {
                sdate = window.selectedDate || localStorage.getItem('selectedDate') || '<?= date("Y-m-d") ?>';
            }
            
            console.log('loadScheduleData executing with date:', sdate);
            
            // 이전 요청 취소 (중복 방지)
            if (window.currentScheduleRequest) {
                try {
                    window.currentScheduleRequest.abort();
                } catch (e) {}
            }
            
            // 날짜 동기화 즉시 처리
            $('#event_start_date').val(sdate);
            $('#csdate').val(sdate);
            currentSelectedDate = sdate;
            
            // 로컬스토리지 즉시 업데이트
            try {
                localStorage.setItem('selectedDate', sdate);
            } catch (storageError) {
                console.error('Error saving to localStorage:', storageError);
            }
            
            // 캘린더에서 해당 날짜 즉시 하이라이트
            $('.c_id').removeClass('active selected');
            $('#calendar_' + sdate).addClass('active selected');
            
            // 스케줄 데이터 로드 준비
            var form_data = new FormData();
            form_data.append("act", "list");
            form_data.append("event_start_date", sdate);
            
            // 로딩 표시 즉시 보여주기
            showMapLoading();
            $('.fs_12.fw_700.text-primary.mb-3.pt_20').text("");
            
            // AJAX 요청 저장하여 필요 시 취소 가능하도록 함
            window.currentScheduleRequest = $.ajax({
                url: "./schedule_update",
                type: "POST",
                data: form_data,
                processData: false,
                contentType: false,
                dataType: 'json',
                cache: false, // 캐시 사용 안 함
                timeout: 10000, // 타임아웃 시간 증가
                success: function(response) {
                    console.log('Schedule data received for date:', sdate);
                    window.currentScheduleRequest = null;
                    
                    if (response) {
                        // 응답 데이터가 있으면 즉시 UI 업데이트
                        updateScheduleHTML(response, sdate);
                    } else {
                        console.log('No data received');
                        $('#mbr_wr').empty();
                    }
                    
                    hideMapLoading();
                    
                    // 지도 일정 업데이트
                    schedule_map_list(sdate);
                    
                    // 선택된 날짜 다시 한번 확인하여 하이라이트
                    $('.c_id').removeClass('active selected');
                    $('#calendar_' + sdate).addClass('active selected');
                },
                error: function(xhr, status, error) {
                    if (status === 'abort') {
                        console.log('Request aborted');
                    } else {
                        console.error('Error loading schedule data:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                    }
                    
                    window.currentScheduleRequest = null;
                    hideMapLoading();
                }
            });
            
        } catch (error) {
            console.error('Error in loadScheduleData:', error);
            hideMapLoading();
        }
    }

    // HTML 업데이트 함수
    function updateScheduleHTML(data, sdate) {
        // 날짜 표시 업데이트
        if (data.event_start_date_t) {
            $('.fs_12.fw_700.text-primary.mb-3.pt_20').text(data.event_start_date_t + data.txt_schedule_of);
        }

        var html = '';
        
        // 개인 일정 처리
        if (data.mt_file1) {
            html += '<div class="grp_list user_grplist">' +
                '<ul class="mbr_wr_ul">' +
                '<li class="schdl_list">' +
                '<ul>' +
                '<li id="mbr_hd01_1" class="mbr_hd">' +
                '<div class="d-flex justify-content-between">' +
                '<div class="d-flex align-items-center flex-auto">' +
                '<a href="#" class="d-flex align-items-center flex-fill">' +
                '<div class="prd_img flex-shrink-0 mr_12">' +
                '<div class="rect_square rounded_14">' +
                '<img src="' + data.mt_file1 + '" alt="이미지" onerror="this.src=\'' + data.ct_no_profile_img_url + '\'" />' +
                '</div>' +
                '</div>' +
                '<p class="fs_14 fw_500 text_dynamic mr-2">' + (data.mt_nickname || data.mt_name) + '</p>' +
                '</a>' +
                '</div>' +
                '<div class="d-flex align-items-center flex-shrink-0">' +
                '<a href="./schedule_form?sdate=' + sdate + '&mt_idx=' + data.mt_idx + '" class="fs_13 fc_navy"><i class="xi-plus-min"></i>' + translations['txt_add_schedule'] + '</a>' +
                '<button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr01_1" aria-expanded="false" aria-controls="mbr01" style="visibility:' + (data.list_sst_a && data.list_sst_a.length > 0 ? 'visible' : 'hidden') + '">' +
                '<img class="open_ic" src="' + data.CDN_HTTP + '/img/ic_open.png" style="width:1.0rem;">' +
                '</button>' +
                '</div>' +
                '</div>';

            // 일정이 있을 때만 collapse 구조 추가
            if (data.list_sst_a && data.list_sst_a.length > 0) {
                html += '<div id="mbr01_1" class="collapse" aria-labelledby="mbr01_1" data-parent="#mbr_wr">' +
                    '<ul class="pt-4 pb-3">';

                data.list_sst_a.forEach(function(schedule, index) {
                    var point_status = getScheduleStatus(schedule);
                    html += generateScheduleListItem(schedule, index + 1, point_status);
                });

                html += '</ul></div>';
            }

            html += '</li></ul></li></ul></div>';
        }

        // 그룹 일정 처리
        if (data.group_data && Array.isArray(data.group_data)) {
            data.group_data.forEach(function(group) {
                if (group.group && group.group.sgt_title) {
                    html += generateGroupSchedule(group, sdate);
                }
            });
        }

        // 부드러운 전환 효과를 위한 처리
        const contentContainer = $('#mbr_wr');
        
        // 기존 내용 페이드 아웃
        contentContainer.css({
            'opacity': '1',
            'transition': 'opacity 0.2s ease'
        });
        
        contentContainer.css('opacity', '0');
        
        // 페이드 아웃 후 새 내용으로 교체
        setTimeout(() => {
            contentContainer.html(html);
            
            // 새 내용 페이드 인
            setTimeout(() => {
                contentContainer.css('opacity', '1');
                console.log('HTML updated with length:', html.length);
            }, 50);
        }, 200);
    }

    function getScheduleStatus(schedule) {
        var current_date = new Date();
        if (schedule.sst_all_day == 'Y') {
            return 'point_ing';
        } else if (current_date >= new Date(schedule.sst_edate)) {
            return 'point_done';
        } else if (current_date >= new Date(schedule.sst_sdate) && current_date <= new Date(schedule.sst_edate)) {
            return 'point_ing';
        }
        return 'point_gonna';
    }

    function generateScheduleListItem(schedule, index, point_status) {
        var grantNumbers = schedule.sst_update_chk ? schedule.sst_update_chk.split(',') : [];
        var grantStrings = grantNumbers.map(function(number) {
            return arr_grant[number] || translations['txt_member']; // 기본값으로 'member' 사용
        });
        var grant = grantNumbers.includes('1') && grantNumbers.includes('2') && grantNumbers.includes('3') ? 
            translations['txt_all'] : grantStrings.join(', ');
            
        // 현재 사용자가 일정 소유자인지 확인
        var isOwner = schedule.mt_idx == <?= $_SESSION['_mt_idx'] ?>;
        var canEdit = isOwner || grantNumbers.includes('1'); // 소유자이거나 수정 권한이 있는 경우
        var editLink = canEdit ? './schedule_form?sst_idx=' + schedule.sst_idx : 'javascript:void(0);';
        var editClass = canEdit ? '' : 'disabled-link';
        
        if (isOwner && !grantNumbers.includes('1')) {
            // 소유자이지만 수정 권한이 없는 경우 (새로 생성된 일정 등)
            grant = translations['txt_owner'] + (grant ? ', ' + grant : '');
        }

        return '<li class="py-2">' +
            '<a href="' + editLink + '" class="d-flex align-items-center justify-content-between ' + editClass + '">' +
            '<div class="d-flex align-items-center">' +
            '<div class="task ' + point_status + '">' +
            '<span class="point_inner">' +
            '<span class="point_txt">' + index + '</span>' +
            '</span>' +
            '</div>' +
            '<div class="mx-3">' +
            '<p class="fs_13 fw_700 text_dynamic line_h1_3 line1_text">' + schedule.sst_title + '</p>' +
            '<p class="fs_10 fw_300 text_gray line_h1_3"><span>' + translations['txt_edit_rights'] + ' : </span> ' + grant + '</p>' +
            '</div>' +
            '</div>' +
            '<p><i class="xi-angle-right-min text_light_gray fs_13"></i></p>' +
            '</a>' +
            '</li>';
    }

    function generateGroupSchedule(group, sdate) {
        var html = '<div class="grp_list">' +
            '<div class="grp_tit">' +
            '<p class="fs_17 fw_700 line_h1_3 line1_text text_dynamic">' + group.group.sgt_title + '</p>' +
            '</div>' +
            '<ul class="mbr_wr_ul">';

        if (group.members && group.members.length > 0) {
            group.members.forEach(function(member, key) {
                html += generateMemberSchedule(member, group.schedules, sdate);
            });
        } else {
            html += '<li class="schdl_list">' +
                '<button type="button" class="btn w-100 h-auto fs_13 fc_navy schdl_btn" onclick="share_link_modal(\'' + group.group.sgt_idx + '\')"><i class="xi-plus-min mr-2"></i>' + translations['txt_invite_group_members'] + '</button>' +
                '</li>';
        }

        html += '</ul></div>';
        return html;
    }

    function generateMemberSchedule(member, schedules, sdate) {
        var memberSchedules = schedules ? schedules.filter(function(schedule) {
            return schedule.sgdt_idx === member.sgdt_idx;
        }) : [];

        var html = '<li class="schdl_list">' +
            '<ul>' +
            '<li id="mbr_hd02_' + member.sgdt_idx + '" class="mbr_hd">' +
            '<div class="d-flex justify-content-between">' +
            '<div class="d-flex align-items-center flex-auto">' +
            '<a href="#" class="d-flex align-items-center flex-fill">' +
            '<div class="prd_img flex-shrink-0 mr_12">' +
            '<div class="rect_square rounded_14">' +
            '<img src="/img/uploads/' + member.mt_file1 + '" onerror="this.src=\'' + "<?= $ct_no_profile_img_url ?>" + '\'" alt="이미지" />' +
            '</div>' +
            '</div>' +
            '<p class="fs_14 fw_500 text_dynamic mr-2">' + (member.mt_nickname ? member.mt_nickname : member.mt_name) + '</p>' +
            '</a>' +
            '</div>' +
            '<div class="d-flex align-items-center flex-shrink-0">';

        // 소유자나 리더인 경우에만 일정 추가 버튼 표시
        if (member.sgdt_owner_leader_chk_t !== translations['txt_owner']) {
            html += '<a href="./schedule_form?sdate=' + sdate + '&sgdt_idx=' + member.sgdt_idx + '" class="fs_13 fc_navy"><i class="xi-plus-min"></i>' + translations['txt_add_schedule'] + '</a>';
        }

        if (memberSchedules.length > 0) {
            html += '<button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr02_' + member.sgdt_idx + '" aria-expanded="false" aria-controls="mbr02_' + member.sgdt_idx + '"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>';
        } else {
            html += '<button type="button" style="visibility:hidden" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr02_' + member.sgdt_idx + '" aria-expanded="false" aria-controls="mbr02_' + member.sgdt_idx + '"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>';
        }

        html += '</div></div>';

        if (memberSchedules.length > 0) {
            html += '<div id="mbr02_' + member.sgdt_idx + '" class="collapse" aria-labelledby="mbr02_' + member.sgdt_idx + '" data-parent="#mbr_wr">' +
                '<ul class="pt-4 pb-3">';

            memberSchedules.forEach(function(schedule, count) {
                var point_status = getScheduleStatus(schedule);
                html += generateScheduleListItem(schedule, count + 1, point_status);
            });

            html += '</ul></div>';
        }

        html += '</li></ul></li>';
        return html;
    }

    // 페이지 로드 시 초기화 함수 수정
    $(document).ready(function() {
        console.log('Document ready');
        
        // 전역 변수 초기화
        window.isLoadingScheduleData = false;
        window.lastLoadedDate = null;
        
        // 전역 이벤트 리스너 - 이벤트 위임 개선
        $(document).on('click touchstart', '.cld_date_wrap .c_id', function(e) {
            // 이벤트 타입 확인 및 처리
            if (e.type === 'touchstart') {
                // 터치 이벤트 발생 시 클릭 이벤트 방지
                e.preventDefault();
                
                // 같은 요소에 대한 클릭 이벤트가 발생하지 않도록 플래그 설정
                $(this).data('touch-processed', true);
                setTimeout(() => {
                    $(this).data('touch-processed', false);
                }, 300); // 300ms 후 플래그 초기화
            } else if (e.type === 'click') {
                // 터치 이벤트가 이미 처리된 경우 클릭 이벤트 무시
                if ($(this).data('touch-processed')) {
                    return;
                }
            }
            
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const $this = $(this);
            // 중복 클릭 방지
            if ($this.data('processing')) {
                return;
            }
            $this.data('processing', true);
            
            // 선택한 날짜 가져오기
            const date = $this.attr('id').replace('calendar_', '');
            console.log('Calendar date selected:', date);
            
            if (!date) {
                console.log('No date found in clicked element');
                $this.data('processing', false);
                return;
            }
            
            try {
                // 전역 변수로 선택된 날짜 설정
                window.selectedDate = date;
                
                // 선택된 날짜 하이라이트 처리
                $('.c_id').removeClass('active selected');
                $this.addClass('active selected');
                
                // 상태 업데이트 - 모든 히든 필드 동기화
                $('#event_start_date').val(date);
                $('#csdate').val(date);
                currentSelectedDate = date;
                
                // 로컬스토리지에 선택한 날짜 저장 - 동기적으로 처리
                try {
                    localStorage.setItem('selectedDate', date);
                } catch (storageError) {
                    console.error('Error saving to localStorage:', storageError);
                }
                
                // 데이터 로드 전에 시각적 피드백
                showMapLoading();
                
                // URL 업데이트 - 즉시 처리
                if (typeof(history.pushState) != "undefined") {
                    var state = { date: date };
                    var url = './schedule?sdate=' + date;
                    history.pushState(state, '', url);
                }
                
                // 년/월 타이틀 업데이트
                const selectedDate = new Date(date);
                const selectedYearMonth = selectedDate.getFullYear() + "." + String(selectedDate.getMonth() + 1).padStart(2, '0');
                $('#calendar_date_title').text(selectedYearMonth);
                
                // 데이터 로드 - 즉시 실행
                loadScheduleData(date);
                
                // 플래그 해제는 데이터 로드 후 바로 처리
                setTimeout(() => {
                    $this.data('processing', false);
                }, 50);
                
            } catch (error) {
                console.error('Error processing calendar event:', error);
                $this.data('processing', false);
            }
        });

        // 캘린더 이벤트 재바인딩 함수 개선
        function rebindCalendarEvents() {
            // 기존 이벤트 제거
            $('.c_id').off('click touchstart');
            
            // 새로운 이벤트 바인딩은 document 레벨에서 이미 처리되므로 추가 바인딩 불필요
            console.log('Calendar events rebound');
            
            // 현재 선택된 날짜 하이라이트 복원
            if (currentSelectedDate) {
                $('.c_id').removeClass('active selected');
                $('#calendar_' + currentSelectedDate).addClass('active selected');
            }
        }

        // 캘린더 초기화 함수 재정의
        const originalCalendarInit = window.f_calendar_init;
        window.f_calendar_init = function(type, callback) {
            console.log('Calendar init:', type);
            
            // 오늘 날짜 초기화 시 현재 날짜로 모든 필드 동기화
            if (type === 'today') {
                var today = '<?= date("Y-m-d") ?>';
                $('#event_start_date').val(today);
                $('#csdate').val(today);
                currentSelectedDate = today;
            }
            
            // 지연 시간 없이 바로 실행
            originalCalendarInit(type, function() {
                rebindCalendarEvents();
                
                // 콜백이 있으면 즉시 실행
                if (typeof callback === 'function') {
                    callback();
                }
            });
        };

        // swiper 버튼 이벤트 처리
        $('.swiper-button-prev').on('click', function() {
            f_calendar_init('prev', function() {
                updateCalendarTitle();
                rebindCalendarEvents();
            });
        });
        
        $('.swiper-button-next').on('click', function() {
            f_calendar_init('next', function() {
                updateCalendarTitle();
                rebindCalendarEvents();
            });
        });
        
        // 페이지 가시성 변경 감지 (다른 페이지에서 돌아왔을 때)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                console.log('Page is now visible again');
                
                // 날짜 정보 가져오기 - URL, localStorage, 현재 선택, 오늘 날짜 순으로 확인
                var urlParams = new URLSearchParams(window.location.search);
                var urlDate = urlParams.get('sdate');
                
                try {
                    var storedDate = localStorage.getItem('selectedDate');
                } catch (e) {
                    var storedDate = null;
                    console.error('Error reading from localStorage:', e);
                }
                
                var today = '<?= date("Y-m-d") ?>';
                
                // 날짜 결정
                var selectedDate = urlDate || window.selectedDate || storedDate || today;
                
                console.log('Visibility change sources - URL:', urlDate, 
                          ', Global:', window.selectedDate,
                          ', Storage:', storedDate, 
                          ', Selected:', selectedDate);
                
                // 현재 표시된 날짜와 다를 경우 업데이트
                if (selectedDate !== $('#event_start_date').val()) {
                    console.log('Date changed, updating UI');
                    
                    // 전역변수 업데이트
                    window.selectedDate = selectedDate;
                    
                    // DOM 업데이트
                    $('#event_start_date').val(selectedDate);
                    $('#csdate').val(selectedDate);
                    currentSelectedDate = selectedDate;
                    
                    // 로컬스토리지 업데이트
                    try {
                        localStorage.setItem('selectedDate', selectedDate);
                    } catch (e) {
                        console.error('Error saving to localStorage:', e);
                    }
                    
                    // URL 업데이트
                    if (typeof(history.replaceState) != "undefined") {
                        var state = { date: selectedDate };
                        var url = './schedule?sdate=' + selectedDate;
                        history.replaceState(state, '', url);
                    }
                    
                    // 캘린더와 데이터 즉시 업데이트 - 순차 실행
                    loadScheduleData(selectedDate);
                    
                    // 캘린더 UI도 업데이트
                    f_calendar_init('date');
                } else {
                    console.log('No date change detected');
                }
            }
        });

        // 전역 변수 - 페이지 간 공유할 선택된 날짜
        window.selectedDate = null;
        
        var urlParams = new URLSearchParams(window.location.search);
        var today = '<?= date("Y-m-d") ?>';
        
        // 날짜 결정 우선순위
        try {
            var storedDate = localStorage.getItem('selectedDate');
        } catch (e) {
            var storedDate = null;
            console.error('Error reading from localStorage:', e);
        }
        
        var initialDate = urlParams.get('sdate') || storedDate || today;
        
        // 전역 변수에 저장
        window.selectedDate = initialDate;
        
        console.log('Initial date sources - URL:', urlParams.get('sdate'), 
                   ', LocalStorage:', storedDate, 
                   ', Today:', today, 
                   ', Selected:', initialDate);
        
        // 페이지 로드 시 캘린더와 스케줄 동기화를 위해 타이밍 보장
        var syncPageLoad = function() {
            // 모든 히든 필드 초기화 동기화
            currentSelectedDate = window.selectedDate || initialDate;
            $('#event_start_date').val(currentSelectedDate);
            $('#csdate').val(currentSelectedDate);
            
            // 로컬스토리지 업데이트
            try {
                localStorage.setItem('selectedDate', currentSelectedDate);
            } catch (e) {
                console.error('Error saving to localStorage:', e);
            }
            
            // URL도 일치하도록 업데이트
            if (typeof(history.replaceState) != "undefined") {
                var state = { date: currentSelectedDate };
                var url = './schedule?sdate=' + currentSelectedDate;
                history.replaceState(state, '', url);
            }
            
            // 데이터 로드
            loadScheduleData(currentSelectedDate);
            
            // 추가 데이터 로드
            f_get_box_list();
            
            // 캘린더 초기화 - 선택된 날짜 유지
            f_calendar_init('date');
        };
        
        // 브라우저 준비 상태에 따라 동기화 실행
        if (document.readyState === 'complete') {
            // 페이지 로드 완료 후 즉시 실행
            syncPageLoad();
        } else {
            // DOM이 완전히 로드된 후 동기화 실행 (우선순위)
            $(document).ready(syncPageLoad);
        }

        // 캘린더 뷰 상태를 저장하는 변수 추가
        let isWeekView = true;
        
        // 화살표 클릭 이벤트 처리
        $('.down_wrap').on('click', function() {
            const arrow = $(this).find('img');
            const calendarBox = $('#schedule_calandar_box');
            const scheduleWrap = $('.sch_wrap');
            isWeekView = !isWeekView;
            
            // 현재 선택된 날짜 가져오기 - 전역변수 우선, 그 다음 DOM
            let currentDate = window.selectedDate || $('#event_start_date').val() || currentSelectedDate || '<?= date("Y-m-d") ?>';
            
            console.log('View toggle - Current selected date:', currentDate);
            
            // 뷰 상태에 따른 UI 변경
            if (isWeekView) {
                // 주간 뷰로 전환
                arrow.css('transform', 'rotate(0deg)');
                calendarBox.css({
                    'transition': 'height 0.3s ease',
                }).removeClass('calendar-month-view').addClass('calendar-week-view');
                scheduleWrap.css({
                    'transform': 'translateY(0)',
                    'transition': 'transform 0.3s ease'
                });
            } else {
                // 월간 뷰로 전환
                arrow.css('transform', 'rotate(180deg)');
                calendarBox.css({
                    'transition': 'height 0.3s ease',
                }).removeClass('calendar-week-view').addClass('calendar-month-view');
                
                const monthViewHeight = calendarBox.height();
                const weekViewHeight = 150;
                const additionalOffset = 300;
                const moveDistance = monthViewHeight - weekViewHeight + additionalOffset + 10;
                
                scheduleWrap.css({
                    'transform': `translateY(${moveDistance}px)`,
                    'transition': 'transform 0.3s ease'
                });
            }
            
            // 히든 필드 업데이트
            $('#week_calendar').val(isWeekView ? 'Y' : 'N');
            
            // 날짜 값 보존을 위해 명시적으로 설정 - 전역변수에도 저장
            $('#event_start_date').val(currentDate);
            $('#csdate').val(currentDate);
            currentSelectedDate = currentDate;
            window.selectedDate = currentDate;
            
            // 로컬스토리지에 직접 저장하여 동기화 유지
            try {
                localStorage.setItem('selectedDate', currentDate);
            } catch (e) {
                console.error('Error saving to localStorage:', e);
            }
            
            // 현재 선택된 날짜로 캘린더 초기화 - 'selected_date' 파라미터 추가
            f_calendar_init('selected_date', function() {
                // 명시적으로 데이터 로드 호출 - 새로 고침 방지
                loadScheduleData(currentDate);
            });
        });
        
        // 초기 상태 설정
        $('#schedule_calandar_box').addClass('calendar-week-view');

        // 브라우저 뒤로가기/앞으로가기 처리
        window.addEventListener('popstate', function(event) {
            // 날짜 결정 (URL 또는 이벤트 상태)
            var urlParams = new URLSearchParams(window.location.search);
            var urlDate = urlParams.get('sdate');
            var today = '<?= date("Y-m-d") ?>';
            
            // 날짜 결정 - URL, 이벤트 상태, 오늘 날짜 순으로 우선순위
            var selectedDate = urlDate;
            if (!selectedDate && event.state && event.state.date) {
                selectedDate = event.state.date;
            }
            if (!selectedDate) {
                selectedDate = today;
            }
            
            console.log('Popstate - Selected date:', selectedDate);
            
            // 전역변수 업데이트
            window.selectedDate = selectedDate;
            
            // 캘린더 선택 동기화
            currentSelectedDate = selectedDate;
            $('#event_start_date').val(selectedDate);
            $('#csdate').val(selectedDate);
            
            // 로컬스토리지 업데이트
            try {
                localStorage.setItem('selectedDate', selectedDate);
            } catch (e) {
                console.error('Error saving to localStorage:', e);
            }
            
            // 캘린더 상태 업데이트
            $('.c_id').removeClass('active selected');
            $('#calendar_' + selectedDate).addClass('active selected');
            
            // 캘린더와 데이터 즉시 업데이트 - 순차 실행
            loadScheduleData(selectedDate);
            
            // 캘린더 UI도 업데이트
            f_calendar_init('date');
        });
    });

    function schedule_map_list(date) {
        showMapLoading();
        $.ajax({
            type: "POST",
            url: "./schedule_update.php",
            data: {
                act: "map_schedule_list",
                event_start_date: date
            },
            success: function(response) {
                $("#map_schedule_list").html(response);
                hideMapLoading();
            },
            error: function() {
                hideMapLoading();
                console.error('지도 일정 로드 중 오류가 발생했습니다.');
            }
        });
    }

    function share_link_modal(i) {
        var form_data = new FormData();
        form_data.append("act", "link_modal");
        form_data.append("sgt_idx", i);

        $.ajax({
            url: "./group_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data == 'N') {
                    jalert('초대드를 사용하였습니다.');
                } else {
                    $('#share_url').val(data);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
        $('#link_modal').modal('show');
    }

    // 캘린더 타이틀 업데이트 함수 추가
    function updateCalendarTitle() {
        if ($('#week_calendar').val() === 'Y') {
            // 주간 뷰일 때는 첫 번째 날짜 기준으로 타이틀 업데이트
            const firstDayElement = $('.cld_date ul li:not(.disabled)').first();
            if (firstDayElement.length) {
                const firstDayId = firstDayElement.find('.c_id').attr('id');
                if (firstDayId) {
                    const date = firstDayId.replace('calendar_', '');
                    const selectedDate = new Date(date);
                    const selectedYearMonth = selectedDate.getFullYear() + "." + String(selectedDate.getMonth() + 1).padStart(2, '0');
                    $('#calendar_date_title').text(selectedYearMonth);
                }
            }
        } else {
            // 월간 뷰일 때는 현재 선택된 날짜 기준으로 타이틀 업데이트
            const currentDate = $('#event_start_date').val() || currentSelectedDate;
            const selectedDate = new Date(currentDate);
            const selectedYearMonth = selectedDate.getFullYear() + "." + String(selectedDate.getMonth() + 1).padStart(2, '0');
            $('#calendar_date_title').text(selectedYearMonth);
        }
    }
</script>
<div class="container sub_pg bg_main px-0">
    <div class="sch_wrap_top">
        <div class="fixed_top sch_cld_wrap bg-white pt-3 border-bottom">
            <div class="cld_head_wr">
                <div class="add_cal_tit">
                    <button type="button" class="btn h-auto swiper-button-prev"><i class="xi-angle-left-min"></i></button>
                    <div class="sel_month d-inline-flex flex-grow-1 text-centerf">
                        <a href="javascript:;" onclick="f_calendar_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="<?= $translations['txt_month_selection_icon'] ?>" style="width:1.6rem; "></a>
                        <p class="fs_15 fw_600" id="calendar_date_title"><?= $calendar_date_title ?></p>
                    </div>
                    <button type="button" class="btn h-auto swiper-button-next"><i class="xi-angle-right-min"></i></button>
                </div>
                <div class="cld_head fs_12">
                    <ul>
                        <li class="sun text-danger"><?= $translations['txt_sunday'] ?></li>
                        <li><?= $translations['txt_monday'] ?></li>
                        <li><?= $translations['txt_tuesday'] ?></li>
                        <li><?= $translations['txt_wednesday'] ?></li>
                        <li><?= $translations['txt_thursday'] ?></li>
                        <li><?= $translations['txt_friday'] ?></li>
                        <li class="sat text-primary"><?= $translations['txt_saturday'] ?></li>
                    </ul>
                </div>
            </div>
            <div id="schedule_calandar_box" class="cld_date_wrap"></div>
            <div class="down_wrap text-center pt_08 pb-3">
                <img src="<?= CDN_HTTP ?>/img/btn_bl_arrow.png" class="top_down mx-auto" width="12px" alt="<?= $translations['txt_top_down'] ?>" style="transform: rotate(0deg); transition: transform 0.3s ease;" />
            </div>
        </div>
        <form name="frm_list" id="frm_list">
            <!-- <input type="hidden" name="act" id="act" value="list" /> -->
            <input type="hidden" name="obj_list" id="obj_list" value="schedule_list_box" />
            <!-- <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" /> -->
            <!-- <input type="hidden" name="obj_uri" id="obj_uri" value="./schedule_update" /> -->
            <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
            <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
            <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
            <input type="hidden" name="event_start_date" id="event_start_date" value="<?= $sdate ?>" />
            <input type="hidden" name="week_calendar" id="week_calendar" value="Y" />
            <input type="hidden" name="csdate" id="csdate" value="<?= $sdate ?>" />
            <input type="hidden" name="nmy" id="nmy" value="<?= $now_month_year ?>" />
        </form>
        <div class="sch_wrap px_16 pt_22 scroll_bar_y" id="schedule_list_box">
            <div id="map-loading" style="display: flex;">
                <div class="dots-spinner">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
            <p class="fs_12 fw_700 text-primary mb-3 pt_20"></p>
            <div id="mbr_wr"></div>
        </div>
        <!-- <button type="button" class="btn w-100 floating_btn rounded b_botton_2" onclick="f_go_schedule_form();"><i class="xi-plus-min mr-3"></i> 일정 추가하기</button> -->
    </div>
    <? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
        <div class="floating_wrap on">
            <div class="flt_inner">
                <div class="flt_head">
                    <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= $translations['txt_create_group'] ?></span></p>
                </div>
                <div class="flt_body pb-5 pt-3">
                    <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= $translations['txt_create_group_first'] ?>
                    </p>
                    <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?= $translations['txt_create_group_friends'] ?></p>
                </div>
                <div class="flt_footer">
                    <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'"><?= $translations['txt_next'] ?></button>
                </div>
            </div>
        </div>
    <? } ?>
    <? if ($sgt_cnt == 1 && $expt_cnt < 1) { ?>
        <div class="floating_wrap on">
            <div class="flt_inner">
                <div class="flt_head">
                    <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= $translations['txt_invite_members'] ?></span></p>
                </div>
                <div class="flt_body pb-5 pt-3">
                    <?= $translations['txt_share_location_schedule_enjoy'] ?>
                </div>
                <div class="flt_footer">
                    <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'"><?= $translations['txt_goto_invite'] ?></button>
                </div>
            </div>
        </div>
    <? } ?>
    <!-- 멤버 초대 링크보내기 -->
    <div class="modal btn_sheeet_wrap fade" id="link_modal" tabindex="-1">
        <div class="modal-dialog btm_sheet">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <div class="d-inline-block w-100 text-right">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png" width="24px"></button>
                    </div>
                    <p class="fs_18 fw_700 text_dynamic line_h1_2"><?= $translations['txt_better_together'] ?></p>
                </div>
                <div class="modal-body">
                    <ul>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('kakao');" class="d-flex align-items-center justify-content-between py_07">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_kakao.png" alt="<?= $translations['txt_kakao'] ?>" width="40px" class="mr_12" id="kakao_image" />
                                    <p class="fs_15 fw_500 gray_900" id="kakao_text"><?= $translations['txt_kakao'] ?></p>
                                </div>
                                <i class=" xi-angle-right-min fs_15 text_gray"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('clipboard');" class="d-flex align-items-center justify-content-between py_07 btn_copy">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_link.png" alt="<?= $translations['txt_referral_code'] ?>" width="40px" class="mr_12" />
                                    <p class="fs_15 fw_500 gray_900"><?= $translations['txt_referral_code'] ?></p>
                                </div>
                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('contact');" class="d-flex align-items-center justify-content-between py_07">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_address.png" alt="<?= $translations['txt_contact'] ?>" width="40px" class="mr_12" />
                                    <p class="fs_15 fw_500 gray_900"><?= $translations['txt_contact'] ?></p>
                                </div>
                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            if (isAndroid()) {
                // $('#kakao_text').text('카카오톡 열기');
                // document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_kakao.png";
                $('#kakao_text').text("<?= $translations['txt_share_button'] ?>");
                document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
            } else if (isiOS()) {
                $('#kakao_text').text("<?= $translations['txt_share_button'] ?>");
                document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
            }
        });
    </script>
    <?php
    include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
    ?>