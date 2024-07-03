<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = "일정 입력";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";

if($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
}

if($_GET['sdate']) {
    $sdate = $_GET['sdate'];
} else {
    $sdate = date('Y-m-d');
}
$tt = strtotime($sdate);

$numDay = date('d', $tt);
$numMonth = date('m', $tt);
$numMonth2 = date('n', $tt);
$numYear = date('Y', $tt);
$prevMonth = date('Y-m-01', strtotime($sdate." -".$dayOfWeek."days"));
$nextMonth = date('Y-m-01', strtotime($sdate." +".$dayOfWeek."days"));
$calendar_date_title = $numYear."년 ".$numMonth2."월";
$today_st = DateType($sdate, 20);
$today_et = DateType($sdate, 20);

//시작시간, 마감시간을 한시간뒤로 셋팅
$now_t = date("Y-m-d H:i:s");
$shour = date("H", strtotime($now_t." +1 hours"));
$ehour = date("H", strtotime($now_t." +2 hours"));

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
        $time_now_t = "오후 " . get_pad($hour). ":". $min;
    } else {
        if($hour == 12 && $min == '00') {
            $time_now_t = "정오 " . get_pad($hour). ":". $min;
        } elseif($hour == 0 && $min == '00') {
            $time_now_t = "자정 " . get_pad($hour). ":". $min;
        } else {
            $time_now_t = "오전 " . get_pad($hour). ":". $min;
        }
    }

    return $time_now_t;
}

if($_GET['sst_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sst_idx', $_GET['sst_idx']);
    $DB->where('sst_show', 'Y');
    $row_sst = $DB->getone('smap_schedule_t');

    if($row_sst['sst_sdate']) {
        $arr_sdate_t = get_date_f($row_sst['sst_sdate']);
        $today_st = DateType($row_sst['sst_sdate'], 20);
        $time_now_st = get_time_format($row_sst['sst_sdate']);
        $ex_sdate_t = explode(' ', $row_sst['sst_sdate']);
    }
    if($row_sst['sst_edate']) {
        $arr_edate_t = get_date_f($row_sst['sst_edate']);
        $today_et = DateType($row_sst['sst_edate'], 20);
        $time_now_et = get_time_format($row_sst['sst_edate']);
        $ex_edate_t = explode(' ', $row_sst['sst_edate']);
    }

    if($row_sst['sst_repeat_json']) {
        $sst_repeat_json_t = json_decode($row_sst['sst_repeat_json'], true);
    }
} else {
    $ex_sdate_t = explode(' ', date("Y-m-d ".$shour.":00"));
    $ex_edate_t = explode(' ', date("Y-m-d ".$ehour.":00"));

    $row_sst['sst_sdate'] = date("Y-m-d ".$shour.":00");
    $row_sst['sst_edate'] = date("Y-m-d ".$ehour.":00");
}

$time_now_st = get_time_format($row_sst['sst_sdate']);
$time_now_et = get_time_format($row_sst['sst_edate']);
?>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?=NCPCLIENTID?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/anypicker@latest/dist/anypicker-all.min.css" />
<!-- <script type="text/javascript" src="//cdn.jsdelivr.net/npm/anypicker@latest/dist/anypicker.min.js"></script> -->
<script type="text/javascript" src="<?=CDN_HTTP?>/lib/anypicker/anypicker.js?v=<?=$v_txt?>"></script>
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
    height: 150px;
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
    height: 50px !important;
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
    line-height: 62px;
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
<div class="container sub_pg">
    <div class="mt_22">
        <form method="post" name="frm_form" id="frm_form" action="./schedule_update" target="hidden_ifrm" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="schedule_form" />
            <input type="hidden" name="sst_idx" id="sst_idx" value="<?=$row_sst['sst_idx']?>" />
            <input type="hidden" name="pick_date" id="pick_date" value="" />
            <input type="hidden" name="pick_time" id="pick_time" value="" />
            <input type="hidden" name="week_calendar" id="week_calendar" value="N" />
            <input type="hidden" name="csdate" id="csdate" value="<?=$sdate?>" />
            <input type="hidden" name="pick_sdate" id="pick_sdate" value="<?=$ex_sdate_t[0]?>" />
            <input type="hidden" name="pick_edate" id="pick_edate" value="<?=$ex_edate_t[0]?>" />
            <input type="hidden" name="pick_stime" id="pick_stime" value="<?=$ex_sdate_t[1]?>" />
            <input type="hidden" name="pick_etime" id="pick_etime" value="<?=$ex_edate_t[1]?>" />
            <input type="hidden" name="sst_sdate" id="sst_sdate" value="<?=$row_sst['sst_sdate']?>">
            <input type="hidden" name="sst_edate" id="sst_edate" value="<?=$row_sst['sst_edate']?>">
            <div class="ip_wr">
                <input type="text" class="form-control txt-cnt" name="sst_title" id="sst_title" value="<?=$row_sst['sst_title']?>" maxlength="30" data-length-id="sst_title_cnt" oninput="maxLengthCheck(this)" placeholder="일정 내용을 입력해주세요.">
                <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_title_cnt">0</span>/15)</p>
            </div>

            <div class="line_ip_box border rounded-lg px_20 py_20 mt_20">
                <div class="line_ip">
                    <div class="row justify-content-between">
                        <h5 class="col col-auto">하루 종일</h5>
                        <div class="col">
                            <div class="custom-switch ml-auto">
                                <input type="checkbox" class="custom-control-input" name="sst_all_day" id="sst_all_day" value="Y" <?php if($row_sst['sst_all_day'] == 'Y') {
                                    echo " checked";
                                } ?> onchange="f_all_day();" />
                                <label class="custom-control-label" for="sst_all_day"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
                <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

                <div class="line_ip mt_25" id="cal_time_box">
                    <div class="row align-items-center">
                        <div class="col text-center">
                            <button type="button" id="btn_sdate" class="btn btn-light btn-c"><span id="sdate_txt"><?=$today_st?></span></button>
                            <button type="button" id="btn_stime" class="btn btn-light btn-c cal_time_box"><span id="stime_txt"><?=$time_now_st?></span></button>
                        </div>
                        <div class="col-1 d-flex justify-content-center align-items-center">
                            <i class="xi-long-arrow-right fs_20" style="color: gray"></i>
                        </div>
                        <div class="col text-center">
                            <button type="button" id="btn_edate" class="btn btn-light btn-c"><span id="edate_txt"><?=$today_et?></span></button>
                            <button type="button" id="btn_etime" class="btn btn-light btn-c cal_time_box"><span id="etime_txt"><?=$time_now_et?></span></button>
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
                                    <a href="javascript:;" onclick="f_calendar_init('today');"><img class="mr-2" src="<?=CDN_HTTP?>/img/sel_month.png" alt="월 선택 아이콘" style="width:1.6rem; "></a>
                                    <p class="fs_15 fw_600" id="calendar_date_title"><?=$calendar_date_title?></p>
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
for($q = 0;$q < 24;$q++) {
    if($w < 10) {
        $w = "0".$w;
    }
    ?>
                            <div class="swiper-slide" data-hh="<?=$w?>"><?=$w?></div>
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
for($q = 0;$q < 12;$q++) {
    if($w < 10) {
        $w = "0".$w;
    }
    ?>
                            <div class="swiper-slide" data-mm="<?=$w?>"><?=$w?></div>
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
for($q = 0;$q < 24;$q++) {
    if($w < 10) {
        $w = "0".$w;
    }
    ?>
                            <div class="swiper-slide" data-hh="<?=$w?>"><?=$w?></div>
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
for($q = 0;$q < 12;$q++) {
    if($w < 10) {
        $w = "0".$w;
    }
    ?>
                            <div class="swiper-slide" data-mm="<?=$w?>"><?=$w?></div>
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

                    // return hh_t + ' ' + get_pad(hh_data) + ':' + get_pad(mm_data);
                    return hh_t + ' ' + hh_data + ':' + mm_data;
                }

                function get_hh_mm_txt(hh_data, mm_data) {
                    var pick_time_tt = $('#pick_time').val();

                    var rtn = get_time_format(hh_data, mm_data);

                    $('#pick_' + pick_time_tt).val(hh_data + ':' + mm_data + ':00');
                    $('#' + pick_time_tt + '_txt').html(rtn);

                    setTimeout(() => {
                        datetime_chk();
                    }, 100);
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
                        f_open_cal('sdate');
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
                        f_open_cal('edate');
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
                    f_open_time('stime');

                    // ttcc();
                });
                btn_etime_b.addEventListener('click', (e) => {
                    this.blur();
                    e.preventDefault();
                    btn_class_active();
                    setTimeout(() => {
                        $('#btn_etime').addClass('btn_active');
                    }, 100);
                    f_open_time('etime');

                    // ttcc();
                });

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
                    $('#calendar_' + dct).addClass('active');
                    var pdt = $('#pick_date').val();

                    var dwt = dateFormat_week(dct);

                    if (pdt == 'sdate') {
                        $('#sdate_txt').html(dwt);
                        $('#edate_txt').html(dwt);
                        $('#pick_sdate').val(dct);
                        $('#pick_edate').val(dct);
                    } else {
                        var date1 = new Date($('#pick_sdate').val());
                        var date2 = new Date(dct);
                        var dctg = chg_ddt(dct);

                        if (date1 <= date2) {
                            $('#pick_edate').val(dct);
                            $('#edate_txt').html(dwt);
                        } else {
                            $('#pick_sdate').val(dct);
                            $('#pick_edate').val(dct);
                            $('#sdate_txt').html(dwt);
                            $('#edate_txt').html(dwt);
                        }
                    }

                    datetime_chk();
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
                    $('#pick_date').val(t);
                    $('#schedule_calandar_box_header').show();
                    f_calendar_init();
                }

                function f_open_time(t) {
                    $('#pick_time').val(t);
                    $('#' + t + '_picker').show();

                    var dStartD, dEndD;

                    var defaults = {
                        pagination: {
                            el: ".swiper-pagination",
                            clickable: true
                        },
                        slidesPerView: "auto",
                        freeMode: {
                            enabled: true,
                            sticky: true,
                            momentumBounce: false,
                            momentum: false
                        },
                        loop: true,
                        loopedSlides: 1,
                        loopAdditionalSlides: 3,
                        direction: "vertical",
                        centeredSlides: true,
                        speed: 600,
                        breakpointsBase: 'container ',
                        init: false,
                    };

                    dStartD = new Date($('#sst_sdate').val());
                    dEndD = new Date($('#sst_edate').val());

                    // console.log(dStartD);

                    var hours = new Swiper(
                        ".swiper-container." + t + "_hours",
                        Object.assign({}, defaults)
                    );

                    var minutes = new Swiper(
                        ".swiper-container." + t + "_minutes",
                        Object.assign({}, defaults)
                    );

                    if (t == 'stime') {
                        var dhh = dStartD.getHours();
                        var dmm = dStartD.getMinutes();
                    } else if (t == 'etime') {
                        var dhh = dEndD.getHours();
                        var dmm = dEndD.getMinutes();
                    }

                    hours.on("init", function() {
                        var hours_initialSlide;

                        $.each(hours.slides, function(index, value) {
                            if (dhh == value.dataset.hh) {
                                hours_initialSlide = value.dataset.swiperSlideIndex;
                            }
                        });

                        hours.slideToLoop(hours_initialSlide, 500, false);
                    });

                    hours.init();

                    minutes.on("init", function() {
                        var min_initialSlide;

                        $.each(minutes.slides, function(index, value) {
                            if (dmm == value.dataset.mm) {
                                min_initialSlide = value.dataset.swiperSlideIndex;
                            }
                        });

                        minutes.slideToLoop(min_initialSlide, 500, false);
                    });

                    minutes.init();

                    hours.on("transitionEnd", function() {
                        var hh_data = hours.slides[hours.activeIndex].dataset.hh;
                        var mm_data = minutes.slides[minutes.activeIndex].dataset.mm;

                        get_hh_mm_txt(hh_data, mm_data);
                    });

                    minutes.on("transitionEnd", function() {
                        var hh_data = hours.slides[hours.activeIndex].dataset.hh;
                        var mm_data = minutes.slides[minutes.activeIndex].dataset.mm;

                        console.log(hh_data);
                        console.log(mm_data);

                        get_hh_mm_txt(hh_data, mm_data);
                    });
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

                function datetime_chk() {
                    var sd = $('#pick_sdate').val();
                    var ed = $('#pick_edate').val();
                    var st = $('#pick_stime').val();
                    var et = $('#pick_etime').val();
                    var pt = $('#pick_time').val();

                    if (sd && ed && st && et) {
                        var csdt = new Date(sd + ' ' + st);
                        var cedt = new Date(ed + ' ' + et);

                        // console.log(csdt > cedt);

                        if (csdt > cedt) {
                            if (pt == 'stime') {
                                $('#pick_edate').val(sd);
                                $('#pick_etime').val(st);

                                var sth = get_pad((csdt.getHours() + 1));
                                var stm = get_pad(csdt.getMinutes());

                                var stt = get_time_format(sth, stm);
                                st = sth + ":" + stm;

                                $('#etime_txt').html(stt);

                                $('#sst_edate').val(sd + ' ' + st);
                            } else {
                                $('#pick_sdate').val(ed);
                                $('#pick_stime').val(et);
                                var ud = (cedt.getTime() / 1000);
                                var uc = Unix_timestamp(ud - 3600);
                                var yd = new Date(uc);

                                var eth = get_pad(yd.getHours());
                                var etm = get_pad(yd.getMinutes());

                                if (cedt.getHours() == 0) {
                                    var dwt = dateFormat_week(uc.substr(0, 10));
                                    $('#sdate_txt').html(dwt);
                                }

                                var ett = get_time_format(eth, etm);
                                et = eth + ":" + etm;

                                $('#stime_txt').html(ett);

                                $('#sst_sdate').val(ed + ' ' + et);
                            }
                        } else {
                            $('#sst_sdate').val(sd + ' ' + st);
                            $('#sst_edate').val(ed + ' ' + et);
                        }
                    } else {
                        $('#sst_sdate').val('');
                        $('#sst_edate').val('');
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
                        <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_repeat.png" alt="반복 아이콘"></div>
                        <div class="col">
                            <input type="hidden" name="sst_repeat_json" id="sst_repeat_json" value='<?=$row_sst['sst_repeat_json']?>' />
                            <input type="text" readonly class="form-none cursor_pointer" name="sst_repeat_json_v" id="sst_repeat_json_v" placeholder="반복" value="<?=$row_sst['sst_repeat_json_v']?>" data-toggle="modal" data-target="#schedule_repeat">
                            <!-- value 안에 데이터 넣어 주세요 -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_member.png" alt="멤버 아이콘"></div>
                    <div class="col">
                        <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?=$row_sst['sgdt_idx']?>" />
                        <input type="text" readonly class="form-none cursor_pointer" name="sgdt_idx_t" id="sgdt_idx_t" placeholder="멤버 선택" value="<?=$row_sst['sgdt_idx_t']?>" onclick="f_modal_schedule_member();">
                        <!-- value 안에 데이터 넣어 주세요 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_notice.png" alt="알림 아이콘"></div>
                    <div class="col">
                        <input type="hidden" name="sst_alram" id="sst_alram" value="<?=$row_sst['sst_alram']?>" />
                        <input type="text" readonly class="form-none cursor_pointer" name="sst_alram_t" id="sst_alram_t" placeholder="알림 선택" value="<?=$row_sst['sst_alram_t']?>" data-toggle="modal" data-target="#schedule_notice">
                        <!-- value 안에 데이터 넣어 주세요 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_location.png" alt="위치 아이콘"></div>
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <!-- <span class="text-primary mr_12">KT&G</span> -->
                            <!-- 별칭 출력 -->
                            <input type="text" readonly class="form-none cursor_pointer flex-fill" name="slt_idx_t" id="slt_idx_t" placeholder="위치 선택" value="<?=$row_sst['slt_idx_t']?>" onclick="f_modal_schedule_location();">
                        </div>
                        <!-- value 안에 데이터 넣어 주세요 -->

                        <input type="hidden" name="slt_idx" id="slt_idx" value="<?=$row_sst['slt_idx']?>" />
                        <input type="hidden" name="sst_location_title" id="sst_location_title" value="<?=$row_sst['sst_location_title']?>" />
                        <input type="hidden" name="sst_location_add" id="sst_location_add" value="<?=$row_sst['sst_location_add']?>" />
                        <input type="hidden" name="sst_location_lat" id="sst_location_lat" value="<?=$row_sst['sst_location_lat']?>" />
                        <input type="hidden" name="sst_location_long" id="sst_location_long" value="<?=$row_sst['sst_location_long']?>" />
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_material.png" alt="준비물 아이콘"></div>
                    <div class="col">
                        <input type="text" class="form-none txt-cnt" name="sst_supplies" id="sst_supplies" maxlength="100" data-length-id="sst_supplies_cnt" oninput="maxLengthCheck(this)" placeholder="준비물 입력" value="<?=$row_sst['sst_supplies']?>">
                    </div>
                    <!-- value 안에 데이터 넣어 주세요 -->
                </div>
            </div>
            <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_supplies_cnt">0</span>/100)</p>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_memo.png" alt="메모 아이콘"></div>
                    <div class="col">
                        <textarea class="form-none line_h1_4 txt-cnt" style="height: 5rem;" name="sst_memo" id="sst_memo" maxlength="500" data-length-id="sst_memo_cnt" oninput="maxLengthCheck(this)" placeholder="메모 입력"><?=$row_sst['sst_memo']?></textarea>
                    </div>
                </div>
            </div>
            <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_memo_cnt">0</span>/500)</p>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_contact.png" alt="연락처 아이콘"></div>
                    <div class="col">
                        <input type="text" readonly class="form-none cursor_pointer" placeholder="연락처 입력" value="" data-toggle="modal" data-target="#schedule_contact">
                        <!-- 연락처미입력시 ↑-->
                        <div class="contact_group fs_15 fc_gray_800 fw_600">
                            <ul id="contact_list_box" class="mt-3">

                            </ul>
                        </div>
                        <!-- 연락처입력시 ↑-->
                    </div>
                </div>
            </div>

            <?php if($row_sst['sst_idx']) { ?>
            <div class="d-flex justify-content-center align-items-end" style="height: 120px;">
                <div class="form-row w-100">
                    <div class="col-5"><button type="button" class="btn rounded btn-bg_gray btn-lg btn-block" data-toggle="modal" data-target="#schedule_delete">일정 삭제하기</button></div>
                    <div class="col-7"><button type="submit" class="btn rounded btn-primary btn-lg btn-block">일정 수정하기</button></div>
                </div>
            </div>
            <?php } else { ?>
            <div class="bottom_btn_flex_end_wrap" style="height: 120px;">
                <div class="bottom_btn_flex_end_box">
                    <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block">입력한 일정 저장하기</button>
                </div>
            </div>
            <?php } ?>
        </form>
        <script>
        $(document).ready(function() {
            $(document).on("keyup", "input.txt-cnt", function() {
                var cnt_id = $(this).data('length-id');
                $('#' + cnt_id).text($(this).val().length);
            });
            $(document).on("keyup", "textarea.txt-cnt", function() {
                var cnt_id = $(this).data('length-id');
                $('#' + cnt_id).text($(this).val().length);
            });

            <?php if($row_sst['sst_idx']) { ?>
            f_contact_list('<?=$row_sst['sst_idx']?>');
            <?php } else { ?>
            f_contact_list();
            <?php } ?>

            <?php if ($row_sst['sst_title']) { ?>
            $('#sst_title_cnt').text($('#sst_title').val().length);
            <?php } ?>
            <?php if ($row_sst['sst_supplies']) { ?>
            $('#sst_supplies_cnt').text($('#sst_supplies').val().length);
            <?php } ?>
            <?php if ($row_sst['sst_memo']) { ?>
            $('#sst_memo_cnt').text($('#sst_memo').val().length);
            <?php } ?>

            <?php if($row_sst['sst_all_day'] == 'Y') { ?>
            f_all_day();
            <?php } ?>
        });

        $("#frm_form").validate({
            submitHandler: function() {
                $('#splinner_modal').modal('toggle');

                return true;
            },
            rules: {
                sst_title: {
                    required: true,
                },
                sst_sdate: {
                    required: true,
                },
                sgdt_idx_t: {
                    required: true,
                },
                slt_idx_t: {
                    required: true,
                },
            },
            messages: {
                sst_title: {
                    required: "일정 내용을 입력해주세요.",
                },
                sst_sdate: {
                    required: "시작일을 입력해주세요.",
                },
                sgdt_idx_t: {
                    required: "멤버를 선택해주세요.",
                },
                slt_idx_t: {
                    required: "위치를 선택해주세요.",
                },
            },
            errorPlacement: function(error, element) {
                $(element)
                    .closest("form")
                    .find("span[for='" + element.attr("id") + "']")
                    .append(error);
            },
        });

        function f_modal_schedule_member() {
            var form_data = new FormData();
            form_data.append("act", "get_schedule_member");
            <?php if($row_sst['sgdt_idx']) { ?>
            form_data.append("sgdt_idx", "<?=$row_sst['sgdt_idx']?>");
            <?php } ?>

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
                        $('#schedule_member_content').html(data);
                        setTimeout(() => {
                            $('#schedule_member').modal('show');
                        }, 100);
                    }
                },
                error: function(err) {
                    console.log(err);
                },
            });
        }

        function f_modal_schedule_location() {
            f_location_like_list();
            setTimeout(() => {
                $('#schedule_location').modal('show');
            }, 100);
        }

        function f_location_like_list() {
            var form_data = new FormData();
            form_data.append("act", "list_like_location");

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
                        $('#location_like_list_box').html(data);
                    }
                },
                error: function(err) {
                    console.log(err);
                },
            });
        }

        function f_contact_list(i) {
            var form_data = new FormData();
            form_data.append("act", "list_contact");
            if (i) {
                form_data.append("sst_idx", i);
            }

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
                        $('#contact_list_box').html(data);
                    }
                },
                error: function(err) {
                    console.log(err);
                },
            });
        }

        function f_delete_schedule(i) {
            var form_data = new FormData();
            form_data.append("act", "schedule_delete");
            form_data.append("sst_idx", i);

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
                    if (data == 'Y') {
                        history.back();
                    }
                },
                error: function(err) {
                    console.log(err);
                },
            });
        }
        </script>
    </div>
</div>

<!-- 토스트 Toast -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>일정이 등록되었습니다</p>
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>


<!-- F-4 일정 입력 > 시작종료  -->
<div class="modal fade" id="schedule_date_time" tabindex="-1">
    <div class="modal-dialog modal-default modal-default_y modal-dialog-scrollable modal-dialog-centered">
        <form method="post" name="frm_schedule_date_time" id="frm_schedule_date_time">
            <div class="modal-content">
                <div class="modal-header justify-content-end border-0 pt_20 pb_4">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button>
                </div>
                <div class="cld_head_wr">
                    <div class="add_cal_tit mb-3">
                        <div class="sel_month d-inline-flex" style="margin-left:2rem;">
                            <img class="mr-2" src="<?=CDN_HTTP?>/img/sel_month.png" alt="월 선택 아이콘" style="width:1.6rem; ">
                            <b id="schedule-title" class="text-text fs_15"></b>
                        </div>
                        <div class="d-flex" style="margin-right:1rem;">
                            <button type="button" id="btn-schedule-today" class="btn h-auto px-1 pl-3 mr-3"><i class="xi-calendar-check"></i></button>
                            <button type="button" id="btn-schedule-prev" class="btn h-auto px-1 pl-3 mr-3"><i class="xi-angle-left-min"></i></button>
                            <button type="button" id="btn-schedule-next" class="btn h-auto px-1 pr-3"><i class="xi-angle-right-min"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-body py-0 mo_cld_body">
                    <div class="scroll_bar_y">
                        <link rel="stylesheet" type="text/css" href="<?=CDN_HTTP?>/lib/fullcalendar/fullcalendar.min.css" />
                        <script type="text/javascript" src="<?=CDN_HTTP?>/lib/fullcalendar/moment.min.js"></script>
                        <script type="text/javascript" src="<?=CDN_HTTP?>/lib/fullcalendar/fullcalendar.min.js"></script>
                        <script type="text/javascript" src="<?=CDN_HTTP?>/lib/fullcalendar/ko.js"></script>
                        <script type="text/javascript" src="<?=CDN_HTTP?>/lib/fullcalendar/gcal.min.js"></script>
                        <link rel="stylesheet" type="text/css" href="<?=CDN_HTTP?>/lib/fullcalendar/fullcalendar.custom.css?v=<?=$v_txt?>" />
                        <script>
                        (function($) {
                            'use strict';
                            $(function() {
                                if ($('#schedule_box').length) {
                                    var $calendar = $('#schedule_box');
                                    $calendar.fullCalendar({
                                        locale: 'ko',
                                        header: false,
                                        viewRender: (view) => {
                                            let date
                                            switch (view.type) {
                                                case 'month':
                                                    date = view.title
                                                    break
                                            }
                                            $('#schedule-title').text(date);
                                        },
                                        defaultView: 'month',
                                        navLinks: false,
                                        editable: false,
                                        eventLimit: true,
                                        displayEventTime: false,
                                        allDayDefault: true,
                                        contentHeight: "auto",
                                        eventSources: [{
                                            url: './schedule_update',
                                            type: 'POST',
                                            data: {
                                                act: 'event_source',
                                            },
                                            error: function() {
                                                console.log('잘못된 접근입니다.');
                                            },
                                        }],
                                        eventAfterAllRender: function(view) {
                                            $('.fc-event-container').html('');
                                            $('.fc-more-cell').html('');
                                        },
                                        eventClick: function(event) {
                                            console.log('event sst_idx: ' + event.start._i);
                                        },
                                        dayClick: function(date, jsEvent, view) {
                                            var date = date.format();
                                            var s_date = $('#sst_sdate_d1').val();
                                            var e_date = $('#sst_edate_d1').val();
                                            var s_date_t = Math.round(new Date(s_date) / 1000);
                                            var e_date_t = Math.round(new Date(date) / 1000);

                                            if (s_date == '') {
                                                $('#sst_sdate_d1').val(date);
                                            } else {
                                                if (s_date != '' && e_date != '') {
                                                    $('#sst_sdate_d1').val(date);
                                                    $('#sst_edate_d1').val('');
                                                } else {
                                                    if (s_date_t > e_date_t) {
                                                        $('#sst_sdate_d1').val(date);
                                                        $('#sst_edate_d1').val('');
                                                    } else {
                                                        $('#sst_edate_d1').val(date);
                                                    }
                                                }
                                            }
                                        }
                                    });
                                    $("#btn-schedule-next").on('click', function() {
                                        $calendar.fullCalendar('next')
                                    });
                                    $("#btn-schedule-prev").on('click', function() {
                                        $calendar.fullCalendar('prev')
                                    });
                                    $("#btn-schedule-today").on('click', function() {
                                        $calendar.fullCalendar('today');
                                    });
                                }
                            });
                        })(jQuery);
                        </script>

                        <div id="schedule_box"></div>
                    </div>
                    <div class="time_conent px-0">
                        <div class="ip_wr border-top pt_20">
                            <div class="ip_tit d-flex align-items-center justify-content-between">
                                <h5 class="text-body fw_800">시작일시</h5>
                            </div>
                            <div class="form-row flex-nowrap align-items-center mb-3">
                                <input type="text" readonly class="form-control form-control-sm" name="sst_sdate_d1" id="sst_sdate_d1" value="<?=$arr_sdate_t['date']?>" placeholder="시작일자를 선택해주세요." />
                                <span class="mx-2"> </span>
                                <select class="form-control custom-select form-control-sm" name="sst_sdate_d4" id="sst_sdate_d4">
                                    <option value="1" <?php if($arr_sdate_t['ampm'] == '1') {
                                        echo " selected";
                                    } ?>>오전</option>
                                    <option value="2" <?php if($arr_sdate_t['ampm'] == '2') {
                                        echo " selected";
                                    } ?>>오후</option>
                                </select>
                            </div>
                            <div class="form-row flex-nowrap align-items-center">
                                <select class="form-control custom-select form-control-sm" name="sst_sdate_d2" id="sst_sdate_d2">
                                    <?php
for($q = 1;$q < 13;$q++) {
    if($q < 10) {
        $q = '0'.$q;
    }
    ?>
                                    <option value="<?=$q?>" <?php if($arr_sdate_t['hour'] == $q) {
                                        echo " selected";
                                    } ?>><?=$q?></option>
                                    <?php
}
?>
                                </select>
                                <span class="mx-2">:</span>
                                <select class="form-control custom-select form-control-sm" name="sst_sdate_d3" id="sst_sdate_d3">
                                    <?php
$w = 0;
for($q = 0;$q < 6;$q++) {
    $w = ($q * 10);
    if($w < 1) {
        $w = '00';
    }
    ?>
                                    <option value="<?=$w?>" <?php if($arr_sdate_t['min'] == $w) {
                                        echo " selected";
                                    } ?>><?=$w?></option>
                                    <?php
}
?>
                                </select>
                            </div>
                        </div>
                        <div class="ip_wr pt_20">
                            <div class="ip_tit d-flex align-items-center justify-content-between">
                                <h5 class="text-body fw_800">종료일시</h5>
                            </div>
                            <div class="form-row flex-nowrap align-items-center mb-3">
                                <input type="text" readonly class="form-control form-control-sm" name="sst_edate_d1" id="sst_edate_d1" value="<?=$arr_edate_t['date']?>" placeholder="종료일자를 선택해주세요." />
                                <span class="mx-2"> </span>
                                <select class="form-control custom-select form-control-sm" name="sst_edate_d4" id="sst_edate_d4">
                                    <option value="1" <?php if($arr_edate_t['ampm'] == '1') {
                                        echo " selected";
                                    } ?>>오전</option>
                                    <option value="2" <?php if($arr_edate_t['ampm'] == '2') {
                                        echo " selected";
                                    } ?>>오후</option>
                                </select>
                            </div>
                            <div class="form-row flex-nowrap align-items-center">
                                <select class="form-control custom-select form-control-sm" name="sst_edate_d2" id="sst_edate_d2">
                                    <?php
for($q = 1;$q < 13;$q++) {
    if($q < 10) {
        $q = '0'.$q;
    }
    ?>
                                    <option value="<?=$q?>" <?php if($arr_edate_t['hour'] == $q) {
                                        echo " selected";
                                    } ?>><?=$q?></option>
                                    <?php
        $w++;
}
?>
                                </select>
                                <span class="mx-2">:</span>
                                <select class="form-control custom-select form-control-sm" name="sst_edate_d3" id="sst_edate_d3">
                                    <?php
$w = 0;
for($q = 0;$q < 6;$q++) {
    $w = ($q * 10);
    if($w < 1) {
        $w = '00';
    }
    ?>
                                    <option value="<?=$w?>" <?php if($arr_edate_t['min'] == $w) {
                                        echo " selected";
                                    } ?>><?=$w?></option>
                                    <?php
}
?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-0 py-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">시간 저장하기</button>
                </div>
            </div>
        </form>
        <script>
        $("#frm_schedule_date_time").validate({
            submitHandler: function() {
                var f = document.frm_schedule_date_time;

                if (f.sst_sdate_d4.value == '2') {
                    var h1 = (parseInt(f.sst_sdate_d2.value) + 12);
                } else {
                    var h1 = f.sst_sdate_d2.value;
                }
                if (f.sst_edate_d4.value == '2') {
                    var h2 = (parseInt(f.sst_edate_d2.value) + 12);
                } else {
                    var h2 = f.sst_edate_d2.value;
                }

                var sdate_t = f.sst_sdate_d1.value + " " + h1 + ":" + f.sst_sdate_d3.value + ":00";
                var edate_t = f.sst_edate_d1.value + " " + h2 + ":" + f.sst_edate_d3.value + ":00";
                var sdate_time = Math.round(new Date(sdate_t) / 1000);
                var edate_time = Math.round(new Date(edate_t) / 1000);

                if (sdate_time > edate_time) {
                    jalert("시작일시가 종료일시보다 큽니다.");
                    return false;
                }

                $('#sst_sdate').val(sdate_t);
                $('#sst_edate').val(edate_t);
                $('#schedule_date_time').modal('hide');

                return false;
            },
            rules: {
                sst_sdate_d1: {
                    required: true,
                },
                sst_sdate_d2: {
                    required: true,
                },
                sst_sdate_d3: {
                    required: true,
                },
                sst_edate_d1: {
                    required: true,
                },
                sst_edate_d2: {
                    required: true,
                },
                sst_edate_d3: {
                    required: true,
                },
            },
            messages: {
                sst_sdate_d1: {
                    required: "시작일을 선택해주세요.",
                },
                sst_sdate_d2: {
                    required: "시작시간을 선택해주세요.",
                },
                sst_sdate_d3: {
                    required: "시작분을 선택해주세요.",
                },
                sst_edate_d1: {
                    required: "종료일을 선택해주세요.",
                },
                sst_edate_d2: {
                    required: "종료시간을 선택해주세요.",
                },
                sst_edate_d3: {
                    required: "종료분을 선택해주세요.",
                },
            },
            errorPlacement: function(error, element) {
                $(element)
                    .closest("form")
                    .find("span[for='" + element.attr("id") + "']")
                    .append(error);
            },
        });
        </script>
    </div>
</div>

<!-- F-4 일정 입력 > 반복  -->
<div class="modal fade" id="schedule_repeat" tabindex="-1">
    <div class="modal-dialog modal-default  modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_repeat" id="frm_schedule_repeat">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">반복</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y">
                    <?php
                    foreach ($arr_sst_repeat_json as $key => $val) {
                        if ($val) {
                            ?>
                    <div class="line_ip pb_16 mt_16">
                        <?php if($key == "3") { ?>
                        <div class="checks mb-4">
                            <label>
                                <input type="radio" class="repeat_r1" name="repeat_r1" id="r1_<?=$key?>" value="<?=$key?>" onchange="f_repeat_sel(this.value);" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p">
                                    <p class="text_dynamic" style="word-break: break-all;">1주 마다</p>
                                </div>
                            </label>
                        </div>
                        <div class="table_scroll scroll_bar_x week_wrappp">
                            <div class="week_btn btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                <?php
                                foreach ($arr_sst_repeat_json_r2 as $key => $val) {
                                    if ($val) {
                                        ?>
                                <label class="btn btn-outline-secondary">
                                    <input type="checkbox" class="repeat_r2" name="r2[]" id="r2_<?=$key?>" value="<?=$key?>" /> <?=$val?>
                                </label>
                                <?php
                                    }
                                }
                            ?>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="checks m-0">
                            <label>
                                <input type="radio" class="repeat_r1" name="repeat_r1" id="r1_<?=$key?>" value="<?=$key?>" onchange="f_repeat_sel(this.value);" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p">
                                    <p class="text_dynamic" style="word-break: break-all;"><?=$val?></p>
                                </div>
                            </label>
                        </div>
                        <?php } ?>
                    </div>
                    <?php
                        }
                    }
?>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">반복 주기 선택완료</button>
                </div>
            </form>
            <script>
            $(document).ready(function() {
                <?php if($sst_repeat_json_t['r1']) { ?>
                $('#r1_<?=$sst_repeat_json_t['r1']?>').prop("checked", true);
                <?php } ?>
                <?php
                    if($sst_repeat_json_t['r2']) {
                        $r2_ex = explode(',', $sst_repeat_json_t['r2']);

                        if($r2_ex) {
                            foreach($r2_ex as $key => $val) {
                                echo "$('#r2_".$val."').prop(\"checked\", true);";
                            }
                        }
                    }
?>
            });

            function f_repeat_sel(v) {
                if (v != '3') {
                    $(".repeat_r2").each(function() {
                        $(this).prop("checked", false);
                        $(this).parent().removeClass('active');
                    });
                }
            }

            function f_switch_week(w) {
                switch (w) {
                    case '1':
                        return '일'
                        break;
                    case '2':
                        return '월'
                        break;
                    case '3':
                        return '화'
                        break;
                    case '4':
                        return '수'
                        break;
                    case '5':
                        return '목'
                        break;
                    case '6':
                        return '금'
                        break;
                    case '7':
                        return '토'
                        break;
                    default:
                        console.log('null');
                }
            }

            function f_switch_repeat(r) {
                switch (r) {
                    case '1':
                        return '반복안함'
                        break;
                    case '2':
                        return '매일'
                        break;
                    case '3':
                        return '1주마다'
                        break;
                    case '4':
                        return '매월'
                        break;
                    case '5':
                        return '매년'
                        break;
                    default:
                        console.log('null');
                }
            }

            $("#frm_schedule_repeat").validate({
                submitHandler: function() {
                    var f = document.frm_schedule_repeat;

                    var q = 0;
                    $(".repeat_r1").each(function() {
                        if ($(this).prop("checked") == true) {
                            q++;
                        }
                    });

                    if (q < 1) {
                        jalert("반복 방식을 선택해주세요.");
                        return false;
                    }

                    var json_rtn_v = '';
                    var json_rtn = '';
                    if (f.repeat_r1.value == '3') {
                        var w = 0;
                        var week_t = '';
                        var week_tv = '';
                        $(".repeat_r2").each(function() {
                            if ($(this).prop("checked") == true) {
                                w++;
                                week_t += $(this).val() + ','
                                week_tv += f_switch_week($(this).val()) + ','
                            }
                        });

                        if (w < 1) {
                            jalert("반복 요일을 선택해주세요.");
                            return false;
                        }

                        json_rtn = '{"r1":"' + f.repeat_r1.value + '","r2":"' + week_t + '"}';
                        json_rtn_v = '1주마다 ' + week_tv;
                    } else {
                        json_rtn = '{"r1":"' + f.repeat_r1.value + '","r2":""}';
                        json_rtn_v = f_switch_repeat(f.repeat_r1.value);
                    }

                    $('#sst_repeat_json').val(json_rtn);
                    $('#sst_repeat_json_v').val(json_rtn_v);
                    $('#schedule_repeat').modal('hide');

                    return false;
                },
                rules: {
                    repeat_r1: {
                        required: true,
                    },
                },
                messages: {
                    repeat_r1: {
                        required: "반복 방식을 선택해주세요.",
                    },
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            </script>
        </div>
    </div>
</div>

<!-- F-4 일정 입력 > 알림 선택  -->
<div class="modal fade" id="schedule_notice" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_notice" id="frm_schedule_notice">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">알림</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y">
                    <?php
    foreach ($arr_sst_alram as $key => $val) {
        if ($val) {
            ?>
                    <div class="line_ip pb_16 mt_16">
                        <div class="checks m-0">
                            <label>
                                <input type="radio" class="sst_alram_c" name="sst_alram_r1" id="sst_alram_r1_<?=$key?>" value="<?=$key?>" <?php if($row_sst['sst_alram'] == $key) {
                                    echo " checked";
                                } ?> />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p">
                                    <p class="text_dynamic" style="word-break: break-all;"><?=$val?></p>
                                </div>
                            </label>
                            <input type="hidden" name="sst_alram_nm_r1" id="sst_alram_nm_r1_<?=$key?>" value="<?=$val?>" />
                        </div>
                    </div>
                    <?php
        }
    }
?>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">알림 설정완료</button>
                </div>
            </form>
            <script>
            $("#frm_schedule_notice").validate({
                submitHandler: function() {
                    var f = document.frm_schedule_notice;

                    var q = 0;
                    $(".sst_alram_c").each(function() {
                        if ($(this).prop("checked") == true) {
                            q++;
                        }
                    });

                    if (q < 1) {
                        jalert("알림 방식을 선택해주세요.");
                        return false;
                    }

                    $('#sst_alram').val(f.sst_alram_r1.value);
                    $('#sst_alram_t').val($('#sst_alram_nm_r1_' + f.sst_alram_r1.value).val());
                    $('#schedule_notice').modal('hide');

                    return false;
                },
                rules: {
                    sst_alram_r1: {
                        required: true,
                    },
                },
                messages: {
                    sst_alram_r1: {
                        required: "알림 방식을 선택해주세요.",
                    },
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            </script>
        </div>
    </div>
</div>

<!-- F-4 일정 입력 > 멤버 선택  -->
<div class="modal fade" id="schedule_member" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_member" id="frm_schedule_member">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">멤버 선택</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y px-0" style="min-height:380px;" id="schedule_member_content"></div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">멤버 선택완료</button>
                </div>
            </form>
            <script>
            $("#frm_schedule_member").validate({
                submitHandler: function() {
                    var f = document.frm_schedule_member;

                    var q = 0;
                    $(".sgdt_idx_c").each(function() {
                        if ($(this).prop("checked") == true) {
                            q++;
                        }
                    });

                    if (q < 1) {
                        jalert("멤버를 선택해주세요.");
                        return false;
                    }

                    $('#sgdt_idx').val(f.sgdt_idx_r1.value);
                    $('#sgdt_idx_t').val($('#mt_nickname_r1_' + f.sgdt_idx_r1.value).val());
                    $('#schedule_member').modal('hide');

                    return false;
                },
                rules: {
                    sgdt_idx_r1: {
                        required: true,
                    },
                },
                messages: {
                    sgdt_idx_r1: {
                        required: "멤버를 선택해주세요.",
                    },
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            </script>
        </div>
    </div>
</div>

<!-- F-4 위치선택 목록  -->
<div class="modal fade" id="schedule_location" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_location" id="frm_schedule_location">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">위치선택</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y pt-0" style="height:400px;">
                    <div class="text-center py-5 border-top">
                        <div class="mx-auto"><img src="<?=CDN_HTTP?>/img/icon_location.png" style="max-width:4.9rem;"></div>
                        <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="f_modal_schedule_map();">지도에서 선택할래요<i class="xi-angle-right-min ml_19"></i></button>
                    </div>
                    <div class="bargray_fluid mx_n20"></div>

                    <div class="location_mark my_20">
                        <p class="tit_h3 fs_15 mb-4">즐겨찾는 위치</p>
                        <ul id="location_like_list_box" class="scroll_bar_y" style="min-height:30rem;">

                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">위치 선택하기</button>
                </div>
            </form>
            <script>
            function f_modal_schedule_map() {
                $('#schedule_location').modal('hide');
                setTimeout(() => {
                    var form_data = new FormData();
                    form_data.append("act", "get_schedule_map");

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
                                setTimeout(() => {
                                    $('#schedule_map').modal('show');
                                }, 100);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                }, 100);
            }

            $("#frm_schedule_location").validate({
                submitHandler: function() {
                    var f = document.frm_schedule_location;

                    var q = 0;
                    $(".slt_idx_c").each(function() {
                        if ($(this).prop("checked") == true) {
                            q++;
                        }
                    });

                    if (q < 1) {
                        jalert("위치를 선택해주세요.");
                        return false;
                    }

                    var slt_idx_r1_t = f.slt_idx_r1.value;

                    $('#slt_idx').val(slt_idx_r1_t);
                    $('#sst_location_title').val($('#slt_title_' + slt_idx_r1_t).val());
                    $('#sst_location_add').val($('#slt_add_' + slt_idx_r1_t).val());
                    $('#sst_location_lat').val($('#slt_lat_' + slt_idx_r1_t).val());
                    $('#sst_location_long').val($('#slt_long_' + slt_idx_r1_t).val());
                    if ($('#slt_title_' + slt_idx_r1_t).val()) {
                        $('#slt_idx_t').val($('#slt_title_' + slt_idx_r1_t).val());
                    } else {
                        $('#slt_idx_t').val($('#slt_add_' + slt_idx_r1_t).val());
                    }
                    $('#schedule_location').modal('hide');

                    return false;
                },
                rules: {
                    sgdt_idx_r1: {
                        required: true,
                    },
                },
                messages: {
                    sgdt_idx_r1: {
                        required: "멤버를 선택해주세요.",
                    },
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            </script>
        </div>
    </div>
</div>


<!-- F-4 일정 입력 > 엽락처 입력 -->
<div class="modal fade" id="schedule_contact" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_contact" id="frm_schedule_contact">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">연락처 입력</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y">
                    <div class="ip_wr">
                        <div class="ip_tit d-flex align-items-center justify-content-between">
                            <h5>카테고리</h5>
                        </div>
                        <input type="text" class="form-control" name="sct_category" id="sct_category" maxlength="40" oninput="maxLengthCheck(this)" placeholder="카테고리 입력">
                    </div>
                    <div class="ip_wr mt_25">
                        <div class="ip_tit">
                            <h5 class="">이름</h5>
                        </div>
                        <input type="text" class="form-control" name="sct_title" id="sct_title" maxlength="40" oninput="maxLengthCheck(this)" placeholder="홍길동">
                    </div>
                    <div class="ip_wr mt_25">
                        <div class="ip_tit">
                            <h5 class="">연락처</h5>
                        </div>
                        <input type="text" class="form-control" name="sct_hp" id="sct_hp" maxlength="40" oninput="maxLengthCheck(this)" placeholder="010-1234-1234">
                    </div>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">연락처 저장하기</button>
                </div>
            </form>
            <script>
            $("#frm_schedule_contact").validate({
                submitHandler: function() {
                    var f = document.frm_schedule_contact;

                    var form_data = new FormData();
                    form_data.append("act", "contact_input");
                    form_data.append("sct_category", f.sct_category.value);
                    form_data.append("sct_title", f.sct_title.value);
                    form_data.append("sct_hp", f.sct_hp.value);

                    $('#sct_category').val('');
                    $('#sct_title').val('');
                    $('#sct_hp').val('');

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
                            if (data == 'Y') {
                                f_contact_list();
                                setTimeout(() => {
                                    $('#schedule_contact').modal('hide');
                                }, 100);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                },
                rules: {
                    sct_category: {
                        required: true,
                    },
                    sct_title: {
                        required: true,
                    },
                    sct_hp: {
                        required: true,
                    },
                },
                messages: {
                    sct_category: {
                        required: "카테고리를 입력해주세요.",
                    },
                    sct_title: {
                        required: "이름을 입력해주세요.",
                    },
                    sct_hp: {
                        required: "연락처를 입력해주세요.",
                    },
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            </script>
        </div>
    </div>
</div>



<!-- F-6 일정삭제 -->
<div class="modal fade" id="schedule_delete" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <!-- opt_bottom_wrap 이거 넣으면 바텀시트 / modal-dialog-scrollable 필요시-->
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center py_14">일정을 삭제하시겠어요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_delete_schedule('<?=$row_sst['sst_idx']?>');">삭제하기</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- F-5 일정상세 - 연락처 목록 – 수정 -->
<div class="modal fade" id="contact_modify" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title line1_text fs_20 fw_700">연락처 수정</p>
                <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
            </div>
            <div class="modal-body scroll_bar_y border-top">
                <form class="">
                    <div class="ip_wr mb-4">
                        <div class="ip_tit d-flex justify-content-between">
                            <h5 class="">카테고리</h5>
                            <button type="button" class="btn btn-link btn-sm fc_gray_500 h-auto p-0 fs_12"><u>삭제하기</u></button>
                        </div>
                        <input type="text" class="form-control" placeholder="카테고리 입력" value="기사아저씨">
                    </div>
                    <div class="bargray_fluid mx_n20"></div>

                    <div class="py_20 border-bottom contact_item">
                        <div class="ip_wr">
                            <div class="ip_tit d-flex justify-content-between">
                                <h5 class="">이름</h5>
                                <button type="button" class="btn btn-link btn-sm fc_gray_500 h-auto p-0 fs_12"><u>삭제하기</u></button>
                            </div>
                            <input type="text" class="form-control" placeholder="홍길동" value="홍길동">
                        </div>
                        <div class="ip_wr mt_25">
                            <div class="ip_tit">
                                <h5 class="">연락처</h5>
                            </div>
                            <input type="text" class="form-control" placeholder="010-1234-1234" value="010-1234-1234">
                        </div>
                    </div>

                    <div class="py_20 border-bottom contact_item">
                        <div class="ip_wr">
                            <div class="ip_tit d-flex justify-content-between">
                                <h5 class="">이름</h5>
                                <button type="button" class="btn btn-link btn-sm fc_gray_500 h-auto p-0 fs_12"><u>삭제하기</u></button>
                            </div>
                            <input type="text" class="form-control" placeholder="홍길동" value="">
                        </div>
                        <div class="ip_wr mt_25">
                            <div class="ip_tit">
                                <h5 class="">연락처</h5>
                            </div>
                            <input type="text" class="form-control" placeholder="010-1234-1234" value="">
                        </div>
                    </div>
                    <!-- 모달 하단에 연락처 추가하기 버튼 누르면 .contact_item 채로 추가-->


                </form>
            </div>
            <div class="modal-footer border-0 p-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">연락처 추가하기</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0">연락처 수정하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pin_cont {
    position: absolute;
    top: 2rem;
}
</style>
<div class="modal fade" id="schedule_map" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content" id="schedule_map_content">
            <form method="post" name="frm_schedule_map" id="frm_schedule_map">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">위치 선택</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y" style="height:70vh;">
                    <div id="naver_map" style="width:100%;height:65vh;"></div>
                    <div class="px-0 py-0 map_wrap d-none-temp" id="map_info_box">
                        <div class="map_wrap_re">
                            <div class="pin_cont bg-white pt_20 px_16 pb_16 rounded_10 ml-2 mr-2">
                                <ul>
                                    <li class="d-flex">
                                        <div class="name flex-fill">
                                            <span class="fs_12 fw_600 text-primary">선택한 위치</span>
                                            <div class="fs_14 fw_600 text_dynamic mt-1 line_h1_3" id="location_add"></div>
                                        </div>
                                        <button type="button" class="mark_btn" id="btn_location_like" onclick="f_location_like();"></button>
                                    </li>
                                    <li class="d-flex mt-3">
                                        <div class="name flex-fill">
                                            <span class="fs_12 fw_600 text-primary">별칭</span>
                                            <input class="fs_14 fw_600 fc_gray_600 form-control text_dynamic mt-1 line_h1_3 loc_nickname" name="slt_title" id="slt_title" value="" placeholder="별칭을 입력해주세요">
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">위치 선택완료</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function f_location_like_delete(i) {
    $.alert({
        title: '',
        type: "blue",
        typeAnimated: true,
        content: '즐겨찾는 위치를 삭제하시겠습니까?',
        buttons: {
            confirm: {
                btnClass: "btn-default btn-lg btn-block",
                text: "확인",
                action: function() {
                    var form_data = new FormData();
                    form_data.append("act", "map_location_like_delete");
                    form_data.append("slt_idx", i);

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
                            if (data == 'Y') {
                                f_location_like_list();
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                },
            },
        },
    });

    return false;
}


function f_location_like() {
    if ($('#slt_title').val() == '') {
        jalert('별칭을 입력바랍니다.');
        return false;
    }

    $.alert({
        title: '',
        type: "blue",
        typeAnimated: true,
        content: '위치를 등록하시겠습니까?',
        buttons: {
            confirm: {
                btnClass: "btn-default btn-lg btn-block",
                text: "확인",
                action: function() {
                    var form_data = new FormData();
                    form_data.append("act", "map_location_input");
                    form_data.append("slt_title", $('#slt_title').val());
                    form_data.append("slt_add", $('#sst_location_add').val());
                    form_data.append("slt_lat", $('#sst_location_lat').val());
                    form_data.append("slt_long", $('#sst_location_long').val());

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
                                jalert('등록되었습니다.');
                                $('#btn_location_like').addClass('on');
                                $('#slt_title').attr('readonly', true);
                                $('#slt_idx').val(data);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                },
            },
        },
    });
}

(function($) {
    'use strict';
    $(function() {
        var st_lat;
        var st_lng;

        st_lat = '<?php echo $_SESSION['_mt_lat'] == "" ? 37.5665 : $_SESSION['_mt_lat']; ?>';
        st_lng = '<?php echo $_SESSION['_mt_long'] == "" ? 126.9780 : $_SESSION['_mt_long']; ?>';

        var map = new naver.maps.Map("naver_map", {
            center: new naver.maps.LatLng(st_lat, st_lng),
            zoom: 19,
            mapTypeControl: false
        });

        var marker = new naver.maps.Marker({
            position: new naver.maps.LatLng(st_lat, st_lng),
            map: map
        });

        map.setCursor('pointer');

        naver.maps.Event.addListener(map, 'click', function(e) {
            searchCoordinateToAddress(e.coord);
        });

        function initGeocoder() {
            map.addListener("click", function(e) {
                searchCoordinateToAddress(e.coord);
            });
            return false;
        }

        function searchCoordinateToAddress(latlng) {
            // infoWindow.close();

            naver.maps.Service.reverseGeocode({
                    coords: latlng,
                    orders: [naver.maps.Service.OrderType.ADDR, naver.maps.Service.OrderType.ROAD_ADDR].join(","),
                },
                function(status, response) {
                    if (status === naver.maps.Service.Status.ERROR) {
                        return alert("Something Wrong!");
                    }

                    var items = response.v2.results,
                        address = "",
                        htmlAddresses = [];

                    for (var i = 0, ii = items.length, item, addrType; i < ii; i++) {
                        item = items[i];
                        address = makeAddress(item) || "";
                        if (item.name == "roadaddr") {
                            addrType = "[도로명 주소]";
                        } else {
                            addrType = "[지번 주소]";
                        }

                        htmlAddresses.push(i + 1 + ". " + addrType + " " + address);
                    }

                    if (latlng._lat && latlng._lng) {
                        htmlAddresses.push("[GPS] 위도:" + latlng._lat + ", 경도: " + latlng._lng);
                    }

                    $('#location_add').html(address);

                    $('#sst_location_add').val(address);
                    $('#sst_location_lat').val(latlng._lat);
                    $('#sst_location_long').val(latlng._lng);

                    // infoWindow.setContent(['<div style="padding:10px;min-width:200px;line-height:150%;">', '<h4 style="margin-top:5px;">검색 좌표</h4><br />', htmlAddresses.join("<br />"), "</div>"].join("\n"));
                    // infoWindow.open(map, latlng);

                    $('#map_info_box').removeClass('d-none-temp');
                }
            );
        }

        function makeAddress(item) {
            if (!item) {
                return;
            }

            var name = item.name,
                region = item.region,
                land = item.land,
                isRoadAddress = name === "roadaddr";

            var sido = "",
                sigugun = "",
                dongmyun = "",
                ri = "",
                rest = "";

            if (hasArea(region.area1)) {
                sido = region.area1.name;
            }

            if (hasArea(region.area2)) {
                sigugun = region.area2.name;
            }

            if (hasArea(region.area3)) {
                dongmyun = region.area3.name;
            }

            if (hasArea(region.area4)) {
                ri = region.area4.name;
            }

            if (land) {
                if (hasData(land.number1)) {
                    if (hasData(land.type) && land.type === "2") {
                        rest += "산";
                    }

                    rest += land.number1;

                    if (hasData(land.number2)) {
                        rest += "-" + land.number2;
                    }
                }

                if (isRoadAddress === true) {
                    if (checkLastString(dongmyun, "면")) {
                        ri = land.name;
                    } else {
                        dongmyun = land.name;
                        ri = "";
                    }

                    if (hasAddition(land.addition0)) {
                        rest += " " + land.addition0.value;
                    }
                }
            }

            return [sido, sigugun, dongmyun, ri, rest].join(" ");
        }

        function hasArea(area) {
            return !!(area && area.name && area.name !== "");
        }

        function hasData(data) {
            return !!(data && data !== "");
        }

        function checkLastString(word, lastString) {
            return new RegExp(lastString + "$").test(word);
        }

        function hasAddition(addition) {
            return !!(addition && addition.value);
        }

        naver.maps.onJSContentLoaded = initGeocoder;
    });
})(jQuery);

$("#frm_schedule_map").validate({
    submitHandler: function() {
        var f = document.frm_schedule_map;

        if ($('#sst_location_add').val() == '') {
            jalert('위치를 선택해주세요.');
            return false;
        }

        $('#slt_idx_t').val($('#sst_location_add').val());
        $('#schedule_map').modal('hide');

        return false;
    },
    rules: {
        sst_location_add: {
            required: true,
        },
    },
    messages: {
        sst_location_add: {
            required: "위치를 선택해주세요.",
        },
    },
    errorPlacement: function(error, element) {
        $(element)
            .closest("form")
            .find("span[for='" + element.attr("id") + "']")
            .append(error);
    },
});
</script>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>