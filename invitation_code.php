<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$h_menu = '2';
$_SUB_HEAD_TITLE = $translations['txt_enter_invitation_code'];
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
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
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic"><?=$translations['txt_create_group_with_friends_invite']?></p>
        <p class="fs_12 fc_gray_600 mt-3 text_dynamic line_h1_2"><?=$translations['txt_invitation_link_info']?>
        </p>
        <form action="" class="">
            <input type="hidden" id="mt_idx" name="mt_idx" value="<?= $_SESSION['_mt_idx'] ?>">
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5 class=""><?=$translations['txt_invitation_code']?></h5>
                    </div>
                    <input type="text" class="form-control" placeholder="<?=$translations['txt_enter_invitation_code']?>" id="sit_code" name="sit_code" maxlength="20" value="<?=$_GET['sit_code']?>">
                </div>
            </div>
            <div class="b_botton">
                <!-- 이메일 중복일 경우 data-toggle="modal" data-target="#dpl_email" 버튼태기에 넣어주세요 -->
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="group_invite_chk()"><?= $translations['txt_input_complete'] ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    function invite_code_insert(sit_code) {
        if (sit_code){
            $('#site_code').val(sit_code);
        }
    }

    function group_invite_chk() {
        var sit_code = $('#sit_code').val();
        var mt_idx = $('#mt_idx').val();

        if (sit_code == '') {
            jalert($translations['txt_enter_invitation_code']);
            return false;
        }

        var form_data = new FormData();
        form_data.append("act", "group_invite_code_chk");
        form_data.append("mt_idx", mt_idx);
        form_data.append("sit_code", $('#sit_code').val());

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
                } else if (data == 'D') {
                    jalert('<?=$translations['txt_group_details_not_found']?>');
                    return false;
                } else if (data == 'N') {
                    jalert('<?=$translations['txt_invitation_code_already_used']?>');
                    return false;
                } else if (data == 'J') {
                    jalert('<?=$translations['txt_already_in_group']?>');
                    return false;
                } else if (data == 'C') {
                    jalert('<?=$translations['txt_group_full']?>');
                    return false;
                }
            },
            error: function(err) {
                console.log(err);
            },
        });

        return false;
    }
</script>