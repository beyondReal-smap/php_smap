<?php

include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$h_menu = '2';
$_SUB_HEAD_TITLE = "초대코드입력";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
    }
}
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic">초대한 친구와 함께 해볼까요?</p>
        <p class="fs_12 fc_gray_600 mt-3 text_dynamic line_h1_2">초대메세지로 이동하여 링크를 누르면,
            초대코드가 자동으로 입력되요
        </p>
        <form action="" class="">
            <input type="hidden" id="mt_idx" name="mt_idx" value="<?= $_SESSION['_mt_idx'] ?>">
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5 class="">초대코드</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="초대코드를 입력해주세요." id="sit_code" name="sit_code" maxlength="20" value="<?=$_GET['sit_code']?>">
                </div>
            </div>
            <div class="b_botton">
                <!-- 이메일 중복일 경우 data-toggle="modal" data-target="#dpl_email" 버튼태기에 넣어주세요 -->
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="group_invite_chk()">입력했어요!</button>
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
            jalert('초대코드를 입력해주세요');
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
                    jalert('해당되는 그룹정보가 없습니다.');
                    return false;
                } else if (data == 'N') {
                    jalert('이미 사용된 초대코드입니다.');
                    return false;
                } else if (data == 'J') {
                    jalert('이미 다른 그룹에 속해있습니다.');
                    return false;
                } else if (data == 'C') {
                    jalert('해당 그룹 인원이 다 찼습니다.');
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