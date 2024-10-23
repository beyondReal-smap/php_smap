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

?>
<style>
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
</style>
<link href="<?= CDN_HTTP ?>/lib/dragula/dragula.min.css" rel="stylesheet" />
<script type="text/javascript" src="<?= CDN_HTTP ?>/lib/dragula/dragula.min.js"></script>
<!-- <script type="text/JavaScript" src="https://developers.kakao.com/sdk/js/kakao.min.js"></script> -->
<input type="hidden" id="share_url" value="">
<script>
    // Kakao.init("<?= KAKAO_JAVASCRIPT_KEY ?>");

    // 로딩 화면을 보이게 하는 함수
    function showMapLoading(center = true) {
        const loadingElement = document.getElementById('map-loading');
        const spinnerDots = document.querySelectorAll('.dot'); // 모든 .dot 요소 선택
        // const otherSpinnerDots = document.querySelectorAll('.mt-2.mb-3.px_16 .dot'); // .mt-2.mb-3.px_16의 .dot 요소 선택

        // 랜덤 색상 적용
        const randomColor = generateSpinnerColor();

        // 두 스피너의 색상 변경
        spinnerDots.forEach(dot => {
            dot.style.backgroundColor = randomColor;
        });

        // loadingElement.style.transform = 'translate(0, -10%)';
        loadingElement.style.display = 'flex'; // 로딩바 표시
    }

    // 로딩 화면을 숨기는 함수
    function hideMapLoading() {
        document.getElementById("map-loading").style.display = 'none';
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
</script>
<div class="container sub_pg bg_main px-0">
    <div class="sch_wrap_top">
        <div class="fixed_top sch_cld_wrap bg-white pt-3 border-bottom">
            <div class="cld_head_wr">
                <div class="add_cal_tit">
                    <button type="button" class="btn h-auto" onclick="f_calendar_init('prev');"><i class="xi-angle-left-min"></i></button>
                    <div class="sel_month d-inline-flex flex-grow-1 text-centerf">
                        <a href="javascript:;" onclick="f_calendar_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="<?= $translations['txt_month'] ?>" style="width:1.6rem; "></a>
                        <p class="fs_15 fw_600" id="calendar_date_title"><?= $calendar_date_title ?></p>
                    </div>
                    <button type="button" class="btn h-auto" onclick="f_calendar_init('next');"><i class="xi-angle-right-min"></i></button>
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
                <img src="<?= CDN_HTTP ?>/img/btn_bl_arrow.png" class="top_down mx-auto" width="12px" alt="<?= $translations['txt_top_down'] ?>" />
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

            <script>
                $(document).ready(function() {
                    // 초기 로드
                    showMapLoading();
                    loadScheduleData($('input[name="event_start_date"]').val());
                    f_get_box_list();
                    f_calendar_init('today');
                });

                // AJAX 요청 함수
                function loadScheduleData() {
                    // HTML 초기화 로직 추가
                    $('#mbr_wr').empty(); // #mbr_wr 내용 비우기
                    $('.fs_12.fw_700.text-primary.mb-3.pt_20').text(""); // 날짜 표시 초기화
                    var form_data = new FormData();
                    form_data.append("act", "list");
                    form_data.append("event_start_date", $("#event_start_date").val());

                    $.ajax({
                        url: "./schedule_update",
                        enctype: "multipart/form-data",
                        data: form_data,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        // cache: true,
                        timeout: 10000,
                        dataType: 'json',
                        success: function(data) {
                            if (data) {
                                updateScheduleHTML(data);
                            } else {
                                console.log('No data received');
                            }
                        },
                    });
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

                // HTML 업데이트 함수
                function updateScheduleHTML(data) {
                    var event_start_date = data.event_start_date_t;
                    // 날짜 표시
                    $('.fs_12.fw_700.text-primary.mb-3.pt_20').text(event_start_date + "<?= $translations['txt_schedule_of'] ?>");
                    var userScheduleHTML = updateUserSchedule(data);
                    var groupSchedulesHTML = updateGroupSchedules(data);

                    $('#mbr_wr').html(userScheduleHTML + groupSchedulesHTML);
                    hideMapLoading();
                }

                // 사용자 일정 HTML 생성 함수
                function updateUserSchedule(schedules) {
                    var mt_file1 = schedules.mt_file1;
                    var mt_nickname = schedules.mt_nickname ? schedules.mt_nickname : schedules.mt_name;
                    var mt_idx = schedules.mt_idx;
                    var CDN_HTTP = schedules.CDN_HTTP;
                    var event_start_date = schedules.event_start_date;
                    var list_sst_a = schedules.list_sst_a.filter(function(schedule) {
                        return schedule.sgdt_idx == schedules.sgdt_idx;
                    });

                    var html = '<div class="grp_list user_grplist">' +
                        '<ul class="mbr_wr_ul">' +
                        '<li class="schdl_list">' +
                        '<ul>' +
                        '<li id="mbr_hd01_1" class="mbr_hd">' +
                        '<div class="d-flex justify-content-between">' +
                        '<div class="d-flex align-items-center flex-auto">' +
                        '<a href="#" class="d-flex align-items-center flex-fill">' +
                        '<div class="prd_img flex-shrink-0 mr_12">' +
                        '<div class="rect_square rounded_14">' +
                        '<img src="' + mt_file1 + '" alt="이미지" onerror="this.src=\'' + "<?= $ct_no_profile_img_url ?>" + '\'" />' +
                        '</div>' +
                        '</div>' +
                        '<p class="fs_14 fw_500 text_dynamic mr-2">' + mt_nickname + '</p>' +
                        '</a>' +
                        '</div>' +
                        '<div class="d-flex align-items-center flex-shrink-0">' +
                        '<a href="./schedule_form?sdate=' + $('#event_start_date').val() + '&mt_idx=' + mt_idx + '" class="fs_13 fc_navy"><i class="xi-plus-min"></i>' + "<?= $translations['txt_add_schedule'] ?>" + '</a>';

                    if (list_sst_a.length > 0) {
                        html += '<button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr01_1" aria-expanded="false" aria-controls="mbr01"><img class="open_ic" src="' + CDN_HTTP + '/img/ic_open.png" style="width:1.0rem;"></button>';
                    } else {
                        html += '<button type="button" style="visibility:hidden" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr01_1" aria-expanded="false" aria-controls="mbr01"><img class="open_ic" src="' + CDN_HTTP + '/img/ic_open.png" style="width:1.0rem;"></button>';
                    }

                    html += '</div></div>';

                    if (list_sst_a.length > 0) {
                        html += '<div id="mbr01_1" class="collapse" aria-labelledby="mbr01_1" aria-labelledby="mbr_hd01_1" data-parent="#mbr_wr">' +
                            '<ul class="pt-4 pb-3">';

                        list_sst_a.forEach(function(schedule, index) {
                            var current_date = new Date();
                            var point_status = '';
                            if (schedule.sst_all_day == 'Y') {
                                point_status = 'point_ing';
                            } else if (current_date >= new Date(schedule.sst_edate)) {
                                point_status = 'point_done';
                            } else if (current_date >= new Date(schedule.sst_sdate) && current_date <= new Date(schedule.sst_edate)) {
                                point_status = 'point_ing';
                            } else {
                                point_status = 'point_gonna';
                            }

                            var arr_grant = <?= json_encode($arr_grant) ?>;
                            var grantNumbers = schedule.sst_update_chk.split(',');
                            var grantStrings = grantNumbers.map(function(number) {
                                return arr_grant[number];
                            });
                            var grant = grantNumbers.includes('1') && grantNumbers.includes('2') && grantNumbers.includes('3') ? '전체' : grantStrings.join(', ');

                            html += '<li class="py-2">' +
                                '<a href="./schedule_form?sst_idx=' + schedule.sst_idx + '" class="d-flex align-items-center justify-content-between">' +
                                '<div class="d-flex align-items-center">' +
                                '<div class="task ' + point_status + '">' +
                                '<span class="point_inner">' +
                                '<span class="point_txt">' + (index + 1) + '</span>' +
                                '</span>' +
                                '</div>' +
                                '<div class="mx-3">' +
                                '<p class="fs_13 fw_700 text_dynamic line_h1_3 line1_text">' + schedule.sst_title + '</p>' +
                                '<p class="fs_10 fw_300 text_gray line_h1_3"><span>' + "<?= $translations['txt_edit_rights'] ?>" + ' : </span> ' + grant + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<p><i class="xi-angle-right-min text_light_gray fs_13"></i></p>' +
                                '</a>' +
                                '</li>';
                        });

                        html += '</ul></div>';
                    }

                    html += '</li></ul></li></ul></div>';

                    // $('.user_grplist').replaceWith(html);
                    return html;
                }

                // 그룹 일정 HTML 생성 함수
                function updateGroupSchedules(data) {
                    var html = '';

                    if (data.user_groups.owner_count > 0 || data.user_groups.leader_count > 0) {
                        data.group_list.forEach(function(group) {
                            var member_cnt_t = data.list_sgdt.length;

                            html += '<div class="grp_list">' +
                                '<div class="grp_tit">' +
                                '<p class="fs_17 fw_700 line_h1_3 line1_text text_dynamic">' + group.sgt_title + '</p>' +
                                '</div>' +
                                '<ul class="mbr_wr_ul">';

                            if (member_cnt_t <= 1) {
                                html += '<li class="schdl_list">' +
                                    '<button type="button" class="btn w-100 h-auto fs_13 fc_navy schdl_btn" onclick="share_link_modal(\'' + group.sgt_idx + '\')"><i class="xi-plus-min mr-2"></i>' + translations['txt_invite_group_members'] + '</button>' +
                                    '</li>';
                            } else {
                                if (data.list_sgdt.data) {
                                    data.list_sgdt.data.forEach(function(member, key) {
                                        if (member.sgdt_owner_leader_chk_t != "<?= $translations['txt_owner'] ?>") {
                                            var cnt = data.list_sgdt.length;
                                            var list_sst_a = data.list_sst_a.filter(function(schedule) {
                                                return schedule.sgdt_idx != data.sgdt_idx;
                                            });

                                            html += '<li class="schdl_list">' +
                                                '<ul>' +
                                                '<li id="mbr_hd02_' + key + '" class="mbr_hd">' +
                                                '<div class="d-flex justify-content-between">' +
                                                '<div class="d-flex align-items-center flex-auto">' +
                                                '<a href="#" class="d-flex align-items-center flex-fill">' +
                                                '<div class="prd_img flex-shrink-0 mr_12">' +
                                                '<div class="rect_square rounded_14">' +
                                                '<img src="' + member.mt_file1_url + '" onerror="this.src=\'' + "<?= $ct_no_profile_img_url ?>" + '\'" alt="이미지" />' +
                                                '</div>' +
                                                '</div>' +
                                                '<p class="fs_14 fw_500 text_dynamic mr-2">' + (member.mt_nickname ? member.mt_nickname : member.mt_name) + '</p>' +
                                                '</a>' +
                                                '</div>' +
                                                '<div class="d-flex align-items-center flex-shrink-0">';

                                            if (data.user_groups.owner_count > 0 || data.user_groups.leader_count > 0) {
                                                if (member.sgdt_owner_leader_chk_t != "<?= $translations['txt_owner'] ?>") {
                                                    html += '<a href="./schedule_form?sdate=' + $('#event_start_date').val() + '&sgdt_idx=' + member.sgdt_idx + '" class="fs_13 fc_navy"><i class="xi-plus-min"></i>' + "<?= $translations['txt_add_schedule'] ?>" + '</a>';
                                                }
                                            }

                                            if (list_sst_a.length > 0 && list_sst_a.some(schedule => schedule.sgdt_idx === member.sgdt_idx)) {
                                                html += '<button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr02_' + key + '" aria-expanded="false" aria-controls="mbr02_' + key + '"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>';
                                            } else {
                                                html += '<button type="button" style="visibility:hidden" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr02_' + key + '" aria-expanded="false" aria-controls="mbr02_' + key + '"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>';
                                            }

                                            html += '</div></div>';

                                            if (list_sst_a.length > 0) {
                                                html += '<div id="mbr02_' + key + '" class="collapse" aria-labelledby="mbr02_' + key + '" aria-labelledby="mbr_hd02_' + key + '" data-parent="#mbr_wr">' +
                                                    '<ul class="pt-4 pb-3">';

                                                if (list_sst_a && list_sst_a.some(schedule => schedule.sgdt_idx === member.sgdt_idx)) {
                                                    list_sst_a.forEach(function(schedule, count) {
                                                        var current_date = new Date();
                                                        var point_status = '';
                                                        if (schedule.sst_all_day == 'Y') {
                                                            point_status = 'point_ing';
                                                        } else if (current_date >= new Date(schedule.sst_edate)) {
                                                            point_status = 'point_done';
                                                        } else if (current_date >= new Date(schedule.sst_sdate) && current_date <= new Date(schedule.sst_edate)) {
                                                            point_status = 'point_ing';
                                                        } else {
                                                            point_status = 'point_gonna';
                                                        }

                                                        // 수정권한 1:오너 2:리더 3:그룹원
                                                        var arr_grant = <?= json_encode($arr_grant) ?>;
                                                        var grantNumbers = schedule.sst_update_chk.split(',');
                                                        var grantStrings = grantNumbers.map(function(number) {
                                                            return arr_grant[number];
                                                        });
                                                        var grant = grantNumbers.includes('1') && grantNumbers.includes('2') && grantNumbers.includes('3') ? '<?= $translations['txt_all'] ?>' : grantStrings.join(', ');

                                                        html += '<li class="py-2">' +
                                                            '<a href="./schedule_form?sst_idx=' + schedule.sst_idx + '" class="d-flex align-items-center justify-content-between">' +
                                                            '<div class="d-flex align-items-center">' +
                                                            '<div class="task ' + point_status + '">' +
                                                            '<span class="point_inner">' +
                                                            '<span class="point_txt">' + (count + 1) + '</span>' +
                                                            '</span>' +
                                                            '</div>' +
                                                            '<div class="mx-3">' +
                                                            '<p class="fs_13 fw_700 text_dynamic line_h1_3 line1_text">' + schedule.sst_title + '</p>' +
                                                            '<p class="fs_10 fw_300 text_gray line_h1_3"><span>' + "<?= $translations['txt_edit_rights'] ?>" + ' : </span> ' + grant + '</p>' +
                                                            '</div>' +
                                                            '</div>' +
                                                            '<p><i class="xi-angle-right-min text_light_gray fs_13"></i></p>' +
                                                            '</a>' +
                                                            '</li>';
                                                    });
                                                }

                                                html += '</ul></div>';
                                            }

                                            html += '</li></ul></li>';
                                        }
                                    });
                                }
                            }

                            html += '</ul></div>';
                        });
                    }

                    // $('#mbr_wr').html(html);
                    return html;
                }

                // 날짜 변경 이벤트 핸들러
                $('input[name="event_start_date"]').on('change', function() {
                    loadScheduleData($(this).val());
                });

                function f_day_click(sdate) {
                    if (typeof(history.pushState) != "undefined") {
                        var state = '';
                        var title = '';
                        var url = './schedule?sdate=' + sdate;
                        history.pushState(state, title, url);

                        // f_cld_wrap();

                        $('#event_start_date').val(sdate);
                        $('#schedule-title').text(get_date_t(sdate));
                        $('.c_id').removeClass('active');
                        $('#calendar_' + sdate).addClass('active');
                        setTimeout(() => {
                            f_get_box_list();
                        }, 100);
                    } else {
                        location.href = url;
                    }
                }

                // 바텀시트 업다운
                $('.down_wrap').click(function() {
                    f_cld_wrap();
                });

                function f_cld_wrap() {
                    var cldDateWrap = $('.sch_cld_wrap .cld_date_wrap');

                    // .on 클래스를 토글
                    cldDateWrap.toggleClass('on');

                    // .on 클래스의 유무에 따라 이미지 파일 이름 변경
                    var imgSrc = cldDateWrap.hasClass('on') ? 'btn_tl_arrow.png' : 'btn_bl_arrow.png';
                    $('.down_wrap img.top_down').attr('src', '<?= CDN_HTTP ?>/img/' + imgSrc);

                    if (cldDateWrap.hasClass('on')) {
                        // $('.sch_wrap').css('padding-top', 'auto');
                        $('.sch_wrap').css('padding-top', '38.7rem');
                        $('#week_calendar').val('N');
                    } else {
                        // $('.sch_wrap').css('padding-top', '17rem');
                        $('.sch_wrap').css('padding-top', '16.8rem');
                        $('#week_calendar').val('Y');
                    }

                    var nmy = $('#nmy').val();
                    var csm = $('#csdate').val().substr(0, 7);

                    if (nmy == csm) {
                        var tty = 'today';
                    } else {
                        var tty = '';
                    }

                    f_calendar_init(tty);
                }

                function f_go_schedule_form() {
                    var sdate = $('#event_start_date').val();

                    location.href = './schedule_form?sdate=' + sdate + '&mt_idx=<?= $_SESSION['_mt_idx'] ?>';
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
                                jalert('초대코드를 다 사용하였습니다.');
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
            </script>
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