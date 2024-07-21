<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '2';
$h_menu = '8';
$_SUB_HEAD_TITLE = "그룹";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/anthropic.php";

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다.\n 다시 로그인 부탁드립니다.', './logout');
    }
}

// //오너인 그룹수
// $DB->where('mt_idx', $_SESSION['_mt_idx']);
// $DB->where('sgt_show', 'Y');
// $row = $DB->getone('smap_group_t', 'count(*) as cnt');
// $sgt_cnt = $row['cnt'];

// //리더인 그룹수
// $DB->where('mt_idx', $_SESSION['_mt_idx']);
// $DB->where('sgdt_owner_chk', 'N');
// $DB->where('sgdt_leader_chk', 'Y');
// $DB->where('sgdt_show', 'Y');
// $DB->where('sgdt_discharge', 'N');
// $DB->where('sgdt_exit', 'N');
// $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
// $sgdt_leader_cnt = $row['cnt'];

// //초대된 그룹수
// $DB->where('mt_idx', $_SESSION['_mt_idx']);
// $DB->where('sgdt_owner_chk', 'N');
// $DB->where('sgdt_show', 'Y');
// $DB->where('sgdt_discharge', 'N');
// $DB->where('sgdt_exit', 'N');
// $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
// $sgdt_cnt = $row['cnt'];

// //그룹활동기한 설정여부
// $DB->where('mt_idx', $_SESSION['_mt_idx']);
// $DB->where('sgdt_owner_chk', 'N');
// $DB->where('sgdt_discharge', 'N');
// $DB->where('sgdt_show', 'Y');
// $DB->where('sgdt_exit', 'N');
// $DB->where('sgdt_group_chk', 'D');
// $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
// $gapt_cnt = $row['cnt'];

// //오너제외한 그룹원 수
// $DB->where('mt_idx', $_SESSION['_mt_idx']);
// $DB->where('sgt_show', 'Y');
// $row_sgt = $DB->getone('smap_group_t', 'sgt_idx');

// $DB->where('sgt_idx', $row_sgt['sgt_idx']);
// $DB->where('sgdt_owner_chk', 'N');
// $DB->where('sgdt_show', 'Y');
// $DB->where('sgdt_discharge', 'N');
// $DB->where('sgdt_exit', 'N');
// $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
// $expt_cnt = $row['cnt'];

$mt_idx = $_SESSION['_mt_idx'];

$query = "
SELECT 
    SUM(CASE WHEN subquery.query_type = 'owner' THEN subquery.cnt ELSE 0 END) AS sgt_cnt,
    SUM(CASE WHEN subquery.query_type = 'leader' THEN subquery.cnt ELSE 0 END) AS sgdt_leader_cnt,
    SUM(CASE WHEN subquery.query_type = 'invited' THEN subquery.cnt ELSE 0 END) AS sgdt_cnt,
    SUM(CASE WHEN subquery.query_type = 'group_activity' THEN subquery.cnt ELSE 0 END) AS gapt_cnt,
    SUM(CASE WHEN subquery.query_type = 'except_owner' THEN subquery.cnt ELSE 0 END) AS expt_cnt
FROM (
    SELECT 'owner' AS query_type, COUNT(*) AS cnt
    FROM smap_group_t AS sgt
    WHERE sgt.mt_idx = $mt_idx AND sgt.sgt_show = 'Y'
    UNION ALL
    SELECT 'leader' AS query_type, COUNT(*) AS cnt
    FROM smap_group_detail_t AS sgdt
    WHERE sgdt.mt_idx = $mt_idx AND sgdt.sgdt_owner_chk = 'N' AND sgdt.sgdt_leader_chk = 'Y' 
        AND sgdt.sgdt_show = 'Y' AND sgdt.sgdt_discharge = 'N' AND sgdt.sgdt_exit = 'N'
    UNION ALL
    SELECT 'invited' AS query_type, COUNT(*) AS cnt
    FROM smap_group_detail_t AS sgdt
    WHERE sgdt.mt_idx = $mt_idx AND sgdt.sgdt_owner_chk = 'N' AND sgdt.sgdt_show = 'Y' 
        AND sgdt.sgdt_discharge = 'N' AND sgdt.sgdt_exit = 'N'
    UNION ALL
    SELECT 'group_activity' AS query_type, COUNT(*) AS cnt
    FROM smap_group_detail_t AS sgdt
    WHERE sgdt.mt_idx = $mt_idx AND sgdt.sgdt_owner_chk = 'N' AND sgdt.sgdt_discharge = 'N' 
        AND sgdt.sgdt_show = 'Y' AND sgdt.sgdt_exit = 'N' AND sgdt.sgdt_group_chk = 'D'
    UNION ALL
    SELECT 'except_owner' AS query_type, COUNT(*) AS cnt
    FROM smap_group_detail_t AS sgdt
    WHERE sgdt.sgt_idx IN (SELECT sgt.sgt_idx FROM smap_group_t AS sgt WHERE sgt.mt_idx = $mt_idx AND sgt.sgt_show = 'Y')
        AND sgdt.sgdt_owner_chk = 'N' AND sgdt.sgdt_show = 'Y' AND sgdt.sgdt_discharge = 'N' AND sgdt.sgdt_exit = 'N'
) as subquery;
";

$result = $DB->Query($query);

if ($result) {
    foreach ($result as $row) {
        $counts = $result[0];
        $sgt_cnt = $row['sgt_cnt'];
        $sgdt_leader_cnt = $row['sgdt_leader_cnt'];
        $sgdt_cnt = $row['sgdt_cnt'];
        $gapt_cnt = $row['gapt_cnt'];
        $expt_cnt = $row['expt_cnt'];
        }
} else {
    // 기본값 설정 또는 오류 메시지 표시
    $sgt_cnt = $sgdt_leader_cnt = $sgdt_cnt = $gapt_cnt = $expt_cnt = 0;
}

//오너제외한 그룹원 수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$row_sgt = $DB->getone('smap_group_t', 'sgt_idx');


//나의 걸음수
// $row = get_member_location_log_t_info();
// $my_working_cnt = $row['mt_health_work'];
?>

<link href="<?= CDN_HTTP ?>/lib/dragula/dragula.min.css" rel="stylesheet" />
<script type="text/javascript" src="<?= CDN_HTTP ?>/lib/dragula/dragula.min.js"></script>
<script type="text/JavaScript" src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
<script>
    console.log("sgt_cnt: <?php echo $sgt_cnt; ?>");
    console.log("sgdt_cnt: <?php echo $sgdt_cnt; ?>");
    console.log("gapt_cnt: <?php echo $gapt_cnt; ?>");
    console.log("expt_cnt: <?php echo $expt_cnt; ?>");
    console.log("mt_idx: <?php echo $_SESSION['_mt_idx']; ?>");
    // 다른 변수들도 동일한 방식으로 출력
</script>
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
                        } else {
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
</script>
<style>
    .top_btn_wr.b_on.active {
        bottom: 14rem
    }
</style>
<div class="container sub_pg bg_main">
    <div class="mt_20">
        <div class="fixed_top bg_main">
            <div class="py_20 px_16">
                <div class="group_mem bg_main d-flex align-items-center justify-content-between">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12 mine">
                            <div class="rect_square rounded_14">
                                <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="프로필이미지" />
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic mr-2"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"><? if ($sgt_cnt > 0) {
                                                                                                        echo "오너";
                                                                                                    } else if ($sgdt_leader_cnt > 0) {
                                                                                                        echo "리더";
                                                                                                    } ?></p>
                                <?php if ($sgt_cnt <= 0) {
                                    $DB->where('mt_idx', $_SESSION['_mt_idx']);
                                    $DB->where('sgdt_discharge', 'N');
                                    $DB->where('sgdt_exit', 'N');
                                    $DB->where('sgdt_show', 'Y');
                                    $sgdt_row = $DB->getone('smap_group_detail_t');
                                    if ($sgdt_row['sgdt_group_chk'] == 'Y') {
                                        $sgdt_row['sgdt_adate'] = '무기한';
                                    } else if ($sgdt_row['sgdt_group_chk'] == 'N') {
                                        // 오늘 날짜
                                        $today = new DateTime();
                                        $date = new DateTime($sgdt_row['sgdt_adate']); // 타임스탬프를 이용하여 DateTime 객체 생성

                                        // 날짜 차이 계산 (음수 포함)
                                        $remainingDays = floor(($date->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24)); // 일자구하기
                                        $remainingTimes = ($date->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24); // 시간구하기
                                        $remainingHours = $remainingTimes * 24; // 시간으로 변환
                                        if ($remainingDays > 0 || $remainingTimes > 0) {
                                            if ($remainingDays > 0) {
                                                $sgdt_row['sgdt_adate'] = $remainingDays . '일';
                                            } else {
                                                $sgdt_row['sgdt_adate'] = floor($remainingHours) . '시간';
                                            }
                                        }
                                    }

                                ?>
                                    <!-- 남은기간 설정시에만 보여집니다. -->
                                    <?php if ($sgdt_row['sgdt_leader_chk'] == 'Y') { ?>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                    <?php }
                                    if ($sgdt_leader_cnt  > 0 || $sgdt_cnt > 0) { ?>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1"> 남은기간 : <?= $sgdt_row['sgdt_adate'] ?></p>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="bargray_fluid"></div>
        </div>
        <?php
        if ($sgt_cnt > 0) {
        ?>
            <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                <input type="hidden" name="act" id="act" value="list" />
                <input type="hidden" name="obj_list" id="obj_list" value="group_list_box" />
                <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                <input type="hidden" name="obj_uri" id="obj_uri" value="./group_update" />
                <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
            </form>
            <script>
                $(document).ready(function() {
                    f_get_box_list();
                });
            </script>
            <div id="group_list_box"></div>
            <!-- 새로 추가되는 채팅 인터페이스 -->
            <style>
                .chat-container {
                    height: 200px;
                }
            </style>
            <div class="container mx-auto p-4">
                <h1 class="text-2xl font-bold mb-4">Anthropic API Chat Interface</h1>
                <div id="chatBox" class="bg-white p-4 rounded-lg shadow-md mb-4 overflow-y-auto chat-container"></div>
                <form id="chatForm" class="flex">
                    <input type="text" id="userInput" class="flex-grow p-2 border rounded-l-lg" placeholder="Type your message here...">
                    <button type="submit" class="bg-blue-500 text-white p-2 rounded-r-lg">Send</button>
                </form>
            </div>
    </div>
    <!-- <button type="button" class="btn w-100 floating_btn rounded" onclick="location.href='./group_create'"><i class="xi-plus-min mr-3"></i> 그룹 추가하기</button> -->
<?php
        }
        if ($sgdt_cnt > 0) {
?>
    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
        <input type="hidden" name="act" id="act" value="invite_list" />
        <input type="hidden" name="obj_list" id="obj_list" value="invite_group_list_box" />
        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
        <input type="hidden" name="obj_uri" id="obj_uri" value="./group_update" />
        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
    </form>
    <script>
        $(document).ready(function() {
            f_get_box_list();
        });
    </script>
    <div id="invite_group_list_box"></div>
</div>
<div class="container sub_pg bg_main">
    <div class="mt_20">
        <div class="fixed_top bg_main">
            <div class="py_20 px_16">
            </div>
        </div>
    </div>
</div>
<?php
        }
?>

<? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹만들기</span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700">친구들과 함께하는
                    <span class="text-primary">그룹</span>을 만들어보세요!
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">그룹을 통해 친구들과 실시간 위치와 일정을 공유해보세요.</p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'">다음</button>
            </div>
        </div>
    </div>
<? } ?>

<? if ($sgt_cnt == 1 && $expt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹원 초대하기</span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700">그룹이 생성되었습니다. 
                    이제 함께할 <span class="text-primary">그룹원</span>을 초대해볼까요?
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">그룹원을 초대하고 함께 위치와 일정을 공유해보세요!</p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'">초대하러 가기</button>
            </div>
        </div>
    </div>
<? } ?>

<? if ($gapt_cnt > 0) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_owner_chk', 'N');
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_group_chk', 'D');
    $row = $DB->getone('smap_group_detail_t');

    $DB->where('sgt_idx', $row['sgt_idx']);
    $sgt_row = $DB->getone('smap_group_t');
?>
    <!-- 그룹만들기 플러팅 : 그룹 활동기한 설정-->
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹 활동기한 설정</span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700">반가워요!
                    <?= $sgt_row['sgt_title'] ?> 그룹 활동 기한 설정이 필요하신가요?
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 fw_500 mt-3"><?= $sgt_row['sgt_title'] ?> 그룹 활동 기한은 개인 정보 보호를 위해
                    설정한 시간 동안만 위치를 공유하게 해줍니다.
                </p>
            </div>
            <div class="flt_footer flt_footer_b">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 flt_close" onclick="group_activity_chk('<?= $row['sgdt_idx'] ?>')">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='./group_act_period?sgdt_idx=<?= $row['sgdt_idx'] ?>'">네</button>
                </div>
            </div>
        </div>
    </div>
<? } ?>
<!-- E-4 그룹 나가기 -->
<div class="modal fade" id="group_out_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" name="group_out_modal_sgdt_idx" id="group_out_modal_sgdt_idx" value="" />
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">그룹에서 나가시겠어요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_out_group();">나가기</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- E-13 멤버 초대 -->
<div class="modal btn_sheeet_wrap fade" id="link_modal" tabindex="-1">
    <div class="modal-dialog btm_sheet">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div class="d-inline-block w-100 text-right">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png" width="24px"></button>
                </div>
                <p class="fs_18 fw_700 text_dynamic line_h1_2">초대장은 어떻게 보낼까요?</p>
            </div>
            <div class="modal-body">
                <ul>
                    <li>
                        <a href="javascript:;" onclick="f_share_link('kakao');" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="<?= CDN_HTTP ?>/img/ico_kakao.png" alt="카카오톡 열기" width="40px" class="mr_12" id="kakao_image" />
                                <p class="fs_15 fw_500 gray_900" id="kakao_text">카카오톡 열기</p>
                            </div>
                            <i class=" xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="f_share_link('clipboard');" class="d-flex align-items-center justify-content-between py_07 btn_copy">
                            <div class="d-flex align-items-center">
                                <img src="<?= CDN_HTTP ?>/img/ico_link.png" alt="초대 링크 복사" width="40px" class="mr_12" />
                                <p class="fs_15 fw_500 gray_900">초대 링크 복사</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="f_share_link('contact');" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="<?= CDN_HTTP ?>/img/ico_address.png" alt="연락처 열기" width="40px" class="mr_12" />
                                <p class="fs_15 fw_500 gray_900">연락처 열기</p>
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
            $('#kakao_text').text('공유하기');
            document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
        } else if (isiOS()) {
            $('#kakao_text').text('공유하기');
            document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
        }
    });

    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const userInput = document.getElementById('userInput');

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userMessage = userInput.value.trim();
        if (!userMessage) return;

        // Display user message
        appendMessage('User', userMessage);
        userInput.value = '';

        try {
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: userMessage }),
            });

            if (!response.ok) {
                throw new Error('API request failed');
            }

            const data = await response.json();
            appendMessage('Assistant', data.response);
        } catch (error) {
            console.error('Error:', error);
            appendMessage('System', 'An error occurred while processing your request.');
        }
    });

    function appendMessage(sender, message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'mb-2';
        messageElement.innerHTML = `<strong>${sender}:</strong> ${message}`;
        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function appendMessage(sender, message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'mb-2';
        messageElement.innerHTML = `<strong>${sender}:</strong> ${message}`;
        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function f_modal_out_group(i) {
        if (i) {
            $('#group_out_modal_sgdt_idx').val(i);
        }

        $('#group_out_modal').modal('show');
    }

    function f_out_group() {
        var form_data = new FormData();
        form_data.append("act", "group_out");
        form_data.append("sgdt_idx", $('#group_out_modal_sgdt_idx').val());

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
                console.log(data);
                if (data == 'Y') {
                    document.location.href = './group';
                }
            },
            error: function(err) {
                console.log(err);
            },
        });

        return false;
    }

    // 그룹활동기한 설정 안할 시 무기한으로 지정
    function group_activity_chk(idx) {
        var form_data = new FormData();
        form_data.append("act", "group_activity_period");
        form_data.append("sgdt_idx", idx);

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
                console.log(data);
                if (data == 'Y') {
                    document.location.href = './group';
                }
            },
            error: function(err) {
                console.log(err);
            },
        });

        return false;
    }
</script>
<script src="https://cdn.tailwindcss.com"></script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>