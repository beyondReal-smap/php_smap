<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = "일정 입력";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
}

if ($_GET['sdate']) {
    $sdate = $_GET['sdate'];
} else {
    $sdate = date('Y-m-d');
}
$tt = strtotime($sdate);

$numDay = date('d', $tt);
$numMonth = date('m', $tt);
$numMonth2 = date('n', $tt);
$numYear = date('Y', $tt);
$prevMonth = date('Y-m-01', strtotime($sdate . " -" . $dayOfWeek . "days"));
$nextMonth = date('Y-m-01', strtotime($sdate . " +" . $dayOfWeek . "days"));
$calendar_date_title = $numYear . "년 " . $numMonth2 . "월";
$today_st = DateType($sdate, 20);
$today_et = DateType($sdate, 20);

//시작시간, 마감시간을 한시간뒤로 셋팅
$now_t = date("Y-m-d H:i:s");
$shour = date("H", strtotime($now_t . " +1 hours"));
$ehour = date("H", strtotime($now_t . " +2 hours"));

function get_pad($v)
{
    return $v > 9 ? $v : "0" . $v;
}

function get_time_format($d)
{
    $hour = date("G", strtotime($d));
    $min  = date("i", strtotime($d));

    if ($hour > 12) {
        $hour = $hour - 12;
        $time_now_t = "오후 " . get_pad($hour) . ":" . $min;
    } else {
        if ($hour == 12 && $min == '00') {
            $time_now_t = "정오 " . get_pad($hour) . ":" . $min;
        } elseif ($hour == 0 && $min == '00') {
            $time_now_t = "자정 " . get_pad($hour) . ":" . $min;
        } else {
            $time_now_t = "오전 " . get_pad($hour) . ":" . $min;
        }
    }

    return $time_now_t;
}

if ($_GET['sst_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sst_idx', $_GET['sst_idx']);
    $DB->where('sst_show', 'Y');
    $row_sst = $DB->getone('smap_schedule_t');

    if ($row_sst['sst_sdate']) {
        $arr_sdate_t = get_date_f($row_sst['sst_sdate']);
        $today_st = DateType($row_sst['sst_sdate'], 20);
        $time_now_st = get_time_format($row_sst['sst_sdate']);
        $ex_sdate_t = explode(' ', $row_sst['sst_sdate']);
    }
    if ($row_sst['sst_edate']) {
        $arr_edate_t = get_date_f($row_sst['sst_edate']);
        $today_et = DateType($row_sst['sst_edate'], 20);
        $time_now_et = get_time_format($row_sst['sst_edate']);
        $ex_edate_t = explode(' ', $row_sst['sst_edate']);
    }

    if ($row_sst['sst_repeat_json']) {
        $sst_repeat_json_t = json_decode($row_sst['sst_repeat_json'], true);
    }
} else {
    $ex_sdate_t = explode(' ', date("Y-m-d " . $shour . ":00"));
    $ex_edate_t = explode(' ', date("Y-m-d " . $ehour . ":00"));

    $row_sst['sst_sdate'] = date("Y-m-d " . $shour . ":00");
    $row_sst['sst_edate'] = date("Y-m-d " . $ehour . ":00");
}

$time_now_st = get_time_format($row_sst['sst_sdate']);
$time_now_et = get_time_format($row_sst['sst_edate']);
?>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/anypicker@latest/dist/anypicker-all.min.css" />
<!-- <script type="text/javascript" src="//cdn.jsdelivr.net/npm/anypicker@latest/dist/anypicker.min.js"></script> -->
<script type="text/javascript" src="<?= CDN_HTTP ?>/lib/anypicker/anypicker.js?v=<?= $v_txt ?>"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/anypicker@latest/dist/i18n/anypicker-i18n.js"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>

<script>
    (function($) {
        $.AnyPicker.i18n["ko"] = $.extend($.AnyPicker.i18n["ko"], {

            // Common

            headerTitle: "선택",
            setButton: "설정",
            clearButton: "지우기",
            nowButton: "지금",
            cancelButton: "취소",
            dateSwitch: "날짜",
            timeSwitch: "시간",

            // DateTime

            veryShortDays: "Su_Mo_Tu_We_Th_Fr_Sa".split("_"),
            shortDays: "Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),
            fullDays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
            shortMonths: "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),
            fullMonths: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
            numbers: "0_1_2_3_4_5_6_7_8_9".split("_"),
            meridiem: {
                a: ["a", "p"],
                aa: ["오전", "오후"],
                A: ["A", "P"],
                AA: ["오전", "오후"]
            },
            componentLabels: {
                date: "Date",
                day: "Day",
                month: "Month",
                year: "Year",
                hours: "Hours",
                minutes: "Minutes",
                seconds: "Seconds",
                meridiem: "Meridiem"
            }

        });

    })(jQuery);
</script>
<style>
    .cld_head_wr {
        padding: 0;
    }

    .btn_active {
        color: #fff !important;
        background-color: #343a40 !important;
        border-color: #343a40 !important;
    }

    .btn-light:focus,
    .btn-light.focus,
    .btn-light:hover {
        color: #212529;
        background-color: #f8f9fa;
        border-color: #f8f9fa;
    }

    .ap-overlay,
    .ap-bg,
    .ap-cont,
    .ap-content {
        z-index: 100;
    }

    .ap-cont {
        width: 100% !important;
    }

    .ap-cont,
    .ap-content,
    .ap-content-middle,
    .ap-component-section,
    .ap-component,
    .ap-component-cont {
        background: transparent !important;
    }

    .ap-layout-inline .ap-cont {
        border: 0 !important;
    }

    .ap-layout-relative,
    .ap-layout-inline {
        position: absolute !important;
        max-width: 53.2rem !important;
    }

    .ap-bg {
        display: block !important;
        font-family: "SUITE Variable", Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", sans-serif !important;
        font-size: 18px !important;
    }

    .ap-cont {
        max-width: 80% !important;
    }

    .ap-component-selector {
        background: transparent !important;

        border-top: 1px solid #ddd !important;
        border-bottom: 1px solid #ddd !important;
    }

    .ap-component-gradient {
        background: transparent !important;
    }

    .ap-overlay {
        display: block !important;
        height: 10px !important;
    }

    .ap-show {
        animation: none !important;
        -webkit-animation: none !important;
        -moz-animation: none !important;
        -o-animation: none !important;
        -ms-animation: none !important;
    }

    .btn:focus,
    .btn:active {
        outline: none !important;
        box-shadow: none;
    }


    .picker {
        position: relative;
        overflow: hidden;
        margin: 1rem auto;
        padding: 0 30px;
        color: #252525;
        /* border: 1px solid red; */
        width: 23rem;
    }

    .swiper-container {
        width: 80px;
        height: 200px;
        float: left;
    }

    .swiper-slide {
        text-align: center;
        font-size: 2rem;
        /* Center slide text vertically */
        display: flex;
        justify-content: center;
        align-items: center;
        user-select: none;
        opacity: 0.25;
        transition: opacity 0.3s ease;
        cursor: default;
        font-weight: bold;
        -webkit-tap-highlight-color: transparent;
        /* border: 1px solid red; */
        height: 70px !important;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .swiper-slide-prev,
    .swiper-slide-next {
        cursor: pointer;
    }

    .swiper-slide-active {
        opacity: 1;
    }

    .vizor {
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        height: 50px;
        position: absolute;
        top: 50%;
        left: 1rem;
        right: 1rem;
        transform: translateY(-50%);
        font-size: 2rem;
        line-height: 60px;
    }

    .vizor:before,
    .vizor:after {
        content: ':';
        display: inline-block;
        line-height: inherit;
        height: 100%;
        position: absolute;
        top: -5px;
        transform: translateX(-50%);
        left: 100px;
    }

    .daterangepicker {
        top: 330px !important;
    }

    .drp-selected {
        display: none !important;
    }
</style>
<?php
$debug_t = 'hidden';
?>
<div class="container sub_pg">
    <div class="mt_22">
        <form method="post" name="frm_form" id="frm_form" action="./schedule_update" target="hidden_ifrm" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="schedule_form" />
            <input type="hidden" name="sst_idx" id="sst_idx" value="<?= $row_sst['sst_idx'] ?>" />
            <input type="<?= $debug_t ?>" name="pick_date" id="pick_date" value="" />
            <input type="<?= $debug_t ?>" name="pick_time" id="pick_time" value="" />
            <input type="hidden" name="week_calendar" id="week_calendar" value="N" />
            <input type="hidden" name="csdate" id="csdate" value="<?= $sdate ?>" />
            <input type="<?= $debug_t ?>" name="pick_sdate" id="pick_sdate" value="<?= $ex_sdate_t[0] ?>" />
            <input type="<?= $debug_t ?>" name="pick_edate" id="pick_edate" value="<?= $ex_edate_t[0] ?>" />
            <input type="<?= $debug_t ?>" name="pick_stime" id="pick_stime" value="<?= $ex_sdate_t[1] ?>" />
            <input type="<?= $debug_t ?>" name="pick_etime" id="pick_etime" value="<?= $ex_edate_t[1] ?>" />
            <input type="<?= $debug_t ?>" name="sst_sdate" id="sst_sdate" value="<?= $row_sst['sst_sdate'] ?>">
            <input type="<?= $debug_t ?>" name="sst_edate" id="sst_edate" value="<?= $row_sst['sst_edate'] ?>">
            <input type="hidden" name="swipe_init" id="swipe_init" value="N">
            <div class="ip_wr">
                <input type="text" class="form-custom" name="sst_title" id="sst_title" value="<?= $row_sst['sst_title'] ?>" maxlength="30" data-length-id="sst_title_cnt" oninput="maxLengthCheck(this)" placeholder="일정 내용을 입력해주세요.">
                <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_title_cnt">0</span>/15)</p>
            </div>

            <div class="line_ip_box border rounded-lg px_20 py_20 mt_20">
                <div class="line_ip">
                    <div class="row justify-content-between align-items-center">
                        <h5 class="col col-auto">하루 종일</h5>
                        <div class="col">
                            <div class="custom-switch ml-auto">
                                <input type="checkbox" class="custom-control-input" name="sst_all_day" id="sst_all_day" value="Y" <?php if ($row_sst['sst_all_day'] == 'Y') {
                                                                                                                                        echo " checked";
                                                                                                                                    } ?> onchange="f_all_day();" />
                                <label class="custom-control-label" for="sst_all_day"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
                <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

                <div class="line_ip mt-3" id="cal_time_box">
                    <div class="row align-items-center">
                        <div class="col text-center">
                            <button type="button" id="btn_sdate" class="btn btn-light btn-c fc_808080 "><span id="sdate_txt"><?= $today_st ?></span></button>
                            <button type="button" id="btn_stime" class="btn btn-light btn-c fc_808080  cal_time_box"><span id="stime_txt"><?= $time_now_st ?></span></button>
                        </div>
                        <div class="col col-auto fc_808080"><i class="xi-long-arrow-right"></i></div>
                        <div class="col text-center">
                            <button type="button" id="btn_edate" class="btn btn-light btn-c fc_808080 "><span id="edate_txt"><?= $today_et ?></span></button>
                            <button type="button" id="btn_etime" class="btn btn-light btn-c fc_808080  cal_time_box"><span id="etime_txt"><?= $time_now_et ?></span></button>
                        </div>
                    </div>
                </div>

                <input type="text" name="datetimes" id="datetimes" class="form-control d-none-temp" />


                <div class="cld_head_wr d-none-temp" id="schedule_calandar_box_header">
                    <div class="row">
                        <div class="col-3 text-left">
                            <button type="button" class="btn ml-2" onclick="f_calendar_init('prev');"><i class="xi-angle-left-min fs_22"></i></button>
                        </div>
                        <div class="col-6 text-center">
                            <div class="mt_13">
                                <div class="sel_month d-inline-flex">
                                    <a href="javascript:;" onclick="f_calendar_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="월 선택 아이콘" style="width:1.6rem; "></a>
                                    <p class="fs_15 fw_600" id="calendar_date_title"><?= $calendar_date_title ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 text-right">
                            <button type="button" class="btn mr-3" onclick="f_calendar_init('next');"><i class="xi-angle-right-min fs_22"></i></button>
                        </div>
                    </div>
                    <div class="cld_head fs_12">
                        <ul>
                            <li class="sun">일</li>
                            <li>월</li>
                            <li>화</li>
                            <li>수</li>
                            <li>목</li>
                            <li>금</li>
                            <li class="sat">토</li>
                        </ul>
                    </div>
                </div>
                <div id="schedule_calandar_box" class="cld_date_wrap"></div>

                <script>
                    var daterangepicker_defaults = {
                        "linkedCalendars": false,
                        "timePicker": true,
                        "timePicker24Hour": true,
                        "locale": {
                            "format": "YYYY-MM-DD hh:mm",
                            "separator": " / ",
                            "applyLabel": "적용",
                            "cancelLabel": "닫기",
                            "fromLabel": "From",
                            "toLabel": "To",
                            "customRangeLabel": "Custom",
                            "weekLabel": "W",
                            "daysOfWeek": [
                                "일",
                                "월",
                                "화",
                                "수",
                                "목",
                                "금",
                                "토"
                            ],
                            "monthNames": [
                                "1월",
                                "2월",
                                "3월",
                                "4월",
                                "5월",
                                "6월",
                                "7월",
                                "8월",
                                "9월",
                                "10월",
                                "11월",
                                "12월"
                            ],
                            "firstDay": 1
                        },
                        "timePickerIncrement": 5,
                        "opens": "center",
                        "drops": "auto"
                    };

                    var setdate_t = {
                        "startDate": $('#sst_sdate').val(),
                        "endDate": $('#sst_edate').val()
                    };

                    $('#datetimes').daterangepicker(Object.assign({}, daterangepicker_defaults, setdate_t));

                    $('#datetimes').on('show.daterangepicker', function(ev, picker) {
                        var setdate_t = {
                            "startDate": $('#sst_sdate').val(),
                            "endDate": $('#sst_edate').val()
                        };

                        var dddd = $('#datetimes').daterangepicker(Object.assign({}, daterangepicker_defaults, setdate_t), function(start, end, label) {
                            console.log('New date range selected: ' + start.format('YYYY-MM-DD hh:mm') + ' to ' + end.format('YYYY-MM-DD hh:mm') + ' (predefined range: ' + label + ')');

                            var std = start.format('YYYY-MM-DD');
                            var etd = end.format('YYYY-MM-DD');

                            var stdt = start.format('YYYY-MM-DD HH:mm');
                            var etdt = end.format('YYYY-MM-DD HH:mm');

                            //날짜
                            var dwts = dateFormat_week(std);
                            var dwte = dateFormat_week(etd);

                            $('#sdate_txt').html(dwts);
                            $('#edate_txt').html(dwte);

                            //시간
                            var csday = new Date(stdt);
                            var ceday = new Date(etdt);

                            var shh = csday.getHours();
                            var smm = csday.getMinutes();

                            var srtn = get_time_format(shh, smm);

                            $('#stime_txt').html(srtn);

                            var ehh = ceday.getHours();
                            var emm = ceday.getMinutes();

                            var ertn = get_time_format(ehh, emm);

                            $('#etime_txt').html(ertn);

                            $('#sst_sdate').val(stdt);
                            $('#sst_edate').val(etdt);
                        });
                    });
                </script>

                <div class="picker d-none-temp" id="stime_picker">
                    <div class="vizor"></div>
                    <div class="swiper-container stime_hours">
                        <div class="swiper-wrapper">
                            <?php
                            $w = 0;
                            for ($q = 0; $q < 24; $q++) {
                                if ($w < 10) {
                                    $w = "0" . $w;
                                }
                            ?>
                                <div class="swiper-slide" data-hh="<?= $w ?>"><?= $w ?></div>
                            <?php
                                $w++;
                            }
                            ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <div class="swiper-container stime_minutes">
                        <div class="swiper-wrapper">
                            <?php
                            $w = 0;
                            for ($q = 0; $q < 12; $q++) {
                                if ($w < 10) {
                                    $w = "0" . $w;
                                }
                            ?>
                                <div class="swiper-slide" data-mm="<?= $w ?>"><?= $w ?></div>
                            <?php
                                $w += 5;
                            }
                            ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                <div class="picker d-none-temp" id="etime_picker">
                    <div class="vizor"></div>
                    <div class="swiper-container etime_hours">
                        <div class="swiper-wrapper">
                            <?php
                            $w = 0;
                            for ($q = 0; $q < 24; $q++) {
                                if ($w < 10) {
                                    $w = "0" . $w;
                                }
                            ?>
                                <div class="swiper-slide" data-hh="<?= $w ?>"><?= $w ?></div>
                            <?php
                                $w++;
                            }
                            ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <div class="swiper-container etime_minutes">
                        <div class="swiper-wrapper">
                            <?php
                            $w = 0;
                            for ($q = 0; $q < 12; $q++) {
                                if ($w < 10) {
                                    $w = "0" . $w;
                                }
                            ?>
                                <div class="swiper-slide" data-mm="<?= $w ?>"><?= $w ?></div>
                            <?php
                                $w += 5;
                            }
                            ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {

                    });


                    function get_time_format(hh_data, mm_data) {
                        if (hh_data > 12) {
                            var hh_t = '오후';
                            hh_data = hh_data - 12;
                            hh_data = get_pad(hh_data);
                        } else {
                            if (hh_data == 12 && mm_data == 0) {
                                var hh_t = '정오';
                            } else if (hh_data == 0 && mm_data == 0) {
                                var hh_t = '자정';
                            } else {
                                var hh_t = '오전';
                            }
                        }

                        // return hh_t + ' ' + get_pad(hh_data) + ':' + mm_data;
                        return hh_t + ' ' + hh_data + ':' + mm_data;
                    }

                    function f_all_day() {
                        var all_day_chk = $('#sst_all_day').prop("checked");

                        if (all_day_chk) {
                            $('.cal_time_box').hide();
                            btn_class_active();
                            $("#schedule_calandar_box").html('');
                            $('#schedule_calandar_box_header').hide();
                        } else {
                            $('.cal_time_box').show();
                        }
                    }

                    const btn_sdate_b = document.getElementById("btn_sdate");
                    const btn_edate_b = document.getElementById("btn_edate");

                    btn_sdate_b.addEventListener('click', (e) => {
                        this.blur();
                        e.preventDefault();
                        btn_class_active();
                        setTimeout(() => {
                            $('#btn_sdate').addClass('btn_active');
                        }, 100);
                        setTimeout(() => {
                            f_open_cal('stime');
                        }, 100);

                        // ttcc();
                    });
                    btn_edate_b.addEventListener('click', (e) => {
                        this.blur();
                        e.preventDefault();
                        btn_class_active();
                        setTimeout(() => {
                            $('#btn_edate').addClass('btn_active');
                        }, 100);
                        setTimeout(() => {
                            f_open_cal('etime');
                        }, 100);

                        // ttcc();
                    });

                    const btn_stime_b = document.getElementById("btn_stime");
                    const btn_etime_b = document.getElementById("btn_etime");

                    btn_stime_b.addEventListener('click', (e) => {
                        this.blur();
                        e.preventDefault();
                        btn_class_active();
                        setTimeout(() => {
                            $('#btn_stime').addClass('btn_active');
                        }, 100);
                        f_sopen_time('stime');

                        // ttcc();
                    });
                    btn_etime_b.addEventListener('click', (e) => {
                        this.blur();
                        e.preventDefault();
                        btn_class_active();
                        setTimeout(() => {
                            $('#btn_etime').addClass('btn_active');
                        }, 100);
                        f_eopen_time('etime');

                        // ttcc();
                    });

                    function body_scroll_lock() {
                        document.getElementsByTagName('body')[0].style.overflow = 'hidden';
                    }

                    function body_scroll_visible() {
                        document.getElementsByTagName('body')[0].style.overflow = 'visible';
                    }

                    function ttcc() {
                        // $('#pick_time').val('etime');
                        $('#datetimes').trigger("click");
                        $('#datetimes').trigger("click");
                    }

                    function chg_ddt(d) {
                        var cday = new Date(d);

                        return cday.getDate() + "/" + (cday.getMonth() + 1) + "/" + cday.getFullYear();
                    }

                    function f_day_click(dct) {
                        $('.c_id').removeClass('active');
                        setTimeout(() => {
                            $('#calendar_' + dct).addClass('active');
                        }, 100);
                        var pdt = $('#pick_time').val();

                        var dwt = dateFormat_week(dct);

                        if (pdt == 'stime') {
                            // $('#sdate_txt').html(dwt);
                            // $('#edate_txt').html(dwt);
                            $('#pick_sdate').val(dct);
                            // $('#pick_edate').val(dct);
                        } else {
                            $('#pick_edate').val(dct);

                            // var date1 = new Date($('#pick_sdate').val());
                            // var date2 = new Date(dct);
                            // var dctg = chg_ddt(dct);

                            // if (date1 <= date2) {
                            //     $('#pick_edate').val(dct);
                            //     $('#edate_txt').html(dwt);
                            // } else {
                            //     $('#pick_sdate').val(dct);
                            //     $('#pick_edate').val(dct);
                            //     $('#sdate_txt').html(dwt);
                            //     $('#edate_txt').html(dwt);
                            // }
                        }

                        setTimeout(() => {
                            datetime_chk();
                        }, 100);
                    }

                    function dateFormat_week(d) {
                        var today = new Date();
                        var cday = new Date(d);

                        var y = cday.getFullYear();
                        var y2 = today.getFullYear();
                        var m = cday.getMonth() + 1;
                        var d = cday.getDate();
                        var w = "일월화수목금토".charAt(cday.getUTCDay());

                        if (y == y2) {
                            var rtn = m + "월 " + d + "일 (" + w + ")";
                        } else {
                            var rtn = y + "년 " + m + "월 " + d + "일 (" + w + ")";
                        }

                        return rtn;
                    }

                    function btn_class_active() {
                        $('.btn-c').addClass('btn-light');
                        $('.btn-c').removeClass('btn-danger');
                        $('.btn-c').removeClass('active');
                        $('.btn-c').removeClass('btn_active');
                        $('#schedule_calandar_box_header').hide();
                        $("#schedule_calandar_box").html('');
                        $('.picker').hide();

                        // if (t == 'sdate') {
                        //     $('#btn_sdate').addClass('btn_active');
                        //     $('#btn_edate').removeClass('btn_active');
                        // } else {
                        //     $('#btn_sdate').removeClass('btn_active');
                        //     $('#btn_edate').addClass('btn_active');
                        // }
                    }

                    function f_open_cal(t) {
                        $('#pick_time').val(t);
                        $('#schedule_calandar_box_header').show();
                        f_calendar_init();
                    }

                    function Unix_timestamp(t) {
                        var date = new Date(t * 1000);
                        var year = date.getFullYear();
                        var month = "0" + (date.getMonth() + 1);
                        var day = "0" + date.getDate();
                        var hour = "0" + date.getHours();
                        var minute = "0" + date.getMinutes();
                        var second = "0" + date.getSeconds();
                        return year + "-" + month.substr(-2) + "-" + day.substr(-2) + " " + hour.substr(-2) + ":" + minute.substr(-2) + ":" + second.substr(-2);
                    }

                    function f_sopen_time(t) {
                        $('#pick_time').val(t);
                        $('#' + t + '_picker').show();

                        setTimeout(() => {
                            var dStartD, dEndD;

                            var defaults = {
                                pagination: {
                                    el: ".swiper-pagination",
                                    clickable: false
                                },
                                slidesPerView: "auto",
                                freeMode: {
                                    enabled: true,
                                    sticky: true,
                                    momentumBounce: true,
                                    momentum: true,
                                    minimumVelocity: 0.05,
                                    momentumBounceRatio: 0.05
                                },
                                loop: true,
                                direction: "vertical",
                                centeredSlides: true,
                                centeredSlidesBounds: true,
                                speed: 600,
                                breakpointsBase: 'container ',
                                init: false,
                            };

                            dStartD = new Date($('#sst_sdate').val());
                            dEndD = new Date($('#sst_edate').val());

                            // console.log("dStartD " + dStartD);

                            var shours = new Swiper(
                                ".swiper-container.stime_hours",
                                Object.assign({}, defaults)
                            );

                            var sminutes = new Swiper(
                                ".swiper-container.stime_minutes",
                                Object.assign({}, defaults)
                            );

                            var sdhh = dStartD.getHours();
                            var sdmm = dStartD.getMinutes();

                            shours.on("init", function() {
                                var shours_initialSlide;

                                $.each(shours.slides, function(index, value) {
                                    if (sdhh == value.dataset.hh) {
                                        shours_initialSlide = value.dataset.swiperSlideIndex;
                                    }
                                });

                                shours.slideToLoop(shours_initialSlide, 500, $('#swipe_init').val('Y'));
                            });

                            shours.init();

                            sminutes.on("init", function() {
                                var smin_initialSlide;

                                $.each(sminutes.slides, function(index, value) {
                                    if (sdmm == value.dataset.mm) {
                                        smin_initialSlide = value.dataset.swiperSlideIndex;
                                    }
                                });
                                sminutes.slideToLoop(smin_initialSlide, 500, $('#swipe_init').val('Y'));
                            });

                            sminutes.init();

                            shours.on("transitionEnd", function() {
                                setTimeout(() => {
                                    var hh_data1 = $('.stime_hours .swiper-slide-active').data("hh");
                                    var mm_data1 = $('.stime_minutes .swiper-slide-active').data("mm");

                                    var rtn = sget_hh_mm_txt(hh_data1, mm_data1);
                                }, 0);
                            });

                            sminutes.on("transitionEnd", function() {
                                setTimeout(() => {
                                    var hh_data2 = $('.stime_hours .swiper-slide-active').data("hh");
                                    var mm_data2 = $('.stime_minutes .swiper-slide-active').data("mm");

                                    var rtn = sget_hh_mm_txt(hh_data2, mm_data2);
                                }, 0);
                            });

                            shours.on("touchStart", function() {
                                // console.log('touchStart');
                                body_scroll_lock()
                            });

                            shours.on("touchEnd", function() {
                                // console.log('touchStart');
                                body_scroll_visible()
                            });

                            sminutes.on("touchStart", function() {
                                // console.log('touchStart');
                                body_scroll_lock()
                            });

                            sminutes.on("touchEnd", function() {
                                // console.log('touchStart');
                                body_scroll_visible()
                            });
                        }, 0);
                    }

                    function f_eopen_time(t) {
                        $('#pick_time').val(t);
                        $('#' + t + '_picker').show();

                        setTimeout(() => {
                            var dStartD, dEndD;

                            var defaults = {
                                pagination: {
                                    el: ".swiper-pagination",
                                    clickable: false
                                },
                                slidesPerView: "auto",
                                freeMode: {
                                    enabled: true,
                                    sticky: true,
                                    momentumBounce: true,
                                    momentum: true,
                                    minimumVelocity: 0.05,
                                    momentumBounceRatio: 0.05
                                },
                                loop: true,
                                direction: "vertical",
                                centeredSlides: true,
                                centeredSlidesBounds: true,
                                speed: 600,
                                breakpointsBase: 'container ',
                                init: false,
                            };

                            dStartD = new Date($('#sst_sdate').val());
                            dEndD = new Date($('#sst_edate').val());

                            // console.log("dStartD " + dStartD);

                            var ehours = new Swiper(
                                ".swiper-container.etime_hours",
                                Object.assign({}, defaults)
                            );

                            var eminutes = new Swiper(
                                ".swiper-container.etime_minutes",
                                Object.assign({}, defaults)
                            );

                            var edhh = dEndD.getHours();
                            var edmm = dEndD.getMinutes();

                            ehours.on("init", function() {
                                var ehours_initialSlide;

                                $.each(ehours.slides, function(index, value) {
                                    if (edhh == value.dataset.hh) {
                                        ehours_initialSlide = value.dataset.swiperSlideIndex;
                                    }
                                });

                                ehours.slideToLoop(ehours_initialSlide, 500, $('#swipe_init').val('Y'));
                            });

                            ehours.init();

                            eminutes.on("init", function() {
                                var emin_initialSlide;

                                $.each(eminutes.slides, function(index, value) {
                                    if (edmm == value.dataset.mm) {
                                        emin_initialSlide = value.dataset.swiperSlideIndex;
                                    }
                                });

                                eminutes.slideToLoop(emin_initialSlide, 500, $('#swipe_init').val('Y'));
                            });

                            eminutes.init();

                            ehours.on("transitionEnd", function() {
                                setTimeout(() => {
                                    var hh_data3 = $('.etime_hours .swiper-slide-active').data("hh");
                                    var mm_data3 = $('.etime_minutes .swiper-slide-active').data("mm");

                                    var rtn = eget_hh_mm_txt(hh_data3, mm_data3);
                                }, 0);
                            });

                            eminutes.on("transitionEnd", function() {
                                setTimeout(() => {
                                    var hh_data4 = $('.etime_hours .swiper-slide-active').data("hh");
                                    var mm_data4 = $('.etime_minutes .swiper-slide-active').data("mm");

                                    var rtn = eget_hh_mm_txt(hh_data4, mm_data4);
                                }, 0);
                            });

                            ehours.on("touchStart", function() {
                                // console.log('touchStart');
                                body_scroll_lock()
                            });

                            ehours.on("touchEnd", function() {
                                // console.log('touchStart');
                                body_scroll_visible()
                            });

                            eminutes.on("touchStart", function() {
                                // console.log('touchStart');
                                body_scroll_lock()
                            });

                            eminutes.on("touchEnd", function() {
                                // console.log('touchStart');
                                body_scroll_visible()
                            });
                        }, 0);
                    }

                    // function f_open_time(t) {
                    //     $('#pick_time').val(t);
                    //     $('#' + t + '_picker').show();

                    //     setTimeout(() => {
                    //         var dStartD, dEndD;

                    //         var defaults = {
                    //             pagination: {
                    //                 el: ".swiper-pagination",
                    //                 clickable: false
                    //             },
                    //             slidesPerView: "auto",
                    //             freeMode: {
                    //                 enabled: true,
                    //                 sticky: true,
                    //                 momentumBounce: true,
                    //                 momentum: true
                    //             },
                    //             loop: true,
                    //             loopedSlides: 1,
                    //             loopAdditionalSlides: 1,
                    //             direction: "vertical",
                    //             centeredSlides: true,
                    //             speed: 1200,
                    //             breakpointsBase: 'container ',
                    //             init: false,
                    //         };

                    //         dStartD = new Date($('#sst_sdate').val());
                    //         dEndD = new Date($('#sst_edate').val());

                    //         console.log("dStartD " + dStartD);

                    //         var shours = new Swiper(
                    //             ".swiper-container.stime_hours",
                    //             Object.assign({}, defaults)
                    //         );

                    //         var sminutes = new Swiper(
                    //             ".swiper-container.stime_minutes",
                    //             Object.assign({}, defaults)
                    //         );

                    //         var sdhh = dStartD.getHours();
                    //         var sdmm = dStartD.getMinutes();

                    //         shours.on("init", function() {
                    //             var shours_initialSlide;

                    //             $.each(shours.slides, function(index, value) {
                    //                 if (sdhh == value.dataset.hh) {
                    //                     shours_initialSlide = value.dataset.swiperSlideIndex;
                    //                 }
                    //             });

                    //             shours.slideToLoop(shours_initialSlide, 500, $('#swipe_init').val('Y'));
                    //         });

                    //         shours.init();

                    //         sminutes.on("init", function() {
                    //             var smin_initialSlide;

                    //             $.each(sminutes.slides, function(index, value) {
                    //                 if (sdmm == value.dataset.mm) {
                    //                     smin_initialSlide = value.dataset.swiperSlideIndex;
                    //                 }
                    //             });

                    //             sminutes.slideToLoop(smin_initialSlide, 500, $('#swipe_init').val('Y'));
                    //         });

                    //         sminutes.init();

                    //         var ehours = new Swiper(
                    //             ".swiper-container.etime_hours",
                    //             Object.assign({}, defaults)
                    //         );

                    //         var eminutes = new Swiper(
                    //             ".swiper-container.etime_minutes",
                    //             Object.assign({}, defaults)
                    //         );

                    //         var edhh = dEndD.getHours();
                    //         var edmm = dEndD.getMinutes();

                    //         ehours.on("init", function() {
                    //             var ehours_initialSlide;

                    //             $.each(ehours.slides, function(index, value) {
                    //                 if (edhh == value.dataset.hh) {
                    //                     ehours_initialSlide = value.dataset.swiperSlideIndex;
                    //                 }
                    //             });

                    //             ehours.slideToLoop(ehours_initialSlide, 500, $('#swipe_init').val('Y'));
                    //         });

                    //         ehours.init();

                    //         eminutes.on("init", function() {
                    //             var emin_initialSlide;

                    //             $.each(eminutes.slides, function(index, value) {
                    //                 if (edmm == value.dataset.mm) {
                    //                     emin_initialSlide = value.dataset.swiperSlideIndex;
                    //                 }
                    //             });

                    //             eminutes.slideToLoop(emin_initialSlide, 500, $('#swipe_init').val('Y'));
                    //         });

                    //         eminutes.init();

                    //         shours.on("transitionEnd", function() {
                    //             setTimeout(() => {
                    //                 var hh_data1 = $('.stime_hours .swiper-slide-active').data("hh");
                    //                 var mm_data1 = $('.stime_minutes .swiper-slide-active').data("mm");

                    //                 var rtn = sget_hh_mm_txt(hh_data1, mm_data1);
                    //             }, 0);
                    //         });

                    //         sminutes.on("transitionEnd", function() {
                    //             setTimeout(() => {
                    //                 var hh_data2 = $('.stime_hours .swiper-slide-active').data("hh");
                    //                 var mm_data2 = $('.stime_minutes .swiper-slide-active').data("mm");

                    //                 var rtn = sget_hh_mm_txt(hh_data2, mm_data2);
                    //             }, 0);
                    //         });

                    //         ehours.on("transitionEnd", function() {
                    //             setTimeout(() => {
                    //                 var hh_data3 = $('.etime_hours .swiper-slide-active').data("hh");
                    //                 var mm_data3 = $('.etime_minutes .swiper-slide-active').data("mm");

                    //                 var rtn = eget_hh_mm_txt(hh_data3, mm_data3);
                    //             }, 0);
                    //         });

                    //         eminutes.on("transitionEnd", function() {
                    //             setTimeout(() => {
                    //                 var hh_data4 = $('.etime_hours .swiper-slide-active').data("hh");
                    //                 var mm_data4 = $('.etime_minutes .swiper-slide-active').data("mm");

                    //                 var rtn = eget_hh_mm_txt(hh_data4, mm_data4);
                    //             }, 0);
                    //         });
                    //     }, 0);
                    // }

                    function sget_hh_mm_txt(hh_data, mm_data) {
                        if ($('#swipe_init').val() == 'Y') {
                            var rtn = get_time_format(hh_data, mm_data);

                            $('#pick_stime').val(hh_data + ':' + mm_data + ':00');
                            // $('#stime_txt').html(rtn);

                            var rtn2 = datetime_chk();

                            return rtn2;
                        }
                    }

                    function eget_hh_mm_txt(hh_data, mm_data) {
                        if ($('#swipe_init').val() == 'Y') {
                            var rtn = get_time_format(hh_data, mm_data);

                            $('#pick_etime').val(hh_data + ':' + mm_data + ':00');
                            // $('#etime_txt').html(rtn);

                            var rtn2 = datetime_chk();

                            return rtn2;
                        }
                    }

                    function get_hh_mm_txt(hh_data, mm_data) {
                        var pick_time_tt = $('#pick_time').val();

                        var rtn = get_time_format(hh_data, mm_data);

                        $('#pick_' + pick_time_tt).val(hh_data + ':' + mm_data + ':00');
                        $('#' + pick_time_tt + '_txt').html(rtn);

                        var rtn2 = datetime_chk();

                        return rtn2;
                    }

                    function set_date_time(sd, ed, st, et) {
                        console.log("sd st " + sd + " " + st);
                        console.log("ed et " + ed + " " + et);

                        var sdhtml = dateFormat_week(sd);
                        $('#sdate_txt').html(sdhtml);

                        var edhtml = dateFormat_week(ed);
                        $('#edate_txt').html(edhtml);

                        var stsp = st.split(":");
                        var sthtml = get_time_format(stsp[0], stsp[1]);
                        $('#stime_txt').html(sthtml);

                        // console.log("sthtml" + sthtml);

                        var etsp = et.split(":");
                        var ethtml = get_time_format(etsp[0], etsp[1]);
                        $('#etime_txt').html(ethtml);

                        // console.log("ethtml" + ethtml);

                        $('#sst_sdate').val(sd + ' ' + st);
                        $('#sst_edate').val(ed + ' ' + et);

                        $('#pick_sdate').val(sd);
                        $('#pick_edate').val(ed);
                        $('#pick_stime').val(st);
                        $('#pick_etime').val(et);
                    }

                    function datetime_chk() {
                        var sd = $('#pick_sdate').val();
                        var ed = $('#pick_edate').val();
                        var st = $('#pick_stime').val();
                        var et = $('#pick_etime').val();
                        var pd = $('#pick_date').val();
                        var pt = $('#pick_time').val();

                        if (sd && ed && st && et) {
                            var csdt = new Date(sd + ' ' + st);
                            var cedt = new Date(ed + ' ' + et);

                            console.log("csdt : " + csdt);
                            console.log("cedt : " + cedt);
                            console.log(csdt > cedt);

                            if (csdt == cedt) { //시작 == 마감
                                var usd = (csdt.getTime() / 1000);
                                var usc = Unix_timestamp(usd + 3600);
                                var syd = new Date(usc);

                                // ed = syd.getFullYear() + "-" + get_pad((syd.getMonth() + 1)) + "-" + get_pad(syd.getDate());
                                // et = get_pad(syd.getHours()) + ":" + get_pad(syd.getMinutes());

                                set_date_time(sd, ed, st, et);
                            } else {
                                if (csdt < cedt) { //시작 < 마감
                                    set_date_time(sd, ed, st, et);
                                } else { //시작 > 마감
                                    console.log("pd pt " + pd + " " + pt);

                                    // if (pt == 'stime') {
                                    //     $('#btn_stime').removeClass('btn_active');
                                    //     $('#btn_stime').removeClass('btn-light');
                                    //     $('#btn_stime').addClass('btn-danger');
                                    // } else {
                                    //     $('#btn_etime').removeClass('btn_active');
                                    //     $('#btn_etime').removeClass('btn-light');
                                    //     $('#btn_etime').addClass('btn-danger');
                                    // }

                                    if (pt == 'stime') { //시작 설정시
                                        var usd = (csdt.getTime() / 1000);
                                        var usc = Unix_timestamp(usd + 3600);
                                        var syd = new Date(usc);

                                        // ed = syd.getFullYear() + "-" + get_pad((syd.getMonth() + 1)) + "-" + get_pad(syd.getDate());
                                        // et = get_pad(syd.getHours()) + ":" + get_pad(syd.getMinutes());

                                        console.log(ed + et);

                                        set_date_time(sd, ed, st, et);
                                    } else { //마감 설정시
                                        var ued = (cedt.getTime() / 1000);
                                        var uec = Unix_timestamp(ued - 3600);
                                        var eyd = new Date(uec);

                                        // sd = eyd.getFullYear() + "-" + get_pad((eyd.getMonth() + 1)) + "-" + get_pad(eyd.getDate());
                                        // st = get_pad(eyd.getHours()) + ":" + get_pad(eyd.getMinutes());

                                        set_date_time(sd, ed, st, et);
                                    }
                                }
                            }

                            // if (csdt > cedt) {
                            //     if (pt == 'stime') {
                            //         $('#sst_sdate').val(sd + ' ' + st);
                            //         $('#pick_edate').val(sd);
                            //         $('#pick_etime').val(st);

                            //         var ud = (csdt.getTime() / 1000);
                            //         var uc = Unix_timestamp(ud + 3600);
                            //         var yd = new Date(uc);

                            //         var sth = get_pad(yd.getHours());
                            //         var stm = get_pad(yd.getMinutes());

                            //         var stt = get_time_format(sth, stm);
                            //         st = sth + ":" + stm;

                            //         $('#etime_txt').html(stt);
                            //         $('#sst_edate').val(sd + ' ' + st);

                            //         return 'edate_chg';
                            //     } else {
                            //         $('#sst_edate').val(ed + ' ' + et);
                            //         $('#pick_sdate').val(ed);
                            //         $('#pick_stime').val(et);

                            //         var ud = (cedt.getTime() / 1000);
                            //         var uc = Unix_timestamp(ud - 3600);
                            //         var yd = new Date(uc);

                            //         var eth = get_pad(yd.getHours());
                            //         var etm = get_pad(yd.getMinutes());

                            //         if (cedt.getHours() == 0) {
                            //             var dwt = dateFormat_week(uc.substr(0, 10));
                            //             $('#sdate_txt').html(dwt);
                            //         }

                            //         var ett = get_time_format(eth, etm);
                            //         et = eth + ":" + etm;

                            //         $('#stime_txt').html(ett);

                            //         $('#sst_sdate').val(ed + ' ' + et);

                            //         return 'sdate_chg';
                            //     }
                            // } else {
                            //     console.log("둘다 자정" + csdt.getHours() + cedt.getHours());

                            //     if (csdt.getHours() == 0 && csdt.getHgetMinutesours() == 0 && cedt.getHours() == 0 && cedt.getHgetMinutesours() == 0) {
                            //         $('#sst_sdate').val(sd + ' ' + st);
                            //         $('#sst_edate').val(ed + ' ' + et);
                            //     } else {
                            //         if (csdt.getHours() == 0 && csdt.getMinutes() == 0) {
                            //             var ud = (csdt.getTime() / 1000);
                            //             var uc = Unix_timestamp(ud + 90000);
                            //             var yd = new Date(uc);
                            //             ed = uc.substr(0, 10);

                            //             var dwt = dateFormat_week(ed);
                            //             $('#edate_txt').html(dwt);

                            //             var eth = get_pad(yd.getHours());
                            //             var etm = get_pad(yd.getMinutes());

                            //             var ett = get_time_format(eth, etm);
                            //             et = eth + ":" + etm;

                            //             $('#etime_txt').html(ett);

                            //             $('#sst_sdate').val(sd + ' ' + st);
                            //             $('#sst_edate').val(ed + ' ' + et);
                            //             $('#pick_edate').val(ed);
                            //             $('#pick_etime').val(et);
                            //         } else {
                            //             $('#sst_sdate').val(sd + ' ' + st);
                            //             $('#sst_edate').val(ed + ' ' + et);
                            //         }
                            //     }

                            //     return 'edate_chg';
                            // }
                        } else {
                            $('#sst_sdate').val('');
                            $('#sst_edate').val('');

                            return 'edate_chg';
                        }
                    }
                </script>
                <div class="line_ip mt_25 d-none-temp">
                    <div class="row">
                        <div class="col col-auto line_tit">
                            <h5>시작</h5>
                        </div>
                        <div class="col">

                            <!-- value 안에 데이터 넣어 주세요 -->
                        </div>
                    </div>
                </div>
                <div class="line_ip mt_25 d-none-temp">
                    <div class="row">
                        <div class="col col-auto line_tit">
                            <h5>종료</h5>
                        </div>
                        <div class="col">

                            <!-- value 안에 데이터 넣어 주세요 -->
                        </div>
                    </div>
                </div>
                <div class="line_ip mt_25">
                    <div class="row">
                        <div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_repeat.png" alt="반복 아이콘"></div>
                        <div class="col">
                            <input type="hidden" name="sst_repeat_json" id="sst_repeat_json" value='<?= $row_sst['sst_repeat_json'] ?>' />
                            <input type="text" readonly class="form-none cursor_pointer" name="sst_repeat_json_v" id="sst_repeat_json_v" placeholder="반복" value="<?= $row_sst['sst_repeat_json_v'] ?>" data-toggle="modal" data-target="#schedule_repeat">
                            <!-- value 안에 데이터 넣어 주세요 -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_member.png" alt="멤버 아이콘"></div>
                    <div class="col">
                        <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $row_sst['sgdt_idx'] ?>" />
                        <input type="text" readonly class="form-none cursor_pointer" name="sgdt_idx_t" id="sgdt_idx_t" placeholder="멤버 선택" value="<?= $row_sst['sgdt_idx_t'] ?>" onclick="f_modal_schedule_member();">
                        <!-- value 안에 데이터 넣어 주세요 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_notice.png" alt="알림 아이콘"></div>
                    <div class="col">
                        <input type="hidden" name="sst_alram" id="sst_alram" value="<?= $row_sst['sst_alram'] ?>" />
                        <input type="text" readonly class="form-none cursor_pointer" name="sst_alram_t" id="sst_alram_t" placeholder="알림 선택" value="<?= $row_sst['sst_alram_t'] ?>" data-toggle="modal" data-target="#schedule_notice">
                        <!-- value 안에 데이터 넣어 주세요 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_location.png" alt="위치 아이콘"></div>
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <!-- <span class="text-primary mr_12">KT&G</span> -->
                            <!-- 별칭 출력 -->
                            <input type="text" readonly class="form-none cursor_pointer flex-fill" name="slt_idx_t" id="slt_idx_t" placeholder="위치 선택" value="<?= $row_sst['slt_idx_t'] ?>" onclick="f_modal_schedule_location();">
                        </div>
                        <!-- value 안에 데이터 넣어 주세요 -->

                        <input type="hidden" name="slt_idx" id="slt_idx" value="<?= $row_sst['slt_idx'] ?>" />
                        <input type="hidden" name="sst_location_title" id="sst_location_title" value="<?= $row_sst['sst_location_title'] ?>" />
                        <input type="hidden" name="sst_location_add" id="sst_location_add" value="<?= $row_sst['sst_location_add'] ?>" />
                        <input type="hidden" name="sst_location_lat" id="sst_location_lat" value="<?= $row_sst['sst_location_lat'] ?>" />
                        <input type="hidden" name="sst_location_long" id="sst_location_long" value="<?= $row_sst['sst_location_long'] ?>" />
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_material.png" alt="준비물 아이콘"></div>
                    <div class="col">
                        <input type="text" class="form-none txt-cnt" name="sst_supplies" id="sst_supplies" maxlength="100" data-length-id="sst_supplies_cnt" oninput="maxLengthCheck(this)" placeholder="준비물 입력" value="<?= $row_sst['sst_supplies'] ?>">
                    </div>
                    <!-- value 안에 데이터 넣어 주세요 -->
                </div>
            </div>
            <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_supplies_cnt">0</span>/100)</p>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_memo.png" alt="메모 아이콘"></div>
                    <div class="col">
                        <textarea class="form-none line_h1_4 txt-cnt" style="height: 5rem;" name="sst_memo" id="sst_memo" maxlength="500" data-length-id="sst_memo_cnt" oninput="maxLengthCheck(this)" placehold