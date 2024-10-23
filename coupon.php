<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = $translations['txt_coupon_input'];
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_another_device'], './logout');
    }
}
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic"><?= $translations['txt_enter_coupon_number'] ?></p>
        <form action="" class="">
            <input type="hidden" name="act" id="act" value="coupon_input" />
            <input type="hidden" name="mt_idx" id="mt_idx" value="<?= $_SESSION['_mt_idx'] ?>" />
            <div class="mt-5">
                <div class="ip_wr ct_code_msg" id="ct_code_text">
                    <div class="ip_tit">
                        <h5 class=""><?= $translations['txt_coupon_number'] ?></h5>
                    </div>
                    <input type="text" class="form-control" placeholder="<?= $translations['txt_enter_coupon_number_placeholder'] ?>" id="ct_code" name="ct_code" maxlength="8">
                    <p class="fs_12 fc_gray_600 mt-3 px-4 line_h1_2 text_dynamic"><?= $translations['txt_coupon_number_format'] ?></p>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_enter_valid_coupon_code'] ?></div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="check_coupon()"><?= $translations['txt_input_complete'] ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    function check_coupon() {
        // 비밀번호 입력값 가져오기
        let mt_idx = $("#mt_idx").val();
        let ct_code = $("#ct_code").val();

        if (ct_code.length < 8) {
            $(".ct_code_msg").addClass("ip_invalid");
            $(".ct_code_msg").removeClass("ip_valid");
            $("#ct_code").focus();
            return false;
        } else {
            $(".ct_code_msg").addClass("ip_valid");
            $(".ct_code_msg").removeClass("ip_invalid");
        }
        $.ajax({
            url: "./coupon_update",
            type: "POST",
            data: {
                act: "coupon_input",
                mt_idx: mt_idx,
                ct_code: ct_code,
            },
            dataType: "json",
            success: function(d, s) {
                console.log(d);
                if (d.result == "ok") {
                    jalert_url('<?= $translations['txt_coupon_applied'] ?>', './setting');
                } else if (d.result == "use") {
                    jalert('<?= $translations['txt_coupon_already_used'] ?>');
                } else if (d.result == "end") {
                    jalert('<?= $translations['txt_coupon_expired'] ?>');
                } else if (d.result == "none") {
                    jalert('<?= $translations['txt_coupon_not_found'] ?>');
                }
            },
            error: function(d) {
                console.log("error:" + d);
            },
        });
    }

    $("#mt_hp").filter(".lower").on("keyup", function() {
        $(this).val($(this).val().toLowerCase());
    });
</script>