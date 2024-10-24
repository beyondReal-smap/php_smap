<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

// 번역 파일 로드 (lib.inc.php에 추가 - 필요에 따라 경로 수정)
$langFiles = [
    'ko' => $_SERVER['DOCUMENT_ROOT'] . '/lang/ko.php',
    'en' => $_SERVER['DOCUMENT_ROOT'] . '/lang/en.php',
    // 다른 언어 추가 가능
];

$userLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko'; // 사용자 언어 설정 (세션 또는 다른 방법으로 설정)

if (isset($langFiles[$userLang])) {
    include $langFiles[$userLang];
} else {
    $translations[$userLang] = []; // 기본 언어 설정 없을 경우 빈 배열
}

$b_menu = '';
$h_menu = '6';
$h_url = './group';

// $_SESSION['_mt_idx'] = '15';
if (empty($_SESSION['_mt_idx'])) {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_other_device'], './logout'); 
    }
}

if (isset($_GET['sgt_idx'])) {
    $DB->where('sgt_idx', $_GET['sgt_idx']);
    $DB->where('sgt_show', 'Y');
    $row_sgt = $DB->getone('smap_group_t');

    if ($row_sgt['sgt_idx']) {
        $DB->where('sgt_idx', $row_sgt['sgt_idx']);
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $row_sgdt = $DB->getone('smap_group_detail_t');
        $sgdt_idx_t = $row_sgdt['sgdt_idx'];

        $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);

        $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
        $mt_info = get_member_t_info($row_sgdt['mt_idx']);
        $mt_file1_url = get_image_url($mt_info['mt_file1']);

        $chk_leader_owner = false;
        if ($row_sgdt['sgdt_owner_chk'] == 'Y') {
            $sgdt_owner_leader_chk_t = $translations['txt_owner']; 
            $chk_leader_owner = true;
        } else {
            if ($row_sgdt['sgdt_leader_chk'] == 'Y') {
                $sgdt_owner_leader_chk_t = $translations['txt_leader']; 
                $chk_leader_owner = true;
            } else {
                $sgdt_owner_leader_chk_t = '';
            }
        }
    } else {
        alert($translations['txt_invalid_access'], './'); 
    }
} else {
    alert($translations['txt_invalid_access'], './');
}

$_SUB_HEAD_TITLE = $translations['txt_group_edit']; 
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<style>
    .top_btn_wr.b_on.active {}

    .drag-drop-item {
        touch-action: none;
    }
</style>
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
                        jalert('<?=$translations['txt_invitation_link_copied']?>'); 
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
<div class="container sub_pg">
    <div class="mt_20">
        <div class="fixed_top">
            <div class="bg-secondary px_16 py-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <p class="fs_16 fw_800 mr-2"><?= $row_sgt['sgt_title'] ?><span class="fs_15"> <span id="member_cnt">(<?= $member_cnt_t ?>)</span></p>
                    <?php if ($row_sgdt['sgdt_owner_chk'] == 'Y') { ?>
                        <!-- 오너일때 필요 -->
                        <button type="button" class="btn h_fit_im px-0 py-0" data-toggle="modal" data-target="#name_edit_modal"><img src="<?= CDN_HTTP ?>/img/ico_edit.png" width="19px" alt="<?=$translations['txt_group_name_edit']?>" /></button> 
                    <?php } ?>
                </div>
                <?php if ($row_sgdt['sgdt_owner_chk'] != 'Y') { ?>
                    <!--맴버 / 그룹리더 -->
                    <button type="button" class="btn fs_14 fw_500 text_gray h_fit_im px-0 py-0 mx-0 my-0 text-right" onclick="f_modal_out_group('<?= $sgdt_idx_t ?>');"><?=$translations['txt_group_exit']?></button> 
                <?php } else { ?>
                    <!--그룹오너 -->
                    <button type="button" class="btn fs_14 fw_500 text_gray h_fit_im px-0 text-right" onclick="f_modal_group_delete('<?= $row_sgt['sgt_idx'] ?>');"><?=$translations['txt_group_delete']?></button> 
                <?php } ?>
            </div>
            <div class="py_20 bg-white px_16">
                <div class="group_mem d-flex align-items-center">
                    <div class="w_fit">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_12 mine">
                                <div class="rect_square rounded_14">
                                    <img src="<?= $mt_file1_url ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                </div>
                            </div>
                            <div>
                                <p class="fs_14 fw_500 text_dynamic mr-2"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                                <div class="d-flex align-items-center flex-wrap ">
                                    <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"><?= $sgdt_owner_leader_chk_t ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bargray_fluid"></div>
        </div>
        <div class="mt_145">
            <!-- 그룹원 없을 때 -->
            <?php if ($invite_cnt) { ?>
                <div class="pt-2">
                    <p class="fs_13 fw_500 text-primary px_14 py-3 rounded-sm w-100 bg-secondary my_08">
                        <?= $invite_cnt ?><?=$translations['txt_people_inviting']?>
                    </p>
                </div>
            <?php } else { ?>
                <?php if ($member_cnt_t < 1) { ?>
                    <div class="">
                        <div class="pt-5 text-center">
                            <img src="<?= CDN_HTTP ?>/img/warring.png" width="82px" alt="<?=$translations['txt_add_group_member']?>" />
                            <p class="mt_20 fc_gray_900 text-center"><?=$translations['txt_please_add_group_member']?></p> 
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                <input type="hidden" name="act" id="act" value="list_info" />
                <input type="hidden" name="obj_list" id="obj_list" value="group_info_list_box" />
                <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                <input type="hidden" name="obj_uri" id="obj_uri" value="./group_update" />
                <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                <input type="hidden" name="sgt_idx" id="sgt_idx" value="<?= $row_sgt['sgt_idx'] ?>" />
            </form>

            <script>
                $(document).ready(function() {
                    f_get_box_list();
                    f_share_link_get('<?= $row_sgt['sgt_idx'] ?>');

                    dragula([document.getElementById("group_info_list_box")]).on('dragend', function(el) {
                        f_order_group_member();
                    });

                    $(document).on("keyup", "input.txt-cnt", function() {
                        var cnt_id = $(this).data('length-id');
                        $('#' + cnt_id).text($(this).val().length);
                    });
                });

                function f_order_group_member() {
                    var arr_sgdt_idx = new Array();

                    $(".group_info_member").each(function(index) {
                        var sgdt_idx = $(this).attr('data-sgdt-idx');
                        var sgdt_rank_index = index;

                        // console.log("index : " + index + ", sgdt_idx : " + sgdt_idx);

                        arr_sgdt_idx.push(sgdt_idx);
                    });

                    var json_data = JSON.stringify(arr_sgdt_idx);
                    // console.log(json_data);

                    var form_data = new FormData();
                    form_data.append("act", "group_member_order");
                    form_data.append("sgt_idx", $('#sgt_idx').val());
                    form_data.append("json_sgdt_idx", json_data);

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
                                $('#top_weather_box').html(data);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                }

                function f_modal_delete_leader(i, n) {
                    if (i) {
                        $('#leader_delete_modal_sgdt_idx').val(i);
                    }
                    if (n) {
                        $('#name_leader_delete_modal').html(n);
                    }
                    $('#more_madal').modal('hide');
                    $('#leader_delete_modal').modal('show');
                }

                function f_delete_leader() {
                    var form_data = new FormData();
                    form_data.append("act", "leader_delete");
                    form_data.append("sgdt_idx", $('#leader_delete_modal_sgdt_idx').val());

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
                            if (data == 'Y') {
                                $('#leader_delete_modal').modal('hide');
                                f_get_box_list();
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                }

                function f_modal_add_leader(i, n) {
                    if (i) {
                        $('#leader_add_modal_sgdt_idx').val(i);
                    }
                    if (n) {
                        $('#name_leader_add_modal').html(n);
                    }

                    $('#more_madal').modal('hide');
                    $('#leader_add_modal').modal('show');
                }

                function f_add_leader() {
                    var form_data = new FormData();
                    form_data.append("act", "leader_add");
                    form_data.append("sgdt_idx", $('#leader_add_modal_sgdt_idx').val());

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
                            if (data == 'Y') {
                                $('#leader_add_modal').modal('hide');
                                f_get_box_list();
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                }

                function f_modal_out_mem(i, n) {
                    if (i) {
                        $('#mem_out_modal_sgdt_idx').val(i);
                    }
                    if (n) {
                        $('#name_mem_out_modal').html(n);
                    }

                    $('#more_madal').modal('hide');
                    $('#mem_out_modal').modal('show');
                }

                function f_out_mem() {
                    var form_data = new FormData();
                    form_data.append("act", "mem_out");
                    form_data.append("sgdt_idx", $('#mem_out_modal_sgdt_idx').val());

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
                            if (data == 'Y') {
                                document.location.reload();
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                }

                function f_modal_group_delete(i) {
                    if (i) {
                        $('#group_delete_modal_sgt_idx').val(i);
                    }

                    $('#group_delete_modal').modal('show');
                }

                function f_delete_group() {
                    var form_data = new FormData();
                    form_data.append("act", "group_delete");
                    form_data.append("sgt_idx", $('#group_delete_modal_sgt_idx').val());

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

                function f_share_link_get(i) {
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
                                $('.b_botton').addClass('d-none');
                            } else {
                                $('#share_url').val(data);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                }

                function moreButtonClick(leaderCheck, sgdtIdx, mtName) {
                    // 여기에 모달 버튼 클릭 시 수행할 동작을 추가
                    console.log("더보기 버튼 클릭! leaderCheck:", leaderCheck, "sgdtIdx:", sgdtIdx, "mtName:", mtName);

                    // 모달창 열기
                    $('#more_madal').modal('show');
                    $('#btn_add_leader').attr('onclick', "f_modal_add_leader('" + sgdtIdx + "', '" + mtName + "')");
                    $('#btn_release_leader').attr('onclick', "f_modal_delete_leader('" + sgdtIdx + "', '" + mtName + "')");
                    $('#btn_group_out').attr('onclick', "f_modal_out_mem('" + sgdtIdx + "', '" + mtName + "')");


                    // leaderCheck 값에 따라 버튼 보이기/숨기기
                    if (leaderCheck === 'Y') {
                        // 리더 해제하기 버튼 보이기
                        $('#btn_release_leader').show();
                        $('#btn_add_leader').hide();
                    } else {
                        // 리더 추가하기 버튼 보이기
                        $('#btn_release_leader').hide();
                        $('#btn_add_leader').show();
                    }

                }
            </script>

            <div id="group_info_list_box"></div>
        </div>
        <?php if ($chk_leader_owner) { ?>
            <!-- 리더/ 오너일 때 사용 -->
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " data-toggle="modal" data-target="#link_modal"><i class="xi-plus-min mr-3"></i><?=$translations['txt_invite_group_member']?></button>
            </div>
        <?php } ?>
    </div>
</div>
<div class="modal fade bottom_modal" id="more_madal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
            </div>
            <div class="modal-body">
                <div class="rounded-lg overflow-hidden ">
                    <button type="button" class="btn btn-block mt-0 rounded-0 bottom_mdl_btn" id="btn_add_leader" style="display:none;" onclick=""><?=$translations['txt_add_leader']?></button>
                    <button type="button" class="btn btn-block mt-0 rounded-0 bottom_mdl_btn" id="btn_release_leader" style="display:none;" onclick=""><?=$translations['txt_release_leader']?></button>
                    <button type="button" class="btn btn-block mt-0 rounded-0 bottom_mdl_btn" id="btn_group_out" onclick=""><?=$translations['txt_expel_group_member']?></button>
                </div>
            </div>
            <div class="modal-footer mt-3 mb-3">
                <button type="button" class="btn text-black btn-block fs_15 fw_600 bg-white mx-0 my-0" data-dismiss="modal"><?=$translations['txt_cancel']?></button>
            </div>
        </div>
    </div>
</div>
<!-- E-4 그룹 나가기 -->
<div class="modal fade" id="group_out_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" name="group_out_modal_sgdt_idx" id="group_out_modal_sgdt_idx" value="" />

            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?=$translations['txt_are_you_sure_leave_group']?></p>
            </div>
            <div class=" modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?=$translations['txt_no']?></button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_out_group();"><?=$translations['txt_leave']?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($row_sgdt['sgdt_owner_chk'] == 'Y') { ?>
    <!-- E-8 리더 추가 -->
    <div class="modal fade" id="leader_add_modal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" name="leader_add_modal_sgdt_idx" id="leader_add_modal_sgdt_idx" value="" />

                <div class="modal-body pt_40 pb_27 px-3 ">
                    <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><span id="name_leader_add_modal"></span><?=$translations['txt_really_add_leader']?><?= $row_sgt['sgt_title'] ?>] <?=$translations['txt_group_leader']?>?</p>
                </div>
                <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                    <div class="d-flex align-items-center w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?=$translations['txt_no']?></button>
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_add_leader();"><?=$translations['txt_add']?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- E-8 리더 삭제 -->
    <div class="modal fade" id="leader_delete_modal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" name="leader_delete_modal_sgdt_idx" id="leader_delete_modal_sgdt_idx" value="" />

                <div class="modal-body pt_40 pb_27 px-3 ">
                    <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><span id="name_leader_delete_modal"></span><?=$translations['txt_really_remove_leader']?><?= $row_sgt['sgt_title'] ?>] <?=$translations['txt_from_group_leader']?>?</p>
                </div>
                <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                    <div class="d-flex align-items-center w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?=$translations['txt_no']?></button>
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_delete_leader();"><?=$translations['txt_remove']?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- E-11 그룹명 수정  -->
    <div class="modal fade" id="name_edit_modal" tabindex="-1">
        <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <form method="post" name="frm_form_name_edit_modal" id="frm_form_name_edit_modal" action="./group_update" target="hidden_ifrm" enctype="multipart/form-data">
                    <input type="hidden" name="firstname" id="firstname" value="" />
                    <input type="hidden" name="sgt_idx" id="sgt_idx" value="<?= $row_sgt['sgt_idx'] ?>" />
                    <input type="hidden" name="act" id="act" value="chg_sgt_title" />
                    <div class="modal-header">
                        <p class="modal-title line1_text fs_20 fw_700"><?=$translations['txt_group_name_edit']?></p>
                        <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                    </div>
                    <div class="modal-body scroll_bar_y">
                        <div class="ip_wr">
                            <div class="ip_tit d-flex align-items-center justify-content-between">
                                <h5 class=""><?=$translations['txt_before_change']?></h5>
                            </div>
                            <input type="text" class="form-control" id="sgt_title" name="sgt_title" placeholder="" value="<?= $row_sgt['sgt_title'] ?>" readonly>
                        </div>
                        <div class="ip_wr mt_25">
                            <div class="ip_tit d-flex align-items-center justify-content-between">
                                <h5 class=""><?=$translations['txt_after_change']?></h5>
                                <p class="text_num fs_12 fc_gray_600">(<span id="sgt_title_chg_cnt">0</span>/15)</p>
                            </div>
                            <input type="text" class="form-control txt-cnt" id="sgt_title_chg" name="sgt_title_chg" minlength="2" maxlength="15" oninput="maxLengthCheck(this)" data-length-id="sgt_title_chg_cnt" placeholder="<?=$translations['txt_enter_group_name']?>">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-0">
                        <button type="submit"                       class="btn btn-lg btn-block btn-primary mx-0 my-0"><?=$translations['txt_change_group_name']?></button>
                    </div>
                </form>
                <script>
                    $("#frm_form_name_edit_modal").validate({
                        submitHandler: function() {
                            // $('#splinner_modal').modal('toggle');

                            return true;
                        },
                        rules: {
                            sgt_title_chg: {
                                required: true,
                            },
                        },
                        messages: {
                            sgt_title_chg: {
                                required: "<?=$translations['txt_please_enter_group_name']?>",
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

    <!-- E-12 그룹 삭제 -->
    <div class="modal fade" id="group_delete_modal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" name="group_delete_modal_sgt_idx" id="group_delete_modal_sgt_idx" value="" />

                <div class="modal-body pt_40 pb_27 px-3 ">
                    <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">[<?= $row_sgt['sgt_title'] ?>] <?=$translations['txt_delete_group']?>?</p>
                    <p class="fs_12 text_dynamic text-center text_gray mt-2 line_h1_2"><?=$translations['txt_if_you_delete_group']?><br> <?=$translations['txt_really_delete']?>?</p>

                </div>
                <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                    <div class="d-flex align-items-center w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?=$translations['txt_later']?></button>
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_delete_group();"><?=$translations['txt_delete']?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if ($chk_leader_owner) { ?>
    <!-- E-13 멤버 초대 -->
    <div class="modal btn_sheeet_wrap fade" id="link_modal" tabindex="-1">
        <div class="modal-dialog btm_sheet">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <div class="d-inline-block w-100 text-right">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png" width="24px"></button>
                    </div>
                    <p class="fs_18 fw_700 text_dynamic line_h1_2"><?=$translations['txt_how_to_send_invitation']?></p>
                </div>
                <div class="modal-body">
                    <ul>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('kakao');" class="d-flex align-items-center justify-content-between py_07">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_kakao.png" alt="<?=$translations['txt_open_kakao']?>" width="40px" class="mr_12" id="kakao_image" />
                                    <p class="fs_15 fw_500 gray_900" id="kakao_text"><?=$translations['txt_open_kakao']?></p>
                                </div>
                                <i class=" xi-angle-right-min fs_15 text_gray"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('clipboard');" class="d-flex align-items-center justify-content-between py_07 btn_copy">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_link.png" alt="<?=$translations['txt_copy_invitation_link']?>" width="40px" class="mr_12" />
                                    <p class="fs_15 fw_500 gray_900"><?=$translations['txt_copy_invitation_link']?></p>
                                </div>
                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick="f_share_link('contact');" class="d-flex align-items-center justify-content-between py_07">
                                <div class="d-flex align-items-center">
                                    <img src="<?= CDN_HTTP ?>/img/ico_address.png" alt="<?=$translations['txt_open_contacts']?>" width="40px" class="mr_12" />
                                    <p class="fs_15 fw_500 gray_900"><?=$translations['txt_open_contacts']?></p>
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
                /*
                document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_kakao.png";
                */
                $('#kakao_text').text("<?=$translations['txt_share_button']?>");
                document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
            } else if (isiOS()) {
                $('#kakao_text').text("<?=$translations['txt_share_button']?>");
                document.getElementById("kakao_image").src = "<?= CDN_HTTP ?>/img/ico_share.png";
            }
        });
    </script>
    <!-- E-8 그룹원 삭제 -->
    <div class="modal fade" id="mem_out_modal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" name="mem_out_modal_sgdt_idx" id="mem_out_modal_sgdt_idx" value="" />

                <div class="modal-body pt_40 pb_27 px-3 ">
                    <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><span id="name_mem_out_modal"></span><?=$translations['txt_really_expel']?><?= $row_sgt['sgt_title'] ?>] <?=$translations['txt_group_out']?>?</p>
                </div>
                <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                    <div class="d-flex align-items-center w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close"><?=$translations['txt_no']?></button>
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_out_mem();"><?=$translations['txt_remove']?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>