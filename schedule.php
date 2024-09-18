<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '3';
$h_menu = '5';
$_SUB_HEAD_TITLE = translate("일정", $userLang);
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다.\n 다시 로그인 부탁드립니다.', $userLang), './logout');
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

<link href="<?= CDN_HTTP ?>/lib/dragula/dragula.min.css" rel="stylesheet" />
<script type="text/javascript" src="<?= CDN_HTTP ?>/lib/dragula/dragula.min.js"></script>
<script type="text/JavaScript" src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
<input type="hidden" id="share_url" value="">
<script>
    Kakao.init("<?= KAKAO_JAVASCRIPT_KEY ?>");

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
                        jalert('초대 링크가 복사되었습니다.');
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
                        <a href="javascript:;" onclick="f_calendar_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="월 선택 아이콘" style="width:1.6rem; "></a>
                        <p class="fs_15 fw_600" id="calendar_date_title"><?= $calendar_date_title ?></p>
                    </div>
                    <button type="button" class="btn h-auto" onclick="f_calendar_init('next');"><i class="xi-angle-right-min"></i></button>
                </div>
                <div class="cld_head fs_12">
                    <ul>
                        <li class="sun"><?= translate('일', $userLang) ?></li>
                        <li><?= translate('월', $userLang) ?></li>
                        <li><?= translate('화', $userLang) ?></li>
                        <li><?= translate('수', $userLang) ?></li>
                        <li><?= translate('목', $userLang) ?></li>
                        <li><?= translate('금', $userLang) ?></li>
                        <li class="sat"><?= translate('토', $userLang) ?></li>
                    </ul>
                </div>
            </div>
            <div id="schedule_calandar_box" class="cld_date_wrap"></div>
            <div class="down_wrap text-center pt_08 pb-3">
                <img src="<?= CDN_HTTP ?>/img/btn_bl_arrow.png" class="top_down mx-auto" width="12px" alt="탑다운" />
            </div>
        </div>
        <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
            <input type="hidden" name="act" id="act" value="list" />
            <input type="hidden" name="obj_list" id="obj_list" value="schedule_list_box" />
            <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
            <input type="hidden" name="obj_uri" id="obj_uri" value="./schedule_update" />
            <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
            <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
            <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
            <input type="hidden" name="event_start_date" id="event_start_date" value="<?= $sdate ?>" />
            <input type="hidden" name="week_calendar" id="week_calendar" value="Y" />
            <input type="hidden" name="csdate" id="csdate" value="<?= $sdate ?>" />
            <input type="hidden" name="nmy" id="nmy" value="<?= $now_month_year ?>" />
        </form>

        <script>
            $(document).ready(function() {
                f_get_box_list();
                f_calendar_init('today');
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
        </script>

        <div class="sch_wrap px_16 pt_22 scroll_bar_y" id="schedule_list_box"></div>
    </div>
    <!-- <button type="button" class="btn w-100 floating_btn rounded b_botton_2" onclick="f_go_schedule_form();"><i class="xi-plus-min mr-3"></i> 일정 추가하기</button> -->
</div>
<? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= translate("그룹만들기", $userLang) ?></span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= translate("아직 그룹이 생성되지 않았네요.", $userLang) ?>
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?= translate("함께 일정을 공유할 그룹을 만들어볼까요?", $userLang) ?></p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'"><?= translate("다음", $userLang) ?></button>
            </div>
        </div>
    </div>
<? } ?>
<? if ($sgt_cnt == 1 && $expt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= translate("그룹원 초대하기", $userLang) ?></span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= translate("일정공유로", $userLang) ?>
                    <span class="text-primary"><?= translate("그룹원", $userLang) ?></span><?= translate("과 함께해요!", $userLang) ?>
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?= translate("SMAP은 그룹원과 일정을 공유할 수 있어요.", $userLang) ?>
                    <?= translate("서로의 일정을 확인하고 조율하여", $userLang) ?>
                    <?= translate("더욱 의미 있는 시간을 보내세요.", $userLang) ?></p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'"><?= translate("초대하러 가기", $userLang) ?></button>
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
                <p class="fs_18 fw_700 text_dynamic line_h1_2"><?= translate("초대장은 어떻게 보낼까요?", $userLang) ?></p>
            </div>
            <div class="modal-body">
                <ul>
                    <li>
                        <a href="javascript:;" onclick="f_share_link('kakao');" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="<?= CDN_HTTP ?>/img/ico_kakao.png" alt="카카오톡 열기" width="40px" class="mr_12" id="kakao_image" />
                                <p class="fs_15 fw_500 gray_900" id="kakao_text"><?= translate("카카오톡 열기", $userLang) ?></p>
                            </div>
                            <i class=" xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="f_share_link('clipboard');" class="d-flex align-items-center justify-content-between py_07 btn_copy">
                            <div class="d-flex align-items-center">
                                <img src="<?= CDN_HTTP ?>/img/ico_link.png" alt="초대 링크 복사" width="40px" class="mr_12" />
                                <p class="fs_15 fw_500 gray_900"><?= translate("초대 링크 복사", $userLang) ?></p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="f_share_link('contact');" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="<?= CDN_HTTP ?>/img/ico_address.png" alt="연락처 열기" width="40px" class="mr_12" />
                                <p class="fs_15 fw_500 gray_900"><?= translate("연락처 열기", $userLang) ?></p>
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
            $('#kakao_text').text("<?= translate('공유하기', $userLang) ?>");
            document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
        } else if (isiOS()) {
            $('#kakao_text').text("<?= translate('공유하기', $userLang) ?>");
            document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
        }
    });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>