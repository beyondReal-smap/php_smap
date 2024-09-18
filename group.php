<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '2';
$h_menu = '8';
$_SUB_HEAD_TITLE = translate("그룹", $userLang);
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";
// include $_SERVER['DOCUMENT_ROOT'] . "/anthropic.php";

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
                                <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="<?= translate("프로필이미지", $userLang) ?>" />
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic mr-2"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"><? if ($sgt_cnt > 0) {
                                                                                                        echo translate("오너", $userLang);
                                                                                                    } else if ($sgdt_leader_cnt > 0) {
                                                                                                        echo translate("리더", $userLang);
                                                                                                    } ?></p>
                                <?php if ($sgt_cnt <= 0) {
                                    $DB->where('mt_idx', $_SESSION['_mt_idx']);
                                    $DB->where('sgdt_discharge', 'N');
                                    $DB->where('sgdt_exit', 'N');
                                    $DB->where('sgdt_show', 'Y');
                                    $sgdt_row = $DB->getone('smap_group_detail_t');
                                    if ($sgdt_row['sgdt_group_chk'] == 'Y') {
                                        $sgdt_row['sgdt_adate'] = "무기한";
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
                                                $sgdt_row['sgdt_adate'] = $remainingDays . translate("일", $userLang);
                                            } else {
                                                $sgdt_row['sgdt_adate'] = floor($remainingHours) . translate("시간", $userLang);
                                            }
                                        }
                                    }

                                ?>
                                    <!-- 남은기간 설정시에만 보여집니다. -->
                                    <?php if ($sgdt_row['sgdt_leader_chk'] == 'Y') { ?>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>
                                    <?php }
                                    if ($sgdt_leader_cnt  > 0 || $sgdt_cnt > 0) { ?>
                                        <p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1"><?= translate("남은기간", $userLang) ?> : <?= translate($sgdt_row['sgdt_adate'], $userLang) ?></p>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="bargray_fluid"></div>
        </div>
        <script>
            $(document).ready(function() {
                f_get_box_list();
            });
        </script>
        <div id="invite_group_list_box"></div>
    </div>


    <? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
        <div class="floating_wrap on">
            <div class="flt_inner">
                <div class="flt_head">
                    <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= translate("그룹만들기", $userLang) ?></span></p>
                </div>
                <div class="flt_body pb-5 pt-3">
                    <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= translate("친구들과 함께하는", $userLang) ?>
                        <span class="text-primary"><?= translate("그룹", $userLang) ?></span>을 <?= translate("만들어보세요!", $userLang) ?>
                    </p>
                    <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?= translate("그룹을 통해 친구들과 실시간 위치와 일정을 공유해보세요.", $userLang) ?></p>
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
                    <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= translate("그룹이 생성되었습니다.", $userLang) ?>
                        <?= translate("이제 함께할", $userLang) ?> <span class="text-primary"><?= translate("그룹원", $userLang) ?></span>을 <?= translate("초대해볼까요?", $userLang) ?>
                    </p>
                    <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?= translate("그룹원을 초대하고 함께 위치와 일정을 공유해보세요!", $userLang) ?></p>
                </div>
                <div class="flt_footer">
                    <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'"><?= translate("초대하러 가기", $userLang) ?></button>
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
                    <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= translate("그룹 활동기한 설정", $userLang) ?></span></p>
                </div>
                <div class="flt_body pb-5 pt-3">
                    <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= translate("반가워요!", $userLang) ?>
                        <?= $sgt_row['sgt_title'] ?> <?= translate("그룹 활동 기한 설정이 필요하신가요?", $userLang) ?>
                    </p>
                    <p class="text_dynamic line_h1_3 text_gray fs_14 fw_500 mt-3"><?= $sgt_row['sgt_title'] ?> <?= translate("그룹 활동 기한은 개인 정보 보호를 위해\n설정한 시간 동안만 위치를 공유하게 해줍니다.", $userLang) ?>
                    </p>
                </div>
                <div class="flt_footer flt_footer_b">
                    <div class="d-flex align-items-center w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 flt_close" onclick="group_activity_chk('<?= $row['sgdt_idx'] ?>')"><?= translate("아니요", $userLang) ?></button>
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='./group_act_period?sgdt_idx=<?= $row['sgdt_idx'] ?>'"><?= translate("네", $userLang) ?></button>
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
                    <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= translate("그룹에서 나가시겠어요?", $userLang) ?></p>
                </div>
                <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                    <div class="d-flex align-items-center w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?= translate("아니요", $userLang) ?></button>
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_out_group();"><?= translate("나가기", $userLang) ?></button>
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
                    <p class="fs_18 fw_700 text_dynamic line_h1_2"><?= translate("초대장은 어떻게 보낼까요?", $userLang) ?></p>
                </div>
                <div class="modal-body">
                    <ul>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('kakao');" class="d-flex align-items-center justify-content-between py_07">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_kakao.png" alt="<?= translate("카카오톡 열기", $userLang) ?>" width="40px" class="mr_12" id="kakao_image" />
                                    <p class="fs_15 fw_500 gray_900" id="kakao_text"><?= translate("카카오톡 열기", $userLang) ?></p>
                                </div>
                                <i class=" xi-angle-right-min fs_15 text_gray"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('clipboard');" class="d-flex align-items-center justify-content-between py_07 btn_copy">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_link.png" alt="<?= translate("초대 링크 복사", $userLang) ?>" width="40px" class="mr_12" />
                                    <p class="fs_15 fw_500 gray_900"><?= translate("초대 링크 복사", $userLang) ?></p>
                                </div>
                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('contact');" class="d-flex align-items-center justify-content-between py_07">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_address.png" alt="<?= translate("연락처 열기", $userLang) ?>" width="40px" class="mr_12" />
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
            createGroupMember();
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

        function createGroupMember() {
            var form_data = new FormData();
            if (<?= $sgdt_cnt ?> > 0) {
                form_data.append("act", "invite_list");
            } else {
                form_data.append("act", "list");
            }

            $.ajax({
                url: "./group_update",
                enctype: "multipart/form-data",
                data: form_data,
                type: "POST",
                async: true,
                contentType: false,
                processData: false,
                cache: true,
                timeout: 10000,
                dataType: 'json',
                success: function(response) {
                    if (response.result === 'success') {
                        renderGroupList(response.data);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(err) {
                    console.log(err);
                },
            });
        }

        function renderGroupList(data) {
            const inviteGroupListBox = $('#invite_group_list_box');
            inviteGroupListBox.empty();

            let grouOut = '<?= translate('그룹나가기', $userLang) ?>';
            let outerDiv = '<div class="mt_85 pt_20 pb_100">';

            if (data.groups.length > 0) {
                data.groups.forEach(group => {
                    let groupHeaderHtml = `
        <div class="group_header d-flex align-items-center justify-content-between px_16 py_16 border-bottom cursor_pointer" onclick="location.href='./group_info?sgt_idx=${group.sgt_idx}'">
          <p class="fs_15 fw_700 text_dynamic line_h1_2 mr-3">${group.sgt_title}<span class="ml-2">(${group.member_cnt})</span></p>
      `;

                    if (data.action === 'list') {
                        groupHeaderHtml += `<i class="fs_15 text_gray xi-angle-right-min"></i>`;
                    } else if (data.action === 'invite_list') {
                        groupHeaderHtml += `<button type="button" class="btn fs_14 fw_500 text_gray h_fit_im px-0 py-0 mx-0 my-0 text-right" onclick="f_modal_out_group('${group.sgdt_idx}');">${grouOut}</button>`;
                    }

                    groupHeaderHtml += `</div>`;

                    let groupHtml = `
        <div class="border bg-white rounded-lg mb-3">
          ${groupHeaderHtml}
      `;

                    if (group.invites.length > 0 || group.members.length > 0) {
                        groupHtml += `<div class="group-body px_16 py_04">`;

                        group.invites.forEach(invite => {
                            groupHtml += `
            <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_12 group_list_ing">
              ${invite.count}명 초대중
            </p>
          `;
                        });

                        group.members.forEach(member => {
                            groupHtml += `
            <div class="d-flex align-items-center justify-content-between py_12 group_list">
              <div class="w_fit">
                <a href="#" class="d-flex align-items-center">
                  <div class="prd_img flex-shrink-0 mr_12">
                    <div class="rect_square rounded_14">
                      <img src="${member.mt_file1_url}" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                    </div>
                  </div>
                  <div>
                    <p class="fs_14 fw_500 text_dynamic line_h1_2 mr-2">${member.nickname}</p>
                    <div class="d-flex align-items-center flex-wrap ">`;

                            if (member.sgdt_owner_leader_chk_t) {
                                groupHtml += `<p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">${member.sgdt_owner_leader_chk_t}</p>`;
                            }

                            if (member.sgdt_adate) {
                                if (member.sgdt_owner_leader_chk_t) {
                                    groupHtml += `<p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1 mx-2"> | </p>`;
                                }
                                groupHtml += `<p class="fs_12 fw_400 text_dynamic text_gray line_h1_2 mt-1">${'<?= translate('남은기간', $userLang) ?>'} : ${member.sgdt_adate}</p>`;
                            }

                            groupHtml += `
                    </div>
                  </div>
                </a>
              </div>
            </div>
          `;
                        });
                        groupHtml += `</div>`; // group-body 닫기
                    }

                    groupHtml += `</div>`; // border bg-white rounded-lg mb-3 닫기
                    outerDiv += groupHtml; // outerDiv에 groupHtml 추가
                });

                outerDiv += '</div>'; // mt_85 pt_20 pb_100 닫기
                inviteGroupListBox.append(outerDiv);
            } else {
                inviteGroupListBox.append('<p>등록된 그룹이 없습니다.</p>');
            }
        }

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