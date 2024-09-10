<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '2';
// if ($_SESSION['_mt_idx'] == '') {
//     alert(translate('로그인이 필요합니다.', $userLang), './login', '');
// } else {
//     // 앱토큰값이 DB와 같은지 확인
//     $DB->where('mt_idx', $_SESSION['_mt_idx']);
//     $mem_row = $DB->getone('member_t');
//     if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
//         alert(translate('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', $userLang), './logout');
//     }
// }
if ($_GET['sst_idx']) { // 수정
    $DB->where('sst_idx', $_GET['sst_idx']);
    $sst_row = $DB->getone('smap_schedule_t');

    if ($sst_row['sgdt_idx'] > 0) { // 오너 또는 리더가 그룹원 일정 등록했을 경우
        $DB->where('sgdt_idx', $sst_row['sgdt_idx']);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $sgdt_row = $DB->getone('smap_group_detail_t');

        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $get_mem_row = $DB->getone('member_t');
    } else { // 나의 일정
        $DB->where('mt_idx', $sst_row['mt_idx']);
        $DB->where('sgt_show', 'Y');
        $sgt_row = $DB->getone('smap_group_t');

        $DB->where('sgt_idx', $sgt_row['sgt_idx']);
        $DB->where('mt_idx', $sst_row['mt_idx']);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $sgdt_row = $DB->getone('smap_group_detail_t');

        $DB->where('mt_idx', $sst_row['mt_idx']);
        $get_mem_row = $DB->getone('member_t');
    }
    if ($get_mem_row['mt_nickname']) {
        $_SUB_HEAD_TITLE = $get_mem_row['mt_nickname'] . translate("의 일정 입력", $userLang);
    } else {
        $_SUB_HEAD_TITLE = $get_mem_row['mt_name'] . translate("의 일정 입력", $userLang);
    }
} else { // 입력
    if ($_GET['sgdt_idx']) { //그룹원 일정추가할때
        $DB->where('sgdt_idx', $_GET['sgdt_idx']);
        $DB->where('sgdt_show', 'Y');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $sgdt_row = $DB->getone('smap_group_detail_t');

        $DB->where('mt_idx', $sgdt_row['mt_idx']);
        $get_mem_row = $DB->getone('member_t');
    } else {
        $DB->where('mt_idx', $_GET['mt_idx']);
        $DB->where('sgt_show', 'Y');
        $sgt_row = $DB->getone('smap_group_t');
        if ($sgt_row['sgt_idx']) { //오너일 경우
            $DB->where('sgt_idx', $sgt_row['sgt_idx']);
            $DB->where('mt_idx', $_GET['mt_idx']);
            $DB->where('sgdt_show', 'Y');
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
            $sgdt_row = $DB->getone('smap_group_detail_t');
        } else { // 리더나 그룹원일 경우
            $DB->where('mt_idx', $_GET['mt_idx']);
            $DB->where('sgdt_show', 'Y');
            $DB->where('sgdt_discharge', 'N');
            $DB->where('sgdt_exit', 'N');
            $sgdt_row = $DB->getone('smap_group_detail_t');
        }
        $DB->where('mt_idx', $_GET['mt_idx']);
        $get_mem_row = $DB->getone('member_t');
    }
    if ($get_mem_row['mt_nickname']) {
        $_SUB_HEAD_TITLE = $get_mem_row['mt_nickname'] . translate("의 일정 입력", $userLang);
    } else {
        $_SUB_HEAD_TITLE = $get_mem_row['mt_name'] . translate("의 일정 입력", $userLang);
    }
}
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', $userLang), './logout');
    }
}

// 그룹에 속해있는지 확인
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgdt_discharge', 'N');
$DB->where('sgdt_exit', 'N');
$DB->where('sgdt_show', 'Y');
$group_chk = $DB->get('smap_group_detail_t');
$group_count = count($group_chk);
if ($group_count < 1) { // 그룹이 없을 경우
    alert(translate('그룹을 먼저 생성해주세요!', $userLang), 'back');
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
// 숫자가 1자리일 경우 앞에 0을 붙여주는 로직 추가
$numMonth2 = str_pad($numMonth2, 2, '0', STR_PAD_LEFT); 
$numYear = date('Y', $tt);
$prevMonth = date('Y-m-01', strtotime($sdate . " -" . $dayOfWeek . "days"));
$nextMonth = date('Y-m-01', strtotime($sdate . " +" . $dayOfWeek . "days"));
$calendar_date_title = $numYear . "." . " " . $numMonth2;
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
        $time_now_t = translate("오후", $userLang) . " " . get_pad($hour) . ":" . $min;
    } else {
        if ($hour == 12 && $min == '00') {
            $time_now_t = translate("정오", $userLang) . " " . get_pad($hour) . ":" . $min;
        } elseif ($hour == 0 && $min == '00') {
            $time_now_t = translate("자정", $userLang) . " " . get_pad($hour) . ":" . $min;
        } else {
            $time_now_t = translate("오전", $userLang) . " " . get_pad($hour) . ":" . $min;
        }
    }

    return $time_now_t;
}

//오너인 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$row = $DB->getone('smap_group_t', 'count(*) as cnt');
$sgt_cnt = $row['cnt'];

//리더인 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('(sgdt_owner_chk = "N" AND sgdt_leader_chk = "Y")');
$DB->where('sgdt_show', 'Y');
$row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
$sgdt_cnt = $row['cnt'];


$readonly = '';
$disable = '';
$dnone = '';
if ($_GET['sst_idx']) {
    $DB->where('sst_idx', $_GET['sst_idx']);
    $DB->where('sst_show', 'Y');
    $row_sst = $DB->getone('smap_schedule_t');
    $readonly = "readonly";
    $disable = "disable";
    $dnone = "d-none";
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
    if ($row_sst['sst_update_chk']) {
        // 나의 권한 찾기
        if ($row_sst['sgt_idx'] > 0) {
            $DB->where('sgt_idx', $row_sst['sgt_idx']);
            $DB->where('mt_idx', $_SESSION['_mt_idx']);
            $DB->where('sgdt_show', 'Y');
            $DB->where('sgdt_exit', 'N');
            $row_sgdt = $DB->getone('smap_group_detail_t');
            $owner_chk = $row_sgdt['sgdt_owner_chk'];
            $leader_chk = $row_sgdt['sgdt_leader_chk'];
        } else {
            if ($sgt_cnt > 0) {
                $owner_chk = 'Y';
            } else {
                $owner_chk = 'N';
            }
            if ($sgdt_cnt > 0) {
                $leader_chk = 'Y';
            } else {
                $leader_chk = 'N';
            }
        }
        // 쉼표(,)를 기준으로 값을 분리하여 배열로 저장
        $checked_values = explode(',', $row_sst['sst_update_chk']);
        foreach ($checked_values as $value) {
            if ($value == 1 && $owner_chk == 'Y') {
                $readonly = '';
                $disable = '';
                $dnone = '';
            }
            if ($value == 2 && $leader_chk == 'Y') {
                $readonly = '';
                $disable = '';
                $dnone = '';
            }
            if ($value == 3 && $owner_chk == 'N' && $leader_chk == 'N') {
                $readonly = '';
                $disable = '';
                $dnone = '';
            }
        }
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

            headerTitle: "<?= translate("선택", $userLang) ?>",
            setButton: "<?=  translate("설정", $userLang) ?>",
            clearButton: "<?=  translate("지우기", $userLang) ?>",
            nowButton: "<?=  translate("지금", $userLang) ?>",
            cancelButton: "<?=  translate("취소", $userLang) ?>",
            dateSwitch: "<?=  translate("날짜", $userLang) ?>",
            timeSwitch: "<?=  translate("시간", $userLang) ?>",

            // DateTime

            veryShortDays: "Su_Mo_Tu_We_Th_Fr_Sa".split("_"),
            shortDays: "Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),
            fullDays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
            shortMonths: "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),
            fullMonths: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
            numbers: "0_1_2_3_4_5_6_7_8_9".split("_"),
            meridiem: {
                a: ["a", "p"],
                aa: ["<?=  translate("오전", $userLang) ?>", "<?=  translate("오후", $userLang) ?>"],
                A: ["A", "P"],
                AA: ["<?=  translate("오전", $userLang) ?>", "<?=  translate("오후", $userLang) ?>"]
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
    #wrap {
        min-height: 100%;
        height: 100%;
        position: relative;
        overflow-y: auto;
    }

    #layoutViewport {
        position: fixed;
        width: 100%;
        height: 100%;
        visibility: hidden;
        background: #FAF2CE;
    }

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

    .form-none {
        height: auto;
        min-height: 2.4rem;
        max-height: 30rem;
        text-wrap: wrap;
        line-height: 1.5;
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
            <input type="hidden" name="sst_pidx" id="sst_pidx" value="<?= $row_sst['sst_pidx'] ?>" />
            <input type="hidden" name="sgt_idx" id="sgt_idx" value="<?= $_GET['sgdt_idx'] ? $sgdt_row['sgt_idx'] : $sgdt_row['sgt_idx'] ?>" />
            <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $_GET['sgdt_idx'] ? $_GET['sgdt_idx'] : $sgdt_row['sgdt_idx'] ?>" />
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
                <input type="text" class="form-custom" name="sst_title" id="sst_title" value="<?= $row_sst['sst_title'] ?>" maxlength="30" data-length-id="sst_title_cnt" oninput="maxLengthCheck(this)" placeholder="<?=  translate('일정 내용을 입력해주세요.', $userLang); ?>" <?= $readonly ?> <?= $disable ?>>
                <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_title_cnt">0</span>/15)</p>
            </div>

            <div class="line_ip_box border rounded-lg px_20 py_20 mt_20">
                <div class="line_ip">
                    <div class="row justify-content-between align-items-center">
                        <h5 class="col col-auto"><?= translate('하루 종일', $userLang); ?></h5>
                        <div class="col">
                            <div class="custom-switch ml-auto">
                                <input type="checkbox" class="custom-control-input" name="sst_all_day" id="sst_all_day" value="Y" <?php if ($row_sst['sst_all_day'] == 'Y') {
                                                                                                                                        echo " checked";
                                                                                                                                    } ?> <? if (!$readonly) {
                                                                                                                                                echo 'onchange="f_all_day();"';
                                                                                                                                            } ?> />
                                <label class="custom-control-label" <? if (!$readonly) {
                                                                        echo 'for="sst_all_day"';
                                                                    } ?>></label>
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
                                    <a href="javascript:;" onclick="f_calendar_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="<?=  translate("월 선택 아이콘", $userLang); ?>" style="width:1.6rem; "></a>
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
                            <li class="sun"><?=  translate('일', $userLang); ?></li>
                            <li><?=  translate('월', $userLang); ?></li>
                            <li><?=  translate('화', $userLang); ?></li>
                            <li><?=  translate('수', $userLang); ?></li>
                            <li><?=  translate('목', $userLang); ?></li>
                            <li><?=  translate('금', $userLang); ?></li>
                            <li class="sat"><?=  translate('토', $userLang); ?></li>
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
                            "applyLabel": "<?= translate("적용", $userLang) ?>",
                            "cancelLabel": "<?= translate("닫기", $userLang) ?>",
                            "fromLabel": "From",
                            "toLabel": "To",
                            "customRangeLabel": "Custom",
                            "weekLabel": "W",
                            "daysOfWeek": [
                                "<?= translate("일", $userLang) ?>",
                                "<?= translate("월", $userLang) ?>",
                                "<?= translate("화", $userLang) ?>",
                                "<?= translate("수", $userLang) ?>",
                                "<?= translate("목", $userLang) ?>",
                                "<?= translate("금", $userLang) ?>",
                                "<?= translate("토", $userLang) ?>"
                            ],
                            "monthNames": [
                                "1" + "<?= translate("월", $userLang) ?>",
                                "2" + "<?= translate("월", $userLang) ?>",
                                "3" + "<?= translate("월", $userLang) ?>",
                                "4" + "<?= translate("월", $userLang) ?>",
                                "5" + "<?= translate("월", $userLang) ?>",
                                "6" + "<?= translate("월", $userLang) ?>",
                                "7" + "<?= translate("월", $userLang) ?>",
                                "8" + "<?= translate("월", $userLang) ?>",
                                "9" + "<?= translate("월", $userLang) ?>",
                                "10" + "<?= translate("월", $userLang) ?>",
                                "11" + "<?= translate("월", $userLang) ?>",
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
<!-- <div class="swiper-pagination"></div> -->
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
<!-- <div class="swiper-pagination"></div> -->
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
<!-- <div class="swiper-pagination"></div> -->
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
<!-- <div class="swiper-pagination"></div> -->
</div>
</div>

<script>
$(document).ready(function() {
    datetime_chk();
});


function get_time_format(hh_data, mm_data) {
    if (hh_data > 12) {
        var hh_t = "<?= translate("오후", $userLang) ?>";
        hh_data = hh_data - 12;
        hh_data = get_pad(hh_data);
    } else {
        if (hh_data == 12 && mm_data == 0) {
            var hh_t = "<?= translate('정오', $userLang) ?>";
        } else if (hh_data == 0 && mm_data == 0) {
            var hh_t = "<?= translate('자정', $userLang) ?>";
        } else {
            var hh_t = "<?= translate('오전', $userLang) ?>";
        }
    }

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
<? if ($readonly) {
} else { ?>
this.blur();
e.preventDefault();
btn_class_active();
setTimeout(() => {
    $('#btn_sdate').addClass('btn_active');
}, 100);
setTimeout(() => {
    f_open_cal('stime');
}, 100);
<? } ?>
// ttcc();
});
btn_edate_b.addEventListener('click', (e) => {
<? if ($readonly) {
} else { ?>
this.blur();
e.preventDefault();
btn_class_active();
setTimeout(() => {
    $('#btn_edate').addClass('btn_active');
}, 100);
setTimeout(() => {
    f_open_cal('etime');
}, 100);
<? } ?>

// ttcc();
});

const btn_stime_b = document.getElementById("btn_stime");
const btn_etime_b = document.getElementById("btn_etime");

btn_stime_b.addEventListener('click', (e) => {
<? if ($readonly) {
} else { ?>
this.blur();
e.preventDefault();
btn_class_active();
setTimeout(() => {
    $('#btn_stime').addClass('btn_active');
}, 100);
f_sopen_time('stime');
<? } ?>
// ttcc();
});
btn_etime_b.addEventListener('click', (e) => {
<? if ($readonly) {
} else { ?>
this.blur();
e.preventDefault();
btn_class_active();
setTimeout(() => {
    $('#btn_etime').addClass('btn_active');
}, 100);
f_eopen_time('etime');
<? } ?>

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
$('#pick_sdate').val(dct);
} else {
$('#pick_edate').val(dct);
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
var w = "<?= translate("일월화수목금토", $userLang) ?>".charAt(cday.getUTCDay());

if (y == y2) {
var rtn = m + "<?= translate("월", $userLang) ?>" + " " + d + "<?= translate("일", $userLang) ?>" + " (" + w + ")";
} else {
var rtn = y + "<?= translate("년", $userLang) ?>" + " " + m + "<?= translate("월", $userLang) ?>" + " " + d + "<?= translate("일", $userLang) ?>" + " (" + w + ")";
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

console.log("dStartD " + dStartD);

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
// console.log(csdt > cedt);

if (csdt == cedt) { //시작 == 마감
    var usd = (csdt.getTime() / 1000);
    var usc = Unix_timestamp(usd + 3600);
    var syd = new Date(usc);

    set_date_time(sd, ed, st, et);
} else {
    if (csdt < cedt) { //시작 < 마감
        set_date_time(sd, ed, st, et);
    } else { //시작 > 마감
        console.log("pd pt " + pd + " " + pt);
        if (pt == 'stime') { //시작 설정시
            var usd = (csdt.getTime() / 1000);
            var usc = Unix_timestamp(usd + 3600);
            var syd = new Date(usc);

            console.log(ed + et);

            set_date_time(sd, ed, st, et);
        } else { //마감 설정시
            var ued = (cedt.getTime() / 1000);
            var uec = Unix_timestamp(ued - 3600);
            var eyd = new Date(uec);

            set_date_time(sd, ed, st, et);
        }
    }
}
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
<h5><?=  translate('시작', $userLang); ?></h5>
</div>
<div class="col">

<!-- value 안에 데이터 넣어 주세요 -->
</div>
</div>
</div>
<div class="line_ip mt_25 d-none-temp">
<div class="row">
<div class="col col-auto line_tit">
<h5><?=  translate('종료', $userLang); ?></h5>
</div>
<div class="col">

<!-- value 안에 데이터 넣어 주세요 -->
</div>
</div>
</div>
<!-- 반복 -->
<div class="line_ip mt_25">
<div class="row">
<div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_repeat.png" alt="<?=  translate('반복 아이콘', $userLang); ?>"></div>
<div class="col">
<input type="hidden" name="sst_repeat_json" id="sst_repeat_json" value='<?= $row_sst['sst_repeat_json'] ?>' />
<input type="text" readonly class="form-none cursor_pointer" name="sst_repeat_json_v" id="sst_repeat_json_v" placeholder="<?=  translate('반복', $userLang); ?>" value="<?= $row_sst['sst_repeat_json_v'] ?>" <? if (!$readonly) { /*echo 'data-toggle="modal" data-target="#schedule_repeat"';*/
                                                                                                                                                                                echo 'onclick="f_schedule_repeat_modal()"';
                                                                                                                                                                            } ?>>
<!-- value 안에 데이터 넣어 주세요 -->
</div>
</div>
</div>
</div>
<!-- <div class="line_ip mt_25 d-none">
<div class="row">
<div class="col col-auto line_tit"><img src="<?= CDN_HTTP ?>/img/ip_ic_member.png" alt="멤버 아이콘"></div>
<div class="col">
<input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $row_sst['sgdt_idx'] ?>" />
<input type="text" readonly class="form-none cursor_pointer" name="sgdt_idx_t" id="sgdt_idx_t" placeholder="멤버 선택" value="<?= $row_sst['sgdt_idx_t'] ?>" onclick="f_modal_schedule_member();">
</div>
</div>
</div> -->
<!-- 알림 -->
<div class="mt_25">
<div class="row line_ip mx-0 pl-0">
<div class="col col-auto line_tit pl-0"><img src="<?= CDN_HTTP ?>/img/ip_ic_notice.png" alt="<?=  translate('알림 아이콘', $userLang); ?>"></div>
<div class="col pl-0" <? if (!$readonly) {
                    echo 'onclick="openArmSettingModal()"';
                } ?>>
<input type="hidden" name="sst_schedule_alarm_chk" id="sst_schedule_alarm_chk" value="<?= $row_sst['sst_schedule_alarm_chk'] ?>" />
<input type="hidden" name="sst_pick_type" id="sst_pick_type" value="<?= $row_sst['sst_pick_type'] ?>" />
<input type="hidden" name="sst_pick_result" id="sst_pick_result" value="<?= $row_sst['sst_pick_result'] ?>" />
<!-- <input type="text" readonly class="form-none cursor_pointer" name="sst_alram_t" id="sst_alram_t" placeholder="알림" value="<?= $row_sst['sst_alram_t'] ?>" data-toggle="modal" data-target="#schedule_notice"> -->
<input type="text" readonly class="form-none cursor_pointer" name="sst_alram_t" id="sst_alram_t" placeholder="<?=  translate('알림', $userLang); ?>" value="<?= $row_sst['sst_alram_t'] ? $row_sst['sst_alram_t'] : $_GET['sst_pick_result'] . $pick_type ?>">
<!-- value 안에 데이터 넣어 주세요 -->
</div>
</div>
<div class="row mx-0 pl-0">
<div class="col col-auto line_tit pl-0"><span></span></div>
<div class="col px-0 ">
<p class="fc_gray_700 fs_12 mt-2 line_h1_3"><?=  translate('일정 관련 알림 및 그룹원의 위치 변동 알림 설정을 입력해주세요.', $userLang); ?></p>
</div>
</div>
</div>
<!-- 알림설정 모달 창 -->
<div class="modal fade" id="armSettingModal" tabindex="-1">
<div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<p class="modal-title line1_text fs_20 fw_700"><?=  translate('알림', $userLang); ?></p>
<div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
</div>
<div class="modal-body scroll_bar_y py-0">
<!-- arm_setting 페이지를 띄울 iframe -->
<iframe id="armSettingFrame" frameborder="0" width="100%" height="500px"></iframe>
</div>
</div>
</div>
</div>
<script>
// arm_setting 페이지를 모달로 띄우는 함수
function openArmSettingModal() {
var sst_schedule_alarm_chk = $('#sst_schedule_alarm_chk').val();
var sst_pick_type = $('#sst_pick_type').val();
var sst_pick_result = $('#sst_pick_result').val();

var armSettingURL = './arm_setting?sst_schedule_alarm_chk=' + sst_schedule_alarm_chk + '&sst_pick_type=' + sst_pick_type + '&sst_pick_result=' + sst_pick_result;
// console.log(armSettingURL);
// 추가 데이터 필요시 추가
// 모달 열기
$('#armSettingModal').modal('show');
// iframe에 arm_setting 페이지 로드
$('#armSettingFrame').attr('src', armSettingURL);
}

// arm_setting 페이지에서 값이 전달될 때 실행되는 함수
function onArmSettingComplete(data) {
var timestamp;
var pick_result;
if (data.pick_type === 'day') {
timestamp = data.pick_result + "<?= translate('일 전', $userLang) ?>";
} else if (data.pick_type === 'minute') {
timestamp = data.pick_result + "<?= translate('분 전', $userLang) ?>";
} else if (data.pick_type === 'hour') {
timestamp = data.pick_result + "<?= translate('시간 전', $userLang) ?>";
} else {
timestamp = "<?= translate('알림설정 안함', $userLang) ?>";
}
if (data.pick_result) {
pick_result = data.pick_result;
} else {
pick_result = '';
}
// 전달받은 값으로 필요한 처리 수행
// $('#sst_location_alarm').val(data.sst_location_alarm);
$('#sst_schedule_alarm_chk').val(data.sst_schedule_alarm_chk);
$('#sst_pick_type').val(data.pick_type);
$('#sst_pick_result').val(pick_result);
$('#sst_alram_t').val(timestamp);
// 이후 필요한 처리 추가 가능
closeArmSettingModal();
}
// 모달을 닫는 함수
function closeArmSettingModal() {
$('#armSettingModal').modal('hide');
}
</script>
<!-- 장소 -->
<div class="mt_25">
<div class="row line_ip mx-0 pl-0">
<div class="col col-auto line_tit pl-0"><img src="<?= CDN_HTTP ?>/img/ip_ic_location.png" alt="<?=  translate('위치 아이콘', $userLang); ?>"></div>
<div class="col pl-0">
<div class="d-flex align-items-center">
<!-- <span class="text-primary mr_12">KT&G</span> -->
<!-- 별칭 출력 -->
<input type="text" readonly class="form-none cursor_pointer flex-fill" name="slt_idx_t" id="slt_idx_t" placeholder="<?=  translate('장소', $userLang); ?>" value="<?= $row_sst['slt_idx_t'] ?>" <? if (!$readonly) {
                                                                                                                                                                    echo 'onclick="f_modal_schedule_location();"';
                                                                                                                                                                } ?>>
</div>
<!-- value 안에 데이터 넣어 주세요 -->
<input type="hidden" name="slt_idx" id="slt_idx" value="<?= $row_sst['slt_idx'] ?>" />
<input type="hidden" name="sst_location_alarm" id="sst_location_alarm" value="<?= $row_sst['sst_location_alarm'] ? $row_sst['sst_location_alarm'] : '4' ?>" />
<input type="hidden" name="sst_location_title" id="sst_location_title" value="<?= $row_sst['sst_location_title'] ?>" />
<input type="hidden" name="sst_location_add" id="sst_location_add" value="<?= $row_sst['sst_location_add'] ?>" />
<input type="hidden" name="sst_location_lat" id="sst_location_lat" value="<?= $row_sst['sst_location_lat'] ?>" />
<input type="hidden" name="sst_location_long" id="sst_location_long" value="<?= $row_sst['sst_location_long'] ?>" />
</div>
</div>
<div class="row mx-0 pl-0">
<div class="col col-auto line_tit pl-0"><span></span></div>
<div class="col px-0">
<p class="fc_gray_700 fs_12 mt-2 line_h1_3"><?=  translate('일정이 진행될 장소를 입력해주세요.', $userLang); ?></p>
</                   div>
                </div>
            </div>
            <!-- 준비물 -->
            <div class="mt_25">
                <div class="row line_ip mx-0 pl-0">
                    <div class="col col-auto line_tit pl-0"><img src="<?= CDN_HTTP ?>/img/ip_ic_material.png" alt="<?=  translate('준비물 아이콘', $userLang) ?> ?>"></div>
                    <div class="col pl-0">
                        <!-- <input type="text" class="form-none txt-cnt" name="sst_supplies" id="sst_supplies" maxlength="100" data-length-id="sst_supplies_cnt" oninput="maxLengthCheck(this)" placeholder="준비물" value="<?= $row_sst['sst_supplies'] ?>" <?= $readonly ?> <?= $disable ?>> -->
                        <textarea class="form-none line_h1_4 txt-cnt" rows="1" name="sst_supplies" id="sst_supplies" maxlength="100" data-length-id="sst_supplies_cnt" oninput="maxLengthCheck(this)" placeholder="<?=  translate('준비물', $userLang); ?>" <?= $readonly ?> <?= $disable ?>><?= $row_sst['sst_supplies'] ?></textarea>
                    </div>
                    <!-- value 안에 데이터 넣어 주세요 -->
                </div>
                <div class="row mx-0 pl-0">
                    <div class="col col-auto line_tit pl-0"><span></span></div>
                    <div class="col px-0 d-flex justify-content-between align-items-center">
                        <p class="fc_gray_700 fs_12 mt-2 line_h1_3"><?=  translate('일정 진행에 필요한 준비물을 입력해주세요.', $userLang); ?></p>
                        <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_supplies_cnt">0</span>/100)</p>
                    </div>
                </div>
            </div>
            <!-- 메모 -->
            <div class="mt_25">
                <div class="row line_ip mx-0 pl-0">
                    <div class="col col-auto line_tit pl-0"><img src="<?= CDN_HTTP ?>/img/ip_ic_memo.png" alt="<?=  translate('메모 아이콘', $userLang); ?>"></div>
                    <div class="col pl-0">
                        <textarea class="form-none line_h1_4 txt-cnt" rows="1" name="sst_memo" id="sst_memo" maxlength="500" data-length-id="sst_memo_cnt" oninput="maxLengthCheck(this)" placeholder="<?=  translate('메모', $userLang); ?>" <?= $readonly ?> <?= $disable ?>><?= $row_sst['sst_memo'] ?></textarea>
                    </div>
                </div>
                <div class="row mx-0 pl-0">
                    <div class="col col-auto line_tit pl-0"><span></span></div>
                    <div class="col px-0 d-flex justify-content-between align-items-center">
                        <p class="fc_gray_700 fs_12 mt-2 line_h1_3"><?=  translate('일정에 대한 추가 정보나 메모를 작성해주세요.', $userLang); ?></p>
                        <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="sst_memo_cnt">0</span>/500)</p>
                    </div>
                </div>
            </div>
            <!-- 연락처 -->
            <div class="mt_25">
                <div class="row line_ip mx-0 pl-0">
                    <div class="col col-auto line_tit pl-0"><img src="<?= CDN_HTTP ?>/img/ip_ic_contact.png" alt="<?=  translate('연락처 아이콘', $userLang) ?> ?>"></div>
                    <div class="col pl-0">
                        <input type="text" readonly class="form-none cursor_pointer" placeholder="<?=  translate('연락처를 입력해주세요.', $userLang) ?>" value="" <? if (!$readonly) {
                                                                                                                                echo 'data-toggle="modal"';
                                                                                                                            } ?> data-target="#schedule_contact">
                    </div>
                </div>
                <div class="row mx-0">
                    <div class="col col-auto line_tit pl-0"><span></span></div>
                    <div class="col px-0">
                        <p class="fc_gray_700 fs_12 mt-2 line_h1_3"><?=  translate('연락처를 입력해주세요.', $userLang) ?></p>
                    </div>
                </div>
                <!-- 연락처미입력시 ↑-->
                <div class="row mx-0">
                    <div class="col col-auto line_tit pl-0"><span></span></div>
                    <div class="col px-0">
                        <div class="contact_group fs_15 fc_gray_800 fw_600 mt-3">
                            <ul id="contact_list_box" class="mt-3">
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- 연락처입력시 ↑-->
            </div>
            <!-- 수정권한 -->
            <div class="mt-5">
                <p class="fs_15 fw_500"><?=  translate('수정권한을 선택해주세요', $userLang); ?></p>
                <div class="checks_wr mt-3">
                    <div class="checks">
                        <label>
                            <input type="checkbox" name="sst_update_chk" value="1" checked disabled>
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic"><?=  translate('오너', $userLang); ?></p>
                            </div>
                        </label>
                    </div>
                    <div class="checks">
                        <label>
                            <input type="checkbox" name="sst_update_chk" value="2" <? if (!$_GET['sst_idx'] && ($sgt_cnt <= 0 || $sgdt_cnt > 0)) {
                                                                                        echo 'checked disabled';
                                                                                    } ?> <? if ($readonly) {
                                                                                                echo 'disabled';
                                                                                            } ?>>
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic"><?=  translate('리더', $userLang); ?></p>
                            </div>
                        </label>
                    </div>
                    <div class="checks">
                        <label>
                            <input type="checkbox" name="sst_update_chk" value="3" <? if (!$_GET['sst_idx'] && $sgt_cnt <= 0 && $sgdt_cnt <= 0) {
                                                                                        echo 'checked disabled';
                                                                                    } ?> <? if ($readonly) {
                                                                                                echo 'disabled';
                                                                                            } ?>>
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic"><?=  translate('그룹원', $userLang); ?></p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="b_botton <?= $dnone ?>">
                <?php if ($row_sst['sst_idx']) { ?>
                    <!-- F-17 일정 수정 수정시 생겨야하는 버튼 -->
                    <div class="row mx-0">
                        <div class="col-5 pl-0 pr-3">
                            <button type="button" class="btn btn-secondary rounded  btn-lg btn-block" data-toggle="modal" data-target="#schedule_delete"><?=  translate('일정 삭제하기', $userLang); ?></button>
                        </div>
                        <div class="col-7 px-0">
                            <button type="submit" id="btn_submit" class="btn btn-primary rounded btn-lg btn-block"><?=  translate('일정 수정하기', $userLang); ?></button>
                        </div>
                    </div>
                <?php } else { ?>
                    <button type="submit" id="btn_submit" class="btn rounded btn-primary btn-lg btn-block"><?=  translate('입력했어요!', $userLang); ?></button>
                <?php } ?>
            </div>
            <div id="layoutViewport"></div>
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
                // 채팅 입력창의 높이를 내용에 맞게 조절하는 함수
                function adjustTextareaHeight(textareaId) {
                    var textarea = $("#" + textareaId);
                    textarea.css("height", "auto"); // 임시로 높이 초기화
                    var newHeight = textarea[0].scrollHeight; // 스크롤이 생기는 높이
                    textarea.height(newHeight); // 높이를 설정하는 방식을 변경하여 더 간결하게 처리
                }

                // 채팅 입력창에 내용이 변경될 때마다 높이 조절
                $("#sst_supplies").on("input", function() {
                    adjustTextareaHeight("sst_supplies");
                });
                $("#sst_memo").on("input", function() {
                    adjustTextareaHeight("sst_memo");
                });

                <?php if ($row_sst['sst_idx']) {
                    if ($row_sst['sst_pidx']) { ?>
                        f_contact_list('<?= $row_sst['sst_pidx'] ?>');
                    <? } else { ?>
                        f_contact_list('<?= $row_sst['sst_idx'] ?>');
                    <?php }
                } else { ?>
                    //f_contact_list();
                <?php } ?>
                <?php if ($row_sst['sst_update_chk']) {
                    // 쉼표(,)를 기준으로 값을 분리하여 배열로 저장
                    $checked_values = explode(',', $row_sst['sst_update_chk']);
                    foreach ($checked_values as $value) { ?>
                        $('input[name="sst_update_chk"][value="<?php echo $value; ?>"]').prop('checked', true);
                <? }
                } ?>

                <?php if ($row_sst['sst_title']) { ?>
                    $('#sst_title_cnt').text($('#sst_title').val().length);
                <?php } ?>
                <?php if ($row_sst['sst_supplies']) { ?>
                    $('#sst_supplies_cnt').text($('#sst_supplies').val().length);
                <?php } ?>
                <?php if ($row_sst['sst_memo']) { ?>
                    $('#sst_memo_cnt').text($('#sst_memo').val().length);
                <?php } ?>

                <?php if ($row_sst['sst_all_day'] == 'Y') { ?>
                    f_all_day();
                <?php } ?>

                // 저장하기 버튼 클릭 이벤트 핸들러
                $('#form_submit_btn').click(function() {
                    console.log('click');
                    $('#btn_submit').attr('disabled', true);

                    // $('#splinner_modal').modal('toggle');

                    var form_t = $("#frm_form")[0];
                    var formData_t = new FormData(form_t);

                    // 체크된 체크박스의 값을 저장할 배열
                    var checkedValues = [];

                    // 체크된 체크박스 요소 선택
                    $('input[name="sst_update_chk"]:checked').each(function() {
                        // 체크된 체크박스의 값 배열에 추가
                        checkedValues.push($(this).val());
                    });

                    // 체크된 체크박스 값들을 콤마로 구분하여 문자열로 변환
                    var checkedValuesString = checkedValues.join(',');

                    // 체크된 체크박스 값 추가
                    $('input[name="sst_update_chk"]:checked').each(function() {
                        formData_t.append("sst_update_chk", checkedValuesString);
                    });
                    // 폼 데이터 확인
                    for (var pair of formData_t.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                    $.ajax({
                        url: './schedule_update',
                        enctype: "multipart/form-data",
                        data: formData_t,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 5000,
                        success: function(data) {
                            $('#btn_submit').attr('disabled', false);
                            console.log(data);

                            if (data == 'Y') {
                                <?php if ($row_sst['sst_idx']) { ?>
                                    /*  $.alert({
                                         title: '',
                                         type: "blue",
                                         typeAnimated: true,
                                         content: '수정되었습니다.',
                                         buttons: {
                                             confirm: {
                                                 btnClass: "btn-default btn-lg btn-block",
                                                 text: "확인",
                                             },
                                         },
                                     }); */
                                    jalert_url("<?= translate('해당 일정이 수정되었습니다.', $userLang) ?>", './schedule');
                                <?php } else { ?>
                                    /*  $.alert({
                                         title: '',
                                         type: "blue",
                                         typeAnimated: true,
                                         content: '등록되었습니다.',
                                         buttons: {
                                             confirm: {
                                                 btnClass: "btn-default btn-lg btn-block",
                                                 text: "확인",
                                                 action: function() {
                                                     location.href = './schedule';
                                                 },
                                             },
                                         },
                                     }); */
                                    jalert_url("<?= translate('해당 일정이 등록되었습니다.', $userLang) ?>", './schedule');
                                <?php } ?>
                            } else {
                                console.log(data);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                });
            });

            function f_schedule_repeat_modal() {
                var sst_repeat_json = document.getElementById('sst_repeat_json').value;
                // JSON 문자열 파싱하여 객체로 변환
                if (sst_repeat_json) {
                    var repeatObject = JSON.parse(sst_repeat_json);

                    // 객체를 배열로 변환
                    var repeatArray = [];
                    for (var key in repeatObject) {
                        if (repeatObject.hasOwnProperty(key)) {
                            repeatArray.push(repeatObject[key]);
                        }
                    }
                    $('#r1_' + repeatArray[0]).prop("checked", true);

                    // repeatArray의 두 번째 요소가 비어 있지 않은 경우 해당 배열 내의 요소를 체크
                    if (repeatArray[1] !== "") {
                        $('label[for*="r2_"]').removeClass('active');
                        var r2_values = repeatArray[1].split(',');
                        r2_values.forEach(function(value) {
                            var r2_check = 'r2_' + value;
                            // console.log(r2_check);
                            $('#' + r2_check).prop("checked", true);
                            $('label[for="' + r2_check + '"]').addClass('active');
                        });
                    }
                }
                $('#schedule_repeat').modal('show');
            }

            $("#frm_form").validate({
                submitHandler: function() {
                    var sd = $('#sst_sdate').val();
                    var ed = $('#sst_edate').val();
                    var slt_idx_t = $('#slt_idx_t').val();

                    if (sd && ed) {
                        var csdt = new Date(sd);
                        var cedt = new Date(ed);

                        if (csdt > cedt) {
                            /* $.alert({
                                title: '',
                                type: "blue",
                                typeAnimated: true,
                                content: '종료시간은 시작 시간보다 나중이어야 합니다.',
                                buttons: {
                                    confirm: {
                                        btnClass: "btn-default btn-lg btn-block",
                                        text: "확인",
                                        action: function() {
                                            var offset = $("#sst_sdate").offset();
                                            $("html, body").animate({
                                                scrollTop: offset.top
                                            }, 400);
                                        },
                                    },
                                },
                            }); */
                            jalert("<?= translate('종료시간은 시작 시간보다 나중이어야 합니다.', $userLang) ?>");
                            return false;
                        }
                    } else {
                        jalert("<?= translate('일정 시간을 입력바랍니다.', $userLang) ?>");
                        return false;
                    }
                    if (!slt_idx_t) {
                        $('#sch_without_modal').modal('toggle');
                        return false;
                    }

                    $('#btn_submit').attr('disabled', true);

                    // $('#splinner_modal').modal('toggle');

                    var form_t = $("#frm_form")[0];
                    var formData_t = new FormData(form_t);

                    // 체크된 체크박스의 값을 저장할 배열
                    var checkedValues = [];

                    // 체크된 체크박스 요소 선택
                    $('input[name="sst_update_chk"]:checked').each(function() {
                        // 체크된 체크박스의 값 배열에 추가
                        checkedValues.push($(this).val());
                    });

                    // 체크된 체크박스 값들을 콤마로 구분하여 문자열로 변환
                    var checkedValuesString = checkedValues.join(',');

                    // 체크된 체크박스 값 추가
                    $('input[name="sst_update_chk"]:checked').each(function() {
                        formData_t.append("sst_update_chk", checkedValuesString);
                    });
                    // 폼 데이터 확인
                    for (var pair of formData_t.entries()) {
                        // console.log(pair[0] + ': ' + pair[1]);
                    }
                    $.ajax({
                        url: './schedule_update',
                        enctype: "multipart/form-data",
                        data: formData_t,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 10000,
                        success: function(data) {
                            $('#btn_submit').attr('disabled', false);

                            if (data == 'Y') {
                                <?php if ($row_sst['sst_idx']) { ?>
                                    /* $.alert({
                                        title: '',
                                        type: "blue",
                                        typeAnimated: true,
                                        content: '수정되었습니다.',
                                        buttons: {
                                            confirm: {
                                                btnClass: "btn-default btn-lg btn-block",
                                                text: "확인",
                                                action: function() {
                                                    location.href = './schedule';
                                                },
                                            },
                                        },
                                    }); */

                                    jalert_url("<?= translate('해당 일정이 수정되었습니다.', $userLang) ?>", './schedule');
                                <?php } else { ?>
                                    /* $.alert({
                                        title: '',
                                        type: "blue",
                                        typeAnimated: true,
                                        content: '등록되었습니다.',
                                        buttons: {
                                            confirm: {
                                                btnClass: "btn-default btn-lg btn-block",
                                                text: "확인",
                                                action: function() {
                                                    location.href = './schedule';
                                                },
                                            },
                                        },
                                    }); */
                                    jalert_url("<?= translate('해당 일정이 등록되었습니다.', $userLang) ?>", './schedule');
                                <?php } ?>
                            } else {
                                console.log(data);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                    return false;
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
                    // slt_idx_t: {
                    //     required: true,
                    // },
                },
                messages: {
                    sst_title: {
                        required: "<?= translate("일정 내용을 입력해주세요.", $userLang) ?>",
                    },
                    sst_sdate: {
                        required: "<?= translate("시작일을 입력해주세요.", $userLang) ?>",
                    },
                    sgdt_idx_t: {
                        required: "<?= translate("멤버를 선택해주세요.", $userLang) ?>",
                    },
                    // slt_idx_t: {
                    //     required: "위치를 선택해주세요.",
                    // },
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
                <?php if ($row_sst['sgdt_idx']) { ?>
                    form_data.append("sgdt_idx", "<?= $row_sst['sgdt_idx'] ?>");
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
                }, 500);
            }

            function f_location_like_list() {
                console.log('<?= $sgdt_row['mt_idx'] ?>');
                console.log('<?= $sgdt_row['sgdt_idx'] ?>');

                var form_data = new FormData();
                form_data.append("act", "list_like_location");
                form_data.append("mt_idx", "<?= $sgdt_row['mt_idx'] ?>");
                form_data.append("sgdt_idx", "<?= $sgdt_row['sgdt_idx'] ?>");

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
                            updateContactCount();
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    },
                });
            }
            // 연락처 개수를 확인하고, input 요소에 반영하는 함수
            function updateContactCount() {
                // 연락처 목록의 개수를 확인
                var contactCount = countContact();
                // input 요소 선택
                var inputElement = document.querySelector('input[data-target="#schedule_contact"]');
                if (contactCount > 0) {
                    // 연락처 개수를 input 요소의 값으로 설정
                    inputElement.value = contactCount + "<?= translate("개의 연락처", $userLang) ?>";
                }
            }
            // 연락처 개수를 확인하는 함수
            function countContact() {
                // 연락처 목록의 개수를 세기 위해 ul 태그의 id를 통해 해당 요소를 선택
                var contactList = document.getElementById("contact_list_li");
                // 선택된 ul 태그 내의 li 태그들의 개수를 세어 반환
                if (contactList) {
                    // 선택된 ul 태그 내의 li 태그들의 개수를 세어 반환
                    return contactList.getElementsByTagName("li").length;
                } else {
                    // 연락처 목록이 없는 경우에는 0을 반환
                    return 0;
                }
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
                            // history.back();
                            // $.alert({
                            //     title: '',
                            //     type: "blue",
                            //     typeAnimated: true,
                            //     content: '해당 일정이 삭제되었습니다.',
                            //     buttons: {
                            //         confirm: {
                            //             btnClass: "btn-default btn-lg btn-block",
                            //             text: "확인",
                            //             action: function() {
                            //                 location.href = './schedule';
                            //             },
                            //         },
                            //     },
                            // });
                            jalert_url("<?= translate('해당 일정이 삭제되었습니다.', $userLang) ?>", './schedule');
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
        <p><i class="xi-check-circle mr-2"></i><?=  translate('일정이 등록되었습니다.', $userLang); ?></p>
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>

<!-- F-10 장소 미입력 저장  -->
<div class="modal fade" id="sch_without_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center pb-5">
                <img src="./img/warring.png" width="72px" class="pt-3" alt="장소입력해주세요." />
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4"><?=  translate('장소를 입력하지 않으면
                    위치 기반 알림을 받지 못해요.', $userLang); ?>
                </p>
                <p class="fs_14 text_dynamic text_gray mt-3 line_h1_2 px-4"><?=  translate('장소 입력없이 저장하시겠어요?', $userLang); ?></p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close" onclick="f_modal_schedule_location();"><?=  translate('장소입력 하러가기', $userLang); ?></button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" id="form_submit_btn" data-dismiss="modal" aria-label="Close"><?=  translate('저장하기', $userLang); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- F-4 일정 입력 > 시작종료  -->
<div class="modal fade" id="schedule_date_time" tabindex="-1">
    <div class="modal-dialog modal-default modal-default_y modal-dialog-scrollable modal-dialog-centered">
        <form method="post" name="frm_schedule_date_time" id="frm_schedule_date_time">
            <div class="modal-content">
                <div class="modal-header justify-content-end border-0 pt_20 pb_4">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button>
                </div>
                <div class="cld_head_wr">
                    <div class="add_cal_tit mb-3">
                        <div class="sel_month d-inline-flex" style="margin-left:2rem;">
                            <img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="<?=  translate('월 선택 아이콘', $userLang); ?>" style="width:1.6rem; ">
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
                        <link rel="stylesheet" type="text/css" href="<?= CDN_HTTP ?>/lib/fullcalendar/fullcalendar.min.css" />
                        <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/moment.min.js"></script>
                        <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/fullcalendar.min.js"></script>
                        <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/<?= $userLang ?>.js"></script>
                        <script type="text/javascript" src="<?= CDN_HTTP ?>/lib/fullcalendar/gcal.min.js"></script>
                        <link rel="stylesheet" type="text/css" href="<?= CDN_HTTP ?>/lib/fullcalendar/fullcalendar.custom.css?v=<?= $v_txt ?>" />
                        <script>
                            (function($) {
                                'use strict';
                                $(function() {
                                    if ($('#schedule_box').length) {
                                        var $calendar = $('#schedule_box');
                                        $calendar.fullCalendar({
                                            locale: '<?= $userLang ?>',
                                            header: false,
                                            viewRender: (view) => {
                                                let date
                                                switch (view.type) {
                                                    case 'month':
                                                        date = view.title
                                                        // alert(date);
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
                                                    console.log("<?= translate('잘못된 접근입니다.', $userLang) ?>");
                                                },
                                            }],
                                            eventAfterAllRender: function(view) {
                                                $('.fc-event-container').html('');
                                                $('.fc-more-cell').                                       html('');
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
                                <h5 class="text-body fw_800"><?=  translate('시작일시', $userLang); ?></h5>
                            </div>
                            <div class="form-row flex-nowrap align-items-center mb-3">
                                <input type="text" readonly class="form-control form-control-sm" name="sst_sdate_d1" id="sst_sdate_d1" value="<?= $arr_sdate_t['date'] ?>" placeholder="<?=  translate('시작일자를 선택해주세요.', $userLang); ?>" />
                                <span class="mx-2"> </span>
                                <select class="form-control custom-select form-control-sm" name="sst_sdate_d4" id="sst_sdate_d4">
                                    <option value="1" <?php if ($arr_sdate_t['ampm'] == '1') {
                                                            echo " selected";
                                                        } ?>><?=  translate('오전', $userLang); ?></option>
                                    <option value="2" <?php if ($arr_sdate_t['ampm'] == '2') {
                                                            echo " selected";
                                                        } ?>><?=  translate('오후', $userLang); ?></option>
                                </select>
                            </div>
                            <div class="form-row flex-nowrap align-items-center">
                                <select class="form-control custom-select form-control-sm" name="sst_sdate_d2" id="sst_sdate_d2">
                                    <?php
                                    for ($q = 1; $q < 13; $q++) {
                                        if ($q < 10) {
                                            $q = '0' . $q;
                                        }
                                    ?>
                                        <option value="<?= $q ?>" <?php if ($arr_sdate_t['hour'] == $q) {
                                                                        echo " selected";
                                                                    } ?>><?= $q ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <span class="mx-2">:</span>
                                <select class="form-control custom-select form-control-sm" name="sst_sdate_d3" id="sst_sdate_d3">
                                    <?php
                                    $w = 0;
                                    for ($q = 0; $q < 6; $q++) {
                                        $w = ($q * 10);
                                        if ($w < 1) {
                                            $w = '00';
                                        }
                                    ?>
                                        <option value="<?= $w ?>" <?php if ($arr_sdate_t['min'] == $w) {
                                                                        echo " selected";
                                                                    } ?>><?= $w ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="ip_wr pt_20">
                            <div class="ip_tit d-flex align-items-center justify-content-between">
                                <h5 class="text-body fw_800"><?=  translate('종료일시', $userLang); ?></h5>
                            </div>
                            <div class="form-row flex-nowrap align-items-center mb-3">
                                <input type="text" readonly class="form-control form-control-sm" name="sst_edate_d1" id="sst_edate_d1" value="<?= $arr_edate_t['date'] ?>" placeholder="<?=  translate('종료일자를 선택해주세요.', $userLang) ?> ?>" />
                                <span class="mx-2"> </span>
                                <select class="form-control custom-select form-control-sm" name="sst_edate_d4" id="sst_edate_d4">
                                    <option value="1" <?php if ($arr_edate_t['ampm'] == '1') {
                                                            echo " selected";
                                                        } ?>><?=  translate('오전', $userLang); ?></option>
                                    <option value="2" <?php if ($arr_edate_t['ampm'] == '2') {
                                                            echo " selected";
                                                        } ?>><?=  translate('오후', $userLang); ?></option>
                                </select>
                            </div>
                            <div class="form-row flex-nowrap align-items-center">
                                <select class="form-control custom-select form-control-sm" name="sst_edate_d2" id="sst_edate_d2">
                                    <?php
                                    for ($q = 1; $q < 13; $q++) {
                                        if ($q < 10) {
                                            $q = '0' . $q;
                                        }
                                    ?>
                                        <option value="<?= $q ?>" <?php if ($arr_edate_t['hour'] == $q) {
                                                                        echo " selected";
                                                                    } ?>><?= $q ?></option>
                                    <?php
                                        $w++;
                                    }
                                    ?>
                                </select>
                                <span class="mx-2">:</span>
                                <select class="form-control custom-select form-control-sm" name="sst_edate_d3" id="sst_edate_d3">
                                    <?php
                                    $w = 0;
                                    for ($q = 0; $q < 6; $q++) {
                                        $w = ($q * 10);
                                        if ($w < 1) {
                                            $w = '00';
                                        }
                                    ?>
                                        <option value="<?= $w ?>" <?php if ($arr_edate_t['min'] == $w) {
                                                                        echo " selected";
                                                                    } ?>><?= $w ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-0 py-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=  translate('시간 저장하기', $userLang); ?></button>
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
                        jalert("<?= translate('시작일시가 종료일시보다 큽니다.', $userLang) ?>");
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
                        required: "<?= translate('시작일을 선택해주세요.', $userLang) ?>",
                    },
                    sst_sdate_d2: {
                        required: "<?= translate('시작시간을 선택해주세요.', $userLang) ?>",
                    },
                    sst_sdate_d3: {
                        required: "<?= translate('시작분을 선택해주세요.', $userLang) ?>",
                    },
                    sst_edate_d1: {
                        required: "<?= translate('종료일을 선택해주세요.', $userLang) ?>",
                    },
                    sst_edate_d2: {
                        required: "<?= translate('종료시간을 선택해주세요.', $userLang) ?>",
                    },
                    sst_edate_d3: {
                        required: "<?= translate('종료분을 선택해주세요.', $userLang) ?>",
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
                    <p class="modal-title line1_text fs_20 fw_700"><?=  translate('반복', $userLang); ?></p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y">
                    <?php
                    foreach ($arr_sst_repeat_json as $key => $val) {
                        if ($val) {
                            if ($key == '1') {
                                $line_class = 'line_ip pb_16';
                            } else {
                                $line_class = 'line_ip pb_16 mt_16';
                            }
                    ?>
                            <div class="<?= $line_class ?>">
                                <?php if ($key == "3") { ?>
                                    <div class="checks mb-4">
                                        <label>
                                            <input type="radio" class="repeat_r1 repeat_week_chk" name="repeat_r1" id="r1_<?= $key ?>" value="<?= $key ?>" onchange="f_repeat_sel(this.value);" />
                                            <span class="ic_box"><i class="xi-check-min"></i></span>
                                            <div class="chk_p">
                                                <p class="text_dynamic" style="word-break: break-all;"><?=  translate('1주 마다', $userLang); ?></p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="table_scroll scroll_bar_x week_wrappp">
                                        <div class="week_btn btn-group btn-group-toggle d-flex aling-items-center justify-content-between week_checks_wrap" data-toggle="buttons">
                                            <?php
                                            if ($sst_repeat_json_t['r2']) {
                                                $r2_ex = explode(',', $sst_repeat_json_t['r2']);
                                            }

                                            foreach ($arr_sst_repeat_json_r2 as $key => $val) {
                                                if ($val) {
                                                    $r2_chk = '';
                                                    if ($r2_ex) {
                                                        foreach ($r2_ex as $key2 => $val2) {
                                                            if ($val2 == $key) {
                                                                $r2_chk = ' checked';
                                                            }
                                                        }
                                                    }
                                            ?>
                                                    <div class="checks week_checks flex-grow-1 mb-0">
                                                        <label class="btn btn-outline-secondary p-3" for="r2_<?= $key ?>">
                                                            <input type="checkbox" class="repeat_r2" name="r2[]" id="r2_<?= $key ?>" value="<?= $key ?>" <?= $r2_chk ?> onchange="f_chk_week_repeat();" />
                                                            <div class="w-100 chk_p week_check px-0">
                                                                <p class="fs_15 fw_600 text_dynamic text-center"><?= $val ?></p>
                                                            </div>
                                                        </label>
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="checks m-0">
                                        <label>
                                            <input type="radio" class="repeat_r1" name="repeat_r1" id="r1_<?= $key ?>" value="<?= $key ?>" onchange="f_repeat_sel(this.value);" />
                                            <span class="ic_box"><i class="xi-check-min"></i></span>
                                            <div class="chk_p">
                                                <p class="text_dynamic" style="word-break: break-all;"><?= $val ?></p>
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
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=  translate('반복 주기 선택완료', $userLang); ?></button>
                </div>
            </form>
            <script>
                $(document).ready(function() {
                    <?php if ($sst_repeat_json_t['r1']) { ?>
                        $('#r1_<?= $sst_repeat_json_t['r1'] ?>').prop("checked", true);
                    <?php } ?>
                });

                function f_chk_week_repeat() {
                    var q = 0;
                    $(".repeat_r2").each(function() {
                        if ($(this).prop("checked") == true) {
                            q++;
                        }
                    });

                    if (q > 0) {
                        $('.repeat_week_chk').prop("checked", true);
                    } else {
                        $('.repeat_week_chk').prop("checked", false);
                    }
                }

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
                            return "<?= translate('월', $userLang) ?>";
                            break;
                        case '2':
                            return "<?= translate('화', $userLang) ?>";
                            break;
                        case '3':
                            return "<?= translate('수', $userLang) ?>";
                            break;
                        case '4':
                            return "<?= translate('목', $userLang) ?>";
                            break;
                        case '5':
                            return "<?= translate('금', $userLang) ?>";
                            break;
                        case '6':
                            return "<?= translate('토', $userLang) ?>";
                            break;
                        case '7':
                            return "<?= translate('일', $userLang) ?>";
                            break;
                        default:
                            console.log('null');
                    }
                }

                function f_switch_repeat(r) {
                    switch (r) {
                        case '1':
                            return "<?= translate('반복 안 함', $userLang) ?>";
                            break;
                        case '2':
                            return "<?= translate('매일', $userLang) ?>";
                            break;
                        case '3':
                            return "<?= translate('1주마다', $userLang) ?>";
                            break;
                        case '4':
                            return "<?= translate('매월', $userLang) ?>";
                            break;
                        case '5':
                            return "<?= translate('매년', $userLang) ?>";
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
                            jalert("<?= translate('반복 방식을 선택해주세요.', $userLang) ?>");
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
                                jalert("<?= translate('반복 요일을 선택해주세요.', $userLang) ?>");
                                return false;
                            }

                            json_rtn = '{"r1":"' + f.repeat_r1.value + '","r2":"' + week_t + '"}';
                            json_rtn_v = <?= translate('1주마다', $userLang) ?> + ' ' + week_tv;
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
                            required: "<?= translate('반복 방식을 선택해주세요.', $userLang) ?>",
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
                    <p class="modal-title line1_text fs_20 fw_700"><?=  translate('알림', $userLang); ?></p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y">
                    <?php
                    foreach ($arr_sst_alram as $key => $val) {
                        if ($val) {
                    ?>
                            <div class="line_ip pb_16 mt_16">
                                <div class="checks m-0">
                                    <label>
                                        <input type="radio" class="sst_alram_c" name="sst_alram_r1" id="sst_alram_r1_<?= $key ?>" value="<?= $key ?>" <?php if ($row_sst['sst_alram'] == $key) {
                                                                                                                                                            echo " checked";
                                                                                                                                                        } ?> />
                                        <span class="ic_box"><i class="xi-check-min"></i></span>
                                        <div class="chk_p">
                                            <p class="text_dynamic" style="word-break: break-all;"><?= $val ?></p>
                                        </div>
                                    </label>
                                    <input type="hidden" name="sst_alram_nm_r1" id="sst_alram_nm_r1_<?= $key ?>" value="<?= $val ?>" />
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=  translate('알림 설정완료', $userLang); ?></button>
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
                            jalert("<?= translate("알림 방식을 선택해주세요.", $userLang) ?>");
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
                            required: "<?= translate("알림 방식을 선택해주세요.", $userLang) ?>",
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
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered" style="overflow-y: initial !important">
        <div class="modal-content">
            <form method="post" name="frm_schedule_location" id="frm_schedule_location">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700"><?=  translate('장소입력', $userLang); ?></p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y pt-0" style="max-height: calc(100vh - 200px); overflow-x: hidden; overflow-y: auto; -webkit-overflow-scrolling: touch;">
                    <div class="text-center py-5 border-top">
                        <div class="mx-auto"><img src="<?= CDN_HTTP ?>/img/icon_location.png" style="max-width:4.9rem;"></div>
                        <!-- <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="f_modal_schedule_map();">지도에서 선택할래요<i class="xi-angle-right-min ml_19"></i></button> -->
                        <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="f_modal_map_search();"><?=  translate('주소로 검색할래요', $userLang); ?><i class="xi-angle-right-min ml_19"></i></button>

                    </div>
                    <div class="bargray_fluid mx_n20"></div>

                    <div class="location_mark my_20">
                        <p class="tit_h3 fs_15 mb-4"><?=  translate('내 장소', $userLang); ?></p>
                        <ul id="location_like_list_box">
                        </ul>
                    </div>
                    <!-- 위치알림 -->
                    <div>
                        <p class="tit_h3 fs_15 mb-4"><?=  translate('위치알림', $userLang); ?></p>
                        <div class="mt-4">
                            <div class="row bg_gray mx-0 arm_set_box">
                                <div class="col checks mb-0 pl-0 px-sm-0 d-flex align-items-center">
                                    <label>
                                        <input type="checkbox" id="sst_location_alarm_t_1" value="1" <? if (!$row_sst['sst_location_alarm']) echo 'checked'; ?>>
                                        <span class="ic_box"><i class="xi-check-min"></i></span>
                                        <div class="chk_p flex-shrink-0">
                                            <p class="text_dynamic text_gray"><?=  translate('진입알림', $userLang); ?></p>
                                        </div>
                                    </label>
                                    <!-- 이하 생략 -->
                                </div>
                                <div class="col checks mb-0 pl-0 px-sm-0 d-flex align-items-center">
                                    <label>
                                        <input type="checkbox" id="sst_location_alarm_t_2" value="2" <? if (!$row_sst['sst_location_alarm']) echo 'checked'; ?>>
                                        <span class="ic_box"><i class="xi-check-min"></i></span>
                                        <div class="chk_p flex-shrink-0">
                                            <p class="text_dynamic text_gray"><?=  translate('이탈알림', $userLang); ?></p>
                                        </div>
                                    </label>
                                    <!-- 이하 생략 -->
                                </div>
                            </div>
                            <div class="row mx-0 arm_set_box pt-4">
                                <div class="checks_wr">
                                    <div class="checks">
                                        <label>
                                            <input type="checkbox" id="sst_location_alarm_t_3" value="3">
                                            <span class="ic_box"><i class="xi-check-min"></i></span>
                                            <div class="chk_p">
                                                <p class="text_dynamic text_gray"><?=  translate('위치알림 안함', $userLang); ?></p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=  translate('장소 입력하기', $userLang); ?></button>
                </div>
            </form>
            <script>
                $(document).ready(function() {
                    // 진입알림 라디오 버튼 클릭 시
                    $('#sst_location_alarm_t_1').click(function() {
                        if ($(this).is(':checked')) {
                            $('#sst_location_alarm').val(1);
                        }
                    });

                    // 이탈알림 라디오 버튼 클릭 시
                    $('#sst_location_alarm_t_2').click(function() {
                        if ($(this).is(':checked')) {
                            $('#sst_location_alarm').val(2);
                        }
                    });
                    // 위치알림 안함 라디오 버튼을 클릭했을 때
                    $('#sst_location_alarm_t_3').click(function() {
                        // 진입알림과 이탈알림 체크 해제
                        $('#sst_location_alarm_t_1').prop('checked', false);
                        $('#sst_location_alarm_t_2').prop('checked', false);
                        if ($(this).is(':checked')) {
                            $('#sst_location_alarm').val(3);
                        }
                    });
                    // 진입알림 또는 이탈알림 라디오 버튼을 클릭했을 때
                    $('#sst_location_alarm_t_1, #sst_location_alarm_t_2').click(function() {
                        // 위치알림 안함 체크 해제
                        $('#sst_location_alarm_t_3').prop('checked', false);
                        if ($('#sst_location_alarm_t_1').is(':checked') && $('#sst_location_alarm_t_2').is(':checked')) {
                            $('#sst_location_alarm').val(4);
                        }
                    });
                    <? if ($row_sst['sst_location_alarm']) {
                        $sst_location_alarm = $row_sst['sst_location_alarm'];
                        if ($sst_location_alarm == 1) { ?>
                            $('#sst_location_alarm_t_1').prop('checked', true);
                        <? } else if ($sst_location_alarm == 2) { ?>
                            $('#sst_location_alarm_t_2').prop('checked', true);
                        <? } else if ($sst_location_alarm == 3) { ?>
                            $('#sst_location_alarm_t_1').prop('checked', false);
                            $('#sst_location_alarm_t_2').prop('checked', false);
                            $('#sst_location_alarm_t_3').prop('checked', true);
                        <? } else if ($sst_location_alarm == 4) { ?>
                            $('#sst_location_alarm_t_1').prop('checked', true);
                            $('#sst_location_alarm_t_2').prop('checked', true);
                            $('#sst_location_alarm_t_3').prop('checked', false);
                    <? }
                    } ?>
                });

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
                    submitHandler: function()                   {
                        var f = document.frm_schedule_location;

                        var q = 0;
                        $(".slt_idx_c").each(function() {
                            if ($(this).prop("checked") == true) {
                                q++;
                            }
                        });
                        if (q < 1) {
                            jalert("<?= translate('장소를 선택해주세요.', $userLang) ?>");
                            return false;
                        }

                        var slt_idx_r1_t = f.slt_idx_r1.value;
                        // var sst_location_alarm_t = f.sst_location_alarm_t.value;

                        $('#slt_idx').val(slt_idx_r1_t);
                        // $('#sst_location_alarm').val(sst_location_alarm_t);
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
                            required: "<?= translate('멤버를 선택해주세요.', $userLang) ?>",
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
                <div class="modal-header pb-0">
                    <p class="tit_h2 line_h1_3 fs_20 fw_700 text_dynamic"><?=  translate('일정과 연관된', $userLang); ?>
                        <?=  translate('연락처를 입력해주세요.', $userLang); ?>
                    </p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y pt-0">
                    <div class="ip_wr d-none">
                        <div class="ip_tit d-flex align-items-center justify-content-between">
                            <h5><?=  translate('카테고리', $userLang); ?></h5>
                        </div>
                        <input type="text" class="form-control" name="sct_category" id="sct_category" maxlength="40" oninput="maxLengthCheck(this)" placeholder="<?=  translate('카테고리 입력', $userLang); ?>">
                    </div>
                    <div class="ip_wr mt_25">
                        <div class="ip_tit">
                            <h5 class=""><?=  translate('연락처 이름', $userLang); ?></h5>
                        </div>
                        <input type="text" class="form-control" name="sct_title" id="sct_title" maxlength="40" oninput="maxLengthCheck(this)" placeholder="<?=  translate('홍길동', $userLang); ?>">
                    </div>
                    <div class="ip_wr mt_25">
                        <div class="ip_tit">
                            <h5 class=""><?=  translate('연락처', $userLang); ?></h5>
                        </div>
                        <!-- <input type="text" class="form-control" name="sct_hp" id="sct_hp" maxlength="40" oninput="maxLengthCheck(this)" placeholder="010-1234-1234"> -->
                        <input type="text" class="form-control" name="sct_hp" id="sct_hp" maxlength="13" placeholder="<?=  translate('010-1234-1234', $userLang); ?>" oninput="formatPhoneNumber(this)">
                    </div>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=  translate('연락처 저장하기', $userLang); ?></button>
                </div>
            </form>
            <script>
                function formatPhoneNumber(input) {
                    // 입력된 내용에서 숫자만 남기고 모든 문자 제거
                    var phoneNumber = input.value.replace(/\D/g, '');

                    // 전화번호 형식에 맞게 "-" 추가
                    if (phoneNumber.startsWith('02')) {
                        if (phoneNumber.length > 2 && phoneNumber.length <= 6) {
                            phoneNumber = phoneNumber.replace(/(\d{2})(\d{1,3})/, '$1-$2');
                        } else if (phoneNumber.length > 6 && phoneNumber.length < 10) {
                            phoneNumber = phoneNumber.replace(/(\d{2})(\d{3})(\d{1,4})/, '$1-$2-$3');
                        } else if (phoneNumber.length >= 10) {
                            phoneNumber = phoneNumber.replace(/(\d{2})(\d{4})(\d{1,4})/, '$1-$2-$3');
                        }
                    } else {
                        if (phoneNumber.length > 3 && phoneNumber.length <= 7) {
                            phoneNumber = phoneNumber.replace(/(\d{3})(\d{1,4})/, '$1-$2');
                        } else if (phoneNumber.length > 7) {
                            phoneNumber = phoneNumber.replace(/(\d{3})(\d{4})(\d{1,4})/, '$1-$2-$3');
                        }
                    }

                    // 최대 길이 제한
                    if (phoneNumber.length > 13) {
                        phoneNumber = phoneNumber.substring(0, 13);
                    }

                    // 형식이 적용된 전화번호로 변경
                    input.value = phoneNumber;
                }
                $("#frm_schedule_contact").validate({
                    submitHandler: function() {
                        var f = document.frm_schedule_contact;
                        var sst_idx = '';
                        <? if ($sst_row['sst_idx']) { ?>
                            sst_idx = <?= $sst_row['sst_pidx'] ? $sst_row['sst_pidx'] : $sst_row['sst_idx'] ?>
                        <? } ?>

                        var form_data = new FormData();
                        form_data.append("act", "contact_input");
                        form_data.append("sst_idx", sst_idx);
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
                            timeout: 10000,
                            success: function(data) {
                                if (data == 'Y') {
                                    if (sst_idx) {
                                        f_contact_list(sst_idx);
                                    } else {
                                        f_contact_list();
                                    }
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
                            required: "<?= translate('카테고리를 입력해주세요.', $userLang) ?>",
                        },
                        sct_title: {
                            required: "<?= translate('연락처 이름을 입력해주세요.', $userLang) ?>",
                        },
                        sct_hp: {
                            required: "<?= translate('연락처를 입력해주세요.', $userLang) ?>",
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
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center py_14"><?=  translate('일정을 삭제하시겠어요?', $userLang); ?></p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?=  translate('아니요', $userLang); ?></button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close" onclick="f_delete_schedule('<?= $row_sst['sst_idx'] ?>');"><?=  translate('삭제하기', $userLang); ?></button>
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
                <p class="modal-title line1_text fs_20 fw_700"><?=  translate('연락처 수정', $userLang); ?></p>
                <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
            </div>
            <div class="modal-body scroll_bar_y border-top">
                <form class="">
                    <div class="ip_wr mb-4">
                        <div class="ip_tit d-flex justify-content-between">
                            <h5 class=""><?=  translate('카테고리', $userLang); ?></h5>
                            <button type="button" class="btn btn-link btn-sm fc_gray_500 h-auto p-0 fs_12"><u><?=  translate('삭제하기', $userLang); ?></u></button>
                        </div>
                        <input type="text" class="form-control" placeholder="<?=  translate('카테고리 입력', $userLang); ?>" value="기사아저씨">
                    </div>
                    <div class="bargray_fluid mx_n20"></div>

                    <div class="py_20 border-bottom contact_item">
                        <div class="ip_wr">
                            <div class="ip_tit d-flex justify-content-between">
                                <h5 class=""><?=  translate('이름', $userLang); ?></h5>
                                <button type="button" class="btn btn-link btn-sm fc_gray_500 h-auto p-0 fs_12"><u><?=  translate('삭제하기', $userLang); ?></u></button>
                            </div>
                            <input type="text" class="form-control" placeholder="홍길동" value="홍길동">
                        </div>
                        <div class="ip_wr mt_25">
                            <div class="ip_tit">
                                <h5 class=""><?=  translate('연락처', $userLang); ?></h5>
                            </div>
                            <input type="text" class="form-control" placeholder="010-1234-1234" value="010-1234-1234">
                        </div>
                    </div>

                    <div class="py_20 border-bottom contact_item">
                        <div class="ip_wr">
                            <div class="ip_tit d-flex justify-content-between">
                                <h5 class=""><?=  translate('이름', $userLang); ?></h5>
                                <button type="button" class="btn btn-link btn-sm fc_gray_500 h-auto p-0 fs_12"><u><?=  translate('삭제하기', $userLang); ?></u></button>
                            </div>
                            <input type="text" class="form-control" placeholder="홍길동" value="">
                        </div>
                        <div class="ip_wr mt_25">
                            <div class="ip_tit">
                                <h5 class=""><?=  translate('연락처', $userLang); ?></h5>
                            </div>
                            <input type="text" class="form-control" placeholder="010-1234-1234" value="">
                        </div>
                    </div>
                    <!-- 모달 하단에 연락처 추가하기 버튼 누르면 .contact_item 채로 추가-->


                </form>
            </div>
            <div class="modal-footer border-0 p-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?=  translate('연락처 추가하기', $userLang); ?></button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0"><?=  translate('연락처 수정하기', $userLang); ?></button>
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
                    <p class="modal-title line1_text fs_20 fw_700"><?=  translate('위치 선택', $userLang); ?></p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y p-0">
                    <div id="naver_map"></div>
                    <div class="px-0 py-0 map_wrap" id="map_info_box">
                        <div class="map_wrap_re">
                            <div class="pin_cont bg-white pt_20 px_16 pb_16 rounded_10 ml-2 mr-2">
                                <ul>
                                    <li class="d-none">
                                        <div class="address_btn" onclick="f_modal_map_search();">
                                            <p class=" fc_gray_700"><span class="pr-3"><img src="./img/ico_search.png" width="14px" alt="검색" /></span> <?=  translate('지번, 도로명, 건물명으로 검색', $userLang); ?></p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="name flex-fill">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fs_12 fw_600 text-primary"><?=  translate('선택한 위치', $userLang); ?></span>
                                                <!-- <a class="fc_gray_900 fs_12 fw_500" href="javascript:f_modal_map_search();">주소검색하기 <i class="xi-angle-right-min"></i></a> -->
                                            </div>
                                            <!-- 위치 선택전후는 폰트컬러 두께만 바껴요! -->
                                            <!-- 위치를 선책전 입니다. -->
                                            <div class="fs_14 fw_600 fc_gray_600 text_dynamic mt-2 line_h1_3" id="location_add"><?=  translate('위치를 선택해주세요', $userLang); ?></div>
                                            <!-- 위치를 선책후 입니다. -->
                                            <!-- <div class="fs_14 fw_700 text-text text_dynamic mt-2 line_h1_3">서울 영등포구 여의대로56</div> -->
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=  translate('위치 선택완료', $userLang); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var map = null;
    var marker;

    function f_location_like_delete(i) {
        $.alert({
            title: '',
            type: "blue",
            typeAnimated: true,
            content: "<?= translate('즐겨찾는 위치를 삭제하시겠습니까?', $userLang) ?>",
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
            jalert("<?= translate('별칭을 입력바랍니다.', $userLang) ?>");
            return false;
        }

        $.alert({
            title: '',
            type: "blue",
            typeAnimated: true,
            content: "<?= translate('위치를 등록하시겠습니까?', $userLang) ?>",
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
                                    jalert("<?= translate('등록되었습니다.', $userLang) ?>");
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

            // 네이버 지도를 생성할 div 요소
            var mapDiv = document.getElementById('naver_map');

            map = new naver.maps.Map("naver_map", {
                center: new naver.maps.LatLng(st_lat, st_lng),
                zoom: 16,
                mapTypeControl: false
            });

            map.setCursor('pointer');
            // 창 크기가 변경될 때마다 호출되는 함수
            function resizeMap() {
                // 일정 시간 후에 지도 크기를 조정하고 다시 그리도록 합니다.
                setTimeout(function() {
                    mapDiv.style.width = (window.innerWidth) * 0.9 + 'px';
                    mapDiv.style.height = (window.innerHeight) * 0.7 + 'px';
                    naver.maps.Event.trigger(map, 'resize');
                }, 300); // 300ms 후에 실행 (원하는 시간으로 수정 가능)
            }

            // 창 크기가 변경될 때마다 resizeMap 함수 호출
            window.addEventListener('resize', resizeMap);

            // 초기에 한 번 resizeMap 함수 호출하여 지도 크기를 조정합니다.
            resizeMap();


            naver.maps.Event.addListener(map, 'click', function(e) {
                // 기존에 그려진 라인 & 마커가 있다면 초기화
                if (marker) {
                    marker.setMap(null);
                }
                searchCoordinateToAddress(e.coord);
            });

            function initGeocoder() {
                map.addListener("click", function(e) {
                    // 기존에 그려진 라인 & 마커가 있다면 초기화
                    if (marker) {
                        marker.setMap(null);
                    }
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
                        $('#location_add').addClass('fs_14 fw_700 text-text text_dynamic mt-2 line_h1_3');

                        $('#sst_location_add').val(address);
                        $('#sst_location_lat').val(latlng._lat);
                        $('#sst_location_long').val(latlng._lng);

                        // infoWindow.setContent(['<div style="padding:10px;min-width:200px;line-height:150%;">', '<h4 style="margin-top:5px;">검색 좌표</h4><br />', htmlAddresses.join("<br />"), "</div>"].join("\n"));
                        // infoWindow.open(map, latlng);

                        $('#map_info_box').removeClass('d-none-temp');

                        if (marker) {
                            marker.setMap(null);
                        }

                        marker = new naver.maps.Marker({
                            position: new naver.maps.LatLng(latlng._lat, latlng._lng),
                            map: map
                        });
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

            // naver.maps.onJSContentLoaded = initGeocoder;
        });
    })(jQuery);

    $("#frm_schedule_map").validate({
        submitHandler: function() {
            var f = document.frm_schedule_map;

            if ($('#sst_location_add').val() == '') {
                jalert("<?= translate('위치를 선택해주세요.', $userLang) ?>");
                return false;
            }

            $('#slt_idx_t').val($('#sst_location_add').val());
            // $('#sst_location_alarm').val($('#sst_location_alarm_t').val());
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
                required: "<?= translate('위치를 선택해주세요.', $userLang) ?>",
            },
        },
        errorPlacement: function(error, element) {
            $(element)
                .closest("form")
                .find("span[for='" + element.attr("id") + "']")
                .append(error);
        },
    });

    function f_modal_map_search() {
        var scheduleSearchURL = './schedule_loc';
        $('#schedule_location').modal('hide');
        $('#schedule_map').modal('hide');
        setTimeout(() => {
            $('#map_search').modal('show');
            // iframe에 arm_setting 페이지 로드
            $('#mapSearchFrame').attr('src', scheduleSearchURL);
        }, 100);
    }
    // 모달을 닫는 함수
    function closelocationSearchModal() {
        $('#map_search').modal('hide');
    }
    // 주소값 받아오기
    function onlocationSearchComplete(data) {
        $('#sst_location_add').val(data.sst_location_add);
        $('#sst_location_lat').val(data.sst_location_lat);
        $('#sst_location_long').val(data.sst_location_long);
        $('#slt_idx_t').val(data.sst_location_add);
        $('#location_add').html(data.sst_location_add);
        $('#location_add').addClass('fs_14 fw_700 text-text text_dynamic mt-2 line_h1_3');

        map_panto(data.sst_location_lat, data.sst_location_long);

        closelocationSearchModal();
        $('#schedule_map').modal('show');
    }

    function map_panto(lat, lng) {
        map.setCenter(new naver.maps.LatLng(lat, lng));

        if (marker) {
            marker.setMap(null);
        }

        marker = new naver.maps.Marker({
            position: new naver.maps.LatLng(lat, lng),
            map: map
        });
    }
</script>
<!-- 주소검색부분 -->
<div class="modal fade" id="map_search" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content" id="map_search_content">
            <form method="post" name="frm_map_search" id="frm_map_search">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700"><?=  translate('주소 검색', $userLang); ?></p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y">
                    <iframe id="mapSearchFrame" frameborder="0" width="100%" height="500px"></iframe>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>